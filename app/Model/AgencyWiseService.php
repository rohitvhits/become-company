<?php

namespace App\Model;

use App\Master;
use Illuminate\Database\Eloquent\Model;

class AgencyWiseService extends Model
{
    protected $table = "agency_wise_service";
    protected $guarded = ["id"];
    public $timestamps = false;

    public static function insert($insertData){
        $insertData['created_date'] = date('Y-m-d H:i:s');
        return AgencyWiseService::create($insertData);
    }
    
    public static function getService($agencyId){
        return AgencyWiseService::where('agency_id',$agencyId)->where('del_flag', 'N')->paginate(10);
    }

    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseService::where($where)->update($data);
        return $update;
    }
    
}
