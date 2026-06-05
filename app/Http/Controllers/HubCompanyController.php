<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\HubCompanyService;
use App\Model\HubCompany;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Helpers\HubGenerateAgencyTokenHelper;
use App\Model\HubGenerateAgencyToken;
class HubCompanyController extends BaseController
{
    protected $hubCompanyService = '';
    public function __construct(HubCompanyService $hubCompanyService)
    {
        $this->middleware('permission:hub-company-list|hub-company-add|hub-company-edit|hub-company-delete|hub-company-view', ['only' => ['index', 'save', 'view']]);
        $this->middleware('permission:hub-company-list', ['only' => ['index']]);
        $this->middleware('permission:hub-company-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:hub-company-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:hub-company-delete', ['only' => ['delete']]);
        $this->middleware('permission:hub-company-view', ['only' => ['view']]);
        $this->middleware('auth');
        $this->hubCompanyService = $hubCompanyService;
    }

    public function index(Request $request)
    {

        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }

        if ($user['agency_fk'] != "") {
            return abort(404);
        }

        $agency_name = $data['agency_name'] = $request->agency_name;
        $email = $data['email'] = $request->input('email');
        $phone = $data['phone'] = $request->input('phone');
        $city = $data['city'] = $request->input('city');
        $data['is_sms'] = $request->is_sms;

        $data['query'] = $this->hubCompanyService->getData($agency_name, $email, $phone, $city, $request->is_sms);

        return view("hubCompany/list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        return view("hubCompany/add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required',
            'email' => 'required|email|unique:agency,email',
            'phone' => 'required|numeric|digits_between:10,15',
            'address1' => 'required',
            'address2' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
        ],[
            'agency_name.required' => "Please enter Company Name.",
        ]);
        if ($validator->fails()) {
            return redirect("/hub-company/add")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $agency_name = $request->input('agency_name');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $address1 = $request->input('address1');
            $address2 = $request->input('address2');
            $state = $request->input('state');
            $city = $request->input('city');
            $zip_code = $request->input('zip_code');
            $county = $request->input('county');
            $other_email = $request->input('other_email');

            $data = array(
                'agency_name' => $agency_name,
                'email' => $email,
                'phone' => $phone,
                'address1' => $address1,
                'address2' => $address2,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zip_code,
                'county' => $county,
                'other_email' => $other_email,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                'app_name' => $request->input('app_name'),
                'app_key' => $request->input('app_key'),
                'app_token' => $request->input('app_token'),
                'notification_email' => $request->input('notification_email'),
                'service_md_appointment' => 1,
                'client_name' => $request->client_name,
                'document_email_notification' => $request->document_email_notification,
                'efax_no' => $request->efax_no,
                'show_hub'=>'1'
            );

            $ins_test = new HubCompany($data);
            $ins_test->save();
            $insert = $ins_test->id;

            if ($insert) {
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/hub-company/save'),
                    'module' => 'Hub Company',
                    'object_id' => $ins_test->id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Company',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'Hub Company successfully inserted');
                return redirect('/hub-company');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/hub-company');
            }
        }
    }

    public function edit($id)
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $data['agency'] = HubCompany::where("id", $id)->first();
        return view('hubCompany/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required',
            'email' => 'required',
        ],[
            'agency_name.required' => "Please enter Company Name.",
        ]);
        if ($validator->fails()) {
            return redirect("/hub-company/edit/$id")
                ->withErrors($validator, 'edit_agency')
                ->withInput();
        } else {
            $agency_name = $request->input('agency_name');
            $email = $request->input('email');
            $phone = str_replace('-', '', $request->input('phone'));
            $address1 = $request->input('address1');
            $address2 = $request->input('address2');
            $state = $request->input('state');
            $city = $request->input('city');
            $zip_code = $request->input('zip_code');
            $county = $request->input('county');
            $other_email = $request->input('other_email');
            $data = array(
                'agency_name' => $agency_name,
                'email' => $email,
                'phone' => $phone,
                'address1' => $address1,
                'address2' => $address2,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zip_code,
                'county' => $county,
                'other_email' => $other_email,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user->id,
                'app_name' => $request->input('app_name'),
                'app_key' => $request->input('app_key'),
                'app_token' => $request->input('app_token'),
                'notification_email' => $request->input('notification_email'),
                'client_name' => $request->client_name,
                'document_email_notification' => $request->document_email_notification,
                'efax_no' => $request->efax_no,
            );
            $data['nybest_email_notification'] = $request->input('nybest_email_notification');
            if ($request->input('nybest_email_notification') != '') {
            }
            $data['notes_email_notification'] = $request->input('notes_email_notification');
            $update = HubCompany::where('id', $id)->update($data);

            if ($update) {
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update',
                    'link' => url('/agency/update'),
                    'module' => 'Agency',
                    'object_id' => $id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has updated Agency',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }
            Session::flash('success', 'Hub Company successfully updated');
            return redirect('/hub-company-view/' . $id);
        }
    }
    public function view(Request $request, $id)
    {
        $data['menu'] = "Agency";
        $data['title'] = "Agency View";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['agency_id'] = $id;
        $data['id'] = $id;
        $data['agencyDetails'] = HubCompany::where('delete_flag', 'N')->where('id', $id)->first();
        if ($data['agencyDetails'] != '') {
            $data['agency_fk'] = $request->input('agency_fk');
            $data['flag'] = $flag = $request->input('flag');

            if ($flag == '') {
                $data['name'] = $request->input('name');
                $data['select_item'] = $request->input('item');
                $data['start_date'] = $request->input('start_date');
                $data['end_date'] = $request->input('end_date');
            }
            if ($flag != '') {
                $data['daterange'] = $request->input('daterange');

                $data['status'] = $request->input('status');
            }

            $data['agencyList'] = HubCompany::where('delete_flag', 'N')->get();
            return view('hubCompany/view', $data);
        } else {
            return redirect('/hub-company');
        }
    }

    function hubGenerateToken(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
         
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {

            $token = $this->random_string(50);
            // $getMasterDetails = Master::getDetailsById($request->notes);
            $finalArray = [
                'agency_id' => $request->agency_id,
                'token' => $token,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                // 'notes' => $getMasterDetails->name,
                'ip_block' => $request->ip_block,
                // 'notes_id' => $request->notes,
            ];


            $ins_test = HubGenerateAgencyTokenHelper::insert($finalArray);
            $message = 'Token successfully generated.';

            if ($ins_test) {
                
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => "Hub Generate Token",
                    'link' => url('/hub-company/generate-token'),
                    'module' => 'Hub Company',
                    'object_id' => $request->agency_id,
                    'message' => $user->first_name . ' ' . $user->last_name . " has generate token",
                    'new_response' => serialize($finalArray),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                return response()->json(['error_msg' => $message, 'status' => 1, 'data' => array()], 200);
              
            } else {
                return response()->json(['error_msg' =>"Sorry, something went wrong. Please try again", 'status' => 1, 'data' => array()], 500);
            }
        }
    }

    function random_string($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    function hubGenerateTokenList(Request $request){
        $data['page'] = $request->page;
        $data['token_list'] = HubGenerateAgencyToken::getAllGenerateToken($request->agency_id);
        return view('hubCompany.hub_company_token_list', $data);
    }
}