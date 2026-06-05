<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Clinical</p>
    <div class="dropdown">
        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="clinicalPdfDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-plus"></i> Add PDF
        </button>
        <div class="dropdown-menu" aria-labelledby="clinicalPdfDropdown">
            <a class="dropdown-item" href="javascript:void(0);" onclick="selectPdf('medical_visit')">Medical Visit Report</a>
            <a class="dropdown-item" href="javascript:void(0);" onclick="selectPdf('medical_note')">Medical Note</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- PDF Selection and Preview Section -->
        <div id="pdfSelectionSection" style="display: none;">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Selected PDF: <span id="selectedPdfTitle"></span></h5>

                    <!-- PDF Preview Container -->
                    <div id="pdfPreviewContainer" class="mb-3">
                        <iframe id="pdfPreviewFrame" width="100%" height="600" style="border: 1px solid #ddd;"></iframe>
                    </div>

                    <!-- Form Fields for PDF Data -->
                    <div class="row" id="pdfFormFields">
                        <!-- Dynamic fields will be inserted here based on selected PDF type -->
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-secondary mr-2" onclick="cancelPdfSelection()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitClinicalPdf()">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clinical Records Listing -->
        <div class="loader-main" id="clinicalLoaderAlayaSkillLoaded" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader">
            </div>
        </div>
        <div id="clinical_records_list">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="clinical_records_tbody">
                        <!-- Records will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for clinical section
var selectedPdfType = '';
var currentRecordId = '{{ $record->id ?? '' }}';
var baseUrl = '{{ url("/") }}';

// Function to select PDF type
function selectPdf(pdfType) {
    selectedPdfType = pdfType;
    let pdfTitle = '';
    let pdfUrl = '';

    // Set PDF title and URL based on type
    switch(pdfType) {
        case 'medical_visit':
            pdfTitle = 'Medical Visit Report';
            pdfUrl = '{{ asset("resources/views/Hubclinical/HubMedicalHistory.blade.php") }}';
            break;
        case 'medical_note':
            pdfTitle = 'Medical Note';
            pdfUrl = '{{ asset("resources/views/Hubclinical/HubMedicalNote.blade.php") }}';
            break;
    }

    // Update UI
    document.getElementById('selectedPdfTitle').textContent = pdfTitle;
    document.getElementById('pdfSelectionSection').style.display = 'block';

    // Load PDF preview (convert HTML to display in iframe)
    loadPdfPreview(pdfUrl, pdfType);

    // Load form fields based on PDF type
    loadPdfFormFields(pdfType);
$('#visit_date, #excuse_from, #excuse_to').inputmask();
}

// Function to load PDF preview
function loadPdfPreview(pdfUrl, pdfType) {
    // For HTML files, we'll load them directly in the iframe
    let iframeContent = '';

    if (pdfType === 'medical_visit') {
        // Load the medical visit HTML content
        fetch('/get-clinical-html/' + pdfType +'?id=' + currentRecordId)
            .then(response => response.text())
            .then(html => {
                const iframe = document.getElementById('pdfPreviewFrame');
                iframe.srcdoc = html;
            })
            .catch(error => {
                console.error('Error loading PDF preview:', error);
                document.getElementById('pdfPreviewFrame').srcdoc = '<p>Preview not available</p>';
            });
    } else if (pdfType === 'medical_note') {
        // Load the medical note HTML content
        fetch('/get-clinical-html/' + pdfType+'?id=' + currentRecordId)
            .then(response => response.text())
            .then(html => {
                const iframe = document.getElementById('pdfPreviewFrame');
                iframe.srcdoc = html;
            })
            .catch(error => {
                console.error('Error loading PDF preview:', error);
                document.getElementById('pdfPreviewFrame').srcdoc = '<p>Preview not available</p>';
            });
    }
}

// Function to load form fields based on PDF type
function loadPdfFormFields(pdfType) {
    const formFieldsContainer = document.getElementById('pdfFormFields');
    let fieldsHtml = '';

    // Common fields for all PDF types
    fieldsHtml += `
        <div class="col-md-6">
            <div class="form-group">
                <label for="clinical_name">Document Name</label>
                <input type="text" class="form-control" id="clinical_name" placeholder="Enter document name" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="clinical_notes">Notes</label>
                <textarea class="form-control" id="clinical_notes" rows="3" placeholder="Enter any additional notes"></textarea>
            </div>
        </div>
    `;

    // Add specific fields based on PDF type
    if (pdfType === 'medical_visit') {
        fieldsHtml += `
            <div class="col-md-6">
                <div class="form-group">
                    <label for="visit_date">Visit Date</label>
                    <input type="text" class="form-control" id="visit_date" required    data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="doctor_name">Doctor Name</label>
                    <input type="text" class="form-control" id="doctor_name" placeholder="Enter doctor name">
                </div>
            </div>
        `;
    } else if (pdfType === 'medical_note') {
        fieldsHtml += `
            <div class="col-md-6">
                <div class="form-group">
                    <label for="excuse_from">Excuse From Date</label>
                    <input type="text" class="form-control" id="excuse_from" required  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="excuse_to">Excuse To Date</label>
                    <input type="text" class="form-control" id="excuse_to" required  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="doctor_name">Doctor Name</label>
                    <input type="text" class="form-control" id="doctor_name" placeholder="Enter doctor name">
                </div>
            </div>
        `;
    }

    formFieldsContainer.innerHTML = fieldsHtml;
}

// Function to cancel PDF selection
function cancelPdfSelection() {
    document.getElementById('pdfSelectionSection').style.display = 'none';
    selectedPdfType = '';
}

// Function to collect form data from iframe
function collectFormDataFromIframe() {
    const iframe = document.getElementById('pdfPreviewFrame');
    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

    const formData = {};

    // Get all input fields from the iframe
    const inputs = iframeDoc.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        if (input.type === 'radio') {
            if (input.checked) {
                formData[input.name] = input.value;
            }
        } else {
            formData[input.name] = input.value;
        }
    });

    return formData;
}

// Function to submit clinical PDF
function submitClinicalPdf() {
    if (!selectedPdfType) {
        toastr.error('Please select a PDF type');
        return;
    }

    // Get form data from main form
    const formData = {
        record_id: currentRecordId,
        pdf_type: selectedPdfType,
        name: document.getElementById('clinical_name').value,
        notes: document.getElementById('clinical_notes').value,
        _token: _CSRF_TOKEN
    };

    // Add specific fields based on PDF type
    if (selectedPdfType === 'medical_visit') {
        formData.visit_date = document.getElementById('visit_date').value;
        formData.doctor_name = document.getElementById('doctor_name').value;
    } else if (selectedPdfType === 'medical_note') {
        formData.excuse_from = document.getElementById('excuse_from').value;
        formData.excuse_to = document.getElementById('excuse_to').value;
        formData.doctor_name = document.getElementById('doctor_name').value;
    }

    // Collect all form data from iframe
    const iframeFormData = collectFormDataFromIframe();

    // Merge iframe form data with main form data
    Object.assign(formData, iframeFormData);

    // Validate required fields
    if (!formData.name) {
        toastr.error('Please enter document name');
        return;
    }

    // Show loader
    document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'block';

    // Submit via AJAX
    fetch('/save-clinical-pdf', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': _CSRF_TOKEN
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'none';

        if (data.status) {
            toastr.success(data.message || 'Clinical record saved successfully');
            cancelPdfSelection();
            loadClinicalRecords(); // Reload the records list
        } else {
            toastr.error(data.message || 'Error saving clinical record');
        }
    })
    .catch(error => {
        document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'none';
        toastr.error('Error submitting clinical record');
        console.error('Error:', error);
    });
}

// Function to load clinical records
function loadClinicalRecords() {
    if (!currentRecordId) return;

    document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'block';

    fetch('/get-clinical-records/' + currentRecordId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'none';

            if (data.status) {
                updateClinicalRecordsList(data.records);
            } else {
                console.error('Error loading clinical records:', data.message);
            }
        })
        .catch(error => {
            document.getElementById('clinicalLoaderAlayaSkillLoaded').style.display = 'none';
            console.error('Error loading clinical records:', error);
        });
}

// Function to update clinical records list
function updateClinicalRecordsList(records) {
    const tbody = document.getElementById('clinical_records_tbody');
    let html = '';

    if (records && records.length > 0) {
        records.forEach(record => {
            html += `
                <tr>
                    <td>${record.name}</td>
                    <td>${record.pdf_type.replace('_', ' ').toUpperCase()}</td>
                    <td>${new Date(record.created_at).toLocaleString()}</td>
                    <td>
                        <a href="${baseUrl}/generate-clinical-pdf/${record.id}" download class="btn btn-sm btn-outline-warning " title="Download PDF" target="_blank">
                            <i class="mdi mdi-download"></i>
                        </a>
                        <a href="/download-clinical-pdf/${record.id}" class="btn btn-sm btn-outline-primary" title="View PDF" target="_blank">
                            <i class="mdi mdi-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger ml-1" onclick="deleteClinicalRecord(${record.id})" title="Delete">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="4" class="text-center">No clinical records found</td></tr>';
    }

    tbody.innerHTML = html;
}
// Function called from the main hub record view when Clinical tab is clicked
function loadAllClinical() {
    loadClinicalRecords();
}


function deleteClinicalRecord(recordId) {
    var url = "/delete-clinical-record/" + recordId ;
  $.confirm({
    title: "Delete",
    columnClass: "col-md-6",
    content: "Are you sure delete record?",
    buttons: {
      formSubmit: {
        text: "Delete",
        btnClass: "btn-danger",
        action: function () {
          $.ajax({
            url: url,
            data: {
              _token: _CSRF_TOKEN,
            },
            type: "DELETE",
            success: function (res) {
                toastr.success('Clinical record deleted successfully');
              loadClinicalRecords();// Reload the records list
            },
            error: function (jqXHR) {
            toastr.error('Error deleting clinical record');
        console.error('Error:', error);
            },
          });
        },
      },
      cancel: function () {
        //close
      },
    },
  });
}



// Load clinical records when the section loads
document.addEventListener('DOMContentLoaded', function() {
    if (currentRecordId) {
        loadClinicalRecords();
    }
});
</script>