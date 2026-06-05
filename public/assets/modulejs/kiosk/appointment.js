loadAppointment(1);
function loadAppointment(page){
    $('#ajax_response_id').html("")
    $('.hideClass').removeClass('d-none');
    var search = $('#search_data').val();
    var status = $('#status').val();
    var location = $('#location').val();
    var created_date = $('#created_date').val();
    $.ajax({
        type: "GET",
        url: _KIOSK_AJAX,
        data: {
             'page':page,
             'search': search,
             'status': status,
             'location': location,
             'date' : created_date
        },
        success: function (res) {
           $('.hideClass').addClass('d-none');
           if (res.status) {
                $('#ajax_response_id').html(res.html); // ✅ Blade rendered HTML
            }
        },
    })
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function refresh(){
    $('#created_date').val('');
    $('#search_data').val('');
    $('#status').val('').change();
    $('#location_id').val(null).trigger('change');
    loadAppointment(1);
}

$('#created_date').datepicker({
    dateFormat: 'mm/dd/yy',
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});