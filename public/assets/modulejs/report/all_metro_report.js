function getAjaxData(page = 1) {
    $('.loader-sec').show();
    var agency_id = $('select[name="agency_id"]').val();
    var location_branch = $('#location_branch').val();
    var full_name = $('#full_name').val();
    var created_date = $('#created_date').val(); // ✅ add

    $.ajax({
        type: "GET",
        url: _All_METRO_DATA,
        data:{
            'page':page,
            'agency_id': agency_id,
            'location_branch': location_branch,
            'full_name': full_name,
            'created_date': created_date // ✅ send to backend
        },
        success: function (response) {
            $('#all_metro_report_html').html("")
            $('#all_metro_report_html').html(response);
            $('.loader-sec').hide();
        }
    })
    return false;
}
getAjaxData();
$(document).on("click", ".searchAppoinment", function() {
    getAjaxData();

});

function reset_data(){
    $('#formsubmit')[0].reset();
    getAjaxData(1);
}

$(document).on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getAjaxData(page);
});

function export_data() {
    $('.loader-sec').show();
    var agency_fk = $('#agency_fk').val();
    var agency_id = $('select[name="agency_id"]').val();
    var location_branch = $('#location_branch').val();
    var full_name = $('#full_name').val();
    var created_date = $('#created_date').val(); // ✅ add

    var temp1 = _All_METRO_EXPORT_DATA+'?agency_fk=' + agency_fk+'&agency_id='+agency_id+'&location_branch='+location_branch+'&full_name='+full_name+'&created_date='+created_date;
    $('#test_user').attr("style", '');
    $('#test_user').attr("href", temp1);
    $('.loader-sec').hide();
}

$(document).on('click', '.read-more-btn', function () {
    var parent = $(this).closest('td');
    parent.find('.short-text').toggleClass('d-none');
    parent.find('.full-text').toggleClass('d-none');
    if ($(this).text() === 'Read More') {
        $(this).text('Read Less');
    } else {
        $(this).text('Read More');
    }
});