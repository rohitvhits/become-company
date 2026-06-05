<?php

namespace App\Services;

use App\Model\ResolutionSmsTemplate;
use App\Helpers\ResolutionSmsHelper;

class ResolutionSmsTemplateService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send Resolution SMS to patient.
     *
     * @param int $patientId
     * @param string $status
     * @return mixed
     */
    public function sendResolutionSms($patientId, $status)
    {
        $smsData = ResolutionSmsHelper::statusWiseSmsSend($status, $patientId);

        if (empty($smsData['message'])) {
            return false;
        }

        $numbers = array_unique(array_filter([$smsData['mobile'], $smsData['phone']]));
        $sent = false;
        foreach ($numbers as $number) {
            $result = $this->smsService->AgencyWiseSmsDynamic($patientId, $number, $smsData['message']);
            if ($result) {
                $sent = true;
            }
        }

        return $sent;
    }

    /**
     * Get resolved message for preview.
     *
     * @param int $patientId
     * @param string $status
     * @return string|false
     */
    public function getResolvedMessage($patientId, $status)
    {
        $smsData = ResolutionSmsHelper::statusWiseSmsSend($status, $patientId);
        return $smsData['message'] ?? false;
    }

    /**
     * Get all resolution SMS templates.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ResolutionSmsTemplate::where('del_flag', 'N')->orderBy('id')->get();
    }

    /**
     * Get a template by ID.
     *
     * @param int $id
     * @return ResolutionSmsTemplate
     */
    public function getById($id)
    {
        return ResolutionSmsTemplate::where('del_flag', 'N')->findOrFail($id);
    }

    /**
     * Update a template.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
    $template = $this->getById($id);
        return $template->update($data);
    }

    /**
     * Handle bulk update of templates.
     *
     * @param array $templatesData Array of id => message
     * @return void
     */
    public function bulkUpdate(array $templatesData)
    {
        foreach ($templatesData as $id => $message) {
            ResolutionSmsTemplate::where('id', $id)->update(['message' => $message,'updated_at' => date('Y-m-d H:i:s'),'updated_by' => auth()->id()]);
        }
    }
}
