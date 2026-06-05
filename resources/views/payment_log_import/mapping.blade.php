@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Map Columns - Payment Log Import</h5>
        </div>

        <!-- File Info -->
        <div class="card mb-4">
            <div class="card-body">
                <h6>File: <strong>{{ $importLog->file_name }}</strong></h6>
                <p class="mb-0">Uploaded on: {{ $importLog->uploaded_at->format('d-m-Y H:i') }}</p>
            </div>
        </div>

        <!-- Preview Data -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">File Preview (First 5 Rows)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="mapping_table">
                        <thead>
                            <tr>
                                @foreach($headers as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preview as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mapping Form -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Map Database Fields to File Columns</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('payment_log_import.process_mapping', $importLog->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        @foreach($dbFields as $field => $label)
                            <div class="col-md-6 mb-3">
                                <label>{{ $label }} @if(in_array($field, ['name', 'dob', 'vendor_name', 'patient_id'])) <span class="text-danger">*</span> @endif</label>
                                <select name="mapping[{{ $field }}]" class="form-control select2">
                                    <option value="">-- Select Column --</option>
                                    @foreach($headers as $header)
                                        <option value="{{ $header }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check"></i> Confirm Mapping & Validate
                        </button>
                        <a href="{{ route('payment_log_import.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap',
        width: '100%'
    });

    // Auto-map fields intelligently to avoid duplicates
    autoMapFields();
});

function autoMapFields() {
    // Define mapping priorities - more specific matches first
    const mappingRules = {
        'name': ['Name'],
        'dob': ['DOB', 'Date of Birth', 'Birth Date'],
        'patient_id': ['Portal ID', 'Patient ID', 'ID'],
        'vendor_name': ['Vendor Name', 'Vendor'],
        'service_type': ['Service Type', 'Type', 'Initial or Annual'],
        'services': ['Services', 'Service'],
        'ppd_q': ['PPD/Q', 'PPD', 'Q'],
        'bill': ['Bill', 'Bill Amount', 'BILL'],
        'cash': ['Cash', 'Cash Amount', 'CASH'],
        'card': ['Card', 'Card Amount', 'CARD'],
        'insurance': ['Insurance', 'Insurance Amount', 'INSURANCE'],
        'location': ['Location', 'LOCATION'],
        'initials': ['Initials'],
        'created_at': ['Created At', 'Date', 'Created Date']
    };

    const usedColumns = new Set();

    // Process each field
    Object.keys(mappingRules).forEach(fieldName => {
        const selectElement = $(`select[name="mapping[${fieldName}]"]`);
        const possibleMatches = mappingRules[fieldName];

        // Find the first available match
        for (let matchTerm of possibleMatches) {
            const options = selectElement.find('option');
            let matchedOption = null;

            options.each(function() {
                const optionValue = $(this).val();
                const optionText = $(this).text().trim();

                // Skip empty option and already used columns
                if (!optionValue || usedColumns.has(optionValue)) {
                    return;
                }

                // Case-insensitive exact match
                if (optionText.toLowerCase() === matchTerm.toLowerCase()) {
                    matchedOption = optionValue;
                    return false; // break the loop
                }
            });

            if (matchedOption) {
                selectElement.val(matchedOption).trigger('change');
                usedColumns.add(matchedOption);
                break; // Move to next field
            }
        }
    });
}
</script>
