<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use App\Services\LogsService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\ResolutionSmsTemplateService;
use Illuminate\Support\Facades\Validator;

class ResolutionSmsTemplateController extends BaseController
{
    protected $service;

    public function __construct(ResolutionSmsTemplateService $service)
    {
        $this->middleware('permission:resolution-sms-template', ['only' => ['index', 'getById', 'update', 'bulkUpdate', 'sendSms', 'resolveMessage']]);
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index()
    {
        $data['menu'] = 'setting';
        $data['templates'] = $this->service->getAll();
        return view('resolution_sms_template.index', $data);
    }

    public function getById(Request $request)
    {
        $template = $this->service->getById($request->id);
        return response()->json(['success' => true, 'data' => $template]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'      => 'required|integer',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error_msg' => $validator->errors()->first()], 422);
        }

        $this->service->update($request->id, ['message' => $request->message]);

        $user = auth()->user();
        $logData = [
            'type'         => 'Update',
            'link'         => url('resolution-sms-template/update'),
            'module'       => 'Resolution SMS Template',
            'object_id'    => $request->id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' updated Resolution SMS template.',
            'new_response' => serialize(['id' => $request->id, 'message' => $request->message]),
            'ip'           => Utility::getIP(),
        ];
        LogsService::save($logData);

        return response()->json(['success' => true, 'error_msg' => 'SMS template updated successfully.']);
    }

    public function bulkUpdate(Request $request)
    {
        $templates = $request->input('templates', []);
        $this->service->bulkUpdate($templates);

        $user = auth()->user();
        $logData = [
            'type'         => 'Bulk Update',
            'link'         => url('resolution-sms-template/bulk-update'),
            'module'       => 'Resolution SMS Template',
            'object_id'    => null,
            'message'      => $user->first_name . ' ' . $user->last_name . ' bulk updated Resolution SMS templates.',
            'new_response' => serialize(['templates' => $templates]),
            'ip'           => Utility::getIP(),
        ];
        LogsService::save($logData);

        return response()->json(['success' => true, 'error_msg' => 'All SMS templates updated successfully.']);
    }

    public function sendSms(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'status'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error_msg' => $validator->errors()->first()], 422);
        }

        $res = $this->service->sendResolutionSms($request->patient_id, $request->status);

        if ($res) {
            $message = $user->first_name . ' ' . $user->last_name . ' Resolution SMS sent successfully.';
            $logData = [
                'type' => 'Send SMS',
                'link' => url('patient-resolution-sms/send'),
                'module' => 'Patient Appointment',
                'object_id' => $request->patient_id,
                'message' => $message,
                'new_response' => serialize([
                    'patient_id' => $request->patient_id,
                    'status' => $request->status,
                    'sms_status' => $res ? 'Sent' : 'Failed'
                ]),
                'ip' => Utility::getIP(),
            ];

            LogsService::save($logData);
            return response()->json(['success' => true, 'error_msg' => 'SMS sent successfully.']);
        }

        return response()->json(['success' => false, 'error_msg' => 'Failed to send SMS. Template might be missing or patient mobile number is not available.'], 400);
    }

    public function resolveMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'status'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error_msg' => $validator->errors()->first()], 422);
        }

        $message = $this->service->getResolvedMessage($request->patient_id, $request->status);

        if ($message) {
            return response()->json(['success' => true, 'error_msg' => $message]);
        }

        return response()->json(['success' => false, 'error_msg' => 'Template not found for selected status.'], 404);
    }
}
