<?php

namespace App\Services;

use App\Model\HubCompany;
use App\Helpers\Utility;

class HubCompanyService
{
	public  function save($data)
	{
		$userId = Auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		if ($userId) {
			$data['created_by'] = $userId['id'];
		}
		$insert = new HubCompany($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public  function update($data, $where)
	{
		$userId = Auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if ($userId) {
			$data['updated_by'] = $userId['id'];
		}
		$update = HubCompany::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$userId = Auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $userId['id'];
		$update = HubCompany::where($where)->update($data);
		return $update;
	}


	public static function getData($agency_name, $email, $phone, $city, $isSMS)
	{
		$temp = 'delete_flag = "N" and show_hub=1';
		if ($agency_name != '') {
			$temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
		}
		if ($email != '') {
			$temp .= ' and email  LIKE "%' . $email . '%"';
		}
		if ($phone != '') {
			$temp .= ' and phone  LIKE "%' . $phone . '%"';
		}
		if ($city != '') {
			$temp .= ' and city  LIKE "%' . $city . '%"';
		}
		if ($isSMS != '') {
			$temp .= ' and is_sms ="' . $isSMS . '"';
		}

		$query = HubCompany::whereRaw($temp)->orderBy('id', 'DESC')->paginate(10);
		return $query;
	}
	public static function nyBestAgencyList($agency_name, $email, $phone, $city)
	{
		$temp = 'delete_flag = "N" and service_md_appointment="1"';
		if ($agency_name != '') {
			$temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
		}
		if ($email != '') {
			$temp .= ' and email  LIKE "%' . $email . '%"';
		}
		if ($phone != '') {
			$temp .= ' and phone  LIKE "%' . $phone . '%"';
		}
		if ($city != '') {
			$temp .= ' and city  LIKE "%' . $city . '%"';
		}

		$query = HubCompany::whereRaw($temp)->orderBy('agency_name', 'asc')->paginate(50);
		return $query;
	}
	public static function getMyAgency()
	{
		$currentUser = auth()->user();
		$query = HubCompany::where('delete_flag', 'N'); //->orderBy('agency_name','asc');

		if (!in_array($currentUser->user_type_fk, array("184", '4'))) {
			$query = $query->where("id", $currentUser->agency_fk);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}
	public static function getDataExport($agency_name, $email, $phone, $city, $isSMS)
	{
		$temp = 'delete_flag = "N" ';
		if ($agency_name != '') {
			$temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
		}
		if ($email != '') {
			$temp .= ' and email  LIKE "%' . $email . '%"';
		}
		if ($phone != '') {
			$temp .= ' and phone  LIKE "%' . $phone . '%"';
		}
		if ($city != '') {
			$temp .= ' and city  LIKE "%' . $city . '%"';
		}
		if ($isSMS != '') {
			$temp .= ' and is_sms ="' . $isSMS . '"';
		}
		$query = HubCompany::whereRaw($temp)->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAllAgencyList()
	{
		$query = HubCompany::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		return $query;
	}
	public static function getDetailsByAgencyId($id)
	{
		$query = HubCompany::where('delete_flag', 'N')->where('id', $id)->first();
		return $query;
	}

	public static function getAllDetailsbyAgencyId($id)
	{
		$query = HubCompany::where('delete_flag', 'N')->where('id', $id)->first();
		return $query;
	}
	public static function getIdById($id)
	{
		return HubCompany::whereRaw('id = "' . $id . '"')->first();
	}

	public static function getAgencyData()
	{
		return HubCompany::where('alaycare_status', '1')->get();
	}

	public static function getAgencyList()
	{
		$agencyids = Utility::getUserWiseAgency();

		if (auth()->user()->agency_fk != "") {
			$agencyids[] = auth()->user()->agency_fk;
		}
		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAgencyList2()
	{
		$agencyids = Utility::getUserWiseAgency();

		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAgencyListAlayaCare()
	{
		$agencyids = Utility::getUserWiseAgency();

		$query = HubCompany::where('delete_flag', 'N')->where('alaycare_status', 1);
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAllAgencyListWithoutAnyCondition()
	{
		return HubCompany::select('id', 'agency_name')->orderBy('agency_name', 'asc')->get();
	}

	public static function totalCountForAgencies()
	{
		return HubCompany::where('delete_flag', 'N')->count();
	}


	public static function getPatientServiceCount($locationId, $agencyId, $typeId, $from_date = "", $to_date = "")
	{
		$query = HubCompany::withCount(['patientCaregiver' => function ($q) use ($locationId, $typeId, $from_date, $to_date) {
			if ($locationId != '') {
				$q->where('location_id', '=', $locationId);
			}
			if ($typeId != '') {
				$q->where('type', $typeId);
			}
			if (!empty($from_date) && !empty($to_date)) {
				$q->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			}
		}])->withCount(['patientTotalPatient' => function ($q) use ($locationId, $typeId, $from_date, $to_date) {
			if ($locationId != '') {
				$q->where('location_id', '=', $locationId);
			}
			if ($typeId != '') {
				$q->where('type', $typeId);
			}
			if (!empty($from_date) && !empty($to_date)) {
				$q->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			}
		}]);
		if ($agencyId != "") {
			$explode = explode(',', $agencyId);
			$query->whereIn('id', $explode);
		}
		$query = $query->get();
		return $query;
	}

	public static function getAllAgencyIds($agencyId)
	{
		$query = HubCompany::where('delete_flag', 'N');
		if ($agencyId != "") {
			$explode = explode(',', $agencyId);
			$query->whereIn('id', $explode);
		}
		return $query = $query->pluck('id');
	}

	public static function totalCountForAgenciesDateWise($from_date, $to_date)
	{
		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		return $query->count();
	}

	public static function getAgencyListWithIds($ids)
	{

		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($ids)) {
			$query->whereIn('id', $ids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getHHAAgencyListById($ids)
	{
		$agencyids = Utility::getUserWiseAgency();
		$agencyids[] = $ids;

		$query = HubCompany::where('delete_flag', 'N')->where('enable_hha', '1');
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}

		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAgencyListWise()
	{
		$data['user'] = $user = auth()->user();
		$permissions = [];
		foreach ($user->roles as $role) {
			$permissions[] = $role->name;
		}
		if (!in_array('Super Admin', $permissions)) {
			$agencyids = Utility::getUserWiseAgency();
			if (auth()->user()->agency_fk != "") {
				$agencyids[] = auth()->user()->agency_fk;
			}
		}
		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	/*******************Third party related */
	public static function getAgencyListByAgencyToken()
	{
		return HubCompany::select('agency.id', 'agency.agency_name')
			->join('agency_token', function ($join) {
				$join->on('agency_token.agency_id', '=', 'agency.id');
			})->where('agency.delete_flag', 'N')->where('agency_token.delete_flag', 'N')->groupBy('agency.id')->orderBy('agency.agency_name', 'asc')->get();
	}

	public static function getAgencyListWithUserAgency()
	{
		$user = auth()->user();

		$agencyids = Utility::getUserWiseAgency();
		if ($user->agency_fk != "") {
			$agencyids[] = $user->agency_fk;
		}

		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($agencyids)) {

			$query->whereIn('id', $agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAgencyListHub()
	{
		$agencyids = Utility::getUserWiseAgency();

		if (auth()->user()->agency_fk != "") {
			$agencyids[] = auth()->user()->agency_fk;
		}
		$query = HubCompany::where('delete_flag', 'N');
		if (!empty($agencyids)) {
			$query->whereIn('id', $agencyids);
		}
		$query = $query->where('show_hub', 1)->orderBy('agency_name', 'asc')->get();
		return $query;
	}

	public static function getAllAgencyPluck($company_name = null)
	{
		return HubCompany::select('id', 'agency_name')
			->when($company_name, function ($query, $company_name) {
				return $query->where('agency_name', $company_name);
			})
			->orderBy('agency_name', 'asc')->pluck('agency_name', 'id');
	}
}
