<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'qc_status',
        'qc_reviewed_by',
        'qc_reviewed_at',
        'qc_overridden',
        'qc_override_note',
        'has_qc_issues',
    ];

    protected $casts = [
        'serial_number' => 'integer',
        'page_number' => 'integer',
        'is_important' => 'boolean',
        'qc_overridden' => 'boolean',
        'has_qc_issues' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'qc_reviewed_at' => 'datetime',
    ];

    // QC Status constants
    const QC_STATUS_PENDING = 'pending';
    const QC_STATUS_PASSED = 'passed';
    const QC_STATUS_FAILED = 'failed';

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

    public function qcReviewer()
    {
        return $this->belongsTo(User::class, 'qc_reviewed_by');
    }

    /**
     * Check if page typing has passed QC
     */
    public function hasPassedQC()
    {
        return $this->qc_status === self::QC_STATUS_PASSED;
    }

    /**
     * Check if page typing has failed QC
     */
    public function hasFailedQC()
    {
        return $this->qc_status === self::QC_STATUS_FAILED;
    }

    /**
     * Check if page typing is pending QC
     */
    public function isPendingQC()
    {
        return $this->qc_status === self::QC_STATUS_PENDING;
    }

    /**
     * Check if QC has been overridden
     */
    public function isQCOverridden()
    {
        return $this->qc_overridden === true;
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