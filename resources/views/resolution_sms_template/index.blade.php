@include('include/header')
@include('include/sidebar')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-1 font-weight-bold">Resolution Status SMS Templates</h5>
        </div>

        <div class="card">
            <form id="bulkSmsTemplateForm" class="forms-sample">
                @csrf
                <div class="card-body">
                    <p class="text-muted mb-4">Manage SMS messages sent to patients when their resolution status changes.</p>

                    <div class="row">
                        @foreach($templates as $template)
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">{{ $template->status }}</label>
                                <div class="col-sm-9">
                                    <textarea name="templates[{{ $template->id }}]"
                                              class="form-control"
                                              style="height: 80px"
                                              placeholder="Enter SMS message...">{{ $template->message }}</textarea>
                                    <div class="text-muted small mt-1">
                                        Use <code>{patient_name}</code> for name, <code>{appointment_date}</code> for date.
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
            <div class="card-footer">
                <button type="button" class="btn btn-primary mr-2" id="saveAllSmsTemplatesBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status"></span>
                    Submit
                </button>
            </div>
        </div>
    </div>
    @include('include/footer')
</div>

<script>
$(document).ready(function () {
    $('#saveAllSmsTemplatesBtn').on('click', function () {
        var formData = $('#bulkSmsTemplateForm').serialize();

        $('#saveSpinner').removeClass('d-none');
        $('#saveAllSmsTemplatesBtn').prop('disabled', true);

        $.ajax({
            url: '{{ route("resolution-sms-template.bulk-update") }}',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.error_msg);
                } else {
                    toastr.error(response.error_msg || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.error_msg ? xhr.responseJSON.error_msg : 'Something went wrong.';
                showErrorAndLoginRedirection(xhr);
            },
            complete: function () {
                $('#saveSpinner').addClass('d-none');
                $('#saveAllSmsTemplatesBtn').prop('disabled', false);
            }
        });
    });
});
</script>
