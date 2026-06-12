<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Master;
use Carbon\Carbon;
class MasterService{
    protected const COMMON_DATE ='Y-m-d H:i:s';

	public function save($data){
		$auth = auth()->user();
		$data['created_at'] = Carbon::now()->format(self::COMMON_DATE);
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new Master($data);
		return $insert->save();
	}

	public function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] =  Carbon::now()->format(self::COMMON_DATE);
		$data['updated_by'] = $auth['id'];
		return Master::where($where)->update($data);
	}

	public function softDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] =  Carbon::now()->format(self::COMMON_DATE);
		$data['deleted_by'] = $auth['id'];
		
		return Master::where($where)->update($data);
	}

    public function getList($search,$type,$paginate=true){
        $query = Master::select('master_table.id','master_table.name','master_table.types','master_table.created_at','master_table.created_by','master_table.is_disable','users.first_name','users.last_name','master_table.enabled_nybest_user')
        ->leftjoin('users',function($join){
            $join->on('users.id','=','master_table.created_by');
        })
        ->where('master_table.del_flag','N')->whereIn('master_type_fk',$type);

        if(isset($search['type']) && $search['type'] !=""){
            $query->where('master_table.types',$search['type']);
        }
        if(isset($search['service_name']) && $search['service_name'] !=""){
            $query->where('master_table.name', 'like', "%{$search['service_name']}%");
        }
        $query->orderBy('id','desc');
        return $paginate? $query->paginate(50): $query->get();
    }

	public function getDetailsById($id){
		return Master::select('id','name','master_type_fk','types','package_id','enabled_nybest_user','description','user_id','status','order_no','public_id','is_disable','enabled_nybest_date_time','enabled_nybest_by')->where('del_flag','N')->where('id',$id)->first();
	}

	public function getMasterDeatils($st,$type){
		return Master::where('id', $st)->where('master_type_fk', 11)->where('del_flag', 'N')
				->where(DB::raw('LOWER(types)'), strtolower($type))->first();
	}

	public function getAllDataByMasterTypeFk($serviceId){
		return Master::select('id', 'master_type_fk', 'name', 'description', 'types', 'is_disable', 'status')->whereIn('master_type_fk', $serviceId)->where('del_flag', 'N')->get();
	}

	public function getAllName(){
		return Master::where('master_table.del_flag','N')->pluck('name','id')->toArray();
	}
	
    public function getNamesByIds(array $ids)
    {
        return Master::whereIn('id', $ids)->pluck('name', 'id');
    }

    public function getServiceRequestNewWithCondition(string $type, string $agencyId = '')
   {
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N')->whereRaw('LOWER(types) = ?', [strtolower($type)]);
		$query->where('master_type_fk', 11);
		$query->where('is_disable', 1);
		$query->whereRaw('(public_id IS NULL OR public_id !=1 )');
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		return $query->get();
	}

	/*****Use for Cronjob */
	public function getAllServiceByMasterTypeFK(){
		return Master::where('del_flag', 'N')
            ->where('master_type_fk', 11)
            ->get();
	}
/*****Use for Cronjob */
	public function getDetailsByName($type,$serviceTrimmed){
		return Master::where('del_flag', 'N')
                        ->where('master_type_fk', 11)
                        ->where('types', trim($type))
                        ->where('name', $serviceTrimmed)
                        ->first();
	}

	public function cronJobSave($data){
		return Master::create($data);
	}
}