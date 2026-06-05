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
use App\Services\LogsService;

use App\Doctor;

use App\Services\DoctorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Utility;

class DoctorController extends BaseController
{
    protected $doctorService;
    protected const COMMON_UPLOAD_DOC = "dosusinguploads/docusign";
    protected const RETURN_REDIRECTION = 'doctor';

    public function __construct(DoctorService $doctorService)
    {
        $this->middleware('permission:doctor-list|doctor-add|doctor-edit|doctor-delete', ['only' => ['index', 'save']]);
        $this->middleware('permission:doctor-list', ['only' => ['index']]);
        $this->middleware('permission:doctor-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:doctor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:doctor-delete', ['only' => ['delete']]);
        $this->middleware('permission:doctor-edit', ['only' => ['toggleDoctorStatus', 'toggleSignatureStampStatus']]);

        $this->middleware('auth');
        $this->doctorService = $doctorService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "user";
        $data['user'] = auth()->user();
        $full_name = $data['full_name'] = request('full_name');
        $email = $data['email'] = request('email');
        $phone = $data['phone'] = request('phone');
        $license = $data['license'] = request('license');
        $state = $data['state'] = request('state');
        $city = $data['city'] = request('city');
        $zipcode = $data['zipcode'] = request('zipcode');
        $place_of_examination = $data['place_of_examination'] = request('place_of_examination');
        $date_of_examination = $data['date_of_examination'] = request('date_of_examination');
        $is_active = $data['is_active'] = request('is_active');
        $is_signature_stamp_active = $data['is_signature_stamp_active'] = request('is_signature_stamp_active');

        $data['query'] = $this->doctorService->getData($full_name, $email, $phone,$license,$state,$city,$zipcode,$place_of_examination,$date_of_examination,$is_active,$is_signature_stamp_active);

        return view("doctor/doctor_list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add Doctor";
        $data['user'] = auth()->user();

        return view("doctor/doctor_add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email|unique:doctor_master,email',
            'phone' => 'required|unique:doctor_master,phone',
            'gender' => 'required',
            'license' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'place_of_examination' => 'required',
            'date_of_examination' => 'required',
            'specialty' => 'required',
            'registry_number' => 'required',
            'npi_number' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("/doctor/add")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $full_name = request('full_name');
            $email = request('email');
            $phone = request('phone');
            $gender = request('gender');
            $message = request('message');
            $license = request('license');
            $address = request('address');
            $state = request('state');
            $city = request('city');
            $zipcode = request('zipcode');
            $place_of_examination = request('place_of_examination');
            $date_of_examination = date('Y-m-d',strtotime(request('date_of_examination')));
            $specialty = request('specialty');
            $registry_number = request('registry_number');
            $npi_number = request('npi_number');
            $name ="";
            
            if ($request->file('signature_upload') != '') {
                $name = $this->uploadSignatureStamp($request,'signature_upload');
			}

            $stampName ="";
            if ($request->file('') != '') {
                $stampName = $this->uploadSignatureStamp($request,'stamp_upload');
			}

            $data = array(
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'remarks' => $message,
                'license' => $license,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'zipcode' => $zipcode,
                'place_of_examination' => $place_of_examination,
                'date_of_examination' => $date_of_examination,
                'signature_upload'=>$name,
                'stamp_upload'=>$stampName,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
                'specialty' => $specialty,
                'registry_number' => $registry_number,
                'npi_number' => $npi_number,
            );
            $insert = $this->doctorService->save($data);
    
            if ($insert) {

                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/doctor/save'),
                    'module' => 'Doctor',
                    'object_id' => $insert,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Doctor',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                Session::flash('success', 'Doctor successfully added.');
                return redirect('/'.self::RETURN_REDIRECTION);
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/doctor/add');
            }
        }
    }

    public function edit($id)
    {
        $data['menu'] = "user";
        $data['user'] = auth()->user();
        $data['id'] = $id;
        $data['doctor'] = $this->doctorService->getDetailById($id);

        return view('doctor/doctor_edit', $data);
    }

    public function update(Request $request, $id)
    {
        
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email|unique:doctor_master,email,' . $id . ',id',
            'phone' => 'required|unique:doctor_master,phone,' . $id . ',id',
            'gender' => 'required',
            'license' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'place_of_examination' => 'required',
            'date_of_examination' => 'required',
            'specialty' => 'required',
            'registry_number' => 'required',
            'npi_number' => 'required',
            
        ]);
        if ($validator->fails()) {
            return redirect("/doctor/edit/$id")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $full_name = request('full_name');
            $email = request('email');
            $phone = request('phone');
            $gender = request('gender');
            $message = request('message');
            $license = request('license');
            $address = request('address');
            $state = request('state');
            $city = request('city');
            $zipcode = request('zipcode');
            $place_of_examination = request('place_of_examination');
            $date_of_examination = date('Y-m-d',strtotime(request('date_of_examination')));
            $specialty = request('specialty');
            $registry_number = request('registry_number');
            $npi_number = request('npi_number');
            $image = '';
            if ($request->file('signature_upload') != '') {
				$image = $this->uploadSignatureStamp($request,'signature_upload');
               
			}
            $stampImage = '';
            if ($request->file('stamp_upload') != '') {
                $stampImage = $this->uploadSignatureStamp($request,'stamp_upload');
				
			}
            $data = array(
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'remarks' => $message,
                'license' => $license,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'zipcode' => $zipcode,
                'place_of_examination' => $place_of_examination,
                'date_of_examination' => $date_of_examination,
                'specialty' => $specialty,
                'registry_number' => $registry_number,
                'npi_number' => $npi_number,
            );
            if ($image !="") {
                $data['signature_upload'] = $image;
            }

            if ($stampImage !="") {
                $data['stamp_upload'] = $stampImage;
            }

            $this->doctorService->update($data, array('id' => $id));

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update',
                'link' => url('/doctor/update/' . $id),
                'module' => 'Doctor',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated Doctor',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            Session::flash('success', 'Doctor successfully update.');
            return redirect('/'.self::RETURN_REDIRECTION);
        }
    }

    public function delete($id)
    {
        $user = auth()->user();

        $data['id'] = $id;
        $data = array('deleted_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id);
        $update = $this->doctorService->SoftDelete($data, array('id' => $id));

        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Delete',
            'link' => url('/doctor/delete/' . $id),
            'module' => 'Doctor',
            'object_id' => $id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Doctor',
            'new_response' => serialize($data),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($update) {
            Session::flash('success', 'Doctor successfully delete.');
            return redirect('/doctor');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/doctor');
        }
    }

    public function agencyExport(Request $request)
    {
        $full_name = $data['full_name'] = request('full_name');
        $email = $data['email'] = request('email');
        $phone = $data['phone'] = request('phone');
        $license = $data['license'] = request('license');
        $address = $data['address'] = request('address');
        $state = $data['state'] = request('state');
        $city = $data['city'] = request('city');
        $zipcode = $data['zipcode'] = request('zipcode');
        $place_of_examination = $data['place_of_examination'] = request('place_of_examination');
        $date_of_examination = $data['date_of_examination'] = request('date_of_examination');
        $is_active = request('is_active');
        $is_signature_stamp_active = request('is_signature_stamp_active');
        $users = $this->doctorService->getDataExport($full_name, $email, $phone, $license, $address, $state, $city, $zipcode, $place_of_examination, $date_of_examination, $is_active, $is_signature_stamp_active);
      
        $filename = 'Doctor' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Full Name', 'Email', 'Phone', 'Gender', 'Remark', 'License', 'Address', 'State', 'City', 'Zipcode', 'Place Of Examination', 'Date Of Examination', 'Doctor Status', 'Signature & Stamp Status');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                fputcsv($file, array($list->full_name, $list->email, $list->phone, $list->gender, $list->remarks, $list->license, $list->address, $list->state, $list->city, $list->zipcode, $list->place_of_examination, $list->date_of_examination, $list->is_active == 1 ? 'Active' : 'Inactive', $list->is_signature_stamp_active == 1 ? 'Active' : 'Inactive'));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function getDoctorLogPage(Request $request)
    {
        $id = request('id');
        $data['user'] = Auth::user();
        $data['logList'] = LogsService::getDatByAllLog($id, 'Doctor');

        return view("user_log_ajax_list", $data);
    }
    public function logs($id)
    {
        $data['id'] = $id;
        return view('doctor/doctor_log_list', $data);
    }

    public function toggleDoctorStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()[0]], 422);
        }

        $doctor = $this->doctorService->getDetailById($request->id);
        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
        }

        $newStatus = $doctor->is_active == 1 ? 0 : 1;
        $this->doctorService->update(['is_active' => $newStatus], ['id' => $request->id]);

        $user = auth()->user();
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $ipaddress = Utility::getIP();
        LogsService::save([
            'type'         => 'Toggle Doctor Status',
            'link'         => url('/doctor/toggle-status'),
            'module'       => 'Doctor',
            'object_id'    => $request->id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has ' . $statusText . ' Doctor.',
            'new_response' => serialize(['is_active' => $newStatus]),
            'ip'           => $ipaddress,
        ]);

        return response()->json(['success' => true, 'new_status' => $newStatus, 'error_message' => 'Doctor ' . $statusText . ' successfully.']);
    }

    public function toggleSignatureStampStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()[0]], 422);
        }

        $doctor = $this->doctorService->getDetailById($request->id);
        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
        }

        $newStatus = $doctor->is_signature_stamp_active == 1 ? 0 : 1;
        $this->doctorService->update(['is_signature_stamp_active' => $newStatus], ['id' => $request->id]);

        $user = auth()->user();
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $ipaddress = Utility::getIP();
        LogsService::save([
            'type'         => 'Toggle Signature Stamp Status',
            'link'         => url('/doctor/toggle-signature-stamp-status'),
            'module'       => 'Doctor',
            'object_id'    => $request->id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has ' . $statusText . ' Signature & Stamp for Doctor.',
            'new_response' => serialize(['is_signature_stamp_active' => $newStatus]),
            'ip'           => $ipaddress,
        ]);

        return response()->json(['success' => true, 'new_status' => $newStatus, 'error_message' => 'Signature & Stamp ' . $statusText . ' successfully.']);
    }

    private function uploadImageStamAWS($filePath,$content,$fileName){
        Storage::disk('s3')->putFileAs($filePath, $content, $fileName);
    }

    private function uploadSignatureStamp($request,$field){
        $signatureUpload = $request->file($field);
        $name = uniqid() . time() . '.' . $signatureUpload->getClientOriginalExtension();
        if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
            /****doctor-signature */
            $destination = public_path(self::COMMON_UPLOAD_DOC);
            $signatureUpload->move($destination, $name);
        } else {
            $this->uploadImageStamAWS(self::COMMON_UPLOAD_DOC,$signatureUpload,$name);
        }

        return $name;
    }
}
