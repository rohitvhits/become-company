<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Model\DocumentPatient;
use App\Model\Patient;

class DocumentMedCountCronController extends Controller
{
    public function updateOldDocumentCounts()
    {
        $batchSize = 500;
        $offset = 0;
        while (true) {
            // Get a batch of documents grouped by patient
            $getExistingPatient = Patient::select('id')->where('deleted_flag',"N")->where('medication_count','!=',0)->pluck('id');
            $records = DocumentPatient::select('patient_id',DB::raw('SUM(medication_list) as total_medication'))
                ->groupBy('patient_id')
                ->where('medication_list',1)
                ->where('deleted_flag','N')
                ->whereNotIn('patient_id',$getExistingPatient)
                ->limit($batchSize)
                ->offset($offset)
                ->get();
            // If no records found, break loop
            if ($records->isEmpty()) {
                break;
            }

            foreach ($records as $row) {
               Patient::where('id', $row->patient_id)
                    ->update([
                        'medication_count' => $row->total_medication,
                    ]);
            }
            // Move to next batch
            $offset += $batchSize;
        }
        return "Document count update completed successfully.";
    }
}
