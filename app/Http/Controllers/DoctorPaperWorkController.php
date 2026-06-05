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
use Illuminate\Support\Facades\DB;

use App\Services\DoctorPaperWorkService;
use App\Services\DoctorPaperWorkDetailService;
use App\User;
class DoctorPaperWorkController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');
		
        
    }
	public function index(Request $request){
		$data['menu'] = "user";
        $data['user']=$user= auth()->user();
		
		$data['record_id'] = $record_id = $request->input('record_id');
		$data['name'] = $name = $request->input('name');
		$data['portal_id'] = $portal_id = $request->input('portal_id');
		$data['gender'] = $gender = $request->input('gender');
		$data['dob'] = $dob = $request->input('dob');
		$data['doctor_name'] = $doctor_name = $request->input('doctor_name');
		$data['doctor_no'] = $doctor_no = $request->input('doctor_no');
		$data['doctor_fax'] = $doctor_fax = $request->input('doctor_fax');
		$data['agency_name'] = $agency_name = $request->input('agency_name');
		$data['status'] = $status = $request->input('status');
       $data['doctor_paper_list'] = DoctorPaperWorkService::getDoctorPaperWorkList("",$record_id,$name,$portal_id,$gender,$dob,$doctor_name,$doctor_no,$doctor_fax,$agency_name,$status);
        return view("doctorPaperWork/doctor_paper_work", $data);
	}
	/*
	* Doctor paper work listing
	*/
	
	
	public function ajaxList(Request $request){
		$record_id = $request->input('record_id');
		$data['doctor_paper_list'] = DoctorPaperWorkService::getDoctorPaperWorkList($record_id);
		
		return view('doctorPaperWork/doctor_paper_work_ajax',$data);
	}
/*
	* Doctor paper work add form
	*/
    public function create()
    {
        $data['menu'] = "Add Doctor";
        $data['user']=$user= auth()->user();
        $data['emc_list'] = User::getEmcUserData();
        return view("doctorPaperWork/doctor_paper_work_add", $data);
    }
/*
	* Doctor paper work add save
	*/
    public function store(Request $request)
    {
		
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            

        ]);
        if ($validator->fails()) {
            return redirect("/doctor-paper-work/create")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
           
        
            $data = array(
                'name' =>$request->input('name'),
                'portal_id' =>$request->input('portal_id'),
                'gender' =>$request->input('gender'),
                'dob' =>date('Y-m-d',strtotime($request->input('dob'))),
                'doctor_name' =>$request->input('doctor_name'),
                'phone' =>$request->input('phone_no'),
                'fax' =>$request->input('doctor_fax'),
                'agency' =>$request->input('agency'),
                'rep' =>$request->input('rep_id'),
                'notes_rep' =>$request->input('notes_rep'),
                'medical_report' =>$request->input('medical_report'),
                'progress_notes' =>$request->input('progress_notes'),
                'date' =>date('Y-m-d',strtotime($request->input('date'))),
                'fax_date' =>date('Y-m-d',strtotime($request->input('fax_date'))),
                'record_id' =>$request->input('record_id'),
                'emc_user_id' =>$request->input('rep_id'),
                 
                
            );
 
            $insert = DoctorPaperWorkService::save( $data);
           // $insert = Agency::insertGetId($data);
            if ($insert) {
				$notes_name = $request->input('notes_name');
				if(!empty($notes_name[0])){
					foreach($notes_name as $va){
						
						DoctorPaperWorkDetailService::save(array('paper_work_id'=>$insert,'notes'=>$va));
					}
				}
				if($request->input('flag') !=''){
					Session::flash('success', 'Doctor Paper work successfully added.');
					return redirect('/doctor-paper-work'); 
				}else{
					 return 1;
				}
              
            } else {
                if($request->input('flag') !=''){
					Session::flash('error', 'Sorry, Something went wrong. Please try again.');
					return redirect('doctor-paper-work/create'); 
				}else{
					 return 0;
				}
              
            }
        }
    }

    public function edit(Request $request,$id)
    {
        $data['menu'] = "user";
        $data['user'] = auth()->user();
       
        $data['id'] = $id;
        $flag = $request->input('flag');
		$data['emc_list'] = User::getEmcUserData();
        $data['doctor_paper_list'] = DoctorPaperWorkService::getDoctorPaperDetailById($id);
        $data['doctor_paper_list_notes'] = DoctorPaperWorkDetailService::getDoctorPaperWorkDetailById($id);
		if($flag !=''){
			return view("doctorPaperWork/doctor_paper_work_edit_new", $data);
		}else{
			return view("doctorPaperWork/doctor_paper_work_edit", $data);
		}
       
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
		
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            
            

        ]);
        if ($validator->fails()) {
            return redirect("/doctor-paper-work/$id/edit?flag=123")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $data = array(
                'name' =>$request->input('name'),
                'portal_id' =>$request->input('portal_id'),
                'gender' =>$request->input('gender'),
                'dob' =>date('Y-m-d',strtotime($request->input('dob'))),
                'doctor_name' =>$request->input('doctor_name'),
                'phone' =>$request->input('phone_no'),
                'fax' =>$request->input('doctor_fax'),
                'agency' =>$request->input('agency'),
                'rep' =>$request->input('rep_id'),
                'notes_rep' =>$request->input('notes_rep'),
                'medical_report' =>$request->input('medical_report'),
                'progress_notes' =>$request->input('progress_notes'),
                'date' =>date('Y-m-d',strtotime($request->input('date'))),
                'fax_date' =>date('Y-m-d',strtotime($request->input('fax_date'))),
                'emc_user_id' =>$request->input('rep_id'),
                 
                
            );
			$update = DoctorPaperWorkService::update($data,array('id'=>$id));
			if(!empty($request->input('notes_name')[0])){
				DoctorPaperWorkDetailService::SoftDelete(array('del_flag'=>'Y'),array('paper_work_id'=>$id));
				foreach($request->input('notes_name') as $val){
					DoctorPaperWorkDetailService::save(array('paper_work_id'=>$id,'notes'=>$val));
				}
				
			}
            
			if($request->input('flag') =='123'){
				Session::flash('success', 'Doctor Paper work successfully update.');
                return redirect('/doctor-paper-work');
			}else{
				 
				return 1;
			}
			
           
        }
    }
    
    public function delete(Request $request,$id)
    {
        $user = auth()->user();
       
        $data['id'] = $id;
        $update = DoctorPaperWorkService::SoftDelete(array('del_flag' => 'Y', 'deleted_date' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id),array('id'=>$id));
        if ($update) {
			if($request->input('flag') !=''){
				Session::flash('success', 'Doctor Paper work successfully deleted.');
                return redirect('/doctor-paper-work');
			}else{
				 return 1;
			}
           
        } else {
             if($request->input('flag') !=''){
				Session::flash('error', 'Sorry, Something went wrong. Please try again.');
                return redirect('/doctor-paper-work');
			}else{
				 return 0;
			}
        }
    }
   
      public function exportCsv(Request $request){
           

           $user = auth()->user();

		$data['record_id'] = $record_id = $request->input('record_id');
		$data['name'] = $name = $request->input('name');
		$data['portal_id'] = $portal_id = $request->input('portal_id');
		$data['gender'] = $gender = $request->input('gender');
		$data['dob'] = $dob = $request->input('dob');
		$data['doctor_name'] = $doctor_name = $request->input('doctor_name');
		$data['doctor_no'] = $doctor_no = $request->input('doctor_no');
		$data['doctor_fax'] = $doctor_fax = $request->input('doctor_fax');
		$data['agency_name'] = $agency_name = $request->input('agency_name');
		$data['status'] = $status = $request->input('status');
       $doctor_paper_list = DoctorPaperWorkService::getDoctorPaperWorkListExport("",$record_id,$name,$portal_id,$gender,$dob,$doctor_name,$doctor_no,$doctor_fax,$agency_name,$status);
            $filename = 'DoctorPaperWork'. date("m-d-Y");
             $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ); 
        $columns = array('Id','Name','Portal Id','Gender','DOB','Doctors name','Doctors No','Doctors Fax','Agency','Rep','Status');

        $callback = function() use ($doctor_paper_list,$columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($doctor_paper_list as $list) {
				$date = '';
				if($list->dob !=''){
					$date = date('m/d/Y',strtotime($list->dob));
				}
            fputcsv($file, array($list->id,$list->name,$list->portal_id,$list->gender,$date,$list->doctor_name,$list->doctor_no,$list->fax,$list->agency_name,$list->first_name.' '.$list->last_name,$list->status));
            
            }
         
            fclose($file);
        };
            return response()->stream($callback, 200, $headers);
        
    }
	
	function EmcUserList(Request $request){
		$auth = auth()->user();
		$record_id ='';
		$data['doctor_paper_list'] = DoctorPaperWorkService::getDoctorPaperWorkListEMC($auth->id);
		return view('doctorPaperWork/doctor_paper_work_ajax_emc',$data);
		
	}
	function NotesUpdate(Request $request){
		$validator = Validator::make($request->all(), [
            'record_id' => 'required',
            'notes_name' => 'required',

        ]);
		 if ($validator->fails()) {
           return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 200);
        } else {
			$saveDetails = DoctorPaperWorkDetailService::save(array('paper_work_id'=>$request->input('record_id'),'notes'=>$request->input('notes_name')));
			if($saveDetails){
				return 1;
			}else{
				return 0;
			}
		}
		
		
	}
   
	function changeStatus(Request $request){
		$status = $request->input('status');
		$id = $request->input('id');
		$update = DoctorPaperWorkService::update(array('status'=>$status),array('id'=>$id));
		return 1;
		
	}
	
	function show($id){
		$doctor_paper_list = DoctorPaperWorkService::getDoctorPaperDetailById($id);
		$doctor_paper_list_notes = DoctorPaperWorkDetailService::getDoctorPaperWorkDetailById($id);
		
		if(!empty($doctor_paper_list_notes[0])){
			foreach($doctor_paper_list_notes as $val){
				$getUserDetails = User::getDetailsById($val->created_by);
				$fname = '';
				$lname = '';
				if($getUserDetails->first_name !=''){
					$fname = $getUserDetails->first_name;
				}
				if($getUserDetails->last_name !=''){
					$lname = $getUserDetails->last_name;
				}
				$val->full_name = $fname.' '.$lname;
				$val->created_date = date('m/d/Y h:i A',strtotime($val->created_date));
				$val->record_id = $doctor_paper_list->record_id;
			
			}
			return response()->json(['error_msg' => "", 'status' => 1, 'data' =>$doctor_paper_list_notes], 200);
		}else{
		return response()->json(['error_msg' => "", 'status' => 0, 'data' =>array()], 200												);	
		}
	
		return json_encode($final_Array);
		
	}
}
