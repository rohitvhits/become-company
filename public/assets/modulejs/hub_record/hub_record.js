if (typeof HUB_RECORD_LIST != "undefined") {
  hubList(1);
}
if (typeof _FLAG != "undefined") {
  // $(":input").inputmask();
}

// Toggle Other Gender text box on create modal
$("body").on("change", '#hub_add_modal input[name="gender"]', function () {
  if ($(this).val() == "other") {
    $("#other_gender_div").show();
  } else {
    $("#other_gender_div").hide();
    $("#dep_other_gender").val("");
  }
});

function hubList(page = 1) {
  $("#hub_list_res").html("");
  $(".hideClass").removeClass("d-none");
  let agency_fk = $("#agency_fk").val();
  let first_name = $("#first_name").val();
  let full_name = $("#full_name").val();
  let last_name = $("#last_name").val();
  let mobile = $("#mobile").val();
  let created_date = $("#created_date").val();
  let created_by = $("#created_by_ny_id").val();
  let dob = $("#dob").val();
  let status = $("#status").val();
  let ssn = $("#ssn").val();
  let parent_id = $("#parent_id").val();
  let id = $("#id").val();
  let email = $("#email").val();
  let employee_code = $("#employee_code").val();
  let member_id = $("#member_id").val();
  $.ajax({
    url: HUB_RECORD_LIST + "?page=" + page,
    type: "get",
    data: {
      agency_fk: agency_fk,
      first_name: first_name,
      full_name: full_name,
      last_name: last_name,
      mobile: mobile,
      created_date: created_date,
      created_by: created_by,
      dob: dob,
      status: status,
      ssn: ssn,
      parent_id: parent_id,
      id: id,
      email: email,
      employee_code: employee_code,
      member_id: member_id,
    },
    success: function (response) {
      $(".hideClass").addClass("d-none");
      $("#hub_list_res").html("");
      $("#hub_list_res").html(response);
    },
  });
}

$(document).on("click", ".hub_list_paginate .pagination a", function (e) {
  e.preventDefault();
  let page = $(this).attr("href").split("page=")[1];
  hubList(page);
});

function refresh() {
  $("#agency_fk").val("").trigger("change");
  $("#first_name").val("").trigger("change");
  $("#last_name").val("").trigger("change");
  $("#mobile").val("").trigger("change");
  $("#assigne_to").val("").trigger("change");
  $("#locationId").val("").trigger("change");
  $("#created_date").val("").trigger("change");
  $("#created_by").val("").trigger("change");
  $("#language").val("").trigger("change");
  $("#dob").val("").trigger("change");
  $("#language_id").val("").trigger("change");
  $("#created_date").val("");
  $("#created_by_ny").tokenInput("clear");
  $("#status").val("");
  $("#ssn").val("");
  $("#parent_id").val("").trigger("change");
  $("#id").val("");
  $("#email").val("");
  $("#full_name").val("");
  $("#employee_code").val("");
  $("#member_id").val("");
  hubList(1);
}

function exportCsv() {
  $(".hideClass").removeClass("d-none");
  let agency_fk = $("#agency_fk").val();
  let first_name = $("#first_name").val();
  let last_name = $("#last_name").val();
  let full_name = $("#full_name").val();
  let mobile = $("#mobile").val();
  let created_date = $("#created_date").val();
  let created_by = $("#created_by_ny_id").val();
  let dob = $("#dob").val();
  let status = $("#status").val();
  let ssn = $("#ssn").val();
  let parent_id = $("#parent_id").val();
  let id = $("#id").val();
  let email = $("#email").val();
  let employee_code = $("#employee_code").val();
  let member_id = $("#member_id").val();
  $.ajax({
    url: HUB_RECORD_CSV,
    type: "get",
    data: {
      agency_fk: agency_fk,
      first_name: first_name,
      last_name: last_name,
      full_name: full_name,
      mobile: mobile,
      email: email,
      created_date: created_date,
      created_by: created_by,
      dob: dob,
      status: status,
      ssn: ssn,
      parent_id: parent_id,
      id: id,
      employee_code: employee_code,
      member_id: member_id,
    },
    success: function (response) {
      $(".hideClass").addClass("d-none");
      let blob = new Blob([response]);

      if (response == "") {
        toastr.error("Please check there is no data to export.");
      } else {
        let link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        let form_name = "hub_record_" + _DATE_TIME;
        link.download = form_name + ".csv";
        link.click();
      }
    },
  });
}

$(function () {
  let start = moment().subtract(0, "days");
  let end = moment();
  if (typeof _FLAG == "undefined") {
    $("#created_date").daterangepicker(
      {
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: "sunday",
        ranges: {
          "Select Date": [start, end],
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
          "Next Month": [
            moment().add(1, "month").startOf("month"),
            moment().add(1, "month").endOf("month"),
          ],
          "Next Week": [
            moment().add(1, "weeks").startOf("isoWeek"),
            moment().add(1, "weeks").endOf("isoWeek"),
          ],
          "Last Week": [
            moment().subtract(1, "weeks").startOf("isoWeek"),
            moment().subtract(1, "weeks").endOf("isoWeek"),
          ],
        },
      },
      function (chosen_date, end_date) {
        $("#created_date").val(
          chosen_date.format("MM/DD/YYYY") +
            " - " +
            end_date.format("MM/DD/YYYY")
        );
      }
    );

    $("#created_date").on("apply.daterangepicker", function (ev, picker) {
      // Detect "Select Date"
      if (picker.chosenLabel === "Select Date") {
        $(this).val("");
      } else {
        $(this).val(
          picker.startDate.format("MM/DD/YYYY") +
            " - " +
            picker.endDate.format("MM/DD/YYYY")
        );
      }
    });
  }
});

$("#filter-btn").click(function () {
  $("#search-filter-btn").slideToggle(600);
});

$(function () {
  var start = moment().subtract(0, "days");
  var end = moment();
  if (typeof _FLAG == "undefined") {
    $("#appointment_date").daterangepicker(
      {
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: "sunday",
        ranges: {
          "Select Date": [start, end],
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
          "Next Month": [
            moment().add(1, "month").startOf("month"),
            moment().add(1, "month").endOf("month"),
          ],
          "Next Week": [
            moment().add(1, "weeks").startOf("isoWeek"),
            moment().add(1, "weeks").endOf("isoWeek"),
          ],
          "Last Week": [
            moment().subtract(1, "weeks").startOf("isoWeek"),
            moment().subtract(1, "weeks").endOf("isoWeek"),
          ],
        },
      },
      function (chosen_date, end_date) {
        $("#appointment_date").val(
          chosen_date.format("MM/DD/YYYY") +
            " - " +
            end_date.format("MM/DD/YYYY")
        );
      }
    );

    $("#appointment_date").on("apply.daterangepicker", function (ev, picker) {
      // Detect "Select Date"
      if (picker.chosenLabel === "Select Date") {
        $(this).val("");
      } else {
        $(this).val(
          picker.startDate.format("MM/DD/YYYY") +
            " - " +
            picker.endDate.format("MM/DD/YYYY")
        );
      }
    });
  }
});

$("body").on("click", "#saveHub", function (e) {
  let first_name = $("#dep_first_name").val();
  let last_name_id = $("#dep_last_name_id").val();
  let phone = $("#dep_phone").val();
  let mobile = $("#dep_mobile_no").val();
  let gender = $('input[name="gender"]').is(":checked");
  let dob_id = $("#dep_dob_id").val();
  let email = $("#dep_email").val();
  let workEmail = $("#dep_work_email").val();
  let dep_employee_code = $("#dep_employee_code").val();
  var dep_ssn = $("#dep_ssn").val();
  var ssnPattern = /^\d{3}-\d{2}-\d{4}$/;
  $("#dep_first_name_error").html("");
  $("#dep_phone_error").html("");
  $("#dep_address2_error").html("");
  $("#dep_last_name_error").html("");
  $("#dep_mobile_error").html("");
  $("#dep_dob_error").html("");
  $("#dep_radio_type_error").html("");
  $("#dep_state_error").html("");
  $("#dep_city_error").html("");
  $("#dep_zip_code_error").html("");
  $("#dep_address1_error").html("");
  $("#dep_email_error").html("");
  $("#dep_ssn_error").html("");
  $("#dep_employee_code_error").html("");
  var temp = 0;
  if (first_name.trim() == "") {
    $("#dep_first_name_error").html("Please enter First Name");
    temp++;
  }

  if (last_name_id.trim() == "") {
    $("#dep_last_name_error").html("Please enter Last Name");
    temp++;
  }

  if (mobile.trim() == "") {
    $("#dep_mobile_error").html("Please enter Mobile");
    temp++;
  }

  if (dob_id.trim() == "") {
    $("#dep_dob_error").html("Please enter Date of Birth");
    temp++;
  }
  if (gender == false) {
    $("#dep_address2_error").html("Please select Gender");
    temp++;
  }

  if (workEmail != "") {
    var emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;

    if (emailRegex.test(workEmail)) {
      $("#dep_work_email_error").html("");
    } else {
      $("#dep_work_email_error").html(
        "Please enter a valid work email address"
      );
      temp++;
    }
  }

  if (email != "") {
    var emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;

    if (emailRegex.test(email)) {
      $("#dep_email_error").html("");
    } else {
      $("#dep_email_error").html("Please enter a valid email address");
      temp++;
    }
  }
  if (dep_ssn.trim() == "") {
    $("#dep_ssn_error").html("Please enter SSN");
    temp++;
  } else {
    if (ssnPattern.test(dep_ssn)) {
    } else {
      $("#dep_ssn_error").html("Invalid SSN format");
      temp++;
    }
  }

  if (dep_employee_code.trim() == "") {
    $("#dep_employee_code_error").html("Please enter Employee Code");
    temp++;
  }
  if (temp != 0) {
    return false;
  } else {
    $.confirm({
      title: "Are you sure?",
      content:
        "The provided data is accurate and relevant, and do you wish to proceed with submission?",
      type: "blue",
      columnClass: "col-md-9",
      buttons: {
        submit: {
          text: "Confirm",
          btnClass: "btn-blue",
          action: function () {
            var formData = new FormData($("#add_new_hub")[0]);
            formData.append("_token", _CSRF_TOKEN);
            $.ajax({
              async: false,
              global: false,
              type: "POST",
              url: _SAVE_HUB_DETAILS,
              data: formData,
              processData: false,
              contentType: false,
              success: function (res) {
                toastr.success(res.error_msg);
                window.location.href = _REDIRECTION_URL;
              },
              error: function (jqr) {
                toastr.error(jqr.responseJSON.error_msg);
              },
            });
          },
        },
        cancel: {
          text: "Cancel",
        },
      },
    });
  }
});

function getCountyByZipCode(val) {
  $.ajax({
    async: false,
    global: false,
    url: _GET_COUNTRY_CODE,
    type: "post",
    data: {
      zip_code: val,
      _token: _CSRF_TOKEN,
    },
    success: function (response) {
      if (response != "County not found") {
        $("#county").val(response);
      } else {
        $("#county").val("");
      }
    },
  });
}

function clearModal() {
  $("#add_new_hub")[0].reset();
  $("#locationId").val("").trigger("change");
  $("#other_gender_div").hide();
  $("#dep_other_gender").val("");
}

function isNumber(evt) {
  evt = evt ? evt : window.event;
  var charCode = evt.which ? evt.which : evt.keyCode;
  if (
    (charCode != 46 || $(this).val().indexOf(".") != -1) &&
    (charCode < 48 || charCode > 57)
  ) {
    return false;
  }
  return true;
}

if (typeof urlToken != "undefined") {
  $("#created_by_ny").tokenInput(urlToken, {
    tokenLimit: 1,
    zindex: 9999,
    onAdd: function (item) {
      $("#created_by_ny_id").val(item.id);
      $("#created_by_ny_name").val(item.name);
    },
    onDelete: function (item) {
      $("#created_by_ny_id").val("");
      $("#created_by_ny_name").val("");
    },
  });
}

// $("#dob").datepicker({
//     buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
// });

function saveImport() {
  $("#import-save").attr("disabled", "");
  $("#importLoader").show();
  $("#importResponseMsg").hide().html("");
  var agency_ids = $("#agency_ids").val();
  var fimagesG = $('input[name="images"]').prop("files");
  var uniqueFieldsChecked = $('input[name="unique_fields[]"]:checked').length;
  var add_remove = $("#add_remove").val();
  var filetype = $('input[name="filetype"]:checked').val();
  var cnt = 0;
  $("#images_error").html("");
  $("#agency_error").html("");
  $("#unique_fields_error").html("");
  $("#add_remove_error").html("");

  if (filetype != "master_file") {
    if (agency_ids == null || agency_ids == "") {
      $("#agency_error").html("Please select agency.");
      cnt = 1;
    }
  }
  if (fimagesG.length == 0) {
    $("#images_error").html("Csv file is required.");
    cnt = 1;
  } else {
    var FileUploadPath = fimagesG[0].name;
    var Extension = FileUploadPath.substring(
      FileUploadPath.lastIndexOf(".") + 1
    ).toLowerCase();
    if (Extension == "csv") {
    } else {
      $("#images_error").html("Only csv file allowed");
      cnt = 1;
    }
  }
  if (uniqueFieldsChecked == 0) {
    $("#unique_fields_error").html(
      "Please select at least one unique field for duplicate checking."
    );
    cnt = 1;
  }
  if (add_remove == null || add_remove == "") {
    $("#add_remove_error").html("Please select add / deactivate option.");
    cnt = 1;
  }

  if (cnt == 1) {
    $("#importLoader").hide();
    return false;
  } else {
    var foms = $("#formnew")[0];
    var formData = new FormData(foms);
    formData.append("_token", _CSRF_TOKEN);
    $("#importResponseMsg").html("").show();
    $.ajax({
      processData: false,
      contentType: false,
      type: "POST",
      url: IMPORT_DATA,
      data: formData,
      success: function (res) {
        let msg = "";
        if (res.summary) {
          msg = `<div class='alert alert-success'>Imported: ${res.summary.imported}, Updated: ${res.summary.updated}, Skipped: ${res.summary.skipped}, Deactivated: ${res.summary.deactivated}`;
          if (res.summary.errors && res.summary.errors.length > 0) {
            msg += `<br><b>Errors:</b><ul style='text-align:left;'>`;
            res.summary.errors.forEach(function (e) {
              msg += `<li>${e}</li>`;
            });
            msg += "</ul>";
          }
          msg += "</div>";
        } else if (res.queued && res.message) {
          msg = `<div class='alert alert-warning'>${res.message}</div>`;
        } else if (res.message) {
          msg = `<div class='alert alert-danger'>${res.message}</div>`;
        } else {
          msg = `<div class='alert alert-danger'>Import failed.</div>`;
        }
        $("#importLoader").hide();
        $("#importResponseMsg").html(msg).show();
        if (res.status && res.summary && res.summary.imported > 0) {
          hubList(1);
          $("#importModal").modal("hide");
        }
        $("#import-save").attr("disabled", "disabled");
      },
      error: function (jqr) {
        $("#importLoader").hide();
        let msg = `<div class='alert alert-danger'>Import failed. Please try again.</div>`;
        if (jqr.responseJSON && jqr.responseJSON.message) {
          msg = `<div class='alert alert-danger'>${jqr.responseJSON.message}</div>`;
        }
        $("#importResponseMsg").html(msg).show();
      },
    });
  }
}

$(document).on("click", ".hub_record_log .pagination a", function (e) {
  e.preventDefault();
  let page = $(this).attr("href").split("page=")[1];
  loadImportLogs(page);
});

function refreshImport() {
  $("#log-file-name").val("");
  $("#log-status").val("");
  $("#add_remove").val("");
  $("#log-date-range").val("");
  loadImportLogs(1);
}

function openImportModal() {
  $("#import-modal").modal("show");
  $("#importResponseMsg").hide().html("");
  $("#importLoader").hide();
  $("#agency_ids").val("").trigger("change");
  $("#images_error").html("");
  $("#add_remove_error").val("");
  $("#agency_error").html("");
  $("#unique_fields_error").html("");
  $('input[name="unique_fields[]"]').prop("checked", false);
  $("#formnew")[0].reset();
  $("#import-save").attr("disabled", false);
}

function openCreateModel() {
  $("#add_new_hub")[0].reset();
  $("#hubModal").modal("show");
  $("#agency_name_error").html("");
  $("#last_name_error").html("");
  $("#phone_error").html("");
  $("#mobile_error").html("");
  $("#dob_error").html("");
  $("#radio_type_error").html("");
  $("#email_error").html("");
  $("#other_name_error").html("");
  $("#address2_error").html("");
}

$("#agency_ids,#timeidnew,#add_remove").on("change", function () {
  $("#import-save").attr("disabled", false);
});

$('input[name="unique_fields[]"]').on("change", function () {
  $("#import-save").attr("disabled", false);
  $("#unique_fields_error").html("");
});

$('input[name="filetype"]').on("change", function () {
  let selectedValue = $(this).val();
  if (selectedValue === "master_file") {
    $("#company_div").hide();
    $('#sampleFileLink').attr('href', masterFile);
  } else {
    $("#company_div").show();
    $('#sampleFileLink').attr('href', companyFile);
  }
});
$("#dep_ssn").keyup(function () {
  var val = this.value.replace(/\D/g, "");
  val = val.replace(/^(\d{3})/, "$1-");
  val = val.replace(/-(\d{2})/, "-$1-");
  val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
  this.value = val;
});

function updateStatusDetails() {
  var selected_data = [];
  $(".cbox").each(function () {
    if ($(this).is(":checked")) {
      selected_data.push($(this).val());
    }
  });
  $("#hub_record_ids").val("");
  if (selected_data.length > 0) {
    $("#hub_record_ids").val(selected_data.join(","));
    $("#statusmodalid").attr("data-target", "#statusmodal");
  } else {
    $("#statusmodalid").attr("data-target", "");
    toastr.error("Please select at least one Hub Records.");
  }
  $("#hub_status_error").html("");
}

function updateHubStatus() {
  var status = $("#status_id").val();
  var cnt = 0;
  $("#hub_status_error").html("");
  if (status == "") {
    $("#hub_status_error").html("Please select Status");
    cnt = 1;
  }
  if (status == $("#status_id_text").data("status")) {
    return false;
  }
  if (cnt == 1) {
    return false;
  } else {
    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _HUB_UPDATE_STATUS,
      data: {
        hub_record_ids: $("#hub_record_ids").val(),
        status: status,
        _token: _CSRF_TOKEN,
      },
      success: function (res) {
        toastr.success(res.error_msg);
        hubList(1);
        $("#close_status").click();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}
