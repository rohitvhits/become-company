$(function() {
    $(".wmd-view-topscroll").scroll(function() {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function() {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadPatientSserviceDataList(1);
});

$('.searchAppoinment').on('click', function() {
   
    loadPatientSserviceDataList(1);
});

function loadPatientSserviceDataList(page=1) {
    var formsubmit = $('#formsubmit').serialize();
    $('.order-listing-loader1').attr('style', '');
    $.ajax({
        url: _PATIENT_SERVICE_LIST+"?page=" + page,
        type: "get",
        data: formsubmit,
        success: function (response) {
            $('.order-listing-loader1').attr('style', 'display:none');
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
   
    loadPatientSserviceDataList(page);
    
});

$('.btnExport').on('click', function (e) {
    $('.order-listing-loader1').attr('style', '');
    var authAgencyFk = _AUTH_AGENCY_FK;
    var authId = _AUTH_ID;
    var user_type_fk = _USER_TYPE_FK;

    var formsubmit = $('#formsubmit').serializeArray();
    var requestData = {
        authAgencyFk: authAgencyFk,
        authId: authId,
        user_type_fk: user_type_fk
    };

    formsubmit.forEach(function (item) {
        requestData[item.name] = item.value;
    });

    $.ajax({
        type: "GET",
        url: _PATIENT_EXPORT_URL,
        data: requestData,

        success: function (res) {
            $('.order-listing-loader1').attr('style', 'display:none');
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);


            link.download = "Patient" + _DATE_TIME + ".csv";
            link.click();
        }
    });

});