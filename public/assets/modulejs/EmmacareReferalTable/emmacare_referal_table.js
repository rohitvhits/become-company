function loadAjaxList(page){
    $('#loadertag1').attr('style','');
    $.ajax({
        url: _LOAD_DATA_URL+"?page="+page,
        data:$('#formsubmit').serialize(),
        type: "GET",
        success:function(res){
            $('#loadertag1').attr('style','display:none');
            $('#response_id').html("")
            $('#response_id').html(res)
        }
    });
}

loadAjaxList(1)

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

$('.btnSearch').click(function(e){
    loadAjaxList(1)
})

$('.btnExport').click(function(e){
    $('#loadertag1').attr('style','');
    $.ajax({
        url: _LOAD_EXPORT_CSV,
        data:$('#formsubmit').serialize(),
        type: "GET",
        success:function(res){
            $('#loadertag1').attr('style','display:none');
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);


            link.download = "emmacare_report"+_DATE_TIME+".csv";
            link.click();
        }
    });
})

$('.btnRefresh').click(function(e){
    $('#formsubmit')[0].reset();
    loadAjaxList(1)
});