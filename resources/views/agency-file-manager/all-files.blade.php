@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css">

<style>
    /* Fix Select2 dropdown rendering inside Bootstrap modal */
    #linkedPatientsModal .modal-content {
        overflow: visible !important;
    }
    #linkedPatientsModal .modal-body {
        overflow: visible !important;
    }
    #linkedPatientsModal .select2-container {
        width: 100% !important;
    }
    #linkedPatientsModal .select2-dropdown {
        z-index: 1060 !important;
    }

    .afm-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .afm-table-wrap {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        overflow: hidden;
    }
    .afm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin: 0;
    }
    .afm-table th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 9px 12px;
        text-align: left;
        font-weight: 600;
        white-space: nowrap;
        user-select: none;
    }
    .afm-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .afm-table tr:last-child td { border-bottom: none; }
    .afm-table tr:hover td { background: #f8f9fa; }
    .afm-icon { font-size: 15px; }
    .afm-icon.pdf   { color: #dc3545; }
    .afm-icon.image { color: #28a745; }
    .afm-icon.doc   { color: #007bff; }
    .afm-icon.excel { color: #28a745; }
    .afm-icon.zip   { color: #6c757d; }
    .afm-badge-agency {
        display: inline-block;
        background: #e8f0fe;
        color: #1a56db;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 3px;
        font-weight: 500;
        white-space: nowrap;
    }
    .afm-path { color: #888; font-size: 12px; }
    .badge-mdo { background: #6f42c1; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 4px; }
    .badge-th  { background: #17a2b8; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 4px; }
    .afm-empty { text-align: center; padding: 60px; color: #aaa; }
    .afm-empty i { font-size: 40px; display: block; margin-bottom: 10px; }
    .afm-loading { text-align: center; padding: 60px; color: #aaa; }
    .afm-archived-row td { background: #fff8f0; }
    .afm-archived-row:hover td { background: #fff3e0; }
    .archived-badge { display:inline-block; background:#ffc107; color:#333; font-size:10px; padding:1px 6px; border-radius:3px; margin-left:4px; }
    .afm-filter-bar #fileSearch { min-width: 180px; }
    #bulkDownloadBar {
        display: none;
        align-items: center;
        gap: 10px;
        background: #e8f4fd;
        border: 1px solid #b8d9f5;
        border-radius: 5px;
        padding: 7px 14px;
        margin-bottom: 10px;
        font-size: 13px;
    }
    #bulkDownloadBar.visible { display: flex; }
    .afm-cb { width: 16px; height: 16px; cursor: pointer; }
    .afm-table th.cb-col, .afm-table td.cb-col { width: 36px; text-align: center; padding: 8px 6px; }
    .pegination-margin { margin-top: 4px; }
    .afm-filter-bar {
        background: #f8f9fa;
        border: 1px solid #e2e6ea;
        border-radius: 6px;
        padding: 12px 14px;
        margin-bottom: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-end;
    }
    .afm-filter-bar .filter-group { display: flex; flex-direction: column; gap: 3px; }
    .afm-filter-bar label { font-size: 11px; font-weight: 600; color: #555; margin: 0; }
    .afm-filter-bar .form-control { font-size: 12px; height: 30px; padding: 2px 8px; min-width: 130px; }
    .afm-filter-bar select.form-control { min-width: 110px; }
    .afm-filter-bar .btn-sm { height: 30px; font-size: 12px; }
    .afm-active-filters { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:8px; }
    .afm-filter-tag {
        display: inline-flex; align-items: center; gap: 4px;
        background: #e8f0fe; color: #1a56db; border-radius: 12px;
        font-size: 11px; padding: 2px 8px;
    }
    .afm-filter-tag button { background:none; border:none; color:#1a56db; padding:0; cursor:pointer; font-size:12px; line-height:1; }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="afm-toolbar">
            <div class="d-flex align-items-center" style="gap:10px;">
                <a href="{{ url('/file-manager') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back to File Manager
                </a>
                <h5 class="mb-0 font-weight-bold">
                    <i class="fa fa-files-o text-primary"></i> File Manager — All Files
                </h5>
            </div>
            <div class="d-flex align-items-center" style="gap:10px;">
                <button class="btn btn-sm btn-success" id="btnExportCsv" onclick="exportCsv()">
                    <i class="fa fa-download"></i> Export CSV
                </button>
                <button class="btn btn-sm btn-warning" id="btnArchived" onclick="toggleArchived()">
                    <i class="fa fa-archive"></i> <span id="archivedBtnText">Archived</span>
                </button>
                <span class="text-muted" id="fileCount" style="font-size:13px;">Loading...</span>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="afm-filter-bar" id="filterBar">
            <div class="filter-group">
                <label>File Name</label>
                <input type="text" class="form-control" id="fileSearch" placeholder="Search file name...">
            </div>
            @if($isSuperAdmin)
            <div class="filter-group">
                <label>Agency</label>
                <select class="form-control" id="fAgency">
                    <option value="">All Agencies</option>
                    @foreach($agencies as $ag)
                        <option value="{{ $ag->id }}">{{ $ag->agency_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="filter-group">
                <label>Type</label>
                <select class="form-control" id="fType">
                    <option value="">All Types</option>
                    <option value="pdf">PDF</option>
                    <option value="jpg">JPG</option>
                    <option value="jpeg">JPEG</option>
                    <option value="png">PNG</option>
                    <option value="docx">DOCX</option>
                    <option value="xlsx">XLSX</option>
                    <option value="zip">ZIP</option>
                    <option value="rar">RAR</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Uploaded By</label>
                <input type="text" class="form-control" id="fUploadedBy" placeholder="Name...">
            </div>
            <div class="filter-group">
                <label>Tag</label>
                <select class="form-control" id="fTag">
                    <option value="">All Tags</option>
                    <option value="mdo">MDO</option>
                    <option value="telehealth">Telehealth</option>
                    <option value="both">MDO + Telehealth</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Linked Chart</label>
                <select class="form-control" id="fLinkedChart">
                    <option value="">All</option>
                    <option value="linked">Linked</option>
                    <option value="not_linked">Not Linked</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select class="form-control" id="fPatientStatus">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                                                 <option value="cancelled" >Cancelled</option>

                                                 <option value="booked">Booked</option>
                                                 <option value="completed">Completed</option>

                                                 <option value="noshow" >No Show</option>

                                                 <option value="arrived" >Arrived</option>
                                                 <option value="processing">Processing</option>
                                                 <option value="Not interested" >Not Interested
                                                 </option>
                                                 <option value="hospitalized/rehab" >
                                                     Hospitalized/Rehab</option>
                                                 <option value="unableToContact" >Unable To Contact
                                                 </option>
                                                 <option value="refused" >Refused</option>
                                                 <option value="checkin" >Mark as CheckIn</option>

                                                 <option value="Pending Termination" >Pending Termination</option>
                                                 <option value="Onhold" >On Hold</option>
                                                 <option value="On Leave" >On Leave</option>
                                                 <option value="Terminated">Terminated</option>
                                                 <option value="inactive">Inactive</option>
                    @foreach($statusAll as $key => $status)
                        <option value="{{ $key }}">{{ $status }}</option>
                    @endforeach 
                </select>
            </div>
            <div class="filter-group">
                <label>Folder</label>
                <input type="text" class="form-control" id="fFolderPath" placeholder="Search folder name...">
            </div>
            <div class="filter-group">
                <label>Upload Date</label>
                <input type="text" class="form-control datepickernn" id="fDateRange" placeholder="MM/DD/YYYY - MM/DD/YYYY" autocomplete="off" style="min-width:210px;">
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:5px;">
                    <button class="btn btn-sm btn-primary" onclick="applyFilters()"><i class="fa fa-filter"></i> Apply</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()"><i class="fa fa-times"></i> Clear</button>
                </div>
            </div>
        </div>
        <div class="afm-active-filters" id="activeFilterTags"></div>

        <div id="bulkDownloadBar">
            <i class="fa fa-check-square-o text-primary"></i>
            <span id="selectedCount">0 file(s) selected</span>
            <button class="btn btn-sm btn-success" id="btnBulkDownload" onclick="bulkDownload()">
                <i class="fa fa-download"></i> Download Selected
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                <i class="fa fa-times"></i> Deselect All
            </button>
        </div>

        <div class="afm-table-wrap">
            <div id="resp" style="overflow-y: auto;">
                <div class="afm-loading"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading files...</div>
            </div>
        </div>

    </div>
</div>
<!-- ===== File Preview Modal ===== -->
<div class="modal fade" id="afmPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="afmPreviewFileName">Preview</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center" id="afmPreviewContainer" style="min-height:300px;padding:10px;">
                <div class="afm-loading"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>
            </div>
        </div>
    </div>
</div>
<!-- ===== Add to Patient Chart Modal ===== -->
<div class="modal fade" id="linkedPatientsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-paperclip mr-1"></i> Add Document — <span id="lpmFileName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="lpmFileId">
                <input type="hidden" id="lpmAgencyId">

                <div class="form-group" style="position:relative;">
                    <label class="col-form-label">Chart <span class="text-danger">*</span></label>
                    <input type="text" id="lpmPatientSearch" class="form-control" placeholder="Search by ID, Name, Mobile..." autocomplete="off">
                    <input type="hidden" id="lpmSelectedPatientId">
                    <div id="lpmSearchResults" style="position:absolute;z-index:9999;background:#fff;border:1px solid #dee2e6;border-top:none;width:100%;max-height:180px;overflow-y:auto;display:none;border-radius:0 0 4px 4px;"></div>
                    <span class="text-danger" id="lpmPatientError" style="font-size:12px;display:none;">Please select a Chart.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Document Name <span class="text-danger">*</span></label>
                    <input type="text" id="lpmDocumentName" class="form-control" placeholder="Enter document name">
                    <span class="text-danger" id="lpmDocumentNameError" style="font-size:12px;display:none;">Document name is required.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Request Services <span class="text-danger">*</span></label>
                    <select class="form-control w-100" id="lpmRequestService" onchange="lpmLoadServices()">
                        <option value="">Select Request Service</option>
                    </select>
                    <span class="text-danger" id="lpmRequestServiceError" style="font-size:12px;display:none;">Please select a request service.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Services <span class="text-danger">*</span></label>
                    <select class="js-example-basic-multiple w-100" multiple="multiple" id="lpmServices">
                        <option value="">Select Service</option>
                    </select>
                    <span class="text-danger" id="lpmServicesError" style="font-size:12px;display:none;">Please select at least one service.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Document Completed Date <span class="text-danger">*</span></label>
                    <input type="text" id="lpmDocumentDate" class="form-control document_completed_date_lpm" placeholder="MM/DD/YYYY" autocomplete="off" readonly>
                    <span class="text-danger" id="lpmDocumentDateError" style="font-size:12px;display:none;">Please select a document completed date.</span>
                </div>

                <div class="form-check form-check-primary mb-2">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="lpmInfoOnly" value="1">
                        <i class="input-helper"></i> Upload for Info Only
                    </label>
                    <div class="text-muted" style="font-size:12px;">When checked, the document is for information only. When unchecked, signatures or stamps are allowed.</div>
                </div>

                @if(auth()->user()->agency_fk == '')
                <div class="form-check form-check-primary mb-0">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="lpmInternalUse" value="1">
                        <i class="input-helper"></i> Internal Use Only
                    </label>
                    <div class="text-muted" style="font-size:12px;">If this checkbox is selected, the agency will not receive any emails.</div>
                </div>

                <div class="form-group mt-2 mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lpmDocumentReview" value="1">
                            <i class="input-helper"></i> Choose for Document Approval
                        </label>
                    </div>
                </div>

                <div id="lpmDocumentApprovalWrap" style="display:none;">
                    <div class="form-group">
                        <label class="col-form-label">User:<span class="text-danger">*</span></label>
                        <input type="text" id="lpmDocumentApprovalUser" class="form-control" placeholder="Search user...">
                        <span class="text-danger" id="lpmDocumentApprovalUserError" style="font-size:12px;display:none;">Please select a user.</span>
                    </div>
                </div>

                <div class="form-group mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lpmMedicationList" value="1">
                            <i class="input-helper"></i> Medication List
                        </label>
                    </div>
                </div>

                <div class="form-group mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lpmInsuranceElg" value="1">
                            <i class="input-helper"></i> Insurance Elg
                        </label>
                    </div>
                </div>
                <span class="text-danger" id="lpmMedInsuranceError" style="font-size:12px;display:none;">Medication List and Insurance Elg cannot both be selected.</span>

                <div class="form-group row align-items-center mb-0 mt-1">
                    <div class="col-4">
                        <div class="form-check form-check-primary mb-0">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="lpmMdoTag" value="1">
                                <i class="input-helper"></i> MDO Tag
                            </label>
                        </div>
                    </div>
                    <div class="col-8">
                        <select class="form-control w-100" id="lpmMdoSource" style="display:none;">
                            <option value="">Select MDO Source</option>
                            @foreach($masterData as $master)
                                @if($master->master_type_fk == '35')
                                    <option value="{{ $master->id }}">{{ $master->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <span class="text-danger" id="lpmMdoTagError" style="font-size:12px;display:none;">Please select an MDO Source.</span>
                @endif
            </div>
            <div class="modal-footer">
                <img src="{{ asset('ajax-loader.gif') }}" id="lpmLoader" style="display:none;height:28px;">
                <button type="button" class="btn btn-success" id="lpmSaveBtn" onclick="lpmSubmit()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}">
<script>

    var csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var searchTimer = null;
    var isArchived  = false;
    var selectedIds = new Set();
    var _DYNAMIC_DOC_APPROVED_USERS = {!! json_encode($dynamicDocApprovedUsers) !!};

    // ---- Collect active filters ----
    function getFilters() {
        return {
            search:        $('#fileSearch').val().trim()    || '',
            agency_id:      $('#fAgency').val()              || '',
            file_type:      $('#fType').val()                || '',
            uploaded_by:    $('#fUploadedBy').val()          || '',
            tag:            $('#fTag').val()                 || '',
            linked_chart:   $('#fLinkedChart').val()         || '',
            pt_status:      $('#fPatientStatus').val()       || '',
            folder_path:    $('#fFolderPath').val().trim()   || '',
            date_range:     $('#fDateRange').val().trim()    || '',
        };
    }

    function applyFilters() {
        renderFilterTags();
        loadAllFiles(1);
    }

    function clearFilters() {
        $('#fileSearch').val('');
        $('#fAgency').val('');
        $('#fType').val('');
        $('#fUploadedBy').val('');
        $('#fTag').val('');
        $('#fLinkedChart').val('');
        $('#fPatientStatus').val('');
        $('#fFolderPath').val('');
        $('#fDateRange').val('');
        $('#activeFilterTags').html('');
        loadAllFiles(1);
    }

    function renderFilterTags() {
        var f    = getFilters();
        var html = '';
        if (f.search)        html += makeTag('Name: ' + f.search, 'search');
        if (f.agency_id)     html += makeTag('Agency: ' + $('#fAgency option:selected').text(), 'agency_id');
        if (f.file_type)     html += makeTag('Type: ' + f.file_type.toUpperCase(), 'file_type');
        if (f.uploaded_by)   html += makeTag('By: ' + f.uploaded_by, 'uploaded_by');
        if (f.tag)           html += makeTag('Tag: ' + $('#fTag option:selected').text(), 'tag');
        if (f.linked_chart)  html += makeTag('Linked Chart: ' + $('#fLinkedChart option:selected').text(), 'linked_chart');
        if (f.pt_status)     html += makeTag('Status: ' + $('#fPatientStatus option:selected').text(), 'pt_status');
        if (f.folder_path)   html += makeTag('Path: ' + f.folder_path, 'folder_path');
        if (f.date_range)    html += makeTag('Date: ' + f.date_range, 'date_range');
        $('#activeFilterTags').html(html);
    }

    function makeTag(label, key) {
        return '<span class="afm-filter-tag">' + label +
            ' <button onclick="clearFilter(\'' + key + '\')" title="Remove">&times;</button></span>';
    }

    function clearFilter(key) {
        if (key === 'search')        $('#fileSearch').val('');
        if (key === 'agency_id')     $('#fAgency').val('');
        if (key === 'file_type')     $('#fType').val('');
        if (key === 'uploaded_by')   $('#fUploadedBy').val('');
        if (key === 'tag')           $('#fTag').val('');
        if (key === 'linked_chart')  $('#fLinkedChart').val('');
        if (key === 'pt_status')     $('#fPatientStatus').val('');
        if (key === 'folder_path')   $('#fFolderPath').val('');
        if (key === 'date_range')    $('#fDateRange').val('');
        applyFilters();
    }

    // ---- Load data (server-rendered HTML partial) ----
    function loadAllFiles(page) {
        page = page || 1;
        var filters = getFilters();
        var url = isArchived ? '/file-manager/all-files/archived' : '/file-manager/all-files/data';
        url += '?page=' + page;
        $.each(filters, function(k, v) { if (v) url += '&' + k + '=' + encodeURIComponent(v); });

        $('#resp').html('<div class="afm-loading"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (html) {
                $('#resp').html(html);
                // Re-check already-selected rows after re-render
                $('.afm-row-cb').each(function () {
                    if (selectedIds.has(parseInt($(this).data('id')))) {
                        $(this).prop('checked', true);
                    }
                });
                syncSelectAll();
                // Update total count from hidden data element injected by partial
                var total = $('#afm-summary').data('total');
                var label = $('#afm-summary').data('label');
                if (total !== undefined) {
                    $('#fileCount').text(total + ' ' + label);
                }
            },
            error: function () {
                $('#resp').html('<div class="afm-empty"><i class="fa fa-exclamation-triangle"></i> Failed to load files.</div>');
            }
        });
    }

    // ---- Pagination (project-standard pattern) ----
    $('body').on('click', '.pagination a', function (event) {
        event.preventDefault();
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var page = $(this).attr('href').split('page=')[1];
        loadAllFiles(page);
    });

    // ---- Toggle archived ----
    function toggleArchived() {
        isArchived = !isArchived;
        clearSelection();
        if (isArchived) {
            $('#archivedBtnText').text('Back to Files');
            $('#btnArchived').removeClass('btn-warning').addClass('btn-secondary');
        } else {
            $('#archivedBtnText').text('Archived');
            $('#btnArchived').removeClass('btn-secondary').addClass('btn-warning');
        }
        clearFilters();
    }

    // ---- Selection helpers ----
    function updateSelectionBar() {
        if (selectedIds.size > 0) {
            $('#selectedCount').text(selectedIds.size + ' file(s) selected');
            $('#bulkDownloadBar').addClass('visible');
        } else {
            $('#bulkDownloadBar').removeClass('visible');
        }
    }

    function clearSelection() {
        selectedIds.clear();
        $('.afm-row-cb').prop('checked', false);
        $('#selectAllCb').prop('checked', false);
        updateSelectionBar();
    }

    function syncSelectAll() {
        var visibleIds = [];
        $('.afm-row-cb').each(function () { visibleIds.push(parseInt($(this).data('id'))); });
        var allChecked = visibleIds.length > 0 && visibleIds.every(function (id) { return selectedIds.has(id); });
        $('#selectAllCb').prop('checked', allChecked);
    }

    $(document).on('change', '#selectAllCb', function () {
        var checked = $(this).is(':checked');
        $('.afm-row-cb').each(function () {
            var id = parseInt($(this).data('id'));
            checked ? selectedIds.add(id) : selectedIds.delete(id);
            $(this).prop('checked', checked);
        });
        updateSelectionBar();
    });

    $(document).on('change', '.afm-row-cb', function () {
        var id = parseInt($(this).data('id'));
        $(this).is(':checked') ? selectedIds.add(id) : selectedIds.delete(id);
        syncSelectAll();
        updateSelectionBar();
    });

    // ---- Bulk download ----
    function bulkDownload() {
        if (selectedIds.size === 0) return;
        var btn = $('#btnBulkDownload');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Preparing...');

        var form = $('<form method="POST" action="/file-manager/all-files/bulk-download" style="display:none">');
        form.append('<input name="_token" value="' + csrfToken + '">');
        selectedIds.forEach(function (id) {
            form.append('<input name="ids[]" value="' + id + '">');
        });
        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(function () {
            btn.prop('disabled', false).html('<i class="fa fa-download"></i> Download Selected');
        }, 2000);
    }

    // ---- Export CSV (respects active filters + archived state) ----
    function exportCsv() {
        var filters = getFilters();
        var url = '/file-manager/all-files/export?';
        var params = [];
        if (isArchived) params.push('archived=1');
        $.each(filters, function (k, v) { if (v) params.push(k + '=' + encodeURIComponent(v)); });
        url += params.join('&');

        var btn = $('#btnExportCsv');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');
        setTimeout(function () {
            btn.prop('disabled', false).html('<i class="fa fa-download"></i> Export CSV');
        }, 2000);

        window.location.href = url;
    }

    // ---- Confirm modal helper ----
    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    function showConfirm(title, message, btnClass, btnText, callback) {
        $.confirm({
            title: title,
            content: message,
            type: 'blue',
            buttons: {
                confirm: {
                    text: btnText,
                    btnClass: btnClass,
                    action: function () { callback(); }
                },
                cancel: { text: 'CANCEL' }
            }
        });
    }

    // ---- Archive / Restore ----
    function archiveFile(id, name, agencyId) {
        showConfirm('Archive File', 'Are you sure you want to archive file <strong>"' + escapeHtml(name) + '"</strong>?', 'btn-warning', 'ARCHIVE', function () {
            $.ajax({
                url: '/file-manager/file/' + id + '?agency_id=' + encodeURIComponent(agencyId || 0),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    toastr.success('File archived successfully');
                    loadAllFiles(1);
                },
                error: function (xhr) { showErrorAndLoginRedirection(xhr); }
            });
        });
    }

    function restoreFile(id, name) {
        showConfirm('Restore File', 'Are you sure you want to restore file <strong>"' + escapeHtml(name) + '"</strong>?', 'btn-success', 'RESTORE', function () {
            $.ajax({
                url: '/file-manager/file/restore/' + id,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    toastr.success('File restored successfully');
                    loadAllFiles(1);
                },
                error: function (xhr) { showErrorAndLoginRedirection(xhr); }
            });
        });
    }

    // ---- Init ----
    $(document).ready(function () {
        // Daterangepicker — same config as appointment page
        $('#fDateRange').daterangepicker({
            autoUpdateInput: false,
            startOfWeek: 'sunday',
            ranges: {
                'Today':        [moment(), moment()],
                'Yesterday':    [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month':   [moment().startOf('month'), moment().endOf('month')],
                'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            }
        }, function (start, end) {
            $('#fDateRange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        });

        loadAllFiles(1);
    });

    // ===== Add to Patient Chart Modal =====
    var lpmFileId        = null;
    var lpmSearchTimer   = null;
    var lpmPatientType   = '';
    var lpmFileNameOrig  = '';

    function openLinkedPatientsModal(fileId, fileName, agencyId) {
        lpmFileId        = fileId;
        lpmPatientType   = '';
        lpmFileNameOrig  = fileName || '';
        $('#lpmFileId').val(fileId);
        $('#lpmAgencyId').val(agencyId);
        $('#lpmFileName').text(fileName);
        $('#lpmPatientSearch').val('');
        $('#lpmSelectedPatientId').val('');
        $('#lpmDocumentName').val(fileName || '');
        $('#lpmRequestService').html('<option value="">Select Request Service</option>');
        if ($('#lpmServices').data('select2')) { $('#lpmServices').select2('destroy'); }
        $('#lpmServices').html('');
        $('#lpmDocumentDate').val('');
        $('#lpmInfoOnly').prop('checked', false);
        $('#lpmInternalUse').prop('checked', false);
        $('#lpmDocumentReview').prop('checked', false).trigger('change');
        $('#lpmMedicationList').prop('checked', false);
        $('#lpmInsuranceElg').prop('checked', false);
        $('#lpmMdoTag').prop('checked', false);
        $('#lpmMdoSource').hide().val('');
        $('#lpmPatientError, #lpmDocumentNameError, #lpmRequestServiceError, #lpmServicesError, #lpmDocumentDateError, #lpmDocumentApprovalUserError, #lpmMedInsuranceError, #lpmMdoTagError').hide();
        $('#lpmSearchResults').hide().empty();
        $('#linkedPatientsModal').modal('show');
        setTimeout(function () {
            if ($('#lpmServices').data('select2')) { $('#lpmServices').select2('destroy'); }
            $('#lpmServices').select2({
                dropdownParent: $('#linkedPatientsModal .modal-content'),
                placeholder: 'Select Service',
                allowClear: true,
                width: '100%',
            });
        }, 300);
    }

    // Init datepicker once modal is shown (idempotent)
    $('#linkedPatientsModal').on('shown.bs.modal', function () {
        if (!$('#lpmDocumentDate').hasClass('hasDatepicker')) {
            $('#lpmDocumentDate').datepicker({
                dateFormat: 'mm/dd/yy',
                changeMonth: true,
                changeYear: true,
            });
        }
    });

    function lpmLoadRequestServices(patientId) {
        $('#lpmRequestService').html('<option value="">Loading...</option>');
        $('#lpmServices').html('').trigger('change');
        $.get('/ajax-request-service', { id: patientId }, function (res) {
            $('#lpmRequestService').html(res || '<option value="">Select Request Service</option>');
        });
    }

    function lpmLoadServices() {
        var requestServiceId = $('#lpmRequestService').val();
        if (!requestServiceId) {
            $('#lpmServices').html('').trigger('change');
            return;
        }
        $.get('/ajax-service', { id: lpmPatientType, document_id: requestServiceId, agency_id: $('#lpmAgencyId').val() }, function (res) {
            if ($('#lpmServices').data('select2')) { $('#lpmServices').select2('destroy'); }
            $('#lpmServices').html(res || '');
            $('#lpmServices').select2({ dropdownParent: $('#linkedPatientsModal .modal-content'), placeholder: 'Select Service', allowClear: true, width: '100%' });
        });
    }

    // Patient search autocomplete
    $('#lpmPatientSearch').on('input', function () {
        var search = $(this).val().trim();
        $('#lpmSelectedPatientId').val('');
        lpmPatientType = '';
        if (lpmSearchTimer) clearTimeout(lpmSearchTimer);
        if (search.length < 2) { $('#lpmSearchResults').hide().empty(); return; }
        lpmSearchTimer = setTimeout(function () {
            $.get('/search-patient-details', { q: search, agency_id: $('#lpmAgencyId').val() }, function (data) {
                var results = typeof data === 'string' ? JSON.parse(data) : data;
                $('#lpmSearchResults').empty();
                if (!results || !results.length) {
                    $('#lpmSearchResults').html('<div class="px-3 py-2 text-muted">No patients found.</div>').show();
                    return;
                }
                $.each(results, function (i, p) {
                    $('<div class="px-3 py-2" style="cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;">')
                        .text(p.name)
                        .data('id', p.id).data('name', p.name).data('type', p.type || 'Patient')
                        .hover(function () { $(this).css('background','#f0f7ff'); }, function () { $(this).css('background',''); })
                        .on('click', function () {
                            var selPatientId = $(this).data('id');
                            $('#lpmSelectedPatientId').val(selPatientId);
                            $('#lpmPatientSearch').val($(this).data('name'));
                            lpmPatientType = $(this).data('type');
                            $('#lpmSearchResults').hide();
                            $('#lpmPatientError').hide();
                            // Append portal ID to document name
                            var ext      = lpmFileNameOrig.lastIndexOf('.') > 0 ? lpmFileNameOrig.slice(lpmFileNameOrig.lastIndexOf('.')) : '';
                            var baseName = ext ? lpmFileNameOrig.slice(0, lpmFileNameOrig.lastIndexOf('.')) : lpmFileNameOrig;
                            $('#lpmDocumentName').val(baseName + ' - ' + selPatientId + ext);
                            // Reset dependent fields then load request services
                            $('#lpmRequestService').html('<option value="">Select Request Service</option>');
                            $('#lpmServices').html('').trigger('change');
                            lpmLoadRequestServices(selPatientId);
                            // Auto-check Document Approval for Patient type
                            if (lpmPatientType === 'Patient') {
                                if (!$('#lpmDocumentReview').is(':checked')) {
                                    $('#lpmDocumentReview').prop('checked', true).trigger('change');
                                }
                            } else {
                                $('#lpmDocumentReview').prop('checked', false).trigger('change');
                            }
                        })
                        .appendTo('#lpmSearchResults');
                });
                $('#lpmSearchResults').show();
            });
        }, 300);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#lpmPatientSearch, #lpmSearchResults').length) {
            $('#lpmSearchResults').hide();
        }
    });

    function lpmSubmit() {
        var patientId          = $('#lpmSelectedPatientId').val();
        var documentName       = $('#lpmDocumentName').val().trim();
        var requestService     = $('#lpmRequestService').val();
        var services           = $('#lpmServices').val() || [];
        var documentDate       = $('#lpmDocumentDate').val().trim();
        var medicationList     = $('#lpmMedicationList').is(':checked') ? 1 : 0;
        var insuranceElg       = $('#lpmInsuranceElg').is(':checked') ? 1 : 0;
        var mdoTag             = $('#lpmMdoTag').is(':checked') ? 1 : 0;
        var mdoSource          = mdoTag ? $('#lpmMdoSource').val() : '';
        var documentReview     = $('#lpmDocumentReview').is(':checked') ? 1 : 0;
        var approvalUserId     = $('#lpmDocumentApprovalUser').val();
        var valid = true;

        $('#lpmPatientError, #lpmDocumentNameError, #lpmRequestServiceError, #lpmServicesError, #lpmDocumentDateError, #lpmDocumentApprovalUserError, #lpmMedInsuranceError, #lpmMdoTagError').hide();

        if (!patientId)      { $('#lpmPatientError').show();        valid = false; }
        if (!documentName)   { $('#lpmDocumentNameError').show();   valid = false; }
        if (!requestService) { $('#lpmRequestServiceError').show(); valid = false; }
        if (!services.length){ $('#lpmServicesError').show();       valid = false; }
        if (!documentDate)   { $('#lpmDocumentDateError').show();   valid = false; }
        if (medicationList && insuranceElg) { $('#lpmMedInsuranceError').show(); valid = false; }
        if (mdoTag && !mdoSource)           { $('#lpmMdoTagError').show();       valid = false; }
        if (documentReview && !approvalUserId) { $('#lpmDocumentApprovalUserError').show(); valid = false; }
        if (!valid) return;

        // Confirmation dialog only for Patient type — skip for Caregiver
        if (lpmPatientType !== 'Patient') {
            $('#lpmLoader').show();
            $('#lpmSaveBtn').prop('disabled', true);
            $.ajax({
                url: '/file-manager/file/' + lpmFileId + '/add-to-chart',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    patient_id:                patientId,
                    document_name:             documentName,
                    request_service_id:        requestService,
                    document_service_id:       services,
                    document_completed_date:   documentDate,
                    upload_for_info_only:      $('#lpmInfoOnly').is(':checked') ? 1 : 0,
                    internal_use:              $('#lpmInternalUse').is(':checked') ? 1 : 0,
                    document_review:           documentReview,
                    document_approval_user_id: approvalUserId,
                    medication_list:           medicationList,
                    insurance_elg:             insuranceElg,
                    mdo_tag:                   mdoTag,
                    mdo_source:                mdoSource,
                    questions:                 [],
                },
                success: function (res) {
                    toastr.success(res.message || 'File added to chart successfully');
                    $('#linkedPatientsModal').modal('hide');
                    loadAllFiles(1);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to add to chart';
                    toastr.error(msg);
                },
                complete: function () {
                    $('#lpmLoader').hide();
                    $('#lpmSaveBtn').prop('disabled', false);
                }
            });
            return;
        }

        // Fetch confirmation questions then show confirm dialog (Patient only)
        var confirmContent = '';
        var queCount = 0;
        $.ajax({
            url: '{{ url("get-doc-questions") }}',
            type: 'GET',
            async: false,
            success: function (res) {
                if (res.data && res.data.length) {
                    queCount = res.data.length;
                    $.each(res.data, function (i, q) {
                        confirmContent += '<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;"><input type="checkbox" value="1" id="lpm_question_' + i + '" data-value="' + q.id + '" style="width:16px;height:16px;min-width:16px;margin-top:2px;cursor:pointer;accent-color:#2196f3;"><label for="lpm_question_' + i + '" style="margin:0;cursor:pointer;font-size:13px;">' + q.question + '</label></div>';
                    });
                    confirmContent += '<div id="lpm_que_error" class="text-danger ml-2"></div>';
                }
            }
        });

        $.confirm({
            title: 'Are you sure?',
            content: confirmContent || 'Do you want to proceed?',
            type: 'blue',
            columnClass: 'col-md-6',
            onContentReady: function () {
                this.$content.closest('.jconfirm-content-pane').css('overflow', 'visible');
            },
            buttons: {
                submit: {
                    text: 'CONFIRM',
                    btnClass: 'btn-blue',
                    action: function () {
                        var self = this;
                        var question_ids = [];
                        if (queCount > 0) {
                            var allChecked = true;
                            for (var i = 0; i < queCount; i++) {
                                if (!$('#lpm_question_' + i).is(':checked')) {
                                    allChecked = false;
                                } else {
                                    question_ids.push($('#lpm_question_' + i).data('value'));
                                }
                            }
                            if (!allChecked) {
                                $('#lpm_que_error').html('Please check your confirmation');
                                return false;
                            }
                        }
                        self.buttons.submit.setText('<i class="fa fa-spinner fa-spin"></i> Confirm');
                        self.buttons.submit.disable();

                        $('#lpmLoader').show();
                        $('#lpmSaveBtn').prop('disabled', true);

                        $.ajax({
                            url: '/file-manager/file/' + lpmFileId + '/add-to-chart',
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            data: {
                                patient_id:                patientId,
                                document_name:             documentName,
                                request_service_id:        requestService,
                                document_service_id:       services,
                                document_completed_date:   documentDate,
                                upload_for_info_only:      $('#lpmInfoOnly').is(':checked') ? 1 : 0,
                                internal_use:1,
                                document_review:           documentReview,
                                document_approval_user_id: approvalUserId,
                                medication_list:           medicationList,
                                insurance_elg:             insuranceElg,
                                mdo_tag:                   mdoTag,
                                mdo_source:                mdoSource,
                                questions:                 question_ids,
                            },
                            success: function (res) {
                                self.close();
                                toastr.success(res.message || 'File added to chart successfully');
                                $('#linkedPatientsModal').modal('hide');
                                loadAllFiles(1);
                            },
                            error: function (xhr) {
                                self.buttons.submit.setText('CONFIRM');
                                self.buttons.submit.enable();
                                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to add to chart';
                                toastr.error(msg);
                            },
                            complete: function () {
                                $('#lpmLoader').hide();
                                $('#lpmSaveBtn').prop('disabled', false);
                            }
                        });
                    }
                },
                cancel: { text: 'CANCEL' }
            }
        });
    }

    // ---- Choose for Document Approval toggle ----
    $('#lpmDocumentReview').on('change', function () {
        if ($(this).is(':checked')) {
            $('#lpmDocumentApprovalWrap').show();
            if (!$('#lpmDocumentApprovalUser').data('tokenInputObject')) {
                $('#lpmDocumentApprovalUser').tokenInput('{{ url("search-nybest-user") }}', {
                    tokenLimit: null,
                    zindex: 1060,
                    preventDuplicates: true,
                    onReady: function () {
                        setTimeout(function () {
                            $('.token-input-list').css({ 'width': '100%', 'border': '1px solid #ced4da', 'border-radius': '4px', 'padding': '2px 6px' });
                            $('.token-input-dropdown').css({ 'max-height': '180px', 'overflow-y': 'auto', 'z-index': '1060' });
                        }, 100);
                    }
                });
            }
        } else {
            $('#lpmDocumentApprovalWrap').hide();
            if ($('#lpmDocumentApprovalUser').data('tokenInputObject')) {
                $('#lpmDocumentApprovalUser').tokenInput('destroy');
            }
        }
    });

    // ---- MDO Tag source show/hide ----
    $('#lpmMdoTag').on('change', function () {
        if ($(this).is(':checked')) {
            $('#lpmMdoSource').show();
        } else {
            $('#lpmMdoSource').hide().val('');
        }
    });

    // ---- Auto-populate Document Approval users when services change (Patient type only) ----
    function lpmGetDynamicApprovalUsers(serviceIds) {
        var dataUser = [];
        if (serviceIds.length === 1) {
            if ($.inArray('181', serviceIds) === -1) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['without_service'] ? _DYNAMIC_DOC_APPROVED_USERS['without_service'][0] : {});
            } else {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['181'] ? _DYNAMIC_DOC_APPROVED_USERS['181'][0] : {});
            }
        } else if (serviceIds.length > 1) {
            var idx = serviceIds.indexOf('181');
            if (idx >= 0 && _DYNAMIC_DOC_APPROVED_USERS['181']) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['181'][0]);
            }
            if (_DYNAMIC_DOC_APPROVED_USERS['without_service']) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['without_service'][0]);
            }
        }
        return dataUser;
    }

    $(document).on('change', '#lpmServices', function () {
        @if(auth()->user()->agency_fk == '')
        if (lpmPatientType === 'Patient') {
            var serviceIds = $(this).val() || [];
            serviceIds = Array.isArray(serviceIds) ? serviceIds : [serviceIds];
            serviceIds = serviceIds.map(String);
            if (serviceIds.length > 0 && serviceIds[0] !== '') {
                var users = lpmGetDynamicApprovalUsers(serviceIds);
                if ($('#lpmDocumentApprovalUser').data('tokenInputObject')) {
                    $('#lpmDocumentApprovalUser').tokenInput('clear');
                    $.each(users, function (i, userObj) {
                        if (userObj) {
                            $.each(userObj, function (userId, userName) {
                                $('#lpmDocumentApprovalUser').tokenInput('add', { id: userId, name: userName });
                            });
                        }
                    });
                }
            }
        }
        @endif
    });

    // ---- openLinkChartModal alias (used by all-files-table partial) ----
    function openLinkChartModal(fileId, fileName, agencyId) {
        openLinkedPatientsModal(fileId, fileName, agencyId || 0);
    }

    // ---- File Preview ----
    function previewFile(id, fileName, fileType, agencyId) {
        $('#afmPreviewFileName').text(fileName);
        $('#afmPreviewContainer').html('<div class="afm-loading"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>');
        $('#afmPreviewModal').modal('show');

        $.ajax({
            url: '/file-manager/file/preview/' + id + '?agency_id=' + encodeURIComponent(agencyId || 0),
            type: 'GET',
            success: function (res) {
                if (res.status && res.data && res.data.url) {
                    var html = '';
                    var ft = (fileType || '').toLowerCase();
                    if (['jpg', 'jpeg', 'png', 'gif'].indexOf(ft) !== -1) {
                        html = '<img src="' + res.data.url + '" alt="' + escapeHtml(fileName) + '" style="max-width:100%;">';
                    } else if (ft === 'pdf') {
                        html = '<iframe src="' + res.data.url + '" style="width:100%;height:70vh;border:none;"></iframe>';
                    } else {
                        html = '<div class="afm-empty">Preview not available for this file type.</div>';
                    }
                    $('#afmPreviewContainer').html(html);
                } else {
                    $('#afmPreviewContainer').html('<div class="afm-empty">' + (res.message || 'Preview not available') + '</div>');
                }
            },
            error: function () {
                $('#afmPreviewContainer').html('<div class="afm-empty">Failed to load preview. Please try downloading the file.</div>');
            }
        });
    }
</script>

