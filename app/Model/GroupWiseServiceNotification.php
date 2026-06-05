<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Master;

class GroupWiseServiceNotification extends Model
{
    use SoftDeletes;
    protected $table = 'group_wise_service_notification';
    protected $guarded = ['id'];

    public function servicesDeatils(){
        return $this->belongsTo(Master::class,"service_id","id");
    }
}
