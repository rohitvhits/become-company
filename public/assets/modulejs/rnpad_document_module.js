var _RECORD_ID;
var _RECORD_AGENCY_ID;
$(function() {
    $(".wmd-view-topscroll").scroll(function() {
        $(".wmd-view").scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function() {
        $(".wmd-view-topscroll").scrollLeft($(".wmd-view").scrollLeft());
    });
});

/**
 * Load RN Pad Documents via AJAX
 */
function rnpadDocumentAjax(page) {
    $('.shimmer_id').removeClass('hide');
    $('#response_rnpad_list').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');

    $.ajax({
        url: _RNPAD_DOCUMENT_AJAX + "?page=" + page,
        type: "get",
        data: {
            'agency_id': $('#agency_id').val(),
            'patient_name': $('#patient_name').val(),
            'document_name': $('#document_name').val(),
            'service': $('#service').val(),
            'status': $('#status').val(),
            'created_date': $('#created_date').val(),
            'sorting_column': 'created_date',
            'sorting_order': 'desc',
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('#response_rnpad_list').html(res);
            $('.location-wise-data-loader').attr('style', 'display:none');
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg || 'An error occurred');
        }
    });

    return false;
}

/**
 * Export to CSV
 */
function exportCsv() {
    $('#exportLoader').removeClass('d-none');
    $('#exportText').text('Exporting...');

    var params = {
        'patient_name': $('#patient_name').val(),
        'agency_id': $('#agency_id').val(),
        'document_name': $('#document_name').val(),
        'service': $('#service').val(),
        'status': $('#status').val(),
        'created_date': $('#created_date').val(),
    };

    var queryString = $.param(params);
    window.location.href = _RNPAD_DOCUMENT_EXPORT_CSV + '?' + queryString;

    setTimeout(function() {
        $('#exportLoader').addClass('d-none');
        $('#exportText').text('Export CSV');
    }, 2000);
}

/**
 * Download Attachment
 */
function downloadAttachment(documentId) {
    window.open('/rnpad/download-attachment/' + documentId, '_blank');
}

/**
 * Reset Filter Form
 */
function refresh() {
    $('#search-form')[0].reset();
    $('#service').val('').trigger('change');
    $('#status').val('').trigger('change');
    $('#agency_id').val('').trigger('change');
    rnpadDocumentAjax(1);
}

/**
 * Filter Toggle
 */
$('#filter-btn').click(function() {
    $('#search-filter-btn').slideToggle();
});

/**
 * Initialize on page load
 */
$(document).ready(function() {
    // Load initial data
    rnpadDocumentAjax(1);

    var start = moment().subtract(0, 'days');
            var end = moment();
            $('#created_date').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: false,
                startOfWeek: 'sunday',
                ranges: {
                    'Select Date': [start, end],
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

                $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                    'MM/DD/YYYY'));
            })

            $('#created_date').on('apply.daterangepicker', function(ev, picker) {
            // Detect "Select Date"
            if (picker.chosenLabel === 'Select Date') {
                $(this).val('');
            } else {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            }
        });

    // Initialize select2
    $('#service').select2({
        placeholder: 'Select Service',
        allowClear: true
    });
    $('#agency_id').select2({
        placeholder: 'Select Agency',
        allowClear: true
    });

    $('#status').select2({
        placeholder: 'Select Status',
        allowClear: true
    });
    // Pagination click handler
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        rnpadDocumentAjax(page);
    });
});

function openRNPadModal(documentId,patientID,agencyID){
    $('#rnpad_choose_services_error').html("");
    $.ajax({
        async:false,
        global:false,
        url: _GET_RNPAD_URL_SERVICES,
        type:"get",
        data: {
            id: patientID,
            agency_id:agencyID
           
        },
        success:function(res){
            var json = res.data;
            var optionHtmlResponse ='<option value="">Select Services</option>';
            if(json.length !=0){

                $.each(json,function(i,v){
                    optionHtmlResponse +="<option value='"+v.id+"'>"+v.id+' - '+v.services+' - '+v.status+' - '+v.created_date+"</option>";
                })
            }

            $('#rnpad_choose_services').html('');
            $('#rnpad_choose_services').html(optionHtmlResponse)
            $('#rnpad_document_id').val(documentId);
            _RECORD_ID = patientID;
            _RECORD_AGENCY_ID = agencyID;
            $('#send-rnpad-document-modal').modal('show')

        },
        error:function(jqr){
            toastr.error(jqr.responseJSON.error_msg);
        }
    });
  }

  function closeSendRnPadModal(){
    $('#send-rnpad-document-modal').modal('hide');
  }

  function submitRndPadDocument(){
    $('#submit-rnpad-doc-spinner').removeClass('d-none')

    var rnpad_choose_services = $('#rnpad_choose_services').val();
    $('#rnpad_choose_services_error').html("");
    var cnt =0;

    if(rnpad_choose_services ==""){
        $('#rnpad_choose_services_error').html("Please select Service");
        cnt =1;
    }

    if(cnt ==1){
        $('#submit-rnpad-doc-spinner').addClass('d-none')
        return false;
    }else{
        $('#btn-submit-rnpad-text').html("Sending...");
        $.ajax({
            type:"POST",
            url:_SEND_RNPAD_DOCUMENT,
            data:{
                third_party_id:rnpad_choose_services,
                appointment_id:_RECORD_ID,
                document_id:$('#rnpad_document_id').val(),
                agency_id:_RECORD_AGENCY_ID,
                '_token':_CSRF_TOKEN
            },
            success:function(res){
                toastr.success(res.error_msg);
                $('#send-rnpad-document-modal').modal('hide');
                $('#btn-submit-rnpad-text').html("Send");
                $('#submit-rnpad-doc-spinner').addClass('d-none')
                rnpadDocumentAjax(1);
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
                $('#btn-submit-rnpad-text').html("Send");
                $('#submit-rnpad-doc-spinner').addClass('d-none')
            }
        })

    }

  }