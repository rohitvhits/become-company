$(document).ready(function() {
    // Custom file input label update
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose CSV file...');
    });

    // Form validation
    $('#submitImportForm').click(function(e) {
        var isValid = true;

        // Clear previous errors
        $('#images_error').html("");
        $('#agency_error').html("");

        var hasAgencyFk = $('#has_agency_fk').val();
        if (hasAgencyFk == '0') {
            // Validate agency selection
            var agency_ids = $('#import_agency_ids').val();
            if (agency_ids == '') {
                $('#agency_error').html("Please select an agency");
                isValid = false;
            }
        }

        // Validate file upload
        var fileInput = $('#upload_csv_file_id').prop('files');
        if (fileInput.length == 0) {
            $('#images_error').html("Please select a CSV file to upload");
            isValid = false;
        } else {
            var fileName = fileInput[0].name;
            var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();

            if (fileExtension !== 'csv') {
                $('#images_error').html("Only CSV files are allowed");
                isValid = false;
            }

        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        $(this).find('button[type="button"]').html(
            '<i class="mdi mdi-loading mdi-spin mr-2"></i>Uploading...'
        ).prop('disabled', true);

        var foms = $('#importForm')[0];
        var formData = new FormData(foms);
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));

         $.ajax({
             async: false,
             global: false,
             processData: false,
             contentType: false,
             type: "POST",
             url: importConfig.importDataUrl,
             data: formData,
             success: function(res) {

                var modalEl = document.getElementById('exampleModal-patient-view-import');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
                $('#formnewNN').html(res);

                 setTimeout(function(e) {
                    $('#btn-text').text('Import');
                    $('#loaderss_id').addClass('d-none');
                 }, 1000);
                 $('#appps_id').click();
            },
            error:function(jqr){
                $('#btn-text').text('Import');
                $('#loaderss_id').addClass('d-none');
                showErrorAndLoginRedirection(jqr);
            }
         })
    });

    // Initialize Select2 if available
    if ($.fn.select2) {
        $('#import_agency_ids').select2({
            placeholder: 'Select Agency',
            allowClear: true
        });
    }
});
$('#submitId').submit(function(e) {
    $('#import_loaderss_id').removeClass('d-none');
    $(this).find('[type="submit"]').prop('disabled', true).text('Importing...');
     $('#row_error').html("");
     var selected = [];
     var selected_data = [];


     $.each($(".selectvalues option:selected"), function() {
         selected.push($(this).val());
         if ($(this).val() != "") {
             selected_data.push($(this).val());
         }
     });

     $('#order_data').val(selected.join());

     if (selected_data.length < 3) {
        $('#import_loaderss_id').addClass('d-none');
        $(this).find('[type="submit"]').prop('disabled', false).text('Confirm & Import');
         toastr.error('Please map all required fields')
         return false;
     }

    const required = ['type', 'dob', 'first_name', 'last_name','mobile','gender','service_id'];
    const missingFields = [];
    hasError = false;
    var errorCount = 1;
    required.forEach(function(field) {
        if (!selected_data.includes(field)) {
            if(field =='type'){
                missingFields.push(errorCount+'. Record Type (only Caregiver or Patient)');
            }
            if(field =='dob'){
                missingFields.push(errorCount+'. Date of Birth');
            }
            if(field =='first_name'){
                missingFields.push(errorCount+'. First Name');
            }
            if(field =='last_name'){
                missingFields.push(errorCount+'. Last Name');
            }
            if(field =='mobile'){
                missingFields.push(errorCount+'.Mobile');
            }
            if(field =='gender'){
                missingFields.push(errorCount+'. Gender (only Male or Female)');
            }
            if(field =='service_id'){
                missingFields.push(errorCount+'. Services');
            }
            errorCount++;
            hasError = true;
        }
    });

    if (hasError) {
        $('#import_loaderss_id').addClass('d-none');
        $(this).find('[type="submit"]').prop('disabled', false).text('Confirm & Import');
        toastr.error('Please map all required fields:<br>' + missingFields.join('<br>'), '', { allowHtml: true });
        return false;
    }

 });

// Import History functionality
let currentPage = 1;
let perPage = 50;
let searchQuery = '';

// Shimmer loading function
function showShimmerLoading() {
    let shimmerRows = '';
    for (let i = 0; i < perPage; i++) {
        shimmerRows += `
            <tr class="shimmer-row">
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-line short"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-line medium"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-line"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-line"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-badge"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-badge"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-badge"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-badge"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-button"></div>
                    <div class="shimmer shimmer-button"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-button"></div>
                    <div class="shimmer shimmer-button"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-button"></div>
                    <div class="shimmer shimmer-button"></div>
                </td>
                <td class="shimmer-cell">
                    <div class="shimmer shimmer-button"></div>
                    <div class="shimmer shimmer-button"></div>
                </td>

            </tr>
        `;
    }
    $('#import_history_tbody').html(shimmerRows);
}

// Load data on page load
loadImportHistory(1);

// Search functionality with debounce
let searchTimeout;
$('#search_import').on('keyup', function() {
    clearTimeout(searchTimeout);
    searchQuery = $(this).val();
    searchTimeout = setTimeout(function() {
        currentPage = 1;
        loadImportHistory(currentPage);
    }, 500);
});

// Per page change
$('#per_page_select').on('change', function() {
    perPage = $(this).val();
    currentPage = 1;
    loadImportHistory(currentPage);
});

function loadImportHistory(page = 1) {
    currentPage = page;

    // Show shimmer loading state
    showShimmerLoading();

    $.ajax({
        type: "POST",
        url: importConfig.importFilesDataUrl,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            page: page,
            per_page: perPage,
            search: searchQuery
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                renderImportHistory(response.data);
                renderPagination(response.pagination, response.links);
            } else {
                showEmptyState();
            }
        },
        error: function(xhr, status, error) {
            $('#import_history_tbody').html(`
                <tr>
                    <td colspan="9">
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

function renderImportHistory(data) {
    let html = '';
    let startIndex = (currentPage - 1) * perPage;

    data.forEach(function(item, index) {
        // Determine if record is approved
        let isApproved = (item.status === 'Waiting For Approval');
        // If import_status is Approved, display status as Pending
        let displayStatus = isApproved ? 'Waiting For Approval' : item.status;
        let statusBadge = getImportStatusBadge(displayStatus);
        let approvedStatus = getImportStatusBadge(item.import_status);
        let approvedUser = item.approved_user === "" ? "" : item.approved_user;
        
        // Build action buttons
        let actionButtons = `
            <a href="${importConfig.baseUrl}/patient/view-import-data/${item.id}" title="View Details">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="${importConfig.baseUrl}/patient/import-file-download/${item.id}" title="Download File">
                <i class="mdi mdi-download"></i>
            </a>
            <a href="javascript:void(0)" class="btn-import-logs" data-id="${item.id}" title="View Logs">
                <i class="mdi mdi-clipboard-text"></i>
            </a>`;

        if (isApproved) {
            // Show Approved button, hide Delete button for approved records
            actionButtons += `
            <a href="javascript:void(0)" class="btn-approved-import" data-id="${item.id}" data-file="${item.file}" title="Approved">
                    <i class="mdi mdi-check-circle"></i>
                </a>`;
                 actionButtons += `
                <a href="javascript:void(0)" class="btn-delete-import" data-id="${item.id}" data-file="${item.file}" title="Delete">
                    <i class="mdi mdi-delete"></i>
                </a>`;
        } 

        html += `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td class="nowrap">
                    <i class="mdi mdi-calendar-clock text-muted mr-1"></i>
                    ${formatDate(item.created_date)}
                </td>
                <td class="nowrap">
                    <i class="mdi mdi-office-building text-primary mr-1"></i>
                    ${item.agency_name}
                </td>
                <td class="nowrap">
                    <i class="mdi mdi-file-document text-success mr-1"></i>
                    ${item.file}
                    <small class="d-block text-muted">${item.extension.toUpperCase()}</small>
                </td>
                <td class="nowrap">
                    <span class="badge badge-info">${item.total_records !=0?item.total_records:item.total_record}</span>
                </td>
                <td class="nowrap">
                    <span class="badge badge-success">${item.successful_count}</span>
                </td>
                <td class="nowrap">
                    <span class="badge badge-danger">${item.failed_count}</span>
                </td>
                <td class="nowrap">
                    <span class="badge badge-danger">${item.duplicate_record}</span>
                </td>
                <td class="nowrap">
                    ${statusBadge}
                </td>
                <td class="nowrap">
                    ${approvedStatus}
                </td>
                <td class="nowrap">
                    ${formatDate(item.approved_date)} <br> ${approvedUser}
                </td>

                <td>
                    ${actionButtons}
                </td>
            </tr>
        `;
    });

    $('#import_history_tbody').html(html);
}

function renderPagination(pagination, links) {
    // Pagination info
    let info = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`;
    $('#pagination_info').html(info);

    // Pagination controls
    let paginationHtml = '';

    // Previous button
    paginationHtml += `
        <li class="page-item ${!links.prev ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadImportHistory(${pagination.current_page - 1}); return false;">
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
                <a class="page-link" href="#" onclick="loadImportHistory(1); return false;">1</a>
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
                <a class="page-link" href="#" onclick="loadImportHistory(${i}); return false;">${i}</a>
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
                <a class="page-link" href="#" onclick="loadImportHistory(${pagination.last_page}); return false;">${pagination.last_page}</a>
            </li>
        `;
    }

    // Next button
    paginationHtml += `
        <li class="page-item ${!links.next ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadImportHistory(${pagination.current_page + 1}); return false;">
                Next <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;

    $('#pagination_controls').html(paginationHtml);
}

function showEmptyState() {
    $('#import_history_tbody').html(`
        <tr>
            <td colspan="9">
                <div class="empty-state">
                    <i class="mdi mdi-file-document-outline"></i>
                    <p>No import records found. Upload your first CSV file to get started.</p>
                </div>
            </td>
        </tr>
    `);
    $('#pagination_info').html('Showing 0 to 0 of 0 entries');
    $('#pagination_controls').html('');
}

function getImportStatusBadge(status) {
    let badgeClass = 'badge-secondary';
    switch(status) {
        case 'Completed':
        case 'Approved':
            badgeClass = 'badge-success';
            break;
        case 'Partially Completed':
            badgeClass = 'badge-warning';
            break;
        case 'Pending':
            badgeClass = 'badge-secondary';
            break;
        case 'In Progress':
            badgeClass = 'badge-info';
            break;
        case 'Failed':
            badgeClass = 'badge-danger';
            break;
    }
    return `<span class="badge badge-status ${badgeClass}">${status}</span>`;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return moment(dateString).format('MM/DD/YYYY hh:mm A');
   
}

// Delegated handler for Approved button
$(document).on('click', '.btn-approved-import', function() {
    var btn = $(this);
    var importId = btn.data('id');
    var fileName = btn.data('file');

    $.confirm({
        title: 'Approve Import Record',
        content: 'The import record <strong>"' + fileName + '"</strong> will be marked as Approved and moved to Pending processing state.',
        type: 'green',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-success',
                action: function() {
                    btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

                    $.ajax({
                        type: 'POST',
                        url: importConfig.approveImportUrl + "/" + importId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.error_msg);
                            loadImportHistory(currentPage);
                        },
                        error: function(xhr) {

                            btn.prop('disabled', false).html('<i class="mdi mdi-check-circle"></i>');
                            showErrorAndLoginRedirection(xhr);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-danger',
                action: function() {

                }
            }
        }
    });
});

// Delegated delete handler for dynamically loaded rows
$(document).on('click', '.btn-delete-import', function() {
    var btn = $(this);
    var importId = btn.data('id');
    var fileName = btn.data('file');

    $.confirm({
        title: 'Delete Import Record',
        content: 'Are you sure you want to delete the import record <strong>"' + fileName + '"</strong>? This action cannot be undone.',
        type: 'blue',
        buttons: {
            confirm: {
                text: 'Delete',
                btnClass: 'btn-primary',
                action: function() {
                    btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

                    $.ajax({
                        type: 'DELETE',
                        url: importConfig.deleteImportUrl + "/" + importId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.error_msg);
                            btn.closest('tr').fadeOut(300, function() {
                                $(this).remove();
                                // Reload if table is empty
                                if ($('#import_history_tbody tr').length === 0) {
                                    loadImportHistory(currentPage);
                                }
                            });
                        },
                        error: function(xhr) {

                            btn.prop('disabled', false).html('<i class="mdi mdi-delete"></i>');
                             showErrorAndLoginRedirection(xhr);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel'
            }
        }
    });
});

function syncFile(){
    $.ajax({
        type: "get",
        url: importConfig.syncImportUrl,
       success:function(res){
            location.reload();
       }
    });
}

// Delegated handler for Log button
var currentLogImportId = null;
$(document).on('click', '.btn-import-logs', function() {
    var importId = $(this).data('id');
    currentLogImportId = importId;

    // Reset modal content
    $('#import_logs_tbody').html(`
        <tr>
            <td colspan="7" class="text-center p-4">
                <i class="mdi mdi-loading mdi-spin" style="font-size:24px"></i>
                <p class="mb-0 mt-2 text-muted">Loading logs...</p>
            </td>
        </tr>
    `);
    $('#log_pagination_info').html('');
    $('#log_pagination_controls').html('');

    // Open modal
    var modalEl = document.getElementById('importLogsModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Fetch logs
    loadImportLogs(importId, 1);
});

var globalResponse = [];
function loadImportLogs(importId, page) {
    $.ajax({
        type: 'POST',
        url: importConfig.importLogsUrl + '/' + importId,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            page: page
        },
        success: function(response) {
            if (response.status && response.data.length > 0) {
                globalResponse = [];
                globalResponse = response.data;
                renderImportLogs(response.data);
                
                renderLogPagination(response.pagination, importId);
            } else {
                $('#import_logs_tbody').html(`
                    <tr>
                        <td colspan="7" class="text-center p-4">
                            <i class="mdi mdi-file-document-outline" style="font-size:48px;opacity:0.3"></i>
                            <p class="mb-0 mt-2 text-muted">No Logs Found</p>
                        </td>
                    </tr>
                `);
                $('#log_pagination_info').html('');
                $('#log_pagination_controls').html('');
            }
        },
        error: function(xhr) {
            $('#import_logs_tbody').html(`
                <tr>
                    <td colspan="7" class="text-center p-4">
                        <i class="mdi mdi-alert-circle text-danger" style="font-size:48px;opacity:0.3"></i>
                        <p class="mb-0 mt-2 text-danger">Error loading logs. Please try again.</p>
                    </td>
                </tr>
            `);
            $('#log_pagination_info').html('');
            $('#log_pagination_controls').html('');
        }
    });
}

function renderImportLogs(data) {
    var html = '';
    data.forEach(function(log, index) {
        var message = log.message || '';
        var shortMessage = message.length > 50 ? message.substring(0, 50) + '...' : message;
        const fullName = log.user_with_trash
    ? `${log.user_with_trash.first_name} ${log.user_with_trash.last_name}`
    : 'N/A';
        html += `
            <tr>
                <td class="nowrap">${log.ip || 'N/A'}</td>
                <td class="nowrap">${log.type || 'N/A'}</td>
                <td class="nowrap">${log.module || 'N/A'}</td>
                <td>${shortMessage}</td>
                <td class="nowrap">${formatDate(log.created_date)}</td>
                <td class="nowrap">${fullName}</td>
                <td class="nowrap">
                    <a href="javascript:void(0)" class="btn-view-log-detail"
                        data-ip="${index}"
                        
                        title="View Detail">
                        <i class="mdi mdi-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    });
    $('#import_logs_tbody').html(html);
}

function renderLogPagination(pagination, importId) {
    var info = 'Showing ' + (pagination.from || 0) + ' to ' + (pagination.to || 0) + ' of ' + pagination.total + ' entries';
    $('#log_pagination_info').html(info);

    var paginationHtml = '';

    // Previous
    paginationHtml += '<li class="page-item ' + (!pagination.from || pagination.current_page <= 1 ? 'disabled' : '') + '">';
    paginationHtml += '<a class="page-link" href="#" onclick="loadImportLogs(' + importId + ',' + (pagination.current_page - 1) + '); return false;">&laquo;</a>';
    paginationHtml += '</li>';

    for (var i = 1; i <= pagination.last_page; i++) {
        paginationHtml += '<li class="page-item ' + (i === pagination.current_page ? 'active' : '') + '">';
        paginationHtml += '<a class="page-link" href="#" onclick="loadImportLogs(' + importId + ',' + i + '); return false;">' + i + '</a>';
        paginationHtml += '</li>';
    }

    // Next
    paginationHtml += '<li class="page-item ' + (pagination.current_page >= pagination.last_page ? 'disabled' : '') + '">';
    paginationHtml += '<a class="page-link" href="#" onclick="loadImportLogs(' + importId + ',' + (pagination.current_page + 1) + '); return false;">&raquo;</a>';
    paginationHtml += '</li>';

    $('#log_pagination_controls').html(paginationHtml);
}

// Format response data for log detail display
function formatResponseData(value) {
    if (!value || value === 'undefined' || value === 'null') {
        return '-';
    }
    // Try to parse as JSON for pretty display
    try {
         const parsed = typeof value === 'object' ? value : JSON.parse(value);
        return $('<div>').text(JSON.stringify(parsed, null, 2)).html();
    } catch (e) {
        // Not JSON, display as escaped plain text
        return $('<div>').text(value).html();
    }
}

// Delegated handler for log detail view
$(document).on('click', '.btn-view-log-detail', function() {
    
    // Format response data for display
    
    var oldResponseHtml = formatResponseData(globalResponse[$(this).data('ip')].old_response);
    var newResponseHtml = formatResponseData(globalResponse[$(this).data('ip')].new_response);

    var html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-0">
                    <div class="card-header py-2" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:6px 6px 0 0">
                        <h6 class="mb-0 text-white font-weight-bold">
                            <i class="mdi mdi-file-document-outline mr-1"></i> Old Response
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <pre style="max-height:350px;overflow-y:auto;background:#f8f9fa;padding:12px;margin:0;font-size:12px;white-space:pre-wrap;word-break:break-word;border-radius:0 0 6px 6px">${oldResponseHtml}</pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-0">
                    <div class="card-header py-2" style="background:linear-gradient(135deg,#28a745 0%,#218838 100%);border-radius:6px 6px 0 0">
                        <h6 class="mb-0 text-white font-weight-bold">
                            <i class="mdi mdi-file-document-outline mr-1"></i> New Response
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <pre style="max-height:350px;overflow-y:auto;background:#f8f9fa;padding:12px;margin:0;font-size:12px;white-space:pre-wrap;word-break:break-word;border-radius:0 0 6px 6px">${newResponseHtml}</pre>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#log_detail_body').html(html);

    var modalEl = document.getElementById('logDetailModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
});
