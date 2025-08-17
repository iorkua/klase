<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FileTracking extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'file_trackings';
    
    protected $fillable = [
        'file_indexing_id',
        'rfid_tag',
        'qr_code',
        'current_location',
        'current_holder',
        'current_handler',
        'date_received',
        'due_date',
        'status',
        'movement_history',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date_received' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'movement_history' => 'array',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_RETURNED = 'returned';
    const STATUS_LOST = 'lost';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Relationship with FileIndexing
     */
    public function fileIndexing(): BelongsTo
    {
        return $this->belongsTo(FileIndexing::class, 'file_indexing_id');
    }

    /**
     * Get the current handler user
     */
    public function currentHandlerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_handler');
    }

    /**
     * Check if the file is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }
        
        return Carbon::now()->isAfter($this->due_date) && 
               !in_array($this->status, [self::STATUS_RETURNED, self::STATUS_ARCHIVED]);
    }

    /**
     * Get days until due or overdue
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    /**
     * Add movement entry to history
     */
    public function addMovementEntry(array $movementData): void
    {
        $history = $this->movement_history ?? [];
        
        $movement = array_merge([
            'timestamp' => Carbon::now()->toISOString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
        ], $movementData);
        
        array_unshift($history, $movement);
        
        // Keep only last 100 movements to prevent excessive data
        if (count($history) > 100) {
            $history = array_slice($history, 0, 100);
        }
        
        $this->movement_history = $history;
        $this->updated_at = Carbon::now();
        $this->save();
    }

    /**
     * Update file location and add to movement history
     */
    public function updateLocation(string $newLocation, string $reason = null): void
    {
        $oldLocation = $this->current_location;
        
        $this->current_location = $newLocation;
        
        $this->addMovementEntry([
            'action' => 'location_change',
            'from_location' => $oldLocation,
            'to_location' => $newLocation,
            'reason' => $reason,
        ]);
    }

    /**
     * Update file handler and add to movement history
     */
    public function updateHandler(string $newHandler, string $reason = null): void
    {
        $oldHandler = $this->current_handler;
        
        $this->current_handler = $newHandler;
        
        $this->addMovementEntry([
            'action' => 'handler_change',
            'from_handler' => $oldHandler,
            'to_handler' => $newHandler,
            'reason' => $reason,
        ]);
    }

    /**
     * Update file status and add to movement history
     */
    public function updateStatus(string $newStatus, string $reason = null): void
    {
        $oldStatus = $this->status;
        
        $this->status = $newStatus;
        
        $this->addMovementEntry([
            'action' => 'status_change',
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'reason' => $reason,
        ]);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('current_location', 'LIKE', "%{$location}%");
    }

    /**
     * Scope for filtering by handler
     */
    public function scopeByHandler($query, $handler)
    {
        return $query->where('current_handler', 'LIKE', "%{$handler}%");
    }

    /**
     * Scope for overdue files
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->whereNotIn('status', [self::STATUS_RETURNED, self::STATUS_ARCHIVED]);
    }

    /**
     * Scope for active files
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RETURNED, self::STATUS_ARCHIVED, self::STATUS_LOST]);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
            
            // Initialize movement history
            if (empty($model->movement_history)) {
                $model->movement_history = [];
            }
            
            // Add initial creation entry
            $history = $model->movement_history;
            array_unshift($history, [
                'action' => 'created',
                'timestamp' => Carbon::now()->toISOString(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
                'initial_location' => $model->current_location,
                'initial_handler' => $model->current_handler,
                'initial_status' => $model->status,
            ]);
            $model->movement_history = $history;
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}