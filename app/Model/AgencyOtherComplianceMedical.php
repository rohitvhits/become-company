<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AgencyOtherComplianceMedical extends Model
{
    protected $table = 'agency_other_compliance_medicals';
    protected $guarded = ['id'];

    public static function getByAgencyId($agencyId)
    {
        return self::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->orderBy('id', 'asc')
            ->get();
    }
}
