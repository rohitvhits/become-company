<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\HHADueMedicalService;
use App\Helpers\HHACaregiversHelper;
use App\Model\HHAOffice;
use Illuminate\Support\Facades\Cache;
use DB;
use Carbon\Carbon;
use App\Services\HHAOfficeService;
use App\Services\MasterService;
use App\Master;
use App\Services\PatientService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Services\DocumentPatientService;

class HHADueMedicalController extends BaseController
{
    protected $hhaDueMedicalService;
    protected $hhaOfficeService;
    protected $masterService;
    protected $patientService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $documentPatientService;
    protected const MDY_DATE_FORMAT = 'm/d/Y';
    protected const REMOVE_DATE_FORMAT = '0000-00-00';
    protected const REMOVE_DATE_DEFAULT = "1969-12-31";

    public function __construct(
        HHAOfficeService $hhaOfficeService,
        HHADueMedicalService $hhaDueMedicalService,
        MasterService $masterService,
        PatientService $patientService,
        PatientServicesRequest $patientServicesRequest,
        PatientWiseServicesRequests $patientWiseServicesRequests,
        DocumentPatientService $documentPatientService
    ) {
        $this->middleware('auth');
        $this->middleware(
            'permission:hha-due-medical|hha-due-medical-export',
            ['only' => ['index', 'hhaAppoitmentAjax']]
        );
        $this->middleware(
            'permission:hha-due-medical-export',
            ['only' => ['exportCsv']]
        );

        $this->hhaDueMedicalService = $hhaDueMedicalService;
        $this->hhaOfficeService = $hhaOfficeService;
        $this->masterService = $masterService;
        $this->patientService = $patientService;
        $this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->documentPatientService = $documentPatientService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }

        $data['status_list'] = Cache::get('hha_due_medical_status_list', function () {
            return HHACaregiversHelper::getHHACaregiverStatus();
        }, 10 * 60);

        $data['office_table_list'] = Cache::get('hha_due_medical_office_table_list', function () {
            return $this->hhaOfficeService->getALLOfficeList();
        }, 10 * 60);

        $data['agency_list'] = Cache::get('hha_due_medical_agency_table_list', function () {
            return Agency::getHHAAgencyList();
        }, 10 * 60);

        $data['startDate'] = Carbon::now()
            ->subMonths(2)
            ->startOfMonth()
            ->format(self::MDY_DATE_FORMAT);

        $data['endDate'] = Carbon::now()->format(self::MDY_DATE_FORMAT);

        return view("hha_due_medical.hha_due_medical_list", $data);
    }

    public function ajaxList(Request $request)
    {
        $data['query'] = $this->hhaDueMedicalService->dueMedicalList($request->all());

        $data['agencyListDetails'] = Cache::get('hha_agency_list', function () {
            return Agency::where('delete_flag', 'N')
                ->whereNotNull('app_name')
                ->where('enable_hha', 1)
                ->pluck('agency_name', 'id');
        });

        $data['office_list'] = Cache::remember('hha_office_list', 10 * 60, function () {
            return $this->hhaOfficeService->getPluckOfficeList();
        });

        return view("hha_due_medical.hha_due_medical_ajax", $data);
    }

    public function exportCsv(Request $request)
    {
        $query = $this->hhaDueMedicalService->dueMedicalList($request->all(), 'export');

        $filename = 'hha_due_medical' . date('m-d-Y');
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = $this->createCsvColumn();

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($query as $row) {
                fputcsv($file, $this->mapCsvRow($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function createCsvColumn(): array
    {
        return [
            'Agency Name',
            'Office Name',
            'Caregiver Full Name',
            'Caregiver Code',
            'Caregiver Phone',
            'DOB',
            'Caregiver Status',
            'Hire Date',
            'Language',
            'Discipline',
            'Employeement Type',
            'Medical Name',
            'Due Date',
            'Medical Status',
            'Appointment Status',
            'First Work Date',
            'Last Work Date',
            'Last SYNC Date'
        ];
    }
    
    private function mapCsvRow($row): array
    {
        return [
            $this->value($row->agencyDetails->agency_name ?? null),
            $this->value($row->hhaOffices->office_name ?? null),
            trim($this->value($row->first_name) . ' ' . $this->value($row->last_name)),
            trim($this->value($row->hhaOffices->office_code ?? null) . ' - ' . $this->value($row->caregiver_code)),
            $this->value($row->mobile_or_sms),
            $this->formatDate($row->dob),
            $this->value($row->caregiverStatus),
            $this->formatDate($row->hire_date),
            $this->value($row->language),
            $this->value($row->EmploymentTypesDiscipline),
            $this->value($row->employment_type),
            $this->value($row->medical_name),
            $this->formatDateTime($row->due_date),
            $this->value($row->status),
            $row->patient_id ? 'Added' : 'Pending',
            $this->formatDate($row->first_work_date),
            $this->formatDate($row->last_work_date),
            $this->formatDateTime($row->updated_date),
        ];
    }

    private function value($value): string
    {
        return !empty($value) ? (string) $value : '';
    }

    private function formatDate($date): string
    {
        if (
            empty($date) ||
            $date === self::REMOVE_DATE_FORMAT ||
            $date === self::REMOVE_DATE_DEFAULT
        ) {
            return '';
        }

        return date(self::MDY_DATE_FORMAT, strtotime($date));
    }

    private function formatDateTime($date): string
    {
        if (
            empty($date) ||
            $date === self::REMOVE_DATE_FORMAT . ' 00:00:00' ||
            $date === self::REMOVE_DATE_DEFAULT . ' 00:00:00'
        ) {
            return '';
        }

        return date(self::MDY_DATE_FORMAT, strtotime($date));
    }

    public function addAppoinmentPatient(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids[0])) {
            return response()->json([
                'error_msg' => "Sorry, something went wrong. Please try again.",
                'data' => []
            ], 500);
        }

        foreach ($ids as $val) {
            $this->processAppointmentPatient($val);
        }

        return response()->json([
            'error_msg' => "Appointment successfully added",
            'data' => []
        ], 200);
    }

    private function processAppointmentPatient($val)
    {
       
        $getDetails = $this->hhaDueMedicalService->getDetailsById($val);

        if (!$getDetails) {
            return;
        }

        $medicalId = $this->resolveMedicalServiceId($getDetails);

        $patientId = $this->saveCaregiverPatient($getDetails, $medicalId);

        if ($patientId) {
            $this->handlePatientServices($patientId, $medicalId);
            $this->savePatientDocument($patientId, $getDetails);

        }
        $this->hhaDueMedicalService->update(array('patient_id'=> $patientId),array('id'=>$val));
      
    }

    private function resolveMedicalServiceId($details)
    {
        if (empty($details->medical_name)) {
            return null;
        }

        $service = Master::getServiceName($details->medical_name);

        if (!empty($service->id)) {
            return $service->id;
        }

        $master =$this->masterService->save([
            'name' => $details->medical_name,
            'master_type_fk' => 11,
            'types' => "Caregiver",
        ]);

        return $master->id;
    }

    private function saveCaregiverPatient($details, $medicalId)
    {
        $data = [
            'first_name' => $details->caregiver_first_name,
            'middle_name' => $details->caregiver_middle_name,
            'last_name' => $details->caregiver_last_name,
            'full_name' => $details->caregiver_first_name . ' ' . $details->caregiver_last_name,
            'patient_code' => $details->caregiver_code,
            'agency_id' => $details->agency_id,
            'phone' => $details->caregiver_phone,
            'mobile' => $details->caregiver_phone,
            'type' => 'Caregiver',
            'service_id' => $medicalId,
            'dob' => $details->dob,
            'gender' => $details->gender,
            'link_hha_caregiver' => $details->caregiver_id,
            'address1' => $details->address1,
            'address2' => $details->address2,
            'state' => $details->State,
            'city' => $details->City,
            'zip_code' => $details->Zip5,
            'referral_type' => 'HHA Exchange'
        ];

        return $this->patientService->save($data);
    }

    private function handlePatientServices($patientId, $medicalId)
    {
        if (empty($medicalId)) {
            return;
        }

        $existing = $this->patientServicesRequest
            ->getServiceCountPatientId($patientId);

        if (count($existing) > 0) {
            return;
        }

        $serviceRequestId = $this->patientServicesRequest->save([
            'patient_id' => $patientId,
            'follow_up_date' => null,
            'due_date' => null,
            'status' => "Pending",
            'created_at' => now(),
            'created_by' => auth()->id(),
            'completed_date' => null,
            'completed_by' => null,
            'flag' => 1
        ]);

        $this->patientWiseServicesRequests->save([
            'patient_id' => $patientId,
            'service_id' => $medicalId,
            'patient_service_request_id' => $serviceRequestId,
        ]);
    }

    private function savePatientDocument($patientId, $details)
    {
        $this->documentPatientService->save([
            'patient_id' => $patientId,
            'document_name' => $details->medical_name,
            'hha_medical_doc_id' => $details->medical_id
        ]);
    }
}
