<?php

namespace App\Http\Controllers;

use App\Services\PatientService;
use App\Services\RingLogixService;
use App\Helpers\RingLogixHelper;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallDetailController extends BaseController
{
    protected RingLogixService $ringLogixService;
    protected PatientService $patientService;

    public function __construct(RingLogixService $ringLogixService, PatientService $patientService)
    {
        $this->middleware('auth');
        $this->ringLogixService = $ringLogixService;
        $this->patientService   = $patientService;
    }

    public function index(Request $request, $patientId = null)
    {
        $patient = null;
        if ($patientId) {
            $patient = $this->patientService->getPatientId($patientId);
            abort_if(!$patient, 404);
        }

        $filters = [
            'start_date'  => $request->input('start_date', Utility::convertYMD('yesterday')),
            'end_date'    => $request->input('end_date', Utility::convertYMD('today')),
            'phone'       => $request->input('phone', ''),
            'type'        => $request->input('type', ''),
            'extension'   => $request->input('extension', ''),
            'caller_name' => $request->input('caller_name', ''),
        ];

        return view('call_details.index', [
            'menu'    => 'Call Details',
            'patient' => $patient,
            'filters' => $filters,
        ]);
    }

    public function ajaxTable(Request $request, $patientId = null)
    {
        $patient = null;
        if ($patientId) {
            $patient = $this->patientService->getPatientId($patientId);
            abort_if(!$patient, 404);
        }

        $perPage = 100;
        $page    = max(1, (int) $request->input('page', 1));

        $validator = Validator::make($request->all(), [
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'phone'       => 'nullable|string|max:20',
            'type'        => 'nullable|integer|in:0,1,2',
            'extension'   => 'nullable|string|max:20',
            'caller_name' => 'nullable|string|max:100',
        ]);

        $startDate    = Utility::convertYMD('yesterday') . ' 00:00:00';
        $endDate      = Utility::convertYMD('today') . ' 23:59:59';
        $phoneFilter  = $patient
            ? array_values(array_filter([$patient->mobile, $patient->phone], fn($p) => !empty(trim((string) $p))))
            : array_filter([$request->input('phone')], fn($p) => !empty(trim((string) $p)));
        $allRecords   = [];
        $errorMessage = null;

        if (!$validator->fails()) {
            $startDate = $request->input('start_date')
                ? Utility::convertYMDTime($request->input('start_date'))
                : $startDate;

            $endDate = $request->input('end_date')
                ? Utility::convertYMD($request->input('end_date')) . ' 23:59:59'
                : $endDate;

            $typeFilter       = $request->input('type', '');
            $extensionFilter  = trim($request->input('extension', ''));
            $callerNameFilter = trim($request->input('caller_name', ''));

            try {
                // Fetch all records — cache key is domain+dates only, so no start/limit here
                $allRecords = $this->ringLogixService->getCallDetails([
                    'domain'     => env('RINGLOGIX_DOMAIN'),
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'phone'      => $phoneFilter,
                ]);

                if ($typeFilter !== '' && $typeFilter !== null) {
                    $allRecords = array_values(array_filter($allRecords, fn($c) => (string)($c['type'] ?? '') === (string)$typeFilter));
                }

                if ($extensionFilter !== '') {
                    $allRecords = array_values(array_filter($allRecords, function ($c) use ($extensionFilter) {
                        $ext = (string)($c['orig_sub'] ?? $c['by_sub'] ?? $c['CdrR']['orig_sub'] ?? '');
                        return stripos($ext, $extensionFilter) !== false;
                    }));
                }

                if ($callerNameFilter !== '') {
                    $allRecords = array_values(array_filter($allRecords, function ($c) use ($callerNameFilter) {
                        $name = (string)($c['orig_from_name'] ?? $c['CdrR']['orig_from_name'] ?? '');
                        return stripos($name, $callerNameFilter) !== false;
                    }));
                }
            } catch (\Throwable $e) {
                $errorMessage = $e->getMessage();
            }
        }

        $totalCount  = count($allRecords);
        $pageRecords = array_slice($allRecords, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $pageRecords,
            $totalCount,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return view('call_details._partial.call_ajax_list', [
            'callDetails'  => $paginator,
            'errorMessage' => $errorMessage,
            'totalCount'   => $totalCount,
        ]);
    }

    public function ajaxList(Request $request, $patientId = null)
    {
        try {
            $data = $this->getCallDetailData($request, $patientId);

            if ($data['validator']->fails()) {
                return response()->json([
                    'status'    => 0,
                    'error_msg' => $data['validator']->errors()->first(),
                    'data'      => [],
                ], 422);
            }

            return response()->json([
                'status'    => 1,
                'error_msg' => 'Success',
                'data'      => $this->formatAjaxResponseData($data),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 0,
                'error_msg' => 'Error loading call details: ' . $e->getMessage(),
                'data'      => [],
            ], 500);
        }
    }

    public function ajaxMessages(Request $request, $patientId)
    {
        try {
            $patient = $this->patientService->getPatientId($patientId);
            abort_if(!$patient, 404);

            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',
                'limit'      => 'nullable|integer|min:1|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => 0,
                    'error_msg' => $validator->errors()->first(),
                    'data'      => [],
                ], 422);
            }

            $startDate = $request->input('start_date')
                ? Utility::convertYMDTime($request->input('start_date'))
                : Utility::convertYMD('-7 days') . ' 00:00:00';

            $endDate = $request->input('end_date')
                ? Utility::convertYMD($request->input('end_date')) . ' 23:59:59'
                : Utility::convertYMD('today') . ' 23:59:59';

            $limit = (int) $request->input('limit', 100);

            $messages = $this->ringLogixService->getMessages([
                'domain'     => env('RINGLOGIX_DOMAIN'),
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'limit'      => $limit,
                'user'       => RingLogixHelper::normalizePhone($patient->mobile),
            ]);

            return response()->json([
                'status'    => 1,
                'error_msg' => 'Success',
                'data'      => [
                    'patient' => [
                        'id'        => $patient->id,
                        'full_name' => trim($patient->first_name . ' ' . $patient->middle_name . ' ' . $patient->last_name),
                        'mobile'    => $patient->mobile ?: '-',
                    ],
                    'messages'      => $messages,
                    'total_records' => count($messages),
                    'filters'       => [
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                        'limit'      => $limit,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 0,
                'error_msg' => 'Error loading messages: ' . $e->getMessage(),
                'data'      => [],
            ], 500);
        }
    }

    public function recordingUrl(Request $request)
    {
        $request->validate([
            'cdr_id' => 'required|string',
        ]);

        $cdrId     = trim($request->input('cdr_id'));
        $timeStart = trim($request->input('time_start', ''));

        try {
            $origCallId = $this->ringLogixService->getCdrOrigCallId($cdrId, $timeStart);

            if (!$origCallId) {
                return response()->json(['status' => 0, 'error_msg' => 'No call record found for the given CDR ID.'], 404);
            }

            $url = $this->ringLogixService->getRecordingUrl($origCallId);

            if (!$url) {
                return response()->json(['status' => 0, 'error_msg' => 'Recording is not available for this call.'], 404);
            }

            return response()->json(['status' => 1, 'url' => $url], 200);
        } catch (\Throwable $e) {
            return response()->json(['status' => 0, 'error_msg' => $e->getMessage() ?: 'Something went wrong while fetching the recording. Please try again.'], 500);
        }
    }

    protected function getCallDetailData(Request $request, $patientId = null): array
    {
        $patient = null;
        if ($patientId) {
            $patient = $this->patientService->getPatientId($patientId);
            abort_if(!$patient, 404);
        }

        $validator = Validator::make($request->all(), [
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'phone'       => 'nullable|string|max:20',
            'type'        => 'nullable|integer|in:0,1,2',
            'extension'   => 'nullable|string|max:20',
            'caller_name' => 'nullable|string|max:100',
        ]);

        $startDate   = Utility::convertYMD('yesterday') . ' 00:00:00';
        $endDate     = Utility::convertYMD('today') . ' 23:59:59';
        $phoneFilter = $patient
            ? array_values(array_filter([$patient->mobile, $patient->phone], fn($p) => !empty(trim((string) $p))))
            : array_filter([$request->input('phone')], fn($p) => !empty(trim((string) $p)));
        $callDetails  = [];
        $errorMessage = null;

        if (!$validator->fails()) {
            $startDate = $request->input('start_date')
                ? Utility::convertYMDTime($request->input('start_date'))
                : $startDate;

            $endDate = $request->input('end_date')
                ? Utility::convertYMD($request->input('end_date')) . ' 23:59:59'
                : $endDate;

            $typeFilter      = $request->input('type', '');
            $extensionFilter = trim($request->input('extension', ''));
            $callerNameFilter = trim($request->input('caller_name', ''));

            try {
                $callDetails = $this->ringLogixService->getCallDetails([
                    'domain'     => env('RINGLOGIX_DOMAIN'),
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start'      => 0,
                    'limit'      => 500,
                    'max_pages'  => 50,
                    'phone'      => $phoneFilter,
                ]);

                if ($typeFilter !== '' && $typeFilter !== null) {
                    $callDetails = array_values(array_filter($callDetails, fn($c) => (string)($c['type'] ?? '') === (string)$typeFilter));
                }

                if ($extensionFilter !== '') {
                    $callDetails = array_values(array_filter($callDetails, function ($c) use ($extensionFilter) {
                        $ext = (string)($c['orig_sub'] ?? $c['by_sub'] ?? $c['CdrR']['orig_sub'] ?? '');
                        return stripos($ext, $extensionFilter) !== false;
                    }));
                }

                if ($callerNameFilter !== '') {
                    $callDetails = array_values(array_filter($callDetails, function ($c) use ($callerNameFilter) {
                        $name = (string)($c['orig_from_name'] ?? $c['CdrR']['orig_from_name'] ?? '');
                        return stripos($name, $callerNameFilter) !== false;
                    }));
                }
            } catch (\Throwable $e) {
                $errorMessage = $e->getMessage();
            }
        }

        return [
            'patient'      => $patient,
            'callDetails'  => $callDetails,
            'errorMessage' => $errorMessage,
            'filters'      => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'phone'      => $phoneFilter ?? '',
            ],
            'validator' => $validator,
        ];
    }

    protected function formatAjaxResponseData(array $data): array
    {
        $patient     = $data['patient'];
        $callDetails = [];

        foreach ($data['callDetails'] as $call) {
            $cdrR      = $call['CdrR'] ?? [];
            $duration  = (int) ($call['duration'] ?? 0);
            $talkTime  = (int) ($call['time_talking'] ?? 0);
            $callerNum = isset($call['orig_from_uri'])
                            ? preg_replace('/^sip:|@.*$/i', '', $call['orig_from_uri'])
                            : '-';

            $callDetails[] = [
                'time_start'    => isset($call['time_start']) ? Utility::unixTimestampToMDYTime($call['time_start']) : '-',
                'type'          => $call['type'] ?? null,
                'type_label'    => RingLogixHelper::callTypeLabel($call['type'] ?? null),
                'caller_name'   => $call['orig_from_name'] ?? $cdrR['orig_from_name'] ?? '-',
                'caller_number' => $callerNum,
                'dialed_number' => $call['orig_req_user'] ?? $call['orig_to_user'] ?? '-',
                'extension'     => $call['orig_sub'] ?? $call['by_sub'] ?? $cdrR['orig_sub'] ?? '-',
                'duration'      => $duration,
                'duration_fmt'  => $duration > 0 ? \sprintf('%d:%02d', floor($duration / 60), $duration % 60) : '0:00',
                'talk_time'     => $talkTime,
                'talk_time_fmt' => $talkTime > 0 ? \sprintf('%d:%02d', floor($talkTime / 60), $talkTime % 60) : '0:00',
                'by_action'       => $cdrR['by_action'] ?? '-',
                'release_text'    => $cdrR['release_text'] ?? '-',
                'codec'           => $cdrR['codec'] ?? '-',
                'cdr_id'          => $call['cdr_id'] ?? $cdrR['id'] ?? null,
                'time_start_unix' => $call['time_start'] ?? null,
            ];
        }

        return [
            'patient' => [
                'id'          => optional($patient)->id,
                'first_name'  => optional($patient)->first_name,
                'middle_name' => optional($patient)->middle_name,
                'last_name'   => optional($patient)->last_name,
                'full_name'   => $patient ? trim($patient->first_name . ' ' . $patient->middle_name . ' ' . $patient->last_name) : '-',
                'mobile'      => optional($patient)->mobile ?: '-',
                'phone'       => optional($patient)->phone ?: '-',
            ],
            'filters'       => $data['filters'],
            'call_details'  => $callDetails,
            'total_records' => count($callDetails),
            'error_message' => $data['errorMessage'],
        ];
    }
}
