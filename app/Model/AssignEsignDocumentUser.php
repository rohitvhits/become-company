<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssignEsignDocumentUser extends Model
{
	protected $table = 'assign_esign_document_user';
	protected $guarded = ['id'];

}
