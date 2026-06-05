$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadENotificationList(1);
});

function loadENotificationList(page) {
    $('#loader').attr('style','display:');
    $.ajax({
        url: _NOTIFICATION_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
            $('#loader').attr('style','display:none');
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadENotificationList(page);
});

function markasReadAll(){
    $('#loader').attr('style','display:');
     $('#resp').html("");
    $.ajax({
        url: MARK_AS_READ,
        type: "POST",
        data: {
            '_token':_CSRF_TOKEN
        },
        success: function (response) {

           toastr.success('Mark as read successfully');
           loadENotificationList(1);
           $('#loader').attr('style','display:none');
        }
    });
}