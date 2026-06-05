<?php 
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Services\HHASyncAgencyService;

class HHAAgencySyncController extends BaseController{
    protected $hhaSyncAgencyService  = "";

    public function __construct(HHASyncAgencyService $hhaSyncAgencyService)
    {
        $this->hhaSyncAgencyService = $hhaSyncAgencyService;
    }
    public function index(){
        $auth = auth()->user();
        return view('hha_agency_sync.index');
    }

    public function ajaxList(Request $request){
        $data['list'] = $this->hhaSyncAgencyService->getList();
        return view('hha_agency_sync.ajax_list',$data);
    }
}