<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;
use App\Services\CMSService;
use App\Services\LogsService;
use Illuminate\Http\Request;
use App\Helpers\Utility;
class CMSController extends BaseController
{
    protected $cmsService = "";
    public function __construct(CMSService $cmsService)
    { 
        $this->middleware('auth', ['except' => ['viewPrivacyPolicy']]);
        $this->middleware('permission:cms-list|cms-update', ['only' => ['index']]);
        $this->middleware('permission:cms-update', ['only' => ['edit', 'update']]);
        $this->cmsService = $cmsService;
    }

    public function index(){
        $data['cms_list'] = $this->cmsService->getList();
        return view('cms.index',$data);
    }

    public function edit(Request $request){
        $data['details'] = $this->cmsService->getDetailsById($request->id);
   
        return view('cms.edit',$data);
    }

    public function update(Request $request){
       $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("/cms-edit?id=".$request->id)
            ->withErrors($validator, 'add_agency')
            ->withInput();
        }else{
            $oldResponse = $this->cmsService->getDetailsById($request->id);
            $data =[
                'message'=>$request->description,
               
            ];
        
            $update = $this->cmsService->update($data,array('id'=>$request->id));
            $newResponse = $this->cmsService->getDetailsById($request->id);
            $ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update CMS',
					'link' =>  url('cms-edit') . '?id=' . $request->id,
					'module' => 'CMS',
					'object_id' => $request->id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has update cms',
					'old_response' => serialize($oldResponse->toArray()),
                    'new_response' => serialize($newResponse->toArray()),

					'ip' => $ipaddress,
				];
				
            LogsService::save($insertLog);
           
            Session::flash('success', 'CMS successfully updated');
            return redirect('/cms');
        }
    }

    public function viewPrivacyPolicy(){
   
        $data['details'] = $this->cmsService->getDetailsById(1);
        return view('cms.privacy_policy',$data);
    }

    public function sendEmailNotification(Request $request){
        Utility::sendTermAndPrivacy($request->id);
        return response()->json(['error_msg'=>'Mail successfully send','data'=>array()],200);
    }
}