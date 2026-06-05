<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Master;
use App\MasterType;
use App\Exmed;
use App\Services\LogsService;
use App\Services\AgencyWiseServiceService;
use App\Helpers\Utility;
class MasterController extends BaseController
{
    protected   $agencyWiseServiceService="";
    public function __construct(AgencyWiseServiceService $agencyWiseServiceService)
    {
        $this->middleware('auth');
        $this->agencyWiseServiceService	=$agencyWiseServiceService;
    }
    public function index(Request $request)
    {
        $data['menu'] = "Master";
        $data['title'] = "Master List";
        $data['user'] = auth()->user();
        if(auth()->user()->agency_fk !=""){
			abort(404);
		}
       $data['master_type_fk'] =  $master_type_fk= request('master_type_fk'); 
        $data['masterType'] = MasterType::where('id',$master_type_fk)->orderBy('name','asc')->get();
        $data['masterData'] = Master::getDataMaster($master_type_fk);
        return view("master_list", $data);
    }
    public function master_type_view(Request $request)
    {
        $data['menu'] = "Master";
        $data['title'] = "Master Type View";
        $data['user'] = auth()->user();
        if(auth()->user()->agency_fk !=""){
			abort(404);
		}
        
        $data['masterType'] = MasterType::orderBy('name','asc')->get();
        return view("master_type_view", $data);
    }
    public function add(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'master_type_fk' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect("/master")
                ->withErrors($validator, 'add_master')
                ->withInput();
        } else {
            $name = request('name');
            $master_type_fk = request('master_type_fk');
			$cused = 0;
			if(request('cused') ==1){
				$cused  = 1;
			}

            
            $data = array(
                'name' => $name,
                'master_type_fk' => $master_type_fk,
                'created_at' => date('Y-m-d h:i:s'),
                'created_by' => $user->id,
				'public_id'=>$cused
            );
            if($master_type_fk ==11){
                $data['types'] = $request->service_type;
            }
           
            $ins_test = new Master($data);
            $ins_test->save();
            $insert = $ins_test->id;
            
            if ($insert) {

                Session::flash('success', 'Master added successfully.');
                return redirect('/master?master_type_fk='.$master_type_fk);
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/master?master_type_fk='.$master_type_fk);
            }
        }
    }
    public function edit(Request $request)
    {

        $data['menu'] = "master";
        $data['title'] = "Master Edit";
        $data['user'] = auth()->user();
          $data['id'] = $id = request("id");
        $data['masterType'] = MasterType::get();
        $data['masterTypeAll'] = MasterType::get();
        $data['masterDetail']=$masterDetail = Master::where("id", $id)->get();

         $data['masterData'] = Master::getDataMaster($masterDetail[0]->master_type_fk);
       $data['master_type_fk'] = $masterDetail[0]->master_type_fk;
         return view('master_list', $data);
    }	
    public function update(Request $request)
    {
        $user = auth()->user();
        $data['id'] = $id = request("id");
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'master_type_fk' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect("/edit_master?id=$id")
                ->withErrors($validator, 'add_master')
                ->withInput();
        } else {
            $name = request('name');
            $master_type_fk = request('master_type_fk');
           /* if ($master_type_fk == 'superadmin') {
                $outlet = NULL;
            } else {
                $outlet = request('outlet');
            }*/
			$cused = 0;
			if(request('cused') ==1){
				$cused  = 1;
			}
            $data = array(
                'name' => $name,
                'master_type_fk' => $master_type_fk,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user->id,
				'public_id'=>$cused
            );
            $update = Master::where('id', $id)->update($data);
            if ($update) {
                Session::flash('success', 'User update successfully.');
                return redirect('/master?master_type_fk='.$master_type_fk);
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/master?master_type_fk='.$master_type_fk);
            }
        }
    }
    public function delete()
    {
        $user = auth()->user();
        $data['id'] = $id = request("id");
        $data['masterDetail']=$masterDetail = Master::where("id", $id)->get();
         $master_type_fk=$masterDetail[0]->master_type_fk;
        $update = Master::where('id', $id)->update(array('del_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id));
        if ($update) {
            Session::flash('success', 'Master delete successfully.');
            return redirect('/master?master_type_fk='.$master_type_fk);
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/master?master_type_fk='.$master_type_fk);
        }
    }
	
	function AjaxService(Request $request){
      
		$id = $request->input('id');
		$jsonencode = $request->input('jsonencode');
        $agency_id = $request->input('agency_id');
        $query  =$this->agencyWiseServiceService->ServiceListNewWithoutNyBestUser($id,$agency_id);
       
        if(!empty($query[0])){
           
            $serviceList = $query;
        }else{
            $serviceList = Master::getServiceRequestNewWithCondition($id,$agency_id);
        }
		
		$htmls = '';
		if(count($serviceList) >0){
			// $htmls = '<option value="">Select Service</option>';
			foreach($serviceList as $vs){
				$selected = '';
				if($jsonencode !=''){
					if(in_array($vs->id,$jsonencode)){
						$selected ="selected='selected'";
					}
				}
				$htmls .= '<option value="'.$vs->id.'" '.$selected.'>'.$vs->name.'</option>';
				
			}
		}
      
		return $htmls;
	}
    function insertLogs(Request $request)
    {
        /**
         * insert log in all view page
         */

        $ipaddress = Utility::getIP();
        $auth = auth()->user();
        $id = request('id');
        $link = request('link');
        $module=request('module');

        $insertLog = [
            'type' =>  'View',
            'object_id' => $id,
            'link'=>$link,
            'module'=>$module,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' has View page',
            'ip' => $ipaddress,
        ];
        $log = LogsService::save($insertLog);

        if ($log) {
            return response()->json([
                'message' => "view page",
                'status' => 'success',
            ], 200);
        }
        return response()->json([
            "timestamp"     => Carbon::now('UTC')->toDateTimeString(),
            "error"         => "Database Error",
            "message"       => "Unable to fetch data. Contact Support",
            "status"          => 'error'

        ], 500);
    }
    public function logView(Request $request)
    {
        $data['menu'] = "User Log";
        $data['user'] = $user = auth()->user();
        $data['id'] =request('id');
       
        if ($user['user_type_fk'] != 3) { 
            return abort(404);
        }

        return view("Logs/index", $data);
    }
    public function allLogList(Request $request)
    {

        $user = auth()->user();
        if ($user['user_type_fk'] != 3) {
            return abort(404);
        }
        $data['user'] = auth()->user();
        $data['userName'] = $username = request('username');
        $data['field'] = $field = request('field');
        $data['sort'] = $sort = request('sort');
        $data['createdAt'] = $createdAt = request('createdat');
        $data['logList'] = LogsService::getData($username, $createdAt, $field, $sort,'');

        return view("Logs/ajax-list", $data);
    }
    public function allLogExport(Request $request)
    {


        $user = auth()->user();

        $username = request('username');
        $field = request('field');
        $sort = request('sort');
        $createdAt = request('createdat');
        $loginLogs = LogsService::getData($username, $createdAt, $field, $sort,'export-data');

        $filename = 'Logs' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('User Name', 'Ip Address', 'Type', 'Module', 'Created Date');

        $callback = function () use ($loginLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($loginLogs as $list) {

                fputcsv($file, array(ucfirst($list->username), $list->ip, $list->type, $list->module, date('m/d/Y h:i A', strtotime($list->created_at))));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function deleteMultipleRecord(Request $request)
    {
        $deleted = Master::masterTableDataDelete($request['master_type_fk'],$request['ids']);
        if($deleted){
            return response()->json(["status"=>true,'message'=>"Deleted successfully."]);
        }else{
            return response()->json(["status"=>false,'message'=>"Something went to wrong!"]);
        }
    }

    function AjaxAllService(Request $request){ 
		$serviceList = Master::getServiceAll();
		return response()->json(["status"=>true,'message'=>"",'data'=>$serviceList]);
	}
	
    function changeStatusServices(Request $request){
        $response = Master::find($request->id);
		$data = [];
        $is_disable = 1;
		if($response->is_disable == 1){
            $is_disable = 0;
			$data['disabled_datetime'] = date('Y-m-d H:i:s');
			$data['disabled_by'] = date('Y-m-d H:i:s');
            
            $msg = 'Disabled successfully.';
		}else{
			$data['disabled_datetime'] = null;
			$data['disabled_by'] = null;
            $msg = 'Enabled successfully.';
		}
		$data['is_disable'] = $is_disable;
        $deleted = Master::where('id', $request->id)->update($data);
        if($deleted){
            return response()->json(["status"=>true,'message'=>$msg]);
        }else{
            return response()->json(["status"=>false,'message'=>"Something went to wrong!"]);
        }
    }

    function AjaxAllDiscipline(Request $request){ 
        $disciplineList = Master::getDiscipline();
        return response()->json(["status"=>true,'message'=>"",'data'=>$disciplineList]);
    }

    function agencyAjaxService(Request $request){ 
      
		$id = $request->input('id');
		$jsonencode = $request->input('jsonencode');
        $agency_id = $request->input('agency_id');
        $query  =$this->agencyWiseServiceService->ServiceListNew($id,$agency_id);
   
        if(!empty($query[0])){
            $serviceList = $query;
        }else{
            $serviceList = Master::getServiceRequestNewWithConditionNew($id,$agency_id);
        }
		
		$htmls = '';
		if(count($serviceList) >0){
			// $htmls = '<option value="">Select Service</option>';
			foreach($serviceList as $vs){
				$selected = '';
				if($jsonencode !=''){
					if(in_array($vs->id,$jsonencode)){
						$selected ="selected='selected'";
					}
				}
				$htmls .= '<option value="'.$vs->id.'" '.$selected.'>'.$vs->name.'</option>';
				
			}
		}
      
		return $htmls;
	}

    public function ajaxServiceWithJson(Request $request){
      
		$id = $request->input('id');
        $agency_id = $request->input('agency_id');
        $query  =$this->agencyWiseServiceService->ServiceListNewWithoutNyBestUser($id,$agency_id);
       
        if(!empty($query[0])){
           
            $serviceList = $query;
        }else{
            $serviceList = Master::getServiceRequestNewWithCondition($id,$agency_id);
        }
		
		return response()->json(['data'=>$serviceList]);
	}
}
