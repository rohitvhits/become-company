<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientDocusignDetail extends Model
{
    use Notifiable;

    public $timestamps = false;
    protected $table = 'patient_docusign_detail';
    protected $fillable = ['id', 'document_report_id', 'type', 'template_id', 'user_id', 'data', 'created_date', 'del_flag', 'temp_img'];
}
