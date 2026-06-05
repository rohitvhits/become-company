@include('_partial.task_health_flags.modal')
<style>
.task_health_class{
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.3px;
    color:#6c757d;
    margin-bottom:3px;
    display:block;
}
</style>
@can('task-health-visit-list')
<div id="task-health-visits-tab">

    {{-- ── Filter Bar ─────────────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e9ecef;border-radius:7px;margin-bottom:10px;overflow:hidden;">

        {{-- Header row --}}
        <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid #f0f2f5;">
            <span style="font-size:13px;font-weight:600;color:#343a40;">
                <i class="mdi mdi-hospital-building mr-1" style="color:#007bff;"></i> Task Health Visits
            </span>
            <div class="d-flex align-items-center gap-1" style="gap:6px;">
                <button class="btn btn-sm" type="button"
                    id="th_filter_toggle_btn"
                    data-toggle="collapse" data-target="#thPatientFilterPanel" aria-expanded="false"
                    style="background:#f8f9fa;border:1px solid #dee2e6;font-size:12px;color:#495057;padding:4px 10px;border-radius:5px;">
                    <i class="mdi mdi-filter-outline"></i> Filter
                    <span id="th_active_filter_dot" style="display:none;width:7px;height:7px;background:#dc3545;border-radius:50%;display:inline-block;margin-left:4px;vertical-align:middle;"></span>
                </button>
            </div>
        </div>

        {{-- Collapsible filter panel --}}
        <div class="collapse" id="thPatientFilterPanel">
            <div class="px-3 py-3" style="background:#fafbfc;border-bottom:1px solid #f0f2f5;">
                <div class="row" style="row-gap:10px;">

                    <div class="col-md-2 col-sm-6">
                        <label class="task_health_class">From Date</label>
                        <input type="text" id="th_filter_from_date" class="form-control form-control-sm datepicker-single"
                               placeholder="MM/DD/YYYY" autocomplete="off"
                               style="font-size:12.5px;border-radius:5px;">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="task_health_class">To Date</label>
                        <input type="text" id="th_filter_to_date" class="form-control form-control-sm datepicker-single"
                               placeholder="MM/DD/YYYY" autocomplete="off"
                               style="font-size:12.5px;border-radius:5px;">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="task_health_class">Sort By</label>
                        <select id="th_filter_sort_by" class="form-control form-control-sm" style="font-size:12.5px;border-radius:5px;">
                            <option value="scheduledDateTime">Scheduled Date</option>
                            <option value="createdAt">Created Date</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="task_health_class">Status</label>
                        <select id="th_filter_status" class="form-control form-control-sm" style="font-size:12.5px;border-radius:5px;">
                            <option value="">All Statuses</option>
                            <option value="Needs Attention">Needs Attention</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="task_health_class">Review Status</label>
                        <select id="th_filter_review_status" class="form-control form-control-sm" style="font-size:12.5px;border-radius:5px;">
                            <option value="">All</option>
                            <option value="Pending RN changes">Pending RN changes</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 d-flex align-items-end">
                        <div class="d-flex w-100" style="gap:6px;">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="loadVisitData(1)"
                                    style="font-size:12px;padding:5px 0;border-radius:5px;white-space:nowrap;">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button class="btn btn-light btn-sm flex-fill" onclick="resetPatientVisitFilters()"
                                    style="font-size:12px;padding:5px 0;border-radius:5px;border:1px solid #dee2e6;white-space:nowrap;">
                                <i class="mdi mdi-reload"></i> Reset
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Active filter summary chips --}}
            <div id="th_filter_chips" class="px-3 py-2" style="display:none;border-bottom:1px solid #f0f2f5;background:#fff;">
                <small class="text-muted mr-1">Active:</small>
                <span id="th_chip_from"   class="badge badge-secondary mr-1" style="display:none;font-size:11px;"></span>
                <span id="th_chip_to"     class="badge badge-secondary mr-1" style="display:none;font-size:11px;"></span>
                <span id="th_chip_sort"   class="badge badge-info mr-1"      style="display:none;font-size:11px;"></span>
                <span id="th_chip_status" class="badge badge-primary mr-1"   style="display:none;font-size:11px;"></span>
                <span id="th_chip_review" class="badge badge-warning mr-1"   style="display:none;font-size:11px;color:#333;"></span>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="visit_list_loader_patient" class="text-center py-3" style="display:none;">
        <img src="{{ asset('/ajax-loader.gif') }}" alt="Loading...">
        <p class="mt-1 text-muted small">Loading visits...</p>
    </div>

    {{-- Results --}}
    <div id="visit_list_patient_container">
        <div class="text-center text-muted py-5">
            <i class="mdi mdi-hospital-building" style="font-size: 48px;"></i>
            <p class="mt-2">Click "Task Health Visits" tab to load visits.</p>
        </div>
    </div>

</div>

<script>
$(function () {
    // Init datetimepicker (date-only) for filter inputs
    var now      = new Date();
    var fromDate = new Date(now.getFullYear(), 0, 1);
    var toDate = new Date(now.getFullYear() + 2, 11, 31);
    var pad = function(n) { return n < 10 ? '0' + n : n; };
    var fmtMDY = function(d) {
        return pad(d.getMonth() + 1) + '/' + pad(d.getDate()) + '/' + d.getFullYear();
    };
    $('#th_filter_from_date').val(fmtMDY(fromDate));
    $('#th_filter_to_date').val(fmtMDY(toDate));
    $('.datepicker-single').datepicker({
        dateFormat: 'mm/dd/yy',
    });
});
</script>
@endcan
