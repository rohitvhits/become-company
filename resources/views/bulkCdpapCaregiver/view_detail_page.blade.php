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
            <h5 class="mb-0 font-weight-bold">View Detail By #{{ $id}}</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                <div id="pendingdata_new"></div>
                <a href="javascript:sendBulkSMS()" class="btn btn-primary cust-right-btn">Send SMS</a>
                    <a href="{{ url('bulk-sms-cdpap-caregiver')}}" class="btn btn-primary cust-right-btn">Back</a>
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
                                    <th>Patient ID</th>
                                    <th>Mobile</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                 
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="7"></td>
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
            url:"{{ url('bulk-sms-cdpap-caregiver/view-ajax-list')}}?page="+page,
            data:{
                'bulk_sms_id':'{{ $id}}'
            },
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

    function sendBulkSMS(){

    }

    var pendingData = 0;
    function sendBulkSMS() {
        var cnt = 1;
        var dataPushCount = 0;
        $.ajax({
            url: "{{ url('fetch-pending-sms-data')}}",
            type: "get",

            success: function (res) {
                pendingData = res;
                if (pendingData.data.length > 0) {
                    $('#pendingdata_new').html('<span class="badge badge-danger">Pending Data: ' + pendingData.data.length + '</span>');
                    fetchAllPendingSMS(0);
                } else {
                    $('#pendingdata').html('');
                }

            }
        });
    }
    function fetchAllPendingSMS(index) {

        const element = pendingData.data[index];
            $('#pendingdata_new').html('<span class="badge badge-success">Synced data: '+index+'/' + pendingData.data.length + '</span>');
        $.ajax({
            url: "{{ url('send-fetch-sms')}}",
            type: "post",
            data: {
                'id': element.bulk_sms_cdpap_caregiver_id,
                'detail_id':element.id,
                '_token': "{{ csrf_token()}}"
            },
            success: function (res) {
                index = index + 1;
                
                if (index < pendingData.data.length) {
                    fetchAllPendingSMS(index);
                } else {
                        $('#pendingdata_new').html('<span class="badge badge-success">Synced data: ' + pendingData.data.length + '</span>');
                }
            }
        });


    }
</script>