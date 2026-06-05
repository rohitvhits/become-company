<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNSProcedureResultService;
use App\Services\VNSProcedureService;

class VNSProcedureResultController extends Controller
{
    protected $vnsProcedureResultService;
    protected $vnsProcedureService;

    /**
     * Constructor
     *
     * @param VNSProcedureResultService $vnsProcedureResultService
     * @param VNSProcedureService $vnsProcedureService
     */
    public function __construct(
        VNSProcedureResultService $vnsProcedureResultService,
        VNSProcedureService $vnsProcedureService
    ) {
        $this->middleware('permission:vns-procedure-result|add-vns-procedure-result|edit-vns-procedure-result|delete-vns-procedure-result|export-vns-procedure-result', ['only' => ['index', 'getData']]);
        $this->middleware('permission:vns-procedure-result', ['only' => ['index']]);
        $this->middleware('permission:add-vns-procedure-result', ['only' => ['save']]);
        $this->middleware('permission:edit-vns-procedure-result', ['only' => ['edit', 'updateAjax']]);
        $this->middleware('permission:delete-vns-procedure-result', ['only' => ['destroy']]);
        $this->middleware('permission:export-vns-procedure-result', ['only' => ['exportCSV']]);

        $this->middleware('auth');
        $this->vnsProcedureResultService = $vnsProcedureResultService;
        $this->vnsProcedureService = $vnsProcedureService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $procedures = $this->vnsProcedureService->getAllProcedures();
        return view('vns_procedure_result.index', compact('procedures'));
    }

    /**
     * Get procedure results data for AJAX listing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $data['query'] = $this->vnsProcedureResultService->getList($request->all());
        return view("vns_procedure_result.ajax_list", $data);
    }

    /**
     * Save VNS Procedure Result via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'vns_procedure_id' => 'required|integer|exists:vns_procedure,id',
            'names' => 'required|array',
            'names.*' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $createdResults = [];
            $vnsProcedureId = $request->vns_procedure_id;

            // Filter out empty names and create results
            $names = array_filter($request->names, function($name) {
                return !empty(trim($name));
            });

            foreach ($names as $name) {
                $procedureResult = $this->vnsProcedureResultService->createProcedureResult([
                    'vns_procedure_id' => $vnsProcedureId,
                    'name' => trim($name)
                ]);
                $createdResults[] = $procedureResult;
            }

            $count = count($createdResults);
            return response()->json([
                'error_msg' => $count > 1
                    ? "{$count} VNS Procedure Results created successfully"
                    : 'VNS Procedure Result created successfully',
                'data' => $createdResults
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error creating procedure result: ' . $e->getMessage()
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
            $procedureResult = $this->vnsProcedureResultService->getProcedureResultById($id);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'data' => $procedureResult
                ], 200);
            }

            return view('vns_procedure_result.edit', compact('procedureResult'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error_msg' => 'Procedure Result not found'
                ], 404);
            }
            return redirect()->route('vns-procedure-result.index')->with('error', 'Procedure Result not found.');
        }
    }

    /**
     * Update VNS Procedure Result via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAjax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'vns_procedure_id' => 'required|integer|exists:vns_procedure,id',
            'names' => 'required|array',
            'names.*' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
        }

        try {
            $recordId = $request->record_id;
            $vnsProcedureId = $request->vns_procedure_id;

            // Filter out empty names
            $names = array_filter($request->names, function($name) {
                return !empty(trim($name));
            });

            // Update the first record with the first name
            $firstNameIndex = 0;
            $firstProcedureResult = $this->vnsProcedureResultService->updateProcedureResult($recordId, [
                'vns_procedure_id' => $vnsProcedureId,
                'name' => trim($names[$firstNameIndex])
            ]);

            $updatedResults = [$firstProcedureResult];

            // Create additional records for remaining names
            for ($i = 1; $i < count($names); $i++) {
                $procedureResult = $this->vnsProcedureResultService->createProcedureResult([
                    'vns_procedure_id' => $vnsProcedureId,
                    'name' => trim($names[$i])
                ]);
                $updatedResults[] = $procedureResult;
            }

            $count = count($updatedResults);
            return response()->json([
                'error_msg' => $count > 1
                    ? "VNS Procedure Result updated and {$count} results saved successfully"
                    : 'VNS Procedure Result updated successfully',
                'data' => $updatedResults
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error updating procedure result: ' . $e->getMessage()
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
        try {
            $this->vnsProcedureResultService->deleteProcedureResult($id);
            return response()->json([
                'status' => true,
                'error_msg' => 'VNS Procedure Result deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error_msg' => "Sorry, something went wrong. Please try again."
            ], 500);
        }
    }

    /**
     * Export VNS Procedure Results to CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request)
    {
        try {
            // Get filtered data
            $procedureResults = $this->vnsProcedureResultService->getList($request->all(), false);

            // Set filename
            $filename = 'vns_procedure_results_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create callback for streaming CSV
            $callback = function() use ($procedureResults) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'VNS Procedure',
                    'Result Name',
                    'Created Date',
                    'Created By',
                    'Updated Date',
                    'Updated By'
                ]);

                // Add data rows
                foreach ($procedureResults as $result) {
                    fputcsv($file, [
                        $result->id,
                        $result->procedure_name ?? 'N/A',
                        $result->name,
                        $result->created_date ? date('m/d/Y h:i A', strtotime($result->created_date)) : '',
                        ($result->first_name ?? '') . ' ' . ($result->last_name ?? ''),
                        $result->updated_date ? date('m/d/Y h:i A', strtotime($result->updated_date)) : '',
                        $result->updated_by ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting CSV: ' . $e->getMessage());
        }
    }

    public function byProcedure(Request $request){
       $getDetails = $this->vnsProcedureResultService->fetchResultByProcedureId($request->id);
       return response()->json([
        'status' => true,
        'error_msg' => 'VNS Procedure Result deleted successfully.',
        'data'=>$getDetails
    ], 200);
    }
}
