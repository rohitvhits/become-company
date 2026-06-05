function getMedicalalList() {
    var hha_status_id = $("#hha_status_id").val();
    $.ajax({
        url: _HHA_CAREGIVER_MEDICAL_AJAX_URL,
        type: "GET",
        data: {
            status: hha_status_id,
            id: _HHA_ID,
        },
        success: function (res) {
            var json = res.data;
            var htmlResponse = "";
            if (res.data.length !== 0) {
                var cnt = 1;
                $.each(json, function (i, v) {
                    htmlResponse +=
                        "<tr><td>" +
                        cnt +
                        "</td><td>" +
                        v.medical_name +
                        "</td><td>" +
                        v.status +
                        "</td><td>" +
                        moment(v.due_date).format("MM/DD/YYYY") +
                        "</td></tr>";
                    cnt++;
                });
            } else {
                htmlResponse = '<tr><td colspan="4">' + res.message + "</td></tr>";
            }
            $("#tbody_id").html("");
            $("#tbody_id").html(htmlResponse);
        },
    });
}

function refreshMedical() {
    $.ajax({
        url: _HHA_CAREGIVER_MEDICAL_URL + "?id=" + _HHA_ID,
        type: "GET",
        success: function (res) {
            toastr.success(res.message);
            getMedicalalList();
        },
    });
}