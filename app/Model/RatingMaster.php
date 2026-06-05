<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatingMaster extends Model
{
    use SoftDeletes;
    protected $table = 'rating_master';
    protected $guarded = ['id'];

}
