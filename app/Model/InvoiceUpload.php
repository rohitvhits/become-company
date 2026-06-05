<?php

namespace App\Model;

use App\Master;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceUpload extends Model
{
    use SoftDeletes;
    protected $table = 'invoice_upload';
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function getService(){
        return $this->belongsTo(Master::class,'service_id','id');
    }
}
