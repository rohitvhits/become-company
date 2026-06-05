function mdOrders(page=1){
    $('#mqOrderLoader').attr('style', '');
    $("#mqorder_reponse_id").html("");
    $("#mqorder_reponse_id").html('<tr><td colspan="8">Loading...</td></tr>');
    $.ajax({
        url: _MQ_ORDER_LIST+"?page=" + page,
        type: "get",
       data:{
        'id':_RECORD_ID
       },
        success: function (response) {
            $('#mqOrderLoader').attr('style', 'display:none');
            $('#mqorder_reponse_id').html("")
            $('#mqorder_reponse_id').html(response);
        }
    });
}

$(document).on('click', '.mqOrder_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    loadMDOrderReportList(page);
});

function createMDOrders(doc_id = ''){
    $('#mq_order_form_submit_id')[0].reset();
    $('#mq_order_start_date_error').html("")
    $('#mq_order_end_date_error').html("")
    $('#mq_order_document_error').html("");
    $('#show_create_mq_order_loader').addClass('hide');
    $('#update_mq_order_text').text(" ")
    $('#update_mq_order_text').text("Create MD Order")
    if(doc_id){
        $('#document-div').css('display','none');
    }else{
        $('#document-div').css('display','block');
    }
    $.ajax({
        url: _GET_DOCUMENT_LIST_BY_PATIENT_ID,
        type: "get",
        data:{
            'id':_RECORD_ID
        },
        success: function (response) {
            var json = response.data;
            $('#mq_order_document_id').html("");
            var mqOrderDocumentDropdown = "";
            mqOrderDocumentDropdown ="<option value=''>Select Document</option>"
            if(response.data.length !=0){
                mqOrderDocumentDropdown ="<option value=''>Select Document</option>"
                $.each(json,function(i,v){
                    mqOrderDocumentDropdown +='<option value="'+v.id+'">'+v.document_name+'</option>';
                })
            }

            $('#mq_order_document_id').html(mqOrderDocumentDropdown);
            if(doc_id != ''){
                $('#mq_order_document_id_hidden').val(doc_id);
            }
        }
    });

  $('#button_mq_orderId').attr('onclick','storeMDOrders()')
}

function storeMDOrders(){
    var mq_order_start_date = $('#mq_order_start_date').val();     
    var mq_order_end_date = $('#mq_order_end_date').val();     
    var mq_order_document = $('#mq_order_document_id').val();  
    if(mq_order_document == ''){
        var mq_order_document = $('#mq_order_document_id_hidden').val();
    }
    var cnt =0;

    $('#mq_order_start_date_error').html("");
    $('#mq_order_end_date_error').html("");
    $('#mq_order_document_error').html("");

    if(mq_order_start_date ==""){
        $('#mq_order_start_date_error').html("Please select Start Date");
        cnt =1;
    }

    if(mq_order_end_date ==""){
        $('#mq_order_end_date_error').html("Please select End Date");
        cnt =1;
    }

    if(mq_order_document ==""){
        $('#mq_order_document_error').html("Please select Document");
        cnt =1;
    }
    if(cnt ==1){
      
        return false;
        
    }else{
        $('#show_create_mq_order_loader').removeClass('hide');
        var formData = new FormData($('#mq_order_form_submit_id')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('patient_id',_RECORD_ID);
        
        $.ajax({
            url: _SAVE_MQ_ORDER,
            type: "post",
            data:formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg) 
                mdOrders();
                $('#show_create_mq_order_loader').addClass('hide');
                $('#exampleModal-create-mq-order').modal('hide')
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg)
                $('#show_create_mq_order_loader').addClass('hide');
            }
        });
        
    }
}

function editMDOrder(id){
    createMDOrders();
    $.ajax({
        url: _EDIT_MQ_ORDER,
        type: "get",
        data:{
            'id':id
        },
        success: function (response) {
            var json = response.data;
            $('#update_mq_order_text').text(" ")
           $('#update_mq_order_text').text("Edit MD Order")
           $('#mq_order_start_date').val(moment(response.data.start_date).format('MM/DD/YYYY'));
           $('#mq_order_end_date').val(moment(response.data.end_date).format('MM/DD/YYYY'));
           $('#mq_order_document_id').val(response.data.document_id).trigger("change")
           $('#mq_order_id').val(response.data.id);
           $('#exampleModal-create-mq-order').modal('show')
           $('#button_mq_orderId').attr('onclick','updateMDOrders()')
        }
    });
}

function updateMDOrders(){
    var mq_order_start_date = $('#mq_order_start_date').val();     
    var mq_order_end_date = $('#mq_order_end_date').val();     
    var mq_order_document_id = $('#mq_order_document_id').val();     
    var cnt =0;
    $('#show_create_mq_order_loader').removeClass('hide');
    $('#mq_order_start_date_error').html("");
    $('#mq_order_end_date_error').html("");
    $('#mq_order_document_error').html("");

    if(mq_order_start_date ==""){
        $('#mq_order_start_date_error').html("Please select Start Date");
        cnt =1;
    }

    if(mq_order_end_date ==""){
        $('#mq_order_end_date_error').html("Please select End Date");
        cnt =1;
    }

    if(mq_order_document_id ==""){
        $('#mq_order_document_error').html("Please select Document");
        cnt =1;
    }

    if(cnt ==1){
        $('#show_create_mq_order_loader').removeClass('hide');
        return false;
        
    }else{
        var formData = new FormData($('#mq_order_form_submit_id')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('id',$('#mq_order_id').val());
        formData.append('patient_id',_RECORD_ID);
        $.ajax({
            url: _UPDATE_MQ_ORDER,
            type: "post",
            data:formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg) 
                mdOrders();
                $('#show_create_mq_order_loader').addClass('hide');
                $('#exampleModal-create-mq-order').modal('hide')
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg)
                $('#show_create_mq_order_loader').removeClass('hide');
            }
        });
        
    }
}

function deleteMDOrder(id){
  $.confirm({
        title:"Are you sure?",

        type: 'blue',
        
        content:"you want to delete this MD Order",
        buttons: {
            submit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function () {
            
                    $.ajax({
                        async:false,
                        global:false,
                        type:"post",
                        url:_DELETE_MQ_ORDER,
                        data:{
                            '_token':_CSRF_TOKEN,
                            'id':id,
                            'patient_id':_RECORD_ID
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            mdOrders();
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg)
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {
                
                }
            }
        }
    });
}

if(typeof(_MQ_LIST) !='undefined'){
    loadMDOrderReportList();
}

function loadMDOrderReportList(page=1){
    $('#loadertag1').removeClass('hide');
   
    $.ajax({
        url: _MQ_LIST+"?page=" + page,
        type: "get",
       data:{
        'id':$('#patient_id').val(),
        'start_date':$('#start_date').val(),
        'end_date':$('#end_date').val(),
        'agency_fk':$('#agency_fk').val(),
       },
        success: function (response) {
            $('#loadertag1').addClass('hide');
            $('#response_mqorder_id').html("")
            $('#response_mqorder_id').html(response);
        }
    });
}

function refresh(){
    $('#patient_id').val('') 
    $('#start_date').val('') 
    $('#end_date').val('') 
    $('#agency_fk').val('').trigger("change")
    loadMDOrderReportList();
}

function exportCsv(){
    $('#loadertag1').removeClass('hide');
   
    $.ajax({
        url: _MQ_EXPORT_CSV,
        type: "get",
       data:{
        'id':$('#patient_id').val(),
        'start_date':$('#start_date').val(),
        'end_date':$('#end_date').val(),
        'agency_fk':$('#agency_fk').val(),
       },
        success: function (response) {
            $('#loadertag1').addClass('hide');
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var form_name = "mdOrder_"+_DATE_TIME;
            link.download = form_name + ".csv";
            link.click();
        }
    });
}