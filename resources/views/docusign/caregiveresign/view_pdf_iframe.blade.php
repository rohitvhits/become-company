<meta name="csrf-token" content="{{ csrf_token() }}">

<title>NY BEST MEDICAL</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.eot">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.ttf">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.woff">
<link href="<?= URL::to('assets/css/vertical-layout-light/jquery-ui.css') ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/css/vendor.bundle.base.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/jqvmap/jqvmap.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/vendors/flag-icon-css/css/flag-icon.min.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/css/horizontal-default-light/style.css') ?>">
<link rel="stylesheet" href="<?= URL::to('assets/css/sweetalert2.min.css') ?>">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />
<link href="<?= URL::to('assets/css/select2.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<link href="<?= URL::to('assets/css/jquery-confirm.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">

<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .iframe-container iframe {
        width: 100%;
        height: 600px !important;
        border: none;
    }

    .sidebar-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 1px solid #ccc;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .text-danger {
        color: red;
    }

    .pdf_status_reason {
        margin-bottom: 10px;
    }

    .form-check-inline {
        margin-right: 10px;
    }
    .form-check-inline {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .form-check-input{
        margin-left:4px;
    }
    .d-flex.gap-3.mt-1 {
        margin-top: -5px !important;
    }
    .form-check {
        margin-bottom: 0 !important;
    }
    .form-group{
        margin-bottom : 0px !important;
    }
    .title {
        margin-top: 20px !important;
    }
    .radio-label {
        display: inline-block;
        padding: 5px 0px 0px  5px;
        outline: 1px solid #238be5;
        padding-right: 35px;
        background-color: #d5ecff;
    }
    
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Iframe Section -->
        <div class="col-md-9">
            <div class="iframe-container">
                <iframe src="{{ $url }}" style="border:0" width="100%" height="600px" title=""></iframe>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-md-3">
            <div class="d-flex justify-content-end mb-3 mt-4">
                <button type="button" class="btn btn-primary btn-sm" onclick="refreshData('{{ $getDetails->id }}', '{{ $getDetails->groupId }}')">Refresh</button>
            </div>

            <div class="sidebar-content">
                <p><strong>Template Name</strong><br>
                    {{ isset($getDetails->templateDetails) ? $getDetails->templateDetails->template_name : 'N/A' }}
                    <span>(#{{ $getDetails->id }})</span>
                </p>

                <p class="title"><strong>Added By</strong><br>
                    {{ isset($getDetails->userDetails) ? $getDetails->userDetails->first_name . ' ' . $getDetails->userDetails->last_name : 'N/A' }}
                    at
                    {{ isset($getDetails['created_date']) && $getDetails['created_date'] ? date('m/d/Y h:i A', strtotime($getDetails['created_date'])) : 'N/A' }}
                </p>

                <p class="title"><strong>Status</strong><br>
                    {{ $getDetails->status ?? 'N/A' }} at
                    {{ isset($getDetails['completed_on']) && $getDetails['completed_on'] ? date('m/d/Y h:i A', strtotime($getDetails['completed_on'])) : 'N/A' }}
                </p>

                <!-- Form Section -->
                <form id="formAdd">
                    @csrf
                    <input type="hidden" name="document_id" id="document_id" value="{{ $getDetails->id ?? '' }}">
                    <input type="hidden" name="group_id" id="group_id" value="{{ $getDetails->groupId ?? '' }}">

                    <div class="form-group title">
                        <label for="pdf_status"><strong>Action</strong><span class="text-danger">*</span></label><br>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check mr-4 m-0 radio-label">
                                <input class="form-check-input" type="radio" name="pdf_status" value="1" id="approve" checked>
                                <label class="form-check-label" for="approve">Approve</label>
                            </div>
                            <div class="form-check m-0 radio-label">
                                <input class="form-check-input" type="radio" name="pdf_status" value="0" id="reject">
                                <label class="form-check-label" for="reject">Reject</label>
                            </div>
                        </div>
                        <div id="pdf_status_error" class="text-danger small"></div>
                    </div>

                    <div class="form-group pdf_status_reason title">
                        <label for="pdf_status_reason"><strong>Reason</strong><span class="text-danger">*</span></label>
                        <textarea class="form-control" name="pdf_status_reason" id="pdf_status_reason" rows="4" placeholder="Enter reason"></textarea>
                        <div id="pdf_status_reason_error" class="text-danger small"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary btn-sm" id="cancelButton">Cancel</button>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="saveFormBtn()">Save</button>
                            @can('esign-pdf-edit')
                                <a href="{{ url('esign/get-document-sent-report-data') }}?groupId={{ $getDetails->groupId }}" class="btn btn-warning btn-sm" target="_blank">Edit</a>
                            @endcan
                        </div>
                    </div>
                </form>
            </div> <!-- sidebar-content -->
        </div> <!-- col-md-3 -->
    </div> <!-- row -->
</div> <!-- container-fluid -->


<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<!-- Plugin js for this page-->
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.world.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/peity/jquery.peity.min.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery.flot.dashes.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<!-- End plugin js for this page-->

<!-- inject:js -->
<script src="<?= URL::to('assets/js/off-canvas.js') ?>"></script>
<script src="<?= URL::to('assets/js/hoverable-collapse.js') ?>"></script>
<script src="<?= URL::to('assets/js/template.js') ?>"></script>
<script src="<?= URL::to('assets/js/settings.js') ?>"></script>
<script src="<?= URL::to('assets/js/todolist.js') ?>"></script>
<!-- endinject -->
<!-- plugin js for this page -->
<script src="<?= URL::to('assets/vendors/datatables.net/jquery.dataTables.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/data-table.js') ?>"></script>
<!-- plugin js for this page -->
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/dashboard.js') ?>"></script>
<!-- End custom js for this page-->

<script src="<?= URL::to('assets/js/sweetalert2.min.js') ?>"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<script src="{{ asset('assets/js/tribute.js') }}"></script>
<script src="{{ asset('assets/modulejs/esign_module_new.js') }}?time={{ env('timestamps') }}"></script>

<script>
    var _PDF_STATUS = "{{ url('esign/pdf/update-status') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var _ESIGN_HISTROY  = "{{ url('esign/esign-history')}}";
    var _BASE_URL = "{{ url('/')}}";
</script>
<script>
    var redirection_module_type = '{{ $redirection_module_type}}';
    function saveFormBtn() {
        var temp = 0;

        var pdf_status = $('input[name="pdf_status"]:checked').val();
        var pdf_status_reason = $("#pdf_status_reason").val().trim();

        if (!pdf_status) {
            $('#pdf_status_error').html("Please select Status");
            temp++;
        }

        if (pdf_status === "0" && pdf_status_reason === '') {
            $('#pdf_status_reason_error').html("Reason is required for rejection");
            temp++;
        } else {
            $('#pdf_status_reason_error').html("");
        }

        if (temp > 0) {
            return false;
        }

        var formAppend = $('#formAdd')[0];
        var formData = new FormData(formAppend);
        formData.append('_token', _CSRF_TOKEN);

        $.ajax({
            url: _PDF_STATUS,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
               
                setTimeout(() => {
                    toastr.success(response.error_msg);
                }, 200);
                if(redirection_module_type !=""){
                    window.parent.loadEsignReportList(1);
                }else{
                    window.parent.esignResponseNew1();
                }
                window.parent.$.fancybox.close();
            },
            error: function(xhr, status, error) {
                showErrorAndLoginRedirection(xhr)
            }
        });
    }

    function editFormBtn() {
        let groupId = $("#group_id").val();
        $.ajax({
            url: "/esign/get-document-sent-report-data/" + groupId,
            type: "GET",
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    alert("Failed to fetch data!");
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }


    $('input[name="pdf_status"]').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === "0") {
            $('.pdf_status_reason').show();
        } else {
            $('.pdf_status_reason').hide();
        }
        $('#pdf_status_error').html('');
        $('#pdf_status_reason_error').html('');
        $('#pdf_status_reason').val('');
    });

    $(document).ready(function() {
        $('.pdf_status_reason').hide();
    });

    $('#cancelButton').click(function() {
        window.parent.$.fancybox.close();
    });

    function refreshData(id, groupId) {
        var previewUrl = _BASE_URL + '/esign/preview-pdf-response-update?id=' + id + '&group_id=' + groupId;

        $.ajax({
            url: previewUrl,
            type: 'GET',
            success: function (response) {
                if (response.url) {
                    $('.iframe-container iframe').attr('src', response.url);
                } else {
                    console.error('PDF URL not found in the response');
                }
            },
            error: function () {
                console.error('Error fetching PDF preview URL');
            }
        });
    }

</script>
