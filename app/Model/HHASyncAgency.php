<?php

namespace App\Model;

use App\Agency;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HHASyncAgency extends Model
{

    protected $table = 'hha_agency_sync';
    protected $guarded = ['id'];

    public function agencyDetails(){
        return $this->hasOne(Agency::class,"id","agency_id");
    }
}
