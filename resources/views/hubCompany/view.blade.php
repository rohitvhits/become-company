@include('include/header')
@include('include/sidebar')
<?php

use Illuminate\Support\Facades\URL;
?>

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<style>
    dl {
        margin-top: 0;
        margin-bottom: 20px;
    }

    ul,
    ol,
    dl {
        padding-left: 0px !important;
    }

    .dl-horizontal dt {
        float: left;
        width: 87px;
        clear: left;
        text-align: right;
        /* overflow: hidden; */
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    h6.fm_1 {
        /* text-align: end;*/
        font-size: 14px;
    }

    dt {
        font-weight: 700;
    }

    .dl-horizontal dd {
        margin-left: 115px;
    }

    .ml-3,
    .rtl .settings-panel .sidebar-bg-options .rounded-circle,
    .rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
    .rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
    .mx-3 {
        margin-left: 1rem !important;
        width: 100%;
    }

    #hr2 .dl-horizontal dd {
        margin-left: 130px;
    }

    #hr2 .dl-horizontal dt {
        width: 101px;
    }

    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 100%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }

    .label-success {
        background-color: #5cb85c;
    }

    .label-danger {
        background-color: #d9534f;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .label-default {
        background-color: #777;
    }

    .custom-toggle-switch .switch {
        position: relative;
        display: inline-block;
        width: 53px;
        height: 28px;
    }

    .custom-toggle-switch .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .custom-toggle-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch input:checked+.slider {
        background-color: #2196F3;
    }

    .custom-toggle-switch input:focus+.slider {
        -webkit-box-shadow: 0 0 1px #2196F3;
        box-shadow: 0 0 1px #2196F3;
    }

    .custom-toggle-switch input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .custom-toggle-switch .slider.round {
        border-radius: 34px;
    }

    .custom-toggle-switch .slider.round:before {
        border-radius: 50%;
    }

    .two-factor-toggle {
        width: max-content !important;
    }

    .agency-detail1 dt {
        width: auto;
        text-align: left;
    }

    .agency-detail1 dd {
        margin-left: 127px;
    }

    .custom-wrapper {
        min-height: auto;
    }

    .error {
        color: Red;
    }

    .action-btns {
        padding-left: 10px !important;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 5px;
        text-align: left;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .loader {
        width: 25px;
    }

    .selection .select2-selection {
        height: 40px;
    }

    .modal-title {
        font-weight: bold;
    }

    .close {
        background: none;
        border: none;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    .form-check-input {
        margin-top: 0.3rem;
    }

    .form-check-label {
        margin-left: 1.25rem;
    }

    /* .modal-footer {
        display: flex;
     
        align-items: center;
    } */

    .hide {
        display: none;
    }

    .loading-shimmer {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        background-size: 1000px 100%;
    }

    th {
        text-align: left;
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }

        100% {
            background-position: 1000px 0;
        }
    }

    .circle {
        height: 70px;
        width: 70px;
        border-radius: 35px;
    }

    .line {
        height: 20px;
        width: 300px;
    }

    .select2-container {
        z-index: 99999 !important;
    }
    .highlightError {
        outline: 1px solid red;
}
</style>
<!--main-container-part-->

<div class="main-panel">
    <div class="content-wrapper custom-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Company # <?= $agencyDetails->id . " - " . ucwords($agencyDetails->agency_name) . " " ?> </h4>
                </div>

                <div class="d-md-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                    </div>
                </div>
            </div>

        </div>

        <div id="msgs"></div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h4>Hub Company Details</h4>
                            </div>
                            <div class="col-sm-9">
                                @can('hub-company-delete')
                                <a href="javascript:void(0);" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1" onclick="deleteRecordAgencies('{{$id}}')" title="Delete"><i class="mdi mdi-delete"></i>Delete</a>
                                @endcan

                                @can('hub-company-edit')
                                <a href="<?php echo URL::asset("/"); ?>hub-company/edit/<?= $id ?>" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1" title="Edit"><i class="mdi mdi-pencil"></i>Edit</a>
                                @endcan
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!-- <h5>Agency Details</h5> -->
                                            <input type="hidden" id="is_sms_status" value="{{ $agencyDetails->is_sms}}">
                                            <dl class="dl-horizontal agency-detail1">
                                                <dt> Agency Name</dt>
                                                <dd> <?= ($agencyDetails->agency_name != '') ? ucwords($agencyDetails->agency_name) : '-'; ?> </dd>

                                                <dt> Email</dt>
                                                <dd> <?= ($agencyDetails->email != '') ? ($agencyDetails->email) : '-'; ?> </dd>
                                                <dt> Phone</dt>
                                                <dd> <?= ($agencyDetails->phone != '') ? $agencyDetails->phone : '-'; ?> </dd>
                                                <dt> Address1</dt>
                                                <dd> <?= ($agencyDetails->address1 != '') ? ($agencyDetails->address1) : '-'; ?> </dd>


                                                <dt> State</dt>
                                                <dd> <?= ($agencyDetails->state != '') ? ($agencyDetails->state) : '-'; ?> </dd>
                                                <dt> City</dt>
                                                <dd> <?= ($agencyDetails->city != '') ? $agencyDetails->city : '-'; ?> </dd>
                                                <dt> Zip Code</dt>
                                                <dd> <?= ($agencyDetails->zip_code != '') ? $agencyDetails->zip_code : '-'; ?> </dd>
                                            </dl>
                                        </div>                                     
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper custom-wrapper">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link" id="hub-company-token-tab" data-toggle="tab" href="#hub-company-token-1" role="tab" aria-controls="hub-company-token-1" token-selected="false" onclick="getAllHubGenerateToken()">Generate Token</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="hub-company-token-1" role="tabpanel" aria-labelledby="hub-company-token-tab">
                            <div class="row">
                                <div class="col-sm-6 card-title">
                                    <h4 class="card-title">Generate Token</h4>
                                </div>
                                <div class="col-sm-6">
                                    @can('agency-generate-token')
                                    <a href="javascript:void(0)" data-toggle="modal" onclick="showModalGenerate()" data-target="#hub_company_generate_token" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"></i>Generate Token</a>
                                    @endcan
                                </div>

                            </div>
                            <div class="table-responsive" id="token_ajax_id">



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('hubCompany._partial.hub_company_token_modal')
@include('include/footer')

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vertical-layout-light/daterangepicker.css')}}" />
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script>
    var _AGENCY_ID = '{{ $agencyDetails->id}}';
    var _CSRF_TOKEN = "{{ csrf_token()}}";
    function showModalGenerate() {
        $('#agency_notes_token_error').html("");
        $('#agency_notes_token').val("");
    }

    $('#save-hub-company-token').click(function(e){
        var formData = new FormData($('#hubFormGenerateSubmit')[0]);
            formData.append('_token',_CSRF_TOKEN); 
            formData.append('agency_id',_AGENCY_ID); 
            $.ajax({
                url: "{{ url('hub-company/generate-token')}}",
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    toastr.success(res.error_msg)

                    $('#hub_company_generate_token').modal('show');
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            })
      
    })

    function getAllHubGenerateToken(page =1){
        console.log("asdasdas")
        $.ajax({
            async:false,
            global:false,
            url: "{{ url('hub-company/generate-token-list')}}",
            data:{
                'agency_id':_AGENCY_ID,
                page:page,
                type:'token'
            },
            success:function(res){
                $('#token_ajax_id').html("")
                $('#token_ajax_id').html(res)
        
            }
        })
        return false;
    }
    getAllHubGenerateToken();
</script>
