<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use App\Services\BookAppointmentService;
use App\Helpers\Utility;
class BookAppointmentReportController extends BaseController
{
    protected $bookAppointmentService;

    public function __construct(BookAppointmentService $bookAppointmentService){
        $this->middleware('auth');
        $this->middleware('permission:bulk-view-report-list|bulk-view-report-export', ['only' => ['index', 'ajaxList', 'exportCsv']]);
        $this->middleware('permission:bulk-view-report-export', ['only' => ['exportCsv']]);
        $this->bookAppointmentService = $bookAppointmentService;
    }

    public function index(Request $request){
        $data['auth'] = auth()->user();
        return view('bulkViewAppointmentReport.index',$data);
    }

    public function ajaxList(Request $request){
        $data['page'] = $request->page;
        $data['query'] = $this->bookAppointmentService->getList($request->all());
        return view('bulkViewAppointmentReport.ajaxList',$data);
    }

    public function exportCsv(Request $request){

        $query = $this->bookAppointmentService->getList($request->all(),'export');
        $filename = 'Book Appointment' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Full Name', 'Mobile', 'Email', 'Agency Name','Service Name','County','Book Date','Created Date');

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($query as $list) {
                $bookDate = "";
                if($list->book_date !="" && $list->book_date !="0000-00-00"){
                    $bookDate = Utility::convertMDY($list->book_date);
                }
                fputcsv($file, array($list->full_name, $list->phone, $list->email, $list->agency_name, $list->service_name, $list->county, $bookDate,Utility::convertMDYTime($list->created_date)));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}