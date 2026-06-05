/**
 * Patient Agency Merge Module
 * Handles AJAX list loading, multi-select, merge modal, and AJAX operations
 */

(function($) {
    'use strict';

    window.PatientAgencyMerge = {
        selectedPatients: [],
        currentPage: 1,

        /**
         * Initialize the module
         */
        init: function() {
            this.bindEvents();
            this.hideInitialTable();
        },

        /**
         * Hide initial table on page load
         */
        hideInitialTable: function() {
            $('#loadingShimmer').hide();
            $('#patientListContainer').html('<div class="alert alert-info text-center p-4"><i class="fa fa-info-circle"></i> <strong>Please select a deleted agency from the filter and click Search to load patient records.</strong></div>').show();
        },

        /**
         * Bind all event handlers
         */
        bindEvents: function() {
            var self = this;

            // Search button click
            $('#searchBtn').on('click', function(e) {
                e.preventDefault();
                self.loadPatientList(1);
            });

            // Reset button click
            $('#resetBtn').on('click', function(e) {
                e.preventDefault();
                self.resetFilters();
            });

            // Merge button click
            $('#mergeButton').on('click', function() {
                self.openMergeModal();
            });

            // Confirm merge button click
            $('#confirmMergeBtn').on('click', function() {
                self.confirmMerge();
            });

            // Sync button click
            $('#sync-btn').on('click', function() {
                self.syncMergeData();
            });

            // Modal show - reinitialize Select2
            $('#mergeModal').on('shown.bs.modal', function() {
                // Reinitialize Select2 for modal
                if (!$('#newAgencySelect').hasClass('select2-hidden-accessible')) {
                    $('#newAgencySelect').select2({
                        dropdownParent: $('#mergeModal'),
                        placeholder: '-- Select Agency --',
                        width: '100%'
                    });
                }
            });

            // Modal close - reset agency selection
            $('#mergeModal').on('hidden.bs.modal', function() {
                $('#newAgencySelect').val('').trigger('change');
            });
        },

        /**
         * Load patient list via AJAX
         */
        loadPatientList: function(page) {
            var self = this;

            // Get filter values
            var filters = {
                agency_fk: $('#agency_fk').val(),
                first_name: $('#first_name').val(),
                status: $('#status').val(),
                mobile: $('#mobile').val(),
                created_date: $('#created_date').val(),
                type: $('#type').val(),
                page: page || 1
            };

            // Validate that deleted agency is selected
            if (!filters.agency_fk) {
                $('#loadingShimmer').hide();
                $('#patientListContainer').html('<div class="alert alert-warning text-center p-4"><i class="fa fa-exclamation-triangle"></i> <strong>Please select a deleted agency from the filter to view patient records.</strong></div>').show();
                return;
            }

            // Show loading shimmer
            $('#loadingShimmer').show();
            $('#patientListContainer').hide();

            // AJAX request
            $.ajax({
                url: _PATIENT_AGENCY_MERGE_AJAX,
                type: 'GET',
                data: filters,
                success: function(response) {
                    $('#loadingShimmer').hide();
                    $('#patientListContainer').html(response).show();
                    self.currentPage = filters.page;
                    self.selectedPatients = [];
                    self.updateMergeButtonVisibility();
                },
                error: function(xhr) {
                    $('#loadingShimmer').hide();
                    var errorMessage = 'An error occurred while loading patient records.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('#patientListContainer').html('<div class="alert alert-danger">' + errorMessage + '</div>').show();
                }
            });
        },

        /**
         * Reinitialize checkboxes after AJAX load
         */
        reinitializeCheckboxes: function() {
            var self = this;

            // Select All checkbox
            $('#selectAllCheckbox').off('change').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('.patient-checkbox').prop('checked', isChecked);
                self.updateSelectedPatients();
            });

            // Individual checkbox change
            $('.patient-checkbox').off('change').on('change', function() {
                self.updateSelectedPatients();
                self.updateSelectAllCheckbox();
            });

            // Pagination links
            $('#patientListContainer').on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (url) {
                    var page = url.split('page=')[1];
                    self.loadPatientList(page);
                }
            });
        },

        /**
         * Reset filters
         */
        resetFilters: function() {
            $('#filterForm')[0].reset();
            $('#agency_fk').val('').trigger('change');
            $('#status').val('');
            $('#type').val('');
            this.hideInitialTable();
            this.selectedPatients = [];
            this.updateMergeButtonVisibility();
        },

        /**
         * Update selected patients array
         */
        updateSelectedPatients: function() {
            var self = this;
            self.selectedPatients = [];

            $('.patient-checkbox:checked').each(function() {
                self.selectedPatients.push({
                    id: parseInt($(this).val()),
                    agencyId: parseInt($(this).data('agency-id'))
                });
            });

            self.updateMergeButtonVisibility();
        },

        /**
         * Update Select All checkbox state
         */
        updateSelectAllCheckbox: function() {
            var totalCheckboxes = $('.patient-checkbox').length;
            var checkedCheckboxes = $('.patient-checkbox:checked').length;

            $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        },

        /**
         * Update merge button visibility and count
         */
        updateMergeButtonVisibility: function() {
            var count = this.selectedPatients.length;

            if (count > 0) {
                $('#mergeBtnContainer').addClass('active');
                $('#selectedCount').text(count);
            } else {
                $('#mergeBtnContainer').removeClass('active');
            }
        },

        /**
         * Open merge modal
         */
        openMergeModal: function() {
            var self = this;

            if (self.selectedPatients.length === 0) {
                self.showError('Please select at least one patient record to merge.');
                return;
            }

            // Get unique agency IDs from selected patients
            var selectedAgencyIds = [...new Set(self.selectedPatients.map(p => p.agencyId))];

            // Filter out agencies already in selected records
            $('#newAgencySelect option').each(function() {
                var optionValue = parseInt($(this).val());
                if (optionValue && selectedAgencyIds.includes(optionValue)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });

            // Reset and reinitialize select2
            $('#newAgencySelect').val('').trigger('change');

            // Update count in modal
            $('#modalSelectedCount').text(self.selectedPatients.length);

            // Show modal
            $('#mergeModal').modal('show');
        },

        /**
         * Confirm merge action with confirmation dialog
         */
        confirmMerge: function() {
            var self = this;
            var newAgencyId = $('#newAgencySelect').val();

            // Validation
            if (!newAgencyId) {
                self.showError('Please select an agency to merge records to.');
                return;
            }

            var newAgencyName = $('#newAgencySelect option:selected').text();
            var patientIds = self.selectedPatients.map(p => p.id);

            // Show confirmation dialog
            $.confirm({
                title: 'Confirm Merge',
                content: 'Are you sure you want to merge ' + patientIds.length + ' patient record(s) to <strong>' + newAgencyName + '</strong>?<br><br>This action will merge the selected records with the chosen agency.',
                type: 'orange',
                buttons: {
                    confirm: {
                        text: 'Yes, Merge Records',
                        btnClass: 'btn-success',
                        action: function() {
                            self.performMerge(patientIds, newAgencyId);
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-secondary'
                    }
                }
            });
        },

        /**
         * Perform AJAX merge operation
         */
        performMerge: function(patientIds, newAgencyId) {
            var self = this;

            // Show loading state
            $('#confirmMergeBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            // Get filter agency ID (deleted agency)
            var filterAgencyId = $('#agency_fk').val();

            // AJAX request
            $.ajax({
                url: _PATIENT_AGENCY_MERGE_UPDATE,
                type: 'POST',
                data: {
                    patient_ids: patientIds,
                    new_agency_id: newAgencyId,
                    filter_agency_id: filterAgencyId,
                    _token: _CSRF_TOKEN
                },
                success: function(response) {
                    if (response.status === 'success') {
                        self.showSuccess(response.message);

                        // Close modal
                        $('#mergeModal').modal('hide');

                        // Reload patient list after 1.5 seconds
                        setTimeout(function() {
                            self.loadPatientList(self.currentPage);
                            $('#confirmMergeBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Merge');
                        }, 1500);
                    } else {
                        self.showError(response.message || 'An error occurred while merging records.');
                        $('#confirmMergeBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Merge');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while merging records.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    self.showError(errorMessage);
                    $('#confirmMergeBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Merge');
                }
            });
        },

        /**
         * Sync merge data - Process pending merge requests
         */
        syncMergeData: function() {
            var self = this;

            // Show confirmation dialog
            $.confirm({
                title: 'Sync Pending Merges',
                content: 'This will process all pending agency merge requests. Are you sure you want to continue?',
                type: 'blue',
                buttons: {
                    confirm: {
                        text: 'Yes, Sync Now',
                        btnClass: 'btn-primary',
                        action: function() {
                            self.performSync();
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-secondary'
                    }
                }
            });
        },

        /**
         * Perform AJAX sync operation
         */
        performSync: function() {
            var self = this;

            // Show loading state on sync button
            var syncBtn = $('#sync-btn');
            var originalHtml = syncBtn.html();
            syncBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Syncing...');

            // AJAX request
            $.ajax({
                url: _PATIENT_AGENCY_MERGE_SYNC,
                type: 'POST',
                data: {
                    _token: _CSRF_TOKEN
                },
                success: function(response) {
                    if (response.status === 'success' || response.status === 'info') {
                        var message = response.message;
                        if (response.processed > 0) {
                            message += '<br><br><strong>Processed:</strong> ' + response.processed + ' record(s)';
                        }
                        if (response.failed > 0) {
                            message += '<br><strong>Failed:</strong> ' + response.failed + ' record(s)';
                        }

                        $.alert({
                            title: response.status === 'success' ? 'Sync Successful' : 'Sync Info',
                            content: message,
                            type: response.status === 'success' ? 'green' : 'blue',
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-primary',
                                    action: function() {
                                        // Reload patient list if records were processed
                                        if (response.processed > 0) {
                                            self.loadPatientList(self.currentPage);
                                        }
                                    }
                                }
                            }
                        });

                        syncBtn.prop('disabled', false).html(originalHtml);
                    } else {
                        self.showError(response.message || 'An error occurred while syncing merge data.');
                        syncBtn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while syncing merge data.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    self.showError(errorMessage);
                    syncBtn.prop('disabled', false).html(originalHtml);
                }
            });
        },

        /**
         * Show success message
         */
        showSuccess: function(message) {
            if (typeof toastr !== 'undefined') {
                toastr.success(message, 'Success', {
                    timeOut: 3000,
                    closeButton: true,
                    progressBar: true
                });
            } else {
                alert(message);
            }
        },

        /**
         * Show error message
         */
        showError: function(message) {
            if (typeof toastr !== 'undefined') {
                toastr.error(message, 'Error', {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true
                });
            } else {
                alert(message);
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        PatientAgencyMerge.init();
    });

})(jQuery);
