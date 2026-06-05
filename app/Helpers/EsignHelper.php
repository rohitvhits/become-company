<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\DocumentSentReport;
use App\Template;
use App\DocusignDetail;
use App\Model\Patient;
use App\PDF;
use App\Master;
use App\Invoice;
use App\Agency;
use App\Record;
use App\Helpers\AttachMailer;
use App\Helpers\Common;
use App\Helpers\PatientApplicationDetailHelper;
use App\Helpers\PatientBankDetailHelper;
use App\Helpers\PatientRentDetailHelper;
use App\PatientDocusignDetail;
use Illuminate\Support\Facades\Mail;
use App\Services\PatientDocumentSentReportService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class EsignHelper
{
	protected const ESIGN_PATIENT_WRITE_DOCUMENT="patientWriteDocument";
	protected const DOCUSIGN_FOLDER = 'dosusinguploads/docusign';
	public function __construct() {}

	public static function nyBestNewResponseOld($selectedValue)
	{
		$data = "";
		$staticOptions = [
			'pm@id' => 'ID',
			'pm@portal_id' => 'Portal ID',
			'pm@p_full_name' => 'Full Name',
			'pm@p_last_name' => 'Last Name First Name Middle Name',
			'pm@first_name' => 'First Name',
			'pm@middle_name' => 'Middle Name',
			'pm@last_name' => 'Last Name',
			'pm@address1' => 'Address',
			'pm@address2' => 'Address2',
			'pm@full_address' => 'Full Address',
			'pm@city' => 'City',
			'pm@state' => 'State',
			'pm@county' => 'County',
			'pm@zip_code' => 'Zipcode',
			'pm@phone' => 'Phone Number',
			'pm@mobile' => 'Mobile Number',
			'pm@dob' => 'Date of Birth',
			'pm@gender' => 'Gender',
			'pm@language' => 'Language',
			'ag@agency_name' => 'Agency Name',
		];

		// Add static options with title "Basic Field"
		$data .= '<optgroup label="Basic Field">';
		$alias = 'fm@';

		foreach ($staticOptions as $key => $value) {
			$selected = ($selectedValue == $key) ? 'selected="selected"' : '';
			$data .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
		}
		$data .= '</optgroup>';

		// Add dynamic fields with title "Form Field"
		if (!empty($dynamicFields)) {
			$data .= '<optgroup label="Form Field">';
			$doctorKey = $alias . 'doctor_name';

			$data .= '<option value="' . $doctorKey . '" ' . ($selectedValue == 'doctor_name' ? 'selected="selected"' : '') . '>Doctor Name</option>';

			foreach ($dynamicFields as $key => $value) {
				$formId = $value['form_id'];
				$fieldId = $value['field_id'];
				$label = $value['label'];

				$fullKey = $alias . $fieldId;
				$selected = ($selectedValue == $key) ? 'selected="selected"' : '';

				$data .= '<option value="' . $fullKey . '" ' . $selected . '>' . $label . '</option>';
			}
			$data .= '</optgroup>';
		}

		return $data;
	}


	public static function nyBestNewResponse($selectedValue, $dynamicFields = [])
	{
		$data = "";
		$staticOptions = [
			'pm@id' => 'ID',
			'pm@portal_id' => 'Portal ID',
			'pm@patient_code' => 'Code',
			'pm@p_full_name' => 'Full Name',
			'pm@p_last_name' => 'Full Name (Last Name First Name Middle Name)',
			'pm@first_name' => 'First Name',
			'pm@middle_name' => 'Middle Name',
			'pm@last_name' => 'Last Name',
			'pm@ssn' => 'SSN',
			'pm@email' => 'Email',
			'pm@address1' => 'Address',
			'pm@address2' => 'Address2',
			'pm@full_address' => 'Full Address',
			'pm@city' => 'City',
			'pm@state' => 'State',
			'pm@county' => 'County',
			'pm@zip_code' => 'Zipcode',
			'pm@phone' => 'Phone Number',
			'pm@mobile' => 'Mobile Number',
			'pm@dob' => 'Date of Birth',
			'pm@gender' => 'Gender',
			'pm@language' => 'Language',
			'ag@agency_name' => 'Agency Name',
			'pm@emergency_contact_name' => 'Emeregency Contact Name',
			'pm@emergency_phone' => 'Emeregency Contact Phone',
			'mt@name' => 'Insurance Name',
			'pm@cin' => 'CIN /Medicaid Number',
			'pm@diciplin' => 'Discipline',
			'pm@medicare_no' => 'Medicare No',
			'pm@form_created_date' => 'Form Created Date',
		];

		// Add static options with title "Basic Field"
		$data .= '<optgroup label="Basic Field">';
		$alias = 'fm@';

		foreach ($staticOptions as $key => $value) {
			$selected = ($selectedValue == $key) ? 'selected="selected"' : '';
			$data .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
		}
		$data .= '</optgroup>';
		
		// Add dynamic fields with title "Form Field"
		if (!empty($dynamicFields)) {

			$data .= '<optgroup label="Form Field">';

			foreach ($dynamicFields as $key => $value) {
				$formId = $value['form_id'];
				$fieldId = $value['field_id'];
				$label = $value['label'];

				$fullKey = $alias . $fieldId;
				$selected = ($selectedValue == $fullKey) ? 'selected="selected"' : '';

				$data .= '<option value="' . $fullKey . '" ' . $selected . '>' . $label . '</option>';
			}
			$data .= '</optgroup>';
		}

		$doctorFields = [
			
			'full_name' => 'Full Name',
			'phone' => 'Phone',
			'email' => 'Email',
			'gender' => 'Gender',
			'remarks' => 'Remarks',
			'license' => 'License',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'zipcode' => 'Zipcode',
			'place_of_examination' => 'Place of Examination',
			'date_of_examination' => 'Date of Examination',
			'specialty'=>'Specialty',
			'registry_number'=>'Registry Number',
			'npi_number'=>'Npi Number'
		];

		$dralias = 'dr@';

		// Add doctor fields with title "Doctor Field"
		
		$data .= '<optgroup label="Doctor Field">';
		foreach ($doctorFields as $key => $label) {
			$fullKey = $dralias . $key;
			$selected = ($selectedValue == $fullKey) ? 'selected="selected"' : '';
			$data .= '<option value="' . $fullKey . '" ' . $selected . '>' . $label . '</option>';
			
		}
		$data .= '</optgroup>';


		//Add Agency Fields
		$agencyFields = [
			
			'agency_name' => 'Agency Name',
			'email' => 'Email',
			'phone' => 'Phone',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'state' => 'State',
			'city' => 'City',
			'zip_code' => 'Zip Code',
			'county' => 'Country',
			'full_address' => 'Full Address',
			
		];

		$agalias = 'ag@';

		$data .= '<optgroup label="Agency Field">';
		foreach ($agencyFields as $key => $label) {
			$fullKey = $agalias . $key;
			$selected = ($selectedValue == $fullKey) ? 'selected="selected"' : '';
			$data .= '<option value="' . $fullKey . '" ' . $selected . '>' . $label . '</option>';
			
		}
		$data .= '</optgroup>';

		return $data;
	}

	public static function RecordResponse($selectedValue)
	{
		$data = "";
		//'Consumer@GENDER'=>'CONSUMER GENDER',
		$final_Array = array();
		$new_array = array(
			'record@id' => 'ID',
			'record@agency_fk' => 'Agency Name',
			'record@full_name' => 'Full Name',
			'record@first_name' => 'First Name',
			'record@middle_name' => 'Middle Name',
			'record@last_name' => 'Last Name',
			'record@email' => 'Email Address',
			'record@address' => 'Address',
			'record@address1' => 'Address1',
			'record@address2' => 'Address2',
			'record@full_address' => 'Full Address',

			'record@city' => 'City',
			'record@state' => 'State',
			'record@zip' => 'Zipcode',
			'record@phone' => 'Phone Number',
			'record@dob' => 'Date of Birth',
			'record@gender' => 'Gender',
			'record@active' => 'Active',
			'record@ssn' => 'SSN',
			'record@cin' => 'CIN',
			'record@follow_date' => 'Follow Date',
			'ms@medicaid_issue' => 'Medical Issue',
			'mss@patient_status' => 'Patient Status',
			'mr@relationship1' => 'Relation Ship',
			'record@rel1_phone' => 'Relation Phone',
			'mts@trust_name' => 'Trust Name',
			'record@family_name1' => 'Family Contact Name1',
			'record@family_name2' => 'Family Contact Name2',
			'mrs@relationship2' => 'Relation Ship2',
			'record@rel2_phone' => 'Relation Phone2',
			'und@undercare_action' => 'Undercare Action',
			'record@surplus1' => 'Surplus Amount',
			'record@surplus2' => 'Surplus Amount2',
			'record@msp' => 'MSP',
			'record@house_visit' => 'House Visit',
			'record@rate' => 'Rate',
			'record@date' => 'Date',
			'record@file_date' => 'File Date',
			'record@completed_date' => 'Completed Date',
			'inv@id' => 'Invoice Id',
			'record@payment_status' => 'Payment Status',
			'record@county' => 'County',
			'record@recent_month' => 'Recent Month',
			'related@id' => 'Related Patient ID',
			'related@agency_fk' => 'Related Patient Agency Name',
			'related@full_name' => 'Related Patient Full Name',
			'related@first_name' => 'Related Patient First Name',
			'related@middle_name' => 'Related Patient Middle Name',
			'related@last_name' => 'Related Patient  Last Name',
			'related@email' => 'Email Address',
			'related@address' => 'Related Patient Address',
			'related@address1' => 'Related Patient Address1',
			'related@address2' => 'Related Patient Address2',
			'related@full_address' => 'Related Patient Full Address',

			'related@city' => 'Related Patient City',
			'related@state' => 'Related Patient State',
			'related@zip' => 'Related Patient Zipcode',
			'related@phone' => 'Related Patient Phone Number',
			'related@dob' => 'Related Patient Date of Birth',
			'related@gender' => 'Related Patient Gender',
			'related@active' => 'Related Patient Active',
			'related@ssn' => 'Related Patient SSN',
			'related@cin' => 'Related Patient CIN',
			'related@follow_date' => 'Related Patient Follow Date',

			'related@rel1_phone' => 'Related Patient Relation Phone',

			'related@family_name1' => 'Related Patient Family Contact Name1',
			'related@family_name2' => 'Related Patient Family Contact Name2',

			'related@rel2_phone' => 'Related Patient Relation Phone2',

			'related@surplus1' => 'Related Patient Surplus Amount',
			'related@surplus2' => 'Related Patient Surplus Amount2',
			'related@msp' => 'Related Patient MSP',
			'related@house_visit' => 'Related Patient House Visit',
			'related@rate' => 'Related Patient Rate',
			'related@date' => 'Related Patient Date',
			'related@file_date' => 'Related Patient File Date',
			'related@completed_date' => 'Related Patient Completed Date',

			'related@payment_status' => 'Related Patient Payment Status',
			'related@county' => 'Related Patient County',
			'related@recent_month' => 'Related Patient Recent Month',
			'pad@bank_name' => 'Patient Bank Name',
			'pad@account_no' => 'Patient Bank Account No',
			'pad@income_amount' => 'Patient Income Amount',
			'pad@name_of_income' => 'Patient Name Of Income',
			'pad@income_type' => 'Patient Income Type',
			'pad@name_of_owner' => 'Patient Name of Owner',
			'pad@balance' => 'Balance',
			'pad@how_often' => 'Patient How Often',
			'pad@marital_status' => 'Marital Status',
			'pad@rent_amount' => 'Rent Amount',

			'pad@bank_name_2' => 'Patient Bank Name Two',
			'pad@account_no_2' => 'Patient Bank Account No Two',
			'pad@income_amount_2' => 'Patient Income Amount Two',
			'pad@name_of_income_2' => 'Patient Name Of Income Two',
			'pad@income_type_2' => 'Patient Income Type Two',
			'pad@name_of_owner_2' => 'Patient Name of Owner Two',
			'pad@balance_2' => 'Balance Two',
			'pad@how_often_2' => 'Patient How Often Two',
			'pad@marital_status_2' => 'Marital Status Two',
			// 'pad@rent_amount_2' => 'Rent Amount Two',

			'pad@bank_name_3' => 'Patient Bank Name Three',
			'pad@account_no_3' => 'Patient Bank Account No Three',
			'pad@income_amount_3' => 'Patient Income Amount Three',
			'pad@name_of_income_3' => 'Patient Name Of Income Three',
			'pad@income_type_3' => 'Patient Income Type Three',
			'pad@name_of_owner_3' => 'Patient Name of Owner Three',
			'pad@balance_3' => 'Balance Three',
			'pad@how_often_3' => 'Patient How Often Three',
			'pad@marital_status_3' => 'Marital Status Three',
			// 'pad@rent_amount_3' => 'Rent Amount Three',

			'pad@bank_name_4' => 'Patient Bank Name Four',
			'pad@account_no_4' => 'Patient Bank Account No Four',
			'pad@income_amount_4' => 'Patient Income Amount Four',
			'pad@name_of_income_4' => 'Patient Name Of Income Four',
			'pad@income_type_4' => 'Patient Income Type Four',
			'pad@name_of_owner_4' => 'Patient Name of Owner Four',
			'pad@balance_4' => 'Balance Four',
			'pad@how_often_4' => 'Patient How Often Four',
			'pad@marital_status_4' => 'Marital Status Four',
			// 'pad@rent_amount_4' => 'Rent Amount Four',

			'pad@bank_name_5' => 'Patient Bank Name Five',
			'pad@account_no_5' => 'Patient Bank Account No Five',
			'pad@income_amount_5' => 'Patient Income Amount Five',
			'pad@name_of_income_5' => 'Patient Name Of Income Five',
			'pad@income_type_5' => 'Patient Income Type Five',
			'pad@name_of_owner_5' => 'Patient Name of Owner Five',
			'pad@balance_5' => 'Balance Five',
			'pad@how_often_5' => 'Patient How Often Five',
			'pad@marital_status_5' => 'Marital Status Five',
			// 'pad@rent_amount_5' => 'Rent Amount Five',

			'pad@bank_name_6' => 'Patient Bank Name Six',
			'pad@account_no_6' => 'Patient Bank Account No Six',
			'pad@income_amount_6' => 'Patient Income Amount Six',
			'pad@name_of_income_6' => 'Patient Name Of Income Six',
			'pad@income_type_6' => 'Patient Income Type Six',
			'pad@name_of_owner_6' => 'Patient Name of Owner Six',
			'pad@balance_6' => 'Balance Six',
			'pad@how_often_6' => 'Patient How Often Six',
			'pad@marital_status_6' => 'Marital Status Six',
			'thp@company_name' => 'Third Party Health Company',
			'thp@amount' => 'Third Party Health Amount',
			'thp@insurance_id' => 'Third Party Health How to Often',
			'thp@policy_holder' => 'Third Party Health Policy Holder',

			'spr@first_name' => 'Spousal Refusal First Name',
			'spr@last_name' => 'Spousal Refusal Last Name',
			'spr@ssn' => 'Spousal Refusal SSN',
			'spr@dob' => 'Spousal Refusal DOB',
			'mp@medicare_part_flag' => 'Medicare Part B',
			'mp@amount' => 'Medicare Premium Amount',


			// 'pad@rent_amount_6' => 'Rent Amount Six',
		);


		foreach ($new_array as $keys => $value) {

			$selected = '';
			if ($selectedValue == $keys) {
				$selected = 'selected="selected"';
			}
			$data .= '<option value="' . $keys . '" ' . $selected . '>' . $value . '</option>';
		}

		return $data;
	}

	public static function getResponse($key, $user_id)
	{

		$explode  = explode('@', $key);
		$finalArray = array();
		$matsreArray = array();
		$masters = Master::where('del_flag', 'N')->get();
		if (!empty($masters)) {
			foreach ($masters as $val) {
				$matsreArray[$val->id] = $val->name;
			}
		}
		$invoice = array();
		$invodiLisue = Invoice::where('del_flag', 'N')->get();
		if (!empty($invodiLisue)) {
			foreach ($invodiLisue as $keysdf) {
				$invoice[$keysdf->id][] = $keysdf->invoice_number;
			}
		}
		$agencyArray = array();
		$agencyList  = Agency::select('id', 'agency_name')->where('delete_flag', 'N')->get();
		if (!empty($agencyList)) {
			foreach ($agencyList as $keyss) {
				$agencyArray[$keyss->id] = $keyss->agency_name;
			}
		}
		$keys = '';
		if ($explode[0] == 'record') {
			if ($explode[1] == 'full_name') {
				$keys = 'CONCAT(record.first_name,record.last_name) as full_name';
				$keys = DB::raw('CONCAT_WS(" ",record.first_name," ",record.last_name) as full_name');
			}
			if ($explode[1] == 'full_address') {
				$keys = DB::raw('CONCAT_WS("",record.address,"-",record.address1,"-",record.address2,"-",record.city,"-",record.state,"-",record.county,"-",record.zip) as full_address');
			}
			if ($explode[1] != 'full_name' && $explode[1] != 'full_address') {
				$keys = 'record.' . $explode[1];
			}
			$keys = $keys;
		}


		if ($explode[0] == 'related') {
			if ($explode[1] == 'full_name') {
				$keys = 'CONCAT(related.first_name,related.last_name) as full_name';
				$keys = DB::raw('CONCAT_WS(" ",related.first_name," ",related.last_name) as full_name');
			}
			if ($explode[1] == 'full_address') {
				$keys = DB::raw('CONCAT_WS(" ",related.address," ",related.address1," ",related.address2," ",related.city," ",related.state," ",related.county," ",related.zip) as full_address');
			}
			if ($explode[1] != 'full_name' && $explode[1] != 'full_address') {
				$keys = 'related.' . $explode[1];
			}
			$keys = $keys;
		}
		if ($explode[0] == 'thp') {
			$keys = 'third_party_health_insurance.' . $explode[1];
		}
		if ($explode[0] == 'spr') {
			$keys = 'spousal_refusal.' . $explode[1];
		}
		if ($explode[0] == 'mp') {
			$keys = 'medicare_premium.' . $explode[1];
		}

		if ($keys == '') {
			$keys = 'record.id';
		}

		$query = Record::select($keys)->leftjoin('record as related', function ($join) {
			$join->on('related.id', '=', 'record.patient_related_id');
		});
		if ($explode[0] == 'thp') {
			$query->leftjoin('third_party_health_insurance', function ($join) {
				$join->on('third_party_health_insurance.record_id', '=', 'record.id');
			});
		}
		if ($explode[0] == 'spr') {
			$query->leftjoin('spousal_refusal', function ($join) {
				$join->on('spousal_refusal.record_id', '=', 'record.id');
			});
		}
		if ($explode[0] == 'mp') {
			$query->leftjoin('medicare_premium', function ($join) {
				$join->on('medicare_premium.record_id', '=', 'record.id');
			});
		}


		$query = $query->where('record.id', $user_id)->first();

		$final[$key] = $query[$explode[1]];

		if ($explode[0] != 'spr' && $explode[0] != 'mp') {
			if ($explode[1] == 'follow_date' || $explode[1] == 'dob' || $explode[1] == 'date' || $explode[1] == 'file_date' || $explode[1] == 'completed_date') {
				$dates = '';
				if ($query[$explode[1]] != '1970-01-01') {
					$dates = date('m/d/Y', strtotime($query[$explode[1]]));
				}
				$final[$key] = $dates;
			}
			if ($explode[1] == 'agency_fk') {
				$ahency = $query[$explode[1]];
				$final[$key] = isset($agencyArray[$ahency]) ? $agencyArray[$ahency] : "";
			}
			if ($explode[0] == 'ms' || $explode[0] == 'mss'   || $explode[0] == 'mr' || $explode[0] == 'mts' || $explode[0] == 'mrs' || $explode[0] == 'und') {

				$ahency = $query[$explode[1]];
				$final[$key] = isset($agencyArray[$ahency]) ? $agencyArray[$ahency] : "";
			}
		} else {
			if ($explode[0] == 'spr') {
				if ($explode[1] == 'dob') {
					$dates = '';
					if ($query[$explode[1]] != '1970-01-01') {
						$dates = date('m/d/Y', strtotime($query[$explode[1]]));
					}
					$final[$key] = $dates;
				}
			} else {
				if ($explode[1] == 'medicare_part_flag') {
					$final[$key] = isset($query[$explode[1]]) ? "Yes" : "No";
				}
			}
		}


		if ($explode[0] == 'pad') {
			$getAllApplicationDetails = PatientApplicationDetailHelper::getDetailsByPatientId($user_id);
			$getAllPatientApplicationDetails = PatientBankDetailHelper::getDetailsByPatientId($user_id);
			$getAllPatientRentDetails = PatientRentDetailHelper::getDetailsByPatientId($user_id);
			if ($explode[1] == 'bank_name') {
				$final[$key] = isset($getAllPatientApplicationDetails[0]->bank_name) ? $getAllPatientApplicationDetails[0]->bank_name : "";
			}
			if ($explode[1] == 'account_no') {
				$final[$key] = isset($getAllPatientApplicationDetails[0]->account_no) ? $getAllPatientApplicationDetails[0]->account_no : "";
			}
			if ($explode[1] == 'income_amount') {
				$final[$key] = isset($getAllApplicationDetails[0]->income_amount) ? $getAllApplicationDetails[0]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income') {
				$final[$key] = isset($getAllApplicationDetails[0]->name_of_income) ? $getAllApplicationDetails[0]->name_of_income : "";
			}
			if ($explode[1] == 'income_type') {
				$ahency = isset($getAllApplicationDetails[0]->income_type) ? $getAllApplicationDetails[0]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status') {
				$ahency = isset($getAllApplicationDetails[0]->marital_status) ? $getAllApplicationDetails[0]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner') {
				$ahency = isset($getAllPatientApplicationDetails[0]->name_of_owner) ? $getAllPatientApplicationDetails[0]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance') {
				$ahency = isset($getAllPatientApplicationDetails[0]->balance) ? $getAllPatientApplicationDetails[0]->balance : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'how_often') {

				$ahency = isset($getAllApplicationDetails[0]->how_often) ? $getAllApplicationDetails[0]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'rent_amount') {
				$ahency = isset($getAllPatientRentDetails[0]->amount) ? $getAllPatientRentDetails[0]->amount : "";
				$final[$key] = $ahency;
			}



			if ($explode[1] == 'bank_name_2') {
				$final[$key] = isset($getAllPatientApplicationDetails[1]->bank_name) ? $getAllPatientApplicationDetails[1]->bank_name : "";
			}
			if ($explode[1] == 'account_no_2') {
				$final[$key] = isset($getAllPatientApplicationDetails[1]->account_no) ? $getAllPatientApplicationDetails[1]->account_no : "";
			}
			if ($explode[1] == 'income_amount_2') {
				$final[$key] = isset($getAllApplicationDetails[1]->income_amount) ? $getAllApplicationDetails[1]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income_2') {
				$final[$key] = isset($getAllApplicationDetails[1]->name_of_income) ? $getAllApplicationDetails[1]->name_of_income : "";
			}
			if ($explode[1] == 'income_type_2') {
				$ahency = isset($getAllApplicationDetails[1]->income_type) ? $getAllApplicationDetails[1]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status_2') {
				$ahency = isset($getAllApplicationDetails[1]->marital_status) ? $getAllApplicationDetails[1]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner_2') {
				$ahency = isset($getAllPatientApplicationDetails[1]->name_of_owner) ? $getAllPatientApplicationDetails[1]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance_2') {
				$ahency = isset($getAllPatientApplicationDetails[1]->balance) ? $getAllPatientApplicationDetails[1]->balance : "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'how_often_2') {
				$ahency = isset($getAllApplicationDetails[1]->how_often) ? $getAllApplicationDetails[1]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";;
			}

			if ($explode[1] == 'rent_amount_2') {
				$ahency = "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'bank_name_3') {
				$final[$key] = isset($getAllPatientApplicationDetails[2]->bank_name) ? $getAllPatientApplicationDetails[2]->bank_name : "";
			}
			if ($explode[1] == 'account_no_3') {
				$final[$key] = isset($getAllPatientApplicationDetails[2]->account_no) ? $getAllPatientApplicationDetails[2]->account_no : "";
			}
			if ($explode[1] == 'income_amount_3') {
				$final[$key] = isset($getAllApplicationDetails[2]->income_amount) ? $getAllApplicationDetails[2]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income_3') {
				$final[$key] = isset($getAllApplicationDetails[2]->name_of_income) ? $getAllApplicationDetails[2]->name_of_income : "";
			}
			if ($explode[1] == 'income_type_3') {
				$ahency = isset($getAllApplicationDetails[2]->income_type) ? $getAllApplicationDetails[2]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status_3') {
				$ahency = isset($getAllApplicationDetails[2]->marital_status) ? $getAllApplicationDetails[2]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner_3') {
				$ahency = isset($getAllPatientApplicationDetails[2]->name_of_owner) ? $getAllPatientApplicationDetails[2]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance_3') {
				$ahency = isset($getAllPatientApplicationDetails[2]->balance) ? $getAllPatientApplicationDetails[2]->balance : "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'how_often_3') {
				$ahency = isset($getAllApplicationDetails[2]->how_often) ? $getAllApplicationDetails[2]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";;
			}

			if ($explode[1] == 'rent_amount_3') {
				$ahency = "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'bank_name_4') {
				$final[$key] = isset($getAllPatientApplicationDetails[3]->bank_name) ? $getAllPatientApplicationDetails[3]->bank_name : "";
			}
			if ($explode[1] == 'account_no_4') {
				$final[$key] = isset($getAllPatientApplicationDetails[3]->account_no) ? $getAllPatientApplicationDetails[3]->account_no : "";
			}
			if ($explode[1] == 'income_amount_4') {
				$final[$key] = isset($getAllApplicationDetails[3]->income_amount) ? $getAllApplicationDetails[3]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income_4') {
				$final[$key] = isset($getAllApplicationDetails[3]->name_of_income) ? $getAllApplicationDetails[3]->name_of_income : "";
			}
			if ($explode[1] == 'income_type_4') {
				$ahency = isset($getAllApplicationDetails[3]->income_type) ? $getAllApplicationDetails[3]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status_4') {
				$ahency = isset($getAllApplicationDetails[3]->marital_status) ? $getAllApplicationDetails[3]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner_4') {
				$ahency = isset($getAllPatientApplicationDetails[3]->name_of_owner) ? $getAllPatientApplicationDetails[3]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance_4') {
				$ahency = isset($getAllPatientApplicationDetails[3]->balance) ? $getAllPatientApplicationDetails[3]->balance : "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'how_often_4') {
				$ahency = isset($getAllApplicationDetails[3]->how_often) ? $getAllApplicationDetails[3]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";;
			}

			if ($explode[1] == 'rent_amount_4') {
				$ahency = "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'bank_name_5') {
				$final[$key] = isset($getAllPatientApplicationDetails[4]->bank_name) ? $getAllPatientApplicationDetails[4]->bank_name : "";
			}
			if ($explode[1] == 'account_no_5') {
				$final[$key] = isset($getAllPatientApplicationDetails[4]->account_no) ? $getAllPatientApplicationDetails[4]->account_no : "";
			}
			if ($explode[1] == 'income_amount_5') {
				$final[$key] = isset($getAllApplicationDetails[4]->income_amount) ? $getAllApplicationDetails[4]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income_5') {
				$final[$key] = isset($getAllApplicationDetails[4]->name_of_income) ? $getAllApplicationDetails[4]->name_of_income : "";
			}
			if ($explode[1] == 'income_type_5') {
				$ahency = isset($getAllApplicationDetails[4]->income_type) ? $getAllApplicationDetails[4]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status_5') {
				$ahency = isset($getAllApplicationDetails[4]->marital_status) ? $getAllApplicationDetails[4]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner_5') {
				$ahency = isset($getAllPatientApplicationDetails[4]->name_of_owner) ? $getAllPatientApplicationDetails[4]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance_5') {
				$ahency = isset($getAllPatientApplicationDetails[4]->balance) ? $getAllPatientApplicationDetails[4]->balance : "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'how_often_5') {
				$ahency = isset($getAllApplicationDetails[4]->how_often) ? $getAllApplicationDetails[4]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";;
			}

			if ($explode[1] == 'rent_amount_5') {
				$ahency = "";
				$final[$key] = $ahency;
			}

			if ($explode[1] == 'bank_name_6') {
				$final[$key] = isset($getAllPatientApplicationDetails[5]->bank_name) ? $getAllPatientApplicationDetails[5]->bank_name : "";
			}
			if ($explode[1] == 'account_no_6') {
				$final[$key] = isset($getAllPatientApplicationDetails[5]->account_no) ? $getAllPatientApplicationDetails[5]->account_no : "";
			}
			if ($explode[1] == 'income_amount_6') {
				$final[$key] = isset($getAllApplicationDetails[5]->income_amount) ? $getAllApplicationDetails[5]->income_amount : "";
			}
			if ($explode[1] == 'name_of_income_6') {
				$final[$key] = isset($getAllApplicationDetails[5]->name_of_income) ? $getAllApplicationDetails[5]->name_of_income : "";
			}
			if ($explode[1] == 'income_type_6') {
				$ahency = isset($getAllApplicationDetails[5]->income_type) ? $getAllApplicationDetails[5]->income_type : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'marital_status_6') {
				$ahency = isset($getAllApplicationDetails[5]->marital_status) ? $getAllApplicationDetails[5]->marital_status : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";
			}
			if ($explode[1] == 'name_of_owner_6') {
				$ahency = isset($getAllPatientApplicationDetails[5]->name_of_owner) ? $getAllPatientApplicationDetails[5]->name_of_owner : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'balance_6') {
				$ahency = isset($getAllPatientApplicationDetails[5]->balance) ? $getAllPatientApplicationDetails[5]->balance : "";
				$final[$key] = $ahency;
			}
			if ($explode[1] == 'how_often_6') {
				$ahency = isset($getAllApplicationDetails[5]->how_often) ? $getAllApplicationDetails[5]->how_often : "";
				$final[$key] = isset($matsreArray[$ahency]) ? $matsreArray[$ahency] : "";;
			}
			if ($explode[1] == 'rent_amount_6') {
				$ahency = "";
				$final[$key] = $ahency;
			}
		}
		if ($explode[0] == 'inv') {
			$invs = $query[$explode[1]];
			$final[$key] = isset($invoice[$invs]) ? $invoice[$invs] : "";
		}

		return $final;
	}
	public static function getNyBestResponse($key, $user_id)
	{

		$explode  = explode('@', $key);
		$finalArray = array();
		$matsreArray = array();
		$masters = Master::where('del_flag', 'N')->get();
		if (!empty($masters)) {
			foreach ($masters as $val) {
				$matsreArray[$val->id][] = $val->name;
			}
		}
		$agencyArray = array();
		$agencyList  = Agency::select('id', 'agency_name')->where('delete_flag', 'N')->get();
		if (!empty($agencyList)) {
			foreach ($agencyList as $keys) {
				$agencyArray[$keys->id] = $keys->agency_name;
			}
		}


		$keys = '';
		if ($explode[0] == 'record') {
			if ($explode[1] == 'full_name') {
				$keys = 'CONCAT(patient_master.first_name,patient_master.last_name) as full_name';
				$keys = DB::raw('CONCAT_WS(" ",patient_master.first_name," ",patient_master.last_name) as full_name');
			}
			if ($explode[1] == 'full_address') {
				$keys = DB::raw('CONCAT_WS("",patient_master.address1,"-",patient_master.address2,"-",patient_master.city,"-",patient_master.state,"-",patient_master.county,"-",patient_master.zip_code) as full_address');
			}
			if ($explode[1] != 'full_name' && $explode[1] != 'full_address' && $explode[1] != 'email') {
				$keys = 'patient_master.' . $explode[1];
			}
			$keys = $keys;
		}


		if ($keys == '') {
			$keys = 'patient_master.id';
		}
		$query = Patient::select($keys);

		$query = $query->where('patient_master.id', $user_id)->first();

		if ($explode[1] == 'dob') {
			$dates = '';
			if ($query[$explode[1]] != '') {
				$dates = date('m/d/Y', strtotime($query[$explode[1]]));
			}
			$final[$key] = $dates;
		} else {
			if ($explode[1] == 'service_id') {

				$subs = Master::selectRaw('GROUP_CONCAT(name) as name')->whereRaw('id IN(' . $query[$explode[1]] . ')')->where('del_flag', 'N')->first();

				$final[$key] = isset($subs->name) ? $subs->name : "";
			} else if ($explode[1] == 'agency_id') {
				$ahency = $query[$explode[1]];
				$final[$key] = isset($agencyArray[$ahency]) ? $agencyArray[$ahency] : "";
			} else {
				$final[$key] = $query[$explode[1]];
			}
		}



		return $final;
	}
	public static function documentInsert($tid, $documentId, $action, $groupId, $permission)
	{

		$inaction = $actions = json_decode($action, true);

		$actions = serialize($actions);
		$getCode = DocumentSentReport::select('caregiver_code')->where('id', $documentId)->where('status', 'Pending')->where('document_submit_status', 0)->first();
		if (isset($getCode->caregiver_code) && $getCode->caregiver_code != '') {
			$data_insert = array(
				'document_report_id' => $documentId,
				'template_id' => $tid,
				'user_id' => $getCode->caregiver_code,
				'data' => $actions,
				'created_date' => date('Y-m-d H:i:s')

			);

			$insert = new DocusignDetail($data_insert);
			$insert->save();
			$insertId = $insert->id;
			if ($insertId) {
				$return = Self::regeneratethepdf($insertId, $tid, $getCode->caregiver_code, $documentId, $groupId);
			} else {
				$return = 0;
			}
		} else {
			$return = 0;
		}


		return $return;
	}


	public static function regeneratethepdf($insert, $tid, $caregiver_code, $documentId, $groupId, $type = "")
	{

		if ($type != '') {

			$mainResponse = PatientDocusignDetail::where('id', $insert)->first();
		} else {
			$mainResponse = DocusignDetail::where('id', $insert)->where('del_flag', 'N')->first();
		}


		$inaction = unserialize($mainResponse->data);

		$headers = array();
		foreach ($inaction as $obj) {
			if (isset($obj['permission'])) {
				$headers = $obj['permission'];
			}
		}

		$conditionalField = array();
		//	echo "<pre>";
		//	print_r($headers);
		if (!empty($headers)) {
			foreach ($headers as  $obj) {
				if ($obj["type"] == 'checkbox') {
					$conditionalField[$obj["ReceiverDivId"]] = $obj["SenderDivId"];
				} else {
					$conditionalField[$obj["ReceiverDivId"]] = $obj["SenderId"];
				}
			}
		}

		$elementValue = array();
		foreach ($inaction as $obj) {
			$elementValue[$obj["id"]] = $obj;
		}

		/*get Pdf File*/
		$documents = Template::where('id', $tid)->where('del_flag', 'N')->first();
		if ($type != '') {
			$spurcePdf = PatientDocumentSentReportService::getDetailsById($documentId);
		} else {
			$spurcePdf = DocumentSentReport::select('sourceFile', 'caregiver_code', 'main_intakeId')->where('id', $documentId)->where('del_flag', 'N')->first();
		}
		//$spurcePdf = DocumentSentReport::select('sourceFile', 'caregiver_code', 'main_intakeId')->where('id', $documentId)->where('del_flag', 'N')->first();

		/*end */

		$inputPath = public_path() . "/dosusinguploads/docusign/" . $spurcePdf->sourceFile;

		$pdf = new PDF(null, 'px');

		$pdf->numPages = $pdf->setSourceFile($inputPath);
		$templateFields = array($documents->docWidth);

		$signed = $updatedFields = $editted = false;

		foreach (range(1, $pdf->numPages, 1) as $page) {

			$rotate = false;
			$degree = 0;
			//try {
			$pdf->_tplIdx = $pdf->importPage($page);
			//	}

			/*catch(\Exception $e) {
					echo "Message:".$e->getMessage();
				  return false; 
				}*/

			foreach ($inaction as $action) {
				if (((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}

			$size = $pdf->getTemplateSize($pdf->_tplIdx);
			$scale = round($size['w'] / $documents->docWidth, 3);

			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate' => $degree), true);
			$pdf->useTemplate($pdf->_tplIdx);


			foreach ($inaction as $action) {

				if (((int) $action['page']) === $page) {

					if ($action['type'] == "image") {

						if ($action['text'] != '') {
							$urlim = str_replace('https://web.exmedc.com/', '', $action['text']);
							$tesd = public_path() . '/' . $urlim;
							$editted = true;
							//$imageArray = base64_encode($action['text']);
							$imageArray = base64_encode($tesd);

							$imgdata = base64_decode($imageArray);

							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 4, $scale), 80, self::scale($action['height'] - 10, $scale), '', '', '', false);
						}
					} else if ($action['type'] == "text") {

						$editted = true;
						if (isset($action['text']) && $action['text'] != '') {
							$text = $action['text'];
						} else {
							$explodes = explode('_', $action['id']);
							if ($explodes[0] == 'intake') {
								$text = '';
							} else if ($explodes[0] == 'caregiver') {
								$text = '';
							} else {
								if (trim($action['placeHolder']) != 'Textbox' && trim($action['placeHolder']) != 'Date Signed') {
									$text = $action['placeHolder'];
								} else {
									$text = '';
								}
							}
						}
						$font = 9;
						if (isset($action['font']) && $action['font'] != NULL  && $action['font'] != "undefined") {
							$font = $action['font'];
						}
						if ($tid == 27) {

							//	$font = 20;
						}


						$pdf->setFontSize($font);

						$showText = true;

						if (isset($conditionalField[$action['id']])) {
							$showText = false;
							$RecivedObj = isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] : " ";

							if (isset($RecivedObj['type']) && $RecivedObj['type'] == "radio") {
								if ($RecivedObj["checked"] != "") {
									$showText = true;
								}
							} elseif (isset($RecivedObj['type']) && $RecivedObj['type'] == "dropdown") {
								foreach ($RecivedObj['permission'] as $permission) {
									if ($permission['SenderId'] == $RecivedObj['id'] &&  $permission['value'] == $RecivedObj["text"]) {
										$showText = true;
									}
								}
							} else if (isset($RecivedObj['type']) && $RecivedObj['type'] == "checkbox") {
								if ($RecivedObj["checked"] != "") {
									$showText = true;
								}
							}
						}
						if ($showText) {


							//$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
							if (trim($action['placeHolder']) == 'SSN') {
								$pdf->writeHTMLCell(50, self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale) - 3, str_replace("%22", '"', $text), 0, 0, false, true, '', true);
							} else {
								if (isset($action['textsmall']) && $action['textsmall'] == 1) {
									$ypos = self::scale($action['yPos'] + 11, $scale);
								} else {
									$ypos = self::scale($action['yPos'] + 3, $scale);
								}
								$pdf->writeHTMLCell(self::scale($action['width'], $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), $ypos, str_replace("%22", '"', $text), 0, 0, false, true, '', true);
							}
						}
					} elseif ($action['type'] == "dropdown") {

						$editted = true;
						if ($action['text'] != 'Select') {
							$text =  $action['text'];
						} else {
							$text =  '';
						}
						//	$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
						$pdf->writeHTMLCell(self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 6, $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true);
					} elseif ($action['type'] == "radio") {

						$name = $action['name'];
						$checked = false;
						if ($action['checked'] == '1') {
							$checked = true;
							$pdf->Circle(self::scale($action['xPos'], $scale) + 5, self::scale($action['yPos'], $scale) + 5, 5, 0, 360, 'F');
						}
					} elseif ($action['type'] == "checkbox") {

						$name = $action['name'];
						$checked = false;
						if ($action['checked'] == '1') {
							$checked = true;
						}

						if ($checked) {
							$pdf->Rect(self::scale($action['xPos'] + 2, $scale), self::scale($action['yPos'] + 5, $scale), 10, 10, 'F');

							//		$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),10, 10, 'F');
						}
					}
				}
			}
		}

		$outputName = $documentId . time() . ".pdf";
		$outputPath = public_path() . "/dosusinguploads/docusign/" . $outputName;
		$pdf->Output($outputPath, 'F');
		$browserDetails =  $user_agent = $_SERVER['HTTP_USER_AGENT'];
		if ($type != '') {
			$final_array = array('completed_on' => date('Y-m-d H:i:s'), 'browser' => $browserDetails, 'pdf_generate' => $outputName, 'document_submit_status' => 1, 'status' => 'Completed');
			$updates = PatientDocumentSentReportService::updateNew($final_array, array('id' => $documentId));

			$second = PatientDocumentSentReportService::nextEsign($tid, $groupId);
			if (isset($second->id)) {
				$updateNew = array(
					'status' => 'Pending',
					'sourceFile' => $outputName
				);

				PatientDocumentSentReportService::updateNew($updateNew, array('id' => $second->id));
			}
		} else {
			$updates = DocumentSentReport::where('id', $documentId)->update(array('completed_on' => date('Y-m-d H:i:s'), 'browser' => $browserDetails, 'pdf_generate' => $outputName, 'document_submit_status' => 1, 'status' => 'Completed'));
			$second = DocumentSentReport::select('id', 'sender_name', 'sent_on', 'mobile')->where('templete_id', $tid)->where('groupId', $groupId)->where('del_flag', 'N')->where('status', '=', '')->first();

			if ($second != '') {
				$updateNew = array(
					'status' => 'Pending',
					'sourceFile' => $outputName
				);

				DocumentSentReport::where('id', $second->id)->update($updateNew);
				$email = $second->sender_name;
				$subject = 'Esign - ' . $documents->template_name . '<br>';
				// $messages = '';
				// $messages .= 'Template Name : ' . $documents->template_name . '<br>';
				// $messages .= 'Link : ' . URL::to('/') . '/sign/' . $second->id . '/' . $groupId;

				$emailData = array(
					'link' => URL::to('/') . '/sign/' . $second->id . '/' . $groupId,
					'template_name' => $documents->template_name
				);
				$messages = Utility::getHtmlContent('email_template.email_esign_regenrate_pdf',$emailData);
				try {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "EMC Rep")
							->subject($subject)->html($messages);
						// $message->cc('leonora@nybestmedicals.com');
						// $message->bcc('leonora@nybestmedicals.com', "leonora");
						$message->bcc('info@nybestmedicals.com', "leonora");
					});
				} catch (\Throwable $th) {
					//throw $th;
				}


				if ($second->sent_on == 'Patient') {
					//Common::sendTextSMS($second->mobile,$messages);
				}
			}
			$getSignLeft = DocumentSentReport::where('groupId', $groupId)->where('status', 'Pending')->count();
			if ($getSignLeft == 0) {
				$getAllDetailsBew  = DocumentSentReport::select('templete_id', 'completed_on', 'browser')->where('groupId', $groupId)->where('status', 'Completed')->orderBy('id', 'desc')->first();
				$tempalte_list = Template::select('template_name')->where('del_flag', 'N')->where('id', $getAllDetailsBew->templete_id)->first();

				$greAllDetails = Record::getAllDetails($spurcePdf->main_intakeId);
				//$messages = $greAllDetails->fname.' '.$greAllDetails->lname.' esign completed for  this patient '.$greAllDetails->first_name.' '.$greAllDetails->last_name.'('.$greAllDetails->id.')';

				$subject = $tempalte_list->template_name . ' Esign Completed -' . $greAllDetails->first_name . ' ' . $greAllDetails->last_name . '(' . $greAllDetails->id . ')';
				// $messages = 'Hello ' . $greAllDetails->fname . ' ' . $greAllDetails->lname . ',<br>';
				// $messages .= 'Patient Id : ' . $greAllDetails->id . '<br>';
				// $messages .= 'Patient Name : ' . $greAllDetails->first_name . ' ' . $greAllDetails->last_name . '<br>';
				// $messages .= 'Completed Date : ' . date('m/d/Y h:i A', strtotime($getAllDetailsBew->completed_on)) . '<br>';
				// $messages .= 'Browser : ' . $getAllDetailsBew->browser . '<br>';

				$newURK = public_path() . '/dosusinguploads/docusign/' . $outputName;
				$email = $greAllDetails->email;

				$emailData = array(
					'full_name' => $greAllDetails->fname . ' ' . $greAllDetails->lname,
					'patient_id' => $greAllDetails->id,
					'patient_name' => $greAllDetails->first_name . ' ' . $greAllDetails->last_name,
					'completed_on' => date('m/d/Y h:i A', strtotime($getAllDetailsBew->completed_on)),
					'browser' => $getAllDetailsBew->browser,
				);
				
				try {
					$messages = Utility::getHtmlContent('email_template.email_esign_complete_document',$emailData);
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages, $newURK) {
						$message->to($email, "EMC Rep") 
							->subject($subject)->html($messages);

						// $message->bcc('leonora@nybestmedicals.com', "leonora");
						// $message->bcc('info@nybestmedicals.com', "leonora");
						$message->attach($newURK);
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}
		return $insert;
	}
	public static function orientation($width, $height)
	{
		if ($width > $height) {
			return "L";
		} else {
			return "P";
		}
	}

	/**
	 * Scale element dimension
	 * 
	 * @param   int $dimension
	 * @return  int
	 */
	public static function scale($dimension, $scale)
	{
		if ($dimension != '' && $scale != '') {
			return round($dimension * $scale);
		}
	}

	/**
	 * Scale position on axis
	 * 
	 * @param   int $position
	 * @return  int
	 */
	public static function adjustPositions($position)
	{
		return round($position - 83);
	}

	/**
	 *
	 *download code in after submit in mobile phone
	 *
	 */
	public function getDownload($file)
	{
		$headers = array(
			'Content-Type: application/pdf',
		);
		return response()->download($file, 'filename.pdf', $headers);
	}


	public static function tempregenerate()
	{

		$insert = 4047;
		$documentId = 6493;
		$tid = 30;
		$groupId = '615ae8d5e93aa';
		$templsd = Template::select('response', 'upload_document')->where('id', $tid)->first();
		//$inactions = unserialize($templsd->response);


		$mainResponse = DocusignDetail::where('id', $insert)->where('del_flag', 'N')->first();

		$inaction = unserialize($mainResponse->data);

		$headers = array();



		$conditionalField = array();
		//	echo "<pre>";
		//	print_r($headers);
		if (!empty($headers)) {
			foreach ($headers as  $obj) {
				if ($obj["type"] == 'checkbox') {
					$conditionalField[$obj["ReceiverDivId"]] = $obj["SenderDivId"];
				} else {
					$conditionalField[$obj["ReceiverDivId"]] = $obj["SenderId"];
				}
			}
		}

		$elementValue = array();
		foreach ($inaction as $obj) {
			$elementValue[$obj["id"]] = $obj;
		}

		/*get Pdf File*/
		$documents = Template::where('id', $tid)->where('del_flag', 'N')->first();
		$spurcePdf = DocumentSentReport::select('sourceFile', 'caregiver_code')->where('id', $documentId)->where('del_flag', 'N')->first();

		/*end */

		$inputPath = public_path() . "/dosusinguploads/docusign/" . $spurcePdf->sourceFile;


		$pdf = new PDF(null, 'px');

		$pdf->numPages = $pdf->setSourceFile($inputPath);
		$templateFields = array($documents->docWidth);
		$signed = $updatedFields = $editted = false;

		foreach (range(1, $pdf->numPages, 1) as $page) {
			$rotate = false;
			$degree = 0;
			try {
				$pdf->_tplIdx = $pdf->importPage($page);
			} catch (\Exception $e) {
				return false;
			}

			foreach ($inaction as $action) {
				if (((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}

			$size = $pdf->getTemplateSize($pdf->_tplIdx);
			$scale = round($size['w'] / $documents->docWidth, 3);

			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate' => $degree), true);
			$pdf->useTemplate($pdf->_tplIdx);

			foreach ($inaction as $action) {

				if (((int) $action['page']) === $page) {

					if ($action['type'] == "image") {

						if ($action['text'] != '') {
							$urlim = str_replace('https://web.exmedc.com/', '', $action['text']);
							$tesd = public_path() . '/' . $urlim;

							$editted = true;
							$imageArray = base64_encode($tesd);

							$imgdata = base64_decode($imageArray);

							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 4, $scale), 80, self::scale($action['height'] - 10, $scale), '', '', '', false);
						}
					} else if ($action['type'] == "text") {

						$editted = true;
						if (isset($action['text']) && $action['text'] != '') {
							$text = $action['text'];
						} else {
							$explodes = explode('_', $action['id']);
							if ($explodes[0] == 'intake') {
								$text = '';
							} else if ($explodes[0] == 'caregiver') {
								$text = '';
							} else {
								if (trim($action['placeHolder']) != 'Textbox' && trim($action['placeHolder']) != 'Date Signed') {
									$text = $action['placeHolder'];
								} else {
									$text = '';
								}
							}
						}

						$font = 8;
						if ($tid == 27) {
							$font = 20;
						}
						$pdf->setFontSize($font);

						$showText = true;

						if (isset($conditionalField[$action['id']])) {
							$showText = false;
							$RecivedObj = isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] : " ";

							if (isset($RecivedObj['type']) && $RecivedObj['type'] == "radio") {
								if ($RecivedObj["checked"] != "") {
									$showText = true;
								}
							} elseif (isset($RecivedObj['type']) && $RecivedObj['type'] == "dropdown") {
								foreach ($RecivedObj['permission'] as $permission) {
									if ($permission['SenderId'] == $RecivedObj['id'] &&  $permission['value'] == $RecivedObj["text"]) {
										$showText = true;
									}
								}
							} else if (isset($RecivedObj['type']) && $RecivedObj['type'] == "checkbox") {
								if ($RecivedObj["checked"] != "") {
									$showText = true;
								}
							}
						}

						if ($showText) {

							if (trim($action['placeHolder']) == 'SSN') {
								$pdf->writeHTMLCell(50, self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 8, $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true);
							} else {
								$pdf->writeHTMLCell(self::scale($action['width'], $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 3, $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true);
							}
						}
					} elseif ($action['type'] == "dropdown") {

						$editted = true;
						if ($action['text'] != 'Select') {
							$text =  $action['text'];
						} else {
							$text =  '';
						}
						$pdf->writeHTMLCell(self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale), self::scale($action['yPos'] + 6, $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true);
					} elseif ($action['type'] == "radio") {

						$name = $action['name'];
						$checked = false;
						if ($action['checked'] == '1') {
							$checked = true;
							$pdf->Circle(self::scale($action['xPos'], $scale) + 5, self::scale($action['yPos'], $scale) + 5, 5, 0, 360, 'F');
						}
					} elseif ($action['type'] == "checkbox") {

						$name = $action['name'];
						$checked = false;
						if ($action['checked'] == '1') {
							$checked = true;
						}

						if ($checked) {

							$pdf->Rect(self::scale($action['xPos'] + 2, $scale), self::scale($action['yPos'] + 5, $scale), 10, 10, 'F');
						}
					}
				}
			}
		}
		$outputName = $documentId . time() . ".pdf";
		$outputPath = public_path() . "/dosusinguploads/docusign/" . $outputName;
		$pdf->Output($outputPath, 'F');
		$browserDetails =  $user_agent = $_SERVER['HTTP_USER_AGENT'];
		echo $outputName;
		die();

		//$updates = DocumentSentReport::where('groupId',$groupId)->update(array('pdf_generate'=>$outputName,'document_submit_status'=>1,'status'=>'Completed'));
		$getSignLeft = DocumentSentReport::where('groupId', $groupId)->where('status', 'Pending')->count();
		if ($getSignLeft == 0) {
			$getAllDetailsBew  = DocumentSentReport::select('completed_on')->where('groupId', $groupId)->where('status', 'Completed')->orderBy('id', 'desc')->first();
			$greAllDetails = Record::getAllDetails($spurcePdf->caregiver_code);
			//$messages = $greAllDetails->fname.' '.$greAllDetails->lname.' esign completed for  this patient '.$greAllDetails->first_name.' '.$greAllDetails->last_name.'('.$greAllDetails->id.')';

			$subject = 'Esign Completed -' . $greAllDetails->first_name . ' ' . $greAllDetails->last_name . '(' . $greAllDetails->id . ')';
			// $messages = 'Hello ' . $greAllDetails->fname . ' ' . $greAllDetails->lname . ',<br>';
			// $messages .= 'Patient Id : ' . $greAllDetails->id . '<br>';
			// $messages .= 'Patient Name : ' . $greAllDetails->first_name . ' ' . $greAllDetails->last_name . '<br>';
			// $messages .= 'Completed Date : ' . $getAllDetailsBew->completed_on . '<br>';

			$newURK = public_path() . '/dosusinguploads/docusign/' . $outputName;
			$emailData = array(
				'full_name' => $greAllDetails->fname . ' ' . $greAllDetails->lname,
				'patient_id' => $greAllDetails->id,
				'patient_name' => $greAllDetails->first_name . ' ' . $greAllDetails->last_name,
				'completed_on' => date('m/d/Y h:i A', strtotime($getAllDetailsBew->completed_on)),
				'browser' => $getAllDetailsBew->browser,
			);
			$messages = Utility::getHtmlContent('email_template.email_esign_complete_document',$emailData);
			$html = '';
			try {
				$mail = Mail::mailer('second')->send([], [], function ($message) use ($subject, $messages, $newURK,$greAllDetails) {
					$message->to($greAllDetails->email, "User")
						->subject($subject)->html($messages);
					//	$message->bcc('hiten@virtualheight.com', "hiten");
					$message->attach($newURK);
				});
			} catch (\Throwable $th) {
				//throw $th;
			}
		}

		return $insert;
	}
	public static function PatientdocumentInsert($tid, $documentId, $action, $groupId, $permission)
	{
		$inaction = $actions = json_decode($action, true);

		$actions = serialize($actions);
		$getCode = PatientDocumentSentReportService::getCaregiverCodeByIdWithPending($documentId);
		if (isset($getCode->caregiver_code) && $getCode->caregiver_code != '') {
			$data_insert = array(
				'document_report_id' => $documentId,
				'template_id' => $tid,
				'user_id' => $getCode->caregiver_code,
				'data' => $actions,
				'created_date' => date('Y-m-d H:i:s'),
				'type' => 'nybest'

			);

			$insert = new PatientDocusignDetail($data_insert);
			$insert->save();
			$insertId = $insert->id;
			if ($insertId) {
				$return = Self::regeneratethepdf($insertId, $tid, $getCode->caregiver_code, $documentId, $groupId, "nybest");
			} else {
				$return = 0;
			}
		} else {
			$return = 0;
		}


		return $return;
	}

	public static function getEsignImagesStorage($images,$updateSelectType){
		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			if($updateSelectType ==1){
				$url = url('/').'/'.self::ESIGN_PATIENT_WRITE_DOCUMENT.'/'.$images;
			}else{
				$url = url('/').'/'.self::DOCUSIGN_FOLDER.'/'.$images;
			}
			
		}else{
			$expiry = Carbon::now()->addMinutes(10);
			if($updateSelectType ==1){
				
				$path = self::ESIGN_PATIENT_WRITE_DOCUMENT.'/' . $images;
				$url = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}else{
				$path = self::DOCUSIGN_FOLDER.'/' . $images;
				$url = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}
		}

		return $url;
	}
}
