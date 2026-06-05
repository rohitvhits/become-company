<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TaskHealthFlags extends Model
{
    protected $table = 'task_health_flags';

    protected $guarded = ["id"];

    public function pocCheckedByUser()          { return $this->belongsTo(User::class, 'poc_check_by',         'id'); }
    public function mdoCheckedByUser()          { return $this->belongsTo(User::class, 'mdo_check_by',         'id'); }
    public function alertCheckedByUser()        { return $this->belongsTo(User::class, 'alert_check_by',       'id'); }
    public function supervisionCheckedByUser()  { return $this->belongsTo(User::class, 'supervision_check_by', 'id'); }
    public function assessmentCheckedByUser()          { return $this->belongsTo(User::class, 'assessment_check_by',           'id'); }
    public function kardexCheckedByUser()              { return $this->belongsTo(User::class, 'kardex_check_by',               'id'); }
    public function patientPackageDocCheckedByUser()   { return $this->belongsTo(User::class, 'patient_package_doc_check_by',  'id'); }
    public function updatedByUser()                    { return $this->belongsTo(User::class, 'updated_by',                    'id'); }
}
