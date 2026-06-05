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
//use App\Record;
use App\RecordNotes;
use App\SMS;

use App\Master;
use App\User;
use App\Agency;
use App\Services\PatientService;
//use App\Services\AssignEMCRecordService;
class DashboardController extends BaseController
{
    public function __construct(PatientService $PatientService)
    {
        $this->middleware('auth'); 
		$this->PatientService =$PatientService;
	//	$this->AssignEMCRecordService =$AssignEMCRecordService;
    } 
    public function index(Request $request)
    {
	
        $data['menu'] = "dashboard";
        $data['user'] = auth()->user();	
        $data['exp'] = request('exp');		

     
            $agency_fk=$data['user']['login_type_fk'];
            $data['agencyList'] = $getAgencyName = Agency::getDetailsByAgencyId($agency_fk);
             $agency_fk=$data['user']['agency_fk'];
            $getAgencyName = Agency::where('id',$agency_fk)->first();
             
       
			if($data['user']['login_type_fk'] ==183){
			
				// $data['open_record_list'] = $this->PatientService->AllPatientList();
				 $data['upcomming_record_list'] = $this->PatientService->AllUpcommningPatientList();
				// return view("hospital_dashboard",$data);
               
                return redirect('dashboard/calendar-hospital-v2');
               
                  
                //  return redirect('dashboard/calendar-hospital');


			}else{
			
                return redirect('appointment');
           $data['record_complete_list'] =array();// Record::getAgencyWiseCompleteRecord();
           $data['NotesList'] =array();// RecordNotes::last50NotesByAgency();
           return view("new_dashboard", $data);
			}
       
			
	
	}
    public function doctorSMSDashboard()
    {
    
    
        $data['menu'] = "dashboard";
        $data['user'] = auth()->user();     
        $data['exp'] = request('exp');		

            if($data['user']['login_type_fk'] ==183 || $data['user']['login_type_fk'] ==2){
            
                $data['open_record_list'] =array(); $this->PatientService->AllPatientList();
                $data['upcomming_record_list'] = $this->PatientService->AllUpcommningPatientList();
                return view("hospital_dashboard",$data);
            }else{
            
                $data['record_complete_list'] = array(); //Record::getAgencyWiseCompleteRecord();
                $data['NotesList'] =array();// RecordNotes::last50NotesByAgency(); 
                return view("new_dashboard", $data);
            }
       
           // $data['record_complete_list'] = Record::getAgencyWiseCompleteRecord();
           // $data['NotesList'] = RecordNotes::last50NotesByAgency();
           // return view("new_dashboard", $data);
            
        }
    
    function AssignEMCExportCsv(Request $request){
		$status_id = $request->input('status_id');
		$assignUserList = $this->AssignEMCRecordService->getListEMCBYUserID($status_id);
		 $filename = 'AssignEMCUser'. date("m-d-Y");
             $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Record Id','Record Name','Agency Name','Status','Progress Notes');

        $callback = function() use ($assignUserList,$columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($assignUserList as $list) {
            fputcsv($file, array($list->record_id,$list->first_name.' '.$list->last_name,$list->agency_name,$list->status,$list->progress_notes));
            
            }
         
            fclose($file);
        };
            return response()->stream($callback, 200, $headers);

		
	}
	function getTelehealth(Request $request){
		 $data['agency_list'] = Agency::getAllAgencyList(); 
		$data['agency_id']=$agency_id = $request->input('agency_id');
		$data['type_id']=  $type_id = $request->input('type_id');
		$data['fullname_id']= $fullname_id = $request->input('fullname_id');
		$data['record_id']= $record_id = $request->input('record_id');
		
		$data['upcomming_telehealth'] = PatientService::getAllUpcommingTelehealth($agency_id,$type_id,$fullname_id,$record_id);
		return view("ajax_response",$data);
		
	}

    function agencyWiseRecordList(Request $request){
        $created_date = $request->input('created_date');
        $fields = $request->input('fields');
        $orderBy = $request->input('orderBy');
        $data['query'] = array();//Record::getAgencyWiseRecordCount($created_date,$fields,$orderBy);
       return view('dashboard.agency_ajax_resp',$data);
    }
}
