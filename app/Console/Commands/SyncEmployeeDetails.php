<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlayacareService;
use App\Services\AgencyService;
use App\Helpers\AlayacareHelper;
use App\Model\AlayacareEmployee;
use App\Model\AlayacareCronLog;

class SyncEmployeeDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employeeDetail:process-sync-employee-detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync employee demographic details from Alayacare';
    protected $alayacareService;
    protected $agencyService;
    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(AlayacareService $alayacareService,AgencyService $agencyService)
    {
        parent::__construct();
        $this->alayacareService = $alayacareService;
        $this->agencyService = $agencyService;
    }

    public function handle()
    {
        try {
            $employees = $this->alayacareService->getRemainingDemographicDetails();
            if (empty($employees)) {
                return;
            }

            if(!empty($employees[0])){
                foreach($employees as $employee){
                    try {
                        $agencyDetails = $this->getValidAgency($employee->agency_id);
                        if (!$agencyDetails) {
                            continue;
                        }

                        $employeeDetails = $this->fetchEmployeeDetails($agencyDetails->id, $employee->emp_id);
                        if (!$employeeDetails) {
                            continue;
                        }

                        $empDataStore = $this->prepareEmployeeData($employeeDetails);
                        if (empty($empDataStore)) {
                            continue;
                        }
                        $this->saveEmployee($employee, $empDataStore);
                    } catch (\Throwable $e) {
                        $this->errorLog($e, $employee);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Sync Employee Details Fatal Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            AlayacareCronLog::create([
                'error_log'   => $e->getMessage(),
                'line'        => $e->getLine(),
                'trace'       => $e->getTraceAsString(),
                'created_at'  => date('Y-m-d H:i:s'),
                'type'        => 'EmployeeDetails',
                'employee_id'=>$employee->emp_id,
                'agency_id'=>$employee->agency_id
            ]);
        }
    }

    private function getValidAgency($agencyId)
    {
        $agency = $this->agencyService->getDetailsByAlayacareAgency($agencyId);

        if (
            !$agency ||
            empty($agency->alaycare_username) ||
            empty($agency->alaycare_password)
        ) {
            return null;
        }

        return $agency;
    }

    private function fetchEmployeeDetails($agencyId, $empId)
    {
        $response = AlayacareHelper::getEmployeeById($agencyId, $empId);
        $data = json_decode($response);

        return $data && isset($data->demographics) ? $data : null;
    }

    private function prepareEmployeeData($data)
    {
        $demographics = $data->demographics;
     
        $empData = [
            "ac_id" => $data->ac_id ?? null,
            "external_id" => $data->external_id ?? null,
            "address" => $demographics->address ?? null,
            "city" => $demographics->city ?? null,
            "state" => $demographics->state ?? null,
            "zip" => $demographics->zip ?? null,
            "birthday" => $demographics->birthday ?? null,
            "country" => $demographics->country ?? null,
            "language" => $data->language ?? null,
            "status" => $data->status ?? null,
            "gender" => $this->formatGender($demographics->gender ?? null),
            "username" => $data->username ?? null,
            "demographic_updated_flag" => 'Y',
            "phone" => $this->cleanPhone($demographics->phone_main ?? null),
            "last_sync_date" =>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ];

        $this->mapAdditionalFields($empData, $demographics, $data);
        $this->mapDepartment($empData, $data);

        return array_filter($empData, fn($v) => $v !== null && $v !== '');
    }

    private function mapAdditionalFields(&$empData, $demo, $data)
    {

        $fields = [
            "first_language" => $demo->First_Language ?? null,
            "reference_1" => $demo->Reference_1 ?? null,
            "reference_2" => $demo->Reference_2 ?? null,
            "address_suite" => $demo->address_suite ?? null,
            "application_date" => $demo->application_date ?? null,
            "country_of_birth" => $demo->country_of_birth ?? null,
            "email" => $demo->email ?? null,
            "employee_referral_source" => $demo->employee_referral_source ?? null,
            "first_name" => $demo->first_name ?? null,
            "last_name" => $demo->last_name ?? null,
            "job_title" => $demo->job_title ?? null,
            "start_date" => $demo->start_date ?? null,
            "termination_date" => $demo->termination_date ?? null,
            "uid" => $demo->uid ?? null,
            "sin_ssn" => $data->sin_ssn ?? null,
            "designation" => $data->designation ?? null,
            "employment_type" => $data->employment_type ?? null,
            "has_pets" =>  $data->has_pets?? null,
            "hire_date" =>  $data->hire_date?? null,
            "hire_date_new" => $data->hire_date_new ??  null,
            "homecare_registry_number" =>$data->homecare_registry_number ??  null,
            "hoyer_lift" =>$data->hoyer_lift ??  null,
            "import_id" =>$data->import_id ??  null,
            "job_title" =>$data->job_title ??  null,
            "kchecks_notes" =>$data->kchecks_notes ??  null,
            "kchecks_status" =>$data->kchecks_status ??  null,
            "referral_person" =>$data->referral_person ??  null,
            "start_date" =>$data->start_date ??  null,
            "start_on" => $data->start_on ??  null,
            "termination_date" => $data->termination_date ??  null,
            "uid" => $data->uid  ??  null,

            "sin_ssn" => $data->sin_ssn ??  null,
            "designation" =>$data->designation ??   null,
            "employment_type" =>$data->employment_type ??  null,
        ];

        $empData = array_merge($empData, $fields);

    }

    private function mapDepartment(&$empData, $data)
    {
        if (!empty($data->departments[0])) {
            $dept = $data->departments[0];

            $empData['department_id'] = $dept->id ?? null;
            $empData['department_name'] = $dept->name ?? null;
        }

    }

    private function formatGender($gender)
    {
        switch ($gender) {
            case 'M':
                return 'Male';
            case 'F':
                return 'Female';
            default:
                return $gender;
        }
    }

    private function cleanPhone($phone)
    {
        if (!$phone) return null;

        return str_replace(['+', '-', '(', ')', '[', ']'], '', $phone);
    }

    private function saveEmployee($employee, $data)
    {
        AlayacareEmployee::updateOrCreate(
            [
                'emp_id'    => $employee->emp_id,
                'agency_id' => $employee->agency_id
            ],
            $data
        );
    }

    private function errorLog($e, $employee){
        try {
            AlayacareCronLog::create([
                'error_log'   => $e->getMessage(),
                'line'        => $e->getLine(),
                'trace'       => $e->getTraceAsString(),
                'created_at'  => date('Y-m-d H:i:s'),
                'employee_id' => $employee->emp_id ?? null,
                'type'        => 'EmployeeDetails',
                'agency_id'   => $employee->agency_id ?? null,
            ]);
        } catch (\Throwable $logException) {
            \Log::error('Failed to save AlayacareCronLog: ' . $logException->getMessage(), [
                'original_error' => $e->getMessage(),
                'agency_id' => $employee->agency_id ?? null,
                'emp_id' => $employee->emp_id ?? null,
            ]);
        }
    }
}
