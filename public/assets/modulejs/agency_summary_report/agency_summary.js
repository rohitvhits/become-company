function getAjaxAgencySummaryData(page = 1) {
    $('.loader-sec').show();
    $.ajax({
        type: "GET",
        url: _AJAX_DATA,
        data: {
            'page': page,
        },
        success: function (response) {
            console.log(response);
            $('#agency_summary_html').html("")
            $('#agency_summary_html').html(response);
            $('.loader-sec').hide();
        }
    })
    return false;
}

getAjaxAgencySummaryData();

$(document).on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    getAjaxAgencySummaryData(page);
});

function export_data() {
    $('.loader-sec').show();
    var temp1 = _EXPORT_DATA;
    $('#test_user').attr("style", '');
    $('#test_user').attr("href", temp1);
    $('.loader-sec').hide();
}