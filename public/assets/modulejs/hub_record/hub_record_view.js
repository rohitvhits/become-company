loadDependentData(1);
loadAllEligibility();
loadAllUitlization();

// Toggle Other Gender text box on view page basic details
$("body").on("change", 'input[name="basic_gender"]', function () {
  if ($(this).val() == "other") {
    $("#basic_other_gender_div").show();
  } else {
    $("#basic_other_gender_div").hide();
    $("#basic_other_gender").val("");
  }
});

// Toggle Other Gender text box on create modal
$("body").on("change", '#hub_add_modal input[name="gender"]', function () {
  if ($(this).val() == "other") {
    $("#other_gender_div").show();
  } else {
    $("#other_gender_div").hide();
    $("#dep_other_gender").val("");
  }
});

function setBasicDetails() {
  $("#dob_error").html("");
  $("#last_name_error").html("");
  $("#first_name_error").html("");
  $("#ssn_error").html("");
  $("#gender_error").html("");
  $("#other_insurance_name_error").html("");
  $(".basic-detail-div").find(".show, .hide").toggleClass("show hide");
  getBasicDeatils();
}

function saveBasicDetails() {
  var temp = 0;

  var last_name_id = $("#last_name_id").val();
  var first_name_id = $("#first_name_id").val();
  var dob_id = $("#dob_id").val();
  var ssn = $("#ssn_id").val();
  var ssnPattern = /^\d{3}-\d{2}-\d{4}$/;
  $("#dob_error").html("");
  $("#payment_type_error").html("");
  $("#last_name_error").html("");
  $("#first_name_error").html("");
  $("#other_insurance_name_error").html("");

  if (last_name_id.trim() == "") {
    $("#last_name_error").html("Please enter Last Name");
    temp++;
  }

  if (first_name_id.trim() == "") {
    $("#first_name_error").html("Please enter First Name");
    temp++;
  }

  if (dob_id == "") {
    $("#dob_error").html("Please select Date of Birth");
    temp++;
  }

  if (_AUTH_VIEW_SSN == 1) {
    if (ssn.trim() == "") {
      $("#ssn_error").html("Please enter SSN");
      temp++;
    } else {
      if (ssnPattern.test(ssn)) {
      } else {
        $("#ssn_error").html("Invalid SSN format");
        temp++;
      }
    }
  }

  var selectedGender = $('input[name="basic_gender"]:checked').val();
  if (!selectedGender) {
    $("#gender_error").html("Please select Gender");
    temp++;
  }
  if (selectedGender == "other" && $("#basic_other_gender").val().trim() == "") {
    $("#gender_error").html("Please enter Other Name");
    temp++;
  }

  if (temp == 0) {
    $.ajax({
      async: false,
      global: false,
      url: SAVE_BASIC_DETAILS,
      type: "post",
      data: {
        id: _RECORD_ID,
        _token: _CSRF_TOKEN,
        patient_code: $("#patient_code").val(),
        last_name: last_name_id,
        first_name: first_name_id,
        middle_name: $("#middle_name").val(),
        dob: dob_id,
        ssn: $("#ssn_id").val(),
        gender: selectedGender,
        other_gender: selectedGender == "other" ? $("#basic_other_gender").val() : "",
      },
      success: function (response) {
        toastr.success(response.error_msg);
        getBasicDeatils(response);
        setBasicDetails();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  } else {
    return false;
  }
}

function getBasicDeatils(response) {
  if (response == undefined) {
    $.ajax({
      async: false,
      global: false,
      url: GET_BASIC_DETAILS,
      type: "get",
      data: {
        id: _RECORD_ID,
      },
      success: function (response) {
        response = response.data;
        $("#first_name_id").val(response.first_name);
        $("#middle_name").val(response.middle_name);
        $("#last_name_id").val(response.last_name);
        $("#dob_id").val(moment(response.dob).format("MM/DD/YYYY"));
        var val = response.ssn.replace(/\D/g, "");
        val = val.replace(/^(\d{3})/, "$1-");
        val = val.replace(/-(\d{2})/, "-$1-");
        val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
        $("#ssn_id").val(val);
        if (response.gender) {
          $('input[name="basic_gender"][value="' + response.gender + '"]').prop("checked", true);
          if (response.gender == "other") {
            $("#basic_other_gender_div").show();
            $("#basic_other_gender").val(response.other_gender);
          } else {
            $("#basic_other_gender_div").hide();
            $("#basic_other_gender").val("");
          }
        }
      },
    });
  } else {
    data = response.data;
    $("#basic_first_name").html(data.first_name);
    $("#basic_middle_name").html(data.middle_name);
    $("#basic_last_name").html(data.last_name);
    $("#patient_dob").html(moment(data.dob).format("MM/DD/YYYY"));
    var val = data.ssn.replace(/\D/g, "");
    val = val.replace(/^(\d{3})/, "$1-");
    val = val.replace(/-(\d{2})/, "-$1-");
    val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
    $("#patient_ssn").html(val);
    $("#basic_location_branch").html(data.location_branch);
    // Update gender display
    var genderDisplay = data.gender ? data.gender.charAt(0).toUpperCase() + data.gender.slice(1) : "N/A";
    if (data.gender == "other" && data.other_gender) {
      genderDisplay += " (" + data.other_gender + ")";
    }
    $("#basic_gender").html(genderDisplay);
    $("#top_gender_display").html(genderDisplay);
  }
}

function setAddressDetails() {
  $(".address-detail-div").find(".show, .hide").toggleClass("show hide");
  getAddressDeatils();
}

function saveAddressDetails() {
  var temp = 0;
  if (temp == 0) {
    $.ajax({
      async: false,
      global: false,
      url: SAVE_ADDRESS_DETAILS,
      type: "post",
      data: {
        id: _RECORD_ID,
        _token: _CSRF_TOKEN,
        county: $("#county").val(),
        state: $("#state").val(),
        city: $("#city").val(),
        address1: $("#address1").val(),
        address2: $("#address2").val(),
        zip_code: $("#zip_code").val(),
        email: $("#email").val(),
        emergency_contact_name: $("#emergency_contact_name").val(),
        emergency_phone: $("#emergency_phone").val(),
      },
      success: function (response) {
        toastr.success(response.error_msg);
        getAddressDeatils(response);
        setAddressDetails();
      },
    });
  } else {
    return false;
  }
}

function getAddressDeatils(response) {
  if (response == undefined) {
    $.ajax({
      async: false,
      global: false,
      url: GET_BASIC_DETAILS,
      type: "get",
      data: {
        id: _RECORD_ID,
      },
      success: function (response) {
        response = response.data;
        $("#country").val(response.country);
        $("#state").val(response.state);
        $("#city").val(response.city);
        $("#address1").val(response.address1);
        $("#address2").val(response.address2);
        $("#zipcode").val(response.zip_code);
        $("#email").val(response.email);
      },
    });
  } else {
    data = response.data;
    $("#basic_country").html(data.country);
    $("#basic_state").html(data.state);
    $("#basic_city").html(data.city);
    $("#basic_address1").html(data.address1);
    $("#basic_address2").html(data.address2);
    $("#basic_zipcode").html(data.zip_code);
    $("#basic_email").html(data.email);
  }
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

function getCountyByZipCode(val) {
  $.ajax({
    async: false,
    global: false,
    url: GET_COUNTY,
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

$("#dob_id").datepicker({
  maxDate: 0,
  buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});

$("#ssn").keyup(function () {
  var val = this.value.replace(/\D/g, "");
  val = val.replace(/^(\d{3})/, "$1-");
  val = val.replace(/-(\d{2})/, "-$1-");
  val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
  this.value = val;
});

function mobileOrPhoneCopy(type) {
  let number = "";
  if (type == "mobile") {
    number = $("#hub_mobile_id").html();
  } else {
    number = $("#hub_phone_id").html();
  }

  let number1 = number.replace(/\D/g, "");
  navigator.clipboard
    .writeText(number1.toString())
    .then(() => toastr.success("Copied successfully"))
    .catch((err) => console.error("Error copying:", err));
}

function updateLanguageDetails() {
  $("#language_id").val($("#record_languages_id").val());
  $("#language_error").html("");
}

function updateMobileDetails() {
  $("#hub_record_mob_id").val($("#hub_mobile_id").text());
  $("#hub_mob_error").html("");
}

function updateHubMobile() {
  var mobileNoId = $("#hub_record_mob_id").val();
  var cnt = 0;
  $("#hub_mob_error").html("");
  if (mobileNoId == "") {
    $("#hub_mob_error").html("Please enter Mobile");
    cnt = 1;
  }

  if (cnt == 1) {
    return false;
  } else {
    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _HUB_UPDATE_MOBILE,
      data: {
        hub_id: _RECORD_ID,
        mobile: mobileNoId,
        _token: _CSRF_TOKEN,
      },
      success: function (res) {
        toastr.success(res.error_msg);
        $("#hub_mobile_id").html(mobileNoId);
        $("#close_hub_mobile").click();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}

function updatePhoneDetails() {
  $("#hub_record_phn_id").val($("#hub_phone_id").text());
  $("#hub_phn_error").html("");
}

function updateStatusDetails() {
  $("#hub_status_id").val($("#status_id_text").data("status"));
  $("#status_id").val($("#status_id_text").data("status"));
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
        hub_id: _RECORD_ID,
        hub_agency_id: $("#hub_agency_id").val(),
        status: status,
        _token: _CSRF_TOKEN,
      },
      success: function (res) {
        toastr.success(res.error_msg);
        var statusValue = `<span><label class="badge badge-danger">Deactivated</label></span>`;
        if (status == "active") {
          statusValue = `<span><label class="badge badge-success">Active</label></span>`;
        }
        $("#status-value").html(statusValue);
        $("#status_id_text").data("status", status);
        $("#close_status").click();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}
function updateHubPhone() {
  var phoneNoId = $("#hub_record_phn_id").val();
  var cnt = 0;
  $("#hub_phn_error").html("");
  if (phoneNoId == "") {
    $("#hub_phn_error").html("Please enter Phone");
    cnt = 1;
  }

  if (cnt == 1) {
    return false;
  } else {
    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _HUB_UPDATE_PHONE,
      data: {
        hub_id: _RECORD_ID,
        phone: phoneNoId,
        _token: _CSRF_TOKEN,
      },
      success: function (res) {
        toastr.success(res.error_msg);
        $("#hub_phone_id").html(phoneNoId);
        $("#close_hub_phone").click();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}

function updateHubLanguage() {
  var language_id = $("#hub_language_id").val();
  var cnt = 0;
  $("#hub_language_error").html("");

  if (language_id == "") {
    $("#hub_language_error").html("Please select Language");
    cnt = 1;
  }

  if (cnt == 1) {
    return false;
  } else {
    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _HUB_UPDATE_LANGUAGE,
      data: {
        hub_id: _RECORD_ID,
        language_id: language_id,
        _token: _CSRF_TOKEN,
      },
      success: function (res) {
        toastr.success(res.error_msg);
        var languageText = $("#hub_language_id option:selected").text();
        $("#hub_record_languages_id").val(language_id);
        $("#hub_record_languages_res_id").html(languageText);
        $("#close_hub_language").click();
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}

var i = 0;
var smsCounter = 0;
var globalNotesArray = [];
function loadAllNotes() {
  $(".notes-messages").html("");
  $("#loadertag1").attr("style", "");
  $.ajax({
    url: _HUB_NOTES + "/" + _RECORD_ID,
    type: "get",
    data: {
      hub_record_agency_id: $("#hub_agency_id option:selected").attr(
        "data-attr"
      ),
      hub_agency_id: $("#hub_agency_id").val(),
    },
    success: function (response) {
      globalNotesArray = response.data;
      if (response.data.length == 0) {
        $("#loadertag1").attr("style", "display:none;");
        $(".notes-messages").html(
          '<div class="text-center" style="margin-top: 20px;"><span class="text-muted"><b>No Notes Found</b></span></div>'
        );
      } else {
        response.data.forEach((element) => {
          add_message_obj(
            element.name,
            element.message,
            element.created_date,
            element.first_name + "" + element.last_name,
            element.flag,
            element.id
          );
        });
        setTimeout(() => {
          $("#loadertag1").attr("style", "display:none;");
        }, 3000);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
    },
  });
  return false;
}

function sendMessagefile() {
  var alldata = [];
  var message = $("#text-sms-box").val();
  var subject = $("#subjectNotesId").val();
  var subjectText = $("#subjectNotesId").text();
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();

  cnt = 0;
  if (subject == "") {
    cnt = 1;
    $("#subject_name_error").html("Enter Subject");
  }
  if (message.trim() == "") {
    cnt = 1;
    $("#notes_error_msg").html("Enter Message");
  }
  if (cnt == 0) {
    $.ajax({
      async: false,
      global: false,
      type: "POST",
      data: {
        _token: _CSRF_TOKEN,
        "msg-box": message.trim(),
        subject: subject.trim(),
        hub_record_agency_id: hub_record_agency_id,
        hub_agency_id: hub_agency_id,
      },
      url: _SAVE_HUB_NOTES + "/" + _RECORD_ID,
      success: function (response) {
        $("#send_message_loader").addClass("hide");
        $("#text-sms-box").val("");
        globalNotesArray.push(response);
        $("#subjectNotesId").val("");
        $("#notes_error_msg").html("");
        $("#subject_name_error").html("");
        $("#add-notes").modal("hide");
        loadAllNotes();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#send_message_loader").addClass("hide");
      },
    });
  } else {
    $("#notes_error_msg").html("Enter Message");
  }
}

function addSMSmessage(name, msg) {
  smsCounter = smsCounter + 1;
  var inner = $(".notes-messages");
  var time = new Date();
  var hours = time.getHours();
  var minutes = time.getMinutes();
  if (hours < 10) hours = "0" + hours;
  if (minutes < 10) minutes = "0" + minutes;
  var id = "msg-" + smsCounter;
  var idname = name.replace(" ", "-").toLowerCase();
  var date =
    time.getMonth() + 1 + "/" + time.getDate() + "/" + time.getFullYear();
  inner.append(
    '<div class="notes"><p id="' +
      id +
      '" class="user-' +
      idname +
      '"><div class="notes-header"><div class="messags-div"><div class="message-footer"><p class="pl-1" style="display:flex;margin-bottom:0px"> ' +
      CREATED_USER_NAME +
      ' </p><div class="time" style="display:flex;align-items:center"> <span>' +
      date +
      " " +
      hours +
      ":" +
      minutes +
      '</span> </div></div></div> <p class="msg notes-content text-mute" style="white-space:pre-line"><b>Subject:</b> ' +
      name +
      '<span class="pull-right"></span></p><p class="msg notes-content text-mute" style="white-space:pre-line">' +
      msg +
      '<span class="pull-right"></span></p></div>'
  );

  $("#" + id)
    .hide()
    .fadeIn(800);
  $("#sms-messages").animate(
    {
      scrollTop: inner.height(),
    },
    1000
  );
}

function add_message_obj(name, msg, date, created_name, flag = 0, mid = 0) {
  var i = 0;

  i = i + 1;

  var inner = $(".notes-messages");
  var time = new Date(date);
  var date =
    time.getMonth() + 1 + "/" + time.getDate() + "/" + time.getFullYear();

  var hours = time.getHours();
  var minutes = time.getMinutes();
  if (hours < 10) hours = "0" + hours;
  if (minutes < 10) minutes = "0" + minutes;
  var id = "msg-" + i;
  var idname = "";
  var flag_html = "";
  if (flag == 0) {
    var flags = "Flag";
    var flagClass = "messags-div";
    var flagButtonclass = "pull-right btn-sm  d-none d-md-block mr-2 ml-2";
  } else {
    var flags = "Flagged";
    var flagClass = "messags-div-flag";
  }
  if (_HUB_RECORD_NOTES_PERMISSION) {
    flag_html =
      '<a class="' +
      flagButtonclass +
      '" onclick="flagNotesChange(' +
      mid +
      ');" class="" title="' +
      flags +
      '"><i class="fa fa-flag"></i> <span id="view_flag_text_' +
      mid +
      '">' +
      flags +
      "</span></a>";
  }
  inner.append(
    '<div class="notes"><p id="' +
      id +
      '" class="user-' +
      idname +
      '"><div class="notes-header"><div id="flag_div_id_' +
      id +
      '" class=" messags-div ' +
      flagClass +
      '"><div class="message-footer"><p class="pl-' +
      idname +
      '" style="display:flex;margin-bottom:0px"> ' +
      created_name +
      " </p>" +
      flag_html +
      '<div class="time" style="display:flex;align-items:center"> <span>' +
      date +
      " " +
      hours +
      ":" +
      minutes +
      '</span> </div></div></div> <p class="msg notes-content text-mute" style="white-space:pre-line"><b>Subject:</b> ' +
      name +
      '<span class="pull-right"></span></p><p class="msg notes-content text-mute" style="white-space:pre-line">' +
      msg +
      '<span class="pull-right"></span></p></div></p></div>'
  );

  $("#" + id)
    .hide()
    .fadeIn(800);

  $("#sms-messages").animate(
    {
      scrollTop: inner.height(),
    },
    20
  );
}

function loadDocumentAjaxList(page = 1) {
  $("#loaderDocument").attr("style", "");
  $("#hub_document_response_list").html("");
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();
  $.ajax({
    type: "GET",
    url: _HUB_DOCUMENT_LIST + "/" + _RECORD_ID,
    data: {
      id: _RECORD_ID,
      page: page,
      hub_record_agency_id: hub_record_agency_id,
      hub_agency_id: hub_agency_id,
    },
    success: function (res) {
      $("#loaderDocument").attr("style", "display:none");
      $("#hub_document_response_list").html("");
      $("#hub_document_response_list").html(res);
    },
  });
  return false;
}

$(document).on("click", ".pagination a", function (e) {
  e.preventDefault();
  var page = $(this).attr("href").split("page=")[1];
  loadDocumentAjaxList(page);
});

function showDocModal() {
  $("#formnew")[0].reset();
  $(".image-cls").show();
  $("#did").val("");
  $("#images_error").html("");
  $("#document_id_error").html("");
}
function saveHubDoc() {
  $("#document_id_error").html("");
  $("#images_error").html("");
  let cnt = 0;
  let document_name = $("#datenew_id").val();
  let time_new = $("#timeidnew").val();
  let doc_id = $("#did").val();
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();

  if (document_name.trim() == "") {
    $("#document_id_error").html("Please enter Document Name");
    cnt = 1;
  }
  if (doc_id == "") {
    if (time_new.trim() == "") {
      $("#images_error").html("Please select Attachment");
      cnt = 1;
    } else {
      let fileExtensionType = ["pdf", "csv", "xlsx", "xls", "docx", "doc"];
      let files = $('input[name="images"]')[0].files;
      let fileName = files[0].name;
      let fileType = fileName.substr(fileName.lastIndexOf(".") + 1);
      $("#images_error").html("");
      if (
        $.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) ==
        -1
      ) {
        $("#images_error").html("Please select only pdf or csv file");
        cnt = 1;
      }
    }
  }

  if (cnt == 0) {
    $("#documentSave").prop("disabled", true);
    let formData = new FormData($("#formnew")[0]);
    formData.append("_token", _CSRF_TOKEN);
    formData.append("hub_record_agency_id", hub_record_agency_id);
    formData.append("hub_agency_id", hub_agency_id);

    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _SAVE_DOCUMENT_LIST + "/" + _RECORD_ID,
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        toastr.success(res.error_msg);
        $("#documentSave").prop("disabled", false);
        $("#hub-document-add").modal("hide");
        $("#document_service_id").val("").change();
        loadDocumentAjaxList();
        closeHubDoc();
      },
      error: function (jqXHR) {
        $("#documentSave").prop("disabled", false);
        toastr.error(jqXHR.responseJSON.error_msg);
      },
    });
  } else {
    return false;
  }
}

function closeHubDoc() {
  $("#formnew")[0].reset();
  $("#images_error").html("");
  $("#document_id_error").html("");
  $("#did").val("");
}

function deleteRecordDocument(recordId, documentId) {
  var url = _DELETE_DOCUMENT;
  $.confirm({
    title: "Delete",
    columnClass: "col-md-6",
    content: "Are you sure delete record?",
    buttons: {
      formSubmit: {
        text: "Delete",
        btnClass: "btn-danger",
        action: function () {
          $.ajax({
            url: url + "/" + recordId + "/" + documentId,
            data: {
              _token: _CSRF_TOKEN,
            },
            type: "POST",
            success: function (res) {
              toastr.success(res.error_msg);
              loadDocumentAjaxList();
            },
            error: function (jqXHR) {
              toastr.error(jqXHR.responseJSON.error_msg);
            },
          });
        },
      },
      cancel: function () {
        //close
      },
    },
  });
}

function editHubRecordDoc(id, document_name) {
  $("#formnew")[0].reset();
  $("#datenew_id").val(document_name);
  $("#did").val(id);
  $(".image-cls").hide();
}

function loadAllTextMessages() {
  $(".text-notes-messages").html("");
  $("#loadertag1").attr("style", "");
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();
  $.ajax({
    url: _GET_SMS_TEXT,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      hub_record_agency_id: hub_record_agency_id,
      hub_agency_id: hub_agency_id,
    },
    success: function (response) {
      var response = response.data;
      response.forEach((element) => {
        add_message_obj_new(
          element.id,
          element.user_details.full_name,
          "",
          element.message,
          element.created_date,
          element.type,
          element.user_details.id
        );
      });
      setTimeout(() => {
        $("#loadertag1").attr("style", "display:none;");
      }, 3000);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
    },
  });
  return false;
}

function add_message_obj_new(
  mid,
  name,
  img,
  msg,
  date,
  type,
  sender_id,
  clear
) {
  //alert(sender_id);
  i = i + 1;

  var inner = $(".text-notes-messages");
  var time = new Date(date);
  var date =
    time.getMonth() + 1 + "/" + time.getDate() + "/" + time.getFullYear();

  var hours = time.getHours();
  var minutes = time.getMinutes();
  if (hours < 10) hours = "0" + hours;
  if (minutes < 10) minutes = "0" + minutes;
  var id = "msg-" + i;
  //  var type="Receive";
  var ondelete = "";

  var idname = "";
  inner.append(
    '<p id="' +
      id +
      '" class="user-' +
      idname +
      '">' +
      '<span class="msg-block"><strong>' +
      name +
      '</strong><span class="time"> ' +
      date +
      " " +
      hours +
      ":" +
      minutes +
      "</span>" +
      '<span class="msg" style="margin-top:6px !important;">' +
      msg +
      '<span class="pull-right">' +
      ondelete +
      "</span></span></span></p>"
  );
  $("#" + id)
    .hide()
    .fadeIn(800);
  if (clear) {
    $(".text-chat-message textarea").val("").focus();
  }
  $("#text-sms-messages").animate(
    {
      scrollTop: inner.height(),
    },
    20
  );
}

function sendTextMessagefile() {
  var alldata = new FormData($("#textMessageSubmits")[0]);
  var id = _RECORD_ID;
  var name = "you";
  var mobile = _MOBILE;
  var message = $("#smsTextMessage").val();
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();

  alldata.append("mobile", _MOBILE);
  alldata.append("hub_record_id", id);
  alldata.append("message", message);
  alldata.append("_token", _CSRF_TOKEN);
  alldata.append("hub_record_agency_id", hub_record_agency_id);
  alldata.append("hub_agency_id", hub_agency_id);
  if (id != 0 && message != "") {
    $.ajax({
      type: "POST",
      data: alldata,
      url: _SEND_SMS_TEXT,
      dataType: "json",
      mimeType: "multipart/form-data",
      contentType: false,
      processData: false,

      success: function (response) {
        $("#textMessageSubmits")[0].reset();
        var response = response.data;
        i = i + 1;

        var inner = $(".text-notes-messages");
        var time = new Date(response.created_date);
        var date =
          time.getMonth() + 1 + "/" + time.getDate() + "/" + time.getFullYear();

        var hours = time.getHours();
        var minutes = time.getMinutes();
        if (hours < 10) hours = "0" + hours;
        if (minutes < 10) minutes = "0" + minutes;
        var id = "msg-" + Math.floor(Math.random() * 1000000);
        //  var type="Receive";
        var ondelete = "";

        var idname = "";
        inner.append(
          '<p id="' +
            id +
            '" class="user-' +
            idname +
            '">' +
            '<span class="msg-block"><strong>' +
            response.user_details.full_name +
            '</strong><span class="time"> ' +
            date +
            " " +
            hours +
            ":" +
            minutes +
            "</span>" +
            '<span class="msg">' +
            response.message +
            '<span class="pull-right">' +
            ondelete +
            "</span></span></span></p>"
        );
        $("#" + id)
          .hide()
          .fadeIn(800);

        $("#text-sms-messages").animate(
          {
            scrollTop: inner.height(),
          },
          20
        );
      },
      error: function (jqXHR, textStatus, errorThrown) {
        toastr.error(jqXHR.responseJSON.error_msg);
      },
    });
  } else {
    $("#smsTextMessageError").html("Please enter message");
    return false;
  }
}

function openNotesModel() {
  $("#text-sms-box").val("");
  $("#subjectNotesId").val("");
  $("#notes_error_msg").html("");
  $("#subject_name_error").html("");
}

function deleteRecord(id) {
  var url = _HUB_RECORD_DELETE;
  $.confirm({
    title: "Delete",
    columnClass: "col-md-6",
    content: "Are you sure delete record?",
    buttons: {
      formSubmit: {
        text: "Delete",
        btnClass: "btn-danger",
        action: function () {
          window.location.href = url + "/" + id;
        },
      },
      cancel: function () {
        //close
      },
    },
  });
}

function loadAllHubLogs(page = 1) {
  $(".hub_logs_id").html("");
  $.ajax({
    url: _GET_HUB_LOGS,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      page: page,
    },
    success: function (response) {
      $("#hub_logs_id").html(response);
    },
  });
  return false;
}

$(document).on("click", ".log-pagination a", function (e) {
  e.preventDefault();
  var page = $(this).attr("href").split("page=")[1];
  loadAllHubLogs(page);
});

$("#ssn_id").keyup(function () {
  var val = this.value.replace(/\D/g, "");
  val = val.replace(/^(\d{3})/, "$1-");
  val = val.replace(/-(\d{2})/, "-$1-");
  val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
  this.value = val;
});

$("#dep_ssn").keyup(function () {
  var val = this.value.replace(/\D/g, "");
  val = val.replace(/^(\d{3})/, "$1-");
  val = val.replace(/-(\d{2})/, "-$1-");
  val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
  this.value = val;
});

checkDuplicateRecord();
// check duplicate record if found add company on dropdown
function checkDuplicateRecord() {
  $.ajax({
    url: _CHECK_HUB_DUPLICATE,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
    },
    success: function (response) {
      let json = response.data;
      let htmlResponse = "";
      $.each(json, function (i, v) {
        if (v.agency_detail) {
          let selected = "";
          if (v.id == _RECORD_ID) {
            selected = "selected";
          }
          htmlResponse +=
            "<option value='" +
            v.agency_id +
            "' " +
            selected +
            " data-attr=" +
            v.id +
            ">" +
            v.agency_detail.agency_name.trim() +
            "</option>";
        }
      });
      $("#hub_agency_id").html(htmlResponse);
      $("#hub_agency_id").change();
    },
  });
  return false;
}

$("#hub_agency_id").change(function () {
  let id = $("#hub_agency_id option:selected").val();
  // get Agency wise data
  getAgencyWiseOtherData(id);
  loadAllNotes();
  loadDocumentAjaxList(1);
  loadAllTextMessages(1);
  loadAllEligibility();
  loadAllUitlization();
  loadAllNyBest();
});

// $("#dep_dob_id").datepicker({
//     maxDate: 0,
//     buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
// });

function loadDependentData(page) {
  $("#child_table").html("");
  $.ajax({
    url: GET_DEPENDENT_DATA,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      page: page,
    },
    success: function (response) {
      $("#child_table").html(response);
    },
  });
  return false;
}

function openAddChildForm() {
  $("#agency-div").attr("style", "display:none");
  // $('#hub_add_dependent_modal').modal('show');
  $(".error_html").html("");
  $("#hub_add_modal").modal("show");
  $("#hub_add_modal .modal-title").html("Create New Hub Dependent");
}
$("#saveHub").click(function (e) {
  let first_name = $("#dep_first_name").val();
  let last_name_id = $("#dep_last_name_id").val();
  let mobile = $("#dep_mobile_no").val();
  let gender = $('input[name="gender"]').is(":checked");
  let dob_id = $("#dep_dob_id").val();
  let email = $("#dep_email").val();
  let dep_ssn = $("#dep_ssn").val();
  let dep_employee_code = $("#dep_employee_code").val();
  var ssnPattern = /^\d{3}-\d{2}-\d{4}$/;

  $("#dep_first_name_error").html("");
  $("#dep_agency_name_error").html("");
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
            formData.append("agency_id", $("#hub_agency_id").val());
            formData.append("hub_record_id", _RECORD_ID);
            $.ajax({
              async: false,
              global: false,
              type: "POST",
              url: _SAVE_HUB_DEPENDENT_DETAILS,
              data: formData,
              processData: false,
              contentType: false,
              success: function (res) {
                if (res.status) {
                  toastr.success(res.error_msg);
                  loadDependentData(1);
                  $("#add_new_hub")[0].reset();
                  $(".close").click();
                } else {
                  toastr.error(res.error_msg);
                }
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

function clearModal() {
  $("#add_new_hub")[0].reset();
  $("#locationId").val("").trigger("change");
  $("#other_gender_div").hide();
  $("#dep_other_gender").val("");
}

function getAgencyWiseOtherData(agency_id) {
  $.ajax({
    url: GET_AGENCY_OTHER_DATA,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      agency_id: agency_id,
    },
    success: function (response) {
      if (response.data != "") {
        var status =
          response.data.status.charAt(0).toUpperCase() +
          response.data.status.slice(1);
        var last_worked_date = moment(
          response.data.last_worked_date,
          "YYYY-MM-DD",
          true
        ).isValid()
          ? moment(response.data.last_worked_date, "YYYY-MM-DD").format(
              "MM/DD/YYYY"
            )
          : "-";
        var work_contact =
          response.data.work_contact != "" ? response.data.work_contact : "-";
        var work_email =
          response.data.work_email != "" ? response.data.work_email : "";
        var employee_code =
          response.data.employee_code != "" ? response.data.employee_code : "";
        var member_id =
          response.data.member_id != "" ? response.data.member_id : "";
        let hire_date = moment(
          response.data.hire_date,
          "YYYY-MM-DD",
          true
        ).isValid()
          ? moment(response.data.hire_date, "YYYY-MM-DD").format("MM/DD/YYYY")
          : "-";
        if (status == "Active") {
          classed = "success";
        } else {
          classed = "danger";
        }
        $("#status-value").html(
          '<span><label class="badge badge-' +
            classed +
            '">' +
            status +
            "</label></span>"
        );
        $("#status_id_text").data("status", response.data.status);
        $("#hire_date_id").html(hire_date);
        $("#last_worked_date_id").html(last_worked_date);
        $("#work_contact_id").html(work_contact);
        $("#work_email_id").html(work_email);
        $("#employee_code_id").html(employee_code);
        $("#member_id").html(member_id);
      }
    },
  });
  return false;
}

function setOtherDetails() {
  $(".other-detail-div").find(".show, .hide").toggleClass("show hide");
  getExistingOtherDetails();
}

function getExistingOtherDetails() {
  $.ajax({
    url: GET_AGENCY_OTHER_DATA,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      agency_id: $("#hub_agency_id").val(),
    },
    success: function (response) {
      var last_worked_date = moment(
        response.data.last_worked_date,
        "YYYY-MM-DD",
        true
      ).isValid()
        ? moment(response.data.last_worked_date, "YYYY-MM-DD").format(
            "MM/DD/YYYY"
          )
        : "-";
      var work_contact =
        response.data.work_contact != "" ? response.data.work_contact : "-";
      var work_email =
        response.data.work_email != "" ? response.data.work_email : "";
      var employee_code =
        response.data.employee_code != "" ? response.data.employee_code : "";
      var member_id =
        response.data.member_id != "" ? response.data.member_id : "";
      let hire_date = moment(
        response.data.hire_date,
        "YYYY-MM-DD",
        true
      ).isValid()
        ? moment(response.data.hire_date, "YYYY-MM-DD").format("MM/DD/YYYY")
        : "-";

      $("#input_last_work_date_id").val(last_worked_date);
      $("#input_hire_date_id").val(hire_date);
      $("#input_work_contact_id").val(work_contact);
      $("#input_work_email_id").val(work_email);
      $("#input_employee_code_id").val(employee_code);
      $("#input_member_id").val(member_id);
    },
  });
  return false;
}

function saveOtherDetails() {
  var member_id = $("#input_member_id").val();
  var employee_code = $("#input_employee_code_id").val();
  var work_email = $("#input_work_email_id").val();
  var work_contact = $("#input_work_contact_id").val();
  var hire_date = $("#input_hire_date_id").val();
  var last_worked_date = $("#input_last_work_date_id").val();
  var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  var cnt = 0;

  $("#input_hire_date_id_error").html("");
  $("#input_employee_code_id_error").html("");
  $("#input_work_email_id_error").html("");
  $("#input_work_contact_id_error").html("");

  if (employee_code.trim() == "") {
    $("#input_employee_code_id_error").html("Please enter Employee Code");
    cnt = 1;
  }

  if (work_email.trim() == "") {
    $("#input_work_email_id_error").html("Please enter Work Email");
    cnt = 1;
  } else {
    if (work_email.trim() != "") {
      if (!regex.test(work_email)) {
        $("#input_work_email_id_error").html("Invalid Email Address");
        cnt = 1;
      }
    }
  }

  if (hire_date.trim() == "") {
    $("#input_hire_date_id_error").html("Please enter Hire Date");
    cnt = 1;
  }

  if (work_contact.trim() == "") {
    $("#input_work_contact_id_error").html("Please enter Work Contact");
    cnt = 1;
  }

  if (cnt == 1) {
    return false;
  } else {
    $.ajax({
      url: _UPDATE_AGENCY_WISE_DATA,
      type: "post",
      data: {
        hub_record_id: _RECORD_ID,
        agency_id: $("#hub_agency_id").val(),
        employee_code: employee_code,
        work_email: work_email,
        hire_date: hire_date,
        work_contact: work_contact,
        last_worked_date: last_worked_date,
        member_id: member_id,
        _token: _CSRF_TOKEN,
      },
      success: function (response) {
        toastr.success(response.error_msg);
        $(".other-detail-div").find(".hide, .show").toggleClass("hide show");
        $("#last_worked_date_id").html("");
        setTimeout(() => {
          getAgencyWiseOtherData($("#hub_agency_id").val());
        }, 1000);
      },
      error: function (jqr) {
        toastr.error(jqr.responseJSON.error_msg);
      },
    });
  }
}
function loadAllHubNyBest(page = 1) {
  $(".hub_nybest_id").html("");
  $.ajax({
    url: _GET_HUB_LOGS,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      page: page,
    },
    success: function (response) {
      $("#hub_nybest_id").html(response);
    },
  });
  return false;
}

$(document).on("click", ".log-pagination a", function (e) {
  e.preventDefault();
  var page = $(this).attr("href").split("page=")[1];
  loadAllHubNyBest(page);
});

function showNybestModal() {
  loadNybestAgency();
  $("#formnewNybest")[0].reset();
  $("#agency_error").html("");
  $("#service_error").html("");
  $("#nyservice").html("");
  $("#booking_date_error").html("");
}

function saveHubNybest() {
  let cnt = 0;
  var hub_record_agency_id = $("#hub_agency_id option:selected").attr(
    "data-attr"
  );
  var hub_agency_id = $("#hub_agency_id").val();
  var nybest_agency_id = $("#nybest_agency").val();
  var service = $("#nyservice").val();
  var booking_date = $("#booking_date").val();

  var selectedType = $('input[name="type"]:checked').val();

  if (selectedType.trim() == "") {
    $("#nybest_type_error").html("Please select Type");
    cnt = 1;
  }
  if (nybest_agency_id == "" || nybest_agency_id == null) {
    $("#agency_error").html("Please select Agency");
    cnt = 1;
  }
  if (service == "" || service == null) {
    $("#service_error").html("Please select Service");
    cnt = 1;
  }
  if (booking_date.trim() == "") {
    $("#booking_date_error").html("Please select Booking Date");
    cnt = 1;
  }
  if (cnt == 0) {
    $("#nybestSave").prop("disabled", true);
    let formData = new FormData($("#formnewNybest")[0]);
    formData.append("_token", _CSRF_TOKEN);
    formData.append("hub_record_agency_id", hub_record_agency_id);
    formData.append("hub_agency_id", hub_agency_id);

    $.ajax({
      async: false,
      global: false,
      type: "POST",
      url: _SAVE_DATA_NYBEST + "/" + _RECORD_ID,
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        toastr.success(res.error_msg);
        $("#nybestSave").prop("disabled", false);
        $("#hub-nybest-add").modal("hide");
        $("#document_service_id").val("").change();
        loadAllNyBest();
        closeHubDoc();
      },
      error: function (jqXHR) {
        $("#nybestSave").prop("disabled", false);
        toastr.error(jqXHR.responseJSON.error_msg);
      },
    });
  } else {
    return false;
  }
}

function loadAllUitlization(page = 1) {
  $("#uitlization_id").html("");
  $.ajax({
    url: _GET_HUB_UITLIZATION,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      hub_agency_id: $("#hub_agency_id").val(),
      ssn: $("#ssn_id").val(),
      page: page,
    },
    success: function (response) {
      $("#uitlization_id").html(response);
      var lastUtiDate = $("#lastUtiDate").data("uti-date");
      if (lastUtiDate == "" || lastUtiDate == undefined) {
        lastUtiDate = "-";
      }
      $("#last-utilization").html(`<h6>${lastUtiDate}</h6>`);
    },
  });
  return false;
}
function loadAllEligibility(page = 1) {
  $("#eligibility_id").html("");
  $.ajax({
    url: _GET_HUB_ELIGIBILITY,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      hub_agency_id: $("#hub_agency_id").val(),
      page: page,
    },
    success: function (response) {
      $("#eligibility_id").html(response);
      var lastDate = $("#lastDate").html();
      if (lastDate == "" || lastDate == undefined) {
        lastDate = "-";
      }
      $("#last-eligibility").html(`<h6>${lastDate}</h6>`);
    },
  });
  return false;
}

function loadNybestAgency() {
  $.ajax({
    url: _GET_NYBEST_AGENCY,
    type: "get",
    success: function (response) {
      let $agencySelect = $("#nybest_agency");
      $agencySelect.empty(); // Clear existing options

      $agencySelect.append('<option value="">Select Agency</option>'); // Optional default
      let agencies = Object.values(response.data); // convert to proper array
      agencies.forEach(function (agency) {
        $agencySelect.append(
          `<option value="${agency.id}">${agency.agency_name}</option>`
        );
      });
    },
  });
}

$("#nybest_agency").on("change", function () {
  const selectedAgencyId = $(this).val();
  const selectedType = $('input[name="type"]:checked').val(); //Output will be "Patient" or "Caregiver"
  getResponse(selectedType, selectedAgencyId); // Call your function with selected value
});
// Trigger when type radio changes
$('input[name="type"]').on("change", function () {
  const selectedAgencyId = $(this).val();
  const selectedType = $('input[name="type"]:checked').val(); //Output will be "Patient" or "Caregiver"
  getResponse(selectedType, selectedAgencyId); // Call your function with selected value
});

function getResponse(id, selectedAgencyId) {
  if (id != "") {
    var jsonencode = [];
    $.ajax({
      async: false,
      global: false,
      type: "GET",
      url: _TYPE_WISE_SERVICE_LIST,
      data: {
        id: id,
        agency_id: selectedAgencyId,
      },
      success: function (res) {
        if (res != "") {
          let $serviceSelect = $("#nyservice");
          $serviceSelect.empty(); // Clear existing options
          htmlsresp = res;
        } else {
          htmlsresp += '<option value="">No record available</option>';
        }
        $("#nyservice").html(htmlsresp);
      },
    });
  }
}

function loadAllNyBest(page = 1) {
  $(".hub_nybest_id").html("");
  $.ajax({
    url: _GET_NYBEST_LIST,
    type: "get",
    data: {
      hub_record_id: _RECORD_ID,
      hub_agency_id: $("#hub_agency_id").val(),
      page: page,
    },
    success: function (response) {
      $("#hub_nybest_id").html(response);
      var total = $("#nybest-total").data("nybest-total-record");
      if (total == "" || total == undefined) {
        total = "-";
      }
      $("#total-bybest-request").html(`<h6>${total}</h6>`);
    },
  });
  return false;
}

$(document).on("click", ".log-pagination a", function (e) {
  e.preventDefault();
  var page = $(this).attr("href").split("page=")[1];
  loadAllNyBest(page);
});
function sendSMS(id) {
  var url = _HUB_RECORD_SEND_DEPENDENT;
  var mobile = $("#hub_mobile_id").html();
  if (mobile.trim() == "") {
    toastr.error("Please Add mobile number to send link");
    return false;
  }
  $.confirm({
    title: `Send Link : ${mobile}`,
    columnClass: "col-md-6",
    content: "Are you sure you want to send the link to add a dependent?",
    buttons: {
      formSubmit: {
        text: "Send to SMS",
        btnClass: "btn-danger",
        action: function () {
          window.location.href = url + "/" + id;
        },
      },
      cancel: function () {
        //close
      },
    },
  });
}
function flagChange() {
  $.confirm({
    title: "Flag",
    columnClass: "col-md-6", // Adjust the width of the modal
    content: function () {
      // Returning HTML string for the input field
      var html =
        '<div><b><label for="reason">Reason:</label><b>' +
        '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason" spellcheck="false"></textarea></div>';
      return html;
    },
    buttons: {
      confirm: {
        text: "Confirm", // Text for the confirm button
        btnClass: "btn-primary", // Style class for the button
        action: function () {
          var reason = this.$content.find("#reason").val(); // Get the value of the reason input
          // AJAX request when Confirm is clicked
          $.ajax({
            global: false, // Disable global AJAX events
            url: _HUB_RECORD_FLAG, // URL to send the request to
            type: "GET", // HTTP method for the request
            data: {
              _token: _CSRF_TOKEN, // CSRF Token for security
              id: _RECORD_ID, // Record ID to identify which record to update
              reason: reason, // Send the reason with the request
            },
            success: function (response) {
              toastr.success(response.error_msg); // Display success message
              location.reload(); // Reload the page
            },
            error: function (xhr, status, error) {
              toastr.error(xhr.responseJSON.error_msg); // Display error message
            },
          });
        },
      },
      cancel: function () {
        // No action for cancel, just closes the dialog
      },
    },
  });
}

function flagNotesChange(id) {
  var url = _HUB_RECORD_NOTES_FLAG;
  $.confirm({
    title: "Flag",
    columnClass: "col-md-6",
    content: function () {
      // Returning HTML string for the input field
      var html =
        '<div><b><label for="reason">Reason:</label><b>' +
        '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_notes" spellcheck="false"></textarea></div>';
      return html;
    },
    buttons: {
      confirm: {
        text: "Confirm",
        btnClass: "btn-primary",
        action: function () {
          var reason_notes = this.$content.find("#reason_notes").val();
          $.ajax({
            global: false,
            url: url,
            type: "GET",
            data: {
              _token: _CSRF_TOKEN,
              id: id,
              reason: reason_notes,
            },
            success: function (response) {
              toastr.success(response.error_msg);
              loadAllNotes();
              $("#flag_div_id_" + id).removeClass("messags-div-flag");
              $("#flag_div_id_" + id).removeClass("messags-div");
              if (response.data.flag == "1") {
                $("#view_flag_text_" + id).html("Flagged");
                $("#flag_div_id_" + id).addClass("messags-div-flag");
              } else {
                $("#view_flag_text_" + id).html("Flag");
                $("#flag_div_id_" + id).addClass("messags-div");
              }
            },
            error: function (xhr, status, error) {
              toastr.error(xhr.responseJSON.error_msg);
            },
          });
        },
      },
      cancel: function () {},
    },
  });
}
function flagDocumentChange(id) {
  var url = _HUB_RECORD_DOC_FLAG;
  $.confirm({
    title: "Flag",
    columnClass: "col-md-6",
    content: function () {
      // Returning HTML string for the input field
      var html =
        '<div><b><label for="reason">Reason:</label><b>' +
        '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_doc" spellcheck="false"></textarea></div>';
      return html;
    },
    buttons: {
      confirm: {
        text: "Confirm",
        btnClass: "btn-primary",
        action: function () {
          var reason_doc = this.$content.find("#reason_doc").val(); // Get the value of the reason input
          $.ajax({
            global: false,
            url: url,
            type: "GET",
            data: {
              _token: _CSRF_TOKEN,
              id: id,
              reason: reason_doc, // Send the reason with the request
            },
            success: function (response) {
              toastr.success(response.error_msg);
              loadDocumentAjaxList();
            },
            error: function (xhr, status, error) {
              toastr.error(xhr.responseJSON.error_msg);
            },
          });
        },
      },
      cancel: function () {},
    },
  });
}
