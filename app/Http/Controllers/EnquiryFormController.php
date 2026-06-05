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
use App\Services\EnquiryService;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Model\EnquiryReply;
use Mail;
class EnquiryFormController extends BaseController
{
    protected $enquiryService="";
    public function __construct(EnquiryService $enquiryService)
    {
        $this->middleware('permission:enquiry-list|enquiry-add|enquiry-delete|enquiry-view', ['only' => ['index', 'save']]);
        $this->middleware('permission:enquiry-list', ['only' => ['index']]);
        $this->middleware('permission:enquiry-add', ['only' => ['add', 'save']]);
       
        $this->middleware('permission:enquiry-delete', ['only' => ['delete']]);
        $this->middleware('permission:send-enquiry-reply', ['only' => ['enquiryReply']]);
        $this->middleware('permission:enquiry-change-status', ['only' => ['changeEnquiryStatus']]);
        $this->middleware('permission:enquiry-view-reply-log', ['only' => ['viewEnquiryReplyLog']]);
        $this->middleware('permission:enquiry-view-log', ['only' => ['delete']]);

        $this->middleware('auth');
        $this->enquiryService = $enquiryService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "Enquiry";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }

        return view("enquiry/list", $data);
    }

    public function ajaxList(Request $request)
    {
        $data['menu'] = "Enquiry";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }

        $data['query'] = $this->enquiryService->getList();
        return view("enquiry/ajax_list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add Enquiry";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        return view("enquiry/enquiry_add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [

            'email' => 'required|email',
            'mobile' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("/enquiry/create")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {

            $data = [
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'user_id'=>auth()->user()->id,
                'subject'=>$request->subject,
                'message'=>$request->message
            ];
            $ins_test = $this->enquiryService->save($data);
            if ($ins_test) {

                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add Enquiry',
                    'link' => url('/enquiry/save'),
                    'module' => 'Enquiry',
                    'object_id' => $ins_test,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added new Enquiry',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'Enquiry added successfully.');
                return redirect('/enquiry');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/enquiry/add');
            }
        }
    }

    public function enquiryReply(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [

            'subject' => 'required',
            'message' => 'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $getDetails = $this->enquiryService->getDetailsById($request->enquiry_id);

            $data = [
                'enquiry_id'=>$request->enquiry_id,
                'user_id'=>auth()->user()->id,
                'email'=>$getDetails->email,
                'message'=>$request->message,
                'subject'=>$request->subject,
                'created_by'=>auth()->user()->id,
            ];

            $saveDetail = new EnquiryReply($data);
           $insert_id= $saveDetail->save();
           
            $save = $insert_id;

            if($save){
                $emailData = array(
                    'username' => auth()->user()->first_name.' '.auth()->user()->last_name,
                    'insert' => $insert_id,
                    'subject'=>$request->subject,
                    'message' => $request->message,
                );

                $messages = Utility::getHtmlContent('email_template.enquiry_message', $emailData);
                $subject = $request->subject;
                $allemails[] = $getDetails->email;
                
                try {
                    $mail = Mail::mailer('second')->send([], [], function ($message) use ($allemails, $subject, $messages) {
                        $message->to($allemails, "EMC Rep")
                            ->subject($subject)->html($messages);
                    });    
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Reply Enquiry',
                    'link' => url('/enquiry-reply'),
                    'module' => 'Enquiry',
                    'object_id' => $insert_id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has reply for enquiry',
                    
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                return response()->json(['error_msg' => "Reply successfully send", 'status' => 0, 'data' => array()], 200);
            }
        }
    }

    function viewEnquiryReplyLog(Request $request){
        $query = EnquiryReply::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('enquiry_id',$request->id)->get();

        if(!empty($query[0])){
            foreach($query as $val){
                $val->messages = $val->message;
                $val->message = strlen($val->message) > 50 ? substr($val->message,0,50)."..." : $val->message;
                
            }
        }
        return response()->json(['error_msg' => "Reply successfully send", 'status' => 0, 'data' => $query], 200);
    }

    function changeEnquiryStatus(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'status' => 'required',            
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $oldresponse = $this->enquiryService->getDetailsById($request->enquiry_id);

            $data = [
                'status'=>$request->status
            ];

            $completedDate = null;
            $completedBy="";

            $rejectedDate = null;
            $rejectedBy="";
            if($request->status =="Completed"){
                $completedDate = date('Y-m-d H:i:s');
                $completedBy=$user->id;
            }

            if($request->status =="Rejected"){
                $rejectedDate = date('Y-m-d H:i:s');
                $rejectedBy=$user->id;
            }

            $data['completed_date'] = $completedDate;
            $data['completed_by']  = $completedBy;
            $data['rejected_date'] = $rejectedDate;
            $data['rejected_by']  = $rejectedBy;

            $update = $this->enquiryService->update($data,array('id'=>$request->enquiry_id));
            $getDetails = $this->enquiryService->getDetailsById($request->enquiry_id);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Change Status Enquiry',
                'link' => url('/change-enquiry-status'),
                'module' => 'Enquiry',
                'object_id' => $request->enquiry_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has been change status',
                'old_response'=>serialize($oldresponse->toArray()),
                'new_response'=>serialize($getDetails->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['error_msg' => "Status successfully changed", 'status' => 0, 'data' => array()], 200);

        }
    }
}
