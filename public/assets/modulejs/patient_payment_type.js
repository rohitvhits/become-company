function getPaymentNewStatus(e) {
    var payments_id = $("#payments_id").val();
    var payments_name = $("#payments_id option:selected").text();
    var cnt = 0;
    $(".payments_id_error").html("");
    if (payments_id == "") {
        $(".payments_id_error").html("Payment type is required");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        var newforms = $("#payment_method_id").serialize();

        $.ajax({
            type: "POST",
            url: _PATIENT_PAYMENT_TYPE,
            data: newforms,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#payment_type_id").html("");
                $("#payment_type_id").html(payments_name);
                $(".close_p").click();
            },
        });
    }
}