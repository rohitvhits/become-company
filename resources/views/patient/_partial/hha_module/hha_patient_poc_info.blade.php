<style>
    #pocTasksModal .modal-footer {
        padding: 4px 1px !important;
    }

</style>

<div class="d-flex justify-content-between mb-3">
    <p class="card-title mb-0">POC Information</p>
    <button type="button" class="btn btn-primary btn-sm" id="createPOCBtn">
        <i class="fa fa-plus"></i> Create POC
    </button>
</div>

<!-- Patient Info Table -->
<div class="row">
    <div class="col-12 hideShow hide">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>POC ID</th>
                                <th>Start Date</th>
                                <th>Stop Date</th>
                                <th>Created Date</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="poc_patient_info_tbody">
                            <tr>
                                <td colspan="9" class="text-center">No record available</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="patient-poc-pagination" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Days array - generated dynamically using JavaScript instead of hardcoding
const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

/**
 * Generate dynamic table headers for tasks table
 * This creates the header row with fixed columns plus dynamic day columns
 */
function generateTasksTableHeaders() {
    const headerRow = document.getElementById('tasksTableHeader');

    // Fixed columns
    const fixedHeaders = ['Code', 'Category Name', 'Task Name', 'As Needed', 'Weekly Min - Max'];

    let headerHTML = '';

    // Add fixed headers
    fixedHeaders.forEach(header => {
        headerHTML += `<th nowrap>${header}</th>`;
    });

    // Dynamically add day headers using the days array
    daysOfWeek.forEach(day => {
        headerHTML += `<th nowrap>${day}</th>`;
    });

    headerRow.innerHTML = headerHTML;
}

/**
 * Populate tasks table with data
 * @param {Array} tasks - Array of task objects
 */
function populateTasksTable(tasks) {
    const tbody = document.getElementById('tasksTableBody');

    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center">No tasks available</td></tr>';
        return;
    }

    let rowsHTML = '';

    tasks.forEach(task => {
        rowsHTML += '<tr>';

        // Fixed columns
        rowsHTML += `<td>${task.code || '-'}</td>`;
        rowsHTML += `<td>${task.category_name || '-'}</td>`;
        rowsHTML += `<td>${task.task_name || '-'}</td>`;
        rowsHTML += `<td>${task.as_needed || '-'}</td>`;
        rowsHTML += `<td>${task.weekly_min +' - '+task.weekly_max || '-'}</td>`;

        // Dynamically populate day columns
        // Loop through days array and get values from task object
        daysOfWeek.forEach(day => {
            const dayKey = day.toLowerCase(); // Convert to lowercase to match object keys
            rowsHTML += `<td>${task[dayKey] || '-'}</td>`;
        });

        rowsHTML += '</tr>';
    });

    tbody.innerHTML = rowsHTML;
}

/**
 * Show tasks modal with task data
 * @param {Array} tasks - Array of task objects
 */
function showTasksModal(tasks) {
    // Generate headers dynamically
    generateTasksTableHeaders();

    // Populate task rows
    populateTasksTable(tasks);

    // Show the modal (Bootstrap 4 syntax)
    $('#pocTasksModal').modal('show');
}

/**
 * Handle View Tasks button click
 * This function should be called when user clicks "View Tasks" button
 * @param {Object} patientPOC - Patient POC object containing tasks
 */
function viewTasks(patientPOC) {
    showTasksModal([]);
    if (patientPOC && patientPOC.tasks) {
        showTasksModal(patientPOC.tasks);
    }
}

/**
 * Render patient POC page with shimmer and pagination
 * @param {number} page - Page number to render
 */
function renderPatientPOCPage(page) {
    _patientPOCCurrentPage = page;
    var tbody = document.getElementById('poc_patient_info_tbody');
    tbody.innerHTML = getPatientPOCShimmer();
    $('#patient-poc-pagination').html('');

    setTimeout(function() {
        var data = _patientPOCData;
        var totalPages = Math.ceil(data.length / _patientPOCPerPage);
        var start = (page - 1) * _patientPOCPerPage;
        var end = start + _patientPOCPerPage;
        var pageData = data.slice(start, end);

        if (!pageData || pageData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">No record available</td></tr>';
            return;
        }

        var rowsHTML = '';
        pageData.forEach(function(poc, i) {
            var globalIndex = start + i;
            var noteText  = poc.notes || '-';
            var noteLimit = 60;
            var noteHtml;
            if (noteText === '-' || noteText.length <= noteLimit) {
                noteHtml = noteText;
            } else {
                var uid   = 'pocnote_' + globalIndex + '_' + Date.now();
                var short = noteText.substring(0, noteLimit).trim();
                noteHtml =
                    '<span id="short_' + uid + '">' + short + '... ' +
                        '<a href="javascript:void(0)" style="font-size:11px;" onclick="togglePocNoteInfo(\'' + uid + '\')">Read more</a>' +
                    '</span>' +
                    '<span id="full_' + uid + '" style="display:none;">' + noteText + ' ' +
                        '<a href="javascript:void(0)" style="font-size:11px;" onclick="togglePocNoteInfo(\'' + uid + '\')">Read less</a>' +
                    '</span>';
            }

            rowsHTML += '<tr>';
            rowsHTML += '<td>' + (globalIndex + 1) + '</td>';
            rowsHTML += '<td>' + (poc.poc_id || '-') + '</td>';
            rowsHTML += '<td>' + (moment(poc.start_date).format('MM/DD/YYYY') || '-') + '</td>';
            rowsHTML += '<td>' + (moment(poc.stop_date).format('MM/DD/YYYY') || '-') + '</td>';
            rowsHTML += '<td>' + (moment(poc.created_date).format('MM/DD/YYYY hh:mm A') || '-') + '</td>';
            rowsHTML += '<td>' + noteHtml + '</td>';
            rowsHTML += '<td><a href="javascript:void(0)" onclick="viewTasks(pocDataGlobal[' + globalIndex + '])"><i class="fa fa-eye"></i></a></td>';
            rowsHTML += '</tr>';
        });

        tbody.innerHTML = rowsHTML;
        renderPatientPOCPagination(totalPages, page);
    }, 300);
}

/**
 * Render pagination for POC table
 * @param {number} totalPages - Total number of pages
 * @param {number} currentPage - Current active page
 */
function renderPatientPOCPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        $('#patient-poc-pagination').html('');
        return;
    }

    var pagination = '<nav><ul class="pagination justify-content-center mb-0 float-right">';

    pagination += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPOCPage(' + (currentPage - 1) + ')">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        pagination += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPOCPage(' + i + ')">' + i + '</a></li>';
    }

    pagination += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
    pagination += '<a class="page-link" href="javascript:void(0)" onclick="renderPatientPOCPage(' + (currentPage + 1) + ')">Next</a></li>';

    pagination += '</ul></nav>';

    $('#patient-poc-pagination').html(pagination);
}

function togglePocNoteInfo(uid) {
    var $short = $('#short_' + uid);
    var $full  = $('#full_'  + uid);
    if ($short.is(':visible')) {
        $short.hide();
        $full.show();
    } else {
        $full.hide();
        $short.show();
    }
}

</script>


