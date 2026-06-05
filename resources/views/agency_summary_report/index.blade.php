@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/agency_summary.css')}}">
<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Agency Summary Report List</h5>
            <div class="page-rightbtns">
                <div>
                    @can('agency-summary-export')
                        <a href="" class="btn btn-success btn-sm btn-rounded btn-fw ml-1" id="test_user"
                            onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <span id="agency_summary_html"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="loader-sec" style="display:none">
        <div id="cover-spin"></div>
    </div>

    <script>
        var _AJAX_DATA ='{{ url("agency-summary-ajax") }}';
        var _EXPORT_DATA ='{{ url("agency-summary-export") }}';
    </script>
    <script src="{{ asset('/assets/modulejs/agency_summary_report/agency_summary.js')}}?time={{ env('timestamps')}}"></script>
@include('include/footer')
