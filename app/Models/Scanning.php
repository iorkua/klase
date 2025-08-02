<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FileIndexing;
use App\Models\User;
use App\Models\PageTyping;

class Scanning extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'scannings';
    
    protected $fillable = [
        'file_indexing_id',
        'document_path',
        'uploaded_by',
        'status',
        'original_filename',
        'paper_size',
        'document_type',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fileIndexing()
    {
        return $this->belongsTo(FileIndexing::class, 'file_indexing_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function pagetypings()
    {
        return $this->hasMany(PageTyping::class, 'scanning_id');
    }
}