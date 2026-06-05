<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\EFaxLogService;

class EfaxReportController extends BaseController
{
	protected $efexLogService ='';

	public function __construct(EFaxLogService $efexLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:efax-report-list', ['only' => ['index', 'efexAjaxList','exportCsv']]);
		$this->efexLogService = $efexLogService;
	
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();		
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        return view('efexReport/efex_report_list', $data);        
    }

	public function efexAjaxList(Request $request){
		$data['page']  = $request->page;
		$query = $this->efexLogService->list($request->all());
		if(count($query) >0){
			foreach($query as $val){
				$val->response = unserialize($val->return_response);
			}
		}

		$data['query'] = $query;
		return view('efexReport/efex_ajax_list', $data);  
    }

    public function exportCsv(Request $request){
        $query = $this->efexLogService->list($request->all(),'export');
		if(count($query) >0){
			foreach($query as $val){
				$response = unserialize($val->return_response);
				
				$responses="";
				if(isset($response['errors'])){

				}else{
					
					$final = '';
					foreach($response as $key=>$va){
						foreach($va as $k=>$value){
							$final .=ucfirst(str_replace('_',' ',$k)).' : '.$value.' , ';
						}
					}
					$responses = $final;	
				}

				$val->response = $responses;
			}
		}

		$data['query'] = $query;

		$filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		$columns = array( 'Portal Id', 'Name', 'Type', 'Document Name', 'Fax No', 'Response', 'Created Date', 'Created By');

		$newass  = array();
		$callback = function () use ($query, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($query as $list) {

				$data = array($list->patient_id, $list->first_name.' '.$list->last_name, $list->type, $list->document_name, $list->fax_no,$list->response, Utility::convertMDYTime($list->created_date), $list->uFirstName.' '.$list->uLastName);
				fputcsv($file, $data);
			}
			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
    }
}