<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNSSocialHistoryService;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\Services\TemplateService;
class VNSSocialHistoryController extends Controller
{
    protected $vnsSocialHistoryService;
    protected $templateService;
    protected const MODULE_NAME="VNS Social History";
    /**
     * Constructor
     *
     * @param VNSSocialHistoryService $vnsSocialHistoryService
     */
    public function __construct(VNSSocialHistoryService $vnsSocialHistoryService,TemplateService $templateService)
    {
        $this->middleware('permission:vns-social-history|add-vns-social-history|edit-vns-social-history|delete-vns-social-history|export-vns-social-history', ['only' => ['index', 'getData']]);
        $this->middleware('permission:vns-social-history', ['only' => ['index']]);
        $this->middleware('permission:add-vns-social-history', ['only' => ['save']]);
        $this->middleware('permission:edit-vns-social-history', ['only' => ['edit', 'updateAjax']]);
        $this->middleware('permission:delete-vns-social-history', ['only' => ['destroy']]);
        $this->middleware('permission:export-vns-social-history', ['only' => ['exportCSV']]);

        $this->middleware('auth');
        $this->vnsSocialHistoryService = $vnsSocialHistoryService;
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = $this->templateService->getTemplateListByVNS();
        return view('vns_social_history.index', compact('templates'));
    }

    /**
     * Get social history data for AJAX listing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $data['query'] = $this->vnsSocialHistoryService->getList($request->all());
        return view("vns_social_history.ajax_list", $data);
    }

    /**
     * Save VNS Social History via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'template_id' => 'required|integer|exists:template_master,id',
            'social_history_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {

            $socialHistory = $this->vnsSocialHistoryService->createSocialHistory([
                'template_id' =>$request->template_id,
                'name' =>$request->social_history_name,
                'default_value' =>$request->default_value
            ]);
           
            $saveLog = [
                'type' => 'Create VNS Social History',
                'link' => url('/vns-social-history/save'),
                'module' => self::MODULE_NAME,
                'object_id' => $socialHistory->id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has create VNS Social History',
                'new_response' => serialize($request->all()),
            ];
            $this->commonLog($saveLog);

            return response()->json([
                'error_msg' => "VNS Social History created successfully",
               
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error creating social history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $socialHistory = $this->vnsSocialHistoryService->getSocialHistoryById($id);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'data' => $socialHistory
                ], 200);
            }

            return view('vns_social_history.edit', compact('socialHistory'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error_msg' => 'Social History not found'
                ], 404);
            }
            return redirect()->route('vns-social-history.index')->with('error', 'Social History not found.');
        }
    }

    /**
     * Update VNS Social History via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAjax(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'template_id' => 'required|integer|exists:template_master,id',
            'social_history_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {

            $getDetails = $this->vnsSocialHistoryService->getSocialHistoryById($request->record_id);
            $this->vnsSocialHistoryService->updateSocialHistory($request->record_id, [
                'template_id' => $request->template_id,
                'name' => $request->social_history_name,
                'default_value' => $request->default_value
            ]);

            $saveLog = [
                'type' => 'Update VNS Social History',
                'link' => url('/vns-social-history/update'),
                'module' => self::MODULE_NAME,
                'object_id' =>$request->record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has update VNS Social History',
                'new_response' => serialize($request->all()),
                'old_response' => serialize($getDetails->toArray()),
            ];
            $this->commonLog($saveLog);

            return response()->json([
                'error_msg' => 'VNS Social History updated successfully',
                'data' => array('id'=>$request->record_id)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error updating social history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth = auth()->user();
        try {
            $this->vnsSocialHistoryService->deleteSocialHistory($id);
            $saveLog = [
                'type' => 'Delete VNS Social History',
                'link' => url('/vns-social-history').'/'.$id,
                'module' =>self::MODULE_NAME,
                'object_id' =>$id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has deleted VNS Social History',
                'new_response' => serialize(array('id'=>$id,'del_flag'=>'Y')),
            ];
            
            $this->commonLog($saveLog);
            return response()->json([
                'status' => true,
                'error_msg' => 'VNS Social History deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error_msg' => "Sorry, something went wrong. Please try again."
            ], 500);
        }
    }

    /**
     * Export VNS Social History to CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request)
    {
        try {
            // Get filtered data
            $socialHistoryRecords = $this->vnsSocialHistoryService->getList($request->all(), false);

            // Set filename
            $filename = 'vns_social_history_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create callback for streaming CSV
            $callback = function() use ($socialHistoryRecords) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Template Name',
                    'Name',
                    'Created Date',
                    'Created By',
                    'Updated Date',
                    'Updated By'
                ]);

                // Add data rows
                foreach ($socialHistoryRecords as $record) {
                    fputcsv($file, [
                        $record->id,
                        $record->template_name ?? 'N/A',
                        $record->name,
                        $record->created_date ? date('m/d/Y h:i A', strtotime($record->created_date)) : '',
                        ($record->first_name ?? '') . ' ' . ($record->last_name ?? ''),
                        $record->updated_date ? date('m/d/Y h:i A', strtotime($record->updated_date)) : '',
                        $record->updated_by ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting CSV: ' . $e->getMessage());
        }
    }

    /**
     * Get social history by template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTemplate(Request $request)
    {
        $getDetails = $this->vnsSocialHistoryService->fetchHistoryByTemplateId($request->id);
        return response()->json([
            'status' => true,
            'error_msg' => 'VNS Social History fetched successfully.',
            'data' => $getDetails
        ], 200);
    }

    public function commonLog($response){
        $ipaddress = Utility::getIP();
        
        $insertLog = [
            'type' => $response['type'],
            'link' =>$response['link'],
            'module' =>$response['module'],
            'object_id' =>$response['object_id'],
            'message' =>$response['message'],
            'new_response' =>$response['new_response'],
            'ip' => $ipaddress,
        ];

        if(isset($response['old_response'])){
            $insertLog['old_response'] = $response['old_response'];
        }
        LogsService::save($insertLog);
    }
}
