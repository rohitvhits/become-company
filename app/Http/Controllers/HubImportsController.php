<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Helpers\Common;
use App\Agency;
use App\Model\HubRecord;
use App\Model\HubCompany;
use App\Services\HubImportsService;
use URL;
use Response;

class HubImportsController extends BaseController
{
    protected $hubImportsService;

    public function __construct(HubImportsService $hubImportsService)
    {
        $this->middleware('auth');
        $this->hubImportsService = $hubImportsService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "hub-imports";
        $data['user'] = $user = auth()->user();
        $agencyObj = Common::getAgencyDetails();

        $data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
        $data['uniqueFields'] = HubRecord::getUniqueFields();
        $data['allFields'] = HubRecord::getFieldsList();

        return view("hubImports/import_list", $data);
    }

    public function upload(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'unique_fields' => 'required|array|min:1',
            'unique_fields.*' => 'string|in:email,ssn,member_id,employee_code'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        }

        $agencyId = $user->agency_fk ?: $request->input('agency_id');

        if (!$agencyId) {
            return response()->json([
                'success' => false,
                'message' => 'Agency is required'
            ], 400);
        }

        try {
            $file = $request->file('file');
            $uniqueFields = $request->input('unique_fields', []);

            $result = $this->hubImportsService->processEmployeeImport($file, $agencyId, $uniqueFields);

            return response()->json([
                'success' => true,
                'message' => 'File processed successfully',
                'summary' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employee_import_sample.csv"',
        ];

        $sampleData = [
            [
                'Last Name',
                'First Name',
                'Middle Initial',
                'Birth Date',
                'Gender',
                'Email Address',
                'Primary Address 1',
                'Primary Address 2',
                'Primary City',
                'Primary State',
                'Primary Zip Code',
                'Home Phone',
                'Mobile Phone',
                'Company',
                'SSN',
                'Hire Date',
                'Work Contact',
                'Work Email',
                'Last Worked Date',
                'Member Id',
                'Employee Code'
            ],
            [
                'Smith',
                'John',
                'A',
                '1985-05-15',
                'Male',
                'john.smith@email.com',
                '123 Main St',
                'Apt 4B',
                'New York',
                'NY',
                '10001',
                '(555) 123-4567',
                '(555) 987-6543',
                'ABC Healthcare',
                '123-45-6789',
                '2020-01-15',
                'Jane Doe',
                'john.smith@company.com',
                '2024-12-31',
                'EMP001',
                'JSmith001'
            ],
            [
                'Johnson',
                'Mary',
                'B',
                '1990-08-22',
                'Female',
                'mary.johnson@email.com',
                '456 Oak Ave',
                '',
                'Los Angeles',
                'CA',
                '90210',
                '(555) 234-5678',
                '(555) 876-5432',
                'XYZ Medical',
                '987-65-4321',
                '2021-03-01',
                'Bob Wilson',
                'mary.johnson@company.com',
                '',
                'EMP002',
                'MJohnson002'
            ]
        ];

        $output = fopen('php://output', 'w');

        return response()->stream(function () use ($sampleData, $output) {
            foreach ($sampleData as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, $headers);
    }

    public function getImportHistory(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_fk ?: $request->input('agency_id');

        $history = $this->hubImportsService->getImportHistory($agencyId);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function getImportDetails(Request $request, $importId)
    {
        $details = $this->hubImportsService->getImportDetails($importId);

        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }
}
