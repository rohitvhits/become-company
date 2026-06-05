<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNSProcedureService;
use App\Services\TemplateService;
use App\Services\LogsService;
use App\Helpers\Utility;
class VNSProcedureController extends Controller
{
    protected $vnsProcedureService;
    protected $templateService;
    protected const MODULE_NAME = "VNS Procedure";
    /**
     * Constructor
     *
     * @param VNSProcedureService $vnsProcedureService
     */
    public function __construct(VNSProcedureService $vnsProcedureService,TemplateService $templateService)
    {
        $this->middleware('permission:vns-procedure|add-vns-procedure|edit-vns-procedure|delete-vns-procedure|export-vns-procedure', ['only' => ['index', 'getData']]);
        $this->middleware('permission:vns-procedure', ['only' => ['index']]);
        $this->middleware('permission:add-vns-procedure', ['only' => ['save']]);
        $this->middleware('permission:edit-vns-procedure', ['only' => ['edit', 'updateAjax']]);
        $this->middleware('permission:delete-vns-procedure', ['only' => ['destroy']]);
        $this->middleware('permission:export-vns-procedure', ['only' => ['exportCSV']]);

        $this->middleware('auth');
        $this->vnsProcedureService = $vnsProcedureService;
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
        return view('vns_procedure.index', compact('templates'));
    }

    /**
     * Get procedures data for AJAX listing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $data['query'] = $this->vnsProcedureService->getList($request->all());
        return view("vns_procedure.ajax_list", $data);
    }


    /**
     * Save VNS Procedure via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'procedure_name' => 'required|string|max:255',
            'template_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $procedure = $this->vnsProcedureService->createProcedure($request->all());
            $saveLog = [
                'type' => 'Create VNS Procedure',
                'link' => url('/vns-procedure/save'),
                'module' => self::MODULE_NAME,
                'object_id' => $procedure->id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has create VNS Procedure',
                'new_response' => serialize($request->except('_token')),
            ];
            $this->commonLog($saveLog);
            return response()->json([
                'error_msg' => 'VNS Procedure created successfully',
                'data' => $procedure
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error creating procedure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $procedure = $this->vnsProcedureService->getProcedureById($id);
            return view('vns_procedure.show', compact('procedure'));
        } catch (\Exception $e) {
            return redirect()->route('vns-procedure.index')->with('error', 'Procedure not found.');
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
            $procedure = $this->vnsProcedureService->getProcedureById($id);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'data' => $procedure
                ], 200);
            }

            return view('vns_procedure.edit', compact('procedure'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error_msg' => 'Procedure not found'
                ], 404);
            }
            
        }
    }

    
    /**
     * Update VNS Procedure via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAjax(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'procedure_name' => 'required|string|max:255',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $getDetails = $this->vnsProcedureService->getProcedureById($request->record_id);
            $procedure = $this->vnsProcedureService->updateProcedure($request->record_id, $request->all());
            
            $saveLog = [
                'type' => 'Update VNS Procedure',
                'link' => url('/vns-procedure/update'),
                'module' => self::MODULE_NAME,
                'object_id' =>$request->record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has update VNS Procedure',
                'new_response' => serialize($request->except('_token')),
                'old_response' => serialize($getDetails->toArray()),
            ];
            $this->commonLog($saveLog);
            
            return response()->json([
                'error_msg' => 'VNS Procedure updated successfully',
                'data' => $procedure
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error updating procedure: ' . $e->getMessage()
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
            $this->vnsProcedureService->deleteProcedure($id);
            $saveLog = [
                'type' => 'Delete VNS Procedure',
                'link' => url('/vns-procedure').'/'.$id,
                'module' =>self::MODULE_NAME,
                'object_id' =>$id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has deleted VNS Procedure',
                'new_response' => serialize(array('id'=>$id,'del_flag'=>'Y')),
            ];
            
            $this->commonLog($saveLog);
            return response()->json([
                'status' => true,
                'error_msg' => 'VNS Procedure deleted successfully.'
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error_msg' => "SorrySorry, something went wrong. Please try again."
            ],500);
       
        }
    }

    /**
     * Export VNS Procedures to CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request)
    {
        try {
            // Get filtered data
            $procedures = $this->vnsProcedureService->getList($request->all(), false);

            // Set filename
            $filename = 'vns_procedures_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create callback for streaming CSV
            $callback = function() use ($procedures) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Procedure Name',
                    
                    'Created Date',
                    'Created By',
                    'Updated Date',
                    'Updated By'
                ]);

                // Add data rows
                foreach ($procedures as $procedure) {
                    fputcsv($file, [
                        $procedure->id,
                     
                        $procedure->template_name ?? 'N/A',
                        $procedure->created_date ? date('m/d/Y h:i A', strtotime($procedure->created_date)) : '',
                        ($procedure->first_name ?? '') . ' ' . ($procedure->last_name ?? ''),
                        $procedure->updated_date ? date('m/d/Y h:i A', strtotime($procedure->updated_date)) : '',
                        $procedure->updated_by ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting CSV: ' . $e->getMessage());
        }
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
