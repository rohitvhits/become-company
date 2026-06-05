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
use Illuminate\Support\Facades\Redirect;
use App\Services\SMSTemplateService;
use URL;
class SMSTemplateController extends BaseController
{
	
	public function __construct(SMSTemplateService  $smsTemplatecron){
		$this->smsTemplatecron = $smsTemplatecron;
	}
	
	public function index(Request $request){
		$user = auth()->user();
		$data['templete_list'] = $this->smsTemplatecron->AllSMSListing();
		return view('smsTemplate/sms_template_list',$data);
		
	}
	
	public function add(Request $request){
		
		return view('smsTemplate/sms_template_add');
		
	}
	
	public function save(Request $request){
		$validator = Validator::make($request->all(), [ 
            'name' => 'required',
			'message' => 'required'
        ]);
		if ($validator->fails()) {
                return redirect("sms-template/add")
                                ->withErrors($validator, 'template')
                                ->withInput();
            } else {
				$data =array(
					'name'=>$request->input('name'),
					'message'=>$request->input('message')
				
				);
				
			$insert= 	$this->smsTemplatecron->save($data);
			  if ($insert) {
                    Session::flash('success', 'SMS Template successfully inserted.');
                    return redirect('sms-template');
                } else {
                    Session::flash('error', 'Sorry, something went wrong. Please try again.');
                    return redirect('sms-template/add');
                }
		
			}
		
	}
	public function edit(Request $request,$id){
		$data['sms_template'] = $this->smsTemplatecron->getDetailById($id);
		return view('smsTemplate/sms_template_edit',$data);
		
	}
	public function update(Request $request){
		$validator = Validator::make($request->all(), [ 
            'name' => 'required',
			'message' => 'required'
        ]);
		if ($validator->fails()) {
                return redirect("sms-template/edit/".$request->input('id'))
                                ->withErrors($validator, 'template')
                                ->withInput();
            } else {
				$data =array(
					'name'=>$request->input('name'),
					'message'=>$request->input('message')
				
				);
				
				$insert = 	$this->smsTemplatecron->update($data,array('id'=>$request->input('id')));
			
                    Session::flash('success', 'SMS Template successfully updated.');
                    return redirect('sms-template');
                
		
			}
		
	}
	public function delete(Request $request,$id){
		
				$data =array(
					'deleted_flag'=>'Y', 
					
				
				);
				 
				$insert = 	$this->smsTemplatecron->SoftDelete($data,array('id'=>$id));
				if($insert){
                    Session::flash('success', 'SMS Template successfully deleted.');
                    return redirect('sms-template');
				}
                else {
                    Session::flash('error', 'Sorry, something went wrong. Please try again.');
                    return redirect('sms-template');
                }
		
			
		
	}
}