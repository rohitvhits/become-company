<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\AgencyWiseHHAMedical;

class AgencyWiseHHAMedicalsService{

	public function ajaxList($requestData = [],$paginate=""){
		$query = AgencyWiseHHAMedical::with(['agency:id,agency_name', 'office:office_id,office_name'])
			->select('id', 'agency_id', 'office_id', 'medical_id', 'medical_name', 'status','last_sync_date');

		// Apply filters if provided
		if (!empty($requestData['agency_fk'])) {
			$query->where('agency_id', $requestData['agency_fk']);
		}

		if (!empty($requestData['office_fk'])) {
			$query->where('office_id', $requestData['office_fk']);
		}

		if (!empty($requestData['medical_name'])) {
			$query->where('medical_name', 'like', '%' . $requestData['medical_name'] . '%');
		}

		if (isset($requestData['status']) && $requestData['status'] !== '') {
			$query->where('status', $requestData['status']);
		}

		// Order by latest
		$query->orderBy('id', 'desc');

		// Paginate results
		if($paginate !=""){
			
			return $query->get();
		}else{
			$perPage = $requestData['per_page'] ?? 50;
			return $query->paginate($perPage);
		}
		
	}

	public function exportCSV($requestData = []){
		$query = AgencyWiseHHAMedical::with(['agency', 'office'])
			->select('id', 'agency_id', 'office_id', 'medical_id', 'medical_name', 'status');

		// Apply same filters as ajaxList
		if (!empty($requestData['agency_fk'])) {
			$query->where('agency_id', $requestData['agency_fk']);
		}

		if (!empty($requestData['office_fk'])) {
			$query->where('office_id', $requestData['office_fk']);
		}

		if (!empty($requestData['medical_name'])) {
			$query->where('medical_name', 'like', '%' . $requestData['medical_name'] . '%');
		}

		if (isset($requestData['status']) && $requestData['status'] !== '') {
			$query->where('status', $requestData['status']);
		}

		$query->orderBy('id', 'desc');

		return $query->get();
	}

	public function getAgencyMedicalList($agencyId,$officeId){
		$medicals = AgencyWiseHHAMedical::select('id', 'medical_id', 'medical_name');
		if($agencyId !=""){
			$medicals->where('agency_id', $agencyId);
		}
		if($agencyId !=""){
			$medicals->where('office_id', $officeId);
		}
		
		return $medicals->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('medical_name', 'asc')
                ->get();

	}
}