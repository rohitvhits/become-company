/**
 * Pending Medicals Module JS with Custom JavaScript Pagination
 */

// Global variables
var allMedicalsData = [];
var currentPage = 1;
var recordsPerPage = 50;

/**
 * Load pending medicals list via AJAX
 */
function loadAjaxList() {
    // Validate agency selection
    var agency_id = $('#agency_id').val();
    if (!agency_id) {
        toastr.error('Please select an agency first');
        return false;
    }

    $('.shimmer_id').removeClass('hide');
    $('#response_requested_id').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');

    $.ajax({
        url: _LOAD_DATA_URL,
        data: {
            'agency_id': agency_id,
            'medical_due_date':$('.medical_due_date').val()
        },
        type: "GET",
        dataType: 'json',
        success: function(response) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');

            if (response.status && response.data) {
                allMedicalsData = response.data;
                currentPage = 1; // Reset to first page
                renderTable();
            } else {
                allMedicalsData = [];
                if (response.message) {
                    toastr.info(response.message);
                }
                renderTable();
            }
        },
        error: function(jqXHR) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');

            var errorMsg = 'Error loading pending medicals data';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                errorMsg = jqXHR.responseJSON.message;
            } else if (jqXHR.statusText) {
                errorMsg = 'Error: ' + jqXHR.statusText;
            }

            toastr.error(errorMsg);
            allMedicalsData = [];
            renderTable();
        }
    });
}

/**
 * Render table with current page data
 */
function renderTable() {
  
    var totalRecords = allMedicalsData.length;
    var totalPages = Math.ceil(totalRecords / recordsPerPage);
    var startIndex = (currentPage - 1) * recordsPerPage;
    var endIndex = Math.min(startIndex + recordsPerPage, totalRecords);

    // Build table HTML
    var tableHtml = '<table id="order-listing1" class="table table-bordered table-width1">';
    tableHtml += '<thead>';
    tableHtml += '<tr>';
    tableHtml += '<th>No</th>';
    tableHtml += '<th>Employee Code</th>';
    tableHtml += '<th>Employee Name</th>';
    tableHtml += '<th>DOB</th>';
    tableHtml += '<th>Gender</th>';
    tableHtml += '<th>Phone</th>';
   
    tableHtml += '<th>Medical Name</th>';
    tableHtml += '<th>Medical Due Date</th>';
    tableHtml += '<th>Medical Status</th>';
    tableHtml += '</tr>';
    tableHtml += '</thead>';
    tableHtml += '<tbody>';

    if (totalRecords === 0) {
        tableHtml += '<tr>';
        tableHtml += '<td colspan="10" class="text-center">No record available</td>';
        tableHtml += '</tr>';
    } else {
        for (var i = startIndex; i < endIndex; i++) {
            var record = allMedicalsData[i];
            var rowNumber = i + 1;
            var employeeCode = record.employee_code || record.EmployeeCode || 'N/A';
            var employeeId = record.employee_id || record.EmployeeID || '';
            var firstName = record.first_name || record.FirstName || '';
            var middleName = record.middle_name || record.MiddleName || '';
            var lastName = record.last_name || record.LastName || '';
            var fullName = (firstName + ' ' + middleName + ' ' + lastName).trim() || 'N/A';

            // Demographic fields
            var dob = record.dob || record.DOB || 'N/A';
            var gender = record.gender || record.Gender || 'N/A';

            // Contact fields
            var phone = record.Cell || record.Cell || '';
            var mobile = record.mobile || record.Mobile || '';
            var primaryPhone = phone || mobile || 'N/A';

            // Address
            var address = record.address || record.Address || 'N/A';
            if (address.length > 30) {
                address = address.substring(0, 30) + '...';
            }
           
            var medicalId = record.medical_id || record.MedicalID || 'N/A';
            var medicalName = record.medical_name || record.MedicalName || 'N/A';
            var medicalDueDate = record.MedicalDue  || 'N/A';
            var medicalStatus = record.medical_status || record.status || 'pending';

            var status = record.status || 'pending';
            
            if (dob !== 'N/A') {
                try {
                    var dobDate = new Date(dob);
                    dob = (dobDate.getMonth() + 1) + '/' + dobDate.getDate() + '/' + dobDate.getFullYear();
                } catch (e) {
                    // Keep original if parsing fails
                }
            }

            // Determine badge class based on status
            var badgeClass = 'badge-info';
            var statusLower = status.toLowerCase();
            if (statusLower === 'completed') {
                badgeClass = 'badge-success';
            } else if (statusLower === 'pending') {
                badgeClass = 'badge-warning';
            } else if (statusLower === 'in_progress' || statusLower === 'in progress') {
                badgeClass = 'badge-info';
            } else if (statusLower === 'cancelled' || statusLower === 'failed') {
                badgeClass = 'badge-danger';
            }

            if (medicalDueDate !== 'N/A' && medicalDueDate !== null) {
                try {
                    var dueDate = new Date(medicalDueDate);
                    medicalDueDate = (dueDate.getMonth() + 1) + '/' + dueDate.getDate() + '/' + dueDate.getFullYear();
                } catch (e) {
                    medicalDueDate = 'N/A';
                }
            } else {
                medicalDueDate = 'N/A';
            }

            // Determine badge class based on medical status
            var medicalBadgeClass = 'badge-info';
            var medicalStatusLower = medicalStatus.toLowerCase();
            if (medicalStatusLower === 'completed') {
                medicalBadgeClass = 'badge-success';
            } else if (medicalStatusLower === 'pending') {
                medicalBadgeClass = 'badge-warning';
            } else if (medicalStatusLower === 'in_progress' || medicalStatusLower === 'in progress') {
                medicalBadgeClass = 'badge-info';
            } else if (medicalStatusLower === 'cancelled' || medicalStatusLower === 'failed') {
                medicalBadgeClass = 'badge-danger';
            }
            
            tableHtml += '<tr>';
            tableHtml += '<td>' + rowNumber + '</td>';
            tableHtml += '<td><a href="javascript:void(0)" class="employee-code-link" data-employee-code="' + employeeCode + '" style="text-decoration:none" onclick="showEmployeeDetails(\'' + employeeCode + '\', this)"><span class="employee-code-text">' + employeeCode + '</span><span class="employee-code-loader" style="display:none;"><i class="mdi mdi-loading mdi-spin"></i> Loading...</span></a></td>';
            tableHtml += '<td>' + fullName + '</td>';
            tableHtml += '<td>' + dob + '</td>';
            tableHtml += '<td>' + gender + '</td>';
            tableHtml += '<td>' + primaryPhone + '</td>';
            // tableHtml += '<td title="' + (record.address || record.Address || '') + '">' + address + '</td>';
            tableHtml += '<td>' + capitalizeFirst(medicalName) + '</td>';
            tableHtml += '<td>' + medicalDueDate + '</td>';
            tableHtml += '<td><span class="badge ' + medicalBadgeClass + '">' + capitalizeFirst(medicalStatus) + '</span></td>';
            
            tableHtml += '</tr>';
        }
    }

    tableHtml += '</tbody>';
    tableHtml += '</table>';

    // Add pagination if needed
    if (totalPages > 1) {
        tableHtml += '<div class="pull-right pegination-margin">';
        tableHtml += '<nav aria-label="Page navigation">';
        tableHtml += '<ul class="pagination">';

        // Previous button
        tableHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
        tableHtml += '<a class="page-link" href="javascript:void(0)" onclick="changePage(' + (currentPage - 1) + ')" aria-label="Previous">';
        tableHtml += '<span aria-hidden="true">&laquo;</span>';
        tableHtml += '</a>';
        tableHtml += '</li>';

        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            tableHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="changePage(1)">1</a></li>';
            if (startPage > 2) {
                tableHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for (var p = startPage; p <= endPage; p++) {
            tableHtml += '<li class="page-item ' + (p === currentPage ? 'active' : '') + '">';
            tableHtml += '<a class="page-link" href="javascript:void(0)" onclick="changePage(' + p + ')">' + p + '</a>';
            tableHtml += '</li>';
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                tableHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            tableHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="changePage(' + totalPages + ')">' + totalPages + '</a></li>';
        }

        // Next button
        tableHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
        tableHtml += '<a class="page-link" href="javascript:void(0)" onclick="changePage(' + (currentPage + 1) + ')" aria-label="Next">';
        tableHtml += '<span aria-hidden="true">&raquo;</span>';
        tableHtml += '</a>';
        tableHtml += '</li>';

        tableHtml += '</ul>';
        tableHtml += '</nav>';
        tableHtml += '</div>';
    }

    $('#response_requested_id').html(tableHtml);

    // Update blank div styling
    $('#blank_div').attr('style', totalRecords == 0 ? 'margin-top:10%' : 'margin-top:10%');

    // Reinitialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * Change page
 */
function changePage(page) {
    var totalPages = Math.ceil(allMedicalsData.length / recordsPerPage);
    if (page < 1 || page > totalPages) return;

    // Show shimmer effect
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $('#response_requested_id').html("");

    // Scroll to top of table
    $('html, body').animate({
        scrollTop: $("#response_requested_id").offset().top - 100
    }, 300);

    // Update page after shimmer delay
    setTimeout(function() {
        currentPage = page;
        renderTable();

        // Hide shimmer
        $('.location-wise-data-loader').attr('style', 'display:none');
    }, 400);
}

/**
 * Capitalize first letter
 */
function capitalizeFirst(str) {
    if (!str || str === 'N/A') return str;
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Toggle filter section visibility
 */
$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

/**
 * Refresh/Reset filters and clear data
 */
function refresh() {
    $('#agency_id').val("");
    $('#medical_due_date').val("");
   
    allMedicalsData = [];
    currentPage = 1;
    showNoRecordTable();
}

/**
 * Show default "No record available" table
 */
function showNoRecordTable() {
    $('#response_requested_id').html(
        '<table id="order-listing1" class="table table-bordered table-width1" style="font-size: 13px;">' +
        '<thead>' +
        '<tr>' +
        '<th>No</th>' +
        '<th>Employee Code</th>' +
        '<th>Employee Name</th>' +
        '<th>DOB</th>' +
        '<th>Gender</th>' +
        '<th>Phone</th>' +
        '<th>Medical Name</th>' +
        '<th>Medical Due Date</th>' +
        '<th>Medical Status</th>' +
        '<th>Employee Status</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '<tr>' +
        '<td colspan="11" class="text-center">No record available</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>'
    );
}

/**
 * Show employee details modal
 */
function showEmployeeDetails(employeeCode, clickedElement) {
    if (!employeeCode || employeeCode === 'N/A') {
        toastr.error('Invalid employee code');
        return;
    }

    // Show loader for specific employee code link
    if (clickedElement) {
        $(clickedElement).find('.employee-code-text').hide();
        $(clickedElement).find('.employee-code-loader').show();
    }

    // Small delay to show loading state
    setTimeout(function() {
        // Hide loader for specific employee code link
        if (clickedElement) {
            $(clickedElement).find('.employee-code-text').show();
            $(clickedElement).find('.employee-code-loader').hide();
        }

        // Set employee code in modal title and hidden field
        $('#modal-employee-code').text(employeeCode);
        $('#third_party_employee_code').val(employeeCode);

        // Clear previous data
        clearModalData();

        // Show loaders in modal
        $('#visiting-demographic-loader').show();
        $('#visiting-demographic-content').hide();

        // Show modal with custom class
        $('#employeeDetailsModal').addClass('show');

        // Reset to first tab
        switchEmployeeTab('demographic');

        // Load demographic data
        loadDemographicData();

        // Load medical data
        loadMedicalData(employeeCode);
    }, 400);
}

/**
 * Switch between tabs in employee modal
 */
function switchEmployeeTab(tabName) {
    // Remove active class from all tabs
    $('.employee-tab-button').removeClass('active').attr('aria-selected', 'false');
    $('.employee-tab-content').removeClass('active');

    // Add active class to selected tab
    $('#' + tabName + '-tab').addClass('active').attr('aria-selected', 'true');
    $('#' + tabName + '-panel').addClass('active');

    // Load data if needed
    if (tabName === 'medical') {
        var employeeCode = $('#third_party_employee_code').val();
        if (employeeCode) {
            loadMedicalData(employeeCode);
        }
    }
}

/**
 * Close employee details modal
 */
function closeEmployeeModal() {
    $('#employeeDetailsModal').removeClass('show');

    // Clear data after modal closes
    setTimeout(function() {
        clearModalData();
    }, 300);
}

/**
 * Clear modal data
 */
function clearModalData() {
    // Reset demographic data container
    $('#visiting-demographic-data').html(
        '<div class="col-12 text-center text-muted py-5">' +
        '<i class="mdi mdi-account-circle" style="font-size: 48px;"></i>' +
        '<p class="mt-2">Loading demographic details...</p>' +
        '</div>'
    );

    // Reset medical table
    $('#medical-table-body').html(
        '<tr>' +
        '<td colspan="5" class="text-center text-muted py-5">' +
        '<i class="mdi mdi-file-document-outline" style="font-size: 48px;"></i>' +
        '<p class="mt-2">No medical records found</p>' +
        '</td>' +
        '</tr>'
    );

    // Hide loaders initially
    $('#visiting-demographic-loader').hide();
    $('#medical-loader').hide();
    $('#visiting-demographic-content').show();
    $('#medical-content').show();
}

/**
 * Load demographic data for employee
 */
function loadDemographicData() {
    $('#visiting-demographic-loader').show();
    $('#visiting-demographic-content').hide();

    try {
        $.ajax({
            async:false,
            global:false,
            type: "POST",
            url: _ADVANCED_SEARCH_THIRD_PARTY,

            data: {
                'employee_code': $('#third_party_employee_code').val(),
                'agency_id': $('#agency_id').val(),
                '_token': _CSRF_TOKEN
            },
            success: function(res) {
                if(res && res.data !="" && res.data.data.employees.length > 0) {
                    // Get the first employee data
                    visitingAidData = res.data.data.employees[0];
                    populateVisitingDemographic(visitingAidData);
                } else {
        
                    $('#visiting-demographic-data').html('<div class="col-12 text-center py-5"><i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 48px;"></i><p class="text-muted mt-2">No demographic data available.</p></div>');
                }
        
                // Hide loader and show content
                $('#visiting-demographic-loader').hide();
                $('#visiting-demographic-content').show();
            },
            error:function(jqr){
                finalResponse = jqr;
            }
        })

    } catch(error) {

        $('#visiting-demographic-data').html('<div class="col-12 text-center py-5"><i class="mdi mdi-close-circle-outline text-danger" style="font-size: 48px;"></i><p class="text-muted mt-2">Failed to load demographic data.</p></div>');
        // Hide loader and show content even on error
        $('#visiting-demographic-loader').hide();
        $('#visiting-demographic-content').show();
    }
}

/**
 * Load medical data for employee
 */
function loadMedicalData(employeeCode) {
    // Show loader
    $('#medical-loader').show();
    $('#medical-content').hide();

    // Simulate loading delay
    setTimeout(function() {
        // Filter medical records for this employee
        var medicalRecords = allMedicalsData.filter(function(record) {
            return record.EmployeeCode === employeeCode;
        });

        // Hide loader, show content
        $('#medical-loader').hide();
        $('#medical-content').show();

        if (medicalRecords.length === 0) {
            $('#medical-table-body').html(
                '<tr>' +
                '<td colspan="5" class="text-center text-muted py-5">' +
                '<i class="mdi mdi-file-document-outline" style="font-size: 48px;"></i>' +
                '<p class="mt-2">No medical records found</p>' +
                '</td>' +
                '</tr>'
            );
            return;
        }

        // Build table rows
        var tableRows = '';
        for (var i = 0; i < medicalRecords.length; i++) {
            var record = medicalRecords[i];
            var rowNumber = i + 1;
            var medicalId = record.MedicalID || 'N/A';
            var medicalName = record.MedicalName || 'N/A';
            var status = record.status || 'pending';
            var date = record.date || 'N/A';

            // Format date if available
            if (date !== 'N/A') {
                try {
                    var dateObj = new Date(date);
                    date = (dateObj.getMonth() + 1) + '/' + dateObj.getDate() + '/' + dateObj.getFullYear();
                } catch (e) {
                    // Keep original date if parsing fails
                }
            }

            // Determine badge class
            var badgeClass = 'badge-info';
            var statusLower = status.toLowerCase();
            if (statusLower === 'completed') {
                badgeClass = 'badge-success';
            } else if (statusLower === 'pending') {
                badgeClass = 'badge-warning';
            } else if (statusLower === 'in_progress' || statusLower === 'in progress') {
                badgeClass = 'badge-info';
            } else if (statusLower === 'cancelled' || statusLower === 'failed') {
                badgeClass = 'badge-danger';
            }

            tableRows += '<tr>';
            tableRows += '<td>' + rowNumber + '</td>';
            tableRows += '<td>' + medicalId + '</td>';
            tableRows += '<td>' + capitalizeFirst(medicalName) + '</td>';
            tableRows += '<td><span class="badge ' + badgeClass + '">' + capitalizeFirst(status) + '</span></td>';
            tableRows += '<td>' + date + '</td>';
            tableRows += '</tr>';
        }

        $('#medical-table-body').html(tableRows);
    }, 500);
}
/**
 * Initialize on page load
 */
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('.location-wise-data-loader').hide();

    // Close modal when clicking outside
    $('#employeeDetailsModal').on('click', function(e) {
        if (e.target.id === 'employeeDetailsModal') {
            closeEmployeeModal();
        }
    });

    // Close modal on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            if ($('#employeeDetailsModal').hasClass('show')) {
                closeEmployeeModal();
            }
        }
    });
});

function populateVisitingDemographic(data) {
    let html = '';

    // Personal Information Section
    html += '<div class="col-md-12 mb-2"><h6 class="text-primary mb-3 border-bottom pb-2"><i class="mdi mdi-account-circle"></i> Personal Information</h6></div>';
    html += createDemographicField('Employee ID', data.employee_id || '-', 4);
    html += createDemographicField('Employee Code', data.employee_code || data.code || '-', 4);
    html += createDemographicField('Full Name', (data.first_name || '') + ' ' + (data.last_name || ''), 4);
    html += createDemographicField('First Name', data.first_name || '-', 4);
    html += createDemographicField('Last Name', data.last_name || '-', 4);
    html += createDemographicField('Date of Birth', data.dob || data.date_of_birth || '-', 4);
    html += createDemographicField(
        'Gender',
        data.gender === 'F' ? 'Female' :
        data.gender === 'M' ? 'Male' : '-',
        4
    );
    html += createDemographicField('SSN', data.ssn ? '***-**-' + data.ssn.slice(-4) : '-', 4);

    // Status badge
    let statusBadge = '<span class="badge badge-success">Active</span>';
    if(data.status && data.status.toLowerCase() === 'inactive') {
        statusBadge = '<span class="badge badge-danger">Inactive</span>';
    } else if(data.status && data.status.toLowerCase() === 'pending') {
        statusBadge = '<span class="badge badge-warning">Pending</span>';
    }
    html += '<div class="col-md-4 mb-3"><label class="font-weight-bold small text-muted">Status:</label><div>' + statusBadge + '</div></div>';

    // Contact Information Section
    html += '<div class="col-md-12 mb-2 mt-3"><h6 class="text-info mb-3 border-bottom pb-2"><i class="mdi mdi-phone"></i> Contact Information</h6></div>';
    html += createDemographicField('Primary Phone', data.phone || data.primary_phone || '-', 6);
    html += createDemographicField('Cell Phone', data.cell || data.cell_phone || '-', 6);

    // Address Information Section
    html += '<div class="col-md-12 mb-2 mt-3"><h6 class="text-secondary mb-3 border-bottom pb-2"><i class="mdi mdi-map-marker"></i> Address Information</h6></div>';
    html += createDemographicField('Street Address', data.address || data.street_address || '-', 12);


    $('#visiting-demographic-data').html(html);
}

function createDemographicField(label, value, colSize = 3) {
    return `
        <div class="col-md-${colSize} col-sm-6 mb-3">
            <div class="form-group mb-0">
                <label class="font-weight-bold small text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">${label}</label>
                <div class="text-dark" style="font-size: 14px; padding: 5px 0;">${value}</div>
            </div>
        </div>
    `;
}

function exportCsv(){
    window.location.href = _EXPORT_CSV+"?agency_id="+$('#agency_id').val()+"&medical_due_date="+$('.medical_due_date').val();
}

async function fetchVisitingMedicalData(employeeCode) {
    // TODO: Replace this with actual API endpoint for medical data
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "GET",
            url: _GET_EMPLOYEE_PARTY_PENDING_MEDICAL, // Replace with actual medical endpoint
            data: {
                'code': employeeCode,
                'agency_id': $('#agency_id').val(),
                
            },
            success: function(res) {
            
                resolve({
                    data: res.data.pendingmedicals || []
                });
            },
            error: function(jqr) {
                reject(jqr);
            }
        });
    });
}