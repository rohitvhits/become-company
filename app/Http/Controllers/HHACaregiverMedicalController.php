<?php

namespace App\Http\Controllers;

use URL;
use App\Agency;
use App\Helpers\HHAAppointmentHelper;
use Illuminate\Http\Request;
use App\Helpers\HHACaregiversHelper;

use Illuminate\Support\Facades\Request as Input;;

use Illuminate\Routing\Controller as BaseController;
use App\Services\HHACaregiverMedicalService;

class HHACaregiverMedicalController extends BaseController
{ 

    public function __construct(HHACaregiverMedicalService $hhaCaregiverMedical)
    {
        $this->hhaCaregiverMedical = $hhaCaregiverMedical;
    }
   
    public function ajaxList(Request $request){
        $query= HHACaregiversHelper::getCaregiverDetailsByAgencyId($request->id,$request->agency_fk);
        $responseData = [];
        if(isset($query->id)){

            $responseData = HHACaregiversHelper::getCaregiverMedicalDetails($query, $query->caregiver_id,$request->status);
        }
       
        if(!empty($responseData[0])){
            $response = [
                'message' =>"success",
                'status' => 1,
                'data'    => $responseData,
            ];
            return response()->json($response, 200);
        }
        // $query =HHAAppointmentHelper::getByIdNewId($request->id);
        // echo "<pre>";print_r($query);die();
        // if(isset($query->id)){
        //     $response = $this->hhaCaregiverMedical->getCaregiverComplianceList($query->caregiver_id,$request->status);
        //     if(!empty($response[0])){
        //         $response = [
        //             'message' =>"success",
        //             'status' => 1,
        //             'data'    => $response,
        //         ];
        //         return response()->json($response, 200);
        //     }
        // }

        $response = [
            'message' =>"No record available",
            'status' => 1,
            'data'    => "",
        ];
        return response()->json($response, 200);
    }
    
}
