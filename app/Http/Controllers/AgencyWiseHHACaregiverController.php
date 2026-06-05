<?php

namespace App\Http\Controllers;

use App\Agency;

use Illuminate\Http\Request;
use App\Services\HHACaregiverService;

use Illuminate\Support\Facades\Validator;

class AgencyWiseHHACaregiverController extends Controller
{
    protected $hhaCaregiverService ="";
    public function __construct(HHACaregiverService $hhaCaregiverService)
	{
        $this->middleware('permission:view-hha-caregiver-list', ['only' => ['index','ajaxList']]);
		$this->hhaCaregiverService = $hhaCaregiverService;
	}

    public function index(Request $request){
      
        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        $data['agency_fk'] =$data['agency_id'] = $request->agency_id;
        $data['agencyDetails'] = Agency::whereRaw('SHA1(id) ="'.$request->agency_id.'"')->first();
        if($request->redirection_agency_id !=""){
    
            $data['agencyList'] = Agency::getHHAAgencyListById(trim($request->redirection_agency_id));
    
        }else{
            $data['agencyList'] = Agency::getHHAAgencyList();
        }
        return view('hha_caregiver.index',$data);
    }

    public function ajaxList(Request $request){
       
        $data['query'] = $this->hhaCaregiverService->ajaxList($request->all());
        return view('hha_caregiver.ajaxList',$data);
    }

}
