<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class InvoicePayment extends Model
{
	use Notifiable;

	protected $table = 'invoice_payment';
	public $timestamps =false;
	protected $fillable = ['id','invoice_id','amount', 'remark', 'created_date', 'created_by'];

}
