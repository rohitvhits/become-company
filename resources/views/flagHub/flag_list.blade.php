@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/tabs.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('/assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
<style>
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .wmd-view-topscroll,
    .wmd-view {
        overflow-x: scroll;
        overflow-y: hidden;
        border: none 0px red;
    }

    .wmd-view {
        overflow: auto;
        height: calc(100vh - 250px);
    }

    .wmd-view-topscroll {
        height: 20px;
    }

    .scroll-div1 {

        overflow-x: scroll;
        overflow-y: hidden;
        height: 20px;
        width: calc(1650px - -17px) !important;
    }

    .scroll-div2 {
        height: 20px;
    }

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 tr th:last-child {
        width: 100px;
    }

    .table-width1 tr th:nth-child(10) {
        width: 100px;
    }

    .table-width1 {
        background-color: #fff;
    }

    .table-width1 tr th:nth-child(11) {
        width: 152px;
    }

    .table-width1 tr th:nth-child(12) {
        white-space: nowrap;
    }

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-card1 {
        margin-bottom: 20px;
    }

    .search-card1 .form-group {
        margin-bottom: 0.5rem;
    }

    .search-card1 label {
        margin-bottom: 0;
    }

    .search-card1 .card-body {
        padding-bottom: 10px;
    }

    .search-card1 input[type=text] {
        border-radius: 4px;
        border-color: #aaa;
    }

    .srch-icon {
        padding: 0 !important;
        width: 40px;
        height: 40px;
    }

    .no_warp {
        white-space: nowrap;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .tableData .add_new_record .left_record {
        left: -9px;
        right: unset !important;
    }

    .tableData .add_new_record {
        position: absolute;
        top: 0;

        background: #00BBE0;
        padding: 1px 5px;
        font-size: 10px;
        color: #fff;
        border-radius: 2px 2px 2px 2px;
        font-size: 10px !important;
    }

    .tableData .add_new_record::after {
        position: absolute;
        content: "";
        bottom: -6px;
        right: 0px;
        background: #b7b7b8;
        z-index: -1;
        width: 10px;
        height: 10px;

    }

    .tableData .add_new_record::after {
        left: 0px;
        border-radius: 0px 0px 0px 50px;
    }

    .service_id_by_patient_type .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .tabs.js-tabs .tabs__toggle-group .tabs__toggle.tabs__toggle--active {
        background-color: #00BBE0 !important;
    }

    .tabs.js-tabs .tabs__toggle-group .tabs__toggle.tabs__toggle--active .active {
        color: white !important;
    }

    .note-container {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f7f7f7;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Note Header */
    .note-header {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: #555;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        background: #ddd;
    }

    /* Note Content */
    .note-content {
        font-size: 14px;
        color: #333;
        line-height: 1.5;
        padding: 5px;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Flagged</h5>
            <div class="page-rightbtns">
                <div>



                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="tabs--container">
                                    <div class="tabs js-tabs">
                                        <div class="tabs--scrollable">
                                            <div class="tabs__toggle-group">
                                                <div class="tabs__toggle tabs__toggle--active">
                                                    <a class="nav-link active" href="#appointment-section" data-toggle="tab" onclick="loadHubFlagList(1)">Hub Records</a>
                                                </div>
                                                <div class="tabs__toggle" onclick="loadDocFlagList(1)">
                                                    <a class="nav-link" href="#document-section" data-toggle="tab">Document</a>
                                                </div>
                                              <div class="tabs__toggle" onclick="loadTaskFlagList(1)">
                                                    <a class="nav-link" href="#task-section" data-toggle="tab">Task</a>
                                                </div>
                                                <div class="tabs__toggle"  onclick="loadNotesFlagList(1)">
                                                    <a class="nav-link" href="#notes-section" data-toggle="tab">Notes</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tabs__tabs-group">
                                            <div class="tabs__tab">
                                                @include('flagHub/_partial/appointment_section')
                                            </div>
                                            <div class="tabs__tab">
                                                @include('flagHub/_partial/document_section')
                                            </div>
                                           <div class="tabs__tab">
                                                @include('flagHub/_partial/task_section')
                                            </div>
                                            <div class="tabs__tab">
                                                @include('flagHub/_partial/notes_section')
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
</div>

    @include('include/footer')
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
    <script src="{{ asset('/assets/js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
    <script src="{{ asset('/assets/css/toastr/toastr.min.js')}}"></script>
    <script src="{{ asset('/assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('/assets/js/select2.js')}}"></script>
    <script src="{{ asset('/assets/js/jquery.tokeninput.js')}}"></script>
    <script src="{{ asset('/assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
    <script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
    <script>
        var _FLAG_HUB_LIST = "{{ url('flag-hub-ajax-list') }}";
        var _FLAG_DOC_LIST = "{{ url('hub-flag-doc-ajax-list') }}";
        var _FLAG_TASK_LIST = "{{ url('hub-flag-task-ajax-list') }}";
        var _FLAG_NOTES_LIST = "{{ url('hub-flag-notes-ajax-list') }}";
        var _SEARCH_PATIENT_LIST = "{{ url('search-nybest-user') }}";
        var _CSRF_TOKEN = "{{ csrf_token() }}";
        var _FLAG_MARK_LIST = "{{ url('hub-mark-flag-read') }}";
    </script>
    <script src="{{ asset('assets/modulejs/patient/hub_flag.js')}}?time={{ env('timestamp')}}"></script>
    <script>
        function patientReasonDescription(id){
            var content = $('#preason'+id).text();
            commonModal(content)
        }
        function patientDocumentReasonDescription(id){
            var content = $('#preasondoc'+id).text();
            commonModal(content)
        }
 $(":input").inputmask(); 
    $("#ssn").keyup(function () {
    var val = this.value.replace(/\D/g, "");
    val = val.replace(/^(\d{3})/, "$1-");
    val = val.replace(/-(\d{2})/, "-$1-");
    val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
    this.value = val;
    });
</script>