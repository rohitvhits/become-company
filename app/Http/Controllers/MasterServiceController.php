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
use App\Services\LogsService;
use App\Helpers\Utility;
use App\Services\MasterService;
use URL;
class MasterServiceController extends BaseController
{
    protected $masterService="";
    protected const SUCCESS_CODE=200;
    protected const ERROR_CODE=500;
    protected const VALIDATION_CODE=422;
    protected const MODULE_NAME ="Master Service";
    public function __construct(MasterService $masterService)
    {
        $this->middleware('auth');
        $this->middleware('permission:service-master-list|service-master-add|service-master-edit|service-master-delete|service-master-assign-nybest', ['only' => ['index','ajaxList']]);
        $this->middleware('permission:service-master-delete', ['only' => ['delete']]);
        $this->masterService = $masterService;
       
    }
    public function index(Request $request)
    {
        $data['menu'] = "Master";
        $data['title'] = "Master List";
        $data['user'] = auth()->user();
        if(auth()->user()->agency_fk !=""){
			abort(404);
		}
      
        return view("service_master.index", $data);
    }

    public function ajaxList(Request $request){
       $data['query'] = $this->masterService->getList($request->all(),[11]);
       return view("service_master.ajax_list", $data);
    }
    
    public function save(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'service_name' => 'required',
            'service_type' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], self::VALIDATION_CODE);
        } else {
            $finalData =[
                'master_type_fk'=>11,
                'name'=>$request->service_name,
                'types'=>$request->service_type
            ];

            if(isset($request->enabled_nybest_user)){
                $finalData['enabled_nybest_user'] =$request->enabled_nybest_user;
            }
            $save = $this->masterService->save($finalData);
            if($save){
                $logArray =[
                    'link'=>URL::to('/service-master/save'),
                    'type'=>'Create a Service',
                    'module'=>self::MODULE_NAME,
                    'object_id'=>$save,
                    'new_response'=>$finalData,
                    'message'=>$auth->first_name . ' ' . $auth->last_name . ' has create a new service'
                ];
                $this->saveLog($logArray);
                return response()->json(['error_msg' => "Service successfully added", 'data' => array()], self::SUCCESS_CODE);
            }else{
                return response()->json(['error_msg' =>"Sorry, something went wrong. Please try again.", 'data' => array()], self::ERROR_CODE);
            }
        }
    }

    public function edit(Request $request,$id){
        $query = $this->masterService->getDetailsById($id);
        if(isset($query->id)){
            return response()->json(['error_msg' => "Service details", 'data' => array($query)], self::SUCCESS_CODE);
        }else{
            return response()->json(['error_msg' =>"Record does not exits", 'data' => array()], self::ERROR_CODE);
        }
    }

    public function update(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'service_name' => 'required',
            'service_type' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], self::VALIDATION_CODE);
        } else {
            $query = $this->masterService->getDetailsById($request->record_id);
            $finalData =[
                'name'=>$request->service_name,
                'types'=>$request->service_type
            ];

            $enabled_nybest_user=0;
            if(isset($request->enabled_nybest_user)){
                $enabled_nybest_user=1;
            }
            $finalData['enabled_nybest_user'] =$enabled_nybest_user;
            $this->masterService->update($finalData,array('id'=>$request->record_id));

            $logArray =[
                'old_response'=>$query->toArray(),
                'link'=>URL::to('/service-master/update'),
                'type'=>'Update Service',
                'module'=>self::MODULE_NAME,
                'object_id'=>$request->record_id,
                'new_response'=>$finalData,
                'message'=>$auth->first_name . ' ' . $auth->last_name . ' has update service'
            ];
            $this->saveLog($logArray);
            return response()->json(['error_msg' => "Service successfully updated",'data' => array()], self::SUCCESS_CODE);
        }
    }

    public function delete(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], self::VALIDATION_CODE);
        } else {
            $delete = $this->masterService->softDelete(array('del_flag'=>'Y'),array('id'=>$request->id));
            if($delete){
                $logArray =[
                    'link'=>URL::to('/service-master/delete'),
                    'type'=>'Delete Service',
                    'module'=>self::MODULE_NAME,
                    'object_id'=>$request->id,
                    'message'=>$auth->first_name . ' ' . $auth->last_name . ' has delete service'
                ];
                $this->saveLog($logArray);
                return response()->json(['error_msg' => "Service successfully deleted", 'data' => array()], self::SUCCESS_CODE);
            }else{
                return response()->json(['error_msg' =>"Sorry, something went wrong. Please try again.", 'data' => array()], self::ERROR_CODE);
            }
        }
    }

    public function enabledService(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], self::VALIDATION_CODE);
        } else {
            $query = $this->masterService->getDetailsById($request->id);
            if(isset($query->id)){
                $is_disable = 1;
                $disabled_datetime =date('Y-m-d H:i:s');
                $disabled_by = $auth->id;
                $message = "enabled successfully";
                $messageStatus ="enabled";
                if($query->is_disable ==1){
                    $is_disable=0;
                    $message = "disabled successfully";
                    $messageStatus ="disabled";
                }

                $finalData = array('is_disable'=>$is_disable,'disabled_datetime'=>$disabled_datetime,'disabled_by'=>$disabled_by);
                $this->masterService->update($finalData,array('id'=>$request->id));
                $logArray =[
                    'old_response'=>$query->toArray(),
                    'link'=>URL::to('/service-master/enabled-service'),
                    'type'=>'Change Service Status',
                    'module'=>self::MODULE_NAME,
                    'object_id'=>$request->id,
                    'new_response'=>$finalData,
                    'message'=>$auth->first_name . ' ' . $auth->last_name . ' has '.$messageStatus.' service'
                ];
                $this->saveLog($logArray);
                
                return response()->json(['error_msg' => "Service ".$message,'data' => array()], self::SUCCESS_CODE);
            }
        }
    }

    public function enabledNyBestUser(Request $request)
    {
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data'      => []
            ], self::VALIDATION_CODE);
        }

        $query = $this->masterService->getDetailsById($request->id);

        if (!$query) {
            return response()->json([
                'error_msg' => 'Record not found',
                'data'      => []
            ], self::VALIDATION_CODE);
        }

        $result = $this->toggleEnabledDisableNyUser($query, $auth->id);
        $oldData = $query->toArray();
        $enabled="enabled";
  
        if($result['response']['enabled_nybest_user'] ==1){
            $enabled="disabled";
        }
        $logArray =[
            'old_response'=>$oldData,
            'link'=>URL::to('/service-master/enabled-nybest-user'),
            'type'=>'Change Nybest User Status',
            'module'=>self::MODULE_NAME,
            'object_id'=>$request->id,
            'new_response'=>$result['response'],
            'message'=>$auth->first_name . ' ' . $auth->last_name . ' has ' . $enabled . ' NyBest User'
        ];
        $this->saveLog($logArray);
        return response()->json([
            'error_msg' => "Nybest User " . $result['message'],
            'data'      => []
        ], self::SUCCESS_CODE);
    }

    /**
     * Shared logic to handle enable/disable.
     */
    private function toggleEnabledDisableNyUser($record, $userId): array
    {
        $isDisable = 1;
        $disabledAt = date('Y-m-d H:i:s');
        $disabledBy = $userId;
        $message = 'enabled successfully';

        if ($record->enabled_nybest_user == 1) {
            $isDisable = 0;
            $message = 'disabled successfully';
        }

        $response = [
            'enabled_nybest_user'         => $isDisable,
            'enabled_nybest_date_time'  => $disabledAt,
            'enabled_nybest_by'     => $disabledBy
        ];
        $this->masterService->update(
            $response,
            ['id' => $record->id]
        );
        return ['message' => $message,'response'=>$response];
    }

    protected function saveLog($data){
        $ipaddress = Utility::getIP();

        $insertLog = [
            'type' =>  $data['type'],
            'object_id' => $data['object_id'],
            'link'=>$data['link'],
            'module'=>$data['module'],
            'message' =>$data['message'],
            'ip' => $ipaddress,
        ];

        if(isset($data['old_response'])){
            $insertLog['old_response'] = serialize($data['old_response']);
        }

        if(isset($data['new_response'])){
            $insertLog['new_response'] = serialize($data['new_response']);
        }
        return LogsService::save($insertLog);
    }
}