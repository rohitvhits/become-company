<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
	use Notifiable;

	protected $table = 'invoice_master';
	public $timestamps = false;
	protected $perPage=15;
	protected $fillable = ['id','agency_fk','invoice_number', 'invoice_date', 'total_amount', 'due_amount','total_record', 'created_date', 'created_by','status','updated_date','updated_by','invoice_month','invoice_year','monthly_bill','invoice_status_id','nin_invoice_date','nin_invoice_id','nin_invoice_type_id','ninja_invitation_link'];	
	public static function getInvoiceList($agency_fk,$status_id,$invoice_date,$user_type){
		$temp ='invoice_master.id !=""';
		if($agency_fk !=''){
			$implode = $agency_fk;
			if($user_type ==3){
				$implode =implode ( '", "', $agency_fk);
			

			}
			
			$temp .=' and invoice_master.agency_fk IN( "'.$implode.'")';
		}

		if($status_id !=''){
			if($status_id =='partial'){
				$status_id = 'Partially Paid';
			}
			$temp .=' and invoice_master.status ="'.$status_id.'"';
		}
		if($invoice_date !=''){
			$explode = explode('-',$invoice_date);
			$temp .=' and invoice_master.invoice_date BETWEEN "'.date('Y-m-d',strtotime($explode[0])).'" and "'.date('Y-m-d',strtotime($explode[1])).'"';
		}
		$query = Invoice::select('invoice_master.*','agency.agency_name')
				->leftjoin('agency',function($join){ 
					$join->on('agency.id','=','invoice_master.agency_fk');
				})
				->whereRaw($temp)->Paginate(50);
				
		return $query;
		
	}
	
	public static function getInvoiceByAgencyid($id,$date,$status){
		$temp = 'agency_fk ="'.$id.'"';
		if($date !=''){
			$explode = explode('-',$date);
			$temp .=' and invoice_date >="'.date('Y-m-d',strtotime($explode[0])).'" and invoice_date <="'.date('Y-m-d',strtotime($explode[1])).'"';
		}
		if($status !=''){
			
			if($status =='partial'){
				$status = 'Partially Paid';
			}
			$temp .= ' and status ="'.$status.'"';
		}
		$query = Invoice::whereRaw($temp)->get();
		return $query;
	}

	public static function search($invoice){
		$query = Invoice::where('invoice_number',$invoice)->where('del_flag','N')->Simplepaginate(10);
		return $query;
	}
}
