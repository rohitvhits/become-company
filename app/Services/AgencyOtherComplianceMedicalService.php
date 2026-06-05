<?php

namespace App\Services;

use App\Helpers\HHACaregiversHelper;
use App\Model\AgencyOtherComplianceMedical;
use App\Model\HHACaregivers;

class AgencyOtherComplianceMedicalService
{
    public function getByAgencyId($agencyId)
    {
        return AgencyOtherComplianceMedical::where('agency_id',$agencyId)->where('del_flag', 'N')
            ->orderBy('id', 'asc')->get();
    }

    public function getHHAOptions($agencyId)
    {
        $caregiver = HHACaregivers::select('officeId')
            ->where('agency_fk', $agencyId)
            ->where('hha_delete_flag', 'N')
            ->first();
        $officeId = $caregiver->officeId ?? 0;

        $complianceTypes = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId, $officeId);
        $options = [];
        if (!empty($complianceTypes)) {
            foreach ($complianceTypes as $item) {
                $options[] = ['id' => $item['id'], 'name' => $item['name']];
            }
        }
        return $options;
    }

    public function getMedicalResults($agencyId, $medicaidId, $officeId)
    {
        return HHACaregiversHelper::getCaregiverOtherComplienceMedicalResults($agencyId, $medicaidId, $officeId) ?? [];
    }

    /**
     * Returns everything needed for the edit form in one call:
     * - existing selected medicals from agency_other_compliance_medicals (DB)
     * - available medical options from HHA API
     * - medical result options from HHA API for the first selected medical
     * - currently saved medical_result_id
     */
    public function getEditData($agency)
    {
        $agencyId  = $agency->id;
        $existing  = $this->getByAgencyId($agencyId);
        $options   = $this->getHHAOptions($agencyId);
        $officeId  = $agency->office_id ?? 0;

        // Pre-load results for the first currently-selected medical
        $firstMedicalId = $existing->first()->medical_id ?? null;
        $results = $firstMedicalId
            ? $this->getMedicalResults($agencyId, $firstMedicalId, $officeId)
            : [];

        return [
            'existing'           => $existing,
            'options'            => $options,
            'results'            => $results,            
        ];
    }

    public function bulkSave($agencyId, array $medicals,$resultId,$resultName)
    {
        AgencyOtherComplianceMedical::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->update(['del_flag' => 'Y']);

        foreach ($medicals as $item) {
            if (empty($item['medical_id'])) continue;
            AgencyOtherComplianceMedical::create([
                'agency_id'    => $agencyId,
                'medical_id'   => $item['medical_id'],
                'medical_name' => $item['medical_name'] ?? '',
                'medical_result_id' => $resultId,
                'medical_result_name'=> $resultName,
                'del_flag'     => 'N',
            ]);
        }

        return $this->getByAgencyId($agencyId);
    }

    public function add($agencyId, $medicalId, $medicalName)
    {
        $exists = AgencyOtherComplianceMedical::where('agency_id', $agencyId)
            ->where('medical_id', $medicalId)
            ->where('del_flag', 'N')
            ->exists();

        if ($exists) {
            return ['duplicate' => true, 'data' => null];
        }

        AgencyOtherComplianceMedical::create([
            'agency_id'    => $agencyId,
            'medical_id'   => $medicalId,
            'medical_name' => $medicalName,
            'del_flag'     => 'N',
        ]);

        return ['duplicate' => false, 'data' => $this->getByAgencyId($agencyId)];
    }

    public function delete($recordId)
    {
        $record = AgencyOtherComplianceMedical::find($recordId);
        if (!$record) {
            return false;
        }
        $record->del_flag = 'Y';
        $record->save();
        return true;
    }

    public function saveBoth($agency, array $medicals, $resultId, $resultName)
    {
        $saved = $this->bulkSave($agency->id, $medicals,$resultId,$resultName);
        return $saved;
    }
}
