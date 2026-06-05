function loadAjax(){
    $.ajax({
        
        type: "GET",
        url: _AJAX_LIST,
        
        success: function (data) {
           console.log(data);
        },
        error: function (response) {
            $(".submit").attr("disabled", false);

            toastr.error(response.responseJSON.message);
        },
        
    })
}