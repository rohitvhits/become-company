<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class ZipCode extends Model
{
	use Notifiable;

	protected $table = 'zipcode';
	protected $guarded = ['id'];


}
