<?php

namespace App\Http\Controllers;
use URL;
use App\Services\PatientServicesRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\LocationMasterService;
class RequestedServiceController extends BaseController
{
    protected $patientServiceRequest,$locationMasterService="";

    public function __construct(PatientServicesRequest $patientServiceRequest,LocationMasterService $locationMasterService)
    {
        $this->patientServiceRequest = $patientServiceRequest;
        $this->locationMasterService = $locationMasterService;
        $this->middleware('permission:service-requested|service-requested-export', ['only' => ['index','ajaxList']]);
        $this->middleware('permission:service-requested-export', ['only' => ['exportCsv']]);
    }

    public function index(Request $request){
        $data['user'] = auth()->user();
        $data['agencyList'] = Agency::getAllAgencyListWithoutAnyCondition();
        $data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
        return view('requested_service.index',$data);
    }

    public function ajaxList(Request $request){
        $search = $request->all();
     
        $data['user'] = auth()->user();
        $data['response'] = $this->patientServiceRequest->getList($search);
        return view('requested_service.ajax_list',$data);
    }

}