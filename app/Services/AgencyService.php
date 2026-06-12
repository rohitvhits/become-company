<?php

namespace App\Services;
use App\Agency;
use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;

class AgencyService{

    public function getAllVisitingAidAgencyList(){
        return  Agency::select('agency.id', 'agency.agency_name')->join('agency_wise_visiting_client',function($join){
            $join->on('agency_wise_visiting_client.agency_id','=','agency.id');
            $join->where('agency_wise_visiting_client.del_flag','N');
            $join->where('agency_wise_visiting_client.status',1);
        })
        ->where('agency.delete_flag', 'N')
        ->orderBy('agency.agency_name', 'asc')
        ->get();
    }

    public function getAgencyDetailsBySha1Id($agencyId){
        return Agency::whereRaw('SHA1(id) = "'.$agencyId.'"')->where('delete_flag','N')->first();
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];
        return Agency::where($where)->update($data);
    }

    public function getDetailsById($id){
        return Agency::where('id',$id)->where('delete_flag','N')->first();
    }

    public function getDetailsByAgencyId($agencyId){
        return Agency::where('delete_flag','N')->where('id',$agencyId)->first();
    }

    public function getFileManagerAgencies()
    {
        $currentUser = auth()->user();
        $query = Agency::where('delete_flag', 'N')->where('enable_file_manager', 1);

        if (!in_array($currentUser->user_type_fk, ['184', '4'])) {
            $agencyIds = Utility::getUserWiseAgency();
            if ($currentUser->agency_fk != '') {
                $agencyIds[] = $currentUser->agency_fk;
            }
            if (!empty($agencyIds)) {
                $query->whereIn('id', $agencyIds);
            }
        }

        return $query->orderBy('agency_name', 'asc')->get();
    }

    public function getAgencySettingsList($agencyName = null, $email = null, $phone = null)
    {
        $query = Agency::select(
                'agency.id',
                'agency.agency_name',
                'agency.email',
                'agency.phone',
                'agency.poc_document_type_id',
                'agency.poc_document_type_name',
                'agency.supervision_document_type_id',
                'agency.supervision_document_type_name',
                'agency.patient_assessment_document_type_id',
                'agency.patient_assessment_document_type_name',
                'agency.patient_package_document_type_id',
                'agency.patient_package_document_type_name',
                'agency.cms_485_document_type_id',
                'agency.cms_485_document_type_name',
                'agency.emergency_kardex_document_type_id',
                'agency.emergency_kardex_document_type_name',
                DB::raw('COALESCE(ags.hha_link, 0) as hha_link'),
                DB::raw('COALESCE(ags.send_poc, 0) as send_poc'),
                DB::raw('COALESCE(ags.send_to_supervision, 0) as send_to_supervision'),
                DB::raw('COALESCE(ags.kardex, 0) as kardex'),
                DB::raw('COALESCE(ags.assessment, 0) as assessment'),
                DB::raw('COALESCE(ags.upload_hha_cms_mdo_485, 0) as upload_hha_cms_mdo_485'),
                DB::raw('COALESCE(ags.upload_hha_patient_package_doc, 0) as upload_hha_patient_package_doc'),
                DB::raw('COALESCE(ags.upload_document_cron, 0) as upload_document_cron'),
                DB::raw('COALESCE(ags.upload_hha_poc, 0) as upload_hha_poc'),
                DB::raw('COALESCE(ags.upload_hha_supervision, 0) as upload_hha_supervision'),
                DB::raw('COALESCE(ags.upload_hha_assessment, 0) as upload_hha_assessment'),
                DB::raw('COALESCE(ags.upload_hha_kardex, 0) as upload_hha_kardex'),
                DB::raw('ags.poc_group_notes as poc_group_notes'),
            )
            ->leftJoin('agency_task_health_settings as ags', 'ags.agency_id', '=', 'agency.id')
            ->where('agency.delete_flag', 'N')->where('agency.enable_hha', 1);

        if (!empty($agencyName)) {
            $query->where('agency.agency_name', 'like', '%' . $agencyName . '%');
        }

        if (!empty($email)) {
            $query->where('agency.email', 'like', '%' . $email . '%');
        }

        if (!empty($phone)) {
            $query->where('agency.phone', 'like', '%' . $phone . '%');
        }

        return $query->orderBy('agency.agency_name', 'asc')->paginate(20);
    }

    public function getTaskHealthAgency($agency_id){
        return Agency::leftjoin('agency_task_health', function ($join) {
			$join->on('agency.id', '=', 'agency_task_health.agency_id');
		})->select('agency.id')
			->where('agency.id', $agency_id)
			->where('agency_task_health.status', '!=', 0)
			->where('agency.delete_flag', 'N')
			->first();
    }

    public function getSyncAlayacareAgencyList(){
        return Agency::select('id')->where('delete_flag', 'N')
        ->where('alaycare_status', 1)
        ->where(function ($query) {
            $query->whereNull('alayacare_employee_sync_date')
                ->orWhere('alayacare_employee_sync_date', '<', now()->subDays(5));
        })->first();
    }

    public function getDetailsByAlayacareAgency($agencyId){
        return Agency::where('id',$agencyId)->where('alaycare_status',1)->first();
    }

    public function getSyncClientAlayacareAgencyList(){
        return Agency::select('id')->where('delete_flag', 'N')
        ->where('alaycare_status', 1)
        ->where(function ($query) {
            $query->whereNull('alayacare_client_sync_date')
                ->orWhere('alayacare_client_sync_date', '<', now()->subDays(5));
        })->first();
    }

    public function getAlayacareAgencyList(){
       $agencyids = Utility::getUserWiseAgency();

        $query = Agency::where('delete_flag', 'N')->where('alaycare_status', 1);
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;

    }

    public function getDropdownList()
    {
        return Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get(['id', 'agency_name']);
    }

    public function getAiCallEnabledAgencyIds(): array
    {
        return Agency::where('ai_call_logs_enabled', 1)->pluck('id')->all();
    }

    public function getAllAgencyList(){
        return Agency::where('delete_flag', 'N')->pluck('agency_name','id')->toArray();
    }
}