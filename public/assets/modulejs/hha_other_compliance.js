function refreshOtherCompliance() {
    $("#loadertag1211").attr("style", "");

    $.ajax({
        url: _HHA_CAREGIVER_OTHER_COMPLIANCE_URL +
            "?id=" +
            _HHA_ID +
            "&agency_fk=" +
            _AGENCYID,
        type: "GET",
        success: function (res) {
            var json = res.data;
            var htmlResponse = "";
            if (json.length !== 0) {
                var cnt = 1;
                $.each(json, function (i, v) {
                    htmlResponse +=
                        "<tr><td>" +
                        cnt++ +
                        "</td><td>" +
                        v.medical_name +
                        "</td><td>" +
                        v.status +
                        "</td><td>" +
                        moment(v.due_date).format("MM/DD/YYYY") +
                        "</td></tr>";
                });
            } else {
                htmlResponse = '<tr><td colspan="3">No record available</td></tr>';
            }
            $("#loadertag1211").attr("style", "display:none");
            $("#tbody_compliance_id").html("");
            $("#tbody_compliance_id").html(htmlResponse);
        },
    });
}