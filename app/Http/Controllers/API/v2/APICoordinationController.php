<?php

namespace App\Http\Controllers\API\v2;

use App\Agency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Validator;
use URL;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Utility;
use Exception;
use App\Model\LeadApiTraceLog;
use App\Services\LeadApiService;
use Carbon\Carbon;
use App\Services\AppTokenService;

class APICoordinationController extends BaseController
{
    public $successStatus = 200;
    protected $leadApiService;
    protected $appTokenService;

    public function __construct(
        LeadApiService $leadApiService,
        AppTokenService $appTokenService
    ) {
        $this->leadApiService = $leadApiService;
        $this->appTokenService = $appTokenService;
    }

    public function app_tarce($apiKey)
    {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $ipaddress = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['QUERY_STRING'])) {
            $page = $_SERVER['QUERY_STRING'];
        } else {
            $page = "";
        }

        if (!empty($_POST)) {
            $user_post_data = $_POST;
        } else {
            $user_post_data = $_GET;
        }

        $user_post_data = json_encode($user_post_data);
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $remotehost = @getHostByAddr($ipaddress);

        $user_info = json_encode([
            "Ip"         => $ipaddress,
            "Page"       => $page,
            "UserAgent"  => $useragent,
            "RemoteHost" => $remotehost
        ]);

        $urlPath = parse_url($actual_link, PHP_URL_PATH);
        $endpoint = basename($urlPath);
        $type = ucwords(str_replace('-', ' ', $endpoint));

        $user_track_data = [
            "url"          => $actual_link,
            "type"         => $type,
            "api_key"      => $apiKey,
            "ip"           => $ipaddress,
            "response"     => $user_info,
            "created_date" => date('Y-m-d H:i:s'),
            "data"         => $user_post_data,
            'created_by'   => env('API_LEAD')
        ];

        $saveLog = new LeadApiTraceLog($user_track_data);
        $saveLog->save();
    }

    public function saveRecord(Request $request)
    {
        $header = $request->header('authorization');
        $checkToken = $this->appTokenService->getDetailTokenWise($header);

        if (empty($checkToken)) {
            return response()->json([
                'error_msg' => "Invalid token.",
                'status'    => 0,
                'data'      => []
            ], 404);
        }

        self::app_tarce($header);

        $validator = Validator::make($request->all(), [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'phone'       => 'required',
            'service_requested'  => 'required',
            'agency_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status'    => 0,
                'data'      => []
            ], 422);
        }

        $data = [
            'first_name'            => $request->first_name,
            'last_name'             => $request->last_name,
            'email'                 => $request->email,
            'agency_name'           => $request->agency_name,
            'phone'                 => preg_replace('/[()\-\s]/', '', $request->phone),
            'service_requested'     => $request->service_requested,
            'appointment_date'      => Carbon::createFromFormat('m/d/Y', $request->appointment_date)
                                            ->format('Y-m-d'),
            'appointment_time'      => Utility::convertTime($request->appointment_time),
            'appointment_address'   => $request->appointment_address,
            'created_by'            => env('API_LEAD'),
            'response'              => serialize($request->all()),
            'app_token_id'          => $checkToken->id,
            'app_referral_id'       => $checkToken->referral_type
        ];

        $save = $this->leadApiService->save($data);

        if ($save) {
            return response()->json([
                'error_msg' => "Record successfully added",
                'status'    => 1,
                'data'      => [
                    [
                        'appoinment_id' => $save->id
                    ]
                ]
            ], 200);
        }

        return response()->json([
            'error_msg' => 'Sorry, something went wrong. Please try again.',
            'status'    => 0,
            'data'      => []
        ], 500);
    }
}
