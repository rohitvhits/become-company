<?php

namespace App\Services;

use App\Model\AgencyWiseCompany;
use App\Model\DomainConfig;

class AgencyWiseCompanyService
{
    public function getByAgency($agencyId)
    {
        return AgencyWiseCompany::with('domainConfig')
            ->where('agency_id', $agencyId)
            ->get();
    }

    public function getAllDomainConfigs()
    {
        return DomainConfig::whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->orderBy('company_name')
            ->get();
    }

    public function findById($id)
    {
        return AgencyWiseCompany::find($id);
    }

    public function updateCompany($id, $domainConfigId)
    {
        $row = AgencyWiseCompany::find($id);
        if (!$row) return null;
        $row->domain_config_id = $domainConfigId ?: null;
        $row->save();
        return $row;
    }

    public function saveForAgency($agencyId, $domainConfigId)
    {
        if (!$domainConfigId) return null;
        return AgencyWiseCompany::create([
            'agency_id'        => $agencyId,
            'domain_config_id' => $domainConfigId,
        ]);
    }

    public function upsertForAgency($agencyId, $domainConfigId)
    {
        if ($domainConfigId) {
            return AgencyWiseCompany::updateOrCreate(
                ['agency_id' => $agencyId],
                ['domain_config_id' => $domainConfigId]
            );
        }
        AgencyWiseCompany::where('agency_id', $agencyId)->delete();
        return null;
    }

    public function getFirstByAgency($agencyId)
    {
        return AgencyWiseCompany::where('agency_id', $agencyId)->first();
    }
}
