<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\PatientV2Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\BulkSMSCdpapCaregiverService;
use App\Model\BulkSMSCdpapCaregiverDetail;
use App\Services\LogsService;
use App\Services\BulkSMSCdpapCaregiverDetailService;
use App\Helpers\Utility;
use App\Model\BulkViewAppointment;
use Mail;
class BulkViewAppointmentController extends BaseController
{
   
    protected const VALIDATION_CODE=422;
    protected const ERROR_CODE = 500;
    protected const SUCCESS_CODE = 200;

    public function viewAnAppointment(Request $request){
        return view('bulkViewAppointment.without_login_appointment');
    }

    public function saveAppointment(Request $request){

        $validator = Validator::make($request->all(), [
			'full_name' => 'required',
            'phone' => 'required',
            'agency_name' => 'required',
            'service_name' => 'required',
            'county' => 'required',
            'book_date' => 'required',
           

		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
            $data = [
                'full_name'=>$request->full_name,
                'phone'=>preg_replace('/\D+/', '',$request->phone),
                'email'=>$request->email,
                'agency_name'=>$request->agency_name,
                'service_name'=>$request->service_name,
                'county'=>$request->county,
                'book_date'=>date('Y-m-d',strtotime($request->book_date)),
                'created_date'=>date('Y-m-d H:i:s'),
                'data'=>serialize($request->all()),
                'ip_address'=>Utility::getIP()
            ];

            $appointment = BulkViewAppointment::create($data);
          
            if($appointment && $appointment->id){
                
                try {

                    $emailData = $request->all();
                $messages = Utility::getHtmlContent('email_template.bulk_sms',$emailData);

                $subject ="New booking enquiry from SMS";
                $mailArray = ['developer@nybestmedical.com','jromero@nybestmedical.com'];
					 Mail::mailer('second')->send([], [], function ($message) use ($mailArray, $subject, $messages) {
						$message->to($mailArray, "EMC Rep")
							->subject($subject)->html($messages);
						
						$message->bcc('Pinak@nybestmedical.com', "Pinak");
					});
				} catch (\Throwable $th) {
					//throw $th;
				}

                return response()->json(['error_msg' => 'Success', 'data' => []], self::SUCCESS_CODE);
            }
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again', 'data' => []], self::ERROR_CODE);
        }
       
    }

    public function thankYou(){
        return view('bulkViewAppointment.bulk_thank_you');
    }
}