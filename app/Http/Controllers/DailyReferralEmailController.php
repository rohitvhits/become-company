<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DailyReferralEmailReportService;
use App\Model\DailyReferralEmailLog;
use App\Model\DailyReferralEmailSchedule;
use App\Agency;
use App\Master;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Cache;
use App\User;
use App\Model\BranchList;
use App\Services\BranchListService;

class DailyReferralEmailController extends Controller
{
    protected $reportService;
    protected $branchListService;

    public function __construct(DailyReferralEmailReportService $reportService, BranchListService $branchListService)
    {
        $this->middleware('auth');
        $this->middleware('permission:detailed-portal-charts-report', ['only' => ['index', 'sendEmail', 'preview']]);
        $this->reportService = $reportService;
        $this->branchListService = $branchListService;
    }

    /**
     * Show the daily referral email automation page
     */
    public function index()
    {
        $data['menu'] = 'daily-referral-email';
        $data['user'] = auth()->user();
        $data['recent_emails'] = $recent_emails = DailyReferralEmailLog::with('createdBy')
            ->orderBy('created_date', 'desc')
            ->limit(10)
            ->get();

        $data['sent_count'] = DailyReferralEmailLog::whereDate('created_date', date('Y-m-d'))
            ->where('status', 'sent')
            ->count();

        $data['failed_count'] = DailyReferralEmailLog::whereDate('created_date', date('Y-m-d'))
            ->where('status', 'failed')
            ->count();

        // Get agencies for filter dropdown
        $data['agencies'] = Agency::getAllAgencyList();

        // Get services for filter dropdown
        $data['services'] = Master::getServiceRequestWithDisabled();

        // Get users for assigned to filter dropdown
        $data['userList'] = Cache::get('patient_master_nubest_user', function () {
            return User::getNYBestUserData();
        }, 10 * 60);

        // Get branches for filter dropdown
        $data['branches'] = $this->branchListService->getAllBranches();
        return view('dailyReferralEmail.index', $data);
    }

    /**
     * Preview the email report
     */
    public function preview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'report_date' => 'required',
                'agency_ids' => 'nullable|array',
                'agency_ids.*' => 'integer|exists:agency,id',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'integer|exists:master_table,id',
                'assigned_to' => 'nullable|array',
                'assigned_to.*' => 'integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters provided',
                    'errors' => $validator->errors()
                ], 400);
            }

            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');

            $date = explode('-', $request->report_date);
            if (count($date) > 0) {
                $startDate = Utility::convertYMD(trim($date[0])) ?? '';
                $endDate = Utility::convertYMD(trim($date[1])) ?? '';
            }

            // Get filter parameters
            $agencyIds = $request->agency_ids ?? [];
            $serviceIds = $request->service_ids ?? [];
            $assignedTo = $request->assigned_to ?? [];
            $medicationList = $request->medication_list ?? '';
            $insuranceElg = $request->insurance_elg ?? '';
            $mdoTag = $request->mdo_tag ?? '';
            $branchIds = $request->branch_ids ?? [];

            // Generate report data with filters
            $reportData = $this->reportService->generateReportData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);
            $formattedData = $this->reportService->formatReportForEmail($reportData, $request->report_date);

            // Add filter information to email data
            $formattedData['filters'] = [
                'agencies' => !empty($agencyIds) ? Agency::whereIn('id', $agencyIds)->where('delete_flag', 'N')->pluck('agency_name')->toArray() : [],
                'services' => !empty($serviceIds) ? Master::whereIn('id', $serviceIds)->where('del_flag', 'N')->pluck('name')->toArray() : [],
                'assigned_to' => !empty($assignedTo) ? User::whereIn('id', $assignedTo)->pluck('name')->toArray() : [],
                'medication_list' => $medicationList,
                'insurance_elg' => $insuranceElg,
                'mdo_tag' => $mdoTag,
                'branches' => !empty($branchIds) ? BranchList::whereIn('id', $branchIds)->where('del_flag', 'N')->pluck('branch_name')->toArray() : []
            ];

            // Add section visibility flags
            $formattedData['show_forms_breakdown'] = $request->boolean('show_forms_breakdown', true);
            $formattedData['show_referral_sources'] = $request->boolean('show_referral_sources', true);
            $formattedData['show_resolution'] = $request->boolean('show_resolution', true);
            $formattedData['show_requests_per_agency'] = $request->boolean('show_requests_per_agency', true);
            $formattedData['show_portal_processing'] = $request->boolean('show_portal_processing', true);
            $formattedData['show_outliers'] = $request->boolean('show_outliers', true);
            $formattedData['show_highest_weight'] = $request->boolean('show_highest_weight', true);
            $formattedData['show_refusals_insights'] = $request->boolean('show_refusals_insights', true);
            $formattedData['show_cancellations_insights'] = $request->boolean('show_cancellations_insights', true);
            $formattedData['show_non_mdo_forms'] = $request->boolean('show_non_mdo_forms', true);
            $formattedData['show_mdo_completed'] = $request->boolean('show_mdo_completed', true);
            $formattedData['show_updates_per_agency'] = $request->boolean('show_updates_per_agency', true);

            // Render the email template
            $emailContent = view('emails.daily_referral_report', $formattedData)->render();

            return response()->json([
                'success' => true,
                'email_content' => $emailContent,
                'report_data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send the daily referral email
     */
    public function sendEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'report_date' => 'required',
                'recipients' => 'required|array|min:1',
                'recipients.*' => 'email',
                'subject' => 'required|string|max:255',
                'cc_emails' => 'nullable|array',
                'cc_emails.*' => 'email',
                'agency_ids' => 'nullable|array',
                'agency_ids.*' => 'integer|exists:agency,id',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'integer|exists:master_table,id',
                'assigned_to' => 'nullable|array',
                'show_forms_breakdown' => 'nullable',
                'show_referral_sources' => 'nullable',
                'show_resolution' => 'nullable',
                'show_requests_per_agency' => 'nullable',
                'show_portal_processing' => 'nullable',
                'show_outliers' => 'nullable',
                'show_highest_weight' => 'nullable',
                'show_refusals_insights' => 'nullable',
                'show_cancellations_insights' => 'nullable',
                'show_non_mdo_forms' => 'nullable',
                'show_mdo_completed' => 'nullable',
                'show_updates_per_agency' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');

            $date = explode('-', $request->report_date);
            if (count($date) > 0) {
                $startDate = Utility::convertYMD(trim($date[0])) ?? '';
                $endDate = Utility::convertYMD(trim($date[1])) ?? '';
            }

            // Get filter parameters
            $agencyIds = $request->agency_ids ?? [];
            $serviceIds = $request->service_ids ?? [];
            $assignedTo = $request->assigned_to ?? [];
            $medicationList = $request->medication_list ?? '';
            $insuranceElg = $request->insurance_elg ?? '';
            $mdoTag = $request->mdo_tag ?? '';
            $branchIds = $request->branch_ids ?? [];

            // Generate report data with filters
            $reportData = $this->reportService->generateReportData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);
            $formattedData = $this->reportService->formatReportForEmail($reportData, $request->report_date);

            // Add filter information to email data
            $formattedData['filters'] = [
                'agencies' => !empty($agencyIds) ? Agency::whereIn('id', $agencyIds)->where('delete_flag', 'N')->pluck('agency_name')->toArray() : [],
                'services' => !empty($serviceIds) ? Master::whereIn('id', $serviceIds)->where('del_flag', 'N')->pluck('name')->toArray() : [],
                'assigned_to' => !empty($assignedTo) ? User::whereIn('id', $assignedTo)->pluck('name')->toArray() : [],
                'medication_list' => $medicationList,
                'insurance_elg' => $insuranceElg,
                'mdo_tag' => $mdoTag,
                'branches' => !empty($branchIds) ? $this->branchListService->getBranchNameById($branchIds) : []
            ];

            // Add section visibility flags
            $sectionToggles = [
                'show_forms_breakdown' => $request->boolean('show_forms_breakdown', true),
                'show_referral_sources' => $request->boolean('show_referral_sources', true),
                'show_resolution' => $request->boolean('show_resolution', true),
                'show_requests_per_agency' => $request->boolean('show_requests_per_agency', true),
                'show_portal_processing' => $request->boolean('show_portal_processing', true),
                'show_outliers' => $request->boolean('show_outliers', true),
                'show_highest_weight' => $request->boolean('show_highest_weight', true),
                'show_refusals_insights' => $request->boolean('show_refusals_insights', true),
                'show_cancellations_insights' => $request->boolean('show_cancellations_insights', true),
                'show_non_mdo_forms' => $request->boolean('show_non_mdo_forms', true),
                'show_mdo_completed' => $request->boolean('show_mdo_completed', true),
                'show_updates_per_agency' => $request->boolean('show_updates_per_agency', true),
            ];

            $formattedData = array_merge($formattedData, $sectionToggles);

            // Render email content
            $emailContent = view('emails.daily_referral_report', $formattedData)->render();

            // Prepare recipients
            $recipients = $request->recipients;
            $ccEmails = $request->cc_emails ?? [];

            // Always include default emails in CC
            $defaultCcEmails = ['pinak@nybestmedical.com', 'developer@nybestmedical.com', 'marina@nybestmedical.com'];
            foreach ($defaultCcEmails as $defaultEmail) {
                if (!in_array($defaultEmail, $ccEmails)) {
                    $ccEmails[] = $defaultEmail;
                }
            }

            // Create email log entry
            $emailLog = DailyReferralEmailLog::create([
                'report_date' => $request->report_date,
                'email_subject' => $request->subject,
                'email_recipients' => array_merge($recipients, $ccEmails),
                'report_data' => array_merge($reportData, [
                    'filters' => array_merge([
                        'agency_ids' => $agencyIds,
                        'service_ids' => $serviceIds,
                        'assigned_to' => $assignedTo,
                        'medication_list' => $medicationList,
                        'insurance_elg' => $insuranceElg,
                        'mdo_tag' => $mdoTag,
                        'branch_ids' => $branchIds,
                    ], $sectionToggles)
                ]),
                'email_content' => $emailContent,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'created_date' => now()
            ]);

            // Send email
            try {
                Mail::send([], [], function ($message) use ($recipients, $ccEmails, $request, $emailContent) {
                    $message->to($recipients)
                        ->cc($ccEmails)
                        ->subject($request->subject)
                        ->html($emailContent);
                });

                // Update log as sent
                $emailLog->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully!',
                    'email_log_id' => $emailLog->id
                ]);
            } catch (\Exception $mailException) {
                // Update log as failed
                $emailLog->update([
                    'status' => 'failed',
                    'error_message' => $mailException->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email: ' . $mailException->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get email history
     */
    public function history(Request $request)
    {
        try {
            $startDate = $endDate = "";
            if ($request->start_date != "") {
                $date = explode('-', $request->start_date);
                if (count($date) > 0) {
                    $startDate = Utility::convertYMD(trim($date[0])) ?? '';
                    $endDate = Utility::convertYMD(trim($date[1])) ?? '';
                }
            }


            $query = DailyReferralEmailLog::with('createdBy')
                ->orderBy('created_date', 'desc');

            if ($startDate && $endDate) {
                $query->whereBetween('created_date', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $emailLogs = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $emailLogs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View specific email log
     */
    public function viewEmailLog($id)
    {
        try {
            $emailLog = DailyReferralEmailLog::with('createdBy')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $emailLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email log not found'
            ], 404);
        }
    }

    /**
     * Resend email from log
     */
    public function resendEmail($id)
    {
        try {
            $emailLog = DailyReferralEmailLog::findOrFail($id);

            // Send email using stored content
            Mail::send([], [], function ($message) use ($emailLog) {
                $message->to($emailLog->email_recipients)
                    ->subject($emailLog->email_subject)
                    ->html($emailLog->email_content);
            });

            // Create new log entry for resend
            $newLog = DailyReferralEmailLog::create([
                'report_date' => $emailLog->report_date,
                'email_subject' => $emailLog->email_subject,
                'email_recipients' => $emailLog->email_recipients,
                'report_data' => $emailLog->report_data,
                'email_content' => $emailLog->email_content,
                'status' => 'sent',
                'sent_at' => now(),
                'created_by' => auth()->id(),
                'created_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email resent successfully!',
                'email_log_id' => $newLog->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update email schedule
     */
    public function scheduleDaily(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'recipients' => 'required|array|min:1',
                'recipients.*' => 'email',
                'email_subject' => 'required|string|max:255',
                'send_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'], // Format: HH:MM
                'frequency' => 'required|in:daily,weekly,monthly',
                'send_days' => 'required_if:frequency,daily|array',
                'send_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'period_days' => 'nullable|integer|min:1|max:365',
                'weekly_day' => 'required_if:frequency,weekly|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'monthly_date' => 'required_if:frequency,monthly|integer|min:-1|max:31',
                'cc_emails' => 'nullable|array',
                'cc_emails.*' => 'email',
                'start_date' => 'nullable|date|after_or_equal:today',
                'end_date' => 'nullable|date|after:start_date',
                'timezone_offset' => 'nullable|integer|between:-720,720',
                'notes' => 'nullable|string|max:1000',
                'agency_ids' => 'nullable|array',
                'agency_ids.*' => 'integer|exists:agency,id',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'integer|exists:master_table,id',
                'assigned_to' => 'nullable|array',
                'medication_list' => 'nullable|string|in:Yes,No',
                'insurance_elg' => 'nullable|string|in:Yes,No',
                'mdo_tag' => 'nullable|string|in:Yes,No',
                'branch_ids' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $schedule = DailyReferralEmailSchedule::create([
                'name' => $request->name ?: 'Daily Referral Report Schedule',
                'recipients' => $request->recipients,
                'cc_emails' => $request->cc_emails ?? [],
                'email_subject' => $request->email_subject,
                'send_time' => $request->send_time,
                'frequency' => $request->frequency,
                'send_days' => $request->frequency === 'daily' ? $request->send_days : null,
                'period_days' => $request->period_days,
                'weekly_day' => $request->frequency === 'weekly' ? $request->weekly_day : null,
                'monthly_date' => $request->frequency === 'monthly' ? $request->monthly_date : null,
                'start_date' => empty($request->start_date) ? null : $request->start_date,
                'end_date' => empty($request->end_date) ? null : $request->end_date,
                'timezone_offset' => $request->timezone_offset ?? 0,
                'notes' => $request->notes,
                'agency_ids' => $request->agency_ids ?? [],
                'service_ids' => $request->service_ids ?? [],
                'assigned_to' => $request->assigned_to ?? [],
                'medication_list' => $request->medication_list ?? null,
                'insurance_elg' => $request->insurance_elg ?? null,
                'mdo_tag' => $request->mdo_tag ?? null,
                'branch_ids' => $request->branch_ids ?? [],
                'show_forms_breakdown' => $request->boolean('show_forms_breakdown', true),
                'show_referral_sources' => $request->boolean('show_referral_sources', true),
                'show_resolution' => $request->boolean('show_resolution', true),
                'show_requests_per_agency' => $request->boolean('show_requests_per_agency', true),
                'show_portal_processing' => $request->boolean('show_portal_processing', true),
                'show_outliers' => $request->boolean('show_outliers', true),
                'show_highest_weight' => $request->boolean('show_highest_weight', true),
                'show_refusals_insights' => $request->boolean('show_refusals_insights', true),
                'show_cancellations_insights' => $request->boolean('show_cancellations_insights', true),
                'show_non_mdo_forms' => $request->boolean('show_non_mdo_forms', true),
                'show_mdo_completed' => $request->boolean('show_mdo_completed', true),
                'show_updates_per_agency' => $request->boolean('show_updates_per_agency', true),
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email schedule created successfully!',
                'schedule_id' => $schedule->id,
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all email schedules
     */
    public function getSchedules(Request $request)
    {
        try {
            $query = DailyReferralEmailSchedule::with('createdBy')
                ->orderBy('created_at', 'desc');

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $schedules = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schedules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific schedule
     */
    public function getSchedule($id)
    {
        try {
            $schedule = DailyReferralEmailSchedule::with('createdBy')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }
    }

    /**
     * Update email schedule
     */
    public function updateSchedule(Request $request, $id)
    {
        try {
            $schedule = DailyReferralEmailSchedule::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'recipients' => 'sometimes|required|array|min:1',
                'recipients.*' => 'email',
                'email_subject' => 'sometimes|required|string|max:255',
                'send_time' => 'sometimes|required|string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',
                'frequency' => 'sometimes|required|in:daily,weekly,monthly',
                'send_days' => 'nullable|array',
                'send_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'period_days' => 'nullable|integer|min:1|max:365',
                'weekly_day' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'monthly_date' => 'nullable|integer|min:-1|max:31',
                'cc_emails' => 'nullable|array',
                'cc_emails.*' => 'email',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'timezone_offset' => 'nullable|integer|between:-720,720',
                'notes' => 'nullable|string|max:1000',
                'agency_ids' => 'nullable|array',
                'agency_ids.*' => 'integer|exists:agency,id',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'integer|exists:master_table,id',
                'assigned_to' => 'nullable|array',
                'medication_list' => 'nullable|string|in:Yes,No',
                'insurance_elg' => 'nullable|string|in:Yes,No',
                'mdo_tag' => 'nullable|string|in:Yes,No',
                'branch_ids' => 'nullable|array',
                'show_forms_breakdown' => 'nullable|boolean',
                'show_referral_sources' => 'nullable|boolean',
                'show_resolution' => 'nullable|boolean',
                'show_requests_per_agency' => 'nullable|boolean',
                'show_portal_processing' => 'nullable|boolean',
                'show_outliers' => 'nullable|boolean',
                'show_highest_weight' => 'nullable|boolean',
                'show_refusals_insights' => 'nullable|boolean',
                'show_cancellations_insights' => 'nullable|boolean',
                'show_non_mdo_forms' => 'nullable|boolean',
                'show_mdo_completed' => 'nullable|boolean',
                'show_updates_per_agency' => 'nullable|boolean',
                'is_active' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $schedule->update($request->only([
                'name',
                'recipients',
                'cc_emails',
                'email_subject',
                'send_time',
                'frequency',
                'send_days',
                'period_days',
                'weekly_day',
                'monthly_date',
                'start_date',
                'end_date',
                'timezone_offset',
                'notes',
                'agency_ids',
                'service_ids',
                'assigned_to',
                'medication_list',
                'insurance_elg',
                'mdo_tag',
                'branch_ids',
                'show_forms_breakdown',
                'show_referral_sources',
                'show_resolution',
                'show_requests_per_agency',
                'show_portal_processing',
                'show_outliers',
                'show_highest_weight',
                'show_refusals_insights',
                'show_cancellations_insights',
                'show_non_mdo_forms',
                'show_mdo_completed',
                'show_updates_per_agency',
                'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully!',
                'schedule' => $schedule->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete email schedule
     */
    public function deleteSchedule($id)
    {
        try {
            $schedule = DailyReferralEmailSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle schedule active status
     */
    public function toggleSchedule($id)
    {
        try {
            $schedule = DailyReferralEmailSchedule::findOrFail($id);
            $schedule->update([
                'is_active' => !$schedule->is_active
            ]);

            $status = $schedule->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Schedule {$status} successfully!",
                'is_active' => $schedule->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test schedule (send immediately)
     */
    public function testSchedule($id)
    {
        try {
            $schedule = DailyReferralEmailSchedule::findOrFail($id);

            // Generate report date range based on frequency
            $dates = $this->getReportDatesForFrequency($schedule->frequency);
            $startDate = $dates['start'];
            $endDate = $dates['end'];
            $reportDateFormatted = $dates['formatted'];

            // Generate report data with filters
            $reportData = $this->reportService->generateReportData(
                $startDate,
                $endDate,
                $schedule->agency_ids ?? [],
                $schedule->service_ids ?? [],
                $schedule->assigned_to ?? [],
                $schedule->medication_list ?? '',
                $schedule->insurance_elg ?? '',
                $schedule->mdo_tag ?? '',
                $schedule->branch_ids ?? []
            );
            $formattedData = $this->reportService->formatReportForEmail($reportData, $reportDateFormatted);

            // Add filter information to email data
            $formattedData['filters'] = [
                'agencies' => !empty($schedule->agency_ids) ? Agency::whereIn('id', $schedule->agency_ids)->where('delete_flag', 'N')->pluck('agency_name')->toArray() : [],
                'services' => !empty($schedule->service_ids) ? Master::whereIn('id', $schedule->service_ids)->where('del_flag', 'N')->pluck('name')->toArray() : [],
                'assigned_to' => !empty($schedule->assigned_to) ? User::whereIn('id', $schedule->assigned_to)->pluck('name')->toArray() : [],
                'medication_list' => $schedule->medication_list ?? '',
                'insurance_elg' => $schedule->insurance_elg ?? '',
                'mdo_tag' => $schedule->mdo_tag ?? '',
                'branches' => !empty($schedule->branch_ids) ? $this->branchListService->getBranchNameById($schedule->branch_ids) : []
            ];

            // Add section visibility flags
            $sectionToggles = [
                'show_forms_breakdown' => $schedule->show_forms_breakdown ?? true,
                'show_referral_sources' => $schedule->show_referral_sources ?? true,
                'show_resolution' => $schedule->show_resolution ?? true,
                'show_requests_per_agency' => $schedule->show_requests_per_agency ?? true,
                'show_portal_processing' => $schedule->show_portal_processing ?? true,
                'show_outliers' => $schedule->show_outliers ?? true,
                'show_highest_weight' => $schedule->show_highest_weight ?? true,
                'show_refusals_insights' => $schedule->show_refusals_insights ?? true,
                'show_cancellations_insights' => $schedule->show_cancellations_insights ?? true,
                'show_non_mdo_forms' => $schedule->show_non_mdo_forms ?? true,
                'show_mdo_completed' => $schedule->show_mdo_completed ?? true,
                'show_updates_per_agency' => $schedule->show_updates_per_agency ?? true,
            ];

            $formattedData = array_merge($formattedData, $sectionToggles);

            // Render email content
            $emailContent = view('emails.daily_referral_report', $formattedData)->render();

            // Prepare recipients
            $recipients = $schedule->recipients;
            $ccEmails = $schedule->cc_emails ?? [];

            // Create email log entry
            $emailLog = DailyReferralEmailLog::create([
                'report_date' => $reportDateFormatted,
                'email_subject' => $schedule->email_subject,
                'email_recipients' => array_merge($recipients, $ccEmails),
                'report_data' => array_merge($reportData, [
                    'filters' => array_merge([
                        'agency_ids' => $schedule->agency_ids ?? [],
                        'service_ids' => $schedule->service_ids ?? [],
                        'assigned_to' => $schedule->assigned_to ?? [],
                        'medication_list' => $schedule->medication_list ?? '',
                        'insurance_elg' => $schedule->insurance_elg ?? '',
                        'mdo_tag' => $schedule->mdo_tag ?? '',
                        'branch_ids' => $schedule->branch_ids ?? [],
                    ], $sectionToggles)
                ]),
                'email_content' => $emailContent,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'created_date' => now()
            ]);

            // Send email
            Mail::send([], [], function ($message) use ($recipients, $ccEmails, $schedule, $emailContent) {
                $message->to($recipients)
                    ->cc($ccEmails)
                    ->subject($schedule->email_subject . ' (Test)')
                    ->html($emailContent);
            });

            // Update log as sent
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'email_log_id' => $emailLog->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report dates based on frequency
     */
    private function getReportDatesForFrequency($frequency)
    {
        switch ($frequency) {
            case 'weekly':
                $endDate = Carbon::yesterday();
                $startDate = $endDate->copy()->subDays(6); // Last 7 days
                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'formatted' => $startDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y')
                ];
            case 'monthly':
                $endDate = Carbon::yesterday();
                $startDate = $endDate->copy()->subDays(29); // Last 30 days
                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'formatted' => $startDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y')
                ];
            case 'daily':
            default:
                $reportDate = Carbon::yesterday();
                return [
                    'start' => $reportDate->format('Y-m-d'),
                    'end' => $reportDate->format('Y-m-d'),
                    'formatted' => $reportDate->format('m/d/Y')
                ];
        }
    }
}
