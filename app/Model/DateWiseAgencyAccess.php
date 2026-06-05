<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\DateWiseAgencyAccessDetail;
class DateWiseAgencyAccess extends Model
{
    public $timestamps = false;
    protected $table = 'date_view_agency_access';
    protected $fillable = ['id', 'agency_id', 'user_id','type','del_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by','start_date','end_date','permanent_access'];

    public function createdUserDetails(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedUserDetails(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function dateWiseUserDetails()
    {
        return $this->hasMany(DateWiseAgencyAccessDetail::class, 'date_view_agency_access_id', 'id')->where('del_flag','N');
    }
}
