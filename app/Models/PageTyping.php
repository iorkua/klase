<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTyping extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'pagetypings';
    
    protected $fillable = [
        'file_indexing_id',
        'page_type',
        'page_subtype',
        'serial_number',
        'page_code',
        'file_path',
        'typed_by',
    ];

    protected $casts = [
        'serial_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fileIndexing()
    {
        return $this->belongsTo(FileIndexing::class, 'file_indexing_id');
    }

    public function typedBy()
    {
        return $this->belongsTo(User::class, 'typed_by');
    }
}