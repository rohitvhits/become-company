<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\PaymentTypeReportService;
use App\Agency;
use App\Master;
use URL;
use Illuminate\Support\Facades\Cache;

class PaymentTypeReportController extends BaseController
{
    protected $paymentTypeReportService;

    public function __construct(PaymentTypeReportService $paymentTypeReportService)
    {
        $this->middleware('auth');
        $this->paymentTypeReportService = $paymentTypeReportService;
    }

    public function index(Request $request)
    {
        $data['user'] = auth()->user();
        if($data['user']->agency_fk !=""){
            $agency_id = $data['user']->agency_fk;
            $agency = Agency::select('id','view_payment_report')->find($agency_id);
            if($agency->view_payment_report == 0){
                abort(404);
            }
		}
        $data['agency_fk'] = $agency_fk = $request->input('agency_fk');
        $data['payment_type'] = $request->input('payment_type');
        $data['search_term'] = $search_term = $request->input('search_term');

        // Get agency list
        $data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();

        // Get payment types from Master table
        $data['paymentTypesList'] = $this->paymentTypeReportService->getPaymentTypesMaster();

        // Get payment types with counts
        $data['paymentTypeCounts'] = $this->paymentTypeReportService->getPaymentTypeCounts($agency_fk, $search_term);

        return view('payment_type_report/payment_type_report_list', $data);
    }

    public function ajaxList(Request $request)
    {
        $data['user'] = auth()->user();
        $agency_fk = $request->input('agency_fk');
        $payment_type = $request->input('payment_type');
        $appointment_date = $request->input('appointment_date');
        $search_term = $request->input('search_term');
        $payment_type_status = $request->payment_type_status;
        // Get patient list using service
        $data['patients'] = $this->paymentTypeReportService->getPatientsByPaymentType($agency_fk, $payment_type, $search_term,$appointment_date,1,$payment_type_status);
        foreach($data['patients'] as $patient){
            $explode = explode(',', $patient->service_id);
            $newss = $patient->service_id;

            if ($newss != '') {
                $sins = Cache::get('patient_master_' . implode(",", $explode), function () use ($explode) {
                    return Master::select('name')->whereIn('id', $explode)->where('del_flag', 'N')->get();
                }, 10 * 60);

                $nrens = array();
                foreach ($sins as $names) {
                    $nrens[$patient->id][] = $names->name;
                }
            }
            $patient->service_name = '';
            if (isset($nrens[$patient->id]) && $nrens[$patient->id] != '') {
                $patient->service_name = implode(', ', $nrens[$patient->id]);
            }
        }
        return view('payment_type_report/payment_type_report_ajax_list', $data);
    }

    public function ajaxCounts(Request $request)
    {
        $agency_fk = $request->input('agency_fk');
        $payment_type = $request->input('payment_type');
        $appointment_date = $request->input('appointment_date');
        $search_term = $request->input('search_term');
        $payment_type_status = $request->payment_type_status;
        // Get payment type counts using service with filters
        $paymentTypeCounts = $this->paymentTypeReportService->getPaymentTypeCounts($agency_fk, $payment_type, $search_term, $appointment_date,$payment_type_status);

        return response()->json([
            'success' => true,
            'counts' => $paymentTypeCounts
        ]);
    }

    public function export(Request $request)
    {
        $agency_fk = $request->input('agency_fk');
        $payment_type = $request->input('payment_type');
        $search_term = $request->input('search_term');
        $appointment_date = $request->input('appointment_date');
        $payment_type_status = $request->payment_type_status;
        $patients = $this->paymentTypeReportService->getPatientsByPaymentType($agency_fk, $payment_type, $search_term,$appointment_date,$payment_type_status);

        $filename = 'PaymentTypeReport_' . date("m-d-Y");
        $headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
        $agency_fk = auth()->user()->agency_fk;
        if($agency_fk == ""){
            $columns = [
                'ID',
                'Patient Code',
                'Agency Name',
                'Type',
                'Full Name',
                'Status',
                'Services',
                'Mobile',
                'Phone',
                'Payment Type',
                'DOB',
                'Gender',
                'Appointment Date'
            ];
        }else{
            $columns = [
                    'ID',
                    'Patient Code',
                    'Type',
                    'Full Name',
                    'Status',
                    'Services',
                    'Mobile',
                    'Phone',
                    'Payment Type',
                    'DOB',
                    'Gender',
                    'Appointment Date'
                ];
        }
        
        if(count($patients) > 0){
            $callback = function () use ($patients, $columns,$agency_fk) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($patients as $patient) {
                    $dob = '';
                    if ($patient->dob != '0000-00-00' && $patient->dob != '') {
                        $dob = date('m-d-Y', strtotime($patient->dob));
                    }

                    $appointmentInfo = '';
                    $telehealthInfo = '';
                     if (strtolower($patient->type) == 'caregiver') {
                        if (!empty($patient->appointment_date)) {
                            $appointmentDate = date('m/d/Y', strtotime($patient->appointment_date));
                            $appointmentInfo .= "Schedule Appointment: " . $appointmentDate;
                        }

                        if (!empty($patient->telehealth_date_time)) {
                            $telehealthInfo .= "Telehealth Appointment: " .
                                date('m/d/Y', strtotime($patient->telehealth_date_time));
                        }
                    }

                    // Patient
                    if (strtolower($patient->type) == 'patient') {
                        if (!empty($patient->appointment_date)) {
                            $appointmentInfo .= "Schedule Appointment: " .
                                date('m/d/Y', strtotime($patient->appointment_date));
                        }

                        if (!empty($patient->telehealth_date_time)) {
                            $telehealthInfo .= "Telehealth Appointment: " .
                                date('m/d/Y', strtotime($patient->telehealth_date_time));
                        }
                    }

                    $explode = explode(',', $patient->service_id);
                    $newss = $patient->service_id;
                    if ($newss != '') {
                        $sins = Cache::get('patient_master_' . implode(",", $explode), function () use ($explode) {
                            return Master::select('name')->whereIn('id', $explode)->where('del_flag', 'N')->get();
                        }, 10 * 60);

                        $nrens = array();
                        foreach ($sins as $names) {
                            $nrens[$patient->id][] = $names->name;
                        }
                    }
                    $patient->service_name = '';
                    if (isset($nrens[$patient->id]) && $nrens[$patient->id] != '') {
                        $patient->service_name = implode(', ', $nrens[$patient->id]);
                    }
                    if($agency_fk == ""){
                        fputcsv($file, [
                            $patient->id ?? '',
                            $patient->patient_code ?? '',
                            $patient->agency_name ?? '',
                            $patient->type ?? '',
                            $patient->first_name ." ".$patient->last_name,
                            $patient->status ?? '',
                            $patient->service_name,
                            $patient->mobile ?? '',
                            $patient->phone ?? '',
                            $patient->payment_type_name ?? '',
                            $dob,
                            $patient->gender ?? '',
                            $appointmentInfo . $telehealthInfo
                        ]);
                    }else{
                        fputcsv($file, [
                            $patient->id ?? '',
                            $patient->patient_code ?? '',
                            $patient->type ?? '',
                            $patient->first_name ." ".$patient->last_name,
                            $patient->status ?? '',
                            $patient->service_name,
                            $patient->mobile ?? '',
                            $patient->phone ?? '',
                            $patient->payment_type_name ?? '',
                            $dob,
                            $patient->gender ?? '',
                            $appointmentInfo . $telehealthInfo
                        ]);
                    }
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
    }
}
