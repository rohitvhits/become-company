<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssignEsignDocument extends Model
{
	protected $table = 'assign_esign_document';
	protected $guarded = ['id'];

}
