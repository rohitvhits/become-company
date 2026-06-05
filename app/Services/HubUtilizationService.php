<?php
namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HubUtilization;
use App\Model\HubRecord;

class HubUtilizationService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new HubUtilization($data);
        $insert_id = $insert->save();

        return $insert_id;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = HubUtilization::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = HubUtilization::where($where)->update($data);
        return $update;
    }

    public static function getData($username = "", $createdAt = "", $field = "", $sort = "", $exportData = "")
    {

        $query = HubUtilization::selectRaw('hub_utilization.*,CONCAT(" ",users.first_name," ",users.last_name) as username')
            ->leftjoin('users', function ($join) {
                $join->on('hub_utilization.created_by', 'users.id');
            });

        if ($username) {
            $query->where(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%" . $username . "%");
        }
        if ($createdAt != '') {
            $explodes = explode('-', $createdAt);
            $query->whereBetween("hub_utilization.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }


        if ($field) {
            if ($field == 'user_name') {
                $query->orderBy('users.first_name', $sort);
            }

            if ($field == 'created_at') {
                $query->orderBy('logs.created_at', $sort);
            }
            if ($field == 'type') {
                $query->orderBy('logs.type', $sort);
            }
            if ($field == 'module') {
                $query->orderBy('logs.module', $sort);
            }
            if ($field == 'ip') {
                $query->orderBy('logs.ip', $sort);
            }
            if ($field == 'id') {
                $query->orderBy('logs.id', $sort);
            }
        } else {
            $query->orderBy('logs.id', 'desc');
        }
        if ($exportData) {
            return  $query->get();
        }
        return  $query->paginate(50);
    }

 
    
    public static function getDatByAllLog($id,$moduleName)
    {
        $query = HubUtilization::with('user')->where('object_id',$id)->where('created_by', auth()->user()->id)->where('module',$moduleName)->orderBy('id', 'desc')->paginate(10);
        return  $query;
    }


    public static function getDatByLogsId($id){
        return HubUtilization::with('user')->where('id',$id)->first();
    }
    public static function getAllAppointmentLogs($id,$moduleName)
    {
        $query = HubUtilization::with('user')->whereNull('deleted_at')->where('object_id',$id)->where('module',$moduleName)->orderBy('id', 'desc')->paginate(10);
        return  $query;
    }


    public function getAllHubUtilization($id="",$agencyId="",$ssn="",$search=array())
    {
        $query = HubUtilization::with('user:id,first_name,last_name')
            ->select('hub_utilization.*','hub_company.agency_name')
            ->leftjoin('hub_company', function ($join) {
                $join->on('hub_company.id', '=', 'hub_utilization.agency_id');
                $join->where('hub_company.delete_flag', 'N');
            });
        if($agencyId!=""){
        
            $query->where('hub_utilization.agency_id',$agencyId);
        }
        if($id != ""){
            $query->where('hub_utilization.hub_record_id',$id);
        }
        if($ssn != ""){
            $query->where('hub_utilization.ssn',$ssn);
        }
        if(isset($search['created_by']) && !empty($search['created_by'])){
            $query->where('hub_utilization.created_by',$search['created_by']);
        }
        if(isset($search['created_date']) && !empty($search['created_date'])){
            $explodes = explode('-', $search['created_date']);
            $query->whereBetween("hub_utilization.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }
        
        $query =  $query->orderBy('id', 'desc')->paginate();
        return  $query;
    }
    

    public static function getAllLogs($search)
    {
        $query = HubUtilization::select('id','object_id','created_by','created_at','module','type','ip','message','old_response','new_response')->with('user:id,first_name,last_name')->whereNull('deleted_at');
        if(isset($search['type']) && !empty($search['type'])){
            $query->where('type',$search['type']);
        }
        if(isset($search['module']) && !empty($search['module'])){
            $query->where('module',$search['module']);
        }
        if(isset($search['created_by']) && !empty($search['created_by'])){
            $query->where('logs.created_by',$search['created_by']);
        }
        if(isset($search['created_date']) && !empty($search['created_date'])){
            $explodes = explode('-', $search['created_date']);
            $query->whereBetween("logs.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }
        $query = $query->orderBy('id', 'desc')->simplePaginate(20);
        return  $query;
    }

    public function getAllType()
	{
		$query = HubUtilization::select('type')->whereNull('deleted_at')->whereNotnull('type')->groupBy('type');
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}

    public function getAllModule()
	{
		$query = HubUtilization::select('module')->whereNull('deleted_at')->whereNotnull('module')->groupBy('module');
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}

    public static function getDatByLogsRes($id){
        return HubUtilization::select('type','module','old_response','new_response')->where('id',$id)->first();
    }

    public static function getImportDuplicateSSN($record){
		$query = HubUtilization::where('first_name', $record['first_name'])->where('last_name',$record['last_name'])->where('dob',$record['dob'])->where('gender',$record['gender'])->where('email',$record['email'])->where('ssn',$record['ssn']);
		$query = $query->first();
		return $query;
	}

    public function checkDuplicateSSN($record,$id=""){
		$query = HubRecord::where('ssn', $record['ssn'])->where('deleted_flag', 'N');
		if($id !=""){
			$query->where('id','!=',$id);
		}
		$query = $query->first();
		return $query;
	}
}