<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KioskAppointmentDocument extends Model
{
    protected $fillable = [
        'kiosk_appointment_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(KioskAppointment::class, 'kiosk_appointment_id');
    }
}