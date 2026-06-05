<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLogList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'dob',
        'patient_id',
        'vendor_name',
        'service_type',
        'services',
        'ppd_q',
        'bill',
        'cash',
        'card',
        'insurance',
        'location',
        'initials',
    ];

    protected $casts = [
        'dob' => 'date',
        'cash' => 'decimal:2',
        'card' => 'decimal:2',
    ];
    protected $table = 'payment_logs';
    /**
     * Get the database fields for mapping
     */
    public static function getMappableFields()
    {
        return [
            'name' => 'Name',
            'dob' => 'DOB',
            'patient_id' => 'Portal ID',
            'vendor_name' => 'Vendor Name',
            'service_type' => 'Initial or Annual',
            'services' => 'Services',
            'ppd_q' => 'PPD/Q',
            'bill' => 'BILL',
            'cash' => 'CASH',
            'card' => 'CARD',
            'insurance' => 'INSURANCE',
            'location' => 'LOCATION',
            'initials' => 'Initials',
        ];
    }
}
