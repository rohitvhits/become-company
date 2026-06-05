$("#availibility_followup_date").datepicker({
    zIndex:999999,
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});
function getAvaibilityFollowupDate(){
    var availibilityFollowupDate = $('#availibility_followup_date').val();
    var cnt =0;
    if(follow_date_id ==''){
        $('#avaibility_followup_date_error').html("Avaibility Followup date is required");
        cnt =1;
    }
    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            type: "POST",
            url:_PATIENT_AVAILABILITY_FOLLOWUP_DATE,
            data: {
                '_token': _CSRF_TOKEN,
                'id': _RECORD_ID,
                'availibility_followup_date':availibilityFollowupDate
            },
            success:function(res){
                $('#'+_AGENCYID+'_availability_followup_date').html(availibilityFollowupDate);
                $('#close_availibility_followup_date').click();
                toastr.success(res.error_msg);
            },
            error:function(jqr){

                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}