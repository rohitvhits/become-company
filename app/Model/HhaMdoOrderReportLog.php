<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HhaMdoOrderReportLog extends Model
{
    public $timestamps = false;
    protected $table = 'hha_send_mdo_log';
    protected $fillable = [
        'id',
        'agency_id',
        'patient_id',
        'hha_patient_id',
        'patient_document_id',
        'hha_document_id',
        'attachment',
        'send_response',
        'del_flag',
        'created_date',
        'created_by',
        'updated_date',
        'updated_by',
        'deleted_date',
        'deleted_by',
        'return_response'
    ];
}
