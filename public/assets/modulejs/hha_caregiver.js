$(".wmd-view-topscroll").scroll(function() {
    $(".wmd-view")
        .scrollLeft($(".wmd-view-topscroll").scrollLeft());
});
$(".wmd-view").scroll(function() {
    $(".wmd-view-topscroll")
        .scrollLeft($(".wmd-view").scrollLeft());
});


$('body').on('click', '.hha_caregiver_paginate .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    hhaCaregiverList(page);
});

$('body').on('click', '.record_id', function(event) {
    var dataSort = $(this).attr('data-sort');
    var dataFields = $(this).attr('data-field');
    $('#sortingColumn').val(dataFields)
    $('#sortingOrder').val(dataSort)
    hhaCaregiverList(1);
});

hhaCaregiverList(1);

function hhaCaregiverList(page) {
    $('.shimmer_id').removeClass('hide');
    $('#resp').html("")
    $('.location-wise-data-loader').attr('style', 'display:flex');
  
     $.ajax({
         url: _HHA_CAREGIVER_LIST+"?page=" + page,
         type: "GET",
         data: {
             'agency_fk': $('#agency_fk').val(),
            'full_name':$('#full_name').val(),
            'code':$('#code').val(),
            'caregiver_phone':$('#caregiver_phone').val(),
            'last_work_date':$('#last_work_date').val(),
            'dob':$('#dob').val(),
            'gender':$('#gender').val(),
            'sortingColumn':$('#sortingColumn').val(),
            'sortingOrder':$('#sortingOrder').val(),
            'redirection_agency_id':_AGENCY_ID
         },
         success: function(res) {
            $('.shimmer_id').addClass('hide')
            $('#resp').html(res)
            $('.location-wise-data-loader').attr('style', 'display:none');
         },
         error:function(jqr){
            showErrorAndLoginRedirection(jqr);
         }
     })
     return false;
 }

 function hhaCaregiverExport() {
    $('.loader-sec').show();
  
     $.ajax({
         url: _HHA_CAREGIVER_EXPORT_CSV,
         type: "GET",
         data: {
             'agency_fk': $('#agency_fk').val(),
            'full_name':$('#full_name').val(),
            'code':$('#code').val(),
            'caregiver_phone':$('#caregiver_phone').val(),
            'last_work_date':$('#last_work_date').val(),
            'dob':$('#dob').val(),
            'gender':$('#gender').val(),
            'sortingColumn':$('#sortingColumn').val(),
            'sortingOrder':$('#sortingOrder').val(),
            'redirection_agency_id':_AGENCY_ID,
            'hhasyncdatetime':$('#hhasyncdatetime').val(),
         },
         success: function(res) {
            $('.loader-sec').hide();
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "hha_caregiver" + _DATE_TIME + ".csv";
            link.click();
         }
     })
     return false;
 }