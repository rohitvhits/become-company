<?php

namespace App\Helpers;

use App\Model\Patient;

class DocumentHelper
{
	public function __construct() {}

	public static function updatePatientDocumentCounts($patient_id, $med_flag, $ins_flag, $old_med, $old_ins,$mdo_flag,$old_mdo)
    {
        $patient = Patient::find($patient_id);
        if (!$patient) return false;

        // Calculate changes
        $medChange = $med_flag - $old_med;   // gives +1, -1, or 0
        $insChange = $ins_flag - $old_ins;   // gives +1, -1, or 0
        $mdoChange = $mdo_flag - $old_mdo;   // gives +1, -1, or 0

        // Update patient counts
        $patient->medication_count = max(0, ($patient->medication_count ?? 0) + $medChange);
        $patient->insurance_elg_count = max(0, ($patient->insurance_elg_count ?? 0) + $insChange);
        $patient->mdo_tag_count = max(0, ($patient->mdo_tag_count ?? 0) + $mdoChange);

        $patient->save();

        return true;
    }


}
