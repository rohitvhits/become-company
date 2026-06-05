@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .import-details-card {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }

    .import-details-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0 !important;
        padding: 15px 20px;
        border: none;
    }

    .import-details-card .card-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 14px;
        color: #333;
    }

    .data-card {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: none;
    }

    .data-card .card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0 !important;
    }

    .data-card .card-header h6 {
        margin: 0;
        font-weight: 600;
        color: #333;
    }

    .table-custom {
        margin: 0;
    }

    .table-custom thead {
        background: #f8f9fa;
    }

    .table-custom thead th {
        border: none;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        padding: 12px 15px;
    }

    .table-custom tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    .table-custom tbody tr:hover {
        background: #f8f9fa;
        transition: background 0.2s ease;
    }

    .badge-custom {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Shimmer Effect */
    .shimmer {
        background: #f6f7f8;
        background-image: linear-gradient(
            to right,
            #f6f7f8 0%,
            #edeef1 20%,
            #f6f7f8 40%,
            #f6f7f8 100%
        );
        background-repeat: no-repeat;
        background-size: 800px 100%;
        display: inline-block;
        position: relative;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% {
            background-position: -800px 0;
        }
        100% {
            background-position: 800px 0;
        }
    }

    .shimmer-line {
        height: 14px;
        margin-bottom: 8px;
        width: 100%;
    }

    .shimmer-line.short {
        width: 40%;
    }

    .shimmer-line.medium {
        width: 70%;
    }

    .shimmer-badge {
        height: 20px;
        width: 60px;
        display: inline-block;
    }

    .shimmer-cell {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    /* Enhanced Modal Styles */
    #recordDetailsModal .modal-dialog {
        max-width: 900px;
    }

    #recordDetailsModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 8px 16px;
    }
    #recordDetailsModal .modal-content{
        border:0px !important;
        border-radius:none !important
    }
    #recordDetailsModal .modal-body {
        padding: 15px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .detail-section {
        padding: 0;
        border-bottom: none;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        padding-bottom: 5px;
        border-bottom: 2px solid #667eea;
    }

    .section-title i {
        font-size: 18px;
        margin-right: 8px;
        color: #667eea;
    }

    .detail-item {
        margin-bottom: 0;
        display: block;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-item-label {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 3px;
    }

    .detail-item-value {
        color: #333;
        font-size: 12px;
        word-break: break-word;
        margin-bottom:5px
    }

    .detail-item-value strong {
        color: #000;
    }

    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .info-card.warning {
        background: #fff3cd;
        border-left-color: #ffc107;
    }

    .info-card.success {
        background: #d4edda;
        border-left-color: #28a745;
    }

    #recordDetailsModal .modal-footer {
        border-top: 2px solid #f0f0f0;
        padding: 15px 30px;
        background: #fafafa;
    }

    /* Scrollbar styling for modal */
    #recordDetailsModal .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    #recordDetailsModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #recordDetailsModal .modal-body::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 4px;
    }

    #recordDetailsModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<div class="main-panel">
    <?php $auth = auth()->user(); ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                <i class="mdi mdi-file-document-outline text-primary"></i> Import Data Records
            </h5>
            <a href="{{ url('patient/import') }}" class="btn btn-sm btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Import List
            </a>
        </div>

        <!-- Import File Details -->
        @if(isset($importFile))
        <div class="card import-details-card">
            <div class="card-header">
                <h6><i class="mdi mdi-information"></i> Import File Details</h6>
            </div>
            <div class="card-body">
                <div class="row mb-1">
                    <div class="col-md-3">
                        <div class="detail-label">File Name</div>
                        <div class="detail-value">
                            {{ ($importFile->file) }}
                            <a href="{{ url('/')}}/patient/import-file-download/{{ $importFile->id}}" download title="Download File">
                                <i class="mdi mdi-download"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="detail-label">Agency</div>
                        <div class="detail-value">{{ $importFile->agency->agency_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="detail-label">Extension</div>
                        <div class="detail-value">{{ strtoupper($importFile->extension) }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="detail-label">Created Date</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($importFile->created_date)->format('m/d/Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Table Card -->
        <div class="card data-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6><i class="mdi mdi-table"></i> Imported Records (Import ID: {{ $import_file_id }})</h6>
                    <div class="d-flex align-items-center">
                        <input type="text" id="search_records" class="form-control form-control-sm mr-2" placeholder="Search records..." style="width: 250px;">
                        <select name="status" class="form-control form-control-sm mr-2" id="status" style="width: 200px;">
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Failed">Failed</option>
                        </select>
                        <select id="per_page_select" class="form-control form-control-sm" style="width: 100px;">
                            <option value="10" @if($per_page ==10) selected @endif>10</option>
                            <option value="25" @if($per_page ==25) selected @endif>25</option>
                            <option value="50" @if($per_page ==50) selected @endif>50</option>
                            <option value="100" @if($per_page ==100) selected @endif>100</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom" id="import_records_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient ID</th>
                                <th>Patient Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Sync Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="import_records_tbody">
                            <tr>
                                <td colspan="12">
                                    <div class="empty-state">
                                        <i class="mdi mdi-loading mdi-spin"></i>
                                        <p>Loading records...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="pagination_info"></div>
                        <nav>
                            <ul class="pagination mb-0" id="pagination_controls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Record Details Modal -->
        <div class="modal fade" id="recordDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white" style="background:#1f202f !important">
                        <h5 class="modal-title">
                            <i class="mdi mdi-file-document"></i> Record Details <span class="view_patient_id"></span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="recordDetailsContent">
                        <div class="text-center py-5">
                            <i class="mdi mdi-loading mdi-spin" style="font-size: 48px;"></i>
                            <p>Loading record details...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 10%;'>
         <pre id='toastrOptions'></pre>
     </div>
    @include('include/footer')

    <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const importFileId = {{ $import_file_id }};
            let currentPage = 1;
            let perPage = 50;
            let searchQuery = '';
            let status = '';

            // Shimmer loading function
            function showShimmerLoading() {
                let shimmerRows = '';
                for (let i = 0; i < perPage; i++) {
                    shimmerRows += `
                        <tr>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line short"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-badge"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line short"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-badge"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-badge"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                            <td class="shimmer-cell"><div class="shimmer shimmer-line medium"></div></td>
                        </tr>
                    `;
                }
                $('#import_records_tbody').html(shimmerRows);
            }

            // Load records on page load
            loadImportRecords(1);

            // Search functionality with debounce
            let searchTimeout;
            $('#search_records').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchQuery = $(this).val();
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    loadImportRecords(currentPage);
                }, 500);
            });

            // Per page change
            $('#per_page_select').on('change', function() {
                perPage = $(this).val();
                currentPage = 1;
                loadImportRecords(currentPage);
            });

            $('#status').on('change', function() {
                status = $(this).val();
                currentPage = 1;
                loadImportRecords(currentPage);
            });

            // Load import records function
            function loadImportRecords(page = 1) {
                currentPage = page;

                // Show shimmer loading
                showShimmerLoading();

                $.ajax({
                    type: "POST",
                    url: "{{ url('patient/view-import-data-ajax') }}/" + importFileId,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        status: status
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            renderRecords(response.data);
                            renderPagination(response.pagination, response.links);
                        } else {
                            showEmptyState();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#import_records_tbody').html(`
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="mdi mdi-alert-circle text-danger"></i>
                                        <p class="text-danger">Error loading data. Please try again.</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                });
            }
            var base_url = "{{ url('/') }}";
            // Render records in table
            function renderRecords(data) {
                let html = '';
                let startIndex = (currentPage - 1) * perPage;

                data.forEach(function(item, index) {

                    let statusBadge = getStatusBadge(item.status);
                    let duplicate ='';
                    if(item.duplicate_status ==1){
                        duplicate = '<span class="badge badge-primary">Duplicate</span>';
                    }
                    let typeBadge = item.type === 'Patient' ?
                        '<span class="badge badge-custom badge-primary">Patient</span>' :
                        '<span class="badge badge-custom badge-success">Caregiver</span>';
                    let genderBadge = item.gender ?
                        `<span class="badge badge-custom badge-info">${item.gender}</span>` :
                        '<span class="text-muted">N/A</span>';
                    let syncStatus = '<span class="badge badge-custom badge-primary">Pending</span>'
                    if(item.sync_status =='Y'){
                        syncStatus = '<span class="badge badge-custom badge-success">Success</span>'
                    }
                    if(item.sync_status =='F'){
                        syncStatus = '<span class="badge badge-custom badge-danger">Failed</span>'
                    }
                    html += `
                        <tr>
                            <td>${startIndex + index + 1}<br>${duplicate}</td>
                            <td>${
    item.patient_id
        ? `<a href="${base_url}/patient/view/${item.patient_id}">${item.patient_id}</a>`
        : 'N/A'
}</td>
                            <td>${item.patient_code || 'N/A'}</td>
                            <td>${item.first_name} ${item.last_name}</td>
                            <td>${typeBadge}</td>
                            <td>${item.mobile || 'N/A'}</td>
                            <td>${item.dob ? formatDate(item.dob) : 'N/A'}</td>
                            <td>${genderBadge}</td>
                            <td>${statusBadge}</td>
                            <td>${syncStatus}</td>
                            <td>${ (item.created_date != "")
        ? moment(item.created_date).format('MM/DD/YYYY hh:mm A')
        : "N/A" }</td>
                            <td>
                                <a id="modal${item.id}" data-toggle="modal" onclick="viewRecordDetails(${item.id})" title="View Details">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });

                $('#import_records_tbody').html(html);
            }

            // Render pagination
            function renderPagination(pagination, links) {
                // Pagination info
                let info = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`;
                $('#pagination_info').html(info);

                // Pagination controls
                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                    <li class="page-item ${!links.prev ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="loadImportRecords(${pagination.current_page - 1}); return false;">
                            <i class="mdi mdi-chevron-left"></i> Previous
                        </a>
                    </li>
                `;

                // Page numbers
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                // First page
                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="loadImportRecords(1); return false;">1</a>
                        </li>
                    `;
                    if (startPage > 2) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadImportRecords(${i}); return false;">${i}</a>
                        </li>
                    `;
                }

                // Last page
                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="loadImportRecords(${pagination.last_page}); return false;">${pagination.last_page}</a>
                        </li>
                    `;
                }

                // Next button
                paginationHtml += `
                    <li class="page-item ${!links.next ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="loadImportRecords(${pagination.current_page + 1}); return false;">
                            Next <i class="mdi mdi-chevron-right"></i>
                        </a>
                    </li>
                `;

                $('#pagination_controls').html(paginationHtml);
            }

            // Show empty state
            function showEmptyState() {
                $('#import_records_tbody').html(`
                    <tr>
                        <td colspan="12">
                            <div class="empty-state">
                                <i class="mdi mdi-file-document-outline"></i>
                                <p>No records found for this import.</p>
                            </div>
                        </td>
                    </tr>
                `);
                $('#pagination_info').html('Showing 0 to 0 of 0 entries');
                $('#pagination_controls').html('');
            }

            // Format date
            function formatDate(dateString) {
           
                if (!dateString) return 'N/A';
                if (dateString =="0000-00-00") return 'N/A';
                let date = new Date(dateString);

                let mm = String(date.getMonth() + 1).padStart(2, '0'); // month
                let dd = String(date.getDate()).padStart(2, '0');      // day
                let yyyy = date.getFullYear();                         // year

                return `${mm}/${dd}/${yyyy}`;
            }

            // Get status badge
            function getStatusBadge(status) {
                let badgeClass = 'badge-secondary';
                switch(status?.toLowerCase()) {
                    case 'completed':
                    case 'success':
                    case 'booked':
                    case 'active':
                        badgeClass = 'badge-success';
                        break;
                    case 'pending':
                        badgeClass = 'badge-warning';
                        break;
                    case 'failed':
                    case 'error':
                    case 'rejected':
                        badgeClass = 'badge-danger';
                        break;
                    case 'processing':
                    case 'in progress':
                        badgeClass = 'badge-info';
                        break;
                }
                return `<span class="badge badge-custom ${badgeClass}">${status || 'N/A'}</span>`;
            }

            // View record details in modal
            function viewRecordDetails(recordId) {
                // Show modal with loading state
                $('#modal'+recordId).attr('data-target','#recordDetailsModal');
                
                $('#recordDetailsContent').html(`
                    <div class="text-center py-5">
                        <i class="mdi mdi-loading mdi-spin" style="font-size: 48px;"></i>
                        <p>Loading record details...</p>
                    </div>
                `);

                // Fetch record details via AJAX
                $.ajax({
                    type: "GET",
                    url: "{{ url('patient/view-import-data-show') }}/" + recordId,
                    success: function(response) {
                        if (response.success && response.data) {
                            renderRecordDetails(response.data);
                        } else {
                            $('#recordDetailsContent').html(`
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert"></i> Record not found.
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#recordDetailsContent').html(`
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle"></i> Error loading record details. Please try again.
                            </div>
                        `);
                    }
                });
            }

            // Render record details in modal
            function renderRecordDetails(data) {
                let html = '';

                // Helper function to format value
                function formatValue(key, value, data) {

                    if (value === null || value === undefined || value === '') {
                        return '<span class="text-muted">N/A</span>';
                    } else if (key === 'created_date' || key === 'service_expiry_date' || key === 'dob') {
                        if(key === 'created_date'){
                            return moment(value).format('MM/DD/YYYY hh:mm A');
                        }
                        if(key === 'service_expiry_date'){
                            let newDateValue ="N/A";
                           if(value !=null && value !="" && value !="0000-00-00"){
                            newDateValue = moment(value).format('MM/DD/YYYY');
                           }
                           return newDateValue;
                        }
                        return formatDate(value);
                    } else if (key === 'patient_id') {
                        return '<a href="{{ url("patient/view/")}}/'+value+'">'+value+'</a>';
                    } else if (key === 'status') {
                        return getStatusBadge(value);
                    } else if (key === 'type') {
                        return data[key] === 'Patient' ?
                            '<span class="badge badge-custom badge-primary">Patient</span>' :
                            '<span class="badge badge-custom badge-success">Caregiver</span>';
                    } else if (key === 'gender') {
                        return `<span class="badge badge-custom badge-info">${value}</span>`;
                    } else if (key === 'created_by') {
                        return `${data.user_detail.first_name} ${data.user_detail.last_name}`;
                    }
                    return value;
                }

                // Helper function to render field
                function renderField(label, key) {
                    let value = formatValue(key, data[key], data);
                    if(key =='appointment_mode' || key =='sms'){
                        return `
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-item-label">${label}</div>
                                <div class="detail-item-value">${value}</div>
                            </div>
                        </div>
                    `;
                    }else{
                        return`
                        <div class="col-md-3 mb-2">
                            <div class="detail-item">
                                <div class="detail-item-label">${label}</div>
                                <div class="detail-item-value">${value}</div>
                            </div>
                        </div>
                    `
                
                    }
                    
                }

                // Section 1: Basic Information
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-information-outline"></i>
                            Basic Information
                        </div>
                        <div class="row">
                            ${renderField('Record ID', 'id')}
                            ${renderField('Patient ID', 'patient_id')}
                            ${renderField('Import File ID', 'import_file_id')}
                            ${renderField('Patient Code', 'patient_code')}
                            ${renderField('Type', 'type')}
                            ${renderField('Status', 'status')}
                        </div>
                    </div>
                `;

                // Section 2: Personal Details
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-account"></i>
                            Personal Details
                        </div>
                        <div class="row">
                            ${renderField('First Name', 'first_name')}
                            ${renderField('Last Name', 'last_name')}
                            ${renderField('Full Name', 'full_name')}
                            ${renderField('Date of Birth', 'dob')}
                            ${renderField('Gender', 'gender')}
                            ${renderField('Language', 'language')}
                        </div>
                    </div>
                `;

                // Section 3: Contact Information
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-phone"></i>
                            Contact Information
                        </div>
                        <div class="row">
                            ${renderField('Mobile', 'mobile')}
                            ${renderField('Phone', 'phone')}
                            
                        </div>
                    </div>
                `;

                // Section 4: Address Details
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-map-marker"></i>
                            Address Details
                        </div>
                        <div class="row">
                            ${renderField('Address Line 1', 'address1')}
                            ${renderField('Address Line 2', 'address2')}
                            ${renderField('City', 'city')}
                            ${renderField('State', 'state')}
                            ${renderField('Zip Code', 'zip_code')}
                        </div>
                    </div>
                `;

                // Section 5: Medical Information
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-medical-bag"></i>
                            Medical Information
                        </div>
                        <div class="row">
                            ${renderField('CIN', 'cin')}
                            ${renderField('Insurance Name', 'insurance_name')}
                            ${renderField('Insurance ID', 'insurance_id')}
                            ${renderField('Services', 'service_id')}
                            ${renderField('Service Expiry Date', 'service_expiry_date')}
                            ${renderField('Discipline', 'diciplin')}
                        </div>
                    </div>
                `;

                // Section 7: Record Metadata
                html += `
                    <div class="detail-section">
                        <div class="section-title">
                            <i class="mdi mdi-file-document-outline"></i>
                            Record Metadata
                        </div>
                        <div class="row">
                            ${renderField('Created Date', 'created_date')}
                            ${renderField('Created By', 'created_by')}
                        </div>
                    </div>
                `;

                $('#recordDetailsContent').html(html);
            }

            // Make functions globally accessible
            window.loadImportRecords = loadImportRecords;
            window.viewRecordDetails = viewRecordDetails;
        });
    </script>
</div>
