<?php
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Master extends Model
{
	use Notifiable;
	protected $table = 'master_table';
	protected $fillable = ['id', 'master_type_fk', 'name', 'description', 'del_flag', 'user_id', 'status', 'order_no', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at', 'public_id', 'types','enabled_nybest_user','package_id','enabled_nybest_date_time','enabled_nybest_by'];

	public static function getDataMaster($master_type_fk = null)
	{
		return  DB::table("master_table")
			->selectRaw("master_table.*,master_type.name as typeName")
			->leftjoin("master_type", function ($join) {
				$join->on("master_type.id", "=", "master_table.master_type_fk");
			})
			->where('master_type_fk', '=', $master_type_fk)

			->where('del_flag', '=', 'N')
			->orderBy('id','desc')
			->paginate(100);
	}

	public static function getagencyWiseDocumentList($id)
	{
		$currentUser = auth()->user();
		$query = Master::whereRaw('del_flag="N" ');

		if (!in_array($currentUser['user_type_fk'], array('3', '4'))) {
			//$agencyArray=	array(0,$currentUser['agency_fk']);
			//$query->whereIn('user_id',$agencyArray);
		} else {
			$query->whereRaw('user_id ="' . $id . '" or user_id IS NULL');
		}
		$query->where('master_type_fk', 9);
		return  $query->get();
	}
	
	public static function getServiceRequest($check_disable="")
	{
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->where('is_disable',1);
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		return  $query->orderBy('name','asc')->get();
	}

	public static function getServiceRequestNew($id,$check_disable="")
	{
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N');
		$query->where('types', $id);
		$query->where('master_type_fk', 11);
		if(!empty($check_disable)){
			$query->where('is_disable',1);
		}
		$mysql = $query->get();


		return $mysql;
	}

	public static function NewgetagencyWiseDocumentList($id)
	{

		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N');

		$query->whereRaw('user_id ="' . $id . '"');

		$query->where('master_type_fk', 9);
		$mysql = $query->get();

		return $mysql;
	}
	public static function getDocumentListByAgencyId($id)
	{

		$query = Master::where('del_flag', 'N')->where('user_id', $id)->where('user_id', '!=', '')->get();
		return $query;
	}

	public static function GetAll()
	{

		$query = Master::where('del_flag', 'N')->where('master_type_fk', 9)->get();
		return $query;
	}

	public static function  geServiceName($serviceId)
	{

		$query = Master::select('name')->whereRaw('id IN(' . $serviceId . ')')->where('del_flag', 'N')->get();
		return $query;
	}
	public static function GetAllRecord()
	{

		$query = Master::select('id', 'name')->where('del_flag', 'N')->where('master_type_fk', 3)->get();
		return $query;
	}
	public static function  getDetailsById($serviceId)
	{

		$query = Master::select('name')->where('id', $serviceId)->where('del_flag', 'N')->first();
		return $query;
	}

	public static function  getAllDataByMasterTypeFk($serviceId)
	{

		$query = Master::select('id', 'master_type_fk', 'name', 'description', 'types', 'is_disable', 'status')->whereIn('master_type_fk', $serviceId)->where('del_flag', 'N')->get();
		return $query;
	}
	public static function  getServiceName($name)
	{

		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->where('types', 'Caregiver')->where('name', $name)->first();
		return $query;
	}

	public static function masterTableDataDelete($masterTypeFk, $ids)
	{
		return Master::whereIn('id', $ids)->where('master_type_fk', $masterTypeFk)->update(['del_flag' => 'Y', 'deleted_at' => date('Y-m-d h:i:s'), 'deleted_by' => auth()->user()->id]);
	}

	public static function getServiceAll()
	{
		$currentUser = auth()->user();
		$query = Master::select('id','name','types')->where('del_flag', 'N');
		$query->where('master_type_fk', 11)->whereNotNull('types')->where('is_disable', 1);
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		$mysql = $query->get();


		return $mysql;
	}

	public static function getServiceTypeBase($type, $id)
	{
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->whereNotNull('types');
		if ($type != "") {
			$query->where('types', $type);
		}

		if ($id != "") {
			$query->where('id', $id);
		}
		$query = $query->get();
		return $query;
	}

	public static function getServiceRequestNewWithCondition($id,$agencyId="")
	{
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N')->whereRaw('LOWER(types) = ?', [strtolower($id)]);
		$query->where('master_type_fk', 11);
		$query->where('is_disable', 1);
		$query->whereRaw('(public_id IS NULL OR public_id !=1 )');
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		return $query->get();
	}

	public static function getServiceTypeBaseWithArray($type, $id)
	{
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->whereNotNull('types');
		$query->whereIn('types', $type);
		if ($id != "") {
			$query->where('id', $id);
		}
		$query = $query->get();
		return $query;
	}

	public static function getMasterListUsingPluck($type)
	{
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->whereNotNull('types')->where('is_disable', 1);

		if ($type != "") {
			$query->where('types', $type);
		}

		return $query->pluck('name', 'id');
	}

	public static function getDiscipline()
	{
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 26);
		return $query->get();
	}

	public static function getServicePaymentData($service_id = "", $agency_id)
	{
		$query = Master::select('master_table.id as service_id', 'name', 'agency_id', 'amount')
			->leftjoin("rate_card", function ($join) {
				$join->on("master_table.id", "=", "rate_card.service_id");
				$join->where("rate_card.deleted_flag", "=", "N");
			});

		if (!empty($service_id)) {
			$query->whereIn('master_table.id', $service_id);
		}
		if (!empty($agency_id)) {
			$query->where(function ($q) use ($agency_id) {
				$q->where('agency_id', $agency_id);
				$q->orWhere('agency_id', 0);
				$q->orWhereNull('agency_id');
			});
		}
		$data = $query->get()->toArray();
		return $data;
	}
	public static function getRecordById($id)
	{
		$query = Master::select('id', 'name')->where('del_flag', 'N')->whereIn('id', $id)->get();
		return $query;
	}

	public static function  getAllDataByMasterTypeFkWithPaginate($serviceId)
	{
		$query = Master::select('id', 'master_type_fk', 'name', 'description', 'types', 'is_disable', 'status')->whereIn('master_type_fk', $serviceId)->where('del_flag', 'N')->paginate(50);
		return $query;
	}

	public static function getServiceRequestNewWithConditionNew($id,$agencyId="")
	{
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N');
		$query->where('types', $id);
		$query->where('master_type_fk', 11);
		$query->where('is_disable', 1);
		
		return  $query->get();
	}

	public static function getServiceRequestWithDisabled()
	{
		$currentUser = auth()->user(); 
		$query = Master::where('del_flag', 'N');
		$query->where('master_type_fk', 11)->where('is_disable', 1);
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		$mysql = $query->orderBy('name','asc')->get();
		return $mysql;
	}

	public static function getMasterListPluck($type="",$ids="")
	{
		$query = Master::where('del_flag', 'N')->where('master_type_fk', 11)->whereNotNull('types')->where('is_disable', 1);
		if ($type != "") {
			$query->where('types', $type);
		}
		if(!empty($ids)){
			$query->whereIn('id',$ids);
		}
		return $query->pluck('name', 'id');
	}

	public static function getServiceRequestTypeWise($type)
	{
		$currentUser = auth()->user();
		$query = Master::select('id','name')->where('del_flag', 'N');
		$query->where('master_type_fk', 11)->where('is_disable', 1);
		if ($type != "") {
			$query->where('types', $type);
		}
		if($currentUser->agency_fk !=""){
			$query->where('enabled_nybest_user',0);
		}
		return $query->orderBy('name','asc')->get();
	}

	public static function getServiceRequestNewWithApi($id,$check_disable="")
	{
		$currentUser = auth()->user();
		$query = Master::where('del_flag', 'N');
		$query->where('types', $id);
		$query->where('master_type_fk', 11);
		if(!empty($check_disable)){
			$query->where('is_disable',1);
		}
		$mysql = $query->get();


		return $mysql;
	}

	public static function getMdoSourceList(){
		return Master::where('del_flag', 'N')->where('master_type_fk', 35)->pluck('name','id');
	}
}