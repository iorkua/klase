<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApplicationMother;
use App\Models\Scanning;
use App\Models\PageTyping;

class FileIndexing extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'file_indexings';
    
    protected $fillable = [
        'main_application_id',
        'subapplication_id',
        'st_fillno',
        'file_number',
        'file_title',
        'land_use_type',
        'plot_number',
        'district',
        'lga',
        'has_cofo',
        'is_merged',
        'has_transaction',
        'is_problematic',
        'is_co_owned_plot',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'has_cofo' => 'boolean',
        'is_merged' => 'boolean',
        'has_transaction' => 'boolean',
        'is_problematic' => 'boolean',
        'is_co_owned_plot' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mainApplication()
    {
        return $this->belongsTo(ApplicationMother::class, 'main_application_id');
    }

    public function scannings()
    {
        return $this->hasMany(Scanning::class, 'file_indexing_id');
    }

    public function pagetypings()
    {
        return $this->hasMany(PageTyping::class, 'file_indexing_id');
    }

    public function getStatusAttribute()
    {
        $hasScanning = $this->scannings()->exists();
        $hasPageTyping = $this->pagetypings()->exists();
        
        if ($hasPageTyping) {
            return 'Typed';
        } elseif ($hasScanning) {
            return 'Scanned';
        } else {
            return 'Indexed';
        }
    }
}