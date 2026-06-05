<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notification;
use App\Notifications\MyFirstNotification;
use App\Services\RemainderService;
use App\Services\TaskAssignLogService;
use App\User;
use URL;
use Mail;
class RemainderController extends BaseController
{
	function __construct(RemainderService $RemainderService,TaskAssignLogService $TaskAssignLogService){
		$this->middleware('auth');
		$this->RemainderService = $RemainderService;
		$this->TaskAssignLogService = $TaskAssignLogService;
	}
	public function index(){
		$data['auth'] = auth()->user();
		$user_list = $this->RemainderService->getData();
		if(count($user_list) >0){
			foreach($user_list as $ls){
				$employeeName ='';
				if($ls->employee_id !=''){
					$userDetails = User::GetConcateData($ls->employee_id);
					
					$employeeName = isset($userDetails->employee_name)?$userDetails->employee_name:"";
				}
				$ls->employeeName =$employeeName;
			}
		}
		$data['user_list'] = $user_list;
		return view('remainder.remainder_list',$data);
	}
	public function add(){
		
		$data['auth'] = auth()->user(); 
		
		$data['users'] = User::getSearchUsersNew(); 
		
		return view('remainder.remainder_add',$data);
	}
	function save(Request $request){
		
		$auth =auth()->user();
		$validator = Validator::make($request->all(), [
				'title' => 'required',
				'description' => 'required',
				'start_date' => 'required',
				'end_date' => 'required',
				'start_time' => 'required',
				
            ]);
            if ($validator->fails()) {
                return redirect("reminder/add")
                                ->withErrors($validator, 'agency')
                                ->withInput();
            } else {
				$employeeId = 22;
				if($request->input('assign_to')[0] !='' ){
					$employeeId = implode(',',$request->input('assign_to'));
				}
				$update = $this->RemainderService->save(array('user_id'=>$auth['id'],'title'=>$request->input('title'),'message'=>$request->input('description'),'employee_id'=>$employeeId,'start_date'=>date('Y-m-d',strtotime($request->input('start_date'))),'end_date'=>date('Y-m-d',strtotime($request->input('end_date'))),'start_time'=>date('H:i:s',strtotime($request->input('start_time')))));
				
				if($update){
					Session::flash('success','Remainder successfully added');
					return redirect('/reminder');
				}else{
					Session::flash('error',"Sorry, something went wrong. Please try again.");
					return redirect('/reminder/add');
				}
				
			}
		
	}
	function changeStatus(Request $request){
		$data['user']=  $auth = auth()->user();
		$id= $request->input('id');
		$status= $request->input('status');
		$update =$this->RemainderService->update(array('status'=>$status,'common_date'=>date('Y-m-d H:i:s')),array('id'=>$id));
         $this->TaskAssignLogService->save(array('task_id'=>$id,'user_id'=>$auth['id'],'status'=>$status));
		
		if($update){
			return 1;
		}else{
			return 0;
		}
		
	}
	public function edit($id){
		$data['auth'] = auth()->user();
		$data['users'] = User::getSearchUsersNew(); 
		$data['query'] = $this->RemainderService->getDetailsById($id);
		return view('remainder.remainder_edit',$data);
	}
	function update(Request $request){
		
		$auth =auth()->user();
		$validator = Validator::make($request->all(), [
				'title' => 'required',
				'description' => 'required',
				'start_date' => 'required',
				'end_date' => 'required',
				'start_time' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect("reminder/edit/".$request->input('id'))
                                ->withErrors($validator, 'agency')
                                ->withInput();
            } else {
				$employeeId = 22;
				if($request->input('assign_to')[0] !='' ){
					$employeeId = implode(',',$request->input('assign_to'));
				}
				$update = $this->RemainderService->update(array('user_id'=>$auth['id'],'title'=>$request->input('title'),'message'=>$request->input('description'),'employee_id'=>$employeeId,'start_date'=>date('Y-m-d',strtotime($request->input('start_date'))),'end_date'=>date('Y-m-d',strtotime($request->input('end_date'))),'start_time'=>date('H:i:s',strtotime($request->input('start_time')))),array('id'=>$request->input('id')));
				
				Session::flash('success','Remainder successfully updated');
					return redirect('/reminder');
			}
		
	}
	
	function delete($id){
		$update = $this->RemainderService->SoftDelete(array('del_flag'=>"Y"),array('id'=>$id));
		if($update){
			Session::flash('success','Remainder successfully deleted');
			
		}else{
			Session::flash('error',"Sorry, something went wrong. Please try again.");
			
		}	
		return redirect('/reminder');
	}
}

?>