function loadData(page =1){
    $('.loader-sec').show();
    $.ajax({
        url: _AJAX_LIST+"?page=" + page,
        type: "GET",
        data: {
        },
        success: function(res) {
          $('.loader-sec').hide();
            $('.table_response_id').html("");
            $('.table_response_id').html(res)
        }
    })
    return false;
}

loadData();

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    loadData(page);
});
