function getInService() {
    $.ajax({
        url: _HHA_CAREGIVER_IN_SERVICE_URL + "?id=" + caregiverId,
        type: "GET",
        success: function (res) {
            var json = res.data;
            var htmlResponse = "";
            $("#caregiver_inservice_id").html("");

            if (json.length !== 0) {
                var cnt = 1;
                $.each(json, function (i, v) {
                    htmlResponse +="<tr><td>" +cnt++ +"</td><td>" +v.topic_name +"</td><td>" +v.inservice_date +"</td><td>" +v.from_time +"</td><td>" +v.end_time +"</td><td>" +v.description +"</td></tr>";
                });
            } else {
                htmlResponse = '<tr><td colspan="6">No Record Available</td></tr>';
            }

            $("#caregiver_inservice_id").html(htmlResponse);

            $("#caregiver_inservice_datatable").dataTable().fnDestroy();
            $("#caregiver_inservice_datatable").dataTable({
                bInfo: false,
                bSort: false,
                pageLength: 10,
                searching: false,
            });
            $(".dataTables_length").attr("style", "display:none");
        },
    });
}