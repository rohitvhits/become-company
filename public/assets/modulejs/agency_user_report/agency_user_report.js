function getAjaxAgencyReportData(page=1){
    $('.loader-sec').show();
    var first_name = $('#first_name').val();
    var last_name = $('#last_name').val();
    var email = $('#email').val();
    var record_access = $('select[name="record_access"]').val();
    var agency_id = $('select[name="agency_id"]').val();

    $.ajax({
        type: "GET",
        url: _AGENCY_DATA,
        data:{
            'page':page,
            'first_name':first_name,
            'last_name':last_name,
            'email':email,
            'record_access':record_access,
            'agency_id':agency_id
        },
        success: function (response) {
            $('#agency_report_html').html("")
            $('#agency_report_html').html(response);
            $('.loader-sec').hide();
        }
    })
    return false;
}
getAjaxAgencyReportData();
$(document).on("click", ".searchAppoinment", function() {
    getAjaxAgencyReportData();

});

function reset_data(){
    $('#formsubmit')[0].reset();
    getAjaxAgencyReportData(1);
}

$(document).on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getAjaxAgencyReportData(page);
});

function export_data() {
    $('.loader-sec').show();
    var agency_fk = $('#agency_fk').val();
    var login_type = "Agency Rep";
    var user_type = "Agency";
    var first_name = $('#first_name').val();
    var last_name = $('#last_name').val();
    var email = $('#email').val();
    var record_access = $('select[name="record_access"]').val();
    var agency_id = $('select[name="agency_id"]').val();

    var temp1 = _AGENCY_EXPORT_DATA+'?agency_fk=' + agency_fk + '&login_type=' + login_type +
        '&user_type=' + user_type + '&first_name=' + first_name + '&last_name=' + last_name + '&email=' + email+'&record_access='+record_access+'&agency_id='+agency_id;
    //  var temp = temp1.replace("http://", "https://");
    $('#test_user').attr("style", '');
    $('#test_user').attr("href", temp1);
    $('.loader-sec').hide();
} 