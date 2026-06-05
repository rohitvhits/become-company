@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>
<div class="main-panel main-page-box" style="margin-bottom:15%">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Deleted Patient Management</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Patient ID</label>
                                            <input type="text" name="patient_id" id="patient_id" class="form-control" placeholder="Enter Patient ID">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Agency Name</label>
                                            <select name="agency_fk" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100">
                                                <option value="">Select Agency</option>
                                                @foreach($agencyList as $rwAgency)
                                                    <option value="{{$rwAgency->id}}">{{$rwAgency->agency_name}}</option>
                                                @endforeach
                                             </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-12">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadDeletedPatients(1)">
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()">
                                            <i class="mdi mdi-reload"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id hideClass">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Created Date/Created By</th>
                                    <th>Deleted Date/Deleted By</th>
                                    <th>Action</th>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="ajax_response_id"></span>
            </div>
        </div>
    </div>
</div>

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script>
    var _DELETED_PATIENT_AJAX = "{{ url('deleted-patient-ajax-list')}}";
    var _DELETED_PATIENT_REACTIVATE = "{{ url('deleted-patient-reactivate')}}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
</script>
<script type="text/javascript" src="{{ asset('/assets/modulejs/deletedPatientManagement/deleted_patient.js')}}"></script>
