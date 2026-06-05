function refreshHHA() {
    $("#loadertag121").attr("style", "");
    $("#chat-messages-news").html("");
    $("#chat-messages-news-dataTable").dataTable().fnDestroy();

    $.ajax({
        url: _HHA_CAREGIVER_NOTES_URL + "?id=" + _HHA_ID,
        type: "GET",
        success: function (res) {
            var response = "";
            var json = res.data;
            $("#loadertag121").attr("style", "display:none");

            if (json.length !== 0) {
                var cnt = 1;
                $.each(json, function (i, v) {
                    response +=
                        '<tr id="msg-' +
                        v.CaregiverNoteID +
                        '"><td>' +
                        cnt++ +
                        "</td><td>" +
                        v.Note +
                        "</td><td>" +
                        v.NoteDate +
                        "</td></tr>";
                });
            }

            $("#chat-messages-news").html(response);

            $("#chat-messages-news-dataTable").dataTable({
                bInfo: false,
                bSort: false,
                pageLength: 10,
                searching: false,
            });
            $(".dataTables_length").attr("style", "display:none");
        },
    });
    return false;
}

$("#hhaCaregiverSave").click(function (e) {
    var hha_caregivers_notes = $("#hha_caregivers_notes_id").val();
    var subjectId = $("#subjectId").val();

    var cnt = 0;
    $("#hha_caregivers_notes_id_error").html("");
    $("#hha_subject_id_error").html("");

    if (hha_caregivers_notes.trim() === "") {
        $("#hha_caregivers_notes_id_error").html("Please enter Notes");
        cnt = 1;
    }
    if (subjectId === "") {
        $("#hha_subject_id_error").html("Please select Subject");
        cnt = 1;
    }
    if (cnt === 1) {
        return false;
    } else {
        var forn = $("#hha_caregivers_notes")[0];
        var formData = new FormData(forn);
        formData.append("_token", _CSRF_TOKEN);
        formData.append("hha_caregivers_notes", hha_caregivers_notes);
        formData.append("subject_id", subjectId);
        formData.append("id", _HHA_ID);
        $.ajax({
            url: _HHA_CAREGIVER_CREATE_NOTES_URL,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success("Notes successfully added");
                $("#hha_caregivers_notes")[0].reset();
                $("#exampleModal-notes").modal("hide");
                refreshHHA();
            },
            error: function (xhr, status, error) {
                toastr.error(xhr.responseJSON.message);
            },
        });
    }
});

function getHHACaregiverSubject() {
    $.ajax({
        url: _HHA_CAREGIVER_SUBJECT_URL + "?id=" + _HHA_ID,
        type: "GET",
        success: function (res) {
            var json = res.data;
            var option = "";
            if (json.length !== 0) {
                option = '<option value="">Select Subject</option>';
                $.each(json, function (i, v) {
                    option += '<option value="' + v.ID + '">' + v.Name + "</option>";
                });
            }

            $("#subjectId").html("");
            $("#subjectId").html(option);
            $("#exampleModal-notes").modal("show");
        },
    });
}