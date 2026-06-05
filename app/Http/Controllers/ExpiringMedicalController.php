<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\Services\ExpiringMedicalService;
use App\Services\PatientNotesService;
use App\Agency;
use App\Model\HHACaregivers;
use App\Helpers\HHACaregiversHelper;
class ExpiringMedicalController extends Controller{

    protected $expiringMedicalService,$patientNotesService = '';
	public function __construct(ExpiringMedicalService $expiringMedicalService,PatientNotesService $patientNotesService)
	{
        $this->middleware('permission:expiring-medical', ['only' => ['index']]);
        $this->expiringMedicalService = $expiringMedicalService;
        $this->patientNotesService = $patientNotesService;
	}   

    public function index(Request $request)
	{
		$data['menu'] = "Patient List";
		$data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['agency_list'] = Agency::getHHAAgencyList();
        return  view('ExpiringMedical/expiring_medical_list',$data);
    }

    public  function expiringAppointmentAjax(Request $request){
        
        $query  =$this->expiringMedicalService->getData($request->agency_fk,"",$request->status);
      
        foreach($query as $val){
            $totalCount = $this->patientNotesService->getCountForNotesPatientId($val->id);
            $val->total_agency = count($totalCount);  
        }

        $data['query'] = $query;
        return  view('ExpiringMedical/expiring_medical_ajax_list',$data);
    }

    public  function exportCsv(Request $request){
        
        $query  =$this->expiringMedicalService->getData($request->agency_fk,'export',$request->status);
        foreach($query as $val){
            $totalCount = $this->patientNotesService->getCountForNotesPatientId($val->id);
            $val->total_agency = count($totalCount);  
        }

        $filename = 'Expiring Medical Next 10 days' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('HRC-Code','Agency Name', 'Caregiver Name', 'Last Work Date', 'NY Best Med Liaison Name', 'Due Date', 'Number of attempts', 'Medicals/Training Notes', 'Coordinator Name
        ','Status');
        
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($query as $list) {
                $assignToUser = '';
                $dueDate = '';
                if ($list->assignToUser !="") {
                    $assignToUser =$list->assignToUser->first_name.' '.$list->assignToUser->last_name;
                }

                if ($list->due_date !="" || $list->due_date !="1969-12-31") {
                    $dueDate =date('m/d/Y',strtotime($list->due_date));
                }
                $agencyName = '';
                if(isset($list->agencyDetail->id)){
                    $agencyName = $list->agencyDetail->agency_name;
                }
                fputcsv($file, array($list->patient_code,$agencyName, $list->first_name.' '.$list->last_name,'',$assignToUser, $dueDate, $list->total_agency, "NA", "NA",ucfirst($list->status)));
            }

            fclose($file);
        };
       
        return response()->stream($callback, 200, $headers);
    }

    function updateLastWorkDate(){
       
       $query = HHACaregivers::whereNull('last_work_date')->inRandomOrder()->limit(100)->get();
        
        foreach($query as $val){
            HHACaregiversHelper::getLastDateUpdate($val);
        }

    }
}