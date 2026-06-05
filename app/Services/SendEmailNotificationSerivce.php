<?php
namespace App\Services;

use App\Model\AgencyWiseNotifictionEmail;
use App\Model\UserNotificationEmail;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Model\Patient;
use App\Model\AssignNyBestUser;
use App\Helpers\Utility;

class SendEmailNotificationSerivce{

	public static function sendEmailNotification($recordType,$notificationType,$agencyId,$subject,$message,$documentName="",$image=""){
		$user = Auth()->user();
		if($recordType =='Patient'){
			$query = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->get();
		}else{
			$query = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->get();
		}

		if(count($query) > 0){
			
			foreach($query as $value){
				$email = $value->email;
				try {
					$file = "";
					if($image !=""){
						$image = "patientdocument/" . $image;
						$file = Storage::disk('s3')->get($image);
					}
					
					$mail = Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message, $user,$documentName,$file) {

					$messages->to($email, "")->cc($user->email)
						->subject($subject)->html($message);	
						if($file !=""){
							$messages->attachData($file, $documentName);
						}
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}

		return $query;
		
	}

	public static function sendEmailNotificationUser($recordType,$notificationType,$userId,$subject,$message,$documentName="",$image=""){
		
		$query = UserNotificationEmail::where('user_id',$userId)->where('delete_flag','N')->first();
		$patientValue ="";
		if(isset($query->id)){	
			$users = User::where('id',$userId)->first();
		
			if(isset($users->id)){
				$email = $users->email;
			}
			$patientValue ="";
			$caregiverValue ="";
			if($recordType == "Patient"){

				if($notificationType == 'Status Update' && $query->status_update_caregiver_id == 'Status Update'){
					$patientValue = $query->status_update_patient_id;
				}else if($notificationType == 'Document Upload' && $query->upload_doc_caregiver_id == 'Document Upload'){
					$patientValue = $query->upload_doc_patient_id;
				}else if($notificationType == 'Send Note' && $query->send_notes_caregiver_id == 'Send Note'){
					$patientValue = $query->send_notes_patient_id;
				}
				
			}else{
				if($notificationType == 'Status Update' && $query->status_update_caregiver_id == 'Status Update'){
					$caregiverValue = $query->status_update_caregiver_id;
				}else if($notificationType == 'Document Upload' && $query->upload_doc_caregiver_id == 'Document Upload'){
					$caregiverValue = $query->upload_doc_caregiver_id;
				}else if($notificationType == 'Send Note' && $query->send_notes_caregiver_id == 'Send Note'){
					$caregiverValue = $query->send_notes_caregiver_id;
				}
				
			}
			if($patientValue != "" || $caregiverValue != ""){
				try {
					$file = "";
					if($image !=""){
						$image = "patientdocument/" . $image;
						$file = Storage::disk('s3')->get($image);
					}
					
					$mail = Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message,$documentName,$file) {
	
					$messages->to($email, "")->cc(Auth()->user()->email)
						->subject($subject)->html($message);	
						if($file !=""){
							$messages->attachData($file, $documentName);
						}
						
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}
	}

	public static function addAppointment($type, $agencyId, $serviceIds, $subject, $message) {
		$emailArray = []; 

		$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',$agencyId)->whereNotNUll('service_id')->get();

		foreach($AgencyWiseNotifictionEmail as $notification){
			$explode = explode(',',$notification->service_id);
			foreach($explode as $service){
				if(in_array($service,$serviceIds)){
					if(!in_array($notification->email,$emailArray)){
						$emailArray[]= $notification->email;
					}

				}
			}
		}
		
		try {
			foreach($emailArray as $email){
			
				Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message) {
					$messages->to($email, "")->subject($subject)->html($message);
				});
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	public static function sendEmailNotificationNew($recordType,$notificationType,$agencyId,$subject,$message,$documentName="",$image="",$recordId=""){
		if($recordType =='Patient'){
			$query = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->whereNotNull('service_id')->get();
		}else{
			$query = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->whereNotNull('service_id')->get();
		}

		if(count($query)==0){
			self::sendEmailNotification($recordType,$notificationType,$agencyId,$subject,$message,$documentName="",$image="");
		}else{

			$patient = Patient::where('id',$recordId)->first();
			$explode = explode(',',$patient->service_id);
			$finalEmailArray = [];
			foreach($query as $val){
				$subId = explode(',',$val->service_id);
				foreach($subId as $vas){
					if(in_array($vas,$explode)){
						if(!in_array($val->emai,$finalEmailArray)){
							$finalEmailArray[] = $val->email;
						}
					}
				}
			}
	
			self::UserMail($finalEmailArray,$image,$subject."|", $message,$documentName);
		}
		
	}

	public static function UserMail($emails,$image,$subject, $message,$documentName){
		$user = Auth()->user();
		try {
			$file = "";
			if($image !=""){
				$image = "patientdocument/" . $image;
				$file = Storage::disk('s3')->get($image);
			}

			foreach($emails as $email){
				$mail = Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message, $user,$documentName,$file) {

				$messages->to($email, "")->cc($user->email)
					->subject($subject)->html($message);	
					if($file !=""){
						$messages->attachData($file, $documentName);
					}
				});
			}
			
				
		} catch (\Throwable $th) {
			//throw $th;
		}
		
	}


	public static function generalAddAppointmentNotificationEmail($recordType,$notificationType,$serviceIds,$subject,$message){
		$auth = auth()->user();
		$emailArray = []; 
		if($recordType =='Patient'){
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}else{
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}
		
		if(!empty($AgencyWiseNotifictionEmail[0])){
			foreach($AgencyWiseNotifictionEmail as $notification){
				$explode = explode(',',$notification->service_id);
				if(!empty($explode[0])){
					
					foreach($explode as $service){
						if(in_array($service,$serviceIds)){
							if(!in_array($notification->email,$emailArray)){
								$emailArray[]= $notification->email;
							}
		
						}
					}
				}
			}
		}else{
			if($recordType =='Patient'){
				$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->whereNUll('service_id')->get();
			}else{
				$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->whereNUll('service_id')->get();
			}
			
			foreach($AgencyWiseNotifictionEmail as $notification){
				if(!in_array($notification->email,$emailArray)){
					$emailArray[]= $notification->email;
				}
			}
		}
		
		
		try {
			if($auth->agency_fk !=""){
				foreach($emailArray as $email){
			
					Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message) {
						$messages->to($email, "")->subject($subject)->html($message);
					});
				}
			}
			
		} catch (\Throwable $th) {
			//throw $th;
		}
		
	}

	public static function sendGeneralNotification($recordType,$notificationType,$subject,$message,$documentName="",$image="",$recordId=""){
		$auth = auth()->user();
		if($recordType =='Patient'){
			$query = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->get();
		}else{
			$query = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->get();
		}

		if(!empty($query)){
			foreach($query as $val){
				$email = $val->email;
				try {
					$file = "";
					if($image !=""){
						$image = "patientdocument/" . $image;
						$file = Storage::disk('s3')->get($image);
					}
					
					if($auth->agency_fk !=""){
						$mail = Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message,$documentName,$file) {

							$messages->to($email, "") 
								->subject($subject)->html($message);	
								if($file !=""){
									$messages->attachData($file, $documentName);
								}
							});
					}
					
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}
	}

	public static function sendEmailNotificationServicesDiscipline($recordType,$notificationType,$agencyId,$subject,$message,$documentName="",$image="",$recordId=""){
		if($recordType =='Patient'){
			$notificationData = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->get();
		}else{
			$notificationData = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->get();
		}
		$finalEmailArray = [];
		$patient = Patient::where('id',$recordId)->first();
		$explode = explode(',',$patient->service_id);
		$discipline = $patient->diciplin;
		foreach($notificationData as $data){
			if(isset($data['service_id']) && !empty($data['service_id'])){
				$subId = explode(',',$data->service_id);
				foreach($subId as $vas){
					if(in_array($vas,$explode)){
						if(!in_array($data->email,$finalEmailArray)){
							$finalEmailArray[] = $data->email;
						}
					}
				}
			}
			if(isset($data['discipline_id']) && !empty($data['discipline_id'])){
				$subId = explode(',',$data->discipline_id);
				foreach($subId as $vas){
					if($vas == $discipline){
						if(!in_array($data->email,$finalEmailArray)){
							$finalEmailArray[] = $data->email;
						}
					}
				}
			} 
			if(empty($data['discipline_id']) && empty($data['service_id'])){
				if(!in_array($data->email,$finalEmailArray)){
					$finalEmailArray[] = $data->email;
				}
			}
		}
		return $finalEmailArray;
	}

	public static function sendGeneralNotificationWithEmail($recordType,$notificationType,$subject,$message,$documentName="",$image="",$recordId=""){
		$auth = auth()->user();
		if($recordType =='Patient'){
			$query = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->get();
		}else{
			$query = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->get();
		}

		$finalEmailArray = [];
		if(!empty($query)){
			foreach($query as $val){
				$finalEmailArray[] = $val->email;
				
			}
		}

		return $finalEmailArray;
	}

	public static function sendEmailNotificationUserWithEmail($recordType,$notificationType,$userId,$subject,$message,$documentName="",$image=""){
		
		$query = UserNotificationEmail::where('user_id',$userId)->where('delete_flag','N')->first();
		$patientValue ="";
		$finalEmailArray =[];
		if(isset($query->id)){	
			$users = User::where('id',$userId)->first();
		
			if(isset($users->id)){
				$email = $users->email;
			}
			$patientValue ="";
			$caregiverValue ="";
			if($recordType == "Patient"){

				if($notificationType == 'Status Update' && $query->status_update_patient_id == 'Status Update'){
					$patientValue = $query->status_update_patient_id;
				}else if($notificationType == 'Document Upload' && $query->upload_doc_patient_id == 'Document Upload'){
					$patientValue = $query->upload_doc_patient_id;
				}else if($notificationType == 'Send Note' && $query->send_notes_patient_id == 'Send Note'){
					$patientValue = $query->send_notes_patient_id;
				}
				
			}else{
				if($notificationType == 'Status Update' && $query->status_update_caregiver_id == 'Status Update'){
					$caregiverValue = $query->status_update_caregiver_id;
				}else if($notificationType == 'Document Upload' && $query->upload_doc_caregiver_id == 'Document Upload'){
					$caregiverValue = $query->upload_doc_caregiver_id;
				}else if($notificationType == 'Send Note' && $query->send_notes_caregiver_id == 'Send Note'){
					$caregiverValue = $query->send_notes_caregiver_id;
				}
				
			}
			if($patientValue != "" || $caregiverValue != ""){
				$finalEmailArray[] = $users->email;
			}
		}

		return $finalEmailArray;
	}

	public static function UserMailWithMultipleEmail($emails,$image,$subject, $message,$documentName){
		try {
			
			$file = "";
				if($image !=""){
					$documentName = $image;
					$image = "patientdocument/" . $image;
					$file = Storage::disk('s3')->get($image);
					
				}
			foreach($emails as $email){
				
				
				$mail = Mail::mailer('second')->send([], [], function ($messages) use ($email, $subject, $message,$documentName,$file) {

				$messages->to($email, "")
					->subject($subject)->html($message);	
					if($file !=""){
						$messages->attachData($file, $documentName);
					}
				});
			}
			
				
		} catch (\Throwable $th) {
			//throw $th;
		}
		
	}

	public static function generalAddAppointmentNotificationEmailOnlyEmail($recordType,$notificationType,$serviceIds){
		$auth = auth()->user();
		$emailArray = []; 
		if($recordType =='Patient'){
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}else{
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}
		
		if(!empty($AgencyWiseNotifictionEmail[0])){
			foreach($AgencyWiseNotifictionEmail as $notification){
				$explode = explode(',',$notification->service_id);
				if(!empty($explode[0])){
					
					foreach($explode as $service){
						if(in_array($service,$serviceIds)){
							if(!in_array($notification->email,$emailArray)){
								$emailArray[]= $notification->email;
							}
		
						}
					}
				}
			}
		}else{
			if($recordType =='Patient'){
				$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->where('delete_flag','N')->whereNUll('service_id')->get();
			}else{
				$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::where('agency_id',0)->whereRaw('FIND_IN_SET("'.$notificationType.'", caregivers)')->where('delete_flag','N')->whereNUll('service_id')->get();
			}
			
			foreach($AgencyWiseNotifictionEmail as $notification){
				if(!in_array($notification->email,$emailArray)){
					$emailArray[]= $notification->email;
				}
			}
		}
		
		return $emailArray;
	}

	public static function addAppointmentOnlyEmail($type, $agencyId, $serviceIds) {
		$emailArray = []; 
		if($type == 'Patient'){
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',$agencyId)->whereRaw("FIND_IN_SET('Add New Record', patients)")->where('delete_flag','N')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}else{
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',$agencyId)->whereRaw("FIND_IN_SET('Add New Record', caregivers)")->where('delete_flag','N')->where('delete_flag','N')->whereNotNUll('service_id')->get();
		}

		foreach($AgencyWiseNotifictionEmail as $notification){
			$explode = explode(',',$notification->service_id);
			foreach($explode as $service){
				if(in_array($service,$serviceIds)){
					if(!in_array($notification->email,$emailArray)){
						$emailArray[]= $notification->email;
					}

				}
			}
		}
		return $emailArray;
	}

	public static function getAssignNyUserAgencyMail($agency_id){
		$nyAssignEmail = [];
		if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
			// $agencyIds = Utility::getUserWiseAgency();
			$agencyIds[] = $agency_id;
			$nyAssignEmail = AssignNyBestUser::getAssignNybestUserEmail($agencyIds);
		}
		return $nyAssignEmail;
	}

	public static function sendPatientNotification($notificationType,$agencyId,$subject,$message,$documentName="",$image="",$recordId="",$notiStatus){
		$notificationData = AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->whereRaw('FIND_IN_SET("'.$notificationType.'", patients)')->whereRaw('FIND_IN_SET("'.$notiStatus.'", patient_status)')->where('delete_flag','N')->get();
		$finalEmailArray = [];
		$patient = Patient::where('id',$recordId)->first();
		$explode = explode(',',$patient->service_id);
		$discipline = $patient->diciplin;
		foreach($notificationData as $data){
			if(isset($data['service_id']) && !empty($data['service_id'])){
				$subId = explode(',',$data->service_id);
				foreach($subId as $vas){
					if(in_array($vas,$explode)){
						if(!in_array($data->email,$finalEmailArray)){
							$finalEmailArray[] = $data->email;
						}
					}
				}
			}
			if(isset($data['discipline_id']) && !empty($data['discipline_id'])){
				$subId = explode(',',$data->discipline_id);
				foreach($subId as $vas){
					if($vas == $discipline){
						if(!in_array($data->email,$finalEmailArray)){
							$finalEmailArray[] = $data->email;
						}
					}
				}
			} 
			if(empty($data['discipline_id']) && empty($data['service_id'])){
				if(!in_array($data->email,$finalEmailArray)){
					$finalEmailArray[] = $data->email;
				}
			}
		}
		self::UserMail($finalEmailArray,$image,$subject, $message,$documentName);
	}

	public static function sendAgencyNotificationMailWithType($type, $notiType, $agencyId, $serviceIds=[], $image="",$subject="",$message ="", $documentName="") {
		$emailArray = []; 
		if($type == 'Patient'){
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',$agencyId)->whereRaw("FIND_IN_SET('".$notiType."', patients)")->where('delete_flag','N')->get();
		}else{
			$AgencyWiseNotifictionEmail = AgencyWiseNotifictionEmail::selectRaw("service_id,email")->where('agency_id',$agencyId)->whereRaw("FIND_IN_SET('".$notiType."', caregivers)")->where('delete_flag','N')->get();
		}

		foreach($AgencyWiseNotifictionEmail as $notification){
			$explode = explode(',',$notification->service_id);
			if(isset($serviceIds) && !empty($serviceIds) && count($serviceIds) > 0){
				foreach($explode as $service){
					if(in_array($service,$serviceIds)){
						if(!in_array($notification->email,$emailArray)){
							$emailArray[]= $notification->email;
						}

					}
				}
			}else{
				$emailArray[]= $notification->email;
			}
		}
		self::UserMail($emailArray,$image,$subject, $message,$documentName);
		return $emailArray;
	}
}