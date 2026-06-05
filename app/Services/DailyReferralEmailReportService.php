<?php

namespace App\Services;

use App\Model\PatientWiseServiceRequest;
use App\Model\PatientServiceRequest;
use App\Model\Patient;
use App\Model\Resolution;
use App\Agency;
use App\Master;
use App\Services\PatientService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyReferralEmailReportService
{
    /**
     * Apply agency and service filters to query
     */
    private function applyFilters($query, $agencyIds = [], $serviceIds = [], $usePatientWiseService = false)
    {
        // Apply agency filter
        if (!empty($agencyIds)) {
            if ($usePatientWiseService) {
                $query->whereHas('patientServiceRequest.patient', function ($q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            } else {
                $query->whereIn('pm.agency_id', $agencyIds);
            }
        }

        // Apply service filter
        if (!empty($serviceIds) && $usePatientWiseService) {
            $query->whereIn('service_id', $serviceIds);
        }

        return $query;
    }

    /**
     * Get patient filter subquery based on medication list, insurance elg, and mdo tag filters
     * Returns a subquery builder (not executed) so MySQL can optimize as a single query
     */
    private function getPatientFilterSubquery($medicationList = '', $insuranceElg = '', $mdoTag = '', $mainQueryPatientIds = [])
    {
        if (empty($medicationList) && empty($insuranceElg) && empty($mdoTag)) {
            return null;
        }

        return Patient::select('id')
            ->where('deleted_flag', 'N')
            ->whereNull('archived_at')
            ->when(!empty($medicationList), function ($query) use ($medicationList) {
                if ($medicationList == 'Yes') {
                    $query->where('medication_count', '>=', 1);
                }
                if ($medicationList == 'No') {
                    $query->where('medication_count', '=', 0);
                }
            })
            ->when(!empty($insuranceElg), function ($query) use ($insuranceElg) {
                if ($insuranceElg == 'Yes') {
                    $query->where('insurance_elg_count', '>=', 1);
                }
                if ($insuranceElg == 'No') {
                    $query->where('insurance_elg_count', '=', 0);
                }
            })
            ->when(!empty($mdoTag), function ($query) use ($mdoTag) {
                if ($mdoTag == 'Yes') {
                    $query->where('mdo_tag_count', '>=', 1);
                }
                if ($mdoTag == 'No') {
                    $query->where('mdo_tag_count', '=', 0);
                }
            })->when(!empty($mainQueryPatientIds), function ($query) use ($mainQueryPatientIds) {
                $query->whereIn('id', $mainQueryPatientIds);
            });
    }

    /**
     * Apply medication list, insurance elg, mdo tag and branch filters to query
     * Uses a subquery so MySQL optimizes as a single query execution
     */
    private function applyNewFilters($query, $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [], $patientIdColumn = 'pm.id')
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);

        if (!is_null($patientFilterSubquery)) {
            $query->whereIn($patientIdColumn, $patientFilterSubquery);
        }

        // Apply branch filter
        if (!empty($branchIds) && is_array($branchIds)) {
            $query->whereIn('pm.branch_id', $branchIds);
        }

        return $query;
    }
    /**
     * Generate daily referral report data
     */
    public function generateReportData($startDate = null, $endDate = null, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        if (!$startDate) {
            $startDate = Carbon::now()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $data = [];

        // Get total new requests received
        $data['new_requests'] = $this->getNewRequestsCount($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get new charts created
        $data['new_charts'] = $this->getNewChartsCount($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get forms requested
        $data['forms_requested'] = $this->getFormsRequestedCount($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get breakdown of forms in new charts
        // $data['forms_breakdown'] = $this->getFormsBreakdown($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo);

        // Get agency breakdown with highest weight
        $data['agency_breakdown'] = $this->getAgencyBreakdown($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get referral type breakdown
        $data['referral_type_breakdown'] = $this->getReferralTypeBreakdown($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get resolution status breakdown
        $data['resolution_breakdown'] = $this->getResolutionBreakdown($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get new requests per agency
        $data['requests_per_agency'] = $this->getRequestsPerAgency($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get portal processing data
        $data['portal_processing'] = $this->getPortalProcessingData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get outliers data
        $data['outliers'] = $this->getOutliersData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get insights data
        $data['insights'] = $this->getInsightsData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get updates per agency
        $data['updates_per_agency'] = $this->getUpdatesPerAgency($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Get breakdown of forms in new charts
        $data['portal_processing2'] = $this->getPortalProcessingData2($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        return $data;
    }

    /**
     * Get total new requests received
     */
    private function getNewRequestsCount($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $query = PatientWiseServiceRequest::join('patient_service_requests as psr', 'patient_wise_service_requested.patient_service_request_id', '=', 'psr.id')
            ->join('patient_master as pm', 'psr.patient_id', '=', 'pm.id')
            ->where('patient_wise_service_requested.del_flag', 'N')
            ->whereBetween('psr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            });

        // Apply agency filter
        if (!empty($agencyIds)) {
            $query->whereIn('pm.agency_id', $agencyIds);
        }

        // Apply service filter
        if (!empty($serviceIds)) {
            $query->whereIn('patient_wise_service_requested.service_id', $serviceIds);
        }

        // Apply assigned to filter
        if (!empty($assignedTo)) {
            $query->whereIn('pm.assign_user_id', $assignedTo);
        }

        // Apply new filters
        $this->applyNewFilters($query, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        return $query->count();
    }

    /**
     * Get new charts created
     */
    private function getNewChartsCount($startDate, $endDate, $agencyIds = [], $service_id = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $query = Patient::whereNull('archived_at')
            ->where('type', 'Patient')
            ->whereBetween('created_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereHas('agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })->when(!empty($medicationList), function ($query) use ($medicationList) {
                if ($medicationList == 'Yes') {
                    $query->where('medication_count', '>=', 1);
                }
                if ($medicationList == 'No') {
                    $query->where('medication_count', '=', 0);
                }
            })->when(!empty($insuranceElg), function ($query) use ($insuranceElg) {
                if ($insuranceElg == 'Yes') {
                    $query->where('insurance_elg_count', '>=', 1);
                }
                if ($insuranceElg == 'No') {
                    $query->where('insurance_elg_count', '=', 0);
                }
            })
            ->when(!empty($mdoTag), function ($query) use ($mdoTag) {
                if ($mdoTag == 'Yes') {
                    $query->where('mdo_tag_count', '>=', 1);
                }
                if ($mdoTag == 'No') {
                    $query->where('mdo_tag_count', '=', 0);
                }
            });
        $where = 'patient_master.deleted_flag ="N"';
        if (count($service_id) > 0) {
            $explode = $service_id;
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",patient_master.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        // Apply agency filter
        if (!empty($agencyIds)) {
            $query->whereIn('agency_id', $agencyIds);
        }

        // Apply new filters using patient ID subquery
        // $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        // if (!is_null($patientFilterSubquery)) {
        //     $query->whereIn('patient_master.id', $patientFilterSubquery);
        // }

        if (!empty($branchIds) && is_array($branchIds)) {
            $query->whereIn('patient_master.branch_id', $branchIds);
        }

        return $query->when($assignedTo, function ($query) use ($assignedTo) {
            $query->whereIn('assign_user_id', $assignedTo);
        })->whereRaw($where)->sum(DB::raw('1'));
    }

    /**
     * Get forms requested count
     */
    private function getFormsRequestedCount($startDate, $endDate, $agencyIds = [], $service_id = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {

        $type = "Patient"; // Based on referrals-weight default

        $query = PatientServiceRequest::leftjoin('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->where('pm.archived_at', null)->whereHas('patientServiceRequestRelationShip', function ($q) {
                $q->where('service_id', '!=', '')->where('del_flag', 'N');
            })->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->select([
                'pm.referral_type',
                DB::raw('COUNT(*) as count')
            ])->whereHas('patient', function ($q) {
                $q->where('deleted_flag', 'N');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('patient_service_requests.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('pm.type', $type);
            })->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            });
        $where = 'pm.deleted_flag ="N"';
        if (count($service_id) > 0) {
            $explode = $service_id;
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        // Apply agency filter - prioritize passed filter over user agency filter
        if (!empty($agencyIds)) {
            $query->whereHas('patient', function ($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds);
            });
        }

        // Apply new filters
        $this->applyNewFilters($query, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        return $query->whereRaw($where)->sum(DB::raw('1'));
    }

    /**
     * Get breakdown of forms in new charts
     */
    private function getFormsBreakdown($startDate, $endDate)
    {
        $breakdown = PatientWiseServiceRequest::join('patient_service_requests as psr', 'patient_wise_service_requested.patient_service_request_id', '=', 'psr.id')
            ->join('master_table as m', 'patient_wise_service_requested.service_id', '=', 'm.id')
            ->where('patient_wise_service_requested.del_flag', 'N')
            ->whereIn('psr.status', ['Completed', 'Signed', 'Signed & Sent Back to the Agency'])
            ->whereBetween('psr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('m.name as service_name', DB::raw('COUNT(*) as count'))
            ->groupBy('m.name')
            ->orderBy('count', 'desc')
            ->get();

        // Map to expected format
        $formTypes = [
            'MDOs & Interim Orders' => 0,
            'M11Q' => 0,
            'DOH' => 0,
            'NYIA' => 0,
            'NYIA-Alzheimer/Dementia' => 0,
            'DHS-54A FORM' => 0,
            'LMN/MNL' => 0,
            'Pool Trust' => 0,
            'CHHA' => 0,
            'HHA Services' => 0,
            'PCPF-PA' => 0,
            'Telehealth' => 0,
            'Need Labs' => 0,
            'Face to Face' => 0,
            'Provider Attestation Form' => 0,
            'Medical Assessment Form' => 0,
            'OT/PT/ST/RN' => 0,
            'PRI' => 0,
            'Swallow test' => 0
        ];

        foreach ($breakdown as $item) {
            $serviceName = $item->service_name;
            $count = $item->count;

            // Map service names to form types
            if (str_contains(strtolower($serviceName), 'mdo') || str_contains(strtolower($serviceName), 'interim')) {
                $formTypes['MDOs & Interim Orders'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'm11q')) {
                $formTypes['M11Q'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'doh')) {
                $formTypes['DOH'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'nyia') && str_contains(strtolower($serviceName), 'alzheimer')) {
                $formTypes['NYIA-Alzheimer/Dementia'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'nyia')) {
                $formTypes['NYIA'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'dhs-54a')) {
                $formTypes['DHS-54A FORM'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'lmn') || str_contains(strtolower($serviceName), 'mnl')) {
                $formTypes['LMN/MNL'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'pool trust')) {
                $formTypes['Pool Trust'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'chha')) {
                $formTypes['CHHA'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'hha')) {
                $formTypes['HHA Services'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'pcpf-pa')) {
                $formTypes['PCPF-PA'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'telehealth')) {
                $formTypes['Telehealth'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'lab')) {
                $formTypes['Need Labs'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'face to face')) {
                $formTypes['Face to Face'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'provider attestation')) {
                $formTypes['Provider Attestation Form'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'medical assessment')) {
                $formTypes['Medical Assessment Form'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'ot') || str_contains(strtolower($serviceName), 'pt') || str_contains(strtolower($serviceName), 'st') || str_contains(strtolower($serviceName), 'rn')) {
                $formTypes['OT/PT/ST/RN'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'pri')) {
                $formTypes['PRI'] += $count;
            } elseif (str_contains(strtolower($serviceName), 'swallow')) {
                $formTypes['Swallow test'] += $count;
            } else {
                // If no match found, add to MDOs & Interim Orders as default
                $formTypes['MDOs & Interim Orders'] += $count;
            }
        }

        return $formTypes;
    }

    /**
     * Get agency breakdown with highest weight
     */
    private function getAgencyBreakdown($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $totalRequests = $this->getNewRequestsCount($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        $query = PatientWiseServiceRequest::join('patient_service_requests as psr', 'patient_wise_service_requested.patient_service_request_id', '=', 'psr.id')
            ->join('patient_master as pm', 'patient_wise_service_requested.patient_id', '=', 'pm.id')
            ->join('agency as a', 'pm.agency_id', '=', 'a.id')
            ->where('patient_wise_service_requested.del_flag', 'N')
            ->whereBetween('psr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->select('a.agency_name', DB::raw('COUNT(patient_wise_service_requested.id) as count'))
            ->groupBy('a.agency_name')
            ->orderBy('count', 'desc')
            ->limit(5);

        // Apply agency filter
        if (!empty($agencyIds)) {
            $query->whereIn('pm.agency_id', $agencyIds);
        }

        // Apply service filter
        if (!empty($serviceIds)) {
            $query->whereIn('patient_wise_service_requested.service_id', $serviceIds);
        }
        // Apply assigned to filter
        if (!empty($assignedTo)) {
            $query->whereIn('pm.assign_user_id', $assignedTo);
        }

        // Apply new filters
        $this->applyNewFilters($query, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        $agencyBreakdown = $query->get();

        $breakdown = [];
        foreach ($agencyBreakdown as $agency) {
            $percentage = $totalRequests > 0 ? round(($agency->count / $totalRequests) * 100, 1) : 0;
            $breakdown[] = [
                'agency_name' => $agency->agency_name,
                'count' => $agency->count,
                'percentage' => $percentage
            ];
        }

        return $breakdown;
    }

    /**
     * Get referral type breakdown
     */
    private function getReferralTypeBreakdown($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);

        $type = "Patient"; // Based on referrals-weight default
        $referralTypeBreakdown = PatientServiceRequest::leftjoin('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->where('pm.archived_at', null)->whereHas('patientServiceRequestRelationShip', function ($q) use ($serviceIds) {
                $q->where('service_id', '!=', '')->where('del_flag', 'N')
                    ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                        $query->whereIn('service_id', $serviceIds);
                    });
            })->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->select([
                'pm.referral_type',
                DB::raw('COUNT(patient_service_requests.id) as count')
            ])->whereHas('patient', function ($q) {
                $q->where('deleted_flag', 'N');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('patient_service_requests.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('pm.type', $type);
            })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereHas('patient', function ($q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds) && is_array($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->groupBy('pm.referral_type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'pm.referral_type');

        return $referralTypeBreakdown;
    }

    /**
     * Get resolution status breakdown
     */
    private function getResolutionBreakdown($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);

        return PatientServiceRequest::where('patient_service_requests.del_flag', 'N')
            ->leftjoin('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->whereBetween('patient_service_requests.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->select('patient_service_requests.status', DB::raw('COUNT(*) as count'))
            ->where('patient_service_requests.status', '!=', '')
            ->where('pm.type', 'Patient')
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereHas('patient', function ($q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            })->whereHas('patientServiceRequestRelationShip', function ($q) use ($serviceIds) {
                $q->where('service_id', '!=', '')
                    ->where('del_flag', 'N')
                    ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                        $query->whereIn('service_id', $serviceIds);
                    });
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds) && is_array($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->groupBy('patient_service_requests.status')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get new requests per agency
     */
    private function getRequestsPerAgency($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $type = "Patient"; // Based on referrals-weight default
        return PatientServiceRequest::join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->join('agency as a', 'pm.agency_id', '=', 'a.id')
            ->where('patient_service_requests.del_flag', 'N')
            ->whereBetween('patient_service_requests.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('a.agency_name', DB::raw('COUNT(patient_service_requests.id) as count'))
            ->when($type, function ($query) use ($type) {
                $query->where('pm.type', $type);
            })->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereHas('patient', function ($q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            })
            ->whereHas('patientServiceRequestRelationShip', function ($q) use ($serviceIds) {
                $q->where('service_id', '!=', '')
                    ->where('del_flag', 'N')
                    ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                        $query->whereIn('service_id', $serviceIds);
                    });
            })
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds) && is_array($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->groupBy('a.agency_name')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'agency_name')
            ->toArray();
    }

    /**
     * Get portal processing data - Real database query
     */
    private function getPortalProcessingData($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $type = "Patient"; // Based on referrals-weight default

        $query =  PatientWiseServiceRequest::leftjoin('master_table as m', 'patient_wise_service_requested.service_id', '=', 'm.id')->leftjoin('patient_master as pm', 'patient_wise_service_requested.patient_id', '=', 'pm.id')->leftjoin('patient_service_requests as ps', 'patient_wise_service_requested.patient_service_request_id', '=', 'ps.id')->where('ps.del_flag', 'N')
            ->where('patient_wise_service_requested.del_flag', 'N')
            ->where('m.master_type_fk', 11)
            ->where('pm.deleted_flag', 'N')
            ->where('pm.archived_at', null)
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->select([
                DB::raw("DATE_FORMAT(patient_wise_service_requested.created_date, '%m/%d/%Y') as service_date"),
                'm.name as service_name',
                DB::raw('COUNT(patient_wise_service_requested.id) as count'),
                'pm.id as patient_id'
            ])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {

                $query->whereBetween('ps.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })

            ->when($type, function ($query) use ($type) {
                $query->where('pm.type', $type);
            })->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereHas('patient', function ($q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            })
            // Apply service filter
            ->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                $query->whereIn('patient_wise_service_requested.service_id', $serviceIds);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            ->when(!empty($branchIds) && is_array($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->groupBy('patient_wise_service_requested.service_id')
            ->orderBy('count', 'desc')
            ->get();

        // Get unique patient IDs from main query results
        $mainQueryPatientIds = $query->pluck('patient_id')->unique()->values()->toArray();

        // Filter those patient IDs through medication/insurance/mdo filter
        // $filteredPatientIds = $mainQueryPatientIds;
        $filteredPatientIds = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag, $mainQueryPatientIds);

        if (!is_null($filteredPatientIds)) {
            $filteredPatientIds = $filteredPatientIds->pluck('id')->toArray();
        }

        // Map the results - only include rows whose patient_id is in the filtered set
        $portalProcessing = [];
        foreach ($query as $row) {
            if ($filteredPatientIds != null) {
                if (!in_array($row->patient_id, $filteredPatientIds)) {
                    continue;
                }
            }
            $status = $row->service_name;
            $count = $row->count;
            $portalProcessing[$status] = ($portalProcessing[$status] ?? 0) + $count;
        }

        return $portalProcessing;
    }

    /**
     * Get portal processing data - Real database query
     */
    private function getPortalProcessingData2($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $auth = auth()->user() ?? "";
        $type = "Patient"; // Based on referrals-weight default
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->whereHas('patient.agencyDetail', function ($q) use ($agencyIds) {
                $q->where('agency.delete_flag', 'N')
                    ->when($agencyIds, function ($query) use ($agencyIds) {
                        $query->whereIn('agency.id', $agencyIds);
                    });
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->select([
                'resolution_log.resolution as status',
                DB::raw('COUNT(resolution_log.id) as total')
            ])
            // ->when(isset($auth->agency_fk) && isset($auth->login_type_fk) && in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
            //     $agencyids = Utility::getUserWiseAgencyDashboard();
            //     $agencyids[] = $auth['agency_fk'];
            //     if (!empty($agencyids)) {
            //         $query->whereIn('pm.agency_id', $agencyids);
            //     }
            // })
            ->when($type, function ($q) use ($type) {
                $q->where('pm.type', $type);
            })
            // ->when(in_array($auth->user_type_id, array(7)), function ($query) use ($auth) {
            //     $query->whereIn('pm.agency_id', array($auth['agency_fk']));
            // })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereIn('pm.agency_id', $agencyIds);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds) && is_array($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->whereRaw($where)
            ->where('pm.archived_at', null)
            ->groupBy('resolution_log.resolution')
            ->orderBy('total', 'desc')
            ->get();


        // Map the results to the expected format
        $portalProcessing = [];
        foreach ($query as $row) {
            $status = $row->status;
            $count = $row->total;
            $portalProcessing[$status] = ($portalProcessing[$status] ?? 0) + $count;
        }

        return $portalProcessing;
    }

    /**
     * Map database status to portal processing categories
     */
    private function mapStatusToPortalProcessing($status)
    {
        $statusMap = [
            'Pending' => 'Pending',
            'Cancelled' => 'cancelled',
            'Booked' => 'booked',
            'Completed' => 'completed',
            'No Show' => 'noshow',
            'Arrived' => 'arrived',
            'Processing' => 'processing',
            'Not Interested' => 'Not interested',
            'Hospitalized/Rehab' => 'hospitalized/rehab',
            'Unable To Contact' => 'unableToContact',
            'Refused' => 'refused',
            'Mark as CheckIn' => 'checkin',
            'Pending Termination' => 'Pending Termination',
            'On Hold' => 'Onhold',
            'On Leave' => 'On Leave',
            'Terminated' => 'Terminated',
            'New Form Requested' => 'New Form Requested',
            'New Order Received' => 'New Order Received',
            'Form Completed' => 'Form Completed',
            'Mark As CheckIn' => 'Mark As CheckIn',
            '1st Attempt - Unable to Contact' => '1st Attempt - Unable to Contact',
            '2nd Attempt - Unable to Contact' => '2nd Attempt - Unable to Contact',
            '3rd Attempt - Unable to Contact' => '3rd Attempt - Unable to Contact',
            'Telehealth Completed' => 'Telehealth Completed',
            'Patient Deceased' => 'Patient Deceased',
            'Signed' => 'Signed',
            'Signed & Sent Back to the Agency' => 'Signed & Sent Back to the Agency',
            'Telehealth Completed , Pending Forms' => 'Telehealth Completed , Pending Forms',
            'Patient Asked to Reschedule' => 'Patient Asked to Reschedule',
            'Appointment Missed' => 'Appointment Missed',
            'Service Provided' => 'Service Provided',
            'Closed Temporarily' => 'Closed Temporarily'
        ];

        return $statusMap[$status] ?? null;
    }

    /**
     * Get outliers data - Real calculations based on status vs agencies
     */
    private function getOutliersData($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $outliers = [];

        // Get the status vs agency data (similar to referrals-weight Status VS Agencies chart)
        $statusVsAgency = $this->getStatusVsAgencyData($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds);

        // Calculate outliers for each major status
        $statusesToAnalyze = [
            'Cancelled' => 'cancellations',
            'Refused' => 'refusals',
            '1st Attempt - Unable to Contact' => 'first_attempt_unable_to_contact',
            '2nd Attempt - Unable to Contact' => 'second_attempt_unable_to_contact',
            '3rd Attempt - Unable to Contact' => 'third_attempt_unable_to_contact',
            'processing' => 'processing_charts',
            'Telehealth Completed' => 'mdo_telehealth_completed',
            'Signed' => 'signed_mdo_forms',
            'Signed & Sent Back to the Agency' => 'signed_mdo_sent_back',
            'Form Completed' => 'forms_completed',
            'Appointment Missed' => 'missed_appointments',
            'Booked' => 'booking_non_mdo'
        ];

        foreach ($statusesToAnalyze as $dbStatus => $outliersKey) {
            if ($outliersKey === 'booking_non_mdo') {
                // Special case for booking - need primary and secondary
                $outliers[$outliersKey] = $this->calculateBookingOutliers($statusVsAgency, $dbStatus);
            } else {
                $outliers[$outliersKey] = $this->calculateOutlierForStatus($statusVsAgency, $dbStatus);
            }
        }

        return $outliers;
    }

    /**
     * Get Status VS Agency data (pivot table)
     */
    private function getStatusVsAgencyData($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $auth = auth()->user() ?? "";
        $type = "Patient";
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->select([
                'pm.agency_id',
                'agency.agency_name',
                'resolution_log.resolution as status',
                DB::raw('COUNT(*) as total')
            ])
            // ->when(isset($auth->agency_fk) && isset($auth->login_type_fk) && in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
            //     $agencyids = Utility::getUserWiseAgencyDashboard();
            //     $agencyids[] = $auth['agency_fk'];
            //     if (!empty($agencyids)) {
            //         $query->whereIn('pm.agency_id', $agencyids);
            //     }
            // })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereIn('pm.agency_id', $agencyIds);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('pm.type', $type);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            // ->when(isset($auth->agency_fk) && in_array($auth->user_type_id, array(7)), function ($query) use ($auth) {
            //     $query->whereIn('pm.agency_id', array($auth['agency_fk']));
            // })
            ->whereRaw($where)
            ->where('pm.archived_at', null)
            ->groupBy('pm.agency_id', 'agency.agency_name', 'resolution_log.resolution')
            ->get();

        return $query;
    }

    /**
     * Calculate booking outliers with primary and secondary
     */
    private function calculateBookingOutliers($statusVsAgency, $targetStatus)
    {
        $statusData = $statusVsAgency->where('status', $targetStatus);
        $totalForStatus = $statusData->sum('total');

        if ($totalForStatus == 0) {
            return [
                'primary' => [
                    'agency' => 'No data',
                    'percentage' => '0%',
                    'count' => 0,
                    'total' => 0
                ],
                'secondary' => [
                    'agency' => 'No data',
                    'percentage' => '0%',
                    'count' => 0,
                    'total' => 0
                ]
            ];
        }

        // Get top 2 agencies for this status
        $topAgencies = $statusData->sortByDesc('total')->take(2);

        $result = [
            'primary' => [
                'agency' => 'No data',
                'percentage' => '0%',
                'count' => 0,
                'total' => $totalForStatus
            ],
            'secondary' => [
                'agency' => 'No data',
                'percentage' => '0%',
                'count' => 0,
                'total' => $totalForStatus
            ]
        ];

        if ($topAgencies->count() > 0) {
            $primary = $topAgencies->first();
            $primaryPercentage = round(($primary->total / $totalForStatus) * 100, 1);

            $result['primary'] = [
                'agency' => $primary->agency_name,
                'percentage' => $primaryPercentage . '%',
                'count' => $primary->total,
                'total' => $totalForStatus
            ];
        }

        if ($topAgencies->count() > 1) {
            $secondary = $topAgencies->last();
            $secondaryPercentage = round(($secondary->total / $totalForStatus) * 100, 1);

            $result['secondary'] = [
                'agency' => $secondary->agency_name,
                'percentage' => $secondaryPercentage . '%',
                'count' => $secondary->total,
                'total' => $totalForStatus
            ];
        }

        return $result;
    }

    /**
     * Calculate outlier for specific status
     */
    private function calculateOutlierForStatus($statusVsAgency, $targetStatus)
    {
        $statusData = $statusVsAgency->where('status', $targetStatus);
        $totalForStatus = $statusData->sum('total');

        if ($totalForStatus == 0) {
            return [
                'agency' => 'No data',
                'percentage' => '0%',
                'count' => 0,
                'total' => 0
            ];
        }

        // Find agency with highest count for this status
        $topAgency = $statusData->sortByDesc('total')->first();

        if (!$topAgency) {
            return [
                'agency' => 'No data',
                'percentage' => '0%',
                'count' => 0,
                'total' => $totalForStatus
            ];
        }

        $percentage = round(($topAgency->total / $totalForStatus) * 100, 1);

        return [
            'agency' => $topAgency->agency_name,
            'percentage' => $percentage . '%',
            'count' => $topAgency->total,
            'total' => $totalForStatus
        ];
    }

    /**
     * Get insights data - Real database queries
     */
    private function getInsightsData($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        return [
            'refusal_reasons' => $this->getRefusalReasons($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds),
            'cancellation_reasons' => $this->getCancellationReasons($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds),
            'non_mdo_forms_per_agency' => $this->getNonMdoFormsPerAgency($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds),
            'mdo_completed_per_agency' => $this->getMdoCompletedPerAgency($startDate, $endDate, $agencyIds, $serviceIds, $assignedTo, $medicationList, $insuranceElg, $mdoTag, $branchIds)
        ];
    }

    /**
     * Get refusal reasons breakdown
     */
    private function getRefusalReasons($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $type = "Patient";
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('master_table', function ($join) {
            $join->on('master_table.id', '=', 'resolution_log.refuse_reason');
            $join->where('master_table.del_flag', 'N')
                ->whereIn('master_table.master_type_fk', [29, 32]);
        })->leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->where('resolution_log.resolution', 'Refused')
            ->whereNotNull('resolution_log.refuse_reason')
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select([
                DB::raw('COUNT(*) as count'),
                'pm.agency_id',
                'agency.agency_name',
                'master_table.name as status_name',
                'master_table.id as status_id',
                'patient_service_requests.id'
            ])->when($type, function ($q) use ($type) {
                $q->where('pm.type', $type);
            }) // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            });
        //$agencyids = Utility::getUserWiseAgency();
        // if (isset(Auth()->user()->agency_fk) && Auth()->user()->agency_fk != "") {
        //     $agencyids[] = Auth()->user()->agency_fk;
        // }
        if (!empty($agencyIds)) {
            $query =    $query->whereIn('pm.agency_id', $agencyIds);
        }

        $query = $query->whereRaw($where)->groupBy('resolution_log.refuse_reason')
            ->orderBy('count', 'desc')
            ->get();
        return $query->pluck('count', 'status_name')->toArray();
    }

    /**
     * Get cancellation reasons breakdown
     */
    private function getCancellationReasons($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $type = "Patient";
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('master_table', function ($join) {
            $join->on('master_table.id', '=', 'resolution_log.cancel_reason');
            $join->where('master_table.del_flag', 'N');
        })->leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->where('resolution_log.resolution', 'Cancelled')
            ->whereNotNull('resolution_log.cancel_reason')
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select([
                DB::raw('COUNT(*) as count'),
                'pm.agency_id',
                'agency.agency_name',
                'master_table.name as status_name',
                'master_table.id as status_id',
                'patient_service_requests.id'
            ])->when($type, function ($q) use ($type) {
                $q->where('pm.type', $type);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            });

        //$agencyids = Utility::getUserWiseAgency();
        // if (isset(Auth()->user()->agency_fk) && Auth()->user()->agency_fk != "") {
        //     $agencyids[] = Auth()->user()->agency_fk;
        // }
        if (!empty($agencyIds)) {
            $query =    $query->whereIn('pm.agency_id', $agencyIds);
        }
        $query = $query->whereRaw($where)->groupBy('resolution_log.cancel_reason')
            ->orderBy('count', 'desc')
            ->get();
        return $query->pluck('count', 'status_name')->toArray();
    }

    /**
     * Get Non-MDO forms completed per agency
     */
    private function getNonMdoFormsPerAgency($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $auth = auth()->user() ?? "";
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->where('resolution_log.resolution', 'Form Completed')
            ->select([
                'agency.agency_name',
                DB::raw('COUNT(*) as total')
            ])
            // ->when(isset(Auth()->user()->agency_fk) && isset($auth->login_type_fk) && in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
            //     $agencyids = Utility::getUserWiseAgencyDashboard();
            //     $agencyids[] = $auth['agency_fk'];
            //     if (!empty($agencyids)) {
            //         $query->whereIn('pm.agency_id', $agencyids);
            //     }
            // })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereIn('pm.agency_id', $agencyIds);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            // ->when(isset($auth['agency_fk']) && in_array($auth->user_type_id, array(7)), function ($query) use ($auth) {
            //     $query->whereIn('pm.agency_id', array($auth['agency_fk']));
            // })
            ->whereRaw($where)
            ->where('pm.archived_at', null)
            ->groupBy('agency.agency_name')
            ->orderBy('total', 'desc')
            ->get();

        return $query->pluck('total', 'agency_name')->toArray();
    }

    /**
     * Get MDO completed per agency (Signed & Sent Back to Agency)
     */
    private function getMdoCompletedPerAgency($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $auth = auth()->user() ?? "";
        $where = 'pm.deleted_flag ="N"';
        if (count($serviceIds) > 0) {
            $explode = $serviceIds;
            // $explode = explode(',', $service_id);
            $final = '';
            foreach ($explode as $key => $vals) {
                $or = '';
                if ($key != 0) {
                    $or = ' OR ';
                }
                $final .= $or . ' FIND_IN_SET("' . $vals . '",pm.service_id)';
            }
            $where .= ' and (' . $final . ')';
        }
        $query = Resolution::leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->whereHas('patient.agencyDetail', function ($q) {
                $q->where('agency.delete_flag', 'N');
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->where('resolution_log.resolution', 'Signed & Sent Back to the Agency')
            ->select([
                'agency.agency_name',
                DB::raw('COUNT(*) as total')
            ])
            // ->when(isset($auth->agency_fk) && isset($auth->login_type_fk) &&  in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
            //     $agencyids = Utility::getUserWiseAgencyDashboard();
            //     $agencyids[] = $auth['agency_fk'];
            //     if (!empty($agencyids)) {
            //         $query->whereIn('pm.agency_id', $agencyids);
            //     }
            // })
            // ->when(in_array($auth->user_type_id, array(7)), function ($query) use ($auth) {
            //     $query->whereIn('pm.agency_id', array($auth['agency_fk']));
            // })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereIn('pm.agency_id', $agencyIds);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->whereRaw($where)
            ->where('pm.archived_at', null)
            ->groupBy('agency.agency_name')
            ->orderBy('total', 'desc')
            ->get();

        return $query->pluck('total', 'agency_name')->toArray();
    }

    /**
     * Get updates per agency - Total resolution updates
     */
    private function getUpdatesPerAgency($startDate, $endDate, $agencyIds = [], $serviceIds = [], $assignedTo = [], $medicationList = '', $insuranceElg = '', $mdoTag = '', $branchIds = [])
    {
        $patientFilterSubquery = $this->getPatientFilterSubquery($medicationList, $insuranceElg, $mdoTag);
        $auth = auth()->user() ?? "";
        $type = "Patient";

        $query = Resolution::leftjoin('patient_service_requests', function ($join) {
            $join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
        })
            ->whereBetween('resolution_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            // ->leftjoin('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
            ->join('patient_master as pm', function ($join) {
                $join->on('patient_service_requests.patient_id', '=', 'pm.id');
            })
            ->join('patient_wise_service_requested', function ($join) {
                $join->on('patient_service_requests.id', '=', 'patient_wise_service_requested.patient_service_request_id');
                $join->on('pm.id', 'patient_wise_service_requested.patient_id');
            })
            ->whereHas('patient.agencyDetail', function ($q) use ($agencyIds) {
                $q->where('agency.delete_flag', 'N')
                    ->when($agencyIds, function ($query) use ($agencyIds) {
                        $query->whereIn('agency.id', $agencyIds);
                    });
            })
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'pm.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->where('patient_service_requests.del_flag', 'N')
            ->where('pm.deleted_flag', 'N')
            ->where('resolution_log.del_flag', 'N')
            ->select([
                'agency.agency_name',
                DB::raw('COUNT(*) as total')
            ])
            // ->when(isset($auth->agency_fk) && isset($auth->login_type_fk) && in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
            //     $agencyids = Utility::getUserWiseAgencyDashboard();
            //     $agencyids[] = $auth['agency_fk'];
            //     if (!empty($agencyids)) {
            //         $query->whereIn('pm.agency_id', $agencyids);
            //     }
            // })
            // ->when(in_array($auth->user_type_id, array(7)), function ($query) use ($auth) {
            //     $query->whereIn('pm.agency_id', array($auth['agency_fk']));
            // })
            ->when($agencyIds, function ($query) use ($agencyIds) {
                $query->whereIn('pm.agency_id', $agencyIds);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('pm.type', $type);
            })->when(!empty($serviceIds), function ($query) use ($serviceIds) {
                $query->whereIn('patient_wise_service_requested.service_id', $serviceIds);
            })
            // Apply assigned to filter
            ->when($assignedTo, function ($query) use ($assignedTo) {
                $query->whereIn('pm.assign_user_id', $assignedTo);
            })
            // Apply new filters using patient ID subquery
            ->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
                $query->whereIn('pm.id', $patientFilterSubquery);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('pm.branch_id', $branchIds);
            })
            ->where('pm.archived_at', null)
            ->groupBy('agency.agency_name')
            ->orderBy('total', 'desc')
            ->get();

        return $query->pluck('total', 'agency_name')->toArray();
    }

    /**
     * Format report data for email
     */
    public function formatReportForEmail($data, $reportDate = null)
    {
        // if (!$reportDate) {
        //     $reportDate = Carbon::now()->format('m/d/Y');
        // }

        $topAgencies = array_slice($data['agency_breakdown'], 0, 2, true);
        $topAgencyText = '';
        foreach ($topAgencies as $index => $agency) {
            if ($index > 0) $topAgencyText .= ', followed by ';
            $topAgencyText .= $agency['agency_name'] . ' at ' . $agency['percentage'] . '%';
        }

        // Calculate total portal processing updates
        $totalPortalUpdates = array_sum($data['portal_processing']);

        $formattedData = [
            'report_date' => $reportDate,
            'total_new_requests' => $data['new_requests'],
            'total_new_charts' => $data['new_charts'],
            'total_forms_requested' => $data['forms_requested'],
            // 'forms_breakdown' => $data['forms_breakdown'],
            'top_agencies_text' => $topAgencyText,
            'referral_breakdown' => $data['referral_type_breakdown'],
            'resolution_breakdown' => $data['resolution_breakdown'],
            'agency_requests' => $data['requests_per_agency'],
            'portal_processing' => $data['portal_processing'],
            'total_portal_updates' => $totalPortalUpdates,
            'outliers' => $data['outliers'],
            'insights' => $data['insights'],
            'updates_per_agency' => $data['updates_per_agency'],
            'grand_total' => $data['new_requests'],
            'portal_processing2' => $data['portal_processing2'],
        ];

        return $formattedData;
    }
}
