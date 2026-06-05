<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgencyCountry extends Model
{
    public $timestamps = false;
    protected $table = 'agency_wise_country_block';
    protected $fillable = ['id', 'agency_id', 'country_id', 'country_name', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public static function agencyWiseCountry($id)
    {
        $query = AgencyCountry::where('agency_id', $id)->paginate(10);
        return $query;
    }
}
