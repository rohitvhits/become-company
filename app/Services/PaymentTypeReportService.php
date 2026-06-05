<?php

namespace App\Services;

use App\Model\Patient;
use App\Master;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
class PaymentTypeReportService
{
    /**
     * Get payment types from Master table where master_type_fk = 17
     */
    public function getPaymentTypesMaster()
    {
        return Master::select('id', 'name')
            ->where('del_flag', 'N')
            ->where('master_type_fk', 17)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get payment type counts with optional filters
     */
    public function getPaymentTypeCounts($agency_fk = null, $payment_type = null, $search_term = null, $appointment_date = null,$payment_type_status="")
    {
        $auth = auth()->user();

        $query = Patient::select('patient_master.payment_type', DB::raw('count(*) as total'), 'master_table.name as payment_type_name')
            ->join('master_table', function($join) {
                $join->on('patient_master.payment_type', '=', 'master_table.id')
                    ->where('master_table.del_flag', 'N')
                    ->where('master_table.master_type_fk', 17);
            })
            ->where('patient_master.deleted_flag', 'N')
            ->where(function ($query) {
                $query->where('patient_master.payment_type', '!=', 0)
                    ->orWhereNotNull('patient_master.payment_type');
            });

        // Apply agency restrictions based on user type
        if (!in_array($auth->user_type_fk, array(3, 4, 184))) {
            $agencyIds = Utility::getUserIdWiseAgency($auth->id);
            $agencyIds[] = $auth->agency_fk;
            $query->whereIn('patient_master.agency_id', $agencyIds);
          
        } elseif ($agency_fk) {
            $query->where('patient_master.agency_id', $agency_fk);
        }
        if ($auth->record_access != 'All') {
            $query->where('patient_master.type', $auth->record_access);
        }
        if($auth->restrict_user==1){
            $query->where('patient_master.created_by',$auth->id);

		}
        // Apply payment type filter
        if ($payment_type !== null && $payment_type !== '') {
            $query->where('patient_master.payment_type', $payment_type);
        }
        // Apply appointment date filter
        if (!empty($appointment_date)) {
            $explode = explode('-', $appointment_date);

            $query->where(function ($q) use ($explode) {
                if (isset($explode[1])) {
                    // Range condition
                    $startDate = date('Y-m-d', strtotime(trim($explode[0])));
                    $endDate   = date('Y-m-d', strtotime(trim($explode[1])));

                    $q->whereBetween(DB::raw('DATE_FORMAT(patient_master.appointment_date, "%Y-%m-%d")'), [$startDate, $endDate])
                    ->orWhereBetween(DB::raw('DATE_FORMAT(patient_master.telehealth_date_time, "%Y-%m-%d")'), [$startDate, $endDate]);
                } else {
                    // Single date condition
                    $date = date('Y-m-d', strtotime(trim($explode[0])));

                    $q->whereDate('patient_master.appointment_date', $date)
                    ->orWhereDate('patient_master.telehealth_date_time', $date);
                }
            });
        }

        $query->whereIn('patient_master.status',['processing','completed','Telehealth Completed','Form Completed']);
        if($payment_type_status !=""){
            $query->where('patient_master.status',$payment_type_status);
        }
        return $query->groupBy('patient_master.payment_type', 'master_table.name')
            ->orderBy('master_table.name', 'asc')
            ->get();
    }

    /**
     * Get patients by payment type with filters and pagination
     */
    public function getPatientsByPaymentType($agency_fk = null, $payment_type = null, $search_term = null, $appointment_date = null, $paginate = "",$payment_type_status="")
    {
        $auth = auth()->user();

        $query = Patient::select(
            'patient_master.id',
            'patient_master.patient_code',
            'patient_master.first_name',
            'patient_master.middle_name',
            'patient_master.last_name',
            'patient_master.mobile',
            'patient_master.phone',
            'patient_master.type',
            'patient_master.payment_type',
            'patient_master.dob',
            'patient_master.gender',
            'patient_master.service_id',
            'agency.agency_name',
            'master_table.name as payment_type_name',
            'appointment_date','telehealth_date_time',
            'patient_master.status'
        )
        ->leftJoin('agency', function ($join) {
            $join->on('patient_master.agency_id', '=', 'agency.id')
                ->where('agency.delete_flag', 'N');
        })
        ->join('master_table', function($join) {
            $join->on('patient_master.payment_type', '=', 'master_table.id')
                ->where('master_table.del_flag', 'N')
                ->where('master_table.master_type_fk', 17);
        })
        ->where('patient_master.deleted_flag', 'N')
        ->where(function ($query) {
            $query->where('patient_master.payment_type', '!=', 0)
                ->orWhereNotNull('patient_master.payment_type');
        });
        if ($auth->record_access != 'All') {
            $query->where('patient_master.type', $auth->record_access);
        }
        // Apply agency restrictions based on user type
        if (!in_array($auth->user_type_fk, array(3, 4, 184))) {
            $agencyIds = Utility::getUserIdWiseAgency($auth->id);
            $agencyIds[] = $auth->agency_fk;
            $query->whereIn('patient_master.agency_id', $agencyIds);
        } elseif ($agency_fk) {
            $query->where('patient_master.agency_id', $agency_fk);
        }

        // Apply payment type filter
        if ($payment_type !== null && $payment_type !== '') {
            $query->where('patient_master.payment_type', $payment_type);
        }

        if (!empty($appointment_date)) {
            $explode = explode('-', $appointment_date);

            $query->where(function ($q) use ($explode) {
                if (isset($explode[1])) {
                    // Range condition
                    $startDate = date('Y-m-d', strtotime(trim($explode[0])));
                    $endDate   = date('Y-m-d', strtotime(trim($explode[1])));

                    $q->whereBetween(DB::raw('DATE_FORMAT(patient_master.appointment_date, "%Y-%m-%d")'), [$startDate, $endDate])
                    ->orWhereBetween(DB::raw('DATE_FORMAT(patient_master.telehealth_date_time, "%Y-%m-%d")'), [$startDate, $endDate]);
                } else {
                    // Single date condition
                    $date = date('Y-m-d', strtotime(trim($explode[0])));

                    $q->whereDate('patient_master.appointment_date', $date)
                    ->orWhereDate('patient_master.telehealth_date_time', $date);
                }
            });
        }


        // Apply search filter
        if ($search_term) {
            $query->where(function ($q) use ($search_term) {
                $q->where('patient_master.first_name', 'like', '%' . $search_term . '%')
                    ->orWhere('patient_master.last_name', 'like', '%' . $search_term . '%')
                    ->orWhere('patient_master.patient_code', 'like', '%' . $search_term . '%')
                    ->orWhere('patient_master.mobile', 'like', '%' . $search_term . '%')
                    ->orWhere('patient_master.full_name', 'like', '%' . $search_term . '%');
            });
        }
        $query->whereIn('patient_master.status',['processing','completed','Telehealth Completed','Form Completed']);

        if($auth->restrict_user==1){
            $query->where('patient_master.created_by',$auth->id);

		}
        if($payment_type_status !=""){
            $query->where('patient_master.status',$payment_type_status);
        }
        if($paginate == ""){
            return $query->orderBy('master_table.name', 'asc')
            ->orderBy('patient_master.first_name', 'asc')
            ->get();
        }else{
            return $query->orderBy('master_table.name', 'asc')
            ->orderBy('patient_master.first_name', 'asc')
            ->simplePaginate(50);
        }
    }
}
