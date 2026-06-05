<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DocumentType extends Model
{
    use Notifiable;

    protected $table = 'document_type_master';
	public $timestamps = false;
    protected $fillable = ['id', 'name', 'del_flag', 'created_date','created_by','updated_date','updated_by','type'];

	
}
