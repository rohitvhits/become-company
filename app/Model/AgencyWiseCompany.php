<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyWiseCompany extends Model
{
    use SoftDeletes;

    protected $table = 'agency_wise_company';

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    public function domainConfig()
    {
        return $this->belongsTo(DomainConfig::class, 'domain_config_id');
    }
}
