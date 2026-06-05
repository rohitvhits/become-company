<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Model\LoginLog;
use Illuminate\Http\Request;
use App\Services\LoginLogService;
use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Controller as BaseController;

class LoginLogController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data['menu'] = "login Log";
        $data['user'] = $user = auth()->user();
        $data['id'] =request('id');
        if ($user['user_type_fk'] != 3) {
            return abort(404);
        }

        return view("LoginLog/index", $data);
    }

    public function loginLogList(Request $request)
    {

        $user = auth()->user();
        if ($user['user_type_fk'] != 3) {
            return abort(404);
        }
        $data['user'] = auth()->user();
        $data['userName'] = $username = request('username');
        $data['ip'] = $ip = request('ip');
        $data['country'] = $country = request('country');
        $data['countryCode'] = $countryCode = request('countrycode');
        $data['loginStatus'] = $loginStatus = request('loginstatus');

        $data['field'] = $field = request('field');
        $data['sort'] = $sort = request('sort');
        $data['createdAt'] = $createdAt = request('createdat');
        $data['userid'] = $userId = request('userid');
        $data['logList'] = LoginLogService::getData($username, $ip, $country, $countryCode, $loginStatus, $createdAt, $field, $sort,$userId,'');

        return view("LoginLog/ajax-list", $data);
    }
    public function loginLogExport(Request $request)
    {


        $user = auth()->user();

        $username = request('username');
        $ip = request('ip');
        $country = request('country');
        $countryCode = request('countrycode');
        $loginStatus = request('loginstatus');
        $field = request('field');
        $sort = request('sort');
        $createdAt = request('createdat');
        $userId = request('userid');
        $loginLogs = LoginLogService::getData($username, $ip, $country, $countryCode, $loginStatus, $createdAt, $field, $sort,$userId,'export-data');



        $filename = 'Login Logs' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('User Name', 'Ip Address', 'Country', 'Country Code', 'Login status', 'Created Date');

        $callback = function () use ($loginLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($loginLogs as $list) {

                fputcsv($file, array(ucfirst($list->username), $list->ipaddress, $list->country, $list->country_code, ucfirst($list->login_status), date('m/d/Y h:i A', strtotime($list->created_at))));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
