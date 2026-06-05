<?php

namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\AppointmentImportFile;

class AppointmentImportFileService{

	public function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new AppointmentImportFile($data);
		$insert->save();
		return $insert->id;
	}

	public function update($data,$where){
		if(isset($data['updated_by'])){
			$updatedBy = $data['updated_by'];
		}else{
			$auth = auth()->user();
			$updatedBy = $auth['id'];
		}
		
		$data['updated_at'] = date('Y-m-d H:i:s');
		return AppointmentImportFile::where($where)->update($data);
	}

	public function softDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		return AppointmentImportFile::where($where)->update($data);
	}

	public function getData($full_name,$email,$phone){
		$where = 'deleted_flag ="N"';
		if($full_name !=''){
			$where .=' and full_name LIKE "%'.$full_name.'%"';
		}
		if($email !=''){
			$where .=' and email ="'.$email.'"';
		}
		if($phone !=''){
			$where .=' and phone = "'.$phone.'"';
		}
		
		return AppointmentImportFile::whereRaw($where)->orderBy('id','desc')->paginate(10);
	}
	
	public function getDetailById($id){
		return AppointmentImportFile::where('delete_flag','N')->with('agency:id,agency_name')->where('id',$id)->first();
	}
	
	public function getDataExport($full_name,$email,$phone){
		$where = 'deleted_flag ="N"';
		if($full_name !=''){
			$where .=' and full_name LIKE "%'.$full_name.'%"';
		}
		if($email !=''){
			$where .=' and email ="'.$email.'"';
		}
		if($phone !=''){
			$where .=' and phone = "'.$phone.'"';
		}
		
		return AppointmentImportFile::whereRaw($where)->get();
	}
	
	public function getAppointmentImportFileList(){
		return AppointmentImportFile::where('deleted_flag','N')->get();
	}

	public function getImportAppointmentsFile($search){
		$user = auth()->user();
        $perPage = $search['per_page']??50;
        $search =$search['search']??"";

        // Build query joining appointment_import_file with import_csv_file_record
        $query = AppointmentImportFile::leftJoin('agency as a', 'appointment_import_file.agency_id', '=', 'a.id')
            ->leftJoin('users as u', 'appointment_import_file.created_by', '=', 'u.id')
			->leftJoin('users as apv', 'appointment_import_file.approved_by', '=', 'apv.id')
            ->select(
                'appointment_import_file.id',
				'appointment_import_file.total_record',
                'appointment_import_file.file',
                'appointment_import_file.extension',
                'appointment_import_file.agency_id',
                'appointment_import_file.created_date',
				'appointment_import_file.status as iStatus',
				'appointment_import_file.failed_records',
				'appointment_import_file.success_records',
				'appointment_import_file.duplicate_record',
				'appointment_import_file.import_status',
                'a.agency_name',
				'apv.first_name',
				'apv.last_name',
				'appointment_import_file.approved_date',
				'appointment_import_file.file_name'
            )
            ->where('appointment_import_file.delete_flag', 'N')->whereNotNull('appointment_import_file.status')
            ->groupBy('appointment_import_file.id');

        // Filter by agency if user has agency restriction
        if (!empty($user->agency_fk)) {
            $query->where('appointment_import_file.agency_id', $user->agency_fk);
        }

        // Apply agency filter from request
        if (isset($search['agency_id']) && !empty($search['agency_id'])) {
            $query->where('appointment_import_file.agency_id', $search['agency_id']);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('appointment_import_file.file', 'like', "%{$search}%")
                  ->orWhere('a.agency_name', 'like', "%{$search}%")
                  ->orWhere('u.first_name', 'like', "%{$search}%")
                  ->orWhere('u.last_name', 'like', "%{$search}%");
            });
        }

        // Order by latest first
        $query->orderBy('appointment_import_file.id', 'desc');

        // Get paginated results
        $importFiles = $query->paginate($perPage);

        // Get detailed child records for each import file
        $data = $importFiles->getCollection()->map(function($file) {
            
            return [
                'id' => $file->id,
                'file' => !empty($file->file_name) ? $file->file_name : $file->file,
				'total_record' => $file->total_record,
                'extension' => $file->extension,
                'agency_id' => $file->agency_id,
                'agency_name' => $file->agency_name ?? 'N/A',
                'created_date' => $file->created_date,
                'created_by_name' => trim($file->created_by_name),
                'total_records' => (int)$file->total_records,
                'successful_count' => (int)$file->success_records,
                'failed_count' => (int)$file->failed_records,
				'duplicate_record' => (int)$file->duplicate_record,
                'status' => $file->iStatus,
				'approved_user' => $file->first_name.' '.$file->last_name,
				'approved_date' => $file->approved_date,
                'import_status' => $file->import_status,
                'import_records' => 0
            ];
        });

		return ['data'=>$data,'importFiles'=>$importFiles];
	}

	public function getPendingRecord($type){
		return AppointmentImportFile::select('id', 'created_by','status')
            ->where('delete_flag', 'N')
            ->where('status', $type)->where('created_date','<',date('Y-m-d H:i:s', strtotime('+10 minutes')))
            ->first();
	}
}
