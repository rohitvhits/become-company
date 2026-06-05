loadBranchDropdown();
SERVICEList();
agencyList();
bookingList();
cancellationsList();
refusalsList();
detailedRefusalsList();
detailedCancellationsList();
unabletocontactList();
completedList();
statusList();

function loadBranchDropdown() {
  if (typeof _GET_BRANCHES === 'undefined') {
    return;
  }
  $.ajax({
    type: "GET",
    url: _GET_BRANCHES,
    success: function (res) {
      if (res.status && res.data && res.data.length > 0) {
        var html = '';
        $.each(res.data, function (i, branch) {
          html += '<option value="' + branch.id + '">' + branch.branch_name + '</option>';
        });
        $('#branch_id').html(html);
      }
    }
  });
}

function filter() {
  SERVICEList();
  agencyList();
  bookingList();
  cancellationsList();
  refusalsList();
  detailedRefusalsList();
  detailedCancellationsList();
  unabletocontactList();
  completedList();
  statusList();
}
$("#agencyId").on("change", function () {
  detailedRefusalsList();
});
$("#agencyIdCancel").on("change", function () {
  detailedCancellationsList();
});

var chartSERVICEResponse;
function SERVICEList(page = 1) {
  $(".user-order-listing-loader1").attr("style", "display:flex");
  $(".user-order-chart-loader1").attr("style", "display:flex");
  $("#user_view_chart").html("");
  $("#user-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _SERVICE_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="servicesTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Services</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.service}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#user_view_chart").html(responseHtml);
      }, 500);
      $(".user-order-listing-loader1").hide();
      jsonData = response;
      drawStatusChart(jsonData);
    },
  });
}
function drawStatusChart(jsonData) {
  if (chartSERVICEResponse) {
    chartSERVICEResponse.destroy();
  }

  var chartData = [["Services", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.service, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("pieChartNew")
  );
  chartd.draw(data, options);
  $(".user-order-chart-loader1").hide();
}
var chartAGENCYresponse;
function agencyList(page = 1) {
  $(".agency-order-listing-loader1").attr("style", "display:flex");
  $(".agency-order-chart-loader1").attr("style", "display:flex");
  $("#agency_view_chart").html("");
  $("#agency-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;

  $.ajax({
    url: _AGENCY_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="agencyTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agency</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;     border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#agency_view_chart").html(responseHtml);
      }, 500);
      $(".agency-order-listing-loader1").hide();
      jsonData = response;
      drawAgencyChart(jsonData);
    },
  });
}
function drawAgencyChart(jsonData) {
  if (chartAGENCYresponse) {
    chartAGENCYresponse.destroy();
  }

  var chartData = [["Services", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("agencypieChartNew")
  );
  chartd.draw(data, options);
  $(".agency-order-chart-loader1").hide();
}
var chartBOOKINGresponse;
function bookingList(page = 1) {
  $(".bookings-order-listing-loader1").attr("style", "display:flex");
  $(".bookings-order-chart-loader1").attr("style", "display:flex");
  $("#bookings_view_chart").html("");
  $("#bookings-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _BOOKING_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="bookingsTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agencies</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#bookings_view_chart").html(responseHtml);
      }, 500);
      $(".bookings-order-listing-loader1").hide();
      jsonData = response;
      drawBookingChart(jsonData);
    },
  });
}
function drawBookingChart(jsonData) {
  if (chartBOOKINGresponse) {
    chartBOOKINGresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("bookingpieChartNew")
  );
  chartd.draw(data, options);
  $(".bookings-order-chart-loader1").hide();
}
var chartCANCELLATIONSresponse;
function cancellationsList(page = 1) {
  $(".cancellations-order-listing-loader1").attr("style", "display:flex");
  $(".cancellations-order-chart-loader1").attr("style", "display:flex");
  $("#cancellations_view_chart").html("");
  $("#cancellations-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _CANCELLATIONS_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="cancellationsTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agencies</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#cancellations_view_chart").html(responseHtml);
      }, 500);
      $(".cancellations-order-listing-loader1").hide();
      jsonData = response;
      drawCancellationsChart(jsonData);
    },
  });
}
function drawCancellationsChart(jsonData) {
  if (chartCANCELLATIONSresponse) {
    chartCANCELLATIONSresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("cancellationspieChartNew")
  );
  chartd.draw(data, options);
  $(".cancellations-order-chart-loader1").hide();
}
var chartREFUSALSresponse;
function refusalsList(page = 1) {
  $(".refusals-order-listing-loader1").attr("style", "display:flex");
  $(".refusals-order-chart-loader1").attr("style", "display:flex");
  $("#refusals_view_chart").html("");
  $("#refusals-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;

  $.ajax({
    url: _REFUSALS_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="refusalsTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agencies</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#refusals_view_chart").html(responseHtml);
      }, 500);
      $(".refusals-order-listing-loader1").hide();
      jsonData = response;
      drawRefusalsChart(jsonData);
    },
  });
}
function drawRefusalsChart(jsonData) {
  if (chartREFUSALSresponse) {
    chartREFUSALSresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("refusalspieChartNew")
  );
  chartd.draw(data, options);
  $(".refusals-order-chart-loader1").hide();
}
var chartDETAILED_REFUSALSresponse;
function detailedRefusalsList(page = 1) {
  $(".detailed-refusals-order-listing-loader1").attr("style", "display:flex");
  $(".detailed-refusals-order-chart-loader1").attr("style", "display:flex");
  $("#detailed-refusals_view_chart").html("");
  $("#detailed-refusals-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agencyId = $("#agencyId").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _DETAILED_REFUSALS_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      agency_id: agencyId,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      last_updated_date: last_updated_date,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="detailedRefusalsTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Reasons</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.status_name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#detailed-refusals_view_chart").html(responseHtml);
      }, 500);
      $(".detailed-refusals-order-listing-loader1").hide();
      jsonData = response;
      drawdetailedRefusalsChart(jsonData);
    },
  });
}
function drawdetailedRefusalsChart(jsonData) {
  if (chartDETAILED_REFUSALSresponse) {
    chartDETAILED_REFUSALSresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.status_name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("detailed-refusalspieChartNew")
  );
  chartd.draw(data, options);
  $(".detailed-refusals-order-chart-loader1").hide();
}

var chartDETAILED_CANCELLATIONSresponse;
function detailedCancellationsList(page = 1) {
  $(".detailed-cancellations-order-listing-loader1").attr(
    "style",
    "display:flex"
  );
  $(".detailed-cancellations-order-chart-loader1").attr(
    "style",
    "display:flex"
  );
  $("#detailed-cancellations_view_chart").html("");
  $("#detailed-cancellations-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agencyId = $("#agencyIdCancel").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _DETAILED_CANCELLATIONS_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      agency_id: agencyId,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      last_updated_date: last_updated_date,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="detailedcancellationsTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Reasons</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.status_name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#detailed-cancellations_view_chart").html(responseHtml);
      }, 500);
      $(".detailed-cancellations-order-listing-loader1").hide();
      jsonData = response;
      drawdetailedCancellationsChart(jsonData);
    },
  });
}
function drawdetailedCancellationsChart(jsonData) {
  if (chartDETAILED_CANCELLATIONSresponse) {
    chartDETAILED_CANCELLATIONSresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.status_name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("detailed-cancellationspieChartNew")
  );
  chartd.draw(data, options);
  $(".detailed-cancellations-order-chart-loader1").hide();
}
var chartUNABLETOCONTACTSresponse;
function unabletocontactList(page = 1) {
  $(".unabletocontact-order-listing-loader1").attr("style", "display:flex");
  $(".unabletocontact-order-chart-loader1").attr("style", "display:flex");
  $("#unabletocontact_view_chart").html("");
  $("#unabletocontact-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;
  $.ajax({
    url: _UNABLETOCONTACT_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="unableTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agencies</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#unabletocontact_view_chart").html(responseHtml);
      }, 500);
      $(".unabletocontact-order-listing-loader1").hide();
      jsonData = response;
      drawUnabletocontactChart(jsonData);
    },
  });
}
function drawUnabletocontactChart(jsonData) {
  if (chartUNABLETOCONTACTSresponse) {
    chartUNABLETOCONTACTSresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };
  var chartd = new google.visualization.PieChart(
    document.getElementById("unabletocontactpieChartNew")
  );
  chartd.draw(data, options);
  $(".unabletocontact-order-chart-loader1").hide();
}
var chartCOMPLETEDresponse;
function completedList(page = 1) {
  $(".completed-order-listing-loader1").attr("style", "display:flex");
  $(".completed-order-chart-loader1").attr("style", "display:flex");
  $("#completed_view_chart").html("");
  $("#completed-no-data").css("display", "none");
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var total = 0;

  $.ajax({
    url: _COMPLETED_LIST + "?page=" + page,
    type: "GET",
    //async: false,
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      setTimeout(() => {
        $(".hideClass").addClass("d-none");
        let responseHtml = `
        <div class="col-md-12 pl-0 table-responsive">
            <table id="completedTable" class="myDataTable table table-striped">
                <thead>
                    <tr>
                        <th>Agencies</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>`;

        if (response.length > 0) {
          response.forEach((item) => {
            responseHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                </tr>`;
            total += parseInt(item.count, 10);
          });
          responseHtml += `
                <tr>
                    <th>Total</th>
                    <th>${total}</th>
                </tr>`;
        } else {
          responseHtml += `
            <tr>
                <td colspan="3" style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">No records found</td>
            </tr>`;
        }

        responseHtml += `
                </tbody>
            </table>
        </div>`;

        $("#completed_view_chart").html(responseHtml);
      }, 500);
      $(".completed-order-listing-loader1").hide();
      jsonData = response;
      drawCompletedChart(jsonData);
    },
  });
}
function drawCompletedChart(jsonData) {
  if (chartCOMPLETEDresponse) {
    chartCOMPLETEDresponse.destroy();
  }

  var chartData = [["Agencies", "Total"]];
  var colors = [];
  var colorpallate = generateColorPalette(jsonData.length);
  var colors = colors.concat(colorpallate);
  // Loop through the data and add it to the chartData array
  jsonData.forEach(function (item) {
    chartData.push([item.name, item.count]);
  });
  var data = google.visualization.arrayToDataTable(chartData);

  var options = {
    is3D: true,
    colors: colors,
    // Show percentages on slices
    pieSliceText: "percentage",
    pieSliceTextStyle: {
      color: "black",
      fontSize: 9,
      bold: true,
    },
    // Optimized chart area to prevent percentage overflow
    chartArea: {
      left: 20,
      top: 60,
      width: "80%",
      height: "70%",
    },
  };

  var chartd = new google.visualization.PieChart(
    document.getElementById("completedpieChartNew")
  );
  chartd.draw(data, options);
  $(".completed-order-chart-loader1").hide();
}
// Global variables
var chartInstance = null;
var agencyNamesGet = {};
var agencyDataGet = {};

function statusList(page = 1) {
  // Show loaders
  $(".status-order-listing-loader1").attr("style", "display:flex");
  $(".status-order-chart-loader1").attr("style", "display:flex");

  // Clear previous content
  $("#status_view_chart").html("");
  $("#status-no-data").css("display", "none");

  // Get filter values
  var range_date = $("#user_range_date").val();
  var last_updated_date = $("#last_updated_date").val();
  var type = $("#type").val();
  var agency_fk = $("#agency_fk").val();
  var agency_filter_type = $("#agency_filter_type").val();
  var service_id = $("#service_id").val();
  var service_filter_type = $("#service_filter_type").val();
  var assigned_to = $("#assigned_to").val();
  var medication_list = $("#medication_list").val();
  var insurance_elg = $("#insurance_elg").val();
  var mdo_tag = $("#mdo_tag").val();
  var branch_id = $("#branch_id").val();
  var branch_filter_type = $("#branch_filter_type").val();
  var allTotal = 0;

  $.ajax({
    url: _STATUS_LIST + "?page=" + page,
    type: "GET",
    global: false,
    data: {
      type: type,
      created_date: range_date,
      last_updated_date: last_updated_date,
      agency_fk: agency_fk,
      agency_filter_type: agency_filter_type,
      service_id: service_id,
      service_filter_type: service_filter_type,
      assigned_to: assigned_to,
      medication_list: medication_list,
      insurance_elg: insurance_elg,
      mdo_tag: mdo_tag,
      branch_id: branch_id,
      branch_filter_type: branch_filter_type,
    },
    success: function (response) {
      try {
        // Validate response
        if (!response.pivotData || !response.agencies) {
          console.error("Invalid response format:", response);
          $("#status-no-data").css("display", "block");
          $("#status_view_chart").html("");
          return;
        }

        // Update global variables
        agencyDataGet = response.pivotData;
        agencyNamesGet = response.agencies;

        // Collect all unique status keys for table header
        const allStatuses = [
          ...new Set(Object.values(agencyDataGet).flatMap(Object.keys)),
        ];

        // Create table
        let table =
          "  <div class='col-md-12 pl-0 table-responsive'><table id='statusTable'  class='myDataTable table table-striped'><thead><tr><th>Agency Name</th>";
        allStatuses.forEach((status) => {
          table += `<th>${status}</th>`;
        });
        table += "<th>Grand Total</th></tr></thead><tbody>";

        // Totals object for columns
        let statusTotals = {};
        allStatuses.forEach((status) => (statusTotals[status] = 0));

        let i = 1;
        for (const [id, data] of Object.entries(agencyDataGet)) {
          const total = Object.values(data).reduce((a, b) => a + b, 0);
          table += `<tr><td>${agencyNamesGet[id]}</td>`;
          allStatuses.forEach((status) => {
            const value = data[status] || 0;
            table += `<td>${value}</td>`;
            statusTotals[status] += value; // add to column total
          });
          table += `<th>${total}</th></tr>`;
          allTotal += parseInt(total, 10);
          i++;
        }

        // Add footer row with totals
        table +=
          "<tr style='font-weight:bold; background:#f0f0f0;'><th>Total</th>";
        allStatuses.forEach((status) => {
          table += `<th>${statusTotals[status]}</th>`;
        });
        table += `<th>${allTotal}</th></tr>`;

        table += "</tbody></table>";
        if (i != 1) {
          $("#status_view_chart").html(table);
        }

        console.log("Data received:", {
          agencies: Object.keys(agencyDataGet).length,
          statuses:
            Object.keys(agencyDataGet).length > 0
              ? Object.keys(Object.values(agencyDataGet)[0])
              : [],
        });
        if (i == 1) {
          $("#status-chart-no-chart").css("display", "block");
        } else {
          $("#status-chart-no-chart").css("display", "none");
        }
        // Small delay for smooth UI transition
        setTimeout(() => {
          // Create/update chart
          createStackedChart();

          // Hide loaders
          $(".hideClass").addClass("d-none");
          $(".status-order-listing-loader1").attr("style", "display:none");
          $(".status-order-chart-loader1").attr("style", "display:none");
        }, 1000); // Reduced from 2000ms for better UX
      } catch (error) {
        console.error("Error processing response:", error);
        $(".status-order-listing-loader1").attr("style", "display:none");
        $(".status-order-chart-loader1").attr("style", "display:none");
        $("#status-no-data").css("display", "block");
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", { xhr, status, error });

      // Hide loaders
      $(".status-order-listing-loader1").attr("style", "display:none");
      $(".status-order-chart-loader1").attr("style", "display:none");

      // Show error message
      $("#status-no-data").css("display", "block");

      // Optional: Show user-friendly error message
      if (typeof toastr !== "undefined") {
        toastr.error("Failed to load chart data. Please try again.");
      }
    },
  });
}

$(function () {
  $(".js-example-basic-multiple").select2();
  var start = moment().subtract(0, "days");
  var end = moment();
  $("#user_range_date").daterangepicker(
    {
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: "sunday",
      ranges: {
        'Select Date': [start, end],
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
        "Last Week": [
          moment().subtract(1, "weeks").startOf("isoWeek"),
          moment().subtract(1, "weeks").endOf("isoWeek"),
        ],
      },
    },
    function (chosen_date, end_date) {
      $("#user_range_date").val(
        chosen_date.format("MM/DD/YYYY") + " - " + end_date.format("MM/DD/YYYY")
      );
    }
  );
   $('#user_range_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });
  $("#last_updated_date").daterangepicker(
    {
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: "sunday",
      ranges: {
        'Select Date': [start, end],
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
        "Last Week": [
          moment().subtract(1, "weeks").startOf("isoWeek"),
          moment().subtract(1, "weeks").endOf("isoWeek"),
        ],
      },
    },
  );
});
 $('#last_updated_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

$("#exportBtn").on("click", function () {
  // Create a new workbook
  let wb = XLSX.utils.book_new();

  // Convert each HTML table to a worksheet
  let servicesTable = document.getElementById("servicesTable");
  let agencyTable = document.getElementById("agencyTable");
  let bookingsTable = document.getElementById("bookingsTable");
  let cancellationsTable = document.getElementById("cancellationsTable");
  let refusalsTable = document.getElementById("refusalsTable");
  let unableTable = document.getElementById("unableTable");
  let detailedRefusalsTable = document.getElementById("detailedRefusalsTable");
  let detailedcancellationsTable = document.getElementById(
    "detailedcancellationsTable"
  );
  let completedTable = document.getElementById("completedTable");
  let statusTable = document.getElementById("statusTable");

  let ws1 = XLSX.utils.table_to_sheet(servicesTable);
  let ws2 = XLSX.utils.table_to_sheet(agencyTable);
  let ws3 = XLSX.utils.table_to_sheet(bookingsTable);
  let ws4 = XLSX.utils.table_to_sheet(cancellationsTable);
  let ws5 = XLSX.utils.table_to_sheet(refusalsTable);
  let ws6 = XLSX.utils.table_to_sheet(unableTable);
  let ws7 = XLSX.utils.table_to_sheet(detailedRefusalsTable);
  let ws10 = XLSX.utils.table_to_sheet(detailedcancellationsTable);
  let ws8 = XLSX.utils.table_to_sheet(completedTable);
  let ws9 = XLSX.utils.table_to_sheet(statusTable);

  // Append sheets
  XLSX.utils.book_append_sheet(wb, ws1, "Services Overview");
  XLSX.utils.book_append_sheet(wb, ws2, "Agency Overview");
  XLSX.utils.book_append_sheet(wb, ws3, "Bookings VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws4, "Cancellations VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws5, "Refusals VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws6, "Unable To Contact VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws7, "Detailed Refusals");
  XLSX.utils.book_append_sheet(wb, ws8, "Completed Forms VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws9, "Status VS Agencies");
  XLSX.utils.book_append_sheet(wb, ws10, "Detailed Cancellations");

  // Export Excel file
  XLSX.writeFile(wb, "Referral_Stats_and_Analytics.xlsx");
});

function generateColorPalette(length) {
  // Predefined pastel colors
  const colors = [];
  // Helper: generate a random pastel color if needed
  function getPastelColor() {
    const r = Math.floor(Math.random() * 127 + 100); // 100–227 range
    const g = Math.floor(Math.random() * 127 + 100);
    const b = Math.floor(Math.random() * 127 + 100);
    return `rgba(${r}, ${g}, ${b}, 0.7)`; // semi-transparent pastel
  }

  // Fill colors array
  for (let i = 0; i < length; i++) {
    colors.push(rgbaToHex(getPastelColor())); // fallback
  }

  return colors;
}
function rgbaToHex(rgba) {
  // Extract numbers from rgba string
  const parts = rgba.match(/[\d.]+/g).map(Number);

  // Ensure at least r,g,b are present
  const [r, g, b] = parts;

  // Convert to hex
  return (
    "#" +
    [r, g, b]
      .map((x) => {
        const hex = x.toString(16);
        return hex.length === 1 ? "0" + hex : hex;
      })
      .join("")
  ).toUpperCase();
}

// Laravel-inspired color palette
const statusColors = {
  Pending: "#fbbf24", // Amber
  Cancelled: "#6b7280", // Gray
  Booked: "#10b981", // Emerald
  Completed: "#8b5cf6", // Violet
  "No Show": "#ef4444", // Red
  Arrived: "#06d6a0", // Teal
  Processing: "#3b82f6", // Blue
  "Not Interested": "#f59e0b", // Orange
  "Hospitalized/Rehab": "#ec4899", // Pink
  "Unable To Contact": "#94a3b8", // Slate
  Refused: "#dc2626", // Dark Red
  "Mark as CheckIn": "#059669", // Dark Emerald
  "Pending Termination": "#b91c1c", // Dark Red
  "On Hold": "#d97706", // Dark Amber
  "On Leave": "#7c3aed", // Dark Violet
  Terminated: "#991b1b", // Very Dark Red
  "New Form Requested": "#0d9488", // Dark Teal
  "New Order Received": "#0284c7", // Dark Blue
  "Form Completed": "#7c2d12", // Brown
  "Mark As CheckIn": "#065f46", // Very Dark Emerald
  "1st Attempt - Unable to Contact": "#f97316", // Orange-500
  "2nd Attempt - Unable to Contact": "#ea580c", // Orange-600
  "3rd Attempt - Unable to Contact": "#c2410c", // Orange-700
  "Telehealth Completed": "#22d3ee", // Cyan
  "Patient Deceased": "#1f2937", // Dark Gray
  Signed: "#16a34a", // Green
  "Signed & Sent Back to the Agency": "#15803d", // Dark Green
  "Telehealth Completed , Pending Forms": "#0891b2", // Dark Cyan
  "Patient Asked to Reschedule": "#a855f7", // Purple
  "Appointment Missed": "#be185d", // Rose
  "Service Provided": "#bbbe18ff",
  "Closed Temporarily" : "#f1405eff"
};

function createStackedChart() {
  try {
    // Validate data
    const agencyData = agencyDataGet || {};
    const agencyNames = agencyNamesGet || {};

    if (Object.keys(agencyData).length === 0) {
      destroyChart();
      console.warn("No agency data available for chart");
      $("#status-no-data").css("display", "block");
      return;
    }

    // Check if canvas element exists
    const canvas = document.getElementById("stackedChart");
    if (!canvas) {
      console.error('Canvas element with id "stackedChart" not found!');
      return;
    }

    const ctx = canvas.getContext("2d");
    if (!ctx) {
      console.error("Unable to get 2D context from canvas!");
      return;
    }

    // Destroy existing chart first
    destroyChart();

    // Get agencies with actual data (not empty)
    const agenciesWithData = Object.keys(agencyData).filter((id) => {
      const agency = agencyData[id];
      return Object.values(agency).some((count) => count > 0);
    });

    // Take more agencies if available, or all if less than 20
    const maxAgencies = Math.min(agenciesWithData.length, 20);
    const topAgencies = agenciesWithData.slice(0, maxAgencies);

    if (topAgencies.length === 0) {
      console.warn("No agencies with data found");
      destroyChart();
      $("#status-chart-no-data").css("display", "block");
      return;
    }

    const labels = topAgencies.map((id) => {
      const name = agencyNames[id] || `Agency ${id}`;
      // Truncate long names
      return name.length > 25 ? name.substring(0, 25) + "..." : name;
    });

    // Get all unique statuses
    const allStatuses = new Set();
    topAgencies.forEach((id) => {
      Object.keys(agencyData[id]).forEach((status) => {
        if (agencyData[id][status] > 0) {
          // Only include statuses with data
          allStatuses.add(status);
        }
      });
    });

    // Create datasets only for statuses that have data
    const datasets = Array.from(allStatuses)
      .map((status) => ({
        label: status,
        data: topAgencies.map((id) => agencyData[id][status] || 0),
        backgroundColor: statusColors[status] || "#6b7280",
        borderWidth: 1,
        borderColor: "rgba(255,255,255,0.8)",
      }))
      .filter((dataset) => dataset.data.some((value) => value > 0)); // Remove empty datasets

    console.log("Creating chart with data:", {
      agenciesCount: topAgencies.length,
      statusesCount: datasets.length,
      totalRecords: datasets.reduce(
        (sum, ds) => sum + ds.data.reduce((a, b) => a + b, 0),
        0
      ),
    });

    // Set canvas height based on number of agencies
    const canvasHeight = Math.max(400, topAgencies.length * 30 + 200);
    canvas.style.height = canvasHeight + "px";

    // Create new chart instance
    chartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: datasets,
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: "y", // Horizontal bars
        plugins: {
          legend: {
            position: "right", // Move legend to right for better space
            labels: {
              padding: 10,
              usePointStyle: true,
              font: { size: 11 },
              generateLabels: function (chart) {
                const original =
                  Chart.defaults.plugins.legend.labels.generateLabels;
                const labels = original.call(this, chart);
                // Truncate long legend labels
                labels.forEach((label) => {
                  if (label.text.length > 20) {
                    label.text = label.text.substring(0, 20) + "...";
                  }
                });
                return labels;
              },
            },
            maxWidth: 200,
          },
          tooltip: {
            mode: "point",
            intersect: true,
            callbacks: {
              title: function (tooltipItems) {
                return (
                  agencyNames[topAgencies[tooltipItems[0].dataIndex]] ||
                  tooltipItems[0].label
                );
              },
              label: function (context) {
                return `${context.dataset.label}: ${context.raw}`;
              },
            },
          },
        },
        layout: {
          padding: {
            right: 20,
            left: 10,
            top: 10,
            bottom: 10,
          },
        },
        scales: {
          x: {
            stacked: true,
            title: {
              display: true,
              text: "Number of Records",
              font: { size: 14, weight: "bold" },
            },
            grid: {
              color: "rgba(0,0,0,0.1)",
            },
            ticks: {
              stepSize: 1, // Show whole numbers only
            },
          },
          y: {
            stacked: true,
            title: {
              display: true,
              text: "Agencies",
              font: { size: 14, weight: "bold" },
            },
            grid: {
              display: false,
            },
            ticks: {
              maxRotation: 0, // Keep labels horizontal
              font: { size: 12 },
            },
          },
        },
        animation: {
          duration: 1000,
          easing: "easeInOutQuart",
        },
        interaction: {
          intersect: false,
          mode: "point",
        },
      },
    });

    $("#status-no-data").css("display", "none");
  } catch (error) {
    console.error("Error creating chart:", error);
    $("#status-no-data").css("display", "block");
  }
}
function destroyChart() {
  try {
    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }
  } catch (error) {
    console.error("Error destroying chart:", error);
  }
}

// Clear button functionality
$("#clearBtn").on("click", function () {
  // Reset date range picker
  $("#user_range_date").val("");
  $("#last_updated_date").val("");

  // Reset type dropdown to default
  $("#type").val("Patient");

  // Clear all multi-select dropdowns
  $("#agency_fk").val(null).trigger("change");
  $("#service_id").val(null).trigger("change");
  $("#assigned_to").val(null).trigger("change");

  // Reset new filter dropdowns
  $("#medication_list").val("");
  $("#insurance_elg").val("");
  $("#mdo_tag").val("");
  $("#branch_id").val(null).trigger("change");

  // Reset toggle buttons to include mode
  $("#agency_filter_type").val("include");
  $("#agencyToggleBtn").attr("data-mode", "include");
  $("#agencyToggleBtn").html('<i class="mdi mdi-plus"></i>');
  $("#agencyToggleBtn").attr("title", "Include - Click to switch to Exclude");
  $("#agencyToggleLabel").text("Include Agency").removeClass("mode-exclude").addClass("mode-include");

  $("#service_filter_type").val("include");
  $("#serviceToggleBtn").attr("data-mode", "include");
  $("#serviceToggleBtn").html('<i class="mdi mdi-plus"></i>');
  $("#serviceToggleBtn").attr("title", "Include - Click to switch to Exclude");
  $("#serviceToggleLabel").text("Include Service").removeClass("mode-exclude").addClass("mode-include");

  $("#branch_filter_type").val("include");
  $("#branchToggleBtn").attr("data-mode", "include");
  $("#branchToggleBtn").html('<i class="mdi mdi-plus"></i>');
  $("#branchToggleBtn").attr("title", "Include - Click to switch to Exclude");
  $("#branchToggleLabel").text("Include Branch").removeClass("mode-exclude").addClass("mode-include");

  // Trigger the filter function to reload data with cleared filters
  filter();
});
