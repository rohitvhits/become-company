<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Agency;
use App\GenerateAgencyToken;
use App\Helpers\GenerateAgencyTokenHelper;
use Excel;
use App\Helpers\UserHelper;
class GenerateAgencyTokenController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');
		
    }

    public function index(Request $request)
    {
		// return redirect('/home');
        $data['menu'] = "user";
        $data['auth'] = $data['user']=$user= auth()->user();
		
        $agency_name=$data['agency_name'] = request('agency_name');
        $data['agency_list'] = Agency::select('id','agency_name')->where('delete_flag','N')->orderBy('agency_name','asc')->get();
		
		if(in_array($user['user_type_fk'],array('3','4'))){
		$data['query'] = GenerateAgencyTokenHelper::getData($agency_name);
		}else{

		$data['query'] = GenerateAgencyTokenHelper::getData($user['agency_fk']);
		}
        return view("token.index", $data);
    }


    public function insert(Request $request)
    { 
        //return redirect('/home');
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            
        ]);
        if ($validator->fails()) {
            return redirect("/agency-token")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
			    
  
            $agency_id = request('agency_id');
            $token = $this->random_string(50);
            $data = array(
                'agency_id' => $agency_id,
                'token' => $token,
                
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $user->id,
            );

            //echo "<pre>"; print_r($data); die();
             $ins_test = GenerateAgencyTokenHelper::insert($data);
            if ($ins_test) {
                Session::flash('success', 'Token successfully generated.');
                return redirect('/agency-token');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/agency-token');
            }
        }
    }

   

    public function delete($id)
    {   return redirect('/home');
        $user = auth()->user();
        if($user['user_type_fk'] !=3){
			return abort(404);
		}
		$data['id'] = $id;
        $update = GenerateAgencyTokenHelper::where('id', $id)->update(array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id));
        if ($update) {
            Session::flash('success', 'Token  successfully deleted.');
            return redirect('/agency-token');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/agency-token');
        }
    }
   
   public function checkGenereteAgencyToken(){
		$agency_id = request('agency_id');
		$query = GenerateAgencyToken::where('agency_id',$agency_id)->where('delete_flag','N')->count();
		if($query ==0){
			echo 1;
		}else{
			echo 0;
		}
   }
   function random_string($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	}
	function export(){		
 return redirect('/home');	
		$agencyId = request('agency_id');				
		$query = GenerateAgencyTokenHelper::getDataExport($agencyId);				
		$filename = 'apiToken'. date("m-d-Y");         		
		$headers = array(            		
			"Content-type" => "text/csv",            		
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",            		
			"Pragma" => "no-cache",           		
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",            		
			"Expires" => "0");				
			$columns = array('Agency Name','Token');								
			$callback = function() use ($query,$columns) {						
				$file = fopen('php://output', 'w');            			
				fputcsv($file, $columns);						
				foreach ($query as $record) {								
				$final =array($record->agency_name,$record->token);				 				
				fputcsv($file, $final);						
			}			 			
			fclose($file);
		};				
		return response()->stream($callback, 200, $headers);				
	}
}
