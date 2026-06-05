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
use App\Services\PatientService;
use App\Services\PatientSMSLogService;
use App\Record;
use App\RecordNotes;
use App\SMS;
use Illuminate\Support\Facades\DB;
use App\Helpers\Common;
use App\Helpers\HHACaregiversHelper;

use App\Model\HHACaregivers;
use App\Model\HhaAppointment;
use App\Master;
use App\User;
use App\Agency;
use App\Helpers\AgencyWiseDomainHelper;
use App\Model\AgencyWiseDomain;
use Illuminate\Support\Facades\URL;
use Mail;

class TestController extends BaseController
{
    public function __construct(PatientService $PatientService, PatientSMSLogService $PatientSMSLogService)
    {
        $this->middleware('auth',['except' => ['ipchecker']]);
        $this->PatientService = $PatientService;
        $this->PatientSMSLogService = $PatientSMSLogService;
    }
    public function ipchecker()
    {
        //        $ipaddress = request()->getClientIp();
        // Function to get the client IP address

        $ipaddress = '';
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
      
        echo $ipaddress;
        die();
    }

    public function index()
    {

        $this->indexb();
        die();
        //echo Common::sendTextSMSNYBest( '7186503540', 'test' );
        //die();

        $smsList = DB::select('SELECT * FROM `covid_sms` where sent=0 limit 0,10');

        foreach ($smsList as $sms) {
            print_r($sms);
            $mobile = $sms->mobile;
            $message = 'We are happy to announce  that we now have vaccines available for qualified individuals. If you are 65 years and over, essential worker and chronically ill, please call today to make your appointment  718-972-3693 or email to covidcaccine@nybestmedical.com';

            $mobileNo = str_replace('(', '', $mobile);
            $mobileNo = str_replace(')', '', $mobileNo);
            $mobileNo = str_replace('-', '', $mobileNo);
            $mobileNo = str_replace(' ', '', $mobileNo);

            echo Common::sendTextSMSNYBest($mobileNo, $message);
            $update = array(
                'sent' => '1',
                'message' => $message
            );
            //  DB::table( 'campaignsmsdata' )->where( 'id', $visit->id )->update( $update );
            DB::table('covid_sms')->where('mobile', $mobile)->where('sent', '0')->update($update);
        }
        if (count($smsList) == 10) {
            echo '<script>window.location.reload()</script>';
        }

        # code...

    }

    function SendSMSBYPending($id)
    {
        $patient = $this->PatientService->getDetailById($id);
        $user = auth()->user();

        $agencyid = $patient->agency_id;
        $getAgencyName = Agency::getDetailsByAgencyId($agencyid);

        $agencyname = '';
        if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
            $agencyname = $getAgencyName->agency_name;
        }
        if ($patient->key != '') {
            $unitId = $patient->key;
        } else {
            $unitId = uniqid();
        }

        $url = URL::to('/') . '/ap/' . $unitId;
        $namearray = array();
        $serviceId = explode(',', $patient->service_id);
        if (!empty($serviceId[0])) {
            foreach ($serviceId as $vdl) {
                if ($vdl != '') {
                    $getMaster = Master::select('name')->where('id', $vdl)->where('del_flag', 'N')->first();
                    $namearray[] = $getMaster->name;
                }
            }
        }

        if (isset($patient->language) && strtolower($patient->language) == 'spanish') {
            $message = 'Aviso de ' . $agencyname . ': Usted tiene prevista una cita con el médico.  Your ' . implode(',', $namearray) . ' vencerán pronto. Haga clic en el enlace a continuación para programar su cita Enlace ' . $url . ' . No responda a este mensaje de texto, para cualquier pregunta, llame al (718) 972-3693';
        } else {
            // $message = 'Notice from ' . $agencyname . ': You are due for a Doctors appointment.  Your ' . implode(',',$namearray) . ' is expiring soon. Please click the link below to schedule your appointment Link ' . $url .' . Do not reply to this text message, for any questions please call (718) 972-3693';
            $message = 'Dear ' . $patient->first_name . ',  Notice from ' . $agencyname . ' : Your ' . implode(',', $namearray) . ' expiring soon and you will need to update it to continue employment and be active with ' . $agencyname . '. Please click the link below to schedule your appointment with NYBest Medical Care ' . $url . '. Do not reply to this text message, for any questions please call NYBest Medical @ 718-972-3693 or email appointment@nybestmedical.com ';
        }
        $mobile = str_replace('-', '', $patient->mobile);
        Common::sendTextSMSNYBest($mobile, $message);


        $this->PatientService->update(array('patient_sms_flag' => 1, 'key' => $unitId), array('id' => $id));
        $this->PatientSMSLogService->save(array('patient_id' => $id, 'mobile_no' => $patient->mobile, 'message' => $message));
        return 1;
    }
    public function indexb()
    {

        $test = Common::sendTwillioSms("+16812393693", "222222");

        die();

        HHACaregiversHelper::GetCaregiverComplianceItemDue(0);



        die();

        $caregiverIDs = HHACaregivers::where("id", "110413")->get();
        // print_r($caregiverIDs);
        echo "<pre>";

        foreach ($caregiverIDs as $caregiver) {
            $flagUpdate = HhaAppointment::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);

            foreach (array('95499', '95500', '80114', '80126', '80121', '80124', '83794', '') as $medicalIds) {
                // foreach (array('78569') as $medicalIds) {

                echo $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
       <soap:Body>
          <GetCaregiverComplianceItemDue xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
             <Authentication>
             <AppName>' . $agencyHHADetail->app_name . '</AppName>
             <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
             <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
             </Authentication>
             <SearchFilter>
                <CaregiverID>' . $caregiver->caregiver_id . '</CaregiverID>
                <OfficeID>' . $caregiver->officeId . '</OfficeID>
                
                <ComplianceItemType>OtherCompliance</ComplianceItemType>
                <ComplianceStatus>All</ComplianceStatus>
                <SequenceID>0</SequenceID>
             </SearchFilter>
          </GetCaregiverComplianceItemDue>
       </soap:Body>
    </soap:Envelope>
        ';
                /* $xml="<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                 <soap:Body>
                 <GetCaregiverComplianceItemDue xmlns='https://www.hhaexchange.com/apis/hhaws.integration'>
                 <Authentication><AppName>NYBestMedical</AppName><AppSecret>959cda02-5337-46b6-b74e-c4563199b6b4</AppSecret>
                 <AppKey>MQA2ADEANAAzADMALQBCAEEAOQBDAEQAQwAxAEMAMAA0ADcANQBBAEEAOQA2ADAAQQBDAEMARAA1AEYAMgA4AEYARAAyAEQANAA=</AppKey>
                 </Authentication>
                 <SearchFilter>
                 <OfficeID>204</OfficeID>
                 <CaregiverID>3283922</CaregiverID>
                 <MedicalID>78569</MedicalID><ComplianceItemType>Medical</ComplianceItemType><ComplianceStatus>All</ComplianceStatus><SequenceID>0</SequenceID></SearchFilter></GetCaregiverComplianceItemDue></soap:Body></soap:Envelope>";
         */
                $json = HHACaregiversHelper::getData($xml, 'GetCaregiverComplianceItemDue');



                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                print_r($xml);
            }
        }
        die();


        $agences = Agency::get();
        foreach ($agences as $agency) {



            $agencyDomain = AgencyWiseDomain::where('agency_id', $agency->id)->get();
            if (count($agencyDomain) == 0) {
                $users = User::where('agency_fk', $agency->id)->get();

                foreach ($users as $user) {
                    echo $mail = $user->email;
                    if ($mail != "" && count(explode('@', $mail)) > 1) {
                        echo $domain = explode('@', $mail)[1];
                        $domainexitst = AgencyWiseDomain::where('agency_id', $agency->id)->where('domain', $domain)->get();
                        if (count($domainexitst) == 0) {
                            AgencyWiseDomainHelper::save(array("domain" => $domain, "agency_id" => $agency->id));
                        }
                    }
                }
            }
        }



        die("sdfsdfsdf");
        $messages = "test";
        echo $email = "hiten@virtualheight.com";
        $subject = 'Esign Template david';
        $mail = Mail::send([], [], function ($message) use ($email, $subject, $messages) {
            $message->to($email, "Esign Template")->subject($subject)->html($messages);
        });
        //    print_r(Mail::set_include_path);
        if (!$mail)
            dd("something wrong");

        die();
        $cnt = 0;
        $data = DB::table('patient_master')->select("*")->where('patient_sms_flag', 0)->where('agency_id', 164)->get();
        foreach ($data as $val) {
            $this->SendSMSBYPending($val->id);
            $cnt++;
            if ($cnt == 100) {
                die();
            }
        }

        echo count($data);
        die();




        die();

        $data = DB::table('casddpap_transition_list_12_15_21_upload_file2')->select("*")->get();
        $cnt = 0;
        foreach ($data as $val) {
            $unitId = uniqid() . $cnt;


            // find all services id if not then create

            $servicesArray = array();
            $servicesIdsArray = array();
            echo "<pre>";
            print_r($val);
            if ($val->Drugscreenstatus == "Needed") {
                $servicesArray[] = "Drug Screen";
                $servicesIdsArray[] = 175;
            }

            if ($val->PhysicalStatus == "Needed") {
                $servicesArray[] = "Pre-employment Physical";
                $servicesIdsArray[] = 821;
            }
            if ($val->PPDStatus == "Needed") {
                $servicesArray[] = "PPD";
                $servicesIdsArray[] = 177;
            }
            if ($val->RubellaStatus == "Needed") {
                $servicesArray[] = "Rubella";
                $servicesIdsArray[] = 873;
            }
            $sers = implode(',', $servicesIdsArray);

            $url = URL::to('/') . '/ap/' . $unitId;
            $agencyname = "Preferred Homecare";
            echo $message = 'Notice from Preferred Homecare : Your ' . implode(',', $servicesArray) . ' expiring soon and you will need to update it to continue employment and be active with ' . $agencyname . '. Please click the link below to schedule your appointment with NYBest Medical Care ' . $url . '. Do not reply to this text message, for any questions please call NYBest Medical @ 718-972-3693 or email appointment@nybestmedical.com ';


            $final_array = array(
                'agency_id' => "164",
                'patient_code' => $val->AllcareHHAPAcode,
                'type' => "Caregiver",
                'first_name' => $val->PA1,

                'mobile' => $val->Phone,

                'language' => $val->Language,
                'service_id' => $sers,
                'sms' => $message,
                'remarks' => $val->StaffTrackID,
                'key' => $unitId,
                'appointment_mode' => 'Manual'
            );

            print_r($final_array);
            $insert = $this->PatientService->save($final_array);
            var_dump($insert);







            echo "<br/>";
            echo "<br/>";

            if ($cnt == 3) {

                // die();
            }



            //Create the sms 




            // Save the record 
            $cnt++;
        }




        die("test");
        $messages = "test";
        $email = "hiten@virtualheight.com";
        $subject = 'Esign Template';
        $mail = Mail::send([], [], function ($message) use ($email, $subject, $messages) {
            $message->to($email, "Esign Template")->subject($subject)->html($messages);
        });
        var_dump($mail);
        die();

        $smsList = SMS::where('created_at', '>', '2021-09-06')->where('type', 'Incoming')->get();

        foreach ($smsList as $sms) {

            $message = json_decode($sms->data);

            if (isset($message[0]->message->media)) {
                $media = $message[0]->message->media;

                $localImage = array();
                foreach ($message[0]->message->media as $key => $media) {
                    $localImage[] = $this->saveSMSImage($media);
                }
                print_r($localImage);

                $data = array(
                    'media' => json_encode($localImage),

                );

                $updateTable = SMS::where('id', $sms->id)->update($data);
            }
        }
    }

    private function saveSMSImage($url)
    {
        echo $url;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'authorization: Basic ' . env('BANDWIDTH_MESSAGING_AUTH'),
                'cache-control: no-cache',
                'content-type: application/json',

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        } else {
            $fileName = explode('/', $url);
            //			print_r( $fileName );
            $fileNamePublic = $fileName[count($fileName) - 1];
            $fileNamePublic = 'upload/files/' . $fileNamePublic;
            echo '<br/>';

            echo $fileNamePublic;
            echo '<br/>';

            file_put_contents(public_path($fileNamePublic), $response);
            // return $response;
            return 'https://web.exmedc.com/' . $fileNamePublic;
        }
    }

    public function formRecord()
    {
        return view('formupdate');
    }
    public function updateformRecord(Request $request)
    {

        $files = $path = $request->file('images');


        $name = time() . 'pay.' . $files->getClientOriginalExtension();
        $destination = public_path('uploads/newallupload');
        $files->move($destination, $name);
        $img = $name;
        $path = public_path() . '/uploads/newallupload/' . $img;
        $extension = explode('.', $img);
        $import_data = array_map('str_getcsv', file($path));
        $texts = $import_data;

        $cnt = 0;
        $finalarray = array();
        $temparray = array();
        foreach ($texts as $paystaub_tax) {
            if ($cnt != 0) {
                if ($paystaub_tax[0] != '') {
                    if (isset($paystaub_tax[1]) && $paystaub_tax[1] != '') {
                        $dates = date('Y-m-d', strtotime('+30 days', strtotime($paystaub_tax[1])));
                        $end_date = date('Y-m-d', strtotime('+15 days', strtotime($dates)));
                        Record::where('id', $paystaub_tax[0])->update(array('field_start_date' => $dates, 'field_end_date' => $end_date, 'file_date' => date('Y-m-d', strtotime($paystaub_tax[1]))));
                    }
                }
            }
            $cnt++;
        }
    }
}
