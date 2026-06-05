<?php



namespace App;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
class Rates extends Model

{
    use Notifiable;

  protected $table = 'rates';
  protected $fillable = ['id','agency_fk', 'item','rate','start_date','end_date','del_flag','created_at','created_by','updated_at','updated_by','deleted_by','deleted_at','type','cused'];


  public static function getDataRates($agency_fk,$item,$start_date,$end_date){
  	 $temp = "rates.del_flag = 'N' ";
  	 if ($agency_fk != "") {
            $temp .= " AND rates.agency_fk='$agency_fk'";
        }
        if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
          if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
        if ($start_date != "") {
        	 $start_date = date("Y-m-d", strtotime($start_date));
            $temp .= " AND rates.start_date >='" . $start_date . " 00:00:00'";
            
        }
         if (!empty($end_date)) {
            $end_date = date("Y-m-d", strtotime($end_date));
            $temp .= " AND rates.end_date <='" . $end_date . " 23:59:59'";
        }

		 $query = DB::table("rates")
		            ->selectRaw("rates.*,master_table.name as item_name,agency.agency_name")
		            ->leftjoin("master_table","master_table.id", "=", "rates.item")
		            ->leftjoin("agency","agency.id", "=", "rates.agency_fk")
		           ->whereRaw($temp)
		           ->paginate(10);
				return $query;
	}
	public static function getDataRatesByAgency($agency_fk,$item,$start_date,$end_date){
		$temp = "rates.del_flag = 'N' AND rates.agency_fk = '$agency_fk'  ";
  	 	/*if ($agency_fk != "") {
            $temp .= " AND rates.agency_fk='$agency_fk'";
        }*/
        if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
          if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
        if (!empty($start_date)) {
        	 $start_date = date("Y-m-d", strtotime($start_date));
            $temp .= " AND rates.start_date >='" . $start_date . " 00:00:00'";
        }
         if (!empty($end_date)) {
            $end_date = date("Y-m-d", strtotime($end_date));
            $temp .= " and rates.end_date <='" . $end_date . " 23:59:59'";
        }
		 $query = DB::table("rates")
		            ->selectRaw("rates.*,master_table.name as item_name,agency.agency_name")
		            ->leftjoin("master_table","master_table.id", "=", "rates.item")
		            ->leftjoin("agency","agency.id", "=", "rates.agency_fk")
		          	->whereRaw($temp)
		          	 ->paginate(10);
				return $query;
	}
	 public static function getDataRatesById($id){

		 $query = DB::table("rates")
		            ->selectRaw("rates.*,master_table.name as item_name,agency.agency_name")
		            ->leftjoin("master_table","master_table.id", "=", "rates.item")
		            ->leftjoin("agency","agency.id", "=", "rates.agency_fk")
		           ->where('rates.id','=',$id)
		           ->first();
				return $query;
	}
	  public static function getCheckAgencyItem($agency_fk,$item,$start_date,$end_date){

		 $query = Rates::where('agency_fk','=',$agency_fk)
		           ->where('item','=',$item)
		            ->whereRaw('CONCAT(start_date,end_date) >="'.$start_date.'" and CONCAT(start_date,end_date) <="'.$end_date.'"')
					->where('del_flag','=','N')
					->count();
		
				return $query;
	}
	public static function getCheckAgencyItemById($agency_fk,$item,$start_date,$end_date,$id){
		
		
		 $query = Rates::where('agency_fk','=',$agency_fk)
		           ->where('item','=',$item)
		          ->whereRaw('CONCAT(start_date,end_date) >="'.$start_date.'" and CONCAT(start_date,end_date) <="'.$end_date.'"')
		           ->where('rates.id','!=',$id)
		           ->count();
				   
				return $query;
	}
	 public static function getDataRatesExport($agency_fk,$item,$start_date,$end_date){
  	 $temp = "rates.del_flag = 'N' ";
  	 if ($agency_fk != "") {
            $temp .= " AND rates.agency_fk='$agency_fk'";
        }
        if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
          if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
        if ($start_date != "") {
        	 $start_date = date("Y-m-d", strtotime($start_date));
            $temp .= " AND rates.start_date >='" . $start_date . " 00:00:00'";
            
        }
         if (!empty($end_date)) {
            $end_date = date("Y-m-d", strtotime($end_date));
            $temp .= " AND rates.end_date <='" . $end_date . " 23:59:59'";
        }

		 $query = DB::table("rates")
		            ->selectRaw("rates.*,master_table.name as item_name,agency.agency_name,users.first_name,users.last_name")
		            ->leftjoin("master_table","master_table.id", "=", "rates.item")
		            ->leftjoin("agency","agency.id", "=", "rates.agency_fk")
		            ->leftjoin("users","users.id", "=", "rates.created_by")
		           ->whereRaw($temp)
		           ->get();
				return $query;
	}
	public static function getDataRatesByAgencyExport($agency_fk,$item,$start_date,$end_date){
		$temp = "rates.del_flag = 'N' AND rates.agency_fk = '$agency_fk'  ";
  	 	/*if ($agency_fk != "") {
            $temp .= " AND rates.agency_fk='$agency_fk'";
        }*/
        if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
          if ($item != "") {
            $temp .= " AND rates.item='$item'";
        }
        if (!empty($start_date)) {
        	 $start_date = date("Y-m-d", strtotime($start_date));
            $temp .= " AND rates.start_date >='" . $start_date . " 00:00:00'";
        }
         if (!empty($end_date)) {
            $end_date = date("Y-m-d", strtotime($end_date));
            $temp .= " and rates.end_date <='" . $end_date . " 23:59:59'";
        }
		 $query = DB::table("rates")
		            ->selectRaw("rates.*,master_table.name as item_name,agency.agency_name,users.first_name,users.last_name")
		            ->leftjoin("master_table","master_table.id", "=", "rates.item")
		            ->leftjoin("agency","agency.id", "=", "rates.agency_fk")
		             ->leftjoin("users","users.id", "=", "rates.created_by")
		          	->whereRaw($temp)
		          	 ->get();
				return $query;
	}
  public static function getRateDetails($id){
    $temp = "rates.del_flag = 'N' AND rates.id = ".$id;
      
     $query = Rates::selectRaw("master_table.name as item_name")
                ->leftjoin("master_table","master_table.id", "=", "rates.item")
                ->whereRaw($temp)
                 ->first();

        return $query;
  }

}

