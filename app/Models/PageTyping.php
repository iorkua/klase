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
        'page_number',
        'scanning_id',
        'notes',
        'is_important',
    ];

    protected $casts = [
        'serial_number' => 'integer',
        'page_number' => 'integer',
        'is_important' => 'boolean',
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

    public function scanning()
    {
        return $this->belongsTo(Scanning::class, 'scanning_id');
    }

    /**
     * Check if this page typing is for a PDF page
     */
    public function isPdfPage()
    {
        return strpos($this->file_path, '#page=') !== false;
    }

    /**
     * Get the PDF page number if this is a PDF page
     */
    public function getPdfPageNumber()
    {
        if ($this->isPdfPage()) {
            preg_match('/#page=(\d+)/', $this->file_path, $matches);
            return isset($matches[1]) ? (int)$matches[1] : null;
        }
        return null;
    }

    /**
     * Get the base file path without PDF page reference
     */
    public function getBaseFilePath()
    {
        if ($this->isPdfPage()) {
            return preg_replace('/#page=\d+/', '', $this->file_path);
        }
        return $this->file_path;
    }
}