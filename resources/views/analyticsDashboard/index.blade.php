@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/global.css') }}" type="text/css" />

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 font-weight-bold">Dashboard</h5>
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    <div class="col-md-4">
                        <select class="form-control js-example-basic" id="record_type">
                            <option value="">Select Type</option>
                            <option value="patient">Patient</option>
                            <option value="caregiver">Caregiver</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control js-example-basic-multiple" multiple id="agency_id">
                            <option value="">Select Agency</option>
                            @foreach($agencyList as $agn)
                            <option value="{{ $agn->id }}">{{ $agn->agency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card common-card-box" style="height: 100%; border-radius:4px;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="dash-sm-card">
                                                <div class="row">
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-info shimmer">
                                                            <div class="inner">
                                                                <h3 id="arrived">0</h3>
                                                                <p>Arrived</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-check-circle"></i>
                                                            </div>
                                                            <a target="_blank" id="arrived_link" href="{{ url('patient-service-requested')}}?status[]=arrived"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-warning shimmer">
                                                            <div class="inner">
                                                                <h3 id="processing">0<sup style="font-size: 20px"></sup></h3>
                                                                <p>Processing</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-cog"></i>
                                                            </div>
                                                            <a target="_blank" id="processing_link" href="{{ url('patient-service-requested')}}?status[]=processing"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-success shimmer">
                                                            <div class="inner">
                                                                <h3 id="check_in">0</h3>
                                                                <p>Check In</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <a target="_blank" id="checkin_link" href="{{ url('patient-service-requested')}}?status[]=checkin"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="dash-side-box">
                                                        <div class="box info-box card common-card-box p-0">
                                                            <div class="title">
                                                                <h5 class="mb-0">Location Wise Status Data</h5>
                                                            </div>
                                                            <div class="location-wise-data-loader" style="display:flex">
                                                                <div class="col-md-6 pl-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Location</th>
                                                                                <th>Processing</th>
                                                                                <th>Arrived</th>
                                                                                <th>Checkin</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6 pr-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Location</th>
                                                                                <th>Processing</th>
                                                                                <th>Arrived</th>
                                                                                <th>Checkin</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row basic-detail-row"
                                                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;padding-right: 0px;" id="location_wise_status_data">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="dash-side-box">
                                                        <div class="box info-box card common-card-box p-0">
                                                            <div class="title">
                                                                <h5 class="mb-0">Agency Wise Status Data</h5>
                                                            </div>
                                                            <div class="agency-wise-data-loader" style="display:flex">
                                                                <div class="col-md-6 pl-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Agency</th>
                                                                                <th>Processing</th>
                                                                                <th>Arrived</th>
                                                                                <th>Checkin</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6 pr-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Agency</th>
                                                                                <th>Processing</th>
                                                                                <th>Arrived</th>
                                                                                <th>Checkin</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                                <td class="text-center">0</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row basic-detail-row"
                                                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;padding-right: 0px;" id="agency_wise_status_data">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row  mb-2">
                        <div class="col-md-6">
                            <div class="dash-side-box">
                                <div class="box info-box card common-card-box p-0">
                                    <div class="title">
                                        <div>
                                            <h5 class="mb-0" style="display: flex;justify-content: space-between;">Current Inprogress and Check In <a target="_blank" href="{{ url('patient-service-requested')}}" class="small-box-footer-link">View All<i class="fa fa-arrow-circle-right"></i></a> </h5>
                                        </div>
                                    </div>
                                    <div class="current-inprogress-loader ml-3" style="display:flex">
                                        <div class="col-md-12">
                                            <div class="row btm-brder">
                                                <div class="row col-md-12">
                                                    <div class="shimmer-loader mb-1 col-md-6">
                                                        <h6 class="mb-1">

                                                        </h6>
                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                        <div class="col-md-6">

                                                        </div>
                                                        <div class="col-md-6" style="display:flex;justify-content: end">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row basic-detail-row" id="current_inprogress"
                                        style="max-height: calc(100vh - 80px);overflow-y:auto;margin-right:0px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dash-side-box">
                                <div class="box info-box card common-card-box p-0">
                                    <div class="title">
                                        <h5 class="mb-0">Recently Added Notes</h5>
                                    </div>
                                    <div class="recent-notes-loader ml-3" style="display:flex">
                                        <div class="col-md-12">
                                            <div class="row btm-brder">
                                                <div class="row col-md-12">
                                                    <div class="shimmer-loader mb-1 col-md-6">
                                                        <h6 class="mb-1">

                                                        </h6>
                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                        <div class="col-md-6">

                                                        </div>
                                                        <div class="col-md-6" style="display:flex;justify-content: end">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row basic-detail-row"
                                        style="max-height: calc(100vh - 80px);overflow-y:auto;margin-right:0px;" id="recent_notes">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="dash-side-box">
                        <div class="box info-box card common-card-box p-0">
                            <div class="title">
                                <h5 class="mb-0">New Visiting Aid</h5>
                            </div>
                            <div class="visiting-data-loader ml-3" style="display:flex">
                                <div class="col-md-12">
                                    <div class="row btm-brder">
                                        <div class="row col-md-12">
                                            <div class="shimmer-loader mb-1 col-md-6">
                                                <h6 class="mb-1">

                                                </h6>
                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                <div class="col-md-6">

                                                </div>
                                                <div class="col-md-6" style="display:flex;justify-content: end">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row basic-detail-row"
                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;" id="visiting_aid_type">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dash-side-box">
                        <div class="box info-box card common-card-box p-0">
                            <div class="title">
                                <h5 class="mb-0">Recently Upload Document</h5>
                            </div>
                            <div class="document-data-loader ml-3" style="display:flex">
                                <div class="col-md-12">
                                    <div class="row btm-brder">
                                        <div class="row col-md-12">
                                            <div class="shimmer-loader mb-1 col-md-6">
                                                <h6 class="mb-1">

                                                </h6>
                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                <div class="col-md-6">

                                                </div>
                                                <div class="col-md-6" style="display:flex;justify-content: end">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row basic-detail-row"
                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;" id="document_recent_data">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
               
                </div>
                <div class="col-md-4 mb-2">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="dash-side-box mb-2">
                                <div class="box info-box card common-card-box p-0 recent-height">
                                    <div class="title">
                                        <h5 class="mb-0">Recently Updated Status</h5>
                                    </div>
                                    <div class="recent-updates-loader ml-3" style="display:flex">
                                        <div class="col-md-12">
                                            <div class="row btm-brder">
                                                <div class="row col-md-12">
                                                    <div class="shimmer-loader mb-1 col-md-6">
                                                        <h6 class="mb-1">

                                                        </h6>
                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12">

                                                    </div>
                                                    <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                        <div class="col-md-6">

                                                        </div>
                                                        <div class="col-md-6" style="display:flex;justify-content: end">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row basic-detail-row" style="max-height: calc(100vh - 0px);overflow-y:auto;margin-right:0px;" id="recent_updated_status">
                                    </div>
                                </div>
                            </div>
                            <div class="dash-side-box">
                        <div class="box info-box card common-card-box p-0">
                            <div class="title">
                                <h5 class="mb-0">Visiting Aid Due Date</h5>
                            </div>
                            <div class="visiting-due-data-loader ml-3" style="display:flex">
                                <div class="col-md-12">
                                    <div class="row btm-brder">
                                        <div class="row col-md-12">
                                            <div class="shimmer-loader mb-1 col-md-6">
                                                <h6 class="mb-1">

                                                </h6>
                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12">

                                            </div>
                                            <div class="shimmer-loader mb-1 col-md-12" style="display:flex">
                                                <div class="col-md-6">

                                                </div>
                                                <div class="col-md-6" style="display:flex;justify-content: end">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row basic-detail-row"
                                style="max-height: calc(100vh - 80px);overflow-y:auto;margin-right:0px;" id="visiting_due_data">
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>



           
        </div>
    </div>

    @include('include/footer')
    @include('analyticsDashboard/js_dashboard')