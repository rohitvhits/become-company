<?php

namespace App\Helpers;
use App\Model\UserWiseAgency;
use App\Model\AgencyWiseService;
use App\User;
use Illuminate\Support\Facades\View;
use App\Jobs\NotificationSaveJob;
use App\Jobs\NotificationSendJob;
use App\Model\GroupNotificationMaster;
use App\Model\GroupWiseUserNotification;
use App\Model\GroupWiseServiceNotification;
use App\Jobs\NotificationTermAndConditionsJob;
use App\Services\AgencyWiseDisabledService;
use App\Services\UserDocApprovalService;
use Carbon\Carbon;
use App\Services\LogsService;
use App\Services\ResolutionService;
use App\Services\ScheduleLocationDisableService;
use App\Model\AgencyOtherComplianceMedical;
use App\Agency;

class Utility
{

	public static function convertYMD($datemew)
	{
		if ($datemew == "")  return '';
		$dates = date('Y-m-d', strtotime($datemew));

		return $dates;
	}
	public static function convertMDY($date)
	{
		if ($date == "")  return '';

		return date('m/d/Y', strtotime($date));
	}
	public static function convertMDYTime($date)
	{
		if ($date == "")  return '';
		return date('m/d/Y h:i A', strtotime($date));
	}
	public static function convertYMDTime($date)
	{
		if ($date == "")  return '';

		return date('Y-m-d H:i:s', strtotime($date));
	}
	public static function convertTime($date)
	{
		if ($date == "")  return '';

		return date('H:i:s', strtotime($date));
	}
	
	public static  function convertUSAToUTC($date){
		$utcTimeZone = new \DateTimeZone('UTC');
		$localDateTime = new \DateTime($date);
        $localDateTime->setTimezone($utcTimeZone);
        return $localDateTime->format('Y-m-d\T');
	}

	public static function convertUTCToUSA($currentDateTime){
		$newDateTime = new \DateTime($currentDateTime); 
		 $newDateTime->setTimezone(new \DateTimeZone("America/New_York")); 
	
		return $newDateTime->format("m/d/Y h:i A");
	}

	public static function getUserWiseAgency(){
		
		$userWiseAgency = UserWiseAgency::join('agency', function ($join) {
			$join->on('agency.id', '=', 'user_wise_agency.agency_id');
			$join->where('agency.delete_flag', 'N');
		})->where('user_id',Auth()->user()->id)->where('user_wise_agency.delete_flag','N')->get();
	
		return $userWiseAgency->pluck('agency_id')->toArray();
	}

	public static function getServiceByAgency(){
		return AgencyWiseService::where('agency_id',Auth()->user()->agency_fk)->where('del_flag','N')->whereNotNull('service_id')->pluck('service_id')->toArray();
		
	}

	public static function getUserIdWiseAgency($user_id){
		$userWiseAgency = UserWiseAgency::where('user_id',$user_id)->where('delete_flag','N')->get();
		return $userWiseAgency->pluck('agency_id')->toArray();
	}

	public static function getHtmlContent($file_name,$data)
	{
		// Render the Blade view with dynamic data
		$htmlContent = View::make($file_name, $data)->render(); // This returns the HTML content as a string
		// Now you can use $htmlContent as you need
		return $htmlContent; // Or return the content for response, email, etc.
	}

	public static function insertNotificationsOfUser($agency_fk,$recordID,$portal_name,$type,$agency_id,$servicesData){
		$auth = auth()->user();
		$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agency_fk);
		if($auth->agency_fk !=""){
			$query->where('id','!=',auth()->user()->id);
		}
		$users = $query->pluck('id')->toArray();
		$users = self::getGroupUsersData($agency_id,$type,'Appointment',$users,$servicesData);
		
		$data['user'] = $users;
		$data['agency_fk'] = $agency_fk;
		$data['record_id'] = $recordID;
		$data['created_by'] = auth()->user()->id;
		$data['portal_name'] = $portal_name;
		$data['type'] = $type;
		NotificationSaveJob::dispatch($data);
	}

	public static function timeAgo($dateTime) {
		$now = strtotime('now'); // Get current time as timestamp
		$inputDate = strtotime($dateTime); // Convert input date to timestamp

		// Get the difference between the current time and the input time
		$diff = $now - $inputDate;

		// Calculate difference in terms of years, months, days, etc.
		$years = floor($diff / (365*60*60*24));  
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24) / (60*60*24));
		$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));
		$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60) / 60);
		$seconds = $diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60;

		// Display result
		if ($years > 0) {
			return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
		}
		if ($months > 0) {
			return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
		}
		if ($days > 0) {
			if ($days == 1) {
				return '1 day ago';
			}
			return $days . ' days ago';
		}
		if ($hours > 0) {
			return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
		}
		if ($minutes > 0) {
			return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
		}
		if ($seconds > 0) {
			return 'Just now';
		}
		return 'Just now'; // Default case, for edge cases

	}

	public static function getDistanceByLatLong($point1_lat, $point1_long, $point2_lat, $point2_long)
	{
		if ($point1_lat == "" || $point1_long == "" || $point2_lat == "" || $point2_long == "") {
			return 0.00;
		}
		$unit = 'mi';
		$decimals = 2;
		$degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_long - $point2_long)))));
		switch ($unit) {
			case 'km':
				$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
				break;
			case 'mi':
				$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
				break;
			case 'nmi':
				$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
		}
		return round($distance, $decimals);
	}

	public static function insertNotificationsType($data){
		$auth =auth()->user();
        $id = 0;
        if($auth){
            $id = $auth->id;
        }
		$data['user'] = $data['users'];
		$data['agency_fk'] = $data['agency_fk'];
		$data['record_id'] = $data['record_id'];
		$data['created_by'] =$id;
		$data['title'] = $data['title'];
		$data['msg'] = $data['msg'];
		$data['type'] = $data['type'];
		$data['sms'] = $data['sms'] ?? null;
		$data['email'] = $data['email'] ?? null;
		NotificationSendJob::dispatch($data);
	}


	public static function getGroupUsersData($agency_id,$type,$ntype,$oldUserData,$serviceData=array()){
		// Get Group Notifications data
		$flag = 0;
		$query = GroupNotificationMaster::whereRaw("FIND_IN_SET(?, agency_id)", [$agency_id])->where('delete_flag', 'N');
		if(strtolower($type) == 'caregiver'){
			$query->whereRaw("FIND_IN_SET(?, caregiver_notification)", [$ntype]);
		}else{
			$query->whereRaw("FIND_IN_SET(?, patients_notification)", [$ntype]);
		}
		$resultGroup = $query->pluck('id');
		
		if(count($resultGroup) == 0){
			$query = GroupNotificationMaster::where('delete_flag', 'N')->whereNull('agency_id');
			if(strtolower($type) == 'caregiver'){
				$query->whereRaw("FIND_IN_SET(?, caregiver_notification)", [$ntype]);
			}else{
				$query->whereRaw("FIND_IN_SET(?, patients_notification)", [$ntype]);
			}
			$resultGroup = $query->pluck('id');
			$flag =1;
		}
		$groupUserData = GroupWiseUserNotification::whereIn('group_id',$resultGroup)->where('delete_flag','N')->pluck('user_id')->toArray();
		
		//get Users from  the GroupId
		
		if(!empty($serviceData)){
			$result = GroupWiseServiceNotification::whereIn('service_id',$serviceData)->whereIn('group_id',$resultGroup)->where('delete_flag','N')->pluck('group_id')->toArray();
			if(!empty($result)){
				$groupUserData = GroupWiseUserNotification::whereIn('group_id',$result)->where('delete_flag','N')->pluck('user_id')->toArray();
			}else{
				if($flag ==0){
					$groupUserData = $oldUserData;
				}
			}
			
		}

		if(!empty($groupUserData[0])){
			$groupUserData = $groupUserData;
		}else{
			$groupUserData = [];
		}
		$users = array_unique(array_merge($oldUserData,$groupUserData));

		$finalRecordType = ['all',strtolower($type)];
		$placeholders = implode(',', array_fill(0, count($finalRecordType), '?'));
		$users = User::whereIn('id', $users)
				->whereRaw("LOWER(record_access) IN ($placeholders)", array_map('strtolower', $finalRecordType))
				->select('id')
				->get()->pluck('id')->toArray();
		return $users;
	}

	public static function insertNotificationsOfUserNew($agency_fk,$recordID,$portal_name,$type,$agency_id,$servicesData){
		$auth = auth()->user();
		$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agency_fk);
		if($auth->agency_fk !=""){
			$query->where('id','!=',auth()->user()->id);
		}
		$users = $query->pluck('id')->toArray();
		$users = self::getGroupUsersData($agency_id,$type,'Add Appointment',$users,$servicesData);
		
		$data['user'] = $users;
		$data['agency_fk'] = $agency_fk;
		$data['record_id'] = $recordID;
		$data['created_by'] = auth()->user()->id;
		$data['portal_name'] = $portal_name;
		$data['type'] = $type;
		NotificationSaveJob::dispatch($data);
	}

	public static function getUserWiseAgencyDashboard(){
		
		$userWiseAgency = UserWiseAgency::select('agency_id')->where('user_id',Auth()->user()->id)->where('delete_flag','N')->get();
	
		return $userWiseAgency->pluck('agency_id')->toArray();
	}

	public static function getServiceByAgencyDashboard(){
		return AgencyWiseService::select('service_id')->where('agency_id',Auth()->user()->agency_fk)->where('del_flag','N')->whereNotNull('service_id')->pluck('service_id')->toArray();
		
	}

	public static function sendTermAndPrivacy($id){
		$auth = auth()->user();
		$data['id'] = $id;
		$data['user_name'] = $auth->first_name.' '.$auth->last_name;
		$data['created_by'] = $auth->id;

		NotificationTermAndConditionsJob::dispatch($data);
		
	}

	private static function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR']??"";

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

	public static function getIP()
    {
        return self::getUserIP();
    }

	public static function getRemoteReferralSourceId(){
		$referralSources = [
			'e4bcee17-a96d-4098-b6c8-06528bf6da1d' => 'Assisted Home Care Services',
			'1f745445-191d-4b37-a111-095b8cbf27f9' => 'The Key',
			'5a51b45f-1363-4d21-b0e3-12ccfddaf475' => 'Crown - NHTD',
			'd96dace1-d44e-41d2-a618-15aebdca0417' => 'Crown Homecare',
			'4a3e3f0c-ed6a-4498-b542-19e3970eeb8c' => 'New York Health Care, Inc',
			'dd58f8d9-377a-43db-a9d4-1c8cf79e8386' => 'Silver Lining',
			'bc357580-bc60-4f5b-939a-20d784c7c512' => 'Castle Rock',
			'bd3addb6-23b6-4a82-884b-2ca1e855e71c' => 'Bryan Skilled Homecare',
			'7ba63328-09f8-477c-82a0-4aea19c2decd' => 'HCS',
			'45788fdc-5398-4cb9-bcef-4bd313fc470b' => 'Helping U Homecare',
			'083607b6-6f63-4c83-82a9-516090cfa19f' => 'VNS',
			'44b9c234-638c-47d4-9dd1-66a1df9595a5' => 'ConstantCare',
			'43ba3002-e8c5-432d-82d7-66dc9facc71f' => 'Home Choice',
			'7d0ca28c-ba5f-4f67-be06-6bf361a7b252' => 'Four Seasons',
			'e194d508-0891-4b98-81f8-74fda7cd452b' => 'Easy Choice Agency',
			'9025407d-7d36-4d7d-b598-7dd75504d62e' => 'Platinum Home Care',
			'43feee86-3487-4d40-9104-80172d3af787' => 'CABS Health Network',
			'cd0045b4-b6b6-4ab0-a68a-83e520714d1f' => 'Parent-U',
			'fce9b0cd-7484-4b7f-b4eb-862a89b726b6' => 'Today\'s Homecare',
			'a2624ed4-ec76-4644-a187-936e6a35a2b5' => 'BHRAGS HC',
			'7a73b050-5344-4aa5-bc76-9ca5dbc3e9e8' => 'A & J Homecare',
			'71a2a558-62d9-48c1-9ae1-ae8df1795c9c' => 'Four Season',
			'c212cbac-d0be-4568-9e7e-b487444123cf' => 'Boulevard',
			'74c982b7-5191-409c-9371-b4c5bc8405db' => 'City Choice',
			'ad7f832a-b8a9-4957-89aa-b71b59929144' => 'Americare',
			'c40efa6c-7a15-404f-9457-c318592eeb46' => 'Mrs. G',
			'b7a073dc-a6c7-47db-a5f2-ca584b2af56e' => 'L & G Homecare',
			'91c69666-d6ae-4218-ba0c-d87d91efa16f' => 'Incare Homecare',
			'b1ba9746-c363-4843-9e87-d88a962b40a2' => 'Aliah Homecare',
			'a512275d-c5fb-4efb-bcca-daa74b7c1be3' => 'Preferred Homecare',
			'abde322d-0e61-48dd-bdc2-dc704f58283f' => 'Angel Care Inc',
			'd74b4a26-10c8-434f-9696-f85aafb3b9c1' => 'Regency HC'
		];

		return $referralSources;
	}

	public static function insertNotificationsOfUserAPI($agency_fk,$recordID,$type,$agency_id,$servicesData,$platform_type){
		$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agency_fk);
		$users = $query->pluck('id')->toArray();

		$users = self::getGroupUsersData($agency_id,$type,'Appointment',$users,$servicesData);
		$notificationData = array(
			'users' => $users,
			'agency_fk' => $agencyFk ?? '',
			'record_id' => $recordID ?? '',
			'title' => 'New Appointment Created from '.$platform_type,
			'msg' => '',
			'type' => 'Appointment',
		);
		Utility::insertNotificationsType($notificationData);
		return $users;
	}

	public static function stopSMSService($agencyId){
		$AgencyWiseDisabledService = new AgencyWiseDisabledService();
		$query = $AgencyWiseDisabledService->getAgencyWiseDisabledSMSServiceList($agencyId);
		return $query->toArray();
	}

	public static function getServiceByAgencyWithUserAccess($accessType){
		if($accessType =='All'){
			return [];
		}else{
			return AgencyWiseService::where('agency_id',Auth()->user()->agency_fk)->where('type',$accessType)->where('del_flag','N')->whereNotNull('service_id')->pluck('service_id')->toArray();
		}

	}
	
	public static function dynamicDocumentApproved(){
		$userDocApprovalService = new UserDocApprovalService();
		$query = $userDocApprovalService->getUserIdsByTypeDetails('patient');

		$temp = [];
		$allUserIds = [];
		foreach ($query as $row) {
			$key = $row['key'] ?? 'unknown';
			$userId = $row['user_id'];
			$name = $row['name'];
			// Initialize sub-array if not set
			if (!isset($temp[$key])) {
				$temp[$key] = [];
			}
			$temp[$key][0][$userId] = $name;
			// Collect for 'All'
			$allUserIds[] = $userId;
		}
		$temp['All'] = array_unique($allUserIds);
		return $temp;
		
	}

	public static function formatSSN($ssn)
	{
		// Remove non-digits just in case
		$ssn = preg_replace('/\D/', '', $ssn);

		if (strlen($ssn) === 9) {
			return substr($ssn, 0, 3) . '-' . substr($ssn, 3, 2) . '-' . substr($ssn, 5);
		}

		return $ssn; // Return as-is if not 9 digits
	}

	public static function parseFlexibleDate($dateInput) {
		$formats = [
			'd-m-Y', 'm-d-Y', 'Y-m-d',
			'd/m/Y', 'm/d/Y', 'Y/m/d',
			'd.m.Y', 'm.d.Y', 'Y.m.d',
			'd.m.y', 'm.d.y', 'm-d-y', 'm/d/y'
		];

			foreach ($formats as $format) {
				$parsed = Carbon::createFromFormat($format, $dateInput);
				if ($parsed && $parsed->format($format) === $dateInput) {
					return Carbon::instance($parsed)->format('Y-m-d');
				}
			}

			// Final fallback attempt with Carbon
			try {
				return Carbon::parse($dateInput)->format('Y-m-d');
			} catch (\Exception $e) {
				return null; // Could not parse
			}
	}

	/**
	 * Parse a daterangepicker string "MM/DD/YYYY - MM/DD/YYYY"
	 * Returns ['from' => 'Y-m-d 00:00:00', 'to' => 'Y-m-d 23:59:59'] or null values if invalid.
	 */
	public static function parseDateRangeFilter($dateRange)
	{
		$result = ['from' => null, 'to' => null];
		if (empty($dateRange) || strpos($dateRange, ' - ') === false) {
			return $result;
		}
		$parts = explode(' - ', $dateRange, 2);
		$from  = static::parseFlexibleDate(trim($parts[0]));
		$to    = static::parseFlexibleDate(trim($parts[1]));
		if ($from) $result['from'] = $from . ' 00:00:00';
		if ($to)   $result['to']   = $to   . ' 23:59:59';
		return $result;
	}
	
		public static function convertMdyToYmd($dateRange)
		{
			$result = ['from' => null, 'to' => null];

			try {
				[$from, $to] = explode(' - ', $dateRange);

				$fromDate = Carbon::createFromFormat('m/d/Y', trim($from))
					->startOfDay()
					->format('Y-m-d H:i:s');

				$toDate = Carbon::createFromFormat('m/d/Y', trim($to))
					->endOfDay()
					->format('Y-m-d H:i:s');

				if ($fromDate) $result['from'] = $fromDate;
				if ($toDate)   $result['to']   = $toDate;

				return $result;

			} catch (\Exception $e) {
				return $result;
			}
		}

	public static function convertMdyToYmdUsingCarbon($date)
	{
		$date = str_replace('/','-',$date);
		$explode = explode('-',$date);
		$date = $explode[2].'-'.$explode[0].'-'.$explode[1];
		return $date;
	}

	public static function relationship(){
		return  [
			'Self',
			'Wife',
			'Child',
			
		];

	}

	public static function convertMdyToYmdUsingCarbonbySlash($date)
	{
		$explode = explode('/',$date);
		$date = $explode[2].'-'.$explode[0].'-'.$explode[1];
		return $date;
	}

	public static function getTeamArray(){
		$team = array(
			'clinicians' => 'Clinicians',
			'mdo_team' => 'MDO Team',
			'schedule_coordinators' => 'Schedule Coordinators',
			'medgen_team' => 'Medgen Team',
			'supervisor' => 'A Manager / Supervisor'
		);
		return $team;
	}

	public static function getResolutionArray(){
		$resolution = array(
			'clinicians' => array(
								'Cancelled','Refused','1st Attempt - Unable to Contact','2nd Attempt - Unable to Contact','3rd Attempt - Unable to Contact','Patient Deceased','Telehealth Completed','Hospitalised / In Rehab','Service Provided','Unable To Contact'),
			'mdo_team' => array('New Order Received','Processing','Signed','Signed & Sent Back to the Agency','Cancelled','Unable To Contact','Closed Temporarily'),
			'schedule_coordinators' => array('New Form Requested','Refused','Cancelled','1st Attempt - Unable to Contact','2nd Attempt - Unable to Contact','3rd Attempt - Unable to Contact','Patient Deceased','Hospitalised / In Rehab','Booked','Form Completed','Unable To Contact'),
			'medgen_team' => array('Telehealth Completed , Pending Forms','Patient Asked to Reschedule','Cancelled','Refused','Hospitalised / In Rehab','Appointment Missed','Form Completed','Service Provided','Unable To Contact'),
			'supervisor' => array('Booked','Appointment Missed','Cancelled','Refused','Form Completed','Telehealth Completed','Telehealth Completed , Pending Forms','Hospitalised / In Rehab','Patient Asked to Reschedule','Patient Deceased','Processing','Service Provided','Signed','Signed & Sent Back to the Agency','Unable To Contact','1st Attempt - Unable to Contact','2nd Attempt - Unable to Contact','3rd Attempt - Unable to Contact','Closed Temporarily')
		);
		return $resolution;
	}

	public static function getStatusData(){
		$status = array(
			'Cancelled',
			'Refused',
			'1st Attempt - Unable to Contact',
			'2nd Attempt - Unable to Contact',
			'3rd Attempt - Unable to Contact',
			'Telehealth Completed',
			'Hospitalised / In Rehab',
			'Processing',
			'Booked',
			'Patient Deceased',
			'Signed',
			'Signed & Sent Back to the Agency',
			'Telehealth Completed , Pending Forms',
			'Patient Asked to Reschedule',
			'New Order Received',
			'New Form Requested',
			'Appointment Missed',
			'Form Completed',
			'Service Provided',
			'Unable To Contact',
			'Closed Temporarily'
		);
		return $status;
	}
	
	public static function getLanguageDefultArray(){
		return [74, 75, 76];
	}

	public static function getLanguageDefault(){
		$default = 74;
		return $default;
	}

	public static function getUniqueStatusData(){
		$status = array(
			'1st Attempt - Unable to Contact',
			'2nd Attempt - Unable to Contact',
			'3rd Attempt - Unable to Contact',
			'Telehealth Completed',
			'Patient Deceased',
			'Signed',
			'Signed & Sent Back to the Agency',
			'Telehealth Completed , Pending Forms',
			'Patient Asked to Reschedule',
			'New Order Received',
			'New Form Requested',
			'Appointment Missed',
			'Form Completed',
			'Service Provided',
			'Closed Temporarily'
		);
		return $status;
	}

	public static function resolutionSupervisorAccess(){
		return [487, 482, 500, 4611]; // 487 = Tiline, 482 = System admin, 500 = Marina, jada = 4611 
	}

	public static function getUniqueStatusDataNew(){
		return array(
			'1st Attempt - Unable to Contact' => '1st Attempt - Unable to Contact',
			'2nd Attempt - Unable to Contact' => '2nd Attempt - Unable to Contact',
			'3rd Attempt - Unable to Contact' => '3rd Attempt - Unable to Contact',
			'Telehealth Completed' => 'Telehealth Completed',
			'Patient Deceased' => 'Patient Deceased',
			'Signed' => 'Signed',
			'Signed-SentBacktotheAgency' => 'Signed & Sent Back to the Agency',
			'TelehealthCompleted-Pending Forms' => 'Telehealth Completed , Pending Forms',
			'PatientAskedtoReschedule' => 'Patient Asked to Reschedule',
			'New Order Received' => 'New Order Received',
			'New Form Requested' => 'New Form Requested',
			'Appointment Missed' => 'Appointment Missed',
			'Form Completed' => 'Form Completed',
			'Service Provided' => 'Service Provided',
			'Closed Temporarily' => 'Closed Temporarily'
		);
	
	}

	public static function getPatientStatusData(){
		return array(
			'Cancelled' => 'Cancelled',
			'Refused' => 'Refused',
			'1st Attempt - Unable to Contact' => '1st Attempt - Unable to Contact',
			'2nd Attempt - Unable to Contact' => '2nd Attempt - Unable to Contact',
			'3rd Attempt - Unable to Contact' => '3rd Attempt - Unable to Contact',
			'Telehealth Completed' => 'Telehealth Completed',
			'hospitalized/rehab' => 'Hospitalised / In Rehab',
			'Processing' => 'Processing',
			'Booked' => 'Booked',
			'Patient Deceased' => 'Patient Deceased',
			'Signed' => 'Signed',
			'Signed-SentBacktotheAgency' => 'Signed & Sent Back to the Agency',
			'TelehealthCompleted-Pending Forms' => 'Telehealth Completed , Pending Forms',
			'Patient Asked to Reschedule' => 'Patient Asked to Reschedule',
			'New Order Received' => 'New Order Received',
			'New Form Requested' => 'New Form Requested',
			'Appointment Missed' => 'Appointment Missed',
			'Form Completed' => 'Form Completed',
			'Service Provided' => 'Service Provided',
			'unableToContact' => 'Unable To Contact',
			'Closed Temporarily' => 'Closed Temporarily'
		);
	}

	public static function getStatusFromServiceId($create_service_id){
		$statusNew = 'New Form Requested'; // default fallback
		$array = array(
			'New Order Received' => ['181','1167'],
			'New Form Requested' => '' // Remaining all
		);
		foreach ($array as $key => $serviceIds) {
			if (!empty($serviceIds) && array_intersect($serviceIds, $create_service_id)) {
				$statusNew = $key;
				break;
			}
		}
		return $statusNew;
	}
	
	public static function agencyPortalRolePermission(){
		return ['5136','5137'];
	}

	public static function saveResolutionLogForms($status,$ser_req_id,$patient_id,$selteam = "",$custom_ip_address=""){
		$user = auth()->user()??'';
		if(!empty($selteam)){
			$user_id = $selteam;
		}else{
			
			if(empty($user)){
				$user = env('API_USER_ID');
			}else{
				$user_id = $user->id;
			}
		}
		
		$user = User::find($user_id);
		if($status == 'New Order Received'){
			$team = 'MDO Team';
		}
		else if($status == 'New Form Requested'){
			$team = 'Schedule Coordinators';
		}
		$resData = array(
			'patient_id' => $patient_id,
			'team' => $team,
			'resolution' => $status,
			'notes' =>"",
			'service_request_id' => $ser_req_id,
			'auto_created_by'=>$user_id,
		);
		
		ResolutionService::saveResImportPatientServices($resData);
		$ipaddress = Utility::getIP();
		if(empty($ipaddress) && isset($custom_ip_address) && !empty($custom_ip_address)){
			$ipaddress = $custom_ip_address;
		}
		$insertLog = [
			'type' => 'Saved resolution data for services',
			'link' => url('/save-pateint-service-requested'),
			'module' => 'Patient Appointment',
			'object_id' => $patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Saved Resolution data for services.',
			'new_response' => serialize($resData),
			'ip' => $ipaddress,
		];

		LogsService::save($insertLog);
		
	}

	public static function staticDateWiseAgencyAccess(){
		return  [
			'AddAppointment' => 'Add Appointment',
			'EditAppointment' => 'Edit Appointment',
			'AddDocument' =>'Add Document',
			'DownloadDocument'=>'Download Document',
			'AddNotes'=>'Add Notes',
			'AddService'=>'Add Service',
			'EditService'=>'Edit Service'
		];
	}
	
	public static function changePDFVersion(){
		return ['1.7','1.4'];
	}

	public static function getLocationDisableForSchedule(){
		return ScheduleLocationDisableService::getLocationDisableForSchedule();
	}

	public static function convertTwelveHourTime($time)
	{
		if ($time == "")  return '';
		return date('h:i a', strtotime($time));
	}

	public static function convertName($title){
		return ucfirst(strtolower($title));
	}

	public static function getPOCDocumentTypeId(){
		return [80983];
	}

	public static function getCreateFontWiseImage(){
		return json_encode(["Adinda Melia.otf", "Agashi Signature Demo.otf", "AlfridaDemoSignature.ttf", "Bellisya.otf", "AmarulaPersonalUse.ttf"]);
	}

	public static function getSuperVisorCaregiverDocumentTypeId(){
		return [80950];
	}

	public static function getHHAOtherComplianceMedicalId($agencyId){
		return AgencyOtherComplianceMedical::where('agency_id', $agencyId)
        ->where('del_flag', 'N')
        ->value('medical_id');
	}

	public static function getHHAOtherComplianceMedicalResultId($agencyId,$medicalId){
		return AgencyOtherComplianceMedical::where('agency_id', $agencyId)
        ->where('delete_flag', 'N')
        ->where('medical_id', $medicalId)
        ->value('medical_result_id');
	}

	public static function convertDays($days){
		return date('Y-m-d H:i:s', strtotime($days));
	}

	public static function hasReportingToolAccess()
	{
		$authUser = Auth()->user();
		if(isset($authUser->agency_fk)){
			return !empty($authUser->agency_fk)
				? Agency::where('id', $authUser->agency_fk)->where('show_reporting_tool', 1)->exists()
				: false;
		}
		return true;
	}

	public static function convertToIso8601String($date){
		if($date == ""){
			return null;
		}
		try {
			return Carbon::parse($date)->toIsoString();
		} catch (\Exception $e) {
			return null; // or handle the error as needed
		}
	}

	public static function convertToTimestamp($date): ?int
	{
		if(empty($date)){
			return null;
		}
		try {
			return Carbon::parse($date)->timestamp;
		} catch (\Exception $e) {
			return null;
		}
	}

	// Converts a Unix timestamp (integer) to m/d/Y H:i:s format.
	// Usage: Utility::unixTimestampToMDYTime(1748465827) => "05/28/2026 13:37:07"
	public static function unixTimestampToMDYTime($timestamp): string
	{
		if (empty($timestamp)){
			return '-';
		}
		return Carbon::createFromTimestamp($timestamp)->format('m/d/Y H:i:s');
	}

	// Converts a Unix timestamp (integer) to Y-m-d format. Used in RingLogix CDR date range lookup.
	// Usage: Utility::convertTimestampToYMD(1748465827) => "2026-05-28"
	public static function convertTimestampToYMD($timestamp): string
	{
		return date('Y-m-d', (int) $timestamp);
	}

	// Appends 23:59:59 to a Y-m-d date string to get end-of-day datetime. Used in RingLogix CDR date range lookup.
	// Usage: Utility::endOfDay('2026-05-28') => "2026-05-28 23:59:59"
	public static function endOfDay($date): string
	{
		return $date . ' 23:59:59';
	}
}