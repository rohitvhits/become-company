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

use App\Agency;
use App\Rates;
use App\User;

use App\Record;
use App\RecordNotes;
use App\Master;
use App\Invoice;
use Excel;
use App\Helpers\UserHelper;
use App\Services\LogsService;
use App\Helpers\Utility;

class DocumentTypeMasterController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        // if ($user['user_type_fk'] != 5) {
        //     abort(404);
        // }
        $data['query'] = master::where('user_id', $user->agency_fk)->where('del_flag', 'N')->paginate(10);

        return view("documentItem/document_type_list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add user";
        $data['user'] = $user = auth()->user();
       
        return view("documentItem/document_type_add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect("/document-item/document-add")
                ->withErrors($validator, 'add_user')
                ->withInput();
        } else {
            if (in_array($user->user_type_fk, array(3, 4))) {
                $userIDs = request('id');
            } else {
                $userIDs = $user->agency_fk;
            }
            $masterArray = array(
                'user_id' => $userIDs,
                'name' => request('name'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                'master_type_fk' => 9
            );
            $insert = new Master($masterArray);
            $insert->save();
            $insertId = $insert->id;
            $flag = request('flag');
            if ($insertId) {
                Session::flash('success', 'Document type added successfully.');
                if ($flag == 'No') {
                    return redirect('document-item');
                } else {
                    return redirect()->back();
                }
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                if ($flag == 'No') {
                    return redirect('document-item');
                } else {
                    return redirect()->back();
                }
            }
        }
    }

    public function edit($id)
    {

        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 5) {
            abort(404);
        }
        $data['id'] = $id;
        $data['edit_details'] = master::where("id", $id)->where('del_flag', 'N')->first();
        return view('documentItem/document_type_edit', $data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data['id'] = request('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect("/document-item/document-edit/$id")
                ->withErrors($validator, 'add_user')
                ->withInput();
        } else {

            $data = array(
                'name' => request('name'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user->id
            );

            $update = master::where('id', request('id'))->update($data);
            Session::flash('success', 'Document type successfully updated.');
            if (request('flag') == 'No') {
                return redirect('/document-item');
            } else {
                return redirect()->back();
            }
        }
    }

    public function documentDeleteByAgency(Request $request, $id)
    {
        $user = auth()->user();
        $docId = $id;

        $masterArray = array(

            'del_flag' => 'Y',
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $user->id
        );
        $insert = Master::where('id', $docId)->update($masterArray);
        if($insert){
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Delete Document From Appointment',
                'link' =>  url('/patient/document-delete/') . $id,
                'module' => 'Patient Appointment',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has Delete Document From Appointment',
                'new_response' => serialize($masterArray),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            Session::flash('success', 'Document  type successfully deleted.');
            return redirect()->back();
        }
      
    }

    function documentexport()
    {
        $user = auth()->user();
        if (in_array($user->user_type_fk, array(3, 4))) {
            $userIDs = request('id');
        } else {
            $userIDs = $user->id;
        }
        $users =  Master::getDocumentListByAgencyId($userIDs);
        //echo "<pre>";print_r($users);die('ello');

        $filename = 'documentlist' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Document Name');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                fputcsv($file, array($list->name));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
