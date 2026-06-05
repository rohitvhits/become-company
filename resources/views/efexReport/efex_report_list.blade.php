@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link href="{{ asset('/assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.token-input-input-token input{
    width: 100% !important;
}
    </style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Efax Log Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Portal ID</label>
                                                    <input type="text" autocomplete="off" class="form-control"  id="patient_id" placeholder="Portal Id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>	Type</label>
                                                   <select class="type form-control" id="type">
                                                    <option value="">Select Type</option>
                                                    <option value="Patient">Patient</option>
                                                    <option value="Caregiver">Caregiver</option>
                                                   </select> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" autocomplete="off" name="created_date" class="form-control" readonly id="created_date"  placeholder="Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created By</label>
                                                    <input type="text" autocomplete="off"  class="form-control" id="review_document_user"  placeholder="Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </form>
                            
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadAjaxList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>

                                        <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsv()">Export</a>
                                        
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hideClass" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th >Portal Id</th>
                                <th >Name</th>
                                <th >Type</th>
                                <th >Document Name</th>
                                <th >Fax No</th>
                                
                                <th >Created Date /Created By</th>
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="efax_reponse_id"></span>



            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
@include('include/footer')

<div id="overlay" class="overlay" onclick="closeSidebar()"></div>
<script>
    var _EFAX_LOG_LIST ="{{ url('efax-ajax-list')}}";
    var _EFAX_LOG_CSV ="{{ url('efax-export-csv')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var _SEARCH_NYBEST_USER = "{{ url('search-nybest-user')}}";
    
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />

<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>

<script type="text/javascript" src="{{ asset('assets/modulejs/efaxReport/efaxReport.js')}}?time={{ env('timestamp')}}"></script>

