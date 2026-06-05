<style>
    .call-detail-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.10);
        color: #fff;
        margin-bottom: 16px;
    }

    .call-detail-hero h4 {
        margin: 0;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
    }

    .call-detail-hero small {
        color: #fff;
        opacity: 0.9;
    }

    .call-detail-panel {
        border: 1px solid #cfd7e6;
        background: #fff;
        margin-bottom: 14px;
    }

    .call-detail-panel-header {
        border-bottom: 1px solid #1e88ff;
        padding: 12px 18px;
        font-weight: 600;
        color: #1f2b45;
    }

    .call-detail-panel-header.green {
        border-bottom-color: #22a447;
    }

    .call-detail-panel-body {
        padding: 16px 18px;
    }

    .call-detail-info-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        row-gap: 10px;
        column-gap: 14px;
        font-size: 13px;
    }

    .call-detail-info-label {
        color: #63708a;
        font-weight: 600;
    }

    .call-detail-table {
        margin-bottom: 0;
        font-size: 13px;
    }

    .call-detail-table thead th {
        background: #f7f9fc;
        color: #2d3854;
        white-space: nowrap;
        border-bottom: 1px solid #cfd7e6;
    }

    .call-detail-filter label {
        color: #63708a;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    @media (max-width: 768px) {
        .call-detail-info-grid {
            grid-template-columns: 1fr;
            row-gap: 4px;
        }
    }
</style>

<div class="call-detail-hero">
    <h4><i class="mdi mdi-phone-log"></i> Call Details</h4>
    <small>RingLogix CDR history for selected patient mobile number</small>
</div>

@if($errorMessage)
    <div class="alert alert-danger">{{ $errorMessage }}</div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="call-detail-panel">
            <div class="call-detail-panel-header">
                <i class="fa fa-user text-primary"></i> Patient Information
            </div>
            <div class="call-detail-panel-body">
                <div class="call-detail-info-grid">
                    <div class="call-detail-info-label">Patient ID:</div>
                    <div>{{ $patient ? '#'.$patient->id : '-' }}</div>
                    <div class="call-detail-info-label">Full Name:</div>
                    <div>{{ $patient ? trim($patient->first_name . ' ' . $patient->middle_name . ' ' . $patient->last_name) : '-' }}</div>
                    <div class="call-detail-info-label">Mobile:</div>
                    <div>{{ $patient && $patient->mobile ? $patient->mobile : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="call-detail-panel">
            <div class="call-detail-panel-header green">
                <i class="fa fa-filter text-success"></i> Filter Details
            </div>
            <div class="call-detail-panel-body">
                <form id="callDetailsFilterForm" class="call-detail-filter"
                    data-ajax-url="{{ route('patient.call-details.ajax', $patient->id) }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="text" name="start_date" class="form-control form-control-sm"
                                value="{{ \Carbon\Carbon::parse($filters['start_date'])->format('m-d-Y') }}"
                                placeholder="mm-dd-yyyy">
                        </div>
                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="text" name="end_date" class="form-control form-control-sm"
                                value="{{ \Carbon\Carbon::parse($filters['end_date'])->format('m-d-Y') }}"
                                placeholder="mm-dd-yyyy" data-is-end="1">
                        </div>
                        <div class="col-md-2">
                            <label>Limit</label>
                            <input type="number" name="limit" class="form-control form-control-sm"
                                value="{{ $filters['limit'] }}" min="1" max="500">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="call-detail-panel">
    <div class="call-detail-panel-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fa fa-list text-primary"></i> CDR List
            <span class="badge badge-info ml-2">{{ count($callDetails) }}</span>
        </span>
        <small id="cdrPaginationInfo" class="text-muted"></small>
    </div>
    <div class="call-detail-panel-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped call-detail-table" id="cdrStaticTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date &amp; Time</th>
                        <th>Type</th>
                        <th>Caller Name</th>
                        <th>Caller Number</th>
                        <th>Dialed Number</th>
                        <th>Extension</th>
                        <th>Duration</th>
                        <th>Talk Time</th>
                        <th>Action</th>
                        <th>Release Reason</th>
                        <th>Codec</th>
                    </tr>
                </thead>
                <tbody id="cdrTableBody">
                    @forelse($callDetails as $index => $call)
                        @php
                            $cdrR     = $call['CdrR'] ?? [];
                            $duration = (int)($call['duration'] ?? 0);
                            $talkTime = (int)($call['time_talking'] ?? 0);
                            $callerNum = isset($call['orig_from_uri']) ? preg_replace('/^sip:|@.*$/i', '', $call['orig_from_uri']) : '-';
                            $type = (int)($call['type'] ?? -1);
                            $typeBadge = match($type) {
                                0 => '<span class="badge badge-primary">Outbound</span>',
                                1 => '<span class="badge badge-success">Inbound</span>',
                                2 => '<span class="badge badge-warning">Missed</span>',
                                default => '<span class="badge badge-secondary">Unknown</span>',
                            };
                        @endphp
                        <tr class="cdr-row" data-row="{{ $index }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ isset($call['time_start']) ? \Carbon\Carbon::createFromTimestamp($call['time_start'])->format('Y-m-d H:i:s') : '-' }}</td>
                            <td>{!! $typeBadge !!}</td>
                            <td>{{ $call['orig_from_name'] ?? $cdrR['orig_from_name'] ?? '-' }}</td>
                            <td>{{ $callerNum }}</td>
                            <td>{{ $call['orig_req_user'] ?? $call['orig_to_user'] ?? '-' }}</td>
                            <td>{{ $call['orig_sub'] ?? $call['by_sub'] ?? $cdrR['orig_sub'] ?? '-' }}</td>
                            <td>{{ $duration > 0 ? sprintf('%d:%02d', floor($duration/60), $duration%60) : '0:00' }}</td>
                            <td>{{ $talkTime > 0 ? sprintf('%d:%02d', floor($talkTime/60), $talkTime%60) : '0:00' }}</td>
                            <td>{{ $cdrR['by_action'] ?? '-' }}</td>
                            <td>{{ $cdrR['release_text'] ?? '-' }}</td>
                            <td>{{ $cdrR['codec'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No call details found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top" id="cdrStaticPagination">
            <button id="cdrPrevBtn" class="btn btn-outline-secondary btn-sm" disabled>
                <i class="fa fa-chevron-left"></i> Previous
            </button>
            <span id="cdrPageInfo" class="text-muted small"></span>
            <button id="cdrNextBtn" class="btn btn-outline-secondary btn-sm" disabled>
                Next <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var pageSize    = 25;
    var currentPage = 1;
    var rows        = $('#cdrStaticTable .cdr-row');
    var total       = rows.length;
    var totalPages  = Math.max(1, Math.ceil(total / pageSize));

    function showPage(page) {
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        currentPage = page;

        rows.hide();
        var start = (currentPage - 1) * pageSize;
        rows.slice(start, start + pageSize).show();

        var from = total > 0 ? start + 1 : 0;
        var to   = Math.min(start + pageSize, total);
        $('#cdrPaginationInfo').text(total > 0 ? 'Showing ' + from + '–' + to + ' of ' + total : '');
        $('#cdrPageInfo').text('Page ' + currentPage + ' of ' + totalPages);
        $('#cdrPrevBtn').prop('disabled', currentPage <= 1);
        $('#cdrNextBtn').prop('disabled', currentPage >= totalPages);
    }

    if (total > pageSize) {
        $('#cdrStaticPagination').show();
        $('#cdrPrevBtn').on('click', function () { showPage(currentPage - 1); });
        $('#cdrNextBtn').on('click', function () { showPage(currentPage + 1); });
        showPage(1);
    } else {
        $('#cdrStaticPagination').hide();
    }
})();
</script>
