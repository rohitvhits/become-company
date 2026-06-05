<?php

namespace App\Http\Controllers;

use App\Services\DateWiseAgencyAccessService;
use App\Services\DateWiseAgencyAccessDetailService;
use App\Services\DateWiseAgencyAccessLogService;
use App\Services\LogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Helpers\Utility;

class DateWiseAgencyAccessController extends Controller
{
    protected $dateWiseAgencyAccessService;
    protected $dateWiseAgencyAccessDetailService;
    protected $dateWiseAgencyAccessLogService;

    protected const VALIDATION_CODE = 422;
    protected const ERROR_CODE = 500;
    protected const SUCCESS_CODE = 200;

    public function __construct(
        DateWiseAgencyAccessService $dateWiseAgencyAccessService,
        DateWiseAgencyAccessDetailService $dateWiseAgencyAccessDetailService,
        DateWiseAgencyAccessLogService $dateWiseAgencyAccessLogService
    ) {
        $this->middleware('permission:date-wise-agency-view', ['only' => ['agencyWiseDateAccessList', 'agencyWiseSave']]);
        $this->dateWiseAgencyAccessService = $dateWiseAgencyAccessService;
        $this->dateWiseAgencyAccessDetailService = $dateWiseAgencyAccessDetailService;
        $this->dateWiseAgencyAccessLogService = $dateWiseAgencyAccessLogService;
    }

    public function agencyWiseDateAccessList(Request $request)
    {
        $query = $this->dateWiseAgencyAccessService->getListByAgencyId($request->agency_id);
        $this->populatePermissions($query);

        return view('agency._partial.date_agency_wise_access.date_wise_agency_list', ['query' => $query]);
    }

    public function userWiseDateAccessList(Request $request)
    {
        $query = $this->dateWiseAgencyAccessService->getListByUserId($request->user_id);
        $this->populatePermissions($query);

        // Check if user has permanent restriction
        $hasPermanentRestriction = $this->hasPermanentRestriction($request->user_id);

        return view('user._partial.date_user_wise_access.date_wise_user_list', [
            'query' => $query,
            'hasPermanentRestriction' => $hasPermanentRestriction
        ]);
    }

    public function save(Request $request)
    {
        return $this->saveRecord($request);
    }

    public function saveUserDateAccess(Request $request)
    {
        return $this->saveRecord($request);
    }

    public function update(Request $request)
    {
        return $this->updateRecord($request);
    }

    public function updateUserDateAccess(Request $request)
    {
        return $this->updateRecord($request);
    }

    public function edit(Request $request)
    {
        return $this->editOrGet($request);
    }

    public function editUserDateAccess(Request $request)
    {
        return $this->editOrGet($request);
    }

    public function delete(Request $request)
    {
        return $this->deleteRecord($request);
    }

    public function deleteUserDateAccess(Request $request)
    {
        return $this->deleteRecord($request);
    }

    /**
     * Check if a user has permanent restriction
     * @param int $userId
     * @return bool
     */
    public function hasPermanentRestriction($userId)
    {
        $restriction = $this->dateWiseAgencyAccessService->getPermanentUser($userId);
        return !is_null($restriction);
    }

    /**
     * Get all permanent restrictions for a user
     * @param int $userId
     * @return array
     */
    public function getUserPermanentRestrictions($userId)
    {
        return $this->dateWiseAgencyAccessService->getUserPermanentRestrictions($userId);
    }

    /**
     * Check if user has existing entries before setting permanent restriction
     */
    public function checkExistingEntriesUser(Request $request)
    {
        $userId = $request->user_id;

        // Check for permanent restriction
        $hasPermanent = $this->hasPermanentRestriction($userId);

        if ($hasPermanent) {
            return response()->json([
                'has_entries' => true,
                'has_permanent' => true,
                'count' => 1,
                'message' => 'User already has permanent restriction set.'
            ], self::SUCCESS_CODE);
        }

        // Check for date-based entries
        $existingEntries = $this->dateWiseAgencyAccessService->getListByUserId($userId);
        $count = count($existingEntries);

        return response()->json([
            'has_entries' => $count > 0,
            'has_permanent' => false,
            'count' => $count,
            'message' => $count > 0
                ? "User has {$count} existing date-based restriction(s). These will be replaced with permanent restriction."
                : 'No existing restrictions found.'
        ], self::SUCCESS_CODE);
    }

    public function setPermanentRestrictionUser(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        // Check if user already has permanent restriction
        if ($this->hasPermanentRestriction($request->user_id)) {
            return response()->json([
                'error_msg' => 'This user already has permanent restriction set.',
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        // Check if user has any existing date-based entries and soft-delete them
        $existingEntries = $this->dateWiseAgencyAccessService->getListByUserId($request->user_id);
        if (count($existingEntries) > 0) {
            foreach ($existingEntries as $entry) {
                // Soft delete existing entries as they will be replaced with permanent restriction
                $this->dateWiseAgencyAccessService->softDelete(['del_flag' => 'Y'], ['id' => $entry->id]);
                $this->dateWiseAgencyAccessDetailService->softDelete(['del_flag' => 'Y'], ['date_view_agency_access_id' => $entry->id]);
            }
        }

        // All permissions to be restricted
        $allPermissions = Utility::staticDateWiseAgencyAccess();

        // Create main record with permanent access
        $data = [
            'type' => 'All',
            'user_id' => $request->user_id,
            'start_date' => null,
            'end_date' => null,
            'permanent_access' => 1,
        ];

        $lastId = $this->dateWiseAgencyAccessService->save($data);

        if ($lastId) {
            // Create detail records for each permission
            foreach ($allPermissions as $key => $permissionName) {
                $this->dateWiseAgencyAccessDetailService->save([
                    'date_view_agency_access_id' => $lastId,
                    'permission' => $key,
                    'start_date' => null,
                    'end_date' => null,
                ]);
            }

            $logData = [
                'user_id' => $request->user_id,
                'permissions' => array_values($allPermissions),
                'permanent_access' => 1
            ];

            $message = $user->first_name . ' ' . $user->last_name . " has set permanent restriction for user.";

            $logResponse = [
                'type' => 'Set Permanent Restriction User',
                'link' => url('date-wise-agency-access/set-permanent-restriction-user'),
                'module' => 'User',
                'object_id' => $request->user_id,
                'message' => $message,
                'new_response' => serialize($logData),
            ];

            $this->logBulkSmsAction($logResponse);

            $dateWiseAgencyAccessLog = [
                'agency_id' => null,
                'date_wise_agency_id' => $lastId,
                'type' => 'Add Permanent Restriction',
                'message' => $message,
                'new_response' => serialize($logData),
            ];

            $this->dateWiseAgencyAccessLog($dateWiseAgencyAccessLog);

            return response()->json([
                'error_msg' => "Permanent restriction successfully set for all permissions"
            ], self::SUCCESS_CODE);
        }

        return response()->json(['error_msg' => __('message.error.message')], self::ERROR_CODE);
    }

    public function removePermanentRestrictionUser(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        // Check if user has permanent restriction
        $permanentRestriction = $this->dateWiseAgencyAccessService->getPermanentUser($request->user_id);

        if (!$permanentRestriction) {
            return response()->json([
                'error_msg' => 'No permanent restriction found for this user.',
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        // Get old data for logging
        $oldData = [
            'user_id' => $request->user_id,
            'permanent_access' => 1
        ];

        // Soft delete the permanent restriction
        $deleted = $this->dateWiseAgencyAccessService->softDelete(['del_flag' => 'Y'], ['id' => $permanentRestriction->id]);
        $this->dateWiseAgencyAccessDetailService->softDelete(['del_flag' => 'Y'], ['date_view_agency_access_id' => $permanentRestriction->id]);

        if ($deleted) {
            $message = $user->first_name . ' ' . $user->last_name . " has removed permanent restriction for user.";

            $logResponse = [
                'type' => 'Remove Permanent Restriction User',
                'link' => url('date-wise-agency-access/remove-permanent-restriction-user'),
                'module' => 'User',
                'object_id' => $request->user_id,
                'message' => $message,
                'old_response' => serialize($oldData),
            ];

            $this->logBulkSmsAction($logResponse);

            $dateWiseAgencyAccessLog = [
                'agency_id' => null,
                'date_wise_agency_id' => $permanentRestriction->id,
                'type' => 'Remove Permanent Restriction',
                'message' => $message,
                'old_response' => serialize($oldData),
            ];

            $this->dateWiseAgencyAccessLog($dateWiseAgencyAccessLog);

            return response()->json([
                'error_msg' => "Permanent restriction successfully removed"
            ], self::SUCCESS_CODE);
        }

        return response()->json(['error_msg' => __('message.error.message')], self::ERROR_CODE);
    }

    private function saveRecord(Request $request){
        $user = auth()->user();

        // Check if permanent access is enabled
        $isPermanent = $request->permanent_access == 1;

        // Make dates required only if permanent access is not enabled
        $rules = [
            'type' => 'required',
        ];

        if (!$isPermanent) {
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules, [
            'type.required' => 'Please select Permission',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        $data =[
            'type' => 'All',
            'start_date' => $isPermanent ? null : Utility::convertYMD($request->start_date),
            'end_date' => $isPermanent ? null : Utility::convertYMD($request->end_date),
            'permanent_access' => $isPermanent ? 1 : 0,
        ];
        $actionModule = "Agency";
        $objectId = $request->agency_id;
        if(isset($request->agency_id)){
            $data['agency_id'] = $request->agency_id;
            $link = url("date-wise-agency-access/save-date-view-agency-view");
        }else{
            $actionModule = "User";
            $data['user_id'] = $request->user_id;
            $objectId = $request->user_id;
            $link = url("date-wise-agency-access/save-date-view-user-view");
        }
        $lastId = $this->dateWiseAgencyAccessService->save($data);
        if ($lastId && !empty($request->type[0])) {
            $allPermission = Utility::staticDateWiseAgencyAccess();
            $fetchAllPermissions = [];

            foreach ($request->type as $permission) {
                $this->dateWiseAgencyAccessDetailService->save([
                    'date_view_agency_access_id' => $lastId,
                    'permission' => $permission,
                    'start_date' => $isPermanent ? null : $data['start_date'],
                    'end_date' => $isPermanent ? null : $data['end_date'],
                ]);

                $fetchAllPermissions[] = $allPermission[$permission];
            }

            $logData = $request->except('_token');
            $logData['type'] = $fetchAllPermissions;

           
            $message = $user->first_name . ' ' . $user->last_name . " has created date-wise ".$actionModule." permission.";

            $logResponse = [
                'type' => 'Add Date Wise '.ucfirst($actionModule)." "
                .'Permission',
                'link' => $link,
                'module' => $actionModule,
                'object_id' => $objectId,
                'message' => $message,
                'new_response' => serialize($logData),
            ];

            $this->logBulkSmsAction($logResponse);

            $dateWiseAgencyAccessLog = [
                'agency_id' => $request->agency_id,
                'date_wise_agency_id' => $lastId,
                'type' => ucfirst("Add"),
                'message' => $message,
                'new_response' => serialize($logData),
            ];

            $this->dateWiseAgencyAccessLog($dateWiseAgencyAccessLog);

            return response()->json(['error_msg' => "Date Wise ".$actionModule." permission successfully created"], self::SUCCESS_CODE);
        }

        return response()->json(['error_msg' => [__('message.error.message')]], self::ERROR_CODE);

    }
    
    private function editOrGet(Request $request)
    {
        $details = $this->dateWiseAgencyAccessService->getDetailsById($request->id);
        $final = [];
        if (isset($details->id)) {

            $final =[
                'id'=>$details->id,
                'start_date'=>Utility::convertMDY($details->start_date),
                'end_date'=>Utility::convertMDY($details->end_date),
                'permission'=>array_column($details->dateWiseUserDetails->toArray(), 'permission'),
                'permanent_access'=>$details->permanent_access ?? 0
            ];

            if(isset($details->agency_id) && $details->agency_id !=""){
                $final['agency_id'] = $details->agency_id;
            }

            if(isset($details->user_id) && $details->user_id !=""){
                $final['user_id'] = $details->user_id;
            }
        }

        return response()->json(['error_msg' => "Date Wise user permission successfully retrieved", 'data' => $final], self::SUCCESS_CODE);
    }

    private function updateRecord(Request $request){
        $user = auth()->user();

        // Check if permanent access is enabled
        $isPermanent = $request->permanent_access == 1;

        // Make dates required only if permanent access is not enabled
        $rules = [
            'edit_type' => 'required',
        ];

        if (!$isPermanent) {
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules,[
            'edit_type.required'=>'Please select Permission'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], self::VALIDATION_CODE);
        } else {

            $getDetails = $this->dateWiseAgencyAccessService->getDetailsById($request->date_view_access_id);
            $updateData = [
                'start_date' => $isPermanent ? null : Utility::convertYMD($request->start_date),
                'end_date' => $isPermanent ? null : Utility::convertYMD($request->end_date),
                'permanent_access' => $isPermanent ? 1 : 0,
            ];
            $update = $this->dateWiseAgencyAccessService->update($updateData, array('id'=>$request->date_view_access_id));
            $lastId = $request->date_view_access_id;
            if($update && !empty($request->edit_type[0])){
                $this->dateWiseAgencyAccessDetailService->softDelete(array('del_flag'=>'Y'),array('date_view_agency_access_id'=>$request->date_view_access_id));
               
                $allPermission = Utility::staticDateWiseAgencyAccess();
                $fetchAllPermissions = [];
                foreach($request->edit_type as $permission){
                    $this->dateWiseAgencyAccessDetailService->save([
                        'date_view_agency_access_id' => $lastId,
                        'permission'                 => $permission,
                        'start_date'                 => $isPermanent ? null : Utility::convertYMD($request->start_date),
                        'end_date'                   => $isPermanent ? null : Utility::convertYMD($request->end_date),
                    ]);

                    $fetchAllPermissions[] = $allPermission[$permission];
                }
                $logData = $request->except('_token');
                $logData['type'] = $fetchAllPermissions;

                if(isset($request->agency_id)){
                    $actionModule = "agency";
                    $link = url('date-wise-agency-access/update-date-view-agency-view');
                    $objectId = $request->agency_id;
                }else{
                    $actionModule = "user";
                    $link = url('date-wise-agency-access/update-date-view-user-view');
                    $objectId = $request->user_id;
                }
                $message  = $user->first_name . ' ' . $user->last_name . " has update date-wise ".$actionModule." permission.";
                $logResponse = [
                    'type'=>'Update Date Wise '.ucfirst($actionModule).' Permission',
                    'link'=>$link,
                    'module'=>ucfirst($actionModule),
                    'object_id'=>$objectId,
                    'message'=>$message,
                    'old_response'=>serialize($getDetails),
                    'new_response'=>serialize($logData)
                ];
                $this->logBulkSmsAction($logResponse);

                $dateWiseAgencyAccessLog = [
                    'agency_id'=>$request->agency_id,
                    'date_wise_agency_id'=>$lastId,
                    'type'=>'Update',
                    'message'=>$message,
                    'new_response'=>serialize($logData),

                    'old_response'=>serialize($getDetails),
                ];
                $this->dateWiseAgencyAccessLog($dateWiseAgencyAccessLog);
                return response()->json(['error_msg' => "Date Wise ".$actionModule." permission successfully updated"], self::SUCCESS_CODE);
            }
            return response()->json(['error_msg' => __('message.error.message')], self::ERROR_CODE);
        }
    }


    private function deleteRecord(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), ['id' => 'required']);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], self::VALIDATION_CODE);
        }

        $update = $this->dateWiseAgencyAccessService->softDelete(['del_flag' => "Y"], ['id' => $request->id]);

        if ($update) {
            if(isset($request->agency_id)){
                $actionModule = "agency";
                $objectId =$request->agency_id;
                $link = url('date-wise-agency-access/delete-date-view-agency-view');
            }else{
                $actionModule = "user";
                $objectId =$request->user_id;
                $link = url('date-wise-agency-access/delete-date-view-user-view');
            }
            $message = $user->first_name . ' ' . $user->last_name . " has deleted date-wise ".$actionModule." permission.";
            $logResponse = [
                'type' => 'Delete Date Wise '.ucfirst($actionModule).' Permission',
                'link' => $link,
                'module' => ucfirst($actionModule),
                'object_id' => $objectId,
                'message' => $message,
            ];
            $this->logBulkSmsAction($logResponse);

            $dateWiseAgencyAccessLog = [
                'agency_id' => $request->agency_id,
                'date_wise_agency_id' => $request->id,
                'type' => 'Delete',
                'message' => $message,
            ];

            $this->dateWiseAgencyAccessLog($dateWiseAgencyAccessLog);

            return response()->json(['error_msg' => "Date Wise user permission successfully deleted"], self::SUCCESS_CODE);
        }

        return response()->json(['error_msg' => __('message.error.message')], self::ERROR_CODE);
    }

    private function populatePermissions($query)
    {
        if(!empty($query[0])){
            foreach($query as $val){
                $permissionDetails = $this->dateWiseAgencyAccessDetailService->getDetailsByDateWiseAgencyPermission($val->id);
                $ptrPermission = [];
                $allPermission = Utility::staticDateWiseAgencyAccess();
              
                if(!empty($permissionDetails[0])){
                    foreach($permissionDetails as $prt){
                        $ptrPermission[] = $allPermission[$prt->permission];
                    }
                }

                $val->permission = implode(',',$ptrPermission);
            }
        }
    }

    private function logBulkSmsAction(array $response): void
    {
        LogsService::save([
            'type' => $response['type'],
            'link' => $response['link'],
            'module' => $response['module'],
            'object_id' => $response['object_id'],
            'message' => $response['message'],
            'old_response' => $response['old_response'] ?? '',
            'new_response' => $response['new_response'] ?? '',
            'ip' => Utility::getIP(),
        ]);
    }

    private function dateWiseAgencyAccessLog(array $response): void
    {
        $this->dateWiseAgencyAccessLogService->save([
            'agency_id' => $response['agency_id'],
            'date_wise_agency_id' => $response['date_wise_agency_id'],
            'type' => $response['type'],
            'message' => $response['message'],
            'old_response' => $response['old_response'] ?? '',
            'new_response' => $response['new_response'] ?? '',
            'ip' => Utility::getIP(),
        ]);
    }
}