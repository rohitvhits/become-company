<style>
    .cd-filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px 12px; margin-bottom: 18px; }
    .cd-filter-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 5px; display: block; }
    .cd-filter-card .form-control-sm { height: 32px; font-size: 12.5px; background: #fff; border-color: #cbd5e1; border-radius: 6px; }
    .cd-filter-card .form-control-sm:focus { border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.15); }
    .cd-filter-card .btn-search { height: 32px; font-size: 12.5px; font-weight: 600; border-radius: 6px; background: #2563eb; border-color: #2563eb; padding: 0 16px; white-space: nowrap; }
    .cd-filter-card .btn-search:hover { background: #1d4ed8; border-color: #1d4ed8; }
    .cd-section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .cd-section-title { font-size: 13px; font-weight: 700; color: #1e293b; display: flex; align-items: center; }
    .cd-count-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #fff; padding: 0 7px; margin-left: 7px; line-height: 1; }
    .cd-showing-info { font-size: 11.5px; color: #94a3b8; }
    .cd-table { font-size: 12.5px; }
    .cd-table thead th { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; background: #f1f5f9 !important; white-space: nowrap; padding: 8px 10px; border-bottom: 2px solid #e2e8f0 !important; }
    .cd-table tbody td { padding: 7px 10px; vertical-align: middle; border-color: #f1f5f9; }
    .cd-table tbody tr:hover { background: #f8fafc; }
    .cd-table code { font-size: 11.5px; background: #f1f5f9; color: #475569; border-radius: 4px; padding: 2px 5px; border: none; }
    .cd-badge { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 20px; letter-spacing: 0.3px; }
    .cd-badge-inbound  { background: #dcfce7; color: #16a34a; }
    .cd-badge-outbound { background: #dbeafe; color: #1d4ed8; }
    .cd-badge-missed   { background: #fee2e2; color: #dc2626; }
    .cd-badge-unknown  { background: #f1f5f9; color: #64748b; }
    .cd-pag { display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid #f1f5f9; margin-top: 6px; }
    .cd-pag-page-info { font-size: 11.5px; color: #94a3b8; }
    .cd-pag .btn { font-size: 12px; font-weight: 600; border-radius: 6px; padding: 4px 12px; }
    .cd-empty { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .cd-empty i { font-size: 2.2rem; margin-bottom: 10px; display: block; opacity: .5; }
    .cd-empty p { font-size: 13px; margin: 0; }
</style>

<div class="right-section-main">
    <ul class="nav nav-tabs tabs-right sideways right-section-ul" id="cdInnerTabs" role="tablist">
        <li class="active">
            <a href="#cd-cdr-pane" aria-controls="cd-cdr-pane" role="tab" data-toggle="tab">
                <i class="fa fa-phone mr-1"></i> Call Details
            </a>
        </li>
    </ul>

    <div class="tab-content right-section-tab-content">

        {{-- CDR Pane --}}
        <div class="tab-pane active" id="cd-cdr-pane" role="tabpanel">

            {{-- Filter --}}
            <div class="cd-filter-card">
                <form id="cdCdrFilterForm">
                    <div class="d-flex align-items-end" style="gap:8px;">
                        <div style="flex:0 0 260px;">
                            <label class="cd-filter-label"><i class="fa fa-calendar-alt mr-1"></i>Date Range</label>
                            <input type="text" id="cdCdrDateRangePicker" class="form-control form-control-sm" placeholder="Select date range" readonly>
                            <input type="hidden" id="cdCdrStartDate" name="start_date">
                            <input type="hidden" id="cdCdrEndDate" name="end_date">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-search">
                                <i class="fa fa-search mr-1"></i>Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Section header --}}
            <div class="cd-section-header">
                <span class="cd-section-title">
                    <i class="fa fa-list text-primary mr-2"></i>CDR List
                    <span class="cd-count-badge" id="cdCdrCountBadge" style="background:#94a3b8;">0</span>
                </span>
                <small id="cdCdrPagInfo" class="cd-showing-info"></small>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 cd-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Date &amp; Time</th>
                            <th>Type</th>
                            <th>Caller Name</th>
                            <th>Caller #</th>
                            <th>Dialed #</th>
                            <th>Ext.</th>
                            <th>Duration</th>
                            <th>Talk Time</th>
                            <th>Action</th>
                            <th>Release</th>
                            <th>Codec</th>
                            <th>Recording</th>
                        </tr>
                    </thead>
                    <tbody id="cdCdrTableBody">
                        <tr>
                            <td colspan="13" class="text-center py-4">
                                <i class="fa fa-spinner fa-spin fa-2x" style="color:#2563eb;opacity:.7;"></i>
                                <p class="mt-2 mb-0" style="font-size:13px;color:#94a3b8;">Loading...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="cd-pag">
                <button id="cdCdrPrevBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled>
                    <i class="fa fa-chevron-left mr-1"></i>Prev
                </button>
                <span id="cdCdrPageInfo" class="cd-pag-page-info"></span>
                <button id="cdCdrNextBtn" class="btn btn-outline-secondary btn-sm" type="button">
                    Next <i class="fa fa-chevron-right ml-1"></i>
                </button>
            </div>

        </div>{{-- /cd-cdr-pane --}}

    </div>{{-- /tab-content --}}
</div>{{-- /right-section-main --}}
