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
        
function openCaregiverModal(agencyFk,caregiverId, caregiverName) {
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

/**
 * Load content for a specific tab
 * @param {string} tabName - The name of the tab to load
 *
 * This function makes an AJAX call to load tab-specific data
 * Customize the URL and data structure based on your backend API
 */
function loadCaregiverTab(tabName) {
    if (!currentCaregiversId) {
        console.error('No caregiver ID set');
        return;
    }

    var contentElementId = tabName + 'Content';
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

    loadedTabs[tabName] = true;
    if(tabName =='notes'){
        loadHHANotes();
    }else{

    }

}


function loadHHANotes(tabName,contentElementId){
    
    var date = $('#notesDateRangePicker').val();
    $.ajax({
        url: _HHA_CAREGIVER_DETAIL_URL + '/' + tabName,
        type: 'GET',
        data: {
            caregiver_id: currentCaregiversId,
            agency_id: agencyId,
            date:date
            
        },
        success: function(response) {
            // Mark tab as loaded
            loadedTabs[tabName] = true;

            // Check if response is valid
            if (response && response.status === 1 && response.data) {
                // Render the content based on tab type
                renderTabContent(tabName, response.data);
            } else {
                $('#'+contentElementId).html(`
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert"></i> ${response.message || 'No data available'}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading ' + tabName + ' tab:', error);
            $('#' + contentElementId).html(`
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert"></i> Error loading data. Please try again.
                </div>
            `);
        }
    });
}