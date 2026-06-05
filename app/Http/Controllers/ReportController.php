<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class ReportController extends BaseController
{
    protected $logsService="";
    public function __construct()
    {
        $this->middleware('permission:reports-list', ['only' => ['index']]);
        $this->middleware('auth');
    }

    public function index(){
        $data['query'] = array(
            [
                'name' => 'Expiring Medical Next 10 Days',
                'link' =>  url('expiring-medical'),
            ],
            [
                'name' => 'Hamaspik Appointment Report',
                'link' =>  url('service-wise-appointment-report'),
            ],
            [
                'name' => 'Document Report',
                'link' =>  url('document-report'),
            ],
            [
                'name' => 'Form Report',
                'link' =>  url('form-report'),
            ],
            [
                'name' => 'Agency User Report',
                'link' =>  url('agency-user-report'),
            ],
            [
                'name' => 'Agency Summary Report',
                'link' =>  url('agency-summary'),
            ],
            [
                'name' => 'Esign Report',
                'link' =>  url('esign-report'),
            ],
            [
                'name' => 'API call log Report',
                'link' =>  url('api-log-report'),
            ],
            [
                'name' => 'Third Party Report List',
                'link' =>  url('third-party-report-list'),
            ],
            [
                'name' => 'Dashboard Graph',
                'link' =>  url('dashboard-graph'),
            ],
            [
                'name' => 'Service Request Report',
                'link' =>  url('patient-service-requested'),
            ],
            [
                'name' => 'Feedback Form Report',
                'link' =>  url('feedback-form-report'),
            ],
            [
                'name' => 'MD Order Report',
                'link' =>  url('md-order-report'),
            ],
            [
                'name' => 'Emmacare Referral Report',
                'link' =>  url('emmacare-referal'),
            ],
            [
                'name' => 'Payment Log Report',
                'link' =>  url('payment-log-report'),
            ],
            [
                'name' => 'Telehealth Book Report',
                'link' =>  url('telehealth-book-report'),
            ],
            [
                'name' => 'Hub Record Report',
                'link' =>  url('hub-record-report'),
            ],
            [
                'name' => 'Hub Notes Report',
                'link' =>  url('hub-notes-report'),
            ],
            [
                'name' => 'Hub Document Report',
                'link' =>  url('hub-doc-report'),
            ],
            [
                'name' => 'Audit Log Report',
                'link' =>  url('audit-log-report'),
            ],
            [
                'name' => 'Payment Log Report',
                'link' =>  url('payment-log-report'),
            ],
            [
                'name' => 'Telehealth Book Report',
                'link' =>  url('telehealth-book-report'),
            ],
            [
                'name' => 'Hub Record Report',
                'link' =>  url('hub-record-report'),
            ],
            [
                'name' => 'Hub Notes Report',
                'link' =>  url('hub-notes-report'),
            ],
            [
                'name' => 'Resolution Log Report',
                'link' =>  url('resolution-log-report'),
            ],
            [
                'name' => 'NyBest Medical Requested',
                'link' =>  url('hub-patient-service-requested'),
            ],
            [
                'name' => 'Referrals Weight',
                'link' =>  url('referrals-weight'),
            ],
       );
        return view("report/index", $data);
    }
}