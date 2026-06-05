@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/user_agency_report.css')}}">
<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Agency User Report List</h5>
            <div class="page-rightbtns">
                <div>
                    
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            @csrf
                            <div class="row">
                            <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Agency</label>
                                        <div class="col-sm-12">
                                            <select name="agency_id" class="form-control">
                                                <option value="">Select Agency</option>
                                                @foreach($agencyList as $agency)
                                                    @php $agencyId = $agency->id @endphp
                                                    <option value="{{$agency->id}}" @if(isset($agency_id) && $agency_id == $agencyId ) selected @endif>{{$agency->agency_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">First Name</label>
                                        <div class="col-sm-12 ">
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="first_name" id="first_name" value="{{ $first_name??'' }}">
                                        </div>
                                        <span class="error ml-2" id="error_all"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Last Name</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="last_name" id="last_name" value="{{ $last_name??'' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Email</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" autocomplete="off" name="email"
                                                id="email" value="{{ $email??''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Record Type</label>
                                        <div class="col-sm-12">
                                            <select name="record_access" class="form-control">
                                                <option value="All">All</option>
                                                <option value="Caregiver" @if(isset($record_access) && $record_access =='Caregiver') selected @endif>Caregiver</option>
                                                <option value="Patient" @if(isset($record_access) && $record_access =='Patient') selected @endif>Patient</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search"
                                            class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                            value="Search">

                                            <a href="javascript:void(0)" class="btn btn-light btn-sm btn-rounded btn-fw ml-1" onclick="reset_data()" ><i class="mdi mdi-reload"></i>Reset</a>

                                            @can('agency-user-report-export')
                                                <a href="" class="btn btn-success btn-sm btn-rounded btn-fw ml-1" id="test_user"
                                                    onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                                            @endcan
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 table-responsive">
                <span id="agency_report_html"></span>
            </div>
        </div>
    </div>
    <div class="loader-sec" style="display:none">
         <div id="cover-spin"></div>
         </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    <script>
        var _AGENCY_DATA ='{{ url("agency-user-report-ajax") }}';
        var _AGENCY_EXPORT_DATA ='{{ url("agency-user-report-export") }}';
    </script>
<script src="{{ asset('/assets/modulejs/agency_user_report/agency_user_report.js')}}?time={{ env('timestamps')}}"></script>

    @include('include/footer')
