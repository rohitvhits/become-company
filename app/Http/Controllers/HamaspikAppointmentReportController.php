<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;
use App\Services\AgencyWiseServiceService;
use App\Services\DocumentUploadService;
use App\Services\DocumentPatientService;
use App\Services\PatientService;
use App\Master;

class HamaspikAppointmentReportController extends BaseController
{
    protected $patientService, $agencyWiseServiceService, $documentUploadService, $documentPatientService = "";

    public function __construct(AgencyWiseServiceService $agencyWiseServiceService, DocumentUploadService $documentUploadService, DocumentPatientService $documentPatientService, PatientService $patientService)
    {
        $this->agencyWiseServiceService = $agencyWiseServiceService;
        $this->documentUploadService = $documentUploadService;
        $this->documentPatientService = $documentPatientService;
        $this->patientService = $patientService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
      
        return view('serviceWiseReport.service_wise_appointment', $data);
    }

    public function ajaxList(Request $request)
    {
        $data['page'] = $request->page;
        $columnsServiceArray = [
            '187' => 'Annual Health Assessment',
            
            '849' => 'Flu Vaccine',
            '1074' => 'Flu Declination',
            '185' => 'QuantiFeron',
            '773' => 'MMR Vaccine for Exempt Follow Up',
            '-1' => 'MMR Exempt Letter',
            '1073' => 'Clearance to Work Completion',
            '1071' => 'Habituation Statement Completetion',
           '1072'=>'PPD QUESTIONARRE',
            '-4' => 'Corporate Compliance Training',
            '-5' => 'Sexual Harassment Training',
            '-6' => 'EVV Training',
            '-7' => 'TB Covid - CDPAP Training',
        ];

        $serviceNameArray = [];
        $servicesIds = [];
        if (!empty($columnsServiceArray)) {
            foreach ($columnsServiceArray as $key => $val) {
             
                if($key >0){
                   
                    $servicesIds[] = $key;
                }
                
            }
        }

        $getDocumentUploadIds = $this->documentUploadService->getUploadDocumentServicesWithNewOther($servicesIds,$request->created_date,$request->agency_id);
        $documentIds = [];
        $getDocuments = [];
   
        if(!empty($getDocumentUploadIds[0])){
            foreach($getDocumentUploadIds as $vl){
                $temp = [];
                $date ="";
                $getDocumentDetails = $this->documentPatientService->getDetailsById($vl->document_id);

                if(isset($getDocumentDetails->document_completed_date) && $getDocumentDetails->document_completed_date !=""){
                    $date =$getDocumentDetails->document_completed_date;
                }
                
                if(isset($temp[$vl->document_id])){
                    //$temp[$vl->document_id][$vl->service_id] = $vl->created_date;
                    $temp[$vl->document_id][$vl->service_id] = $date;
                   
                   
                }else{
                    $temp[$vl->document_id] = [];
                    $temp[$vl->document_id][$vl->service_id] = $date;
                   
                }
                
               
                
                if(!in_array($vl->document_id,$documentIds)){
                    $documentIds[] = $vl->document_id;
                }

                $getDocuments[$vl->document_id][]= $temp[$vl->document_id];
                
            }
        }
       
        $list = $this->documentPatientService->getDataForHamaspik($documentIds,$request->agency_id,$request->created_date);
        
        if(!empty($list[0])){
            foreach($list as $val){
                $temp = [];
                $temp = $val;

                foreach($columnsServiceArray as $key=>$vs){
                    
                    
                    if ($key <= 0) {
                        $temp[$key] ='';
                        if ($val->patientDetails->training_completed_date != "") {
                            $temp[$key] = date('m/d/Y', strtotime($val->patientDetails->training_completed_date));
                        } else {
                           
                            if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETED')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETED'), strtolower($val->patientDetails->training_status));
                                $temp[$key] = $explode[1] ?? "";
                            } else if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETE')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETE'), strtolower($val->patientDetails->training_status));
                                $temp[$key] = $explode[1] ?? "";
                            } else {
                                $temp[$key] = $val->patientDetails->training_status;
                            }
                        }
                    }else{
                        if(isset($getDocuments[$val->id])){
                            foreach($getDocuments[$val->id] as $ss){
                                if(isset($ss[$key])){
                                    $temp[$key] =$ss[$key];
                                    
                                }
                            }
                        
                        }
                       
                    }
                }

                $val = $temp;
            }
        }
        $data['list'] = $list;
        $data['master_list'] =$columnsServiceArray;
       
        return view('serviceWiseReport.service_wise_appointment_ajax', $data);
    }


    public function exportCsv(Request $request)
    {
        $columnsServiceArray = [
            '187' => 'Annual Health Assessment',
            '849' => 'Flu Vaccine',
            '1074' => 'Flu Declination',
            '185' => 'QuantiFeron',
            '773' => 'MMR Vaccine for Exempt Follow Up',
            '-1' => 'MMR Exempt Letter',
            '1073' => 'Clearance to Work Completion',
            '1071' => 'Habituation Statement Completetion',
            '1072'=>'PPD QUESTIONARRE',
            '-4' => 'Corporate Compliance Training',
            '-5' => 'Sexual Harassment Training',
            '-6' => 'EVV Training',
            '-7' => 'TB Covid - CDPAP Training',
     
        ];

        $serviceNameArray = [];
        $servicesIds = [];
        if (!empty($columnsServiceArray)) {
            foreach ($columnsServiceArray as $key => $val) {
             
                if($key >0){
                   
                    $servicesIds[] = $key;
                }
                
            }
        }

        $getDocumentUploadIds = $this->documentUploadService->getUploadDocumentServicesWithNewOther($servicesIds,$request->created_date,$request->agency_id);
        $documentIds = [];
        $getDocuments = [];
        if(!empty($getDocumentUploadIds[0])){
            foreach($getDocumentUploadIds as $vl){
                $temp = [];
                $getDocumentDetails = $this->documentPatientService->getDetailsById($vl->document_id);

                $date ="";
                if(isset($getDocumentDetails->document_completed_date) && $getDocumentDetails->document_completed_date !=""){
                    $date =$getDocumentDetails->document_completed_date;
                }

                if(isset($temp[$vl->document_id])){
                    //$temp[$vl->document_id][$vl->service_id] = $vl->created_date;

                    if($vl->service_id ==191){
                        $temp[$vl->document_id][$vl->service_id] = $temp[$vl->document_id][187]??"";
                    }else{
                        $temp[$vl->document_id][$vl->service_id] = $date;
                    }
                    
                }else{
                    $temp[$vl->document_id] = [];
                    if($vl->service_id ==191){
                        $temp[$vl->document_id][$vl->service_id] = $temp[$vl->document_id][187]??"";
                    }else{
                        $temp[$vl->document_id][$vl->service_id] = $date;
                    }
                    
                   // $temp[$vl->document_id][$vl->service_id] = $vl->created_date;
                }
                
                if(!in_array($vl->document_id,$documentIds)){
                    $documentIds[] = $vl->document_id;
                }

                $getDocuments[$vl->document_id][]= $temp[$vl->document_id];
                
            }
        }

        $list = $this->documentPatientService->getDataForHamaspik($documentIds,$request->agency_id,$request->created_date,'export');
       
        if(!empty($list[0])){
            foreach($list as $val){
                $temp = [];
                $temp = $val;
            
                foreach($columnsServiceArray as $key=>$vs){
                    
                    
                    if ($key <= 0) {
                        $temp[$key] ='';
                        if ($val->patientDetails->training_completed_date != "") {
                            $temp[$key] = date('m/d/Y', strtotime($val->patientDetails->training_completed_date));
                        } else {
                           
                            if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETED')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETED'), strtolower($val->patientDetails->training_status));
                                $temp[$key] = $explode[1] ?? "";
                            } else if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETE')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETE'), strtolower($val->patientDetails->training_status));
                                $temp[$key] = $explode[1] ?? "";
                            } else {
                                $temp[$key] = $val->patientDetails->training_status;
                            }
                        }
                    }else{
                        if(isset($getDocuments[$val->id])){
                            foreach($getDocuments[$val->id] as $ss){
                                if(isset($ss[$key])){
                                    $temp[$key] = $ss[$key];
                                }
                            }
                        
                        }
                        
                    }
                }

                $val = $temp;
            }
        }
       
        $finalResponse = $list;
    
        $filename = 'Hamaspik' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('HHAX Code', 'Agency Id', 'Caregiver Last Name', 'Caregiver First Name', 'Caregiver Phone Number', 'Caregiver Email');
        foreach ($columnsServiceArray as $srv) {
            $columns[] = $srv;
        }

        

        $callback = function () use ($finalResponse, $columns, $columnsServiceArray) {

            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($finalResponse as $record) {
                $final = array($record['patientDetails']->patient_code, $record->patientDetails->agencyDetail->agency_name, $record['patientDetails']->last_name, $record['patientDetails']->first_name, $record['patientDetails']->mobile, $record['patientDetails']->email);

                foreach ($columnsServiceArray as $key=>$srv) {
                    $date = '';
                    if (isset($record[$key]) && $record[$key] !="") {
                        $date = date('m/d/Y',strtotime( $record[strtolower($key)]));
                    }
                    $final[] = $date;
                }


                fputcsv($file, $final);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }


    public function exportCsvServicesOld(Request $request)
    {
        $query = $this->patientService->getExportcsvByAgencyid($request->agency_id);
        $final = [];
        $servicesName = [];

        if (!empty($query[0])) {
            foreach ($query as $val) {
                $explode = explode(',', $val->service_id);
                if (!empty($explode[0])) {
                    foreach ($explode as $sr) {
                        $masterDetails = Master::where('id', $sr)->first();
                        $temp = [];
                        $temp['id'] = $val->id;
                        $temp['code'] = $val->patient_code;
                        $temp['first_name'] = $val->first_name;
                        $temp['last_name'] = $val->last_name;
                        $temp['email'] = $val->email;
                        $temp['phone'] = $val->phone;
                        $temp['service_name'] = $masterDetails->name;
                        $temp['training_due_date'] = date('m/d/Y h:i A', strtotime($val->traning_due_date));
                        $temp['training_status'] = $val->training_status;
                        $serviceArrays = [];
                        $serviceArrays[$masterDetails->name] = date('m/d/Y h:i A', strtotime($val->completed_date));
                        if (in_array($masterDetails->name, $servicesName)) {
                        } else {
                            $servicesName[] = $masterDetails->name;
                        }
                        $temp['completed_date'] = date('m/d/Y h:i A', strtotime($val->completed_date));
                        $temp[$masterDetails->name] = $serviceArrays[$masterDetails->name];
                        $final[] = $temp;
                    }
                }
            }
        }

        $filename = 'HamaspikService' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Patient Id', 'Patient Code', 'First Name', 'Last Name', 'Email', 'Phone', 'Completed Date', 'Traning Due Date', 'Traning Status');
        foreach ($servicesName as $sr) {
            $columns[] = $sr;
        }
        

        $callback = function () use ($final, $columns, $servicesName) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($final as $record) {

                $final = array($record['id'], $record['code'], $record['first_name'], $record['last_name'], $record['email'], $record['phone'], $record['completed_date'], $record['training_due_date'], $record['training_status']);
                foreach ($servicesName as $srv) {
                    $date = '';
                    if (isset($record[$srv])) {
                        $date = $record[$srv];
                    }
                    $final[] = $date;
                }
                fputcsv($file, $final);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportCsvDocument(Request $request)
    {
        $query = $this->patientService->getExportcsvByAgencyid($request->agency_id);
        $final = [];

        if (!empty($query[0])) {
            foreach ($query as $val) {
                $getDocumentDetails = $this->documentPatientService->getAllDocumentByPatientId($val->id);
                if (!empty($getDocumentDetails[0])) {
                    foreach ($getDocumentDetails as $sr) {
                        $temp = [];
                        $temp['id'] = $val->id;
                        $temp['document_name'] = $sr->document_name;

                        $temp['created_date'] = date('m/d/Y h:i A', strtotime($sr->created_date));
                        $final[] = $temp;
                    }
                }
            }
        }

        $filename = 'HamaspikDocument' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Patient Id', 'Document Name', 'Created Date');
        

        $callback = function () use ($final, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($final as $record) {

                $final = array($record['id'], $record['document_name'], date('m/d/Y h:i A', strtotime($record['created_date'])));

                fputcsv($file, $final);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCsvServices(Request $request)
    {
        // ini_set('memory_limit', '-1');
        $columnsServiceArray = ['Annual Health Assessment', 'Flu Vaccine', 'Flu Declination', 'QuantiFeron', 'MMR Vaccine for Exempt Follow Up', 'MMR Exempt Letter', 'Clearance to Work', 'Habituation Statement', 'Corporate Compliance Training', 'Sexual Harassment Training', 'EVV Training', 'TB Covid - CDPAP Training'];

        $subQuery = $this->documentPatientService->getDocumentWisePatientData($request->agency_id);
        $getAllDetails = $this->documentPatientService->getDetailsByPatientId($subQuery->toArray());

        $final  = [];
        if (!empty($getAllDetails[0])) {
            foreach ($getAllDetails as $val) {


                $temp = [];

                foreach ($columnsServiceArray as $searchKey) {

                    $temp = $val;
                    $temp[$searchKey] = '';
                    if ($searchKey == 'Corporate Compliance Training' || $searchKey == 'Sexual Harassment Training' || $searchKey == 'TB Covid - CDPAP Training') {

                    //    $temp[$searchKey] = date('m/d/Y', strtotime($val->patientDetails->completed_date));
                    }

                    if ($searchKey == 'EVV Training' || $searchKey == 'Corporate Compliance Training'  || $searchKey == 'Sexual Harassment Training'|| $searchKey == 'TB Covid - CDPAP Training') {
                        if ($val->patientDetails->training_completed_date != "") {
                            $temp[$searchKey] = date('m/d/Y', strtotime($val->patientDetails->training_completed_date));
                        } else {
                            if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETED')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETED'), strtolower($val->patientDetails->training_status));
                                $temp[$searchKey] = $explode[1] ?? "";
                            } else if (strpos(strtolower($val->patientDetails->training_status), strtolower('TRAINING COMPLETE')) !== false) {
                                $explode = explode(strtolower('TRAINING COMPLETE'), strtolower($val->patientDetails->training_status));
                                $temp[$searchKey] = $explode[1] ?? "";
                            } else {
                                $temp[$searchKey] = $val->patientDetails->training_status;
                            }
                        }
                    }

                    if (strpos(strtolower($val->document_name), 'annual') !== false) {

                        $temp['Annual Health Assessment'] = date('m/d/Y', strtotime($val->created_date));
                    }
                    if (strpos(strtolower($val->document_name), strtolower('FLU DECLINATION')) !== false) {

                        $temp['Flu Declination'] = date('m/d/Y', strtotime($val->created_date));
                    }
                    if (strpos(strtolower($val->document_name), strtolower($searchKey)) !== false) {
                        $temp[$searchKey] = date('m/d/Y', strtotime($val->created_date));
                    }
                }
                $final[] = $temp;
            }
        }
     
        $filename = 'HamaspikDocument' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Patient Id', 'HHAX Code', 'Caregiver Last Name', 'Caregiver First Name', 'Caregiver Phone Number', 'Caregiver Email', 'Status','Dicipline');


        foreach ($columnsServiceArray as $val) {
            $temp = $val;
            if ($val == 'Annual Health Assessment' || $val == 'QuantiFeron' || $val == 'Clearance to Work' || $val == 'Habituation Statement' || $val == 'Corporate Compliance Training' || $val == 'Sexual Harassment Training' || $val == 'EVV Training' || $val == 'TB Covid - CDPAP Training') {
                $temp = $val . ' Completion Date';
            }
            if ($val == 'Flu Vaccine' || $val == 'Flu Declination' || $val == 'MMR Exempt Letter') {
                $temp = $val . ' Date';
            }
            $columns[] = $temp;
        }

        

        $callback = function () use ($final, $columns, $columnsServiceArray) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($final as $record) {
                if ($record['Annual Health Assessment'] != "" || $record['QuantiFeron'] != ""  || $record['Clearance to Work'] != ""  || $record['Habituation Statement'] != "" || $record['Flu Vaccine'] != "" || $record['Flu Declination'] != "" || $record['MMR Vaccine for Exempt Follow Up'] !="" || $record['MMR Exempt Letter']) {


                    $final = array($record['patientDetails']->id, $record['patientDetails']->patient_code, $record['patientDetails']->last_name, $record['patientDetails']->first_name, ($record['patientDetails']->phone !="")?str_replace('-','',$record['patientDetails']->phone):str_replace('-','',$record['patientDetails']->mobile), $record['patientDetails']->email, $record['patientDetails']->status, $record['patientDetails']->diciplin);
                    foreach ($columnsServiceArray as $searchKey) {

                        $date = '';


                        $date = ($record[$searchKey] != "") ? $record[$searchKey] : "";

                        $final[] = $date;
                    }
                    fputcsv($file, $final);
                }
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
