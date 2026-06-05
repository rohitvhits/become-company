/**
 * Document Workflow Module
 * Handles Draft -> Signature Required -> Form Completed transitions.
 */
function documentWorkflowAction(documentId, action) {
    var actionLabel = action === 'signature_required' ? 'Signature Required' : 'Form Completed';
    var url = action === 'signature_required' ? _DOCUMENT_WORKFLOW_SIGNATURE_REQUIRED : _DOCUMENT_WORKFLOW_COMPLETED;

    var confirmMsg = action === 'signature_required'
        ? 'Mark this document as "Signature Required"? Notifications will be sent to designated reviewers.'
        : 'Mark this document as "Form Completed"? The document will be finalized and accessible to the agency.';

    if (!confirm(confirmMsg)) {
        return;
    }

    $.ajax({
        url: url,
        type: "POST",
        data: {
            '_token': _CSRF_TOKEN,
            'document_id': documentId
        },
        success: function (res) {
            if (res.status == 1) {
                toastr.success(res.error_msg || 'Document marked as ' + actionLabel + ' successfully.');
                if (typeof loadDocumentAjaxList === 'function') {
                    loadDocumentAjaxList();
                } else {
                    location.reload();
                }
            } else {
                toastr.error(res.error_msg || 'Something went wrong.');
            }
        },
        error: function (xhr) {
            var errorMsg = 'Something went wrong. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error_msg) {
                errorMsg = xhr.responseJSON.error_msg;
            }
            toastr.error(errorMsg);
        }
    });
}
