<?php
namespace App\Services;

use App\Model\DocumentUploadModal;
use Illuminate\Support\Facades\DB;
class DocumentUploadService{
    public  function save($data){
		$auth = auth()->user();
		if(isset($auth['id'])){
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $auth['id'];
		}
		
		$insert = new DocumentUploadModal($data);
		$insert->save();
		$insert_id =$insert->id;
		return $insert_id;
	}

    public function getDocumentListByDocumentId($id){
        return DocumentUploadModal::with('masterDetails:id,name')->where('document_id',$id)->where('del_flag','N')->get();
    }

	public function getDocumentListByPatientId($id,$serviceId=""){
	
        $query= DocumentUploadModal::with('masterDetails:id,name')->where('del_flag','N')->where('patient_id',$id);
		
		if($serviceId !=""){
			$query->where('service_id',$serviceId);
		}
		$query = $query->get();
		return $query;
    }

	public function getUploadDocumentServices($serviceIds,$agencyId){
		
		return DocumentUploadModal::with(['documentDetails','patientDetails'])->whereHas('patientDetails',function($join) use($agencyId){
			$join->where('agency_id',$agencyId);
		})->where('del_flag','N')->whereIn('service_id',$serviceIds)->pluck('document_id');

	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		return DocumentUploadModal::where($where)->update($data);
	}

	public function getUploadDocumentServicesWithNew($serviceIds,$agencyId,$created_date,$export=""){
		
		$query =  DocumentUploadModal::with(['documentDetails','patientDetails:id,patient_code,first_name,last_name,email,phone,agency_id','patientDetails.agencyDetail'])->whereHas('patientDetails',function($join) use($agencyId){
			$join->where('agency_id',$agencyId);
		})->where('del_flag','N')->whereIn('service_id',$serviceIds);
		if($created_date !=""){
			$explode = explode('-',$created_date);
			$query->whereDate('created_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($explode[1])));
		}
		if($export !=""){
			return $query->get();
		}else{
			return $query->paginate(50);
		}

	}

	public function getDocumentListByDocumentIdAllData($ids){
        return DocumentUploadModal::select(
			'document_upload_services.*',
			DB::raw('GROUP_CONCAT(master_table.name) as service_name')
		)
		->leftJoin('master_table', 'document_upload_services.service_id', '=', 'master_table.id')
		->with([
			'documentDetails:id,document_name,created_date,created_by,request_service_id,patient_id',
			'documentDetails.patientDetails:id,first_name,last_name,agency_id',
			'documentDetails.patientDetails.agencyDetail:id,agency_name',
			'documentDetails.userDetails:id,first_name,last_name'
		])
		->whereIn('document_upload_services.document_id', $ids)
		->where('document_upload_services.del_flag', 'N')
		->groupBy('document_upload_services.document_id')
		->get();
    }

	public function getDocumentListByDocumentIdAllDataIds($ids){
        return DocumentUploadModal::whereIn('document_id',$ids)->where('del_flag','N')->groupBy('document_id')->pluck('document_id');
    }

	public function getUploadDocumentServicesWithNewOther($serviceIds,$created_date,$agencyId){
		
		$query =  DocumentUploadModal::where('del_flag','N')->whereIn('service_id',$serviceIds);
		$query->whereHas('patientDetails',function($subQuery) use($agencyId){
			$subQuery->where('deleted_flag','N')->where('agency_id',$agencyId);
			
		});
		if($created_date !=""){
			$explode = explode('-',$created_date);
			$query->whereDate('created_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($explode[1])));
		}
		return $query= $query->get();

	}

	public function getDataNewForHamaspick($patientId,$serviceId){
		$query =  DocumentUploadModal::where('del_flag','N')->whereIn('service_id',$serviceId)->where('patient_id',$patientId);
		$query->whereHas('patientDetails',function($subQuery) use($patientId){
			$subQuery->where('deleted_flag','N')->where('id',$patientId);
			
		});
		
		return $query= $query->orderBy('id','desc')->first();
	}

	public function mergeAppointmentDocumentUploadServices($patientId){
		return DocumentUploadModal::select('id','patient_id')->where('del_flag','N')->where('patient_id',$patientId)->get();
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		return DocumentUploadModal::where($where)->update($data);
	}

	public function getAllDocumentList(){
		return DocumentUploadModal::select('document_id','service_id')->with(['masterDetails:id,name'])->where('del_flag','N')->get();
	}

	public function getDocumentListByDocumentIdAllDataNew($ids){
        return DocumentUploadModal::select(
			'document_upload_services.document_id',
			DB::raw('GROUP_CONCAT(master_table.name) as service_name')
		)
		->leftJoin('master_table', 'document_upload_services.service_id', '=', 'master_table.id')
		
		
		->where('document_upload_services.del_flag', 'N')
		->groupBy('document_upload_services.document_id')
		->get();
    }
	
	public function getAllDocumentListId($doc_id){
		return DocumentUploadModal::select('document_id','service_id')->where('document_id',$doc_id)->where('del_flag','N')->get();
	}

	public function getDocumentIdsByServices($serviceIds){
		return DocumentUploadModal::where('del_flag','N')->whereIn('service_id',$serviceIds)->pluck('document_id');
	}
}