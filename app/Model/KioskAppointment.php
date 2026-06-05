<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KioskAppointment extends Model
{
    protected $fillable = [
        'token_no',
        'api_appointment_id',
        'type',
        'first_name',
        'middle_name',
        'last_name',
        'mobile',
        'phone',
        'dob',
        'gender',
        'email',
        'address1',
        'city',
        'state',
        'zip_code',
        'service_id',
        'agency_id',
        'location_id',
        'language',
        'insurance_name',
        'status',
        'checked_in_at',
    ];

    protected $casts = [
        'service_id' => 'array',
        'checked_in_at' => 'datetime',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(KioskAppointmentDocument::class, 'kiosk_appointment_id');
    }

    /**
     * Generate a unique token number for the appointment
     * Format: LOC-YYYYMMDD-XXX (e.g., L1-20251224-001)
     */
    public static function generateTokenNo($locationId = null): string
    {
        $today = now()->format('Ymd');
        $locationPrefix = $locationId ? 'L' . $locationId : 'L0';

        // Get the last token number for today and this location
        $lastAppointment = self::where('token_no', 'like', $locationPrefix  . '-%')
            ->orderBy('token_no', 'desc')
            ->first();

        if ($lastAppointment && $lastAppointment->token_no) {
            // Extract the sequence number and increment
            $parts = explode('-', $lastAppointment->token_no);
            $sequence = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
        } else {
            $sequence = 1;
        }

        return $locationPrefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}