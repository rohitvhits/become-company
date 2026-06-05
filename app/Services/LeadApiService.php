<?php
namespace App\Services;

use App\Model\LeadApi;
use App\Helpers\Utility;
class LeadApiService
{

    protected const COMMON_DATE_FORMAT_YMD = "Y-m-d H:i:s";

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date(self::COMMON_DATE_FORMAT_YMD);

        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'] ?? "";
        }

        return LeadApi::create($data);
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date(self::COMMON_DATE_FORMAT_YMD);

        if (isset($auth['id'])) {
            $data['updated_by'] = $auth['id'];
        }

        return LeadApi::where($where)->update($data);
    }

    public function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        return LeadApi::where($where)->update($data);
    }

    /**
     * Get filtered list of lead coordination data
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList($filters = [],$type="")
    {
        $query = LeadApi::select(
            'lead_api.id',
            'lead_api.first_name',
            'lead_api.last_name',
            'lead_api.email',
            'lead_api.phone',
            'lead_api.agency_name',
            'lead_api.service_requested',
            'lead_api.appointment_date',
            'lead_api.appointment_time',
            'lead_api.appointment_address',
            'lead_api.created_date',
            'master_table.name'
        );
        $query->leftjoin('master_table',function($join){
            $join->on('master_table.id','=','lead_api.app_referral_id');
        })->where('master_table.master_type_fk',34)->where('master_table.del_flag',"N");
        // Apply filters
        if (!empty($filters['full_name'])) {
            $fullName = $filters['full_name'];
            $query->where(function ($q) use ($fullName) {
                $q->where('lead_api.first_name', 'LIKE', "%{$fullName}%")
                  ->orWhere('lead_api.last_name', 'LIKE', "%{$fullName}%")
                  ->orWhereRaw("CONCAT(lead_api.first_name, ' ', lead_api.last_name) LIKE ?", ["%{$fullName}%"]);
            });
        }

        if (!empty($filters['phone'])) {
            $query->where('lead_api.phone', 'LIKE', "%{$filters['phone']}%");
        }

        if (!empty($filters['agency_name'])) {
            $query->where('lead_api.agency_name', 'LIKE', "%{$filters['agency_name']}%");
        }

        if (!empty($filters['service_requested'])) {
            $query->where('lead_api.service_requested', 'LIKE', "%{$filters['service_requested']}%");
        }

        if (!empty($filters['appointment_date_from']) && !empty($filters['appointment_date_to'])) {
            $query->whereBetween('lead_api.appointment_date', [Utility::convertYMD($filters['appointment_date_from']), Utility::convertYMD($filters['appointment_date_to'])]);
        }

        if (!empty($filters['created_date_from']) && !empty($filters['created_date_to'])) {
            $query->whereBetween('lead_api.created_date', [Utility::convertYMD($filters['created_date_from']), Utility::convertYMD($filters['created_date_to'])]);
        }

        if($type !=""){
            return $query->orderBy('lead_api.id', 'desc')->get();
        }else{
            return $query->orderBy('lead_api.id', 'desc')->paginate(50);
        }
    }

}
