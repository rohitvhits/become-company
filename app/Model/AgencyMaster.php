<?php

namespace App\Model;

use App\Agency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AgencyMaster extends Model
{
    use SoftDeletes;
    use Notifiable;

    protected $table = 'agency_masters';
    protected $guarded = ['id'];

    public function agency()
    {
        return $this->hasOne(Agency::class, 'id', 'agency_id');
    }

    public function fields()
    {
        return $this->hasOne(FieldMaster::class, 'id', 'field_id');
    }

    public function formGroup()
    {
        return $this->hasOne(FormGroup::class, 'id', 'form_group_id');
    }
}
