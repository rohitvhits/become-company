@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<style>
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 26px;
        margin-right: 10px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .toggle-switch input:checked+.toggle-slider {
        background-color: #007bff;
    }

    .toggle-switch input:focus+.toggle-slider {
        box-shadow: 0 0 1px #007bff;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }

    .toggle-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        background-color: #f8f9fa;
    }

    .toggle-group:last-child {
        margin-bottom: 0;
    }

    .toggle-label {
        font-weight: 500;
        margin: 0;
        color: #495057;
    }

    .toggle-description {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-1 font-weight-bold">Site Setting</h5>

        </div>

        <div class="card">
            <form class="forms-sample" id="site_setting_form">
                <input type="hidden" name="id" value="{{ $query->id ?? '' }}" id="site_setting_id">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="exampleInputName1" class="col-sm-6 col-form-label">Cancellation Services
                                    Mail</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" value="{{ $query->cancellation_email ?? '' }}" name="cancellation_email"
                                        placeholder="Please enter Cancellation Services Mail" id="cancellation_email" style="height: 50px">{{ $query->cancellation_email ?? '' }}</textarea>
                                    <span class="text-muted">Notes : Comma Separated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="exampleInputName2" class="col-sm-3 col-form-label">Status-wise
                                    Documents</label>
                                <div class="col-sm-6">
                                    <select class="js-example-basic-multiple w-100" multiple="multiple"
                                        name="document_dashboard_status[]" id="document_dashboard_status">
                                        @if (isset($statusData) && !empty($statusData))
                                            @php $statusDashboardArray = explode(',',$query->document_dashboard_status); @endphp
                                            @foreach ($statusData as $key => $status)
                                                <option value="{{ $key }}"
                                                    {{ in_array($key, $statusDashboardArray) ? 'selected' : '' }}>
                                                    {{ $status }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="text-muted">Notes : Based on the selected status, documents will display
                                        on the Document Dashboard.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="exampleInputName1" class="col-sm-6 col-form-label">Hub NyBest Medical
                                    Request Mail
                                </label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" value="{{ $query->cancellation_email ?? '' }}" name="hub_nybest_email"
                                        placeholder="Please enter Hub NyBest Medical Request Mail" id="hub_nybest_email" style="height: 50px">{{ $query->hub_nybest_email ?? '' }}</textarea>
                                    <span class="text-muted">Notes : Comma Separated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">MDO File Upload Notification</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="mdo_upload_notify_email" id="mdo_upload_notify_email" placeholder="Enter email address(es)" style="height: 50px">{{ $query->mdo_upload_notify_email ?? '' }}</textarea>
                                    <span class="text-muted">Notes : Comma Separated</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Telehealth File Upload Notification</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="telehealth_upload_notify_email" id="telehealth_upload_notify_email" placeholder="Enter email address(es)" style="height: 50px">{{ $query->telehealth_upload_notify_email ?? '' }}</textarea>
                                    <span class="text-muted">Notes : Comma Separated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Agency Notification Extra Users</label>
                                <div class="col-sm-6">
                                    @php $selectedExtraUsers = !empty($query->agency_notification_extra_users) ? explode(',', $query->agency_notification_extra_users) : []; @endphp
                                    <select class="js-example-basic-multiple w-100" multiple="multiple" name="agency_notification_extra_users[]" id="agency_notification_extra_users">
                                        @foreach($nybest_users as $nybest_user)
                                            <option value="{{ $nybest_user->id }}" {{ in_array($nybest_user->id, $selectedExtraUsers) ? 'selected' : '' }}>{{ $nybest_user->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-muted">Notes : Select NyBest users to always include in agency notifications</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="mdi mdi-toggle-switch mr-1"></i>Health Check Endpoint</label>
                                <div class="mt-2">
                                    <div class="toggle-group">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="health_check_enabled" name="health_check_enabled"
                                                {{ (isset($query->health_check_enabled) && $query->health_check_enabled == 1) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <div>
                                            <div class="toggle-label">
                                                <i class="mdi mdi-heart-pulse mr-1"></i>Enable Health Check
                                            </div>
                                            <div class="toggle-description">
                                                When enabled, /health-check endpoint returns 200 OK. When disabled, returns 500 error.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="mdi mdi-bullhorn mr-1"></i>Announcement Popup</label>
                                <div class="mt-2">
                                    <div class="toggle-group">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="announcement_popup_enabled" name="announcement_popup_enabled"
                                                {{ (isset($query->announcement_popup_enabled) && $query->announcement_popup_enabled == 1) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <div>
                                            <div class="toggle-label">
                                                <i class="mdi mdi-bell-ring mr-1"></i>Enable Announcement Popup
                                            </div>
                                            <div class="toggle-description">
                                                When enabled, announcement popups will be shown to users on login. When disabled, popups will not appear.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Supervision Due Date (Months)</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="supervision_due_date_months" id="supervision_due_date_months">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ (isset($query->supervision_due_date_months) && $query->supervision_due_date_months == $i) ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'Month' : 'Months' }}
                                            </option>
                                        @endfor
                                    </select>
                                    <span class="text-muted">Sets the due date window for supervision compliance documents.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="mdi mdi-link-variant mr-1"></i>Task Health HHA Link Cron</label>
                                <div class="mt-2">
                                    <div class="toggle-group">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="task_health_cron_enabled" name="task_health_cron_enabled"
                                                {{ (isset($query->task_health_cron_enabled) && $query->task_health_cron_enabled == 1) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <div>
                                            <div class="toggle-label">
                                                <i class="mdi mdi-sync mr-1"></i>Enable Task Health Cron
                                            </div>
                                            <div class="toggle-description">
                                                When enabled, the task health HHA link cron will run and sync records. When disabled, the cron will exit without processing.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Caregiver Notification</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="caregiver_email_notification" id="caregiver_email_notification" placeholder="Enter email address(es)" style="height: 50px"></textarea>
                                    <span class="text-muted">Notes : Comma Separated<br></span>
                                    <span class="text-muted">Functionality : (Create Appointment)<br></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Patient Notification</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="patient_email_notification" id="patient_email_notification" placeholder="Enter email address(es)" style="height: 50px"></textarea>
                                    <span class="text-muted">Notes : Comma Separated<br></span>
                                    <span class="text-muted">Functionality : (Create Appointment)<br></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer">
                <button type="button" class="btn btn-primary mr-2" onclick="submitData()">Submit</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
@include('include/footer')
<script>
    function submitData() {
        var formData = new FormData($('#site_setting_form')[0]);
        formData.append('_token', '{{ csrf_token() }}')

        // Handle checkbox values properly
        if ($('#health_check_enabled').is(':checked')) {
            formData.set('health_check_enabled', '1');
        } else {
            formData.set('health_check_enabled', '0');
        }

        if ($('#announcement_popup_enabled').is(':checked')) {
            formData.set('announcement_popup_enabled', '1');
        } else {
            formData.set('announcement_popup_enabled', '0');
        }

        if ($('#task_health_cron_enabled').is(':checked')) {
            formData.set('task_health_cron_enabled', '1');
        } else {
            formData.set('task_health_cron_enabled', '0');
        }

        $.ajax({
            async: false,
            global: false,
            type: "post",
            url: "{{ url('/site-setting/save') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                toastr.success(res.error_msg)
                $('#site_setting_id').val(res.data.id);
            },
            error: function(jqr) {
                toastr.success(jqr.responseJSON.error_msg)
            }
        })
    }
</script>
