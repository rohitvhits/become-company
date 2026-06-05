<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DailyReferralEmailLog extends Model
{
    protected $table = 'daily_referral_email_logs';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'report_data' => 'array',
        'email_recipients' => 'array'
    ];

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}