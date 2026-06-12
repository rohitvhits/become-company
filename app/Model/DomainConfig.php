<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainConfig extends Model
{
    use SoftDeletes;

    protected $table = 'domain_configs';

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];
}
