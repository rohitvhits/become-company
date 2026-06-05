document.addEventListener('DOMContentLoaded', function () {

    $("#stampUserFormModal").on("hidden.bs.modal", function () {
        $("#stampUserFormAdd")[0].reset();
    });

});

function stampFormModal(id) {
    $("#stamp_user").val("");
    $("#stamp_user_id").val(id);
    $("#stampUserForm").text("Save");
    $("#ModalLabel").text("Add Stamp");
    $("#stampUserForm").attr("data-uid", "");
    $(".stamp_user_error").html("");
    $("#stampUserFormModal").modal("show");
}


