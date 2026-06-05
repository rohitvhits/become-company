<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HHAOffice extends Model
{
    public $timestamps = false;
    protected $table = 'hha_office';
    protected $guarded = ["id"];

    public static function getOfficeList(){
        return HHAOffice::select('id','office_name','office_id')->where('del_flag','N')->groupBy('office_id')->orderBy('office_name','asc')->get();
    }

    public static function getOfficeListByAgencyId($id){
        return HHAOffice::select('id','office_name','office_id')->where('del_flag','N')->where('agency_fk',$id)->groupBy('office_id')->orderBy('office_name','asc')->get();
    }
}