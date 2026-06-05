<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApproveStamp extends Model
{
    use SoftDeletes;
    protected $table = 'approve_stamp';
    protected $guarded = ['id'];
}
