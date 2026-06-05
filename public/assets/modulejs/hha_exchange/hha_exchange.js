/**
     * Open Caregiver View Modal
     * @param {string|number} caregiverId - The ID of the caregiver to view
     * @param {string} caregiverName - The name of the caregiver (optional, for display)
     *
     * Usage: Add this to your table row action buttons:
     * <button onclick="openCaregiverModal(123, 'John Doe')" class="btn btn-sm btn-info">
     *     <i class="mdi mdi-eye"></i> View
     * </button>
*/
    
let NOTES_PER_PAGE = 10;
let currentNotesPage = 1;
let allNotesData = [];

let INSERVICE_PER_PAGE = 10;
let currentInServicePage = 1;
let allInServiceData = [];

let HHA_OTHER_COMPLIANCE_PER_PAGE = 10;
let currentHHAOtherCompliancePage = 1;
let allHHAOtherComplianceData = [];

function openCaregiverModal(agencyFk,caregiverId, caregiverName) {
    if (!caregiverId) {
        toastr.error('No caregiver ID set');
        return false;
    }
    currentCaregiversId = caregiverId;
    agencyId = agencyFk;
    loadedTabs = {}; // Reset loaded tabs

    // Set caregiver name in modal title
    $('#caregiverName').text(caregiverName || 'Loading...');

    // Show modal
    $('#caregiverViewModal').addClass('show');
    $('body').css('overflow', 'hidden'); // Prevent background scrolling

    // Load demographic details (first tab) immediately
    loadCaregiverTab('demographic');

    // Set focus to close button for accessibility
    $('.caregiver-modal-close').focus();
}

 /**
         * Close Caregiver View Modal
         */
 function closeCaregiverModal() {
    $('#caregiverViewModal').removeClass('show');
    $('body').css('overflow', ''); // Restore scrolling
    currentCaregiversId = null;
    agencyId = null;
    // Reset to first tab
    $('.caregiver-tab-button').removeClass('active');
    $('.caregiver-tab-button').first().addClass('active');
    $('.caregiver-tab-content').removeClass('active');
    $('.caregiver-tab-content').first().addClass('active');
}

function loadCaregiverTab(tabName) {
    if (!currentCaregiversId) {
        toastr.error('No caregiver ID set');
        return;
    }

    var contentElementId = tabName + 'Content';

    // Show shimmer loading effect
    $('#' + contentElementId).html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Make AJAX call to load tab data
    // TODO: Replace with your actual API endpoint
    loadedTabs[tabName] = true;
    if(tabName !="calender"){
        let tabParams = {
            caregiver_id: currentCaregiversId,
            agency_id: agencyId
        };
    
        if(tabName =='notes'){
            tabParams.date = $('#notesDateRangePicker').val();
        }
        if(tabName =='medical'){
            tabParams.status = $('#hha_status_medical_id').val();
        }
        $.ajax({
    
            url: _HHA_CAREGIVER_DETAIL_URL + '/' + tabName,
            type: 'GET',
            data: tabParams,
            success: function(response) {
                // Mark tab as loaded
                loadedTabs[tabName] = true;
    
                // Check if response is valid
                if (response && response.data) {
                    // Render the content based on tab type
                    renderTabContent(tabName, response.data);
                } else {
                    $('#' + contentElementId).html(`
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i> ${response.message || 'No data available'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                showErrorAndLoginRedirection(xhr)
                $('#' + contentElementId).html(`
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert"></i> Error loading data. Please try again.
                    </div>
                `);
            }
        });
    }
}

/**
 * Render content for each tab
 * @param {string} tabName - The name of the tab
 * @param {object} data - The data returned from the API
 *
 * Customize this function based on your data structure
 */
function renderTabContent(tabName, data,page=1) {
    var contentElementId = tabName + 'Content';
    var html = '';

    switch(tabName) {
        case 'demographic':
            html = renderDemographicContent(data);
            break;
        case 'calendar':
            html = renderCalendarContent(data);
            break;
        case 'availability':
            html = renderAvailabilityContent(data);
            break;
        case 'notes':
            // Store original notes data for filtering
            originalNotesData = data;
            html = renderNotesContent(data,page);
            // Initialize date range picker after rendering
            initializeNotesDateRangePicker();
            break;
        case 'inservice':
            html = renderInServiceContent(data,page);
            break;
        case 'medical':
            html = renderMedicalContent(data);
            break;
        case 'compliance':
            html = renderComplianceContent(data,page);
            break;
        case 'document':
            html = renderDocumentContent(data);
            break;
        case 'preferences':
            html = renderPreferencesContent(data);
            break;
        default:
            html = '<p>Content not available</p>';
    }

    $('#' + contentElementId).html(html);
}

// ============================================
// TAB CONTENT RENDERING FUNCTIONS
// Customize these based on your data structure
// ============================================

function renderDemographicContent(data) {
    // Determine status badge color
    let statusBadge = 'secondary';
    if (data.status === 'Active') {
        statusBadge = 'success';
    } else if (data.status === 'Inactive') {
        statusBadge = 'warning';
    } else if (data.status === 'Terminated') {
        statusBadge = 'danger';
    }

    return `
        <!-- Main Header -->
        <div class="row">
            <!-- Personal Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                        <h6 class="mb-0"><i class="mdi mdi-account-circle text-primary"></i> Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Full Name:</div>
                            <div class="col-7">${data.firstName || ''} ${data.middleName || ''} ${data.lastName || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Caregiver Code:</div>
                            <div class="col-7"><span class="badge badge-info">${data.office_code ? data.office_code + ' - ' : ''}${data.caregiverCode || 'N/A'}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Date of Birth:</div>
                            <div class="col-7">${data.dob || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Gender:</div>
                            <div class="col-7">${data.gender || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">SSN:</div>
                            <div class="col-7">${data.ssn || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #17a2b8;">
                        <h6 class="mb-0"><i class="mdi mdi-phone text-info"></i> Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Notification Email:</div>
                            <div class="col-7">${data.notificationEmail || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Home Phone:</div>
                            <div class="col-7">${data.phone || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Notification Mobile:</div>
                            <div class="col-7">${data.notificationMobile || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Phone2:</div>
                            <div class="col-7">${data.phone2 || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Address:</div>
                            <div class="col-7">${data.address || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">City:</div>
                            <div class="col-7">${data.city || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">State/Zip:</div>
                            <div class="col-7">${data.state || 'N/A'} ${data.zip || ''}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #28a745;">
                        <h6 class="mb-0"><i class="mdi mdi-briefcase text-success"></i> Employment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Status:</div>
                            <div class="col-7"><span class="badge badge-${statusBadge}">${data.status || 'N/A'}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Branch Name:</div>
                            <div class="col-7">${data.branch || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Office Name:</div>
                            <div class="col-7">${data.office_name || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Coordinator Name:</div>
                            <div class="col-7">${data.coordinatorName || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Team Name:</div>
                            <div class="col-7">${data.teamName || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Application Date:</div>
                            <div class="col-7">${data.applicationDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Hire Date:</div>
                            <div class="col-7">${data.hireDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">First Work Date:</div>
                            <div class="col-7">${data.firstWorkDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Last Work Date:</div>
                            <div class="col-7">${data.lastWorkDate || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Employee Type:</div>
                            <div class="col-7">${data.employment_type || 'N/A'}</div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Professional Information Card -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #ffc107;">
                        <h6 class="mb-0"><i class="mdi mdi-school text-warning"></i> Professional Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Discipline:</div>
                            <div class="col-7">${data.EmploymentTypesDiscipline || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Location:</div>
                            <div class="col-7">${data.location || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 1:</div>
                            <div class="col-7">${data.lang || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 2:</div>
                            <div class="col-7">${data.lang2 || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Language 3:</div>
                            <div class="col-7">${data.lang3 || 'N/A'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold text-muted">Employment Type:</div>
                            <div class="col-7">${data.empType || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Card -->
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dc3545;">
                        <h6 class="mb-0"><i class="mdi mdi-phone-alert text-danger"></i> Emergency Contact</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Contact Name:</div>
                                    <div class="col-7">${data.emergencyName || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Phone:</div>
                                    <div class="col-7">${data.emergencyPhone1 || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-5 font-weight-bold text-muted">Relationship:</div>
                                    <div class="col-7">${data.emergencyRelationShip || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderAvailabilityContent(data) {
    let availabilityHtml = '';

    if (data.availability && Array.isArray(data.availability) && data.availability.length > 0) {
        availabilityHtml = `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sunday</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
var cnt =1;
        data.availability.forEach(function(avail) {
            var Sunday = "";
            var Monday = "";
            var Tuesday = "";
            var Wednesday = "";
            var Thursday = "";
            var Friday = "";
            var Saturday = "";

            avail.sunday_from = (avail.sunday_from) == null ? '' : avail.sunday_from;
            avail. sunday_to = (avail.sunday_to) == null ? '' : avail.sunday_to;
            avail.sunday_live_in = (avail.sunday_live_in) == null ? '' : avail.sunday_live_in;

            avail.monday_from = (avail.monday_from) == null ? '' : avail.monday_from;
            avail.monday_to = (avail.monday_to) == null ? '' : avail.monday_to;
            avail.monday_live_in = (avail.monday_live_in) == null ? '' : avail.monday_live_in;

            avail.tuesday_from = (avail.tuesday_from) == null ? '' : avail.tuesday_from;
            avail.tuesday_to = (avail.tuesday_to) == null ? '' : avail.tuesday_to;
            avail.tuesday_live_in = (avail.tuesday_live_in) == null ? '' : avail.tuesday_live_in;

            avail.wednesday_from = (avail.wednesday_from) == null ? '' : avail.wednesday_from;
            avail.wednesday_to = (avail.wednesday_to) == null ? '' : avail.wednesday_to;
            avail.wednesday_live_in = (avail.wednesday_live_in) == null ? '' : avail.wednesday_live_in;

            avail.thursday_from = (avail.thursday_from) == null ? '' : avail.thursday_from;
            avail.thursday_to = (avail.thursday_to) == null ? '' : avail.thursday_to;
            avail.thursday_live_in = (avail.thursday_live_in) == null ? '' : avail.thursday_live_in;

            avail.friday_from = (avail.friday_from) == null ? '' : avail.friday_from;
            avail.friday_to = (avail.friday_to) == null ? '' : avail.friday_to;
            avail.friday_live_in = (avail.friday_live_in) == null ? '' : avail.friday_live_in;

            avail.saturday_from = (avail.saturday_from) == null ? '' : avail.saturday_from;
            avail.saturday_to = (avail.saturday_to) == null ? '' : avail.saturday_to;
            avail.saturday_live_in = (avail.saturday_live_in) == null ? '' : avail.saturday_live_in;

            availabilityHtml += `<tr>
            <td># ${cnt++}</td>
            <td>${avail.sunday_from} - ${avail.sunday_to}</td>
            <td>${avail.monday_from} - ${avail.monday_to}</td>
            <td>${avail.tuesday_from} - ${avail.tuesday_to}</td>
            <td>${avail.wednesday_from} - ${avail.wednesday_to}</td>
            <td>${avail.thursday_from} - ${avail.thursday_to}</td>
            <td>${avail.friday_from} - ${avail.friday_to}</td>
            <td>${avail.saturday_from} - ${avail.saturday_to}</td>
            </tr>
            <tr>
            <td>Live In</td>
            <td>${avail.sunday_live_in} </td>
            <td>${avail.monday_live_in}</td>
            <td>${avail.tuesday_live_in}</td>
            <td>${avail.wednesday_live_in}</td>
            <td>${avail.thursday_live_in}</td>
            <td>${avail.friday_live_in}</td>
            <td>${avail.saturday_live_in}</td>
            </tr>
            `;
        });

        availabilityHtml += `
                    </tbody>
                </table>
            </div>
        `;
    } else {
        availabilityHtml = `<div class="card">
                <div class="card-body text-center py-4">
                    <i class="mdi mdi-note-text-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No Weekly Availability available</p>
                </div>
            </div>`;
    }

    return `
        <div class="info-group">
          
            ${availabilityHtml}
        </div>
    `;
}

function renderNotesContent(data, page = 1) {
    let notesHtml = '';

    if (data.notes && Array.isArray(data.notes) && data.notes.length > 0) {
        // Sort notes by created_date (newest first)
        let sortedNotes = data.notes;
        let totalNotes = sortedNotes.length;
        let totalPages = Math.ceil(totalNotes / NOTES_PER_PAGE);
        let startIndex = (page - 1) * NOTES_PER_PAGE;
        let endIndex = startIndex + NOTES_PER_PAGE;
        let paginatedNotes = sortedNotes.slice(startIndex, endIndex);
        
        allNotesData = data.notes;
        currentNotesPage = page;
        let sortedNotess = paginatedNotes.sort(function(a, b) {
            let dateA = new Date(a.created_date || 0);
            let dateB = new Date(b.created_date || 0);
            return dateB - dateA;
        });
        // Build note cards based on NOTES_UI_DESIGN.md structure
        let noteCards = '';
        sortedNotess.forEach(function(note, index) {
            // Get note fields from JSON structure
            let caregiverNoteId = note.CaregiverNoteID || note.caregiver_note_id || '';
            let caregiverId = note.CaregiverID || note.caregiver_id || '';
            let noteDate = note.NoteDate || note.note_date || note.created_date || 'N/A';
            let noteContent = note.Note || note.note || 'No content';
            let createdDate = note.created_date || note.NoteDate || 'N/A';

            // Analyze note content for categorization
            let categoryColor = 'primary';
            let categoryIcon = 'mdi-note-text';
            let noteContentLower = noteContent.toLowerCase();
            // Format dates
            let formattedNoteDate = 'N/A';
            let timeAgo = '';
            if (createdDate !== 'N/A') {
                formattedNoteDate = moment(createdDate).format('MMM DD, YYYY h:mm A');
                timeAgo = moment(createdDate).fromNow();
            }

            // Truncate long notes for preview
            let notePreview = noteContent;
            let isLongNote = noteContent.length > 200;
            if (isLongNote) {
                notePreview = noteContent.substring(0, 200) + '...';
            }

            noteCards += `
                <div class="card mb-2 note-card" style="border-left: 3px solid primary">
                    <div class="card-body" style="padding: 12px;">
                        <div class="row">
                            <div class="col-auto">
                                <div class="bg-${categoryColor} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="mdi ${categoryIcon}" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">#${caregiverNoteId}</h6>
                                        
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline"></i> ${timeAgo}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-1" style="line-height: 1.5; font-size: 0.9rem;" id="note-preview-${caregiverNoteId}">
                                    ${notePreview}
                                    ${isLongNote ? ' <a href="javascript:void(0);" onclick="toggleNoteContent(\'' + caregiverNoteId + '\')" class="text-primary">more</a>' : ''}
                                </p>
                                <div style="display: none;" id="note-full-${caregiverNoteId}">
                                    <p class="mb-1" style="line-height: 1.5; font-size: 0.9rem;">
                                        ${noteContent}
                                        ${isLongNote ? ' <a href="javascript:void(0);" onclick="toggleNoteContent(\'' + caregiverNoteId + '\')" class="text-primary">less</a>' : ''}
                                    </p>
                                </div>
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar"></i> ${noteDate}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        notesHtml = `
            <!-- Notes List -->
            <div class="row">
                <div class="col-12">
                    ${noteCards}
                    ${renderNotesPagination(totalPages)}
                </div>
            </div>
        `;
    } else {
        notesHtml = `
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="mdi mdi-note-text-outline" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2 mb-0">No notes available</p>
                </div>
            </div>
        `;
    }

    return notesHtml;
}

/**
 * Toggle note content between preview and full view
 */
function toggleNoteContent(caregiverNoteId) {
    var previewDiv = $('#note-preview-' + caregiverNoteId);
    var fullDiv = $('#note-full-' + caregiverNoteId);

    if (fullDiv.is(':visible')) {
        // Currently showing full, switch to preview
        fullDiv.hide();
        previewDiv.show();
    } else {
        // Currently showing preview, switch to full
        previewDiv.hide();
        fullDiv.show();
    }
}

/**
 * Initialize notes date range picker
 */
function initializeNotesDateRangePicker() {
    setTimeout(function() {
        if ($('#notesDateRangePicker').length) {
            $('#notesDateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months').startOf('month'), moment().endOf('month')]
                },
                opens: 'left'
            });

            $('#notesDateRangePicker').off('apply.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                filterNotesByDateRange(picker.startDate, picker.endDate);
            });

            $('#notesDateRangePicker').off('cancel.daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
                clearNotesDateFilter();
            });
        }
    }, 200);
}

function renderInServiceContent(data,page) {
    let inserviceHtml = '';
    var services = data.inservices;

    if (Array.isArray(services) && services.length > 0) {
        // Sort by date (newest first)
        allInServiceData = services;
        currentInServicePage = page;

        let sortedInservices = allInServiceData.sort(function(a, b) {
            let dateA = new Date(a.inservice_date || 0);
            let dateB = new Date(b.inservice_date || 0);
            return dateB - dateA;
        });

        // Build inservice cards
        let totalRecords = sortedInservices.length;
        let totalPages = Math.ceil(totalRecords / INSERVICE_PER_PAGE);
        let startIndex = (page - 1) * INSERVICE_PER_PAGE;
        let endIndex = startIndex + INSERVICE_PER_PAGE;
        let paginatedData = sortedInservices.slice(startIndex, endIndex);

        let inserviceCards = '';
        let cnt = startIndex + 1;
        paginatedData.forEach(function(inservice, index) {
            // Get inservice fields
            let topicName = inservice.topic_name || 'Untitled Training';
            let description = inservice.description || 'No description available';
            let inserviceDate = inservice.inservice_date || 'N/A';
            let fromTime = inservice.from_time || inservice.start_time || 'N/A';
            let endTime = inservice.end_time || 'N/A';

            // Format date
            let formattedDate = 'N/A';
            let timeAgo = '';
            if (inserviceDate !== 'N/A') {
                formattedDate = moment(inserviceDate).format('MMM DD, YYYY');
                timeAgo = moment(inserviceDate).fromNow();
            }

            // Truncate description for preview
            let descriptionPreview = description;
            let isLongDescription = description.length > 50;
            if (isLongDescription) {
                descriptionPreview = description.substring(0, 50) + '...';
            }

            // Generate unique ID for this inservice
            let inserviceId = 'inservice_' + index;
            inserviceCards += `<tr><span id="${inserviceId}" style="display:none">${description}</span>
                    <td>${cnt++}</td>
                    <td>${topicName}</td>
                    <td>${inserviceDate}</td>
                    <td>${fromTime}</td>
                    <td>${endTime}</td>
                    <td><a onclick="showInServiceDescription(${index})">${descriptionPreview}</a></td>
                </tr>
            `;
        });

        inserviceHtml = `
            <!-- InService Cards -->
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered dataTable no-footer">
                        <thead style="background-color:#f5f5f5;color:#333333">
                            <tr>
                            <th >No</th>
                            <th>Topic Name</th>
                            <th>InService Date</th>
                            <th>From Time</th>
                            <th>End Time</th>
                            <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${inserviceCards}
                        </tbody>
                    </table>
                    ${renderInServicePagination(totalPages)}
                </div>
            </div>
        `;
    } else {
        inserviceHtml = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-school-outline" style="font-size: 4rem; color: #ddd;"></i>
                    <p class="text-muted mt-3 mb-0">No inservice training records found for this caregiver.</p>
                </div>
            </div>
        `;
    }

    return inserviceHtml;
}

function renderMedicalContent(data) {
    var medicalResponse = data.medicals;

    let medicalHtml = '';
    if (Array.isArray(medicalResponse) && medicalResponse.length > 0) {
        let medicalCards = '';
        var cnt =1;
        medicalResponse.forEach(function(medical, index) {
            var medicalStatus ="";
            if(medical.status =='Completed'){
                medicalStatus ="<span class='badge badge-success'>Completed</span>";
            }else if(medical.status =='Overdue'){
                medicalStatus ="<span class='badge badge-danger'>Overdue</span>";
            }else{
                medicalStatus ="<span class='badge badge-warning'>Pending</span>";
            }

            var dueDate = (medical.due_date !="")?moment(medical.due_date).format('MM/DD/YYYY'):"N/A";
            var date_perform = (medical.date_perform !="")?moment(medical.date_perform).format('MM/DD/YYYY'):"N/A";
            medicalCards += `<tr>
                        <td >${cnt++}</td>
                        <td>${medical.medical_name}</td>
                        <td>${medicalStatus}</td>
                        <td>${dueDate}</td>
                        <td>${date_perform}</td>
                        <td>${medical.result}</td>
                    </tr>
                `;
        });

        medicalHtml = `
                <!-- InService Cards -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered dataTable no-footer">
                            <thead style="background-color:#f5f5f5;color:#333333">
                                <tr>
                                <th>No</th>
                                <th>Medical Name</th>
                                <th>Status</th>
                                <th>Medical Due Date</th>
                                <th>Date Perform</th>
                                <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${medicalCards}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
    }else{
        medicalHtml = `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-medical-bag" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 mb-0">No Medical found for this caregiver.</p>
                    </div>
                </div>
            `;
    }
    return medicalHtml;
}

let getAllOtherComplianceResponse = [];
function renderComplianceContent(data,page) {
    let otherComplianceDetailsResponse = data.compliance;
    let otherComplianceHtml = '';
    if (Array.isArray(otherComplianceDetailsResponse) && otherComplianceDetailsResponse.length > 0) {
        allHHAOtherComplianceData = otherComplianceDetailsResponse;
        let sortedOtherCompliance = allHHAOtherComplianceData.sort(function(a, b) {
            let dateA = new Date(a.modifiedDate || 0);
            let dateB = new Date(b.modifiedDate || 0);
            return dateB - dateA;
        });
        currentHHAOtherCompliancePage  = page;

        let otherComplianceCards = '';
        getAllOtherComplianceResponse = otherComplianceDetailsResponse;

        let totalRecords = otherComplianceDetailsResponse.length;
        let totalPages = Math.ceil(totalRecords / HHA_OTHER_COMPLIANCE_PER_PAGE);
        let startIndex = (page - 1) * HHA_OTHER_COMPLIANCE_PER_PAGE;
        let endIndex = startIndex + HHA_OTHER_COMPLIANCE_PER_PAGE;
        let paginatedData = sortedOtherCompliance.slice(startIndex, endIndex);
       
        let cnt = startIndex + 1;

        paginatedData.forEach(function(medical, index) {
      
            let medicalStatus ="";
            if(medical.status =='Completed'){
                medicalStatus ="<span class='badge badge-success'>Completed</span>";
            }else if(medical.status =='Overdue'){
                medicalStatus ="<span class='badge badge-danger'>Overdue</span>";
            }else{
                medicalStatus ="<span class='badge badge-warning'>Pending</span>";
            }

            let dueDate = (medical.due_date !="")?moment(medical.due_date).format('MM/DD/YYYY'):"N/A";
            let date_perform = (medical.date_perform !="")?moment(medical.date_perform).format('MM/DD/YYYY'):"N/A";
            let modifiedDate = (medical.modifiedDate !="")?moment(medical.modifiedDate).format('MM/DD/YYYY'):"N/A";
            let action = "";
            if(medical.status !="Completed"){
                action = '<a href="javascript:void(0)" data-toggle="modal" data-target="#editOtherComplianceModal" onclick="editOtherCompliance('+medical.caregiver_medical_id+')"><i class="fa fa-edit"></i></a>';
            }

            let medical_notes = medical.notes && medical.notes.length > 50 
    ? medical.notes.slice(0, 50) + '...' 
    : (medical.notes || '');

            otherComplianceCards += `<tr>
                        <td style="white-space:nowrap">${cnt++}</td>
                        <td style="white-space:nowrap">${medical.medical_id}</td>
                        <td style="white-space:nowrap">${medical.medical_name}</td>
                        <td style="white-space:nowrap">${medicalStatus}</td>
                        <td style="white-space:nowrap">${medical.result }</td>
                        <td style="white-space:nowrap" class="show-notes-other-compliance" data-notes="${medical.notes}">${medical_notes}</td>
                        
                        <td style="white-space:nowrap">${dueDate}</td>
                        <td style="white-space:nowrap">${date_perform}</td>
                        <td style="white-space:nowrap">${modifiedDate}</td>
                        <td style="white-space:nowrap">${action}
                        </td>
                    </tr>
                `;
        });

        otherComplianceHtml = `
                <!-- InService Cards -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered dataTable no-footer table-responsive">
                            <thead style="background-color:#f5f5f5;color:#333333">
                                <tr>
                                <th style="white-space:nowrap">No</th>
                                <th style="white-space:nowrap">Medical Id</th>
                                <th style="white-space:nowrap">Medical Name</th>
                                <th style="white-space:nowrap">Status</th>
                                <th style="white-space:nowrap">	Result</th>
                                <th style="white-space:nowrap">Notes</th>
                                <th style="white-space:nowrap">Due Date</th>
                                <th style="white-space:nowrap">Date Performed</th>
                                <th style="white-space:nowrap">Modified Date</th>
                                <th style="white-space:nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${otherComplianceCards}
                            </tbody>
                        </table>
                        ${renderOtherCompliancePagination(totalPages)}
                    </div>
                </div>
            `;
    }else{
        otherComplianceHtml = `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-file-check" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 mb-0">No Medical found for this caregiver.</p>
                    </div>
                </div>
            `;
    }
    return otherComplianceHtml;
}

function renderDocumentContent(data) {
    var hhaDocumentResponse = data.documents;
    let hhaDocumentResponseHtml = '';
    if (Array.isArray(hhaDocumentResponse) && hhaDocumentResponse.length > 0) {
        let hhaDocCards = '';
        var cnt =1;
        hhaDocumentResponse.forEach(function(doc, index) {
            var CreatedOn = (doc.CreatedOn !="")?moment(doc.CreatedOn).format('MM/DD/YYYY h:mm A'):"N/A";
            var CreatedBy = (doc.CreatedBy !="")?doc.CreatedBy:"N/A";
            hhaDocCards += `<tr id="tr_${doc.caregiverDocId}">
                        <td>${cnt++}</td>
                        <td>${doc.caregiverDocId}</td>
                        <td>${doc.caregiverDocumentType}</td>
                        <td>${doc.description}</td>
                        <td>${doc.fileName}</td>
                        <td>${CreatedOn}<br>${CreatedBy}</td>
                        <td id="td_${doc.caregiverDocId}"><a id="document_${doc.caregiverDocId}" onclick="getDowloadCaregiverDocument(${doc.caregiverDocId});"><i class="fa fa-download"></i></a></td>
                    </tr>
                `;
        });

        hhaDocumentResponseHtml = `
                <!-- InService Cards -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered dataTable no-footer">
                            <thead style="background-color:#f5f5f5;color:#333333">
                                <tr>
                                <th>No</th>
                                <th>Doc Id</th>
                                <th>Document Type</th>
                                <th>Description</th>
                                <th>File Name</th>
                                <th>Created On / Created By</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${hhaDocCards}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
    }else{
        hhaDocumentResponseHtml = `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-file-document" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 mb-0">No Document found for this caregiver.</p>
                    </div>
                </div>
            `;
    }
    return hhaDocumentResponseHtml;
   
}

function renderPreferencesContent(data) {
    var preferferenceData = data.preferences.preferenceInfo;
    let hhapreferferenceHtml = '';
    if (Array.isArray(preferferenceData) && preferferenceData.length > 0) {
        let hhaPreferferenceCards = '';
        var cnt =1;
        preferferenceData.forEach(function(prf, index) {
            
            hhaPreferferenceCards += `<tr>
                        <td style="white-space:nowrap">${cnt++}</td>
                        <td style="white-space:nowrap">${prf.preferenceID}</td>
                        <td style="white-space:nowrap">${prf.preferenceName}</td>
                        <td style="white-space:nowrap">${prf.preferenceValue}</td>
                        <td style="white-space:nowrap">${prf.PreferenceType}</td>
                    </tr>
                    
                `;
        });

        hhapreferferenceHtml = `
                <!-- InService Cards -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered dataTable no-footer">
                            <thead style="background-color:#f5f5f5;color:#333333">
                                <tr>
                                <th style="white-space:nowrap">No</th>
                                <th style="white-space:nowrap">Preference ID</th>
                                <th style="white-space:nowrap">Preference Name</th>
                                <th style="white-space:nowrap">	Preference Value</th>
                                <th style="white-space:nowrap">	Preference Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${hhaPreferferenceCards}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
    }else{
        hhapreferferenceHtml = `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-file-document" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 mb-0">No Preferences found for this caregiver.</p>
                    </div>
                </div>
            `;
    }
    return hhapreferferenceHtml;
}

// ============================================
// KEYBOARD ACCESSIBILITY
// ============================================

// Close modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#caregiverViewModal').hasClass('show')) {
        closeCaregiverModal();
    }
});

// Close modal when clicking outside
$(document).on('click', '#caregiverViewModal', function(e) {
    if (e.target.id === 'caregiverViewModal') {
        closeCaregiverModal();
    }
});

// Focus trap within modal (cycle through focusable elements)
$('#caregiverViewModal').on('keydown', function(e) {
    if (e.key === 'Tab') {
        var focusableElements = $('#caregiverViewModal').find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        var firstElement = focusableElements.first();
        var lastElement = focusableElements.last();

        if (e.shiftKey) { // Shift + Tab
            if (document.activeElement === firstElement[0]) {
                e.preventDefault();
                lastElement.focus();
            }
        } else { // Tab
            if (document.activeElement === lastElement[0]) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
});

$(function() {
    reinitDateRangePicker();
    // Attach individual click event handlers for each tab
    attachTabClickEvents();
});

/**
 * Attach individual click event handlers for all tabs
 */
function attachTabClickEvents() {
    // Demographic Tab
    $('#demographic-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadDemographicTab();
    });

    // Calendar Tab
    $('#calendar-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadCalendarTab();
    });

    // Availability Tab
    $('#availability-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadAvailabilityTab();
    });

    // Notes Tab
    $('#notes-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadNotesTab();
    });

    // InService Tab
    $('#inservice-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadInServiceTab();
    });

    // Medical Tab
    $('#medical-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadMedicalTab();
    });

    // Compliance Tab
    $('#compliance-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadComplianceTab();
    });

    // Document Tab
    $('#document-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadDocumentTab();
    });

    // Preferences Tab
    $('#preferences-tab').off('click').on('click', function(e) {
        e.preventDefault();
        loadPreferencesTab();
    });
}

/**
 * Individual tab loading functions
 */
function loadDemographicTab() {
    activateTab('demographic', $('#demographic-tab'));
}

function loadCalendarTab() {
    activateTab('calendar', $('#calendar-tab'));
}

function loadAvailabilityTab() {
    activateTab('availability', $('#availability-tab'));
}

function loadNotesTab() {
    activateTab('notes', $('#notes-tab'));
}

function loadInServiceTab() {
    activateTab('inservice', $('#inservice-tab'));
}

function loadMedicalTab() {
    activateTab('medical', $('#medical-tab'));
}

function loadComplianceTab() {
    activateTab('compliance', $('#compliance-tab'));
}

function loadDocumentTab() {
    activateTab('document', $('#document-tab'));
}

function loadPreferencesTab() {
    activateTab('preferences', $('#preferences-tab'));
}

/**
 * Activate a specific tab
 */
function activateTab(tabName, $tabButton) {
    // Remove active class from all tabs
    $('.caregiver-tab-button').removeClass('active');
    $('.caregiver-tab-button').attr('aria-selected', 'false');

    // Add active class to clicked tab
    $tabButton.addClass('active');
    $tabButton.attr('aria-selected', 'true');

    // Hide all tab content
    $('.caregiver-tab-content').removeClass('active');

    // Show selected tab content
    $('#' + tabName + '-panel').addClass('active');

    // Load tab data if not already loaded
    loadCaregiverTab(tabName);
}

function reinitDateRangePicker(){
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function(chosen_date, end_date) {
        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
    })

    $('.datepickernn_sync').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select HHA Sync DateTime': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
        }
    }, function(chosen_date, end_date) {
        $('.datepickernn_sync').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
    })
}

function refresh(){
    $('#search-form')[0].reset();
    reinitDateRangePicker();
}

function clearNotesDateFilter(){
    $('#notesDateRangePicker').val("");
    loadCaregiverTab('notes')
}

function renderCalendarContent(data = []){
    setTimeout(() => {
        $('#caregiverFullCalendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay,listWeek,print'
            },
            aspectRatio: 1.5,
            eventLimit: true,
            dayMaxEvents: 3,
            defaultView: 'month',
            navLinks: true,
            editable: true,
            events: function(start, end, timezone, callback) {

                var startDate = moment(start).format("YYYY-MM-DD");
                var endDate = moment(end).format("YYYY-MM-DD");

                $('#loadertag12').show();

                var id = currentCaregiversId;
                var type = 'Caregiver';
                
                var url = _HHA_CALENDER_LIST+"?caregiver_id="+currentCaregiversId+"&agency_id="+agencyId;

                if (id !== "") {
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: {
                            start: startDate,
                            end: endDate,
                        },
                        success: function (res) {
                            var doc = res.data.visits;
                            $('#loadertag12').hide();
                            callback(doc);
                        }
                    });
                }
            },

            eventRender: function (event, eventElement) {
                eventElement.find(".fc-time").remove();
                eventElement.find(".fc-title").append("<br/><b>" + event.label + "</b>");
            },

        });
    }, 200);
    return `<div class="card">
                            <div class="card-body">
                                <!-- Calendar Legend -->
                                <div class="calendar-legend">
                                    <div class="calendar-legend-item">
                                        <div class="calendar-legend-color" style="background-color: #007bff;"></div>
                                        <span>Scheduled</span>
                                    </div>
                                    <div class="calendar-legend-item">
                                        <div class="calendar-legend-color" style="background-color: #28a745;"></div>
                                        <span>Completed</span>
                                    </div>
                                    <div class="calendar-legend-item">
                                        <div class="calendar-legend-color" style="background-color: #ffc107;"></div>
                                        <span>Pending</span>
                                    </div>
                                    <div class="calendar-legend-item">
                                        <div class="calendar-legend-color" style="background-color: #dc3545;"></div>
                                        <span>Cancelled</span>
                                    </div>
                                    <div class="calendar-legend-item">
                                        <div class="calendar-legend-color" style="background-color: #e83e8c;"></div>
                                        <span>Missed</span>
                                    </div>
                                </div>

                                <!-- FullCalendar Container -->
                                <div id="caregiverFullCalendar"></div>
                            </div>
                        </div>`
}

function showInServiceDescription(index){
    var notes = $('#inservice_'+index).html();
  
    $.confirm({
            title: 'View InService Description',
            columnClass: 'col-md-6',
            content:'<div style="white-space:pre-line">'+notes+'</div>',
            type: 'blue',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    action: function () {}
                }
            }
    });
}

function renderNotesPagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `<nav class="mt-3"><ul class="hha_caregiver_notes pagination justify-content-center">`;

    html += `
        <li class="page-item ${currentNotesPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changeNotesPage(${currentNotesPage - 1})">Prev</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === currentNotesPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changeNotesPage(${i})">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${currentNotesPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changeNotesPage(${currentNotesPage + 1})">Next</a>
        </li>
    `;

    html += `</ul></nav>`;
    return html;
}

function changeNotesPage(page) {
 
    $('#notesContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    setTimeout(()=>{
        renderTabContent('notes', { notes: allNotesData },page);
    },1000)
   
}

function getDowloadCaregiverDocument(docId){
    $('#tr_'+docId).addClass('shimmer');
    $('#tr_'+docId).css('pointer-events', 'none')
   
    $.ajax({
        url: _HHA_CAREGIVER_DOWNLOAD_DOCUMENT,
        type: "GET",
        contentType: false,
        data:{
            'id': currentCaregiversId,
            'docid': docId
        },
        success: function(response) {
            var response = response.data;
            if(response.length !=0){
                $("#td_"+docId).html('');
                
                var base64String = response.streamData;
                // Use a generic MIME type if unknown
                var fileType = "application/octet-stream"; // This works for binary data of any type
                var fileName = response.fileName; // Set a generic filename without an extension
                var base64String = "data:" + fileType + ";base64," + base64String; // Replace with actual Base64 data    
                // Set the href and download attributes on the anchor element
                var link = document.createElement("a");
                link.href = base64String;
                link.download = fileName;
                link.click();
                $("#td_"+docId).html('');
                // Append Html 
                $("#td_"+docId).html('<a target="_blank" id="document_'+docId+'" onclick="getDowloadCaregiverDocument('+docId+')"><i class="fa fa-download"></i></a>')
            }else{
                $('#document_'+docId).attr('href','');
            }
            $('#tr_'+docId).removeClass('shimmer');
            $('#tr_'+docId).css('pointer-events', 'auto')
   
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

function renderInServicePagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <nav class="mt-3">
            <ul class="hha_caregiver_inservice pagination justify-content-center">
    `;

    html += `
        <li class="page-item ${currentInServicePage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
               onclick="changeInServicePage(${currentInServicePage - 1})">Prev</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === currentInServicePage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)"
                   onclick="changeInServicePage(${i})">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${currentInServicePage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
               onclick="changeInServicePage(${currentInServicePage + 1})">Next</a>
        </li>
    `;

    html += `</ul></nav>`;
    return html;
}

function changeInServicePage(page) {
    $('#inserviceContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    setTimeout(()=>{
        renderTabContent('inservice', { inservices: allInServiceData },page);
    },1000)
    
}

/**
 * Medical Dropdown Change Event - Handle Multiple Selection and Create Dynamic Result Dropdowns
 */
$(document).on('select2:select', '.created_medical_id', function (e) {
   
    fetchMedicalResults(e.params.data.id);
    // Add your logic here, e.g., remove from array or fetch updated results
});

$(document).on('select2:unselect', '.created_medical_id', function (e) {
    
    $('#create_other_compliance_result_'+e.params.data.id).remove();
    // Add your logic here, e.g., remove from array or fetch updated results
});

function editOtherCompliance(caregiverMedicalId){
    editCloseOtherComplianceModal();
    let editResponse = getAllOtherComplianceResponse.find(o=>o.caregiver_medical_id == caregiverMedicalId);

    if (!editResponse) {
        toastr.error('Compliance data not found');
        return;
    }

    // Format dates for input fields
    var dueDate = (editResponse.due_date != "" && editResponse.due_date) ? moment(editResponse.due_date).format('MM/DD/YYYY') : "";
    var datePerform = (editResponse.date_perform != "" && editResponse.date_perform) ? moment(editResponse.date_perform).format('MM/DD/YYYY') : "";

    // Populate modal fields
    $('#edit_caregiver_medical_id').val(caregiverMedicalId);
    $('#edit_medical_id').val(editResponse.medical_id || '');
    $('#edit_medical_name').val(editResponse.medical_name || '');
    $('#edit_document_type').val(editResponse.document_type || '');
    $('#edit_date_perform').val(datePerform);
    $('#edit_status').val(editResponse.status || 'Pending');
    $('#edit_result').val(editResponse.result || '');
    $('#edit_due_date').val(dueDate);
    $('#edit_notes').val(editResponse.notes || '');

    loadMedicalResult(editResponse.medical_id);
    loadEditDocumentList();
}

/**
 * Save other compliance changes
 */
function saveOtherComplianceChanges() {
    var caregiverMedicalId = $('#edit_medical_id').val();
    var documentType = $('#edit_document_type').val();
    var datePerform = $('#edit_date_perform').val();
    var result = $('#edit_result').val();
    var documentUpload = $('#edit_document_upload').prop('files');
    var dueDate = $('#edit_due_date').val();
    var notes = $('#edit_notes').val();
    var datePattern = /^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/(19|20)\d\d$/;

    $('#edit_hha_other_medical_id_error').html("");
    $('#edit_hha_other_medical_name_error').html("");
    $('#edit_hha_other_due_date_error').html("");
    $('#edit_hha_other_date_perform_error').html("");
    $('#edit_hha_other_document_type_error').html("");
    $('#edit_hha_other_result_error').html("");
    $('#edit_hha_other_document_upload_error').html("");
    // Validation
    var cnt =0;
    if (!caregiverMedicalId) {
        $('#edit_hha_other_medical_id_error').html("Please enter Medical Id");
        cnt = 1;
    }

    if (documentType == "") {
        $('#edit_hha_other_document_type_error').html("Please select Document Type");
        cnt = 1;
    }

    if (datePerform == "") {
        $('#edit_hha_other_date_perform_error').html("Please enter Date Performed");
        cnt = 1;
    }else{
        if (!datePattern.test(datePerform)) {
            $('#edit_hha_other_date_perform_error').html('Please enter valid date format (MM/DD/YYYY)');
            isValid = false;
        }
    }

    if (result == "") {
        $('#edit_hha_other_result_error').html("Please select Result");
        cnt = 1;
    }

    if (documentUpload.length == 0) {
        $('#edit_hha_other_document_upload_error').html("Please upload Document");
        cnt = 1;
    }
    if (documentUpload.length != 0) {
        var fileExtensionType = ['pdf'];
        var files = $('input[name="edit_document_upload"]')[0].files;
        var fileName = files[0].name;
        
        if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtensionType) == -1) {
            $('#edit_hha_other_document_upload_error').html("Please select only pdf file");
            cnt=1;
        }
    }

    if(dueDate ==""){
        $('#edit_hha_other_due_date_error').html("Please enter due date");
        cnt=1;
    }else{
        if (!datePattern.test(dueDate)) {
            $('#edit_hha_other_due_date_error').html('Please enter valid date format (MM/DD/YYYY)');
            isValid = false;
        }
    }

    if(cnt ==1){
        return false;
    }
    // Show loading state
    $('#saveOtherComplianceBtn').prop('disabled', true).text('Saving...');

    let form = new FormData($('#editOtherComplianceForm')[0]);
    form.append('_token',_CSRF_TOKEN)
    form.append('agency_id',agencyId)
    form.append('caregiver_id',currentCaregiversId)
    form.append('auto_update_next_due_date',$('#edit_auto_update_next_due_date:checked').val())
    form.append('date_performed',$('#edit_date_perform').val())
    // AJAX call to save changes
    $.ajax({
        url: _UPDATE_HHA_OTHER_COMPLIANCE_URL, // You need to define this URL
        type: 'POST',
        data: form,
        processData: false,
        contentType: false,
        success: function(response) {
            toastr.success('Other Compliance updated successfully');
            // Close modal
            editCloseOtherComplianceModal();
            $('#edit_close_other_compliance_modal').click();
            
            // Reload the compliance tab to show updated data
            loadCaregiverTab('compliance');
        },
        error: function(xhr) {
            showErrorAndLoginRedirection(xhr);
            toastr.error('Failed to update Other Compliance. Please try again.');
        },
        complete: function() {
            $('#saveOtherComplianceBtn').prop('disabled', false).text('Save Changes');
        }
    });
}

// Bind save button click event
$(document).on('click', '#saveOtherComplianceBtn', function() {
    saveOtherComplianceChanges();
});

function loadMedicalResult(medicalId){
    $.ajax({
        url: _LOAD_MEDICAL_RESULT,
        type: 'GET',
        data: {
            id: currentCaregiversId,
            agency_id: agencyId,
            medicaid_id: medicalId
        },
        success: function(response) {
            let medicalResultResponse = "";
            if(response.data.length !=0){
                medicalResultResponse = '<option value="">Select Result</option>'
                $.each(response.data,function(i,v){
                    medicalResultResponse +=`<option value="${v.id}">${v.name}</option>`;
                })
            }else{
                medicalResultResponse = '<option value="">No Result Available</option>'
            }

            $('#edit_result').html("")
            $('#edit_result').html(medicalResultResponse)
        }
    });
}

async function loadDocumentDropdown(){
    let data = await commonLoadDocunentType();
    var res = data.data.length;
    //$('#document_hha_id').val(val);
    var htmlrs = '<option value="">Select Document Type</option>';
    if (res != 0) {
        $.each(data.data, function(i, v) {
            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
        })
    }

    $('#create_view_document_type').html('');
    $('#create_view_document_type').html(htmlrs);
}

async function commonLoadDocunentType(){
    let final = [];
    $.ajax({
        async: false,
        global: false,
        url: _LOAD_DOCUMENT_TYPE,
        data: {
            'agencyId': agencyId,
        },
        success: function(response) {
            final = response;
        }
    });

    return final;
}

async function loadEditDocumentList(){
    let data = await commonLoadDocunentType();
    var res = data.data.length;
    //$('#document_hha_id').val(val);
    var htmlrs = '<option value="">Select Document Type</option>';
    if (res != 0) {
        $.each(data.data, function(i, v) {
            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
        })
    }

    $('#edit_document_type').html('');
    $('#edit_document_type').html(htmlrs)
}

/**
 * Open Other Compliance Modal
 */
function openOtherComplianceModal() {
    // Check if caregiver is selected
    if (typeof currentCaregiversId === 'undefined' || !currentCaregiversId) {
        toastr.error('No caregiver selected');
        return;
    }

    if (typeof agencyId === 'undefined' || !agencyId) {
        toastr.error('No agency selected');
        return;
    }

    // Reset form
    $('#otherComplianceForm')[0].reset();
    $('.text-danger').addClass('d-none');
    $('#medical_results_container').html('');

 
    loadDocumentDropdown();
    // Load medical dropdown
    loadMedicalDropdown();
    $('.created_medical_id').select2({
        dropdownParent: $('#created_medical_id')
    });
}

/**
 * Load Medical Dropdown via AJAX
 */
let caregiverViewHHAMedicalResponse = [];
function loadMedicalDropdown() {
    if (!currentCaregiversId || !agencyId) {
        toastr.error('Caregiver ID or Agency ID not found');
        return;
    }

    $.ajax({
        url: _HHA_LOAD_ALL_OTHER_COMPLIANCES_LIST,
        type: 'GET',
        data: {
            id: currentCaregiversId,
            agency_id: agencyId
        },
        success: function(response) {
            var options = '<option value="">Select Medical</option>';
            caregiverViewHHAMedicalResponse = [];
            if (response.data && response.data.length > 0) {
                
                $.each(response.data, function(index, item) {
                    caregiverViewHHAMedicalResponse.push({
                            key: item.id,
                            value: item.name
                        });
                    var medicalId = item.id;
                    var medicalName = item.name ;
                    options += '<option value="' + medicalId + '">' + medicalName + '</option>';
                });
            } else {
                options += '<option value="" disabled>No medical records available</option>';
            }

            $('#created_medical_id').html(options);
            $('#created_medical_id').select2({
                    placeholder: "Select HHX Compliance Name",
                    allowClear: true
                });
        },
        error: function(xhr) {
           showErrorAndLoginRedirection(xhr);
        }
    });
}

/**
 * Fetch Medical Results for a specific Medical Item
 * @param {string} medicalId - The medical ID
 * @param {string} medicalName - The medical name
 */
function fetchMedicalResults(medicalId) {
    let getCaregiverOthersMedicalIds = caregiverViewHHAMedicalResponse.find(item => item.key === medicalId);
    $.ajax({
        url: _LOAD_MEDICAL_RESULT,
        type: 'GET',
        data: {
            id: currentCaregiversId,
            agency_id: agencyId,
            medicaid_id: medicalId
        },
        success: function(response) {
            var options = '<option value="">Select Result</option>';

            if (response.data && Array.isArray(response.data)) {
                // If response.data is an array of result options
                $.each(response.data, function(index, result) {
                    var resultId = result.id || result.result_id || result.ResultID || '';
                    var resultName = result.name || result.result_name || result.ResultName || result.result || 'N/A';
                    options += '<option value="' + resultId + '">' + resultName + '</option>';
                });
            } else if (esponse.data && typeof response.data === 'object') {
                // If response.data is a single object with result options
                if (response.data.options && Array.isArray(response.data.options)) {
                    $.each(response.data.options, function(index, result) {
                        var resultId = result.id || result.result_id || result.ResultID || '';
                        var resultName = result.name || result.result_name || result.ResultName || result.result || 'N/A';
                        options += '<option value="' + resultId + '">' + resultName + '</option>';
                    });
                } else {
                    // If the response contains direct result data, create options from it
                    var resultData = response.data;
                    if (resultData.result) options += '<option value="' + (resultData.id || '') + '">' + resultData.result + '</option>';
                    if (resultData.status) options += '<option value="' + (resultData.id || '') + '">' + resultData.status + '</option>';
                }
            } else {
                options += '<option value="" disabled>No results available</option>';
            }

            var selectedText = '';
            var selectedTextData = $('#created_medical_id').select2("data");

            for (var i = 0; i <= selectedTextData.length - 1; i++) {

                if (selectedTextData[i].id == medicalId) {
                    selectedText = selectedTextData[i].text;
                }
            }

            var selectHtml = `<div class="col-md-6"  id="create_other_compliance_result_${medicalId}"><div class="form-group">
                <label for="recipient-name" class="col-form-label">${selectedText} Results:</label>
                    <select name="hha_create_other_compliance_result[${medicalId}]" class="form-control" id="hha_create_other_compliance_result_id${medicalId}">${options}</select>
                    <span id="hha_create_other_compliance_result_id_${medicalId}_error" style="color:red" class="error"></span>
            </div></div>`;
            $('#medical_results_container').append(selectHtml);
        },
        error: function(xhr) {
            showErrorAndLoginRedirection(xhr);
        }
    });
}

/**
 * Form Submission via AJAX
 */
$('#saveComplianceBtn').on('click', function(e) {
 
    var selectedMedicals = $('select[name="created_medical_id[]"] option:selected').length;
    var document_upload =  $('#document_upload').prop('files');
    var due_date = $('#due_date').val();
    var datePattern = /^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/(19|20)\d\d$/;
    var auto_update_next_due_date = $('#auto_update_next_due_date').is(":checked");
    // Clear previous errors
    $('.text-danger').addClass('d-none').text('');

    // Validate form
    var isValid = true;

    if (!$('#create_view_document_type').val()) {
        $('#create_view_document_type_error').removeClass('d-none').text('Document type is required');
        isValid = false;
    }

    if(selectedMedicals == '0'){
        $('#created_medical_id_error').removeClass('d-none').text('Medical is required');
        isValid = false;
    }

    if(auto_update_next_due_date){
        if($('#date_performed').val() ==""){
            $('#date_performed_error').removeClass('d-none').text('Date performed is required');
            isValid = false;
        }
    }
    
    if(selectedMedicals != '0'){
        $('select[name="created_medical_id[]"]').each(function() {
            var selected = $(this).val(); // this will be an array for multi-select
            var medicalResults = $('#hha_create_other_compliance_result_id'+selected).val();
            $('#hha_create_other_compliance_result_id_'+selected+'_error').html("");
            
            $('#date_performed_error').addClass('d-none').text('');

            // If date is filled but result is empty
            if ($('#date_performed').val() !== "" && medicalResults === "") {
                $('#hha_create_other_compliance_result_id_' + selected + '_error').html("Please select result");
                isValid = false;
            }

            // If result is filled but date is empty
            if (medicalResults !== "" && $('#date_performed').val() === "") {
                $('#date_performed_error').removeClass('d-none').text('Date performed is required');
                isValid = false;
            }else{
                if (medicalResults !== "" && $('#date_performed').val() !== "") {
                    if (!datePattern.test($('#date_performed').val())) {
                        $('#date_performed_error').removeClass('d-none').text('Please enter valid date format (MM/DD/YYYY)');
                        isValid = false;
                    }
                    
                }
            }
        });
    }
    
    if (due_date =="") {
         $('#due_date_error').removeClass('d-none').text('Due Date is required');
         isValid = false;
    }else{
        if (!datePattern.test(due_date)) {
            $('#due_date_error').removeClass('d-none').text('Please enter valid date format (MM/DD/YYYY)');
            isValid = false;
        }
    }

    if(document_upload.length == 0){
        $('#document_upload_error').removeClass('d-none').text('Document is required');
        isValid = false;
    }
    if (!isValid) {
        return false;
    }

    // Prepare form data
    var formData = new FormData($('#otherComplianceForm')[0]);
    formData.append('caregiver_id', currentCaregiversId);
    formData.append('agency_id', agencyId);
    formData.append('_token', _CSRF_TOKEN);

    // Disable save button
    $('#saveComplianceBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

    // Submit via AJAX
    $.ajax({
        async:false,
        global:false,
        url: _SAVE_HHA_OTHER_COMPLIANCE,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            toastr.success(response.message || 'Other compliance created successfully');
            $('#close_other_cmp_modal').click();
            if (typeof loadCaregiverTab === 'function') {
                loadCaregiverTab('compliance');
            }
        },
        error: function(xhr) {
            showErrorAndLoginRedirection(xhr)
           
        },
        complete: function() {
            // Re-enable save button
            $('#saveComplianceBtn').prop('disabled', false).html('<i class="mdi mdi-content-save"></i> Save');
        }
    });
});

function editCloseOtherComplianceModal(){
    $('#edit_hha_other_date_perform_error').html("");
    $('#edit_hha_other_document_type_error').html("");
    $('#edit_hha_other_result_error').html("");
    $('#edit_hha_other_document_upload_error').html("");
    $('#editOtherComplianceForm')[0].reset();
}

function renderOtherCompliancePagination(totalPages) {
    if (totalPages <= 1) return '';

    let html = `
        <nav class="mt-3">
            <ul class="hha_caregiver_other_compliance pagination justify-content-center">
    `;

    html += `
        <li class="page-item ${currentHHAOtherCompliancePage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
               onclick="changeOtherCompliancePage(${currentHHAOtherCompliancePage - 1})">Prev</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === currentHHAOtherCompliancePage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)"
                   onclick="changeOtherCompliancePage(${i})">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${currentHHAOtherCompliancePage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
               onclick="changeOtherCompliancePage(${currentHHAOtherCompliancePage + 1})">Next</a>
        </li>
    `;

    html += `</ul></nav>`;
    return html;
}

function changeOtherCompliancePage(page) {
    $('#complianceContent').html(`
        <div class="shimmer-wrapper">
            <!-- Header Shimmer -->
            <div class="shimmer shimmer-header"></div>
            <!-- Content Cards Shimmer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                        <div class="shimmer shimmer-line medium"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="shimmer-card">
                        <div class="shimmer shimmer-line title"></div>
                        <div class="shimmer shimmer-line medium"></div>
                        <div class="shimmer shimmer-line short"></div>
                        <div class="shimmer shimmer-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    setTimeout(()=>{
        renderTabContent('compliance', {'compliance':allHHAOtherComplianceData},page);
    },1000)
    
}

$(document).on('click', '.show-notes-other-compliance', function () {
    let notes = $(this).data('notes');
    let finalNotes =" No detail available";
    if(notes !=""){
        finalNotes =notes;
    }
    $.confirm({
        title: 'Medical Notes',
        content: `
            <div style="white-space:pre-line;">
                ${finalNotes}
            </div>
        `,
        type: 'blue',
      
        useBootstrap: false,
        buttons: {
            close: function () {}
        }
    });
});