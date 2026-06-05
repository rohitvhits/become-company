@include('include/header')
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<style type="text/css">
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 {
        background-color: #fff;
    }

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-card1 {
        margin-bottom: 20px;
    }

    .search-card1 .form-group {
        margin-bottom: 0.5rem;
    }

    .search-card1 label {
        margin-bottom: 0;
    }

    .search-card1 .card-body {
        padding-bottom: 10px;
    }

    .search-card1 input[type=text] {
        border-radius: 4px;
        border-color: #aaa;
    }

    .srch-icon {
        padding: 0 !important;
        width: 40px;
        height: 40px;
    }

    .hide {
        display: none;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Enquiry List</h5>
            <div class="page-rightbtns">
                <div>
                    @can('enquiry-add')
                        <a href="{{ url('enquiry/create') }}"
                            class="btn btn-primary btn-rounded btn-fw btn-sm showModalInsurance"><i class="mdi mdi-plus"> </i>
                            Add
                            Enquiry</a>
                   @endcan
                </div>
            </div>
        </div>

        <div class="col-12 grid-margin-top">
           
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <span id="response_id" ></span>
                    </div>
                </div>
                    
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
@include('enquiry/_partial/send_replay')
@include('enquiry/_partial/change_status')
    @include('include/footer')
    <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js')}}"></script>
    <script>
        $(function(e){
            ajaxList(1);
        })

        function ajaxList(page){
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('enquiry-ajax-list')}}",
                data: {
                    "page": page
                },
                success:function(res){
                    $('#response_id').html("");
                    $('#response_id').html(res);
                }
            });
        }

        function common(id,type=""){
            if(type !=""){
                var content = $('#log'+id).text();
            }else{
                var content = $('#'+id).text();
            }
            
            $.confirm({
                title: 'Message',
                content: "<p style='white-space:pre-line'>"+content+"</p>",
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

        function sendReply(id){
            $('#enquiry_id').val(id)
            $('.subject_error').html('');
            $('.message_id_error').html("");
            $('#insuranceAdd')[0].reset();
            $('#send_reply_modal').modal('show');
        }

        $('#sendReplaySubmit').click(function(e){
            var subject = $('#subject').val();
            var message_id = $('#message_id').val();

            var cnt =0;
            $('.subject_error').html('');
            $('.message_id_error').html("");

            if(subject.trim() ==''){
                $('.subject_error').html('Please enter Subject');
                cnt =1;
            }

            if(message_id.trim() ==''){
                $('.message_id_error').html('Please enter Message');
                cnt =1;
            }

            if(cnt ==1){
return false;
            }else{
                
                $.ajax({
                    async: false,
                    global: false,
                    type: "post",
                    url: "{{ url('enquiry-reply')}}",
                    data: {
                        "enquiry_id": $('#enquiry_id').val(),
                        'subject':$('#subject').val(),
                        'message':$('#message_id').val(),
                        '_token':"{{ csrf_token()}}"
                    },
                    success:function(res){
                      
                        toastr.success(res.error_msg)
                        ajaxList(1);
                        $('#insuranceAdd')[0].reset();
                        $('.close').click();
                    },
                    error:function(jqr){
                        toastr.error(jqr.responseJSON.error_msg)
                    }
                });
            }
        })

        function viewReplyLog(id){
            $.confirm({
                title: "View Reply Log",
                type: 'blue',
                columnClass: 'col-md-9',
                content: function () {
                    var self = this;

                    return $.ajax({
                        url: '{{ url("view-enquiry-reply-log")}}',
                        dataType: 'json',
                        method: 'get',
                        data:{
                            'id':id
                        }
                    }).done(function (response) {
                        var response = response.data;
                        var htmlRes ="";
                        var nc =1;
                        
                        $.each(response,function(i,v){
                            htmlRes +=`<span id="log${v.id}" style="display:none">${v.messages}</span><tr>
                                <td>${nc++}</td>
                                <td>${v.email}</td>
                                <td>${v.subject}</td>
                                <td><a onclick="common(${v.id},'view_log')">${v.message}</a></td>
                                <td>${moment(v.created_at).format('MM/DD/YYYY hh:mm A')}</td>
                                <td>${v.user_details.first_name+" "+v.user_details.last_name}</td>
                            </tr>`
                        })

                        var contentHtml =`<table class="table table-bordered">
                            <thead>
                                <th>#</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Created Date</th>
                                <th>Created By</th>
                            </thead>
                            <tbody>
                                ${htmlRes}
                            </tbody>
                        </table>`
                        self.setContent(contentHtml);
                    }).fail(function () {
                        self.setContent('Something went wrong.');
                    });
                },
                buttons: {
                    cancel: {
                        text: 'Cancel',
                        action: function () {
                            // Do nothing
                        }
                    }
                }
            });
        }

        function changeStatus(id){
            $('#change_enquiry_id').val(id)
            $('.status_id_error').html('');
          
            $('#change_status_form_modal').modal('show');
        }

        $('#changeStatusId').click(function(e){
            var status_id = $('#status_id').val();
        
            var cnt =0;
            $('.status_id_error').html('');
            
            if(status_id ==''){
                $('.status_id_error').html('Please select Status');
                cnt =1;
            }

            if(cnt ==1){
                return false;
            }else{
                
                $.ajax({
                    async: false,
                    global: false,
                    type: "post",
                    url: "{{ url('change-enquiry-status')}}",
                    data: {
                        "enquiry_id": $('#change_enquiry_id').val(),
                        'status':$('#status_id').val(),
                        '_token':"{{ csrf_token()}}"
                    },
                    success:function(res){
                      
                        toastr.success(res.error_msg)
                        ajaxList(1);
                        $('#insuranceAdd')[0].reset();
                        $('.close').click();
                    },
                    error:function(jqr){
                        toastr.error(jqr.responseJSON.error_msg)
                    }
                });
            }
        })
        function clearSendReplay(){
            
        }
        </script>