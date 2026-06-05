@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/docDashboard/docDashboard.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Document Dashboard(<span id="total_record_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                    <a class=" btn btn-primary btn-sm cust-right-btn" onclick="documentData(1)"><i class="mdi mdi-refresh"></i>Refresh</a>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    @if($agencyCnt > 1)
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency['id']; ?>">
                                                                <?php echo $rwAgency['agency_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Portal ID</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="patient_id" placeholder="Enter Portal ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <select class="form-control" name="type" id="patient_type" class="form-control">
                                                        <option value="">Select Type</option>
                                                        <option value="Caregiver">Caregiver</option>
                                                        <option value="Patient">Patient</option>
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
                                                    <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="appointment_date" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created By</label>
                                                    <input type="text" autocomplete="off" class="form-control" name="created_by" id="document_created_by" style="width:100% !important">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Services</label>
                                                    <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="service_id[]" id="service_id">
                                                        @foreach ($serviceList as $service)
                                                        <option value="{{$service->id}}">{{$service->name}}</option>
                                                        @endforeach
                                                    </select>
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
                                            value="Search" onclick="documentData()">
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refreshDocList();"><i class="mdi mdi-reload"></i> Clear</a>
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
                <div class="document-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <th nowrap>#</th>
                                <th nowrap>Agency Name</th>
                                <th nowrap>Portal ID</th>
                                <th nowrap>Patient Name</th>
                                <th nowrap>Portal Status</th>
                                <th nowrap>Document Name</th>
                                <th nowrap>Attachment</th>
                                <th nowrap>Requested Id</th>
                                <th nowrap>Document Completion Date</th>
                                <th nowrap>Document Status</th>
                                <th nowrap>Created Date /<br> Created By</th>
                                <th nowrap>Action</th>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="12"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="doc_type_table_list">
                </span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
@include('documentReport._partial.modal.edit_service_document')
@include('documentReport._partial.modal.doc_review_model')
@include('patient._partial.modal.patient_document.view_document_details_modal')
@include('include/footer')
@include('docDashboard/js_dashboard')