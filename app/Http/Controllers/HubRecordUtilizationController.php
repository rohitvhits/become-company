<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Model\HubCompany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\LocationMasterService;
use App\Services\HubLogsService;
use App\Services\HubUtilizationService;
use App\Services\HubUtilizationImportLogService;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Services\HubRecordDependentService;
class HubRecordUtilizationController extends BaseController
{

	protected $hubUtilizationImportLogService,$hubUtilizationService;
	public function __construct(HubUtilizationImportLogService $hubUtilizationImportLogService,HubUtilizationService $hubUtilizationService)
	{
		$this->middleware('permission:hub-utilization-report', ['only' => ['hubUtilizationReport', 'ajaxList']]);
		$this->middleware('permission:hub-utilization-report-export', ['only' => ['exportToCsv']]);
		$this->middleware('permission:hub-utilization-report-import', ['only' => ['hubImports']]);
        $this->middleware('auth');
		$this->hubUtilizationImportLogService = $hubUtilizationImportLogService;
		$this->hubUtilizationService = $hubUtilizationService;
		
	}

	public function ajaxList(Request $request)
	{
        $search_data = $request->all();
		$search_data['status'] = 'completed';
		$data['query'] = $this->hubUtilizationImportLogService->getImportLogs($search_data);
		
		return view("hubUtilization/ajax_list", $data);
	}
    

	public function hubUitlization(Request $request){
		$id = $request->hub_record_id;
		$agencyId = $request->hub_agency_id;
		$ssn = $request->ssn;
		$data['user'] = auth()->user();
		$data['hubUitlization'] =$this->hubUtilizationService->getAllHubUtilization($id,$agencyId,$ssn);

		return view("hubRecord.hub_uitlization_ajax_list", $data);
	}

	public function hubUtilizationReport(Request $request)
	{
		
		if (auth()->user()->agency_fk == "" && auth()->user()->show_hub == 1) {
			$angecyList = Cache::get('patient_master_locations', function () {
				return HubCompany::getAgencyListHub();
			}, 10);
			$data['agencyList'] = $angecyList;
			$data['auth'] = auth()->user();
		
			return view("hubUtilization/list", $data);
		}else{
			abort(404);
		}
	}

	public function hubImports(Request $request)
	{
		$auth = auth()->user();
		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$deactivated = 0;
		$errors = [];
		$header = [];
		$importedRecords = [];

		if (!$request->hasFile('images')) {
			return response()->json(['status' => false, 'message' => 'No file uploaded.']);
		}

		$file = $request->file('images');
		$extension = $file->getClientOriginalExtension();
		$path = $file->getRealPath();
		$fileName = $file->getClientOriginalName();
		$name = time() . '_' . $file->getClientOriginalName();
		$destinationPath = public_path('hubauitlizationupload');
		
		$file = $request->file('images');
		$newName = uniqid() . '.' . $file->getClientOriginalExtension();

		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			$file->move(public_path() . '/hubauitlizationupload', $newName);
			$path = public_path() . '/hubauitlizationupload/' . $newName;
			$url = URL::to('/').'/hubauitlizationupload/'.$newName;
		} else {
			$s3Path = 'hubauitlizationupload/' . $newName;
			Storage::disk('s3')->putFileAs('hubauitlizationupload', $file, $newName);
		}
		// Start import logging
		$logId =$this->hubUtilizationImportLogService->logImportStart(
			$fileName,
			$request->agency_id ?? $auth->agency_fk,
			0 // Will update total records after reading file
		);

		try {
			// Read file
			$rows = [];
			if (strtolower($extension) === 'csv') {
				$rows = array_map('str_getcsv', file($path));
			}
			
			if (empty($rows) || count($rows) < 2) {
				$this->hubUtilizationImportLogService->failImport($logId, 'File is empty or missing data.');
				return response()->json(['status' => false, 'message' => 'File is empty or missing data.']);
			}

			// Update total records count
			$this->hubUtilizationImportLogService->updateImportProgress($logId, [
				'total_records' => count($rows) - 1 // Exclude header row
			]);

			$header = array_map('trim', $rows[0]);
			// Map columns
			$map = [
				'LastName' => 'last_name',
				'FirstName' => 'first_name',
				'DOB' => 'dob',
				'Gender' => 'gender',
				'Email' => 'email',
				'SSN' => 'ssn',
				'Company' => 'company',
				'Utilization'=> 'utilization',
			];
			$colIndex = [];
			foreach ($map as $col => $field) {
				$idx = array_search($col, $header);
				if ($idx !== false) {
					$colIndex[$field] = $idx;
				}
			}
			$header = array_map('trim', $header);

			$expected = array_keys($map);
			$missing = array_diff($expected, $header);
			if (!empty($missing)) {
				$errors[] = "Missing required columns: " . implode(', ', $missing);
			}else{
				$flag = 0;
				// Status Update to deactivate all fields
				for ($i = 1; $i < count($rows); $i++) {
					
					$row = $rows[$i];
					if (count(array_filter($row)) == 0) {
						$flag++;
						$errors[] = "For Row $i: The following required columns are missing from your file: [FirstName, LastName, SSN, Email, DOB and Gender]. Please upload a valid file.";
						$skipped++;
					} // skip empty rows
					else{
						$record = [];
						foreach ($colIndex as $field => $idx) {
							$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
						}
												
						if (empty($record['first_name']) || empty($record['last_name']) || empty($record['email']) || empty($record['dob']) || empty($record['gender']) || empty($record['ssn'])) {
							$errors[] = "For Row $i: The following required columns are missing from your file: [FirstName, LastName, SSN, Email, DOB and Gender]. Please upload a valid file.";
							$skipped++;
							continue;
						}

						if (empty($record['ssn'])) {
							$errors[] = "For Row $i: SSN is required.";
							$skipped++;
							continue;
						}
						$existing = null;
						// Format DOB
						if (!empty($record['dob'])) {

							$dateStr = $record['dob'];
							$parts = explode('-', $dateStr);

							$exPort = "";
							$dateFlag = 0;
							if(isset($parts[2])){
								$exPort = $parts[2];
								
							}else{
								$parts = explode('/', $dateStr);
								$dateFlag = 1;
								$exPort = $parts[2];
							}

							$yearPart = trim($exPort);

							if($yearPart !=""){
								
								if(strlen($yearPart) == 2){
									
									if($dateFlag != 1){
										$date = Carbon::createFromFormat('m-d-y', $record['dob']);
									}else{
									
										$date = Carbon::createFromFormat('m/d/y', $record['dob']);
									}
									
									
									if ($date->year > date('Y') + 10) {
										$date = $date->subCentury();
									}
								}elseif (strlen($yearPart) == 4){
									$date  =$record['dob'];
								}
							}
							$record['dob'] = date('Y-m-d', strtotime($date));
							if($record['dob'] == '1969-12-31'){
								$record['dob'] = Utility::parseFlexibleDate($date);
							}
						}else{
							$record['dob'] = NULL;
						}
					
						$record['ssn'] = str_replace('-', '', $record['ssn']);
					
						// Check duplicate ssn
						$record['import_flag'] = 1;
						$agencyData = [];
						
								$checkSSN = $this->hubUtilizationService->checkDuplicateSSN($record);
								$agencyData = array(
									'hub_record_id' => $checkSSN->id??"",
									'agency_id' => $request->agency_id,
									
									'first_name' => !empty($record['first_name']) ? $record['first_name'] : NULL,
									'last_name' => !empty($record['last_name']) ? $record['last_name'] : NULL,
									'email' => $record['email']??'',
									'SSN' => $record['ssn']??'',
									'company' => $record['company']??'',
									'employee_code' => $record['employee_code']??'',
									'gender'  => $record['gender']??'',
									'dob' => !empty($record['dob']) ? date('Y-m-d', strtotime($record['dob'])) : NULL,
									'utilization' => $record['utilization']??'',
									
								);
							$insertID=	HubUtilizationService::save($agencyData);
							
							$updated++;
							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'Hub Utilization created',
								'link' => url('/hub-utilization-report/'),
								'module' => 'Hub Utilization',
								'object_id' => $insertID,
								'message' => $auth->first_name . ' ' . $auth->last_name . ' has activate Hub Record',
								'ip' => $ipaddress,
							];
							HubLogsService::save($insertLog);
					
						// Update progress every 10 records
						if ($i % 10 === 0) {
							$this->hubUtilizationImportLogService->updateImportProgress($logId, [
								'successful_records' => $imported,
								'failed_records' => $skipped,
								'updated_records' => $updated
							]);
						}
						$importedRecords[] = $agencyData;
					}
				}
		
				// Complete import logging
				$this->hubUtilizationImportLogService->completeImport($logId, [
					'successful_records' => $imported,
					'failed_records' => $skipped,
					'updated_records' => $updated,
					'deactivate_records' => 0
				], !empty($errors) ? json_encode($errors) : null);
			}
			$summary = [
				'imported' => $imported,
				'skipped' => $skipped,
				'updated' => $updated,
				'deactivated' => 0,
				'errors' => $errors,
			];

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Hub Utilization data imported',
				'link' => url('/hub-record/'),
				'module' => 'Hub Utilization Import',
				'message' => '"Import completed. "'.$imported.'" records were imported, " .
           "'.$updated.'" updated, "'.$skipped.'" skipped, " .
           "0" deactivated.',
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			if($imported == 0 && $skipped == 0 && $updated == 0){
				return response()->json(['status' => false, 'summary' => $summary,'message'=>$errors]);
			}else{
				return response()->json(['status' => true, 'summary' => $summary]);
			}

		} catch (\Exception $e) {
			// Log import failure
			return response()->json(['status' => false, 'message' => 'Error during import: ' . $e->getMessage()]);
		}
	}

	public function getImportLogs(Request $request)
    {
        $search_data = [
            'file_name' => $request->file_name,
            'status' => $request->status,
            'created_date' => $request->date_range
        ];
        $data['query'] = $this->hubUtilizationImportLogService->getImportLogs($search_data);
        return view("hubRecord/hub_record_import", $data);
    }

	public function exportToCsv(Request $request){
		$auth = auth()->user();
		$search_data = $request->all();
		$search_data['status'] = 'completed';
		$response = $this->hubUtilizationImportLogService->getImportLogs($search_data,'export');
       
		if(count($response) > 0 ){
			$filename = 'Hub_utilization' . date("m-d-Y");
			$headers = array(
				"Content-type" => "text/csv",
				"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
				"Pragma" => "no-cache",
				"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
				"Expires" => "0",
			);
		
				$columns = array('ID', 'Company','Created Date', 'Created By');
			
			
			$callback = function () use ($response, $columns,$auth) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($response as $list) { 
					
						fputcsv($file, array($list->id, $list->agency_name,date('m/d/Y h:i A', strtotime($list->created_date)), $list->users->first_name ? $list->users->first_name . ' ' . $list->users->last_name : ''));
				}
				fclose($file);
			};
			return response()->stream($callback, 200, $headers);
		}else{
			return null;
		}
    }

}