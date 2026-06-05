<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\Helpers\Common;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\OptInOutService;
use App\Services\PatientService;
use App\Model\TextMessages;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\TextMessageService;
use App\Helpers\Utility;
use Exception;
class OptInOutController extends BaseController
{

    protected $optInOutService = "", $patientService;
    protected $textMessageService = "";

    public function __construct(OptInOutService $optInOutService, PatientService $patientService,TextMessageService $textMessageService)
    {
        $this->optInOutService = $optInOutService;
        $this->patientService = $patientService;
        $this->textMessageService = $textMessageService;
        $this->middleware('auth', ['except' => ['test', 'callback']]);
    }

    public function index(Request $request)
    {

        return view("optInOut/index");
    }
    public function callback(Request $request)
    {
        //        echo $update = Common::sendTextSMSNYBest("9175895746","test"); 
        $postdate = request()->all();
        //echo json_encode($request->all());
        DB::table('sms_callback')->insert(
            ['link' => 'callabck', 'data' => json_encode($request->all()), 'datetime' => date("Y-m-d H:i:s")]
        );
        $from = str_replace("+1", "", $request->From);
        $to = str_replace("+1", "", $request->To);
        if ($to == "6092077517") {
            $patient = $this->patientService->getByPhoneNumber($from);
            // echo $from ;
            // print_r($patient);

            if ($patient) {
                $filename = "";
                try {
                    if(isset($request->NumMedia) && $request->NumMedia !=0){
                        if(isset($request->MediaUrl0) && $request->MediaUrl0 !=""){
                            $mediaUrl = $request->MediaUrl0;
                         
                            $response = Http::withBasicAuth(env('TWILLIO_USERNAME'), env('TWILLIO_PASSWORD'))
                                ->get($mediaUrl);
                                $mimeToExt = [
                                    'image/jpeg' => 'jpg',
                                    'image/png'  => 'png',
                                    'image/gif'  => 'gif',
                                    'image/webp' => 'webp',
                                    'image/heic' => 'heic',
                                    'audio/amr'  => 'amr',
                                    'audio/mpeg' => 'mp3',
                                    'audio/ogg'  => 'ogg',
                                    
                                    'video/mp4'  => 'mp4',
                                    'video/webm' => 'webm',
                                    'application/pdf'=> 'pdf',
                                    'application/msword' => 'doc',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                                    'application/vnd.ms-excel'                      => 'xls',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                                    'application/vnd.ms-powerpoint'                 => 'ppt',
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                                    'text/plain'=> 'txt',
                                    'text/csv' => 'csv',
                                ];

                            if ($response->successful()) {
                                $extension = $mimeToExt[$request->MediaContentType0] ?? 'bin';
                                $imageContent = $response->body();
                                $filename = 'twilio_media_' . uniqid().time() . '.'.$extension;
                                $path = public_path('twillio/' . $filename);
                                file_put_contents($path, $imageContent);
                                if(env('FILE_UPLOAD_PERMISSION') =='production'){
                                    Storage::disk('s3')->putFileAs('twillio', new \Illuminate\Http\File($path), $filename);
                                    unlink($path);
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // info($e->getMessage());
                }

                $data = array(
                    'mobile' => $from,
                    'case_id' => $patient->id,
                    'message' => $request->Body,
                    'message_type' => 'text',
                    'message_file'=>$filename
                );
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['created_by'] = 4117;
                $data['delete_flag'] = "N";
                $insert = new TextMessages($data);
                $insert->save();
                
                if($patient->type == 'Patient'){
                    $this->sendNotificationToUser($patient);
                }
                echo "insertd";
            }
        }
        echo "data2";
    }
    public function test(Request $request)
    {
        //        echo $update = Common::sendTextSMSNYBest("9175895746","test"); 
        $postdate = request()->all();
        //echo json_encode($request->all());
        $test = DB::table('sms_callback')->orderBy("datetime", "desc")->first();

        $obj = json_decode($test->data);
        print_r($obj->To);
        print_r($obj->From);
        $from = str_replace("+1", "", $obj->From);
        $patient = $this->patientService->getByPhoneNumber($from);
        print_r($patient);
        $data = array(
            'mobile' => $from,
            'case_id' => $patient->id,
            'message' => $$obj->Body,
            'message_type' => 'reply',

        );
        $data['created_date'] = date('Y-m-d H:i:s');
        // $data['created_by'] = $auth['id'];
        $data['delete_flag'] = "N";
        $insert = new TextMessages($data);
        $insert->save();
        echo "data";
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits_between:10,15',
            'opt_in_out' => 'required'

        ]);
        if ($validator->fails()) {

            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {

            $saveData = [
                'mobile' => $request->mobile,
                'optInOut' => $request->opt_in_out,

            ];

            $save = $this->optInOutService->save($saveData);
            if ($save) {
                return response()->json(['error_msg' => "Successfully added", 'status' => 1, 'data' => array()], 200);
            } else {
                return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
            }
        }
    }

    public function sendNotificationToUser($patient){
        try{
            if(isset($patient->agency_id) && !empty($patient->agency_id) ){
                $agencyNotifyData = array(
                    'agencyid' => $patient->agency_id,
                    'title' => 'New text message reply from '.$patient->full_name.', Kindly check.',
                    'record_id' => $patient->id,
                    'record_type' => 'Appointment',
                    'msg' => '',
                );
                Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                // info('New text message reply from '.$patient->full_name.', Kindly check.');
            }

            $lastReplyUserCheck = $this->textMessageService->getLastMessage($patient->id);
            $userData = [];
            if($lastReplyUserCheck && isset($lastReplyUserCheck->userDetails)){
                $userData[] = $lastReplyUserCheck->userDetails->id;
            }
            $this->notificationSend('Appointment', 'Text Message Reply', 'Patient '.trim($patient->full_name).' replied. Kindly review and follow up.', $patient->id, $patient->agency_id, 'Text Message', $userData, []);
        }catch(Exception $e){
            \Log::error('Notification Error: '.$e->getMessage());
        }
    }

    public function notificationSend($type, $title, $msg, $patientId, $agency_id, $userType, $userData, $serviceData = [])
	{
        $userData = Utility::getGroupUsersData($agency_id, $userType, $type, $userData, $serviceData);
		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agency_id ?? '',
			'record_id' => $patientId ?? '',
			'title' => $title,
			'msg' => $msg,
			'type' => $type,
		);
		Utility::insertNotificationsType($notificationData);
        // info($patientId);
	}
}
