@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet"
    href="<?php echo URL::to('/'); ?>/assets/modulejs/css/patient-new-design.css?time={{ env('timestamp') }}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}" />
<link href="<?php echo URL::to('/'); ?>/assets/modulejs/css/task-module.css" rel="stylesheet" type="text/css" />
<style>
    .created-label-margin {
        margin-bottom: -10px !important;
    }

    .div-top-margin {
        /* margin-top:-6px !important; */
    }

    .ds-card {
        background: #d7ffd8;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        transition: 0.3s ease;
    }

    .ds-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .icon {
        font-size: 32px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .details h3 {
        font-size: 16px;
        margin: 0;
        color: #555;
    }

    .details p {
        font-size: 20px;
        font-weight: bold;
        margin: 5px 0 0;
        color: #111;
    }

    .bg-orange {
        background-color: #f39c12;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
    .badge {
    border-radius: 3px;
    font-size: 15px;
    line-height: 1;
    padding: 0.187rem 1.0rem;
    font-weight: 500;
}
</style>


<!--main-container-part-->
<div class="main-panel view-appointmenr-main">
    <div class="content-wrapper px-3 pb-0">
        <div class="dashboard-header d-flex flex-column ">
            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap   mb-2">
                     @php $class = ''; @endphp
                    @if($record->flag == 1)
                    @php $class = 'highlight-hub-record badge badge-outline-danger'; @endphp
                    @endif
                    <div class="d-flex align-items-center  {{$class}}">
                        <h4 class="mb-0 font-weight-bold mr-1">ID #
                            <?= $record->id . ' - ' . ucwords($record->first_name) . ' ' . ucwords($record->last_name) . ' ' ?>
                        </h4><?php echo $record->phone; ?>
                        <span>
                            <select name="agency_id[]" id="hub_agency_id" class="col-md-12 ml-2">
                                <option value="">Select Agency</option>
                            </select>
                        </span>
                    </div>
                    <div class="appoin-btn-wrapper">
                         @can('flag-change-status')
                                            @if($record->flag == 0)
                                            @php $flag = 'Flag'; @endphp
                                            @php $color = 'btn-outline-secondary'; @endphp
                                            @else
                                            @php $flag = 'Flagged'; @endphp
                                            @php $color = 'btn-success'; @endphp
                                            @endif
                                            <a onclick="flagChange();" class="pull-right btn {{$color}} btn-rounded  btn-sm  d-none d-md-block mr-2" style="padding: 0.282rem 12px" title="{{ $flag}}"><i class="fa fa-flag"></i> &nbsp; {{$flag}}</a>
                                            @endcan
                        @can('hub-record-sent-dependent-link')
                            <a href="javascript:void(0);" onclick="sendSMS('{{ $record->id }}')"
                                class="pull-right btn btn-success btn-rounded  btn-sm  d-none d-md-block" class="pull-right"
                                title="Send Dependent Link"><i class="fa fa-send-o"></i>
                            </a>
                        @endcan
                        @can('hub-record-delete')
                            <a href="javascript:void(0);" onclick="deleteRecord('{{ $record->id }}')"
                                class="pull-right btn btn-danger btn-rounded  btn-sm  d-none d-md-block" class="pull-right"
                                title="Delete"><i class="fa fa-trash"></i>
                                Delete</a>
                        @endcan

                    </div>
                </div>
                <div class="top-detail-sec">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="top-basic-detail-sec">
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Gender</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <span id="top_gender_display">
                                                    @if(isset($record->gender) && $record->gender != '')
                                                        {{ ucfirst($record->gender) }}@if(strtolower($record->gender) == 'other') ({{ $record->other_gender }})@endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="">
                                            <div class="">
                                                <div class="col-md-12">
                                                    <dt class="detail-title  mb-2">Mobile</dt>
                                                </div>
                                                <div class="col-md-12">
                                                    <dl>
                                                        <span id="hub_mobile_id"><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->mobile); ?></span>
                                                        <a class="mr-1" data-toggle="modal"
                                                            data-target="#mobile-number" data-whatever="@mdo"
                                                            title="Mobile" onclick="updateMobileDetails()"><i
                                                                class="fa fa-edit"></i></a>
                                                        <a title="Copy Mobile No"
                                                            onclick="mobileOrPhoneCopy('mobile')"><i
                                                                class="mdi mdi-content-copy"></i></a>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Phone</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <span id="hub_phone_id"><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->phone); ?></span>
                                                <a class="mr-1" data-toggle="modal" data-target="#phone-number"
                                                    data-whatever="@mdo" title="Phone"
                                                    onclick="updatePhoneDetails()"><i class="fa fa-edit"></i></a>

                                                <a title="Copy Phone No" onclick="mobileOrPhoneCopy('phone')"><i
                                                        class="mdi mdi-content-copy"></i></a>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Status</dt>
                                        </div>
                                        <div class="col-md-12" id="status_id_text" data-status="{{ $record->status }}">
                                            <dl>
                                                <span id="status-value">
                                                    @if ($record->status == 'active')
                                                        <span><label
                                                                class="badge badge-success">{{ $record->status != '' ? ucfirst($record->status) : '' }}</label></span>
                                                    @else
                                                        <span><label
                                                                class="badge badge-danger">{{ $record->status != '' ? ucfirst($record->status) : '' }}</label></span>
                                                    @endif
                                                </span>
                                                @can('hub-record-status-change')
                                                    <a class="mr-1" data-toggle="modal" data-target="#status"
                                                        data-whatever="@mdo" title="Status"
                                                        onclick="updateStatusDetails()"><i class="fa fa-edit"></i></a>
                                                @endcan
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <div class="dashboard">
                                    <div class="ds-card">
                                        <div class="icon bg-orange ">📝</div>
                                        <div class="details">
                                            <h3>Total NyBest Medical <br> Requested</h3>
                                            <p id="total-bybest-request"></p>
                                        </div>
                                    </div>
                                </div>

                                @can('hub-record-eligibility')
                                    <div class="dashboard">
                                        <div class="ds-card">
                                            <div class="icon bg-orange ">📅</div>
                                            <div class="details">
                                                <h3>Last Eligibility</h3>
                                                <p id="last-eligibility"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('hub-record-uitlization')
                                    <div class="dashboard">
                                        <div class="ds-card">
                                            <div class="icon bg-orange ">📅</div>
                                            <div class="details">
                                                <h3>Last Utilization</h3>
                                                <p id="last-utilization"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 grid-margin stretch-card mb-4" style="margin-bottom:10% !important">
                        <div class="card">
                            <div class="left-section-main info-tab-sec">
                                <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                    <li class="active"><a href="#personal-info-section" data-toggle="tab"> <i
                                                class="fa fa-info-circle"></i> &nbsp;Personal Information</a>
                                    </li>
                                    <li><a href="#document-section" data-toggle="tab"
                                            onclick="loadDocumentAjaxList()"> <i class="mdi mdi-file-document"></i>
                                            &nbsp;Document</a>
                                    </li>
                                    <li><a href="#notes-section" data-toggle="tab" onClick="loadAllNotes()"> <i
                                                class="mdi mdi-note"></i> &nbsp;Notes</a></li>

                                    {{-- <li><a href="#sms-logs-section" data-toggle="tab" onClick="smsLogs(1)"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M64 0C28.7 0 0 28.7 0 64L0 352c0 35.3 28.7 64 64 64l96 0 0 80c0 6.1 3.4 11.6 8.8 14.3s11.9 2.1 16.8-1.5L309.3 416 448 416c35.3 0 64-28.7 64-64l0-288c0-35.3-28.7-64-64-64L64 0z" />
                                            </svg> &nbsp;SMS
                                            Logs</a>
                                    </li> --}}

                                    <li><a href="#text-messages-section" data-toggle="tab"
                                            onClick="loadAllTextMessages()"> <svg xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512">
                                                <path
                                                    d="M64 0C28.7 0 0 28.7 0 64L0 352c0 35.3 28.7 64 64 64l96 0 0 80c0 6.1 3.4 11.6 8.8 14.3s11.9 2.1 16.8-1.5L309.3 416 448 416c35.3 0 64-28.7 64-64l0-288c0-35.3-28.7-64-64-64L64 0z" />
                                            </svg> &nbsp;Text Messages</a>
                                    </li>
                                    <li><a href="#hub-logs-section" data-toggle="tab" onClick="loadAllHubLogs()"> <i
                                                class="mdi mdi-eye"></i> &nbsp;Hub Logs</a></li>
                                    <li><a href="#hub-nybest-section" data-toggle="tab" onClick="loadAllNyBest()">
                                            <img src="{{ asset('/img/favicon.png') }}"
                                                style="height: 20px;width: 20px;"> &nbsp;NyBest Medical Request</a>
                                    </li>

                                    @can('hub-record-uitlization')
                                        <li><a href="#uitlization-section" data-toggle="tab"
                                                onClick="loadAllUitlization()"> <i class="mdi mdi-calendar-check"></i>
                                                &nbsp;Uitlization</a></li>
                                    @endcan
                                    @can('hub-record-eligibility')
                                        <li><a href="#eligibility-section" data-toggle="tab"
                                                onClick="loadAllEligibility()"> <i class="mdi mdi-calendar-check"></i>
                                                &nbsp;Eligibility </a></li>
                                    @endcan
                                    @can('hub-record-task')
                                     <li><a href="#task-section" data-toggle="tab" onclick="getTaskList()"><i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                                &nbsp;Task Section</a></li>
                                     @endcan

                                      @can('hub-record-clinical')
                                        <li><a href="#clinical-section" data-toggle="tab"
                                                onClick="loadAllClinical()"> <i class="mdi mdi-calendar-check"></i>
                                                &nbsp;Clinical</a></li>
                                                  @endcan
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content left-section-tab-content">
                                    <div class="tab-pane active" id="personal-info-section">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="box info-box card basic-detail-div">
                                                            <div class="title">
                                                                <h5><i class="mdi mdi-information mr-1"></i>Basic
                                                                    Details <a class="show pull-right"
                                                                        onclick="setBasicDetails()"><i
                                                                            class="fa fa-edit" title="Edit"></i></a>
                                                                    <a class="hide pull-right"
                                                                        onclick="setBasicDetails()"><i
                                                                            class="fa fa-close"
                                                                            title="Close"></i></a> <a
                                                                        class="hide pull-right mr-2"
                                                                        onclick="saveBasicDetails()"><i
                                                                            class="fa fa-save" title="Save"></i></a>
                                                                </h5>
                                                            </div>
                                                            <div class="row basic-detail-row">
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt> First Name</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_first_name">
                                                                                <?php echo $record->first_name . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide"> <input type="text"
                                                                                    class="form-control charCls"
                                                                                    placeholder="Enter First Name "
                                                                                    id="first_name_id"
                                                                                    name="first_name"
                                                                                    value="<?php echo $record->first_name; ?>">
                                                                                <span id="first_name_error"
                                                                                    class="error mt-2"><?php echo $errors->add_agency->first('first_name'); ?></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt> Middle Name</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_middle_name">
                                                                                <?php if (isset($record->middle_name) && $record->middle_name != '') {
                                                                                    echo $record->middle_name;
                                                                                } else {
                                                                                    echo 'N/A';
                                                                                } ?>
                                                                            </dd>
                                                                            <dd class="hide"> <input type="text"
                                                                                    id="middle_name"
                                                                                    name="middle_name"
                                                                                    placeholder="Middle Name"
                                                                                    value="<?php if (isset($record->middle_name) && $record->middle_name != '') {
                                                                                        echo $record->middle_name;
                                                                                    } ?>"
                                                                                    class="form-control">
                                                                                <span id="radio_type_error"
                                                                                    class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Last Name</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_last_name">
                                                                                <?php if (isset($record->last_name) && $record->last_name != '') {
                                                                                    echo $record->last_name . '<br>';
                                                                                } else {
                                                                                    echo 'N/A';
                                                                                } ?></dd>
                                                                            <dd class="hide"> <input type="text"
                                                                                    class="form-control charCls"
                                                                                    placeholder="Enter Last Name "
                                                                                    id="last_name_id" name="last_name"
                                                                                    value="<?php echo $record->last_name; ?>">
                                                                                <span id="last_name_error"
                                                                                    class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                                                                                <span id="radio_type_error"
                                                                                    class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Date of Birth</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show"> <span
                                                                                    id="patient_dob">
                                                                                    <?php if ($record->dob != '0000-00-00') {
                                                                                        echo Common::convertMDY($record->dob);
                                                                                    } else {
                                                                                        echo '';
                                                                                    } ?>
                                                                                </span>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text" readonly
                                                                                    name="dob"
                                                                                    class="form-control"
                                                                                    placeholder="Select Date of Birth"
                                                                                    id="dob_id"
                                                                                    data-inputmask="'alias': 'datetime'"
                                                                                    data-inputmask-inputformat="mm/dd/yyyy"
                                                                                    im-insert="false"
                                                                                    value="<?php if ($record->dob != '') {
                                                                                        echo date('m/d/Y', strtotime($record->dob));
                                                                                    } ?>">
                                                                                <span id="dob_error"
                                                                                    class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if (Auth()->user()->view_ssn_hub == 1)
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>SSN</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show"> <span
                                                                                        id="patient_ssn">
                                                                                        <span>
                                                                                            {{ common::formatSSN($record->ssn) ?? '-' }}
                                                                                        </span>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text"
                                                                                        name="ssn"
                                                                                        class="form-control"
                                                                                        placeholder="Enter SSN"
                                                                                        id="ssn_id"
                                                                                        value="{{ $record->ssn ?? '-' }}">
                                                                                    <span id="ssn_error"
                                                                                        class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <input type="hidden" name="ssn"
                                                                        class="form-control" placeholder="Enter SSN"
                                                                        id="ssn_id"
                                                                        value="{{ $record->ssn ?? '-' }}">
                                                                @endif
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Gender</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_gender">
                                                                                @if(isset($record->gender) && $record->gender != '')
                                                                                    {{ ucfirst($record->gender) }}@if(strtolower($record->gender) == 'other') ({{ $record->other_gender }})@endif
                                                                                @else
                                                                                    N/A
                                                                                @endif
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <div class="row">
                                                                                    <div class="form-check mr-3 mt-0">
                                                                                        <label class="form-check-label">
                                                                                            <input type="radio" class="form-check-input" name="basic_gender" value="male" @if(strtolower($record->gender) == 'male') checked='checked' @endif> Male
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="form-check mr-3 mt-0">
                                                                                        <label class="form-check-label">
                                                                                            <input type="radio" class="form-check-input" name="basic_gender" value="female" @if(strtolower($record->gender) == 'female') checked='checked' @endif> Female
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="form-check mt-0">
                                                                                        <label class="form-check-label">
                                                                                            <input type="radio" class="form-check-input" name="basic_gender" value="other" @if(strtolower($record->gender) == 'other') checked='checked' @endif> Other
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div id="basic_other_gender_div" class="mt-1" style="{{ (strtolower($record->gender) == 'other') ? '' : 'display:none;' }}">
                                                                                    <input type="text" class="form-control" placeholder="Enter Other Name" id="basic_other_gender" name="basic_other_gender" value="{{ $record->other_gender ?? '' }}">
                                                                                </div>
                                                                                <span id="gender_error" class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="box info-box card address-detail-div">
                                                            <div class="title ">
                                                                <h5><i
                                                                        class="fa fa-address-card mr-1"></i>Address/Contact
                                                                    Details <a class="show pull-right"
                                                                        onclick="setAddressDetails()"><i
                                                                            class="fa fa-edit" title="Show"></i></a>
                                                                    <a class="hide pull-right"
                                                                        onclick="setAddressDetails()"><i
                                                                            class="fa fa-close"
                                                                            title="Close"></i></a> <a
                                                                        class="hide pull-right mr-2"
                                                                        onclick="saveAddressDetails()"><i
                                                                            class="fa fa-save" title="Save"></i></a>
                                                                </h5>
                                                            </div>
                                                            <div class="row basic-detail-row">
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Address1</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_address1">
                                                                                <?php echo $record->address1 . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    placeholder="Enter Address 1"
                                                                                    id="address1" name="address1"
                                                                                    value="{{ $record['address1'] }}">
                                                                                <span id="address1_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt> Address 2</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_address2">
                                                                                <?php echo $record->address2 . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    placeholder="Enter Apt/Suite/Floor"
                                                                                    id="address2" name="address2"
                                                                                    value="{{ $record['address2'] }}">
                                                                                <span id="address2_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>City</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_city">
                                                                                <?php echo $record->city . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control charCls"
                                                                                    placeholder="Enter City"
                                                                                    id="city" name="city"
                                                                                    value="{{ $record['city'] }}"
                                                                                    maxlength="50">
                                                                                <span id="city_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>State</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_state">
                                                                                <?php echo $record->state . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control charCls"
                                                                                    placeholder="Enter State"
                                                                                    id="state" name="state"
                                                                                    value="{{ $record['state'] }}"
                                                                                    maxlength="50">
                                                                                <span id="state_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Country</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_county">
                                                                                <?php echo $record->county == null ? 'N/A' : $record->county . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    id="county" name="county"
                                                                                    readonly
                                                                                    onkeypress="return isNumber(event)"
                                                                                    value="{{ $record->county }}">
                                                                                <span id="county_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Zip Code</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_zipcode">
                                                                                <?php echo $record->zip_code . '<br>'; ?>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    placeholder="Enter Zip Code"
                                                                                    id="zip_code" name="zip_code"
                                                                                    onkeypress="return isNumber(event)"
                                                                                    onchange="getCountyByZipCode(this.value)"
                                                                                    value="{{ $record['zip_code'] }}">
                                                                                <span id="zip_code_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Email</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show" id="basic_email"> <span
                                                                                    id="emergency_email"><?php if ($record->email != '') {
                                                                                        echo $record->email;
                                                                                    } ?></span>
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    placeholder="Enter Email "
                                                                                    id="email" name="email"
                                                                                    value="<?php echo $record->email; ?>">
                                                                                <span id="email_error"
                                                                                    class="error mt-2"></span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="box info-box card other-detail-div">
                                                    <div class="title ">
                                                        <h5><i class="fa fa-list-alt mr-1"></i> Other Details
                                                            <a class="show pull-right" onclick="setOtherDetails()"><i
                                                                    class="fa fa-edit" title="Show"></i></a> <a
                                                                class="hide pull-right" onclick="setOtherDetails()"><i
                                                                    class="fa fa-close" title="Close"></i></a> <a
                                                                class="hide pull-right mr-2"
                                                                onclick="saveOtherDetails()"><i class="fa fa-save"
                                                                    title="Save"></i></a>
                                                        </h5>
                                                    </div>
                                                    <div class="row other-detail-row">

                                                        <div class="col-md-12">
                                                            <div class="row mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Hire Date</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="hire_date_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control charCls"
                                                                            placeholder="Enter Hire Date "
                                                                            id="input_hire_date_id"
                                                                            name="input_hire_date_id"
                                                                            data-inputmask="'alias': 'datetime'"
                                                                            data-inputmask-inputformat="mm/dd/yyyy"
                                                                            style="margin-right:-10px">
                                                                        <span id="input_hire_date_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_hire_date_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row  mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Work Contact</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="work_contact_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control charCls"
                                                                            placeholder="Enter Work Contact"
                                                                            id="input_work_contact_id"
                                                                            name="input_work_contact_id"
                                                                            data-inputmask-alias="(999) 999-9999"
                                                                            im-insert="true">
                                                                        <span id="input_work_contact_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_work_contact_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Work Email</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="work_email_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control"
                                                                            placeholder="Enter Work Email"
                                                                            id="input_work_email_id"
                                                                            name="input_work_email_id">
                                                                        <span id="input_work_email_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_work_email_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Member Id</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="member_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control"
                                                                            placeholder="Enter Member Id"
                                                                            id="input_member_id"
                                                                            name="input_member_id">
                                                                        <span id="input_member_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_member_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Employee Code</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="employee_code_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control"
                                                                            placeholder="Enter Employee Code"
                                                                            id="input_employee_code_id"
                                                                            name="input_employee_code_id">
                                                                        <span id="input_employee_code_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_employee_code_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row mb-2 align-items-center">
                                                                <div class="col-md-5">
                                                                    <dt style="margin-left: -6px;">Last Worked Date
                                                                    </dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd class="show">
                                                                        <span id="last_worked_date_id"></span>
                                                                    </dd>
                                                                    <dd class="hide"> <input type="text"
                                                                            class="form-control charCls"
                                                                            placeholder="Enter Last Worked Date"
                                                                            id="input_last_work_date_id"
                                                                            name="input_last_work_date_id"
                                                                            data-inputmask="'alias': 'datetime'"
                                                                            data-inputmask-inputformat="mm/dd/yyyy">
                                                                        <span id="input_last_work_date_id_error"
                                                                            class="error"
                                                                            style="font-size:smaller"><?php echo $errors->add_agency->first('input_last_work_date_id'); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <dt>Created Date</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd>
                                                                        <span><?php echo Common::convertMDYTime($record->created_date); ?></span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <dt>Created By</dt>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <dd>
                                                                        <span>{{ $record->createdBy }} @if ($record->userTypes != '')
                                                                                ({{ $record->userTypes }})
                                                                            @endif
                                                                        </span>
                                                                    </dd>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="box info-box card other-detail-div">
                                                    <div class="title ">
                                                        <h5><i class="fa fa-list-alt mr-1"></i> Dependent records <a
                                                                class="show pull-right"
                                                                onclick="loadDependentData(1)"><i
                                                                    class="fa fa-refresh mr-1"
                                                                    title="Refresh"></i></a> <a
                                                                class="show pull-right"
                                                                onclick="openAddChildForm()"><i
                                                                    class="fa fa-plus mr-1" title="Add"></i></a>
                                                        </h5>
                                                    </div>
                                                    <div class="row other-detail-row">
                                                        <div id="child_table">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="document-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">Document</p>
                                            <?php if ($user['user_type_fk'] == 184 || ($user['user_type_fk'] == 2 || $user['user_type_fk'] == 6)) { ?>
                                            <p class="mb-0 tx-13">
                                                <a data-toggle="modal" onclick="showDocModal()"
                                                    class="pull-right btn btn-info btn-sm d-none d-md-block"
                                                    data-target="#hub-document-add" data-whatever="@mdo"><i
                                                        class="mdi mdi-plus"></i>
                                                    Add</a>
                                            </p>
                                            <?php } ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="loader-main" id="loaderAlayaSkillLoaded"
                                                    style="display:none">
                                                    <div class="loader-inner">
                                                        <img src="{{ asset('/ajax-loader.gif') }}" class=""
                                                            alt="loader">
                                                    </div>
                                                </div>
                                                <div id="hub_document_response_list"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="notes-section">
                                        @include('hubRecord.modal.hub_record_notes_section')
                                    </div>
                                    <div class="tab-pane" id="text-messages-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">Text Message</p>
                                            <div class="pull-right">
                                                <img src="{{ asset('/ajax-loader.gif') }}" alt="loader"
                                                    id="loadertag122" style="display: none; ">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-chat-messages" id="text-sms-messages">
                                                    <div id="text-chat-messages-inner" class="text-notes-messages">
                                                    </div>
                                                </div>
                                                <div class="chat-message  custom-chat" style="margin-top:14px">
                                                    <form id="textMessageSubmits" method="post"
                                                        onsubmit="return false;">
                                                        <input type="hidden" name="_token"
                                                            value="<?php echo csrf_token(); ?>">

                                                        <span class="input-box">
                                                            <textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="smsTextMessage"></textarea>

                                                        </span>
                                                        <span class="error" id="smsTextMessageError"></span><br>
                                                        <button class="btn btn-success btn-sm" id="text-sms-send-btn"
                                                            onclick="sendTextMessagefile()">Send</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="tab-pane" id="sms-logs-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">SMS Logs</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="col-12 loader-calender" id="logList1"
                                                    style="display:flex;justify-content:center;margin-top:10%">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader"
                                                        id="loadertag121" style="display:none">
                                                </div>
                                            </div>
                                            <div class="col-12" id="sms_logs_id">
                                            </div>
                                        </div> --}}
                                    <div class="tab-pane" id="hub-logs-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">Hub Logs</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="col-12 loader-calender" id="logList1"
                                                    style="display:flex;justify-content:center;margin-top:10%">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" class=""
                                                        alt="loader" id="loadertag121" style="display:none">
                                                </div>
                                            </div>
                                            <div class="col-12" id="hub_logs_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="hub-nybest-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">NyBest Medical Request</p>

                                            <p class="mb-0 tx-13">
                                                <a data-toggle="modal" onclick="showNybestModal()"
                                                    class="pull-right btn btn-info btn-sm d-none d-md-block"
                                                    data-target="#hub-nybest-add" data-whatever="@mdo"><i
                                                        class="mdi mdi-plus"></i>
                                                    Add</a>
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="col-12 loader-calender" id="nybestList1"
                                                    style="display:flex;justify-content:center;margin-top:10%">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" class=""
                                                        alt="loader" id="loadertag121" style="display:none">
                                                </div>
                                            </div>
                                            <div class="col-12" id="hub_nybest_id">
                                            </div>
                                        </div>
                                    </div>



                                    <div class="tab-pane" id="uitlization-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">Uitlization</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="col-12 loader-calender" id="logList1"
                                                    style="display:flex;justify-content:center;margin-top:10%">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" class=""
                                                        alt="loader" id="loadertag121" style="display:none">
                                                </div>
                                            </div>

                                            <div class="col-12" id="uitlization_id">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="eligibility-section">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="card-title mb-0">Eligibility</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="col-12 loader-calender" id="logList1"
                                                    style="display:flex;justify-content:center;margin-top:10%">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" class=""
                                                        alt="loader" id="loadertag121" style="display:none">
                                                </div>
                                            </div>
                                            <div class="col-12" id="eligibility_id">
                                            </div>
                                        </div>
                                    </div>
                                     <div class="tab-pane" id="task-section">

                                        @include('hubRecord.modal.task_section')
                                     </div>
                                      <div class="tab-pane" id="clinical-section">
                                        @include('hubRecord.modal.hub_record_clinical_section')
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
@include('hubRecord/modal/mobile_number_modal')
@include('hubRecord/modal/phone_number_modal')
@include('hubRecord/modal/hub_language_modal')
@include('hubRecord/modal/hub_add_document_modal')
@include('hubRecord/modal/nybest_add_modal')
@include('hubRecord/modal/hub_add_notes_modal')
@include('hubRecord/modal/hub_record_add')
@include('hubRecord/modal/hub_records_status_modal')
@include('hubRecord/modal/create_task_modal')
@include('hubRecord/modal/task_view')
@include('hubRecord/modal/task_due_date')
@include('hubRecord/modal/task_title_modal')
@include('hubRecord/modal/task_description_modal')
@include('hubRecord/modal/task_assignee_modal')
@include('hubRecord/modal/task_change_status')
<script>
    var SAVE_BASIC_DETAILS = "{{ url('hub-record/save-basic-details') }}";
    var SAVE_ADDRESS_DETAILS = "{{ url('hub-record/save-address-details') }}";
    var _RECORD_ID = "{{ $record->id }}";
    var _GET_COUNTRY_CODE = "{{ url('get-county') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var GET_COUNTY = '{{ url('get-county') }}';
    var _HUB_UPDATE_MOBILE = '{{ url('update-hub-mobile') }}';
    var _HUB_UPDATE_PHONE = '{{ url('update-hub-phone') }}';
    var _HUB_UPDATE_LANGUAGE = '{{ url('update-hub-langauge') }}';
    var _HUB_NOTES = '{{ url('get-hub-notes') }}'
    var _SAVE_HUB_NOTES = '{{ url('save-hub-notes') }}'
    var _HUB_DOCUMENT_LIST = '{{ url('get-hub-document') }}'
    var _SAVE_DOCUMENT_LIST = '{{ url('save-hub-document-data') }}'
    var _DELETE_DOCUMENT = '{{ url('delete-hub-document-data') }}'
    var _SEND_SMS_TEXT = "{{ url('hub-record/text-message-notes') }}";
    var _MOBILE = "{{ $record->mobile }}";
    var _GET_SMS_TEXT = "{{ url('hub-record/get-sms-text') }}";
    var CREATED_USER_NAME = "{{ auth()->user()->first_name }}" + "{{ auth()->user()->last_name }}";
    var _HUB_RECORD_DELETE = "{{ url('hub-record/delete') }}";
    var GET_BASIC_DETAILS = "{{ url('hub-get-basic-details') }}";
    var _GET_HUB_LOGS = "{{ url('hub-record-view-logs') }}";
    var _GET_NYBEST_LIST = "{{ url('hub-nybest-list') }}";
    var _CHECK_HUB_DUPLICATE = "{{ url('check-hub-duplicate-data') }}";
    var GET_DEPENDENT_DATA = "{{ url('get-hub-dependent-data') }}";
    var _SAVE_HUB_DEPENDENT_DETAILS = "{{ url('hub-record/dependent-save/') }}";
    var GET_AGENCY_OTHER_DATA = "{{ url('get-agency-other-data') }}";
    var _AUTH_VIEW_SSN = "{{ auth()->user()->view_ssn_hub }}";
    var _UPDATE_AGENCY_WISE_DATA = "{{ url('update-agency-wise-hub-data') }}";
    var _SAVE_NYBEST_LIST = '{{ url('save-hub-document-data') }}'
    var _GET_HUB_UITLIZATION = "{{ url('hub-uitlization') }}";
    var _SAVE_DATA_NYBEST = '{{ url('save-hub-nybest-data') }}';
    var _GET_NYBEST_AGENCY = '{{ url('get-nybest-agency') }}';
    var _TYPE_WISE_SERVICE_LIST = "{{ url('/ajax-service') }}";
    var _GET_HUB_ELIGIBILITY = "{{ url('hub-eligibility') }}";
    var _HUB_RECORD_SEND_DEPENDENT = "{{ url('hub-record/send-dependent') }}";
    var _HUB_UPDATE_STATUS = "{{ url('update-hub-status') }}";
    var _HUB_RECORD_FLAG = '{{ url("hub-flag-change-status/") }}';
    var _HUB_RECORD_NOTES_PERMISSION = @json(auth()->user()->can('hub-flag-notes-change-status'));
    var _HUB_RECORD_NOTES_FLAG = '{{ url("hub-flag-change-notes-status/") }}';
    var _HUB_RECORD_DOC_FLAG = '{{ url("hub-flag-change-document-status/") }}';
    var _HUB_TASK_LIST = '{{ url("hub-record/task-list") }}';
    var _HUB_TASK_ADD = '{{ url("hub-record/task-add") }}';
    var TASK_AJAX = "{{ url('hub-task-list-ajax')}}";
    var ACTIVITY_LOG = "{{ url('hub-task/activity-log-list') }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var TASK_COMMENT_LIST = "{{ url('hub-task-comment-list') }}";
    var TASK_TIME_LOG_LIST = "{{ url('hub-record/task-time-log-list') }}";
    var TASK_STATUS_CHANGE = "{{url('hub-task-change-status')}}";
    var TASK_PRIORITY_CHANGE = "{{ url('hub-task-priority-update') }}";
    var COMMENT_SAVE = "{{ url('hub-task-comment-save') }}";
    var TASK_ASSIGN_USER = "{{ url('hub-task-assign-to-user') }}";
    var TASK_DUE_DATE = "{{ url('hub-task-due-date') }}";
    var TASK_TITLE_UPDATE = "{{ url('hub-task-title-update') }}";
    var TASK_DESCRIPTION_UPDATE = "{{ url('hub-task-discription-update')}}";
    var change_status_url = "{{url('hub-task-change-status')}}";
    var FLAG_TASK = "{{ url('hub-flag-change-task-status')}}";
    var CLOCK_IN_OUT = "{{ url('hub-record/task-clock-in-out') }}";
    var AUTH = "{{auth()->user()->id}}";
    var _TASK = "{{ url('hub-record/task-record') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css" />
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/tribute.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}" />
<script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript"
    src="{{ asset('assets/modulejs/hub_record/hub_record_view.js') }}?time={{ time() }}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/hub_record/task_page.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/hub_record/task_list.js')}}?time={{ env('timestamp')}}"></script>
<script>
    $(":input").inputmask();

    $(document).ready(function() {
        $('ul.left-section-ul li').click(function() {
            $('ul.left-section-ul li').removeClass('active');
            $(this).addClass('active');
        })

        $('ul.right-section-ul li').click(function() {
            $('ul.right-section-ul li').removeClass('active');
            $(this).addClass('active');

        })

        $('.fancybox').fancybox({
            toolbar: false,
            smallBtn: true,
            iframe: {
                preload: false
            }
        })
        $('.booking_date').datepicker({
            minDate: 0 // 0 means today, 1 means tomorrow, etc.
        });
    })
        $('#exampleModal-task').on('hidden.bs.modal', function() {
        $('.error').html("");
        $('#task_name_id').val("")
        $('#assign_to_id').val('').trigger("change");
        $('#start_date').val("");
        $('#due_date').val("");
        $('#priority').val("");
        $('#task_description').val("");
    });
    
</script>
<script>
$('#due_date_task_model_id_date').datetimepicker({
    "allowInputToggle": true,
    "showClose": true,
    "showClear": true,
    "showTodayButton": true,
    "format": "MM/DD/YYYY hh:mm:ss A",
});
</script>
<script src="{{ asset('assets/js/jquery.fancybox.min.js') }}"></script>
