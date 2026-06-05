function ajaxList(page){
    $('.location-wise-data-loader').attr('style','')
    $('#list_directory_id').html("");
    if($('#department_id').val().trim() == "" && $('#full_name').val().trim() == "" && $('#email').val().trim() == "" && $('#phone').val().trim() == "" && $('#ext').val().trim() == ""){
        $('#filter-btn span').removeClass('active-filter');
    }else{
        $('#filter-btn span').addClass('active-filter');
    }
    $.ajax({
        type: "GET",
        url: _AJAX_LIST,
        data:{
            'page':page,
            'department':$('#department_id').val(),
            'full_name':$('#full_name').val(),
            'email':$('#email').val(),
            'phone':$('#phone').val(),
            'ext':$('#ext').val(),
        },
        success: function (data) {
            $('.location-wise-data-loader').attr('style','display:none');
           $('#list_directory_id').html(data);
        },
    })
    return false;
}

ajaxList(1);
$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    ajaxList(page);
});

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

function refresh(){
    $('#department_id').val("");
    $('#full_name').val("");
    $('#email').val("");
    $('#phone').val("");
    $('#ext').val("");
    $('#filter-btn span').removeClass('active-filter');
    ajaxList(1)
}

