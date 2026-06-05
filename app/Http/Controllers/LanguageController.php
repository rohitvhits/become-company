<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\LanguageService;
use App\Services\LogsService;
use App\Helpers\Utility;

class LanguageController extends Controller
{
    public function __construct(LanguageService $languageService)
    {
        $this->middleware('permission:language-list|language-add|language-edit|language-delete', ['only' => ['index','store']]);
        $this->middleware('permission:language-list', ['only' => ['index']]);
        $this->middleware('permission:language-delete', ['only' => ['destroy']]);

        $this->middleware('auth');
		$this->languageService = $languageService;
        
    }

    public function index(Request $request)
    {
        $data['menu'] = "user";
        $data['user']= $user= auth()->user();
        $name=$data['name'] = request('language_name');
        $data['query'] = $this->languageService->getData($name);
        return view("language/language_list", $data);
    }
   
    public function store(Request $request)
    {
       
        $user = auth()->user();
        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ],['name.required' => 'Please enter Name']);

        if($validator->fails()){
            return response()->json(['status'=>false, 'error'=>$validator->errors()->toArray()]);
        } else {
            $name = request('name');
            
            $data = array(
                'name' =>$name,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
            );

            $insert = $this->languageService->save($data);
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Language Add',
                'link' =>  url('language'),
                'module' => 'Language',
                'object_id' => $insert->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has added Language',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            if ($insert) {
                return response()->json(['status'=>true, 'msg'=>'Language added successfully','data'=>$insert]);
            } else {
                return response()->json(['status'=>false, 'msg'=>'Sorry, something went wrong. Please try again.']);
            }
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ],['name.required' => 'Please enter Name']);

        if($validator->fails()){
            return response()->json(['status'=>false, 'error'=>$validator->errors()->toArray()]);
        } else {
            $name = request('name');
            
            $data = array(
                'name' =>$name,
            );

            $update = $this->languageService->update($data,array('id'=>$id));
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Language Update',
                'link' =>  url('language'),
                'module' => 'Language',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated Language',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status'=>true, 'msg'=>'Language updated successfully','data'=>$data]);
        }
    }

    
    public function destroy($id)
    {
        $user = auth()->user();
       
        $data['id'] = $id;
        $update = $this->languageService->SoftDelete(array('deleted_by' => $user->id),array('id'=>$id));
        if ($update) {
            $totalCount = $this->languageService->totalRecord();
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Language Delete',
                'link' => url('language'),
                'module' => 'Language',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has delete Language',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status'=>true, 'msg'=>'Language delete successfully','data'=>$totalCount]);
        } else {
            return response()->json(['status'=>false, 'msg'=>'Sorry, something went wrong. Please try again.','data'=>$data]);
        }
    }
    public function getLogShowPage(Request $request)
    {
        $id = request('id');
		$data['user'] = $authId = auth()->user();
        if($authId->agency_fk !=""){
            return abort(404);
        }
		$data['logList'] = LogsService::getDatByAllLog($id,'Language');
        return view("user_log_ajax_list", $data);		
    }
    public function show($id)
    {
        $data['id'] = $id;
        $data['user'] = $authId = auth()->user();
        if($authId->agency_fk !=""){
            return abort(404);
        }
        return view("language/language_log_list",$data);
    }
    
   
     
   
}
