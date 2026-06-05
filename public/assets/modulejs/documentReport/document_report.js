var _sortBy = '';
var _sortOrder = 'desc';

function loadAjaxList(page=1){
    $('.shimmer_id').removeClass('hide')
    $('#response_requested_id').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data:{
            'page':page,
            'agency_id':$('#agency_fk').val(),
            'created_date':$('#appointment_date').val(),
            'patient_id':$('#patient_id').val(),
            'patient_type':$('#patient_type').val(),
            'internal_use':$('#archived:checked').val(),
            'document_created_by':$('#document_created_by').val(),
            'assign_document_user':$('#assign_document_user').val(),
            'completion_date':$('#completion_date').val(),
            'document_review_date':$('#document_review_date').val(),
            'review_document_user':$('#review_document_user').val(),
            'document_status':$('#pending_doc:checked').val(),
            'service_id':$('#service_id').val(),
            'mdo_tag':$('#mdo_tag').val(),
            'insurance_elg':$('#insurance_elg').val(),
            'medication_list':$('#medication_list').val(),
            'branch_id':$('#branch_id').val(),
            'mdo_source':$('#mdo_source').val(),
            'send_email':$('#send_email').val(),
            'sort_by':_sortBy,
            'sort_order':_sortOrder,
        },
        type: "GET",
        success:function(res){
            $('.shimmer_id').addClass('hide')
            $('#response_requested_id').html(res)
            $('.location-wise-data-loader').attr('style', 'display:none');
            if(_sortBy == 'updated_date'){
                var iconClass = (_sortOrder == 'asc') ? 'fa-sort-asc' : 'fa-sort-desc';
                $('#sort-updated-date-icon').removeClass('fa-sort fa-sort-asc fa-sort-desc').addClass(iconClass);
            }
        }
    });
}

function sortByUpdatedDate(){
    if(_sortBy == 'updated_date'){
        _sortOrder = (_sortOrder == 'desc') ? 'asc' : 'desc';
    } else {
        _sortBy = 'updated_date';
        _sortOrder = 'desc';
    }
    loadAjaxList(1);
}

loadAjaxList(1)

function editPatientRequestServiceDocument(id,documentId,patientId,patientType,agencyId,type="") {
    _RECORD_ID = patientId;
    _RECORD_TYPE  = patientType;
    _AGENCYID  = agencyId
    requestsServices(id, "edit", documentId)
    loadExistingDocument(documentId);
    getServicesOfDocument(documentId);
   $('#edit_document_completed_date').val($('#doc_completed_id'+documentId).text().trim());
   $('#edit_document_completed_date_error').html("");
   $('#updated_module_flag').val("");
   if(type !=""){
    $('#updated_module_flag').val(type);
   }
}

function deleteRecordDocument(recordId, documentId) {
    var url = _DELETE_DOCUMENT;

    $.confirm({
        title: 'Delete',
        columnClass: "col-md-6",
        content: 'Are you sure delete record?',
        buttons: {
            formSubmit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        async:false,
                        global:false,
                        url: _DELETE_DOCUMENT,

                        type: "POST",
                        data:{
                            'patient_id':recordId,
                            'document_id':documentId
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            loadAjaxList(1)
                        },
                        error:function(jqr){
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

function editDocumentServicesNew() {
    var documentService = $('#edit_document_service_id').val();
    var edit_document_completed_date = $('#edit_document_completed_date').val();
    var cnt = 0;
    $('#edit_document_service_id_error').html("");
    $('#edit_document_completed_date_error').html("");
    if (documentService.length == 0) {
        $('#edit_document_service_id_error').html("Please select Services");
        cnt = 1;
    }

    if(edit_document_completed_date ==""){
        $('#edit_document_completed_date_error').html("Please select Document Completed Date");
        cnt = 1;
    }

    if (cnt == 1) {

        return false;
    } else {
        var formData = new FormData($('#edit_document_service_form')[0]);
        formData.append('document_id', $('#edit_document_main_id').val())
        formData.append('patient_id', _RECORD_ID)
        formData.append('_token', _CSRF_TOKEN)
        formData.append('document_report_flag',"Yes")
        formData.append('edits_request_service_id', $('#edits_request_service_id').val())
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _UPDATE_DOCUMENT_SERVICES,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success(res.error_msg);
                loadAjaxList(1);

                $('#edit-exampleModal-services').modal('hide');
                closeEditDocumentServices();
            },
            error: function (jqhr) {
                showErrorAndLoginRedirection(jqhr);
            }

        })
    }
}


function exportCsv(){
    $('#loadertag1').removeClass('hide');
    $.ajax({
        url: _EXPORT_CSV,
        data:{

            'agency_id':$('#agency_fk').val(),
            'created_date':$('#appointment_date').val(),
            'patient_id':$('#patient_id').val(),
            'patient_type':$('#patient_type').val(),
            'internal_use':$('#archived:checked').val(),
            'document_created_by':$('#document_created_by').val(),
            'assign_document_user':$('#assign_document_user').val(),
            'completion_date':$('#completion_date').val(),
            'document_review_date':$('#document_review_date').val(),
            'review_document_user':$('#review_document_user').val(),
            'document_status':$('#pending_doc:checked').val(),
            'service_id':$('#service_id').val(),
            'mdo_tag':$('#mdo_tag').val(),
            'insurance_elg':$('#insurance_elg').val(),
            'medication_list':$('#medication_list').val(),
            'branch_id':$('#branch_id').val(),
            'mdo_source':$('#mdo_source').val(),
            'send_email':$('#send_email').val(),
            'sort_by':_sortBy,
            'sort_order':_sortOrder,
        },
        type: "GET",
        success:function(res){
            if(res == ""){
                toastr.error('Please check there is no data to export.');
                return false;
            }else{
                $('#loadertag1').addClass('hide');
                var blob = new Blob([res]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "document_report" + _DATE_TIME + ".csv";
                link.click();
            }

        }
    });
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

function viewServicesNew(documentId,patientId,patientType,agencyId){
    _RECORD_ID = patientId;
    _RECORD_TYPE  = patientType;
    _AGENCYID  = agencyId
    viewServices();
}

$(function() {
    let start = moment().startOf('day');
    let end = moment().endOf('day');

    $('.datepicker1').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        showCustomRangeLabel: true,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
        }
    });

    $('.datepicker1').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });


    $('#completion_date').daterangepicker({
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
    });

    $('#completion_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
    $('#document_review_date').daterangepicker({
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

        $('#document_review_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#document_review_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});


function refresh(){
    $('#agency_fk').val(null).trigger('change');
    $('#appointment_date').val('')
    $('#search-form')[0].reset();
    $('#service_id').val(null).trigger('change');
    clearTokenInputIfPresent("#review_document_user");
    clearTokenInputIfPresent("#assign_document_user");
    clearTokenInputIfPresent("#document_created_by");
    loadAjaxList(1)
}

function clearTokenInputIfPresent(selector) {
    const $input = $(selector);
    const tokenInput = $input.data("tokenInputObject");
    if (tokenInput && tokenInput.getTokens().length > 0) {
        tokenInput.clear();
    }
}
function closeEditDocumentServicesNew(){

}

function exportCsvWithoutService(){
    $('#loadertag1').removeClass('hide');
    $.ajax({
        url: _EXPORT_CSV_WITHOUT_SERVICE,
        data:{

            'agency_id':$('#agency_fk').val(),
            'created_date':$('#appointment_date').val(),
            'patient_id':$('#patient_id').val(),
            'patient_type':$('#patient_type').val(),
            'internal_use':$('#archived:checked').val()
        },
        type: "GET",
        success:function(res){
            $('#loadertag1').addClass('hide');
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "document_report" + _DATE_TIME + ".csv";
            link.click();
        }
    });
}

function loadExistingDocument(id){
    $.ajax({
        url: _SHOW_DOCUMENT_NAME,
        data:{

            'id':id,

        },
        type: "GET",
        success:function(res){
            $('#show_document_id').attr('src',res.data.url)

        }
    });
}

// function review(documentId){
//     $.ajax({
//         url: _DOCUMENT_SEND_REPORT_DETAILS_BY_ID,
//         data:{
//             'id':documentId,
//         },
//         type: "GET",
//         success:function(res){
//           $('#review-exampleModal-document-services').modal('show');
//           $('#show_review_document_id').attr('src',res.data.attachment);
//           $('#review_document_id').val(res.data.id);
//           $('#review_document_name').html(res.data.document_name);
//           $('#review_requested_id').html(res.data.request_service_id);
//           $('#review_attachment_service_id').html(res.data.services);

//           var document_completed_date = " - ";
//           if(res.data.document_completed_date !=null){
//             document_completed_date = moment(res.data.document_completed_date).format('MM/DD/YYYY hh:mm A')
//           }

//           var created_date = " - ";
//           if(res.data.created_date !=null){
//             created_date = moment(res.data.created_date).format('MM/DD/YYYY hh:mm A')
//           }

//           var assign_user = " - ";
//           if(res.data.assign_user_review_document && res.data.assign_user_review_document.id !=null){
//             assign_user = res.data.assign_user_review_document.full_name;
//           }

//           if(res.data.document_review_status =="Approved"){
//             var status ='<span class="badge badge-outline-success" style="color:#d76718;">Approved</span>';
//           }else if(res.data.document_review_status =="Rejected"){
//             var status ='<span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>';
//           }else{
//             var status ='<span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>';
//           }

//           $('#review_document_completion_date').html(document_completed_date);
//           $('#review_document_created_date').html(created_date+'<br>'+res.data.user_details.full_name);
//           $('#review_document_created_by').html(res.data.document_name);
//           $('input[name="pdf_status"]').prop("checked",false);
//           $('#review_document_status').html(status);
//           $('#review_document_assign_by').html(assign_user);

//           $('#remove_hide_show').addClass('hide');
//           $('.remove_hide_review_show').addClass('hide');
//           if(res.data.document_review_status ==null || res.data.document_review_status =='Pending'){
//             $('#remove_hide_show').removeClass('hide');
//           }else{
//                 $('.remove_hide_review_show').removeClass('hide');
//                 var review_user = "";
//                 var review_date = " - ";
//                 if(res.data.review_user_details && res.data.review_user_details.id !=null){
//                     review_user = res.data.review_user_details.full_name;
//                     review_date = moment(res.data.document_review_date).format('MM/DD/YYYY hh:mm A')
//                 }
//                 $('#review_over_document_review_by').html(review_date +'<br>'+review_user);
//                 $('#review_over_document_review_notes').html((res.data.status_note !="")?res.data.status_note:" - ");
//           }
//         }
//     });
// }

function saveFormBtn(){
    var status = $('input[name="pdf_status"]:checked').val();
    var pdf_status_reason = $("#pdf_status_reason").val().trim();
    $('#pdf_status_reason_error').html("");
    $('#pdf_status_error').html("");
   var  statusMessage ="reject";
    if(status == '1'){
        statusMessage ="approve";
    }

    if (!status) {
        $('#pdf_status_error').html("Please select Status");
        return false;
    }

    if (status === "0" && pdf_status_reason === '') {
        $('#pdf_status_reason_error').html("Please enter a Reason");
        return false;
    }
    let internal_use = '';
    let type = $('#modal_patient_type').val();
    if(type == 'Patient'){
        internal_use = $('input[name="internal_use"]:checked').val();
    }
    $.confirm({
        title:"Are you sure?",
        content:"Do you want "+statusMessage.charAt(0).toUpperCase() + statusMessage.slice(1)+" this document?",
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    $.ajax({

                        type: "POST",
                        url: _UPLOAD_DOCUMENT_REVIEW_BY_ID,
                        data: {
                            '_token':_CSRF_TOKEN,
                            'document_id':$('#review_document_id').val(),
                            'status':status,
                            'note':pdf_status_reason,
                            'internal_use_only': internal_use
                        },

                        success: function (res) {
                            toastr.success(res.error_msg);
                            loadAjaxList();

                            closeReviewModal();
                        },
                        error: function (jqhr) {
                            showErrorAndLoginRedirection(jqhr)
                        }

                    })
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {

                }
            }

        },
    });
}

function closeReviewModal(){
    $('#pdf_status_reason').val("");
    $('#review-document-services').modal('hide');
}


$("#document_created_by").tokenInput(_SEARCH_CREATED_BY_USER, {
    tokenLimit: 1,
    zindex: 9999,

    onAdd: function (item) {

    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto"
            });
        }, 500);
    }
});

$("#assign_document_user").tokenInput(_SEARCH_NYBEST_USER, {
    tokenLimit: 1,
    zindex: 9999,

    onAdd: function (item) {

    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto",
                "width":'100% !important'
            });
        }, 500);
    }
});

$("#review_document_user").tokenInput(_SEARCH_NYBEST_USER, {
    tokenLimit: 1,
    zindex: 9999,

    onAdd: function (item) {

    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto"
            });
        }, 500);
    }
});

$('input[name="pdf_status"]').click(function(e){
    var checked = $('input[name="pdf_status"]:checked').val();
    $('#rejected_notes_error').addClass('hide');
    if(checked == '0'){
        $('#rejected_notes_error').removeClass('hide');
    }
})

function commonModal(id){
    var text = $('#preason'+id).html();

    $.confirm({
        title: "Reason",
        content: "<p style='white-space:pre-line'>"+text+"</p>",
        type: 'blue',
        columnClass: 'col-md-9',
        buttons: {
            cancel: {
                text: 'Cancel',
                action: function () {

                }
            }
        }
    });
}

function viewDocumentDetails(documentId){
    $.ajax({
        url: _DOCUMENT_SEND_REPORT_DETAILS_BY_ID,
        data:{
            'id':documentId,
        },
        type: "GET",
        success:function(res){
          $('#view-exampleModal-document-services').modal('show');
          $('#show_over_review_document_id').attr('src',res.data.attachment);
          $('#review_over_document_id').val(res.data.id);
          $('#review_over_document_name').html(res.data.document_name);
          $('#review_over_requested_id').html((res.data.request_service_id !="")?res.data.request_service_id:" - ");
          $('#review_over_attachment_service_id').html((res.data.services !="")?res.data.services:" - ");

          var document_completed_date = " - ";
          if(res.data.document_completed_date !=null){
            document_completed_date = moment(res.data.document_completed_date).format('MM/DD/YYYY')
          }

          var created_date = " - ";
          if(res.data.created_date !=null){
            created_date = moment(res.data.created_date).format('MM/DD/YYYY hh:mm A')
          }

          var review_user = "";
          var review_date = " - ";
          if(res.data.review_user_details && res.data.review_user_details.id !=null){
            review_user = res.data.review_user_details.full_name;
            review_date = moment(res.data.document_review_date).format('MM/DD/YYYY hh:mm A')
          }

          var assign_user = " - ";
          if(res.data.assign_user_review_document && res.data.assign_user_review_document.id !=null){
            assign_user = res.data.assign_user_review_document.full_name;
          }

          if(res.data.document_review_status =="Approved"){
            var status ='<span class="badge badge-outline-success" style="color:#d76718;">Approved</span>';
          }else if(res.data.document_review_status =="Rejected"){
            var status ='<span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>';
          }else{
            var status ='<span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>';
          }

          $('#review_over_document_completion_date').html(document_completed_date);
          $('#review_over_document_created_date').html(created_date +'<br>'+res.data.user_details.full_name);

          $('#review_over_document_status').html(status);
          $('#review_over_document_assign_by').html(assign_user);
          $('#review_over_document_review_by').html(review_date +'<br>'+review_user);
          $('#review_over_document_review_notes').html((res.data.status_note !="")?res.data.status_note:" - ");

        }
    });
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function getServicesOfDocument($doc_id){
    $.ajax({
        url: _GET_SERVICES_OF_DOCUMENT,
        data:{
            'document_id':$doc_id,
        },
        type: "GET",
        success:function(res){
            values = [];
            $.each(res.data, function(index, value) {
                values.push(value.id);
            });
            $('#edit_document_service_id').val(values).trigger('change');
        }
    })
}

function review(documentId){
    let docNameHtml = `<a class="show-class" onclick="setBasicDetails()"><i class="fa fa-edit ml-2"></i></a>`;
    $('#show_review_document_id').attr('src','');
    $.ajax({
        url: _DOCUMENT_SEND_REPORT_DETAILS_BY_ID,
        data:{
            'id':documentId,
        },
        type: "GET",
        success:function(res){
          $('#review-document-services').modal('show');
          $('#show_review_document_id').attr('src',res.data.attachment);
          $('#review_document_id').val(res.data.id);
          $('#review_document_name').html(res.data.document_name + docNameHtml);
          $('#review_requested_id').html(res.data.request_service_id);
          $('#review_attachment_service_id').html(res.data.services);

            $('#sign-url').attr('style','');
            if (res.data.templete_id == null) {
                $('#sign-url').attr('style','display:none !important');
            }

          var document_completed_date = " - ";
          if(res.data.document_completed_date !=null){
            document_completed_date = moment(res.data.document_completed_date).format('MM/DD/YYYY hh:mm A')
          }

          var created_date = " - ";
          if(res.data.created_date !=null){
            created_date = moment(res.data.created_date).format('MM/DD/YYYY hh:mm A')
          }

          var assign_user = " - ";
          if(res.data.assign_user_review_document && res.data.assign_user_review_document.id !=null){
            assign_user = res.data.assign_user_review_document.full_name;
          }

          if(res.data.document_review_status =="Approved"){
            var status ='<span class="badge badge-outline-success" style="color:#d76718;">Approved</span>';
          }else if(res.data.document_review_status =="Rejected"){
            var status ='<span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>';
          }else{
            var status ='<span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>';
          }

          $('#review_document_completion_date').html(document_completed_date);
          $('#review_document_created_date').html(created_date+'<br>'+res.data.user_details.full_name);
          $('#review_document_created_by').html(res.data.document_name);
          $('input[name="pdf_status"]').prop("checked",false);
          $('#review_document_status').html(status);
          $('#review_document_assign_by').html(assign_user);

          $('#remove_hide_show').addClass('hide');
          $('.remove_hide_review_show').addClass('hide');
          $('#review_over_document_review_by_model').html('-');
          $('#review_over_document_review_notes_model').html(" - ");
          if(res.data.document_review_status ==null || res.data.document_review_status =='Pending'){
            $('#remove_hide_show').removeClass('hide');
          }else{
                $('.remove_hide_review_show').removeClass('hide');
                var review_user = "";
                var review_date = " - ";

                if(res.data.review_user_details && res.data.review_user_details.id !=null){

                    review_user = res.data.review_user_details.first_name+' '+res.data.review_user_details.last_name;
                    review_date = moment(res.data.document_review_date).format('MM/DD/YYYY hh:mm A')
                }

                $('#review_over_document_review_by_model').html(review_date +'<br>'+review_user);
                $('#review_over_document_review_notes_model').html((res.data.status_note !="")?res.data.status_note:" - ");
          }
          $('#review_agency_name').html(res.data.patient_details.agency_detail.agency_name);
          $('#review_portal_id').html('<a href="'+PATIENT_URL+'/'+res.data.patient_id+'" target="_blank" class="no-decoration">'+res.data.patient_id+'</a>');
          $('#review_patient_name').html(res.data.patient_details.first_name+' '+res.data.patient_details.last_name);
          $('#review_gender_name').html(res.data.patient_details.gender);
          $('#review_birth_date').html(moment(res.data.patient_details.dob).format('MM/DD/YYYY'));
          $('#review_ssn').html(res.data.patient_details.ssn);
          $('#review_document_id').val(documentId);
          $('#review_doc_name').val(res.data.document_name);
          $('.internal_use_div').addClass('hide');
          $('#modal_patient_type').val(res.data.patient_details.type);
          if(res.data.patient_details.type == 'Patient'){
            $('.internal_use_div').removeClass('hide');
            $('#internal_use').attr('checked',false);
            if(res.data.internal_use == 1){
                $('#internal_use').attr('checked',true);
            }
          }
        }
    });
}

function setBasicDetails(){
    var documentName = $('#review_document_name').text().trim();
    if(documentName == "-"){
        documentName = "";
    }
    html = `<div class="row"><input type="text" class="form-control col-md-10 mr-1" placeholder="Document Name" id="edit_document_name" value="${documentName}"><a class="show-class" onclick="closeBasicDetails()"><i class="fa fa-close mr-1 mt-2"></i></a><a class="show-class" onclick="saveBasicDetails()"><i class="fa fa-save mt-2"></i></a><span class="error" id="doc_name_error"></span></div>`;
    $('#review_document_name').html(html);
}

function saveBasicDetails(){
    let document_id =  $('#review_document_id').val();
    let doc_name =  $('#edit_document_name').val();
    if(doc_name.trim() == ''){
        $('#doc_name_error').html('Please enter Doc Name');
        return false;
    }
    $.ajax({
        type: "GET",
        url: SAVE_DOC_NAME,
        data: {
            'document_id' : document_id,
            'document_name' : doc_name
         },
        success: function (res) {
           if(res.status == 1){
                toastr.success(res.error_msg);
                $('#review_doc_name').val(doc_name)
                closeBasicDetails();
           }
        }
    })
}

function closeBasicDetails(){
    let doc_name =  $('#review_doc_name').val();
    let docNameHtml = `${doc_name}<a class="show-class" onclick="setBasicDetails()"><i class="fa fa-edit ml-2"></i></a><a class="hide-class" onclick="setBasicDetails()">`;
    $('#review_document_name').html(docNameHtml);
}

function openSignPopup(){
    let document_id =  $('#review_document_id').val();
    href = DOC_URL+'?id='+document_id+'&type='+uniqId;
    $('#sign-url').attr('href',href);
    $('#sign-url')[0].click();
}

function refreshDoc()
{
    let document_id =  $('#review_document_id').val();
    review(document_id);
}

function refreshDocList(){
    $('#agency_fk').val(null).trigger('change');
    $('#appointment_date').val('')
    $('#search-form')[0].reset();
    $('#service_id').val(null).trigger('change');
    clearTokenInputIfPresent("#created_by");
    loadAjaxList(1);
}

$('input[name="pdf_status"]').change(function(){
    if($('#modal_patient_type').val() == 'Patient'){
        if($(this).val() == 1){
            $('.internal_use_div').addClass('hide');
            $('.internal_use_div').removeClass('show');
        }else{
            $('.internal_use_div').addClass('show');
            $('.internal_use_div').removeClass('hide');
        }
    }
});

function exportCsv2(){
    $('#loadertag1').removeClass('hide');
    $.ajax({
        url: _EXPORT_CSV_TWO,
        data:{

            'agency_id':$('#agency_fk').val(),
            'created_date':$('#appointment_date').val(),
            'patient_id':$('#patient_id').val(),
            'patient_type':$('#patient_type').val(),
            'internal_use':$('#archived:checked').val(),
            'document_created_by':$('#document_created_by').val(),
            'assign_document_user':$('#assign_document_user').val(),
            'completion_date':$('#completion_date').val(),
            'document_review_date':$('#document_review_date').val(),
            'review_document_user':$('#review_document_user').val(),
            'document_status':$('#pending_doc:checked').val(),
            'service_id':$('#service_id').val(),
            'mdo_tag':$('#mdo_tag').val(),
            'insurance_elg':$('#insurance_elg').val(),
            'medication_list':$('#medication_list').val(),
            'branch_id':$('#branch_id').val(),
            'mdo_source':$('#mdo_source').val(),
            'send_email':$('#send_email').val(),
            'sort_by':_sortBy,
            'sort_order':_sortOrder,
        },
        type: "GET",
        success:function(res){
            if(res == ""){
                toastr.error('Please check there is no data to export.');
                return false;
            }else{
                $('#loadertag1').addClass('hide');
                var blob = new Blob([res]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "document_report" + _DATE_TIME + ".csv";
                link.click();
            }

        }
    });
}

function resetBranchDropdown(){
    $('#branch_id').html('<option value="">Select Branch</option>');
}

loadBranchDropdown();

function loadBranchDropdown(){
    if(typeof _GET_BRANCHES_BY_AGENCY_SERVICES === 'undefined'){
        return;
    }

    $.ajax({
        type: "GET",
        url: _GET_BRANCHES_BY_AGENCY_SERVICES,
        success: function(res){
            if(res.status && res.data && res.data.length > 0){
                var html = '<option value="">Select Branch</option>';
                $.each(res.data, function(i, branch){
                    html += '<option value="'+ branch.id +'">'+ branch.branch_name +'</option>';
                });
                $('#branch_id').html(html);
            } else {
                resetBranchDropdown();
            }
        },
        error: function(){
            resetBranchDropdown();
        }
    });
}