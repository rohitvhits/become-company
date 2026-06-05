<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\AgencyNotificationEmailHelper;
use App\Master;

class NotificationSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notification-setting-list|notification-setting-add|notification-setting-edit|notification-setting-delete|notification-setting-view', ['only' => ['index', 'agencyWiseNotification']]);
        $this->middleware('permission:notification-setting-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:notification-setting-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:notification-setting-delete', ['only' => ['delete']]);
        $this->middleware('permission:notification-setting-view', ['only' => ['view']]);
    }
    public function index(Request $request)
    {
        $data['page'] = $request->input('page');
        $data['agencyWiseNotificationEmail'] =  Master::getAllDataByMasterTypeFk(array(24));
        return view("notification-setting/index", $data);
    }

    function agencyWiseNotification(Request $request)
    {
        // dd($request);
        $data['page'] = $request->input('page');
        $query = AgencyNotificationEmailHelper::notificationEmailByAgencyId($request->input('agency_id'));
        foreach ($query as $val) {
            $service_name = "";
            if ($val->service_id != "") {
                $explode = explode(',', $val->service_id);

                $getDetails = Master::geServiceName($val->service_id);
                $finals = [];
                foreach ($getDetails->toArray() as $names) {
                    $finals[] = $names['name'];
                }

                $service_name = implode(',', $finals);
            }

            $val->service_name = $service_name;
        }
        $data['query'] = $query;
        return view("agency/notification_email_ajax_list", $data);
    }

}