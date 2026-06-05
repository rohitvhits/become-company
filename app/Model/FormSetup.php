<?php

namespace App\Model;

use App\Agency;
use App\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSetup extends Model
{
    use SoftDeletes;
    protected $table = 'form_setup';
    protected $guarded = ['id'];

    public function agencyValue()
    {
        return $this->hasOne(Agency::class, 'id', 'agency');
    }

    public function agencyMaster()
    {
        return $this->hasMany(AgencyMaster::class, 'form_id', 'id');
    }

    public function getTemplateById()
    {
        return $this->hasMany(Template::class, 'custom_form_id', 'id');
    }
}
