<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRecord extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';
    protected $table = 'property_records';

    protected $fillable = [
        'mlsFNo',
        'kangisFileNo', 
        'NewKANGISFileno',
        'title_type',
        'transaction_type',
        'transaction_date',
        'serialNo',
        'pageNo',
        'volumeNo',
        'regNo',
        'instrument_type',
        'period',
        'period_unit',
        'Assignor',
        'Assignee',
        'Mortgagor',
        'Mortgagee',
        'Surrenderor',
        'Surrenderee',
        'Lessor',
        'Lessee',
        'Grantor',
        'Grantee',
        'property_description',
        'location',
        'plot_no',
        'lgsaOrCity',
        'layout',
        'schedule',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function fileIndexing()
    {
        return $this->belongsTo(FileIndexing::class, 'kangisFileNo', 'file_number');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
