<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PendingVisitingMedical extends Model
{
    public $timestamps = false;

    protected $table = 'pending_visiting_medical';

    protected $guarded = ["id"];
}