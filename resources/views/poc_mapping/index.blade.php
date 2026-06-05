@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .select2-container { width: 100% !important; }
    .task-row td { vertical-align: middle; }
    .visit-task-input { max-width: 300px; }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">POC Mapping</h5>
        </div>
        <hr />

        <div class="row mb-3 align-items-end">
            <div class="col-md-4">
                <label class="font-weight-semibold">Agency <span class="text-danger">*</span></label>
                <select id="page_agency_id" class="form-control">
                    <option value="">Select Agency</option>
                    @foreach($agency_list as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 text-right">
                <button type="button" class="btn btn-warning btn-sm px-3" id="sync-tasks-btn" onclick="syncPocTasks()" style="display:none;">
                    <span class="spinner-border spinner-border-sm d-none mr-1" id="syncTasksLoader" role="status"></span>
                    <i class="fa fa-refresh mr-1" id="syncTasksIcon"></i>Sync Tasks
                </button>
            </div>
        </div>

        <div id="tasks-loading" style="display:none;" class="text-center py-4">
            <span class="spinner-border text-primary" role="status"></span>
            <span class="ml-2">Loading tasks...</span>
        </div>

        <div id="tasks-table-wrap">
            <div class="row">
                <div class="col-12">
                    <div class="location-wise-data-loader table-responsive">
                        <div class="col-md-12 pl-0">
                            <table class="table table-bordered table-head-fix recordtabletdwidth" id="tasks-table">
                                <thead>
                                    <tr>
                                        <th style="white-space:nowrap; width:60px;">Sr. No.</th>
                                        <th style="white-space:nowrap;">Task Name</th>
                                        <th style="white-space:nowrap; width:260px;">Visit Task ID</th>
                                    </tr>
                                </thead>
                                <tbody id="tasks-tbody">
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            <i class="fa fa-info-circle mr-1"></i> No data found. Please select an agency to view tasks.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3" id="save-all-wrap" style="display:none;">
                <div class="col-12 text-right">
                    <button type="button" class="btn btn-success px-4" id="save-all-btn" onclick="saveAllMappings()">
                        <span class="spinner-border spinner-border-sm d-none" id="saveAllLoader" role="status" aria-hidden="true"></span>
                        <span id="save-all-text">Save</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

@include('include/footer')

<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script>
    var _TASKS_WITH_MAPPINGS = "{{ url('hha/poc-mapping/tasks-with-mappings') }}";
    var _SAVE_ALL_URL        = "{{ url('hha/poc-mapping/save-all') }}";
    var _SYNC_TASKS_URL      = "{{ url('hha/poc-mapping/sync-tasks') }}";
    var _CSRF_TOKEN          = "{{ csrf_token() }}";

    $('#page_agency_id').select2({ placeholder: 'Select Agency', allowClear: true });

    $('#page_agency_id').on('change', function () {
        var agencyId = $(this).val();
        if (!agencyId) {
            $('#save-all-wrap').hide();
            $('#sync-tasks-btn').hide();
            $('#tasks-tbody').html('<tr><td colspan="3" class="text-center py-3 text-muted"><i class="fa fa-info-circle mr-1"></i> No data found. Please select an agency to view tasks.</td></tr>');
            return;
        }
        $('#sync-tasks-btn').show();
        loadTasksWithMappings(agencyId);
    });

    function loadTasksWithMappings(agencyId) {
        $('#save-all-wrap').hide();
        $('#tasks-loading').show();
        $('#tasks-tbody').html('');

        $.ajax({
            url: _TASKS_WITH_MAPPINGS,
            type: 'GET',
            data: { agency_id: agencyId },
            success: function (tasks) {
                $('#tasks-loading').hide();
                if (!tasks || tasks.length === 0) {
                    $('#tasks-tbody').html('<tr><td colspan="3" class="text-center py-3 text-muted">No tasks found for this agency. Click <strong>Sync Tasks</strong> to fetch from HHA.</td></tr>');
                    $('#save-all-wrap').hide();
                    return;
                }

                var html = '';
                $.each(tasks, function (i, task) {
                    var visitVal = task.visit_task_id ? $('<div>').text(task.visit_task_id).html() : '';
                    html += '<tr class="task-row">';
                    html += '<td class="text-center">' + (i + 1) + '</td>';
                    html += '<td>' + $('<div>').text(task.task_name).html() + '</td>';
                    html += '<td><input type="text" class="form-control form-control-sm visit-task-input" data-task-id="' + task.task_id + '" value="' + visitVal + '" placeholder="Enter Visit Task ID"></td>';
                    html += '</tr>';
                });

                $('#tasks-tbody').html(html);
                $('#save-all-wrap').show();
            },
            error: function (jqXHR) {
                $('#tasks-loading').hide();
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }

    function syncPocTasks() {
        var agencyId = $('#page_agency_id').val();
        if (!agencyId) {
            toastr.warning('Please select an agency first.');
            return;
        }

        $('#syncTasksLoader').removeClass('d-none');
        $('#syncTasksIcon').addClass('d-none');
        $('#sync-tasks-btn').prop('disabled', true);

        $.ajax({
            url: _SYNC_TASKS_URL,
            type: 'GET',
            data: { agency_id: agencyId },
            success: function (res) {
                toastr.success(res.message);
                loadTasksWithMappings(agencyId);
            },
            error: function (jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function () {
                $('#syncTasksLoader').addClass('d-none');
                $('#syncTasksIcon').removeClass('d-none');
                $('#sync-tasks-btn').prop('disabled', false);
            }
        });
    }

    function saveAllMappings() {
        var agencyId = $('#page_agency_id').val();
        if (!agencyId) {
            toastr.warning('Please select an agency first.');
            return;
        }

        var mappings = [];
        $('#tasks-tbody .task-row').each(function () {
            var $row      = $(this);
            var taskId    = $row.find('.visit-task-input').data('task-id');
            var visitTask = $row.find('.visit-task-input').val().trim();
            mappings.push({
                hha_task_id:   taskId,
                visit_task_id: visitTask,
                checked:       visitTask ? 1 : 0
            });
        });

        $('#saveAllLoader').removeClass('d-none');
        $('#save-all-text').text('Saving...');
        $('#save-all-btn').prop('disabled', true);

        $.ajax({
            url: _SAVE_ALL_URL,
            type: 'POST',
            data: {
                _token:    _CSRF_TOKEN,
                agency_id: agencyId,
                mappings:  mappings
            },
            success: function (response) {
                toastr.success(response.message || 'Mappings saved successfully.');
                loadTasksWithMappings(agencyId);
            },
            error: function (jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function () {
                $('#saveAllLoader').addClass('d-none');
                $('#save-all-text').text('Save');
                $('#save-all-btn').prop('disabled', false);
            }
        });
    }
</script>
