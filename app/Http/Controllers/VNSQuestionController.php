<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNSQuestionService;
use App\Services\TemplateService;
use App\Services\LogsService;
use App\Helpers\Utility;
class VNSQuestionController extends Controller
{
    protected $vnsQuestionService;
    protected $templateService;
    protected const MODULE_NAME = "VNS Question";
    /**
     * Constructor
     *
     * @param VNSQuestionService $vnsQuestionService
     */
    public function __construct(VNSQuestionService $vnsQuestionService,TemplateService $templateService)
    {
        $this->middleware('permission:vns-question|add-vns-question|edit-vns-question|delete-vns-question|export-vns-question', ['only' => ['index', 'getData']]);
        $this->middleware('permission:vns-question', ['only' => ['index']]);
        $this->middleware('permission:add-vns-question', ['only' => ['save']]);
        $this->middleware('permission:edit-vns-question', ['only' => ['edit', 'updateAjax']]);
        $this->middleware('permission:delete-vns-question', ['only' => ['destroy']]);
        $this->middleware('permission:export-vns-question', ['only' => ['exportCSV']]);

        $this->middleware('auth');
        $this->vnsQuestionService = $vnsQuestionService;
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
        return view('vns_question.index', compact('templates'));
    }

    /**
     * Get questions data for AJAX listing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $data['query'] = $this->vnsQuestionService->getList($request->all());
        return view("vns_question.ajax_list", $data);
    }

    /**
     * Save VNS Question via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'question_name' => 'required',
            'template_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $question = $this->vnsQuestionService->createQuestion($request->all());
            $saveLog = [
                'type' => 'Create VNS Question',
                'link' => url('/vns-question/save'),
                'module' => self::MODULE_NAME,
                'object_id' => $question->id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has create VNS Question',
                'new_response' => serialize($request->except('_token')),
            ];
            $this->commonLog($saveLog);
            return response()->json([
                'error_msg' => 'VNS Question created successfully',
                'data' => $question
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error creating question: ' . $e->getMessage()
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
            $question = $this->vnsQuestionService->getQuestionById($id);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'data' => $question
                ], 200);
            }

            return view('vns_question.edit', compact('question'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error_msg' => 'Question not found'
                ], 404);
            }
            return redirect()->route('vns-question.index')->with('error', 'Question not found.');
        }
    }

    /**
     * Update VNS Question via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAjax(Request $request)
    {
        $auth = auth()->user();
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'question_name' => 'required|string',
            'template_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $getDetails = $this->vnsQuestionService->getQuestionById($request->record_id);
            $question = $this->vnsQuestionService->updateQuestion($request->record_id, $request->all());

            $saveLog = [
                'type' => 'Update VNS Question',
                'link' => url('/vns-question/update'),
                'module' => self::MODULE_NAME,
                'object_id' =>$request->record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has update VNS Question',
                'new_response' => serialize($request->except('_token')),
                'old_response' => serialize($getDetails->toArray()),
            ];
            $this->commonLog($saveLog);

            return response()->json([
                'error_msg' => 'VNS Question updated successfully',
                'data' => $question
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error updating question: ' . $e->getMessage()
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
            $this->vnsQuestionService->deleteQuestion($id);
            $saveLog = [
                'type' => 'Delete VNS Question',
                'link' => url('/vns-question').'/'.$id,
                'module' =>self::MODULE_NAME,
                'object_id' =>$id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has deleted VNS Question',
                'new_response' => serialize(array('id'=>$id,'del_flag'=>'Y')),
            ];
            
            $this->commonLog($saveLog);
            return response()->json([
                'success' => true,
                'message' => 'VNS Question deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export VNS Questions to CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request)
    {
        try {
            // Get filtered data
            $questions = $this->vnsQuestionService->getList($request->all(), false);

            // Set filename
            $filename = 'vns_questions_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create callback for streaming CSV
            $callback = function() use ($questions) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Question Name',
                    'Template Type',
                    'Created Date',
                    'Created By',
                    'Updated Date',
                    'Updated By'
                ]);

                // Add data rows
                foreach ($questions as $question) {
                    fputcsv($file, [
                        $question->id,
                        $question->question_name,
                        $question->template_name ?? 'N/A',
                        $question->created_date ? date('m/d/Y h:i A', strtotime($question->created_date)) : '',
                        ($question->first_name ?? '') . ' ' . ($question->last_name ?? ''),
                        $question->updated_date ? date('m/d/Y h:i A', strtotime($question->updated_date)) : '',
                        $question->updated_by ?? ''
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
