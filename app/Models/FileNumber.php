<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileNumber extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'fileNumber';
    
    protected $fillable = [
        'type',
        'kangisFileNo',
        'mlsfNo',
        'NewKANGISFileNo',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Scope to get only generated file numbers
     */
    public function scopeGenerated($query)
    {
        return $query->where('type', 'Generated');
    }

    /**
     * Get the next serial number for a given year and land use type
     */
    public static function getNextSerial($year = null, $landUsePrefix = null)
    {
        $year = $year ?: date('Y');
        
        $query = self::where('type', 'Generated')
                    ->where('mlsfNo', 'like', '%-' . $year . '-%');
        
        if ($landUsePrefix) {
            $query->where('mlsfNo', 'like', $landUsePrefix . '-%');
        }
        
        $lastRecord = $query->orderByRaw('CAST(RIGHT(mlsfNo, 4) AS INT) DESC')->first();
        
        if ($lastRecord) {
            $lastSerial = (int) substr($lastRecord->mlsfNo, -4);
            return $lastSerial + 1;
        }
        
        return 1;
    }

    /**
     * Generate MLSF number
     */
    public static function generateMlsfNo($landUse, $year, $serial)
    {
        $paddedSerial = str_pad($serial, 4, '0', STR_PAD_LEFT);
        return $landUse . '-' . $year . '-' . $paddedSerial;
    }

    /**
     * Check if MLSF number exists
     */
    public static function mlsfExists($mlsfNo)
    {
        return self::where('mlsfNo', $mlsfNo)->exists();
    }
}