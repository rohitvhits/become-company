@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .actions {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Bulk SMS List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                @can('bulk-sms-cdpap-caregiver-save')
                         <a onclick="saveBulk()" class="btn btn-primary cust-right-btn"><i
                                 class="mdi mdi-plus"></i>Add Bulk SMS</a>
                     @endcan

                    
                </div>
            </div>
        </div>
        <hr />
        
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Message</th>
                                    <th>Created Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="response_requested_id">
                    
                </span>



            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>


@include('include/footer')

<script>
    function ajaxList(page){
        $('.shimmer_id').removeClass('hide')
        $('#response_requested_id').html("")
        $('.location-wise-data-loader').attr('style', 'display:flex');
        $.ajax({
            type:"get",
            url:"{{ url('bulk-sms-cdpap-caregiver/ajax-list')}}?page="+page,
            success:function(res){
                $('.shimmer_id').addClass('hide')
                $('#response_requested_id').html(res)
                $('.location-wise-data-loader').attr('style', 'display:none');
            }
        })

        return false;
    }
    ajaxList(1);

    $('body').on('click', '.pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        ajaxList(page);
    });

    function saveBulk(){
        $.confirm({
            title: 'Create Bulk SMS',
            columnClass: 'col-md-6',
            content: '' +
                '<form action="" id="form_bulk_sms_id" class="formName">' +
                '<div class="form-group">' +
                '<label for="message" class="col-form-label">Message<span class="text-danger error">*</span>:</label>' +
                '<textarea class="form-control" name="message" placeHolder="Enter Message" id="message_id" rows="10" cols="10"></textarea><span id="message_error" class="text-danger error"></span>' +
                '</div>' +
                '</form>',
            type: 'blue',
            buttons: {
                submit: {
                    text: 'Save',
                    btnClass: 'btn-blue',
                    action: function () {
                        let name = this.$content.find('#message_id').val();
                        if (!name) {
                            $('#message_error').html("Please enter Message");
                            return false;
                        }
                        
                        var formData = new FormData($('#form_bulk_sms_id')[0]);
                        formData.append('_token',"{{ csrf_token()}}");

                        $.ajax({
                            async:false,
                            global:false,
                            type:"POST",
                            url:"{{ url('bulk-sms-cdpap-caregiver/save-bulk-sms') }}",
                            data:formData,
                            processData: false,
                            contentType: false,
                            success:function(res){
                                toastr.success(res.error_msg)
                                ajaxList(1);
                            },
                            error:function(xhr, status, error){
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {
                        console.log("Cancelled!");
                    }
                }
            }
        });
    }
   
   function viewMessage(id){
    var notes = $('#notes_'+id).html();
  
        $.confirm({
                title: 'View Message',
                columnClass: 'col-md-6',
                content:'<div style="white-space:pre-line">'+notes+'</div>',
                type: 'blue',
                buttons: {
                    cancel: {
                        text: 'Cancel',
                        action: function () {
                            console.log("Cancelled!");
                        }
                    }
                }
        });
   }
</script>