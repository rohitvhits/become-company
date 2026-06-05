<?php

namespace App\Model;

use App\Agency;
use App\Template;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyWiseHHAMedical extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'agency_wise_hha_medical';
    protected $guarded = ['id'];

    /**
     * Get the agency that owns the medical record
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    /**
     * Get the office that owns the medical record
     */
    public function office()
    {
        return $this->belongsTo(HHAOffice::class, 'office_id', 'office_id');
    }
}