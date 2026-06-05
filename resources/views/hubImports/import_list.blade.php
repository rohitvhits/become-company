@include('include/header')
@include('include/sidebar')

<style>
    .field-selection-group {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        background-color: #f9f9f9;
    }
    .field-checkbox {
        margin-bottom: 8px;
    }
    .import-summary {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .summary-item .label {
        font-weight: bold;
    }
    .summary-item .value {
        color: #495057;
    }
    .error-list {
        max-height: 150px;
        overflow-y: auto;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 4px;
        margin-top: 10px;
    }
    .upload-progress {
        margin-top: 15px;
    }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-panel">
    <?php $auth = auth()->user(); ?>
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">📘 HubImports – Employee Data Import</h5>
            <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                <i class="mdi mdi-upload"></i> Upload File
            </a>
        </div>

        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Employee Import Instructions</h6>
                        <ul class="small text-muted">
                            <li>Upload CSV or Excel files with employee data</li>
                            <li>Select unique fields for duplicate detection</li>
                            <li>Existing records will be updated, new ones inserted</li>
                            <li>Missing employees will be marked as Inactive</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ URL::to('/hub-imports/download-sample') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-download"></i> Download Sample File
                        </a>
                    </div>
                </div>

                <div id="import-history" class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>File Name</th>
                                <th>Total Records</th>
                                <th>Inserted</th>
                                <th>Updated</th>
                                <th>Failed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No import history found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">📁 Upload Employee Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="importForm" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        @if($user->agency_fk == '')
                        <div class="form-group">
                            <label for="agency_id" class="col-form-label">Agency <span style="color:red">*</span>:</label>
                            <select name="agency_id" class="form-control" id="agency_id" required>
                                <option value="">Select Agency</option>
                                @foreach($agencyList as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                @endforeach
                            </select>
                            <span class="error mt-2 text-danger" id="agency_error"></span>
                        </div>
                        @else
                        <input type="hidden" name="agency_id" value="{{ $user->agency_fk }}">
                        @endif

                        <div class="form-group">
                            <label for="file" class="col-form-label">Upload File <span style="color:red">*</span>:</label>
                            <input type="file" class="form-control-file" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                            <small class="form-text text-muted">Accepted formats: CSV, Excel (.xlsx, .xls). Maximum size: 10MB</small>
                            <span class="error mt-2 text-danger" id="file_error"></span>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Unique Fields for Duplicate Check <span style="color:red">*</span>:</label>
                            <p class="small text-muted">Select one or more fields to check for duplicate employees:</p>
                            <div class="field-selection-group">
                                @foreach($uniqueFields as $field => $label)
                                <div class="field-checkbox">
                                    <label class="form-check-label" style="margin-left: 25px !important">
                                        <input type="checkbox" name="unique_fields[]" value="{{ $field }}" class="form-check-input">
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <span class="error mt-2 text-danger" id="unique_fields_error"></span>
                        </div>

                        <div class="upload-progress" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-center mt-2 mb-0">Processing file...</p>
                        </div>

                        <div id="import-summary-container" style="display: none;">
                            <div class="import-summary">
                                <h6 class="font-weight-bold mb-3">📊 Import Summary</h6>
                                <div id="import-summary-content"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="uploadBtn">
                        <i class="mdi mdi-upload"></i> Process Import
                    </button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('include/footer')

    <script src="{{ URL::to('/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#uploadBtn').click(function() {
                processImport();
            });

            function processImport() {
                // Validate form
                let isValid = true;
                $('.error').html('');

                // Check file
                if (!$('#file')[0].files.length) {
                    $('#file_error').html('Please select a file');
                    isValid = false;
                }

                // Check agency (if visible)
                if ($('#agency_id').is(':visible') && !$('#agency_id').val()) {
                    $('#agency_error').html('Please select an agency');
                    isValid = false;
                }

                // Check unique fields
                if (!$('input[name="unique_fields[]"]:checked').length) {
                    $('#unique_fields_error').html('Please select at least one unique field');
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                // Show progress
                $('.upload-progress').show();
                $('#uploadBtn').prop('disabled', true);
                $('#import-summary-container').hide();

                // Prepare form data
                let formData = new FormData($('#importForm')[0]);

                $.ajax({
                    url: '{{ URL::to("/hub-imports/upload") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('.upload-progress').hide();
                        $('#uploadBtn').prop('disabled', false);

                        if (response.success) {
                            displayImportSummary(response.summary);
                        } else {
                            alert('Import failed: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        $('.upload-progress').hide();
                        $('#uploadBtn').prop('disabled', false);

                        let errorMsg = 'Import failed';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            }

            function displayImportSummary(summary) {
                let summaryHtml = `
                    <div class="summary-item">
                        <span class="label">Total Records in File:</span>
                        <span class="value">${summary.total_records}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">✅ Inserted:</span>
                        <span class="value">${summary.inserted}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">✏️ Updated:</span>
                        <span class="value">${summary.updated}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">⚠️ Failed:</span>
                        <span class="value">${summary.failed}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">🔒 Marked Inactive:</span>
                        <span class="value">${summary.marked_inactive || 0}</span>
                    </div>
                `;

                if (summary.errors && summary.errors.length > 0) {
                    summaryHtml += `
                        <div class="mt-3">
                            <strong>Errors:</strong>
                            <div class="error-list">
                                ${summary.errors.map(error => `<div class="small">${error}</div>`).join('')}
                            </div>
                        </div>
                    `;
                }

                $('#import-summary-content').html(summaryHtml);
                $('#import-summary-container').show();
            }

            // Reset form when modal is closed
            $('#uploadModal').on('hidden.bs.modal', function() {
                $('#importForm')[0].reset();
                $('.error').html('');
                $('.upload-progress').hide();
                $('#import-summary-container').hide();
                $('#uploadBtn').prop('disabled', false);
            });
        });
    </script>
</div>