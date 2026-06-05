/*!
 * Signer
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://simcycreative.com
 * Simcy Creative - <hello@simcycreative.com>
 * Private License
 */

/**
 * Insert property set object
 */


/*
 * close editor overlay
 */
var d = new Date();
var dat = d.getDate();
var mon = d.getMonth() + 1;
var year = d.getFullYear();
var DateSingDate = mon + "/" + dat + "/" + year;

var urls = $('.siteURL').val();
var dropArray = [];
var dropdownResponse = [];
var final_array = [];
var ConditionalTempArray = [];
var ConditionalSTempArray = [];
var RadiosArray = [];
var headers = [];
var TextArray = [];
var headersNew = [];
$(".close-editor-overlay").click(function () {
  if ($('.signer-assembler action').length > 0) {
    notify("Discard Changes?", "Your changes will be lost.", "warning", "Discard Changes", { showCancelButton: true, closeOnConfirm: true, callback: "closeEditor()" });
  } else {
    closeEditor();
  }
});

/*
 * function to close editor overlay
 */
function closeEditor() {
  $('.signer-assembler').empty();
  renderPage(pageNum);
  $(".signer-document").appendTo(".document");
  $("body").removeClass("editor");
  emptyBuilder();
};


/*
 * launch editor overlay
 */
$(".launch-editor").click(function () {
  inviting = false;
  enableTools();
  launchEditor();
  GetLoadComponents();
});
/*Docusign for mobile app */

function getLuncha() {
  inviting = false;

  launchEditor();

}

/*
 * function to launch editor overlay
 */
function launchEditor() {

  if (inviting) { $(".signer-save span").text("Send"); } else { $(".signer-save span").text("Save"); }
  $(".signer-document").appendTo(".signer-overlay-previewer");
  $("body").addClass("editor skin-blue sidebar-mini");
  $('#document-viewer').attr('width', 899);
  renderPage(pageNum);
};

/*
 * Copy to clipboard
 */
var clipboard = new Clipboard('.copy-link');
clipboard.on('success', function (e) {
  $('#sharefile').modal('hide');
  toastr.success("Link copied to clipboard.", "Copied!");
});
clipboard.on('error', function (e) {
  toastr.error("Failed to copy, please try again.", "Oops!");
});

/*
 * validate email
 */
function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

/*
 * Select users to send file
 */
$("input[name=send-select]").change(function () {
  var email = $(this).val();
  if ($(this).prop("checked")) {
    $('input[name=receivers]').tagsinput('add', email);
  } else {
    $('input[name=receivers]').tagsinput('remove', email);
  }
});

/*
 * Select users to send request
 */
$("input[name=request-select]").change(function () {
  var email = $(this).val();
  if ($(this).prop("checked")) {
    $('input[name=recipients]').tagsinput('add', email);
  } else {
    $('input[name=recipients]').tagsinput('remove', email);
  }
});


/*
 * Before an email is added to form
 */
$('input[name=receivers], input[name=recipients]').on('beforeItemAdd', function (event) {
  if (!isEmail(event.item)) {
    event.cancel = true;
    toastr.error("Enter a valid email address.", "Oops!");
  }
});


/*
 * After an email is added
 */
$('input[name=recipients]').on('itemAdded', function (event) {
  requestOptions();
});

/*
 * After an email is added
 */
$('input[name=recipients]').on('itemRemoved', function (event) {
  requestOptions();
});

/*
 * When request oprions are updated
 */
$("input[name=restricted], input[name=duplicate]").change(function () {
  requestOptions();
});


/*
 * Signing request options
 */
function requestOptions() {
  recipients = $("input[name=recipients]").tagsinput('items');
  if (recipients.length > 1) {
    $(".duplicate-request").show();
    $(".restricted-request").hide();
    if ($("input[name=duplicate]").prop("checked")) {
      $(".restricted-request").show();
    } else {
      if ($("input[name=restricted]").prop("checked")) {
        $("input[name=restricted]").click();
      }
    }
  } else {
    $(".duplicate-request").hide();
    $(".restricted-request").show();
    if ($("input[name=duplicate]").prop("checked")) {
      $("input[name=duplicate]").click();
    }
  }
}

/*
 * validate request
 */
function validateRequest() {
  if ($("input[name=restricted]").prop("checked")) {
    $("#sendRequest").modal("hide");
    inviting = true;
    launchEditor();
    enableTools("request");
  } else {
    sendRequest();
  }
}

/*
 * send request
 */
function sendRequest() {
  var emails = JSON.stringify($("input[name=recipients]").tagsinput('items')),
    message = $("textarea[name=requestmessage]").val(),
    duplicate = "No", positions = docWidth = '';
  if ($("input[name=duplicate]").prop("checked")) { duplicate = "Yes"; }
  if (isTemplate === "Yes" && templateFields !== '') {
    positions = JSON.stringify(templateFields);
    docWidth = "set";
  } else if ($("input[name=restricted]").prop("checked")) {
    orgnizeData(false);
    positions = prepareData(false);
    docWidth = $("#document-viewer").width();
  }
  server({
    url: sendRequestUrl,
    data: {
      "emails": emails,
      "message": message,
      "positions": positions,
      "duplicate": duplicate,
      "docWidth": docWidth,
      "document_key": document_key,
      "csrf-token": Cookies.get("CSRF-TOKEN"),
      "_token": tokens
    },
    loader: true
  });
}

/*
 * Set document password toggle
 */
$(".password-protect-toggle").change(function () {
  if ($(this).prop("checked")) {
    $('.protection-password').show();
    $('.protection-password').find("input").attr("required", true);
  } else {
    $('.protection-password').hide();
    $('.protection-password').find("input").attr("required", false);
  }
});

/*
 * Tools responsiveness
 */
$('#send-team .col-md-12, #send-customers .col-md-12').slimscroll({
  height: '200px',
  width: '100%',
  size: "3px",
  color: 'rgba(0, 0, 0, 0.8)'
});

/*
 * Tools responsiveness
 */
$('.right-bar-body').slimscroll({
  height: 'auto',
  position: 'right',
  size: "3px",
  color: '#9ea5ab'
});

/*
 * Toggle right bar
 */
$(".right-bar-toggle").click(function (event) {
  event.preventDefault();
  $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
  $(".right-bar." + $(this).attr("bar")).toggleClass("open");
  $(".right-bar-toggle").find("span").hide();
});

/*
 * Close
 */
$(".close-right-bar").click(function (event) {
  event.preventDefault();
  $(this).closest(".right-bar").removeClass("open");
});

$(function () {
  var timeToAccelerate;
  var clickedElement;
  $(".arrow").on("mousedown", function () {
    clickedElement = $(this);
    updateValue(clickedElement);

    timeToAccelerate = setInterval(function () {
      updateValue(clickedElement);
    }, 150);
  });
  $(document).on("mouseup", function () {
    clearInterval(timeToAccelerate);
  });
  function updateValue(element) {
    var value = parseInt(element.siblings("input").val(), 10);
    if (element.hasClass("up")) {
      value += 1;
    } else {
      value -= 1;
      if (value < 0) value = 0;
    }
    element.siblings("input").val(value);
    if (isDrawMode()) {
      modules.stroke(value);
    } else {
      updateTextSize(value);
    }
  }
});

/*
 * Post chat
 */
$(".new-message").keypress(function (e) {
  if (e.which == 13) {
    $(".empty-chat").remove();
    var message = $(this).val(), avatar = $(".user-avatar").attr("src"), chatId = random();
    $(".chat-list").append("<div class='chat-message chat-message-sender'><img class='chat-image chat-image-default' src='" + avatar + "' />" +
      "<div class='chat-message-wrapper'><div class='chat-message-content'><p>" + message + "</p></div><div class='chat-details'>" +
      "<span class='chat-message-localization font-size-small chat-" + chatId + "'>Sending....</span></div></div></div>");
    $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
    $(this).val("");
    e.preventDefault();
    server({
      url: postChatUrl,
      data: {
        "message": message,
        "chatId": chatId,
        "document_key": document_key,
        "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: false
    });
  }
});

/*
 *  chat response
 */
function chatResponse(sendTime, chatKey, chatId) {
  $('.chat-list').find(".chat-" + chatKey).text(sendTime);
  $('.chat-list').find(".chat-" + chatKey).closest(".chat-message").attr("id", chatId);
}

/*
 *  fetch chats 
 */
function getChats() {
  var data = {
    "lastChat": $('.chat-list').children().last().attr("id"),
    "document_key": document_key,
    "csrf-token": Cookies.get("CSRF-TOKEN")
  }
  var posting = $.post(getChatUrl, data);
  posting.done(function (data) {
    if (data != 'empty') {
      $(".chat-list").append(data);
      $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
      $(".empty-chat").remove();
      $('[data-toggle="tooltip"]').tooltip();
      if (!$(".right-bar").hasClass("open")) {
        $(".right-bar").addClass("open");
      }
    }
  });
}


/*
 *  check for new chats after 5seconds 
 */
if (getChatUrl !== '') {
  setInterval(function () {
    if ($(".chat-list").length) {
      getChats();
    }
  }, 5000);
}


/*
 *  Signer tools select
 */
$(".signer-tool").click(function (event) {
  event.preventDefault();
  if ($(this).hasClass("disabled")) {
    return false;
  }
  if ($(this).attr("action") === "true") {
    deselectElements();
    deactivateTools();
  }

  var tool = $(this).attr("tool");
  if (tool !== "rotate" && $('action[type=rotate]').length) {
    toastr.warning("Save rotation changes before editing document.", "Hmm!", { timeOut: 2000, closeButton: true, progressBar: false });
    return false;
  }
  if (tool === "rotate") {
    if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
      toastr.warning("Save changes before rotating.", "Hmm!", { timeOut: 2000, closeButton: true, progressBar: false })
    } else {
      rotatePage(pageNum);
    }
  } else if (tool === "image") {
    $("#selectImage").modal({ show: true, backdrop: 'static', keyboard: false });
  } else if (tool === "delete") {
    deleteElement();
  } else if (tool === "text") {
    enableTextMode();
  } else if (tool === "font") {
    $(".right-bar.font-list").toggleClass("open");
  } else if (tool === "symbol") {
    $(".right-bar.symbol-list").toggleClass("open");
  } else if (tool === "shape") {
    $(".right-bar.shape-list").toggleClass("open");
  } else if (tool === "fields") {
    if (auth) {
      $(".right-bar.fields-list").toggleClass("open");
    } else {
      loginRequired();
    }
  } else if (tool === "input") {
    if (auth) {
      if (isTemplate === "Yes" || inviting) {
        $(".right-bar.input-fields-list").toggleClass("open");
      } else {
        notify("Template Only", "Inputs are added to templates only. Do you want to create a template copy of this file?", "warning", "Yes, Create", { showCancelButton: true, closeOnConfirm: true, callback: "createTemplate()" });
      }
    } else {
      loginRequired();
    }
  } else if (tool === "color") {
    document.getElementById('color-picker').jscolor.show();
  } else if (tool === "duplicate") {
    duplicateSelected();
  } else if (tool === "signature") {
    enableSignatureMode();
  } else if (tool === "draw") {
    enableDrawMode();
  } else if (tool === "bold" || tool === "italic" || tool === "underline" || tool === "strikethrough" || tool === "alignright" || tool === "aligncenter" || tool === "alignleft") {
    styleText(tool);
  }
});

/*
 *  Rotate Page
 */

function objToString(obj, ndeep) {
  switch (typeof obj) {
    case "string": return '"' + obj + '"';
    case "function": return obj.name || obj.toString();
    case "object":
      var indent = Array(ndeep || 1).join('\t'), isArray = Array.isArray(obj);
      return ('{['[+isArray] + Object.keys(obj).map(function (key) {
        return '\n\t' + indent + (isArray ? '' : key + ': ') + objToString(obj[key], (ndeep || 1) + 1);
      }).join(',') + '\n' + indent + '}]'[+isArray]).replace(/[\s\t\n]+(?=(?:[^\'"]*[\'"][^\'"]*[\'"])*[^\'"]*$)/g, '');
    default: return obj.toString();
  }
}


function rotatePage(pageNum) {
  var degree = parseInt(getActualRotation(pageNum) + 90);
  if (degree == 360) { degree = 0; }
  assemble({ "type": "rotate", "page": pageNum, "degree": degree });
  renderPage(pageNum);
  $("#document-viewer").css("max-width", "100%");
}

/*
 *  Get actual page rotation
 */
function getActualRotation(pageNumber) {
  if ($("action[type=rotate][page=" + pageNumber + "]").length > 0) {
    rotationDegree = parseInt($("action[type=rotate][page=" + pageNumber + "]").attr("degree"));
  } else {
    rotationDegree = 0;
  }
  return rotationDegree;
}

/*
 *  Group completed actions
 */
function assemble(data, prepare) {

  if (prepare === undefined) {
    send = true;
  }
  if (data.group === undefined) {
    data.group = "field";
  }
  if (data.type === "rotate") {
    if ($("action[type=rotate][page=" + data.page + "]").length > 0) {
      if (data.degree == 0) {
        $("action[type=rotate][page=" + data.page + "]").remove();
      } else {
        $("action[type=rotate][page=" + data.page + "]").attr("degree", data.degree);
      }
    } else {
      $(".signer-assembler").append('<action type="rotate" group="' + data.group + '" page="' + data.page + '" degree="' + data.degree + '">');
    }
  } else if (data.type === "image" || data.type === "signature" || data.type === "symbol" || data.type === "shape" || data.type === "stamp") {
    if (data.group === "field") {
      var testinf = data.tempArray;
      if (data.tempArray != '') {
        var new_array = data.tempArray;
      } else {
        var new_array = '';
      }

      var tttttt = '<action id="' + data.id + '" type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" image="' + data.image + '" value="' + new_array + '" datats="' + data.type + '" required="' + data.required + '" readOnly="' + data.readOnly + '" background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '">';

      $(".signer-assembler").append(tttttt);


    } else if (data.group === "input") {

      $(".signer-assembler").append('<action type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" required="' + data.required + '" readOnly="' + data.readOnly + '">');
    }
  } else if (data.type === "drawing") {
    $(".signer-assembler").append('<action type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" drawing="' + data.drawing + '" >');
  } else if (data.type === "text") {

    var testinf = data.tempArray;
    if (data.tempArray != '') {
      var new_array = data.tempArray;
    } else {
      var new_array = '';
    }
    var checklength = $('#' + data.id).length;

    if (data.group === "field") {
      $(".signer-assembler").append('<action disabled="' + data.disabled + '" dataid="' + data.datats + '" id="' + data.id + '" placeHolder="' + data.palceHolder + '" type="text" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" value="' + data.text + '" datats="' + data.type + '" vishalpatel="' + data.required + '" readOnly="' + data.readOnly + '"  background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '">');

    } else if (data.group === "input") {

      $(".signer-assembler").append('<action disabled="' + data.disabled + '" dataid="' + data.datats + '" id="' + data.id + '"  placeHolder="' + data.palceHolder + '" type="text" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" fontfamily="' + data.fontfamily + '" underline="' + data.underline + '" strikethrough="' + data.strikethrough + '" color="' + data.color + '" align="' + data.align + '" value="' + data.text + '" datats="' + data.type + '" required="' + data.required + '" readOnly="' + data.readOnly + '"  background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '">');
    }
  } else if (data.type === 'checkbox') {

    var testinf = data.tempArray;
    if (data.tempArray != '') {
      var new_array = data.tempArray;
    } else {
      var new_array = '';
    }

    var readOnly = '';
    if (data.readOnly != '') {
      readOnly = 'readOnly="' + data.readOnly + '"';
    }
    $(".signer-assembler").append('<action name="' + data.name + '" id="' + data.id + '" type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.InputId + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" value="' + data.value + '" datats="' + data.type + '" required="' + data.required + '" ' + readOnly + ' background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '"  datas_keys="' + data.datas_keys + '" groups_checkbox="' + data.groups_checkbox + '">');
  }
  else if (data.type === 'radio') {

    var testinf = data.tempArray;
    if (data.tempArray != '') {
      var new_array = data.tempArray;
    } else {
      var new_array = '';
    }

    var readOnly = '';
    if (data.readOnly != '') {
      readOnly = 'readOnly="' + data.readOnly + '"';
    }
    $(".signer-assembler").append('<action name="' + data.name + '" id="' + data.id + '" type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.InputId + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" value="' + data.value + '" datats="' + data.type + '" required="' + data.required + '" ' + readOnly + ' background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '" datas_keys="' + data.datas_keys + '" groups_checkbox="' + data.groups_checkbox + '">');
  }
  else if (data.type === 'fields') {

    var testinf = data.tempArray;
    if (data.tempArray != '') {
      var new_array = data.tempArray;
    } else {
      var new_array = '';
    }


    $(".signer-assembler").append('<action id="' + data.id + '" type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" value="' + new_array + '" datats="' + data.type + '" required="' + data.required + '" readOnly="' + data.readOnly + '">');
  } else if (data.type === 'dropdown') {

    var drops = { "id": data.id, "temp": data.tempArray };

    var element = Object.assign({}, drops);
    dropArray.push(element);
    var dropsVal = { "id": data.id, "temps": data.addmoreArray };

    var elements = Object.assign({}, dropsVal);
    dropdownResponse.push(elements);
    $(".signer-assembler").append('<action id="' + data.id + '" type="' + data.type + '" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" datats="' + data.type + '" required="' + data.required + '" readOnly="' + data.readOnly + '" background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '" background_color="' + data.background_color + '"  signer_id="' + data.signer_id + '">');
  }else if (data.type === "look") {

   
  }
  if (prepare) {
    prepareData();
  }
}

/*
 *  Signer Save click
 */
$(".signer-save").click(function (event) {

  event.preventDefault();
  if (inviting) {
    sendRequest();
  } else if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {

    orgnizeData();
  } else if ($('.signer-assembler action').length) {

    prepareData();
  } else {
    toastr.warning("No changes to save.", "Hmm!");
    return false;
  }
});



/*
 *  On font/stroke size change
 */
$(".font-size").change(function () {
  size = parseInt($(this).val());
  if (isDrawMode()) {
    modules.stroke(size);
  } else {
    updateTextSize(size);
  }
});

/*
 *  Font preview on mouseover
 */
$(".font-item").mouseover(function () {
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  } else {
    elem = $(".signer-element[type=text]");
  }
  elem.find(".writing-pad").css("font-family", $(this).attr("family"));
});

/*
 *  Exit font preview
 */
$(".font-item").mouseleave(function () {
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  } else {
    elem = $(".signer-element[type=text]");
  }
  elem.each(function () {
    if (elem.attr("font") === undefined) {
      elem.find(".writing-pad").css("font-family", "'Lato', sans-serif");
    } else {
      elem.find(".writing-pad").css("font-family", $(".font-item[font=" + elem.attr("font") + "]").attr("family"));
    }
  })
});

/*
 *  Update font of text
 */
$(".font-item").click(function () {
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  } else {
    elem = $(".signer-element[type=text]");
  }
  elem.attr("font", $(this).attr("font"));
  elem.find(".writing-pad").css("font-family", $(this).attr("family"));
  highlightSelectedFont($(this).attr("font"));
});

/*
 *  select an element
 */
$(".signer-builder").on("click", ".signer-element", function () {
  deselectElements();
  $(this).addClass("selected-element");
  if ($(this).attr("type") === "text") {
    $(this).find(".writing-pad").focus();
    if ($(this).attr("group") !== "field") {
      showActiveTextTools();
      //updateColorPicker($(this).attr("color"));
      //updateSelectedFontSize($(this).attr("font-size"));
      //highlightSelectedFont($(this).attr("font"));
    }
    //do code here
  } else if ($(this).attr("type") === "signature") {
    if ($(this).attr("group") === "field") {
      if (!auth) {
        if (sessionStorage.getItem('signature') === null) {
          $("#updateSignature").modal({ show: true, backdrop: 'static', keyboard: false });
        } else {
          $(this).attr("signed", "true")
          $(this).find("img").attr("src", sessionStorage.getItem('signature'));
        }
      } else if (signature !== '') {
        $(this).attr("signed", "true")
        $(this).find("img").attr("src", signature);
      } else {
        notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('" + settingsPage + "')" });
      }
    }
  }
  if ($(this).attr("group") === "field") {
    disableTools();
  }
});

/*
 *  Organize data in action format before it's prepared
 */
function orgnizeData(prepare) {

  stopOrganizing = false;

  if (prepare === undefined) { prepare = true; }
  if (modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
    assemble({ "type": "drawing", "page": pageNum, "drawing": $('#document-viewer').getCanvasImage("image/png") }, false);
  }
  $('.signer-builder .signer-element').each(function (index, value) {

    var signerElement = $(this), actionType = signerElement.attr('type'), thisImage;
   
    signerElement.show();
    viewerPosition = $("#document-viewer").offset();

    group = signerElement.attr('group');

    pageNumber = parseInt(signerElement.attr('page'));

    if (actionType === "image" || actionType === "signature" || actionType === "symbol" || actionType === "shape" || actionType === "stamp") {
        
      if (group === "field") {
        if (signerElement.attr("signed") === "false") {
          emptyAssembler();
          renderPage(pageNumber);
          signerElement.addClass("selected-element");
          notify("Hmm!", "A signature is required on page " + pageNumber + ". Please sign to continue.", "info", "Sign Now");
          stopOrganizing = true;
          return false
        }
      }

      if (actionType === "symbol" || actionType === "shape") {
        signerElement.find("div").remove();
        thisImage = signerEscape(signerElement.html());
        elementWidth = signerElement.find("svg").width();
        elementHeight = signerElement.find("svg").height();
        elementPosition = signerElement.find("svg").offset();
      } else {
        thisImage = signerElement.find("img").attr('src');
        elementWidth = signerElement.find("img").width();
        elementHeight = signerElement.find("img").height();
        elementPosition = signerElement.find("img").offset();
        textHolder = signerElement.find(".img_wrap");
      }
      elementXpos = elementPosition.left - viewerPosition.left;
      elementYpos = elementPosition.top - viewerPosition.top;

      assemble({ "id": signerElement.attr('id'), "group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "image": thisImage, "tempArray": sessionArrays, "background_color": $(textHolder[0]).attr("background_color"), "signer_id": $(textHolder[0]).attr("signer_id") }, false);

    }else if (actionType === "text") {
    
      underline = italic = bold = strikethrough = align = fontfamily = '';
      fontsize = 14;
      font = "lato";
      textHolder = signerElement.find(".writing-pad1");

      if (textHolder[0]?.required == true) {
        elementWidth = parseFloat(textHolder.width()) - 2 + 6;
        elementHeight = parseFloat(textHolder.height()) - 2 + 6;
      } else {
        elementWidth = parseFloat(textHolder.width()) + 6;
        elementHeight = parseFloat(textHolder.height()) + 6;
      }

      var elementPosition = textHolder.offset();

      elementXpos = elementPosition.left - viewerPosition.left;
      elementYpos = elementPosition.top - viewerPosition.top;
      if (group === "field") {
        userInput = textHolder.text();
        if (!userInput.replace(/\s/g, '').length) {
          emptyAssembler();
          renderPage(pageNumber);
          signerElement.addClass("selected-element");
          notify("Hmm!", "An input on page " + pageNumber + " is empty. Please fill to continue.", "info", "Fill Now");
          stopOrganizing = true;
          return false
        }
      }

      color = "";
      if (group === "input") {
        if (signerElement.attr("bold") === "true") { bold = "bold"; }
        if (signerElement.attr("italic") === "true") { italic = "italic"; }
        if (signerElement.attr("strikethrough") === "true") { strikethrough = "strikethrough"; }
        if (signerElement.attr("underline") === "true") { underline = "underline"; }
        if (signerElement.attr("color") !== undefined) { color = signerElement.attr("color"); }
        if (signerElement.attr("font-size") !== undefined) { fontsize = signerElement.attr("font-size"); }
        if (signerElement.attr("align") !== undefined) { align = signerElement.attr("align"); }
        if (signerElement.attr("font") !== undefined) { font = signerElement.attr("font"); }
        if (signerElement.attr("font") !== undefined) { fontfamily = signerElement.find(".writing-pad").css("font-family"); }
        textBody = textHolder.html();

        if(textBody !=null){
            textBody = textHolder[0].value;
        }

      } else {

        if (signerElement.attr("bold") === "true") { bold = "bold"; }
        if (signerElement.attr("italic") === "true") { italic = "italic"; }
        if (signerElement.attr("strikethrough") === "true") { strikethrough = "strikethrough"; }
        if (signerElement.attr("underline") === "true") { underline = "underline"; }
        if (signerElement.attr("color") !== undefined) { color = signerElement.attr("color"); }
        if (signerElement.attr("font-size") !== undefined) { fontsize = signerElement.attr("font-size"); }
        if (signerElement.attr("align") !== undefined) { align = signerElement.attr("align"); }
        if (signerElement.attr("font") !== undefined) { font = signerElement.attr("font"); }
        if (signerElement.attr("font") !== undefined) { fontfamily = signerElement.find(".writing-pad").css("font-family"); }
        // textBody = '<div style="'+align+strikethrough+underline+color+'">'+textHolder[0].value+'</div>';
        
        textBody = textHolder[0].value;
     
      }
      palceHolder = textHolder[0].placeholder;
      disabled = '';
      if (textHolder[0].disabled === true) {
        disabled = 'disabled';
      }
      texts = signerEscape(textBody);

      assemble({ "id": signerElement.attr('id'),'disabled': disabled, "datats": textHolder[0].title, "required": textHolder[0].required, "readOnly": textHolder[0].readOnly, "group": group, "palceHolder": palceHolder, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": texts, "align": align, "bold": bold, "italic": italic, "font": font, "fontsize": fontsize, "fontfamily": fontfamily, "underline": underline, "color": color, "strikethrough": strikethrough, "tempArray": sessionArrays, "background_color": $(textHolder[0]).attr("background_color"), "signer_id": $(textHolder[0]).attr("signer_id") }, false);
    } else if (actionType === "checkbox") {
      var checkHolder = signerElement.find(".checkbox_wrapper").offset();
      elementWidth = signerElement.find(".checkbox_wrapper").width();
      elementHeight = signerElement.find(".checkbox_wrapper").height();

      elementXpos = checkHolder.left - viewerPosition.left;
      elementYpos = checkHolder.top - viewerPosition.top;
      var eid = signerElement.find('.checkbox_wrapper');

      if (eid[0].checked == true) {
        checkedval = 1;
      } else {
        checkedval = 0;
      }

      assemble({ "id": signerElement.attr('id'), "name": eid[0].name, "required": eid[0]?.required, "readOnly": eid[0]?.readOnly, "group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType, "value": checkedval, "InputId": eid[0].id, "background_color": $(eid[0]).attr("background_color"), "signer_id": $(eid[0]).attr("signer_id"), 'datas_keys': $(eid[0]).attr("data-keys"), "group": $(eid[0]).attr("group") }, false);

    } else if (actionType === "radio") {
      var checkHolder = signerElement.find(".radio_wrap").offset();

      elementWidth = signerElement.find(".radio_wrap").width();
      elementHeight = signerElement.find(".radio_wrap").height();

      elementXpos = checkHolder.left - viewerPosition.left;
      elementYpos = checkHolder.top - viewerPosition.top;

      var eid = signerElement.find('.radio_wrap');

      if (eid[0].checked == true) {
        checkedRadioval = 1;
      } else {
        checkedRadioval = 0;
      }


      assemble({ "id": signerElement.attr('id'), "required": eid[0].required, "readOnly": eid[0].readOnly, "InputId": eid[0].id, "group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType, "value": eid[0].value, 'name': eid[0].name, "background_color": $(eid[0]).attr("background_color"), "signer_id": $(eid[0]).attr("signer_id"), 'datas_keys': $(eid[0]).attr("data-keys"), "group": $(eid[0]).attr("group") }, false);

    } else if (actionType === "fields") {
      textHolders = signerElement.find(".writing-pad");
      var checkHolder = signerElement.find(".writing-pad").offset();

      elementWidth = signerElement.find(".writing-pad").width();
      elementHeight = signerElement.find(".writing-pad").height();

      elementXpos = checkHolder.left - viewerPosition.left;
      elementYpos = checkHolder.top - viewerPosition.top;

      var sessionArrays;

      assemble({ "id": signerElement.attr('id'), "required": textHolders[0].required, "readOnly": textHolders[0].readOnly, "group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType, "tempArray": sessionArrays }, false);

    } else if (actionType === "dropdown") {
      textHolders = signerElement.find(".drips");
      var checkHolder = signerElement.find(".drips").offset();

      elementWidth = signerElement.find(".drips").width();
      elementHeight = signerElement.find(".drips").height();

      elementXpos = checkHolder.left - viewerPosition.left;
      elementYpos = checkHolder.top - viewerPosition.top;

      var sessionArrays;
      assemble({ "id": signerElement.attr('id'), "required": textHolders[0].required, "readOnly": textHolders[0].readOnly, "group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType, "tempArray": textHolders[0].innerHTML, "background_color": $(textHolders[0]).attr("background_color"), "signer_id": $(textHolders[0]).attr("signer_id"), 'addmoreArray': final_array }, false);
    } else if (actionType === "look") {
        
      
    }

    

    if (pageNumber == pageNum) {
      signerElement.show();
    } else {
      signerElement.hide();
    }
  });
  if (stopOrganizing) {
    emptyAssembler();
    return false;
  } else {

    if (prepare) { prepareData(); }
  }

}

/*
 *  Prepare data before sending to database
 */
function prepareData(save) {
    
  if (save === undefined) { save = true; }
    var actions = [];
   
    $('.signer-assembler action').each(function (index, value) {
      const result = globalNewResponse.find(item => item.id === $(this).attr('id'));
      const resultImages = globalNewResponseImages.length > 0;
      if(typeof result != 'undefined'){
          if(typeof $(this).attr('xPos') != 'undefined'){
              result.xPos = $(this).attr('xPos');
          }
          if(typeof $(this).attr('yPos') != 'undefined'){
              result.yPos = $(this).attr('yPos');
          }
          
          if($(this).attr('placeholder') == "Date Signed"){
            result.width = $(this).attr('width');
            result.height = $(this).attr('height');
          }
          if(resultImages){
            if($(this).attr('type') == "stamp"){
            
              result.width = $(this).attr('width');
              result.height = $(this).attr('height');
              
            }
          }
          
        
          $.each(globalNewResponse,function(index,value){
              if(value.id == $(this).attr('id')){
                  value = result;
              }
          })
      }
    });
    actions = JSON.stringify(globalNewResponse);

    if (save) { saveChanges(actions); } else { return actions; }
}

/*
 *  send actions to server
 */
function saveChanges(actions) {

  saveAllData(actions)
}

/*
 *  escape string
 */
function signerEscape(string) {

  string = string.replace(/"/g, "%22");

  return string;
}

/*
 *  empty assembled data.
 */
function emptyAssembler() {
  $(".signer-assembler").empty();
}

/*
 *  empty builder data.
 */
function emptyBuilder() {
  $(".signer-builder").empty();
}

/*
 *  Duplicate selected element
 */
function duplicateSelected() {
  original = $('.signer-element.selected-element');
  duplicate = original.clone();
  duplicate.appendTo(".signer-builder");
  original.removeClass("selected-element")
  position = duplicate.position();
  if ($(window).width() < 1101) {
    topOffset = 225;
  } else {
    topOffset = 185;
  }
  currentOffset = $(".signer-overlay-previewer").offset();
  yPos = parseInt(position.top - currentOffset.top + topOffset);
  duplicate.css({ top: parseInt(yPos + 30) + 'px', left: parseInt(position.left + 30) + 'px' });
  initElementsDrag();
  focusText();
}

/*
 *  Select image to add on PDF
 */
function selectDocImage() {
  showLoader()
  var reader = new FileReader();
  reader.readAsDataURL(document.querySelector('input[name=document-selected-image]').files[0]);
  imageWidth = parseInt($("#document-viewer").width() - 30)
  reader.addEventListener("load", function () {
    $("#selectImage").modal("hide");
    hideLoader();
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" page="' + pageNum + '"><img src="' + reader.result + '" style="max-width:' + imageWidth + 'px;opacity:0.5;"></div>').appendTo(".signer-builder");
    $(document).mousemove(function (event) {
      $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
    });
    disableTools();
    highlightCanvas();
  }, false);
}

/*
 *  Enable signature mode
 */
function enableSignatureMode() {
  if (!auth) {
    if (sessionStorage.getItem('signature') === null) {
      $("#updateSignature").modal({ show: true, backdrop: 'static', keyboard: false });
    } else {
      imageWidth = parseInt($("#document-viewer").width() - 30);
      $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="' + pageNum + '"><img src="' + sessionStorage.getItem('signature') + '" style="max-width:' + imageWidth + 'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
      $(document).mousemove(function (event) {
        $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
      });
      disableTools();
      highlightCanvas();
    }
  } else if (signature !== '') {
    imageWidth = parseInt($("#document-viewer").width() - 30);
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="' + pageNum + '"><img src="' + signature + '" style="max-width:' + imageWidth + 'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
    $(document).mousemove(function (event) {
      $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
    });
    disableTools();
    highlightCanvas();
  } else {
    notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('" + settingsPage + "')" });
  }
}

/*
 *  When symbol is selected
 */
$(".symbol-item").click(function () {
  deselectElements();
  $(".right-bar.symbol-list").toggleClass("open");
  $('<div class="signer-element selected-element" status="drop" resizeable="true" color="' + selectedColor() + '" type="symbol" page="' + pageNum + '" style="width:40px;height:40px;">' + $(this).html() + '</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
  $(document).mousemove(function (event) {
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is selected
 */
$(".field-list").on("click", ".field-item div", function (event) {
  event.preventDefault();
  deselectElements();
  $(".right-bar.fields-list").toggleClass("open");
  font = selectedFont();
  $('<div class="signer-element selected-element" status="drop" type="text" page="' + pageNum + '" ' + currentTextStyle() + ' font="' + font.font + '" color="' + selectedColor() + '" font-size="' + selectedFontSize() + '" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:' + selectedColor() + ';font-size:' + selectedFontSize() + 'px;font-family:' + font.family + '"  spellcheck="false">' + $(this).text() + '</div></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is selected
 */
$(".input-field-list").on("click", ".input-field-item div", function (event) {

  event.preventDefault();
  deselectElements();
  $(".right-bar.input-fields-list").toggleClass("open");
  font = selectedFont();
  fieldLabel = $(this).text();
  if (fieldLabel == "Signature") {
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" group="input" page="' + pageNum + '"><img src="' + baseUrl + '/assets/images/signhere.png" style="width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
  } else {
    $('<div class="signer-element selected-element" status="drop" type="text" resizeable="free" group="input" page="' + pageNum + '" ' + currentTextStyle() + ' font="' + font.font + '" color="' + selectedColor() + '" font-size="' + selectedFontSize() + '" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:' + selectedColor() + ';font-size:' + selectedFontSize() + 'px;font-family:' + font.family + '"  spellcheck="false">' + $(this).text() + '</div></div>').appendTo(".signer-builder");
  }
  $(document).mousemove(function (event) {
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is deleted
 */
$(".field-list").on("click", "#delete-field", function (event) {
  event.preventDefault();
  fieldId = $(this).closest(".field-item").attr("id");
  $(this).closest(".field-item").remove();
  deleteField(fieldId);
});

/*
 *  When input field is deleted
 */
$(".input-field-list").on("click", "#delete-input-field", function (event) {
  event.preventDefault();
  fieldId = $(this).closest(".input-field-item").attr("id");
  $(this).closest(".input-field-item").remove();
  deleteField(fieldId);
});

/*
 *  Delete custom or input field
 */
function deleteField(fieldId) {
  server({
    url: deleteFieldsUrl,
    data: {
      "fieldId": fieldId,
      "csrf-token": Cookies.get("CSRF-TOKEN")
    },
    loader: false
  });
}

/*
 *  When shape is selected
 */
$(".shape-item").click(function () {
  deselectElements();
  $(".right-bar.shape-list").toggleClass("open");
  $('<div class="signer-element selected-element" status="drop" resizeable="true" color="' + selectedColor() + '" type="shape" page="' + pageNum + '" style="width:100px;height100px;">' + $(this).html() + '</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
  $(document).mousemove(function (event) {
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
});
var sign;
$('.signstatus').click(function () {
  sign = elemtcount++;
  deselectElements();
  $('.signstatus').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }

  $('.deselected').val('signstatus');
  $('<div class="signer-element selected-element tttts" resizeable="true" status="drop" type="image" id="sign_' + sign + '" page="' + pageNum + '"><img src="' + urls + '/assets/images/new_favicon_01.png" class="img_wrap" id="signatures_signer_' + sign + '"></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('signature');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})
var totalInilia;
$('.initial').click(function () {

  totalInilia = elemtcount++;
  deselectElements();

  $('.initial').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }

  $('.deselected').val('initial');
  $('<div class="signer-element " type="image" resizeable="true" status="drop" page="' + pageNum + '" id="initial_' + totalInilia + '"><img src="' + urls + '/assets/images/new_favicon_01.png" class="img_wrap" id="initial_signer_' + totalInilia + '"></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('initial');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})

var totalDateSign;
$('.datesigned').click(function () {

  totalDateSign = elemtcount++;
  deselectElements();

  $('.totalDateSign').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }

  $('.deselected').val('datesigned');
  $('<div class="signer-element " type="text" status="drop" page="' + pageNum + '" id="datesigned_' + totalDateSign + '"><textarea disabled  style="width:200px;height:30px;" type="text" placeHolder="Date Signed" id="datesigneds_' + totalDateSign + '"  value=""  class="writing-pad1 signeeddate"  title=""></textarea></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('datesigned');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})



var totalCompany;
$('.company').click(function () {

  totalCompany = elemtcount++;
  deselectElements();
  $('.company').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('company');
  $('<div class="signer-element" resizeable="textbox" type="text" page="' + pageNum + '" status="drop" id="company_' + totalCompany + '" ><input type="text"  class="writing-pad" id="company' + totalCompany + '" placeHolder="company"></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('company');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})

var totalText;
$('.text').click(function () {

  totalText = elemtcount++;

  deselectElements();
  $('.text').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('text');

  $('<div class="signer-element"  status="drop" page="' + pageNum + '" id="text_' + totalText + '" type="text"><textarea class="writing-pad1" style="width:200px;height:30px;" placeHolder="Textbox" id="checks' + totalText + '" type="text"></textarea></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('text');

    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})
var checkTotal;
var chkcount = 1;
$('.checkboxs').click(function () {

  checkTotal = elemtcount++;;
  var checkTes = "'cbox" + checkTotal + "'";
  deselectElements();
  $('.checkboxs').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('checkboxs');
  $('<div class="signer-element" type="checkbox" page="' + pageNum + '" status="drop" id="checkboxs_' + checkTotal + '"><input type="checkbox" class="checkbox_wrapper " name="cbox' + checkTotal + '" id="cid_' + checkTotal + chkcount + '" value="' + chkcount + '" group="multiplecheck' + checkTotal + '" data-keys="addmore"><a href="javascript:void(0)" onclick="getCheckAppend(' + checkTotal + ',' + chkcount + ',' + checkTes + ')"><i class="fa fa-plus"></i></a></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('checkboxs');
    $('.next').val(chkcount);
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})
var drops;
$('.dropdowsns').click(function () {

  drops = elemtcount++;

  deselectElements();
  $('.dropdowsns').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('dropdowsns');
  $('<div class="signer-element ui-resizable" resizable="textbox" type="dropdown" page="' + pageNum + '" status="drop" id="dropdowsns_' + drops + '"><select id="dropid' + drops + '" class="drips"><option>select</option></select></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('dropdowsns');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})
var radios;
var rcount = 1;
$('.radios').click(function () {

  var rcounts = $('.next').val();


  radios = elemtcount++;

  $('#totalRadio').val(radios);
  deselectElements();
  $('.prev').val('radios');
  $('.radios').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('radios');

  var tennn = "'radiogroup" + radios + "'";

  $('<div class="signer-element selected-element" type="radio" page="' + pageNum + '" status="drop" id="radios_' + radios + '"><input type="radio" style="color:red;" name="radiogroup' + radios + '" class="radio_wrap" id="radio_wrap_' + radios + "" + rcount + '" value="' + rcount + '" group="multipleradio' + radios + '" data-keys="addmore"><br><div class=""><a href="javascript:void(0)" onclick="getAppend(' + radios + ',' + rcount + ',' + tennn + ')"><i class="fa fa-plus"></i></a></div></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.next').val(rcount);
    $('.prev').val('radios');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();

})


var fields_caregiver;
$('.fields_caregiver').click(function () {

  fields_caregiver = elemtcount++;

  deselectElements();
  $('.fields_caregiver').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('fields');

  $('<div class="signer-element" status="drop" page="' + pageNum + '" id="caregiver_' + fields_caregiver + '" type="text"><textarea disabled  style="width:200px;height:30px;" type="text" placeHolder="Caregiver Lookup field" id="caregivers_' + fields_caregiver + '"  value=""  class="writing-pad1" datakey="caregiver" title=""></textarea></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('fields');

    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})

var totalPatient;
$('.fields_patient').click(function () {

  totalPatient = elemtcount++;

  deselectElements();
  $('.fields_patient').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('fields_patient');
  $('<div class="signer-element" status="drop"  type="text" id="patient_' + totalPatient + '" page="' + pageNum + '"><textarea disabled  style="width:200px;height:30px;" type="text" placeHolder="Patients Lookup field" id="patients' + totalPatient + '"  value=""  class="writing-pad1" datakey="patient" title=""></textarea></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('fields_patient');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})

var totalIntake;
$('.fields_intake').click(function () {

  totalIntake = elemtcount++;


  deselectElements();

  $('.email').addClass('active');
  var check = $('.deselected').val();
  if (check != '') {
    $('.' + check).removeClass('active');
  }
  $('.deselected').val('fields_intake');

  $('<div class="signer-element" page="' + pageNum + '" type="text" status="drop" id="intake_' + totalIntake + '"><textarea disabled  style="width:200px;height:30px;" type="text" placeHolder="Enrollment" id="intakes_' + totalIntake + '"  value=""  class="writing-pad1" datakey="intake" title=""></textarea></div>').appendTo(".signer-builder");
  $(document).mousemove(function (event) {
    $('#tempids').val(1);
    $('.prev').val('fields_intake');
    $(".signer-element[status=drop]").css({ left: event.pageX + 1, top: event.pageY + 1 });
  });
  disableTools();
  highlightCanvas();
})

/*
 *  Make elements draggable
 */

function initElementsDrag(type="") {

  if(type =='stamp'){
    $(".all_status_id").draggable({
      containment: $("#document-viewer"),
      drag: function () {
        highlightCanvas();
      },
      stop: function () {
        unHighlightCanvas();
      },
  
    });
  }
  
}


/*
 *  Make elements resizeable
 */
function initElementsResize() {
  $(".signer-element[resizeable=true]").resizable({
    aspectRatio: true,
    autoHide: false,
    handles: "n, e, s, w, se, sw, nw, ne",
    resize: function (event, ui) {
      ui.helper.find("img").width(ui.size.width - 10);
      ui.helper.find("img").height(ui.size.height - 12);
    }
  });
  $(".signer-element[resizeable=free]").resizable({
    autoHide: false,
    handles: "n, e, s, w, se, sw, nw, ne",
    resize: function (event, ui) {
      ui.helper.find(".writing-pad").width(ui.size.width - 10);
      ui.helper.find(".writing-pad").height(ui.size.height - 12);
    }
  });

  $(".signer-element[resizeable=textbox]").resizable({
    aspectRatio: true,
    autoHide: false,
    handles: "n, e, s, w, se, sw, nw, ne",
    resize: function (event, ui) {
      ui.helper.find(".writing-pad").width(ui.size.width - 10);
      ui.helper.find(".writing-pad").height(ui.size.height - 12);
    }
  });
  getResponse();
}

var data = [];
var vishaldata = [];
var count = 1;
var elemtcount = 1 + counter;

function getResponse() {
  var testingArray = [];
  var tempi = $('#tempids').val();
  var texts = $('.prev').val();

  var ResponseList;
  viewerPositions = $("#document-viewer").offset();
  $('#vishal123').empty();

  var html = "";
  var deleteid;
  if (texts == 'signature') {
    var signname = '"signature"';
    var localid = 'sign_' + sign;
    html = "<div class='row' id='" + sign + "'><div class='box box-solid'><div class='box-body'><div id='" + sign + "'><div class='form-group'><label>Signature</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"signatures_signer_" + sign + "\"," + signname + "," + sign + ");' id='signer_signatures" + sign + "' class='form-control'></select></div></div><hr><div class='col-md-12'><div class='form-group'><label><input type='checkbox' class=''  name='signature" + sign + "' id='check_sign_" + sign + "' onclick='getGenerateArray(" + signname + "," + sign + ");gerRequired(this.value," + sign + "," + signname + ")' value='1' ></label>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' class='minimal'  name='signature_read" + sign + "' id='check_read_" + sign + "' onclick='getGenerateArray(" + signname + "," + sign + ");gerRequired(" + signname + "," + sign + ")' value='1' >Read Only</div></div></div></div>";
    ActionType = $('#' + localid).attr('type');
    page = $('#' + localid).attr('page');
    elementPosition = $('#' + localid).find("img").offset();
    elementWidth = $('#' + localid).find("img").width();
    elementHeight = $('#' + localid).find("img").height();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    deleteid = $('#' + localid).attr('id');
    deleteids = sign;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
  }

  if (texts == 'initial') {

    var signname = '"initial"';
    var localid = 'initial_' + totalInilia;
    html = "<div class='row' id='" + totalInilia + "'><div class='box box-solid'><div class='box-body'><div id='" + totalInilia + "'><div class='form-group'><span class='tool-icon tool-user'></span> <label>Initial</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"initial_signer_" + totalInilia + "\"," + signname + "," + totalInilia + ");' id='signer_initail" + totalInilia + "' class='form-control'></select></div></div><div class='form-group'><label><input type='checkbox' class='minimal' name='intialize" + totalInilia + "' id='check_intial_" + totalInilia + "' onclick='getGenerateArray(" + signname + "," + totalInilia + ")' value='1'></label>&nbsp;&nbsp;<span>Required Fields</span></div></div>";
    ActionType = $('#' + localid).attr('type');
    page = $('#' + localid).attr('page');
    elementPosition = $('#' + localid).find("img").offset();
    elementWidth = $('#' + localid).find("img").width();
    elementHeight = $('#' + localid).find("img").height();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    deleteid = $('#' + localid).attr('id');
    deleteids = totalInilia;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';

  }

  if (texts == 'company') {
    var signname = '"company"';
    var localid = 'company_' + totalCompany;
    html = "<div class='row' id='" + totalCompany + "'><div class='box box-solid'><div class='box-body'><div id='" + totalCompany + "'><div class=''><i class='fa fa-building' aria-hidden='true'></i> <label>Company</label></div><hr><div class=''><div class='col-md-12'><div class='form-group'><label><input type='checkbox' class='' id='company_required_" + totalCompany + "' name='company' value='1' onchange='getGenerateArray(" + signname + "," + totalCompany + ")' onclick='gerRequired(this.value," + totalCompany + "," + signname + ")'></label>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' name='' class='minimal' id='company_read_" + totalCompany + "' value='1'  onchange='getGenerateArray(" + signname + "," + totalCompany + ")' onclick='gerReadOnly(this.value," + totalCompany + "," + signname + ")'>Read Only</div></div></div></div>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad");

    elementWidth = $('#' + localid).find(".writing-pad").width();
    elementHeight = $('#' + localid).find(".writing-pad").height();
    elementPosition = $('#' + localid).find(".writing-pad").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = totalCompany;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';

  }

  if (texts == 'text') {
    var signname = '"text"';
    var localid = 'text_' + totalText;
    html = "<div class='row' id='" + totalText + "'><div class='box box-solid'><div class='box-body'><div id='" + totalText + "'><div class='form-group'><i class='fa fa-text-width' aria-hidden='true'></i>&nbsp;&nbsp;<label>Text</label></div><hr><div class='col-md-12'><div class='form-group'><select name='changesColor' onchange='setSigner(this.id,\"checks" + totalText + "\"," + signname + "," + totalText + ");' id='textss" + totalText + "' class='form-control'></select></div></div><div class='col-md-12'><div class='row'><div class='form-group'><label><input type='checkbox' name='text_required' id='text_required_" + totalText + "' onchange='getGenerateArray(" + signname + "," + totalText + ")' onclick='gerRequired(this.value," + totalText + "," + signname + ")' ></label>Required Field</div></div><div class='row'><div class='form-group'><label><input type='checkbox' name='text_read' id='text_read_" + totalText + "' onchange='getGenerateArray(" + signname + "," + totalText + ")'  onclick='gerReadOnly(this.value," + totalText + "," + signname + ")' value='1'></label>Read Only</div></div><hr></div><div class='form-group'><label>Add Text</label><div class=''><textarea class='textare" + totalText + "'onkeyup='getKeys(" + totalText + ")' onchange='getGenerateArray(" + signname + "," + totalText + ")'></textarea></div></div></div>";

    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad1");

    elementWidth = $('#' + localid).find(".writing-pad1").width();
    elementHeight = $('#' + localid).find(".writing-pad1").height();
    elementPosition = $('#' + localid).find(".writing-pad1").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = totalCompany;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
  }

  if (texts == 'checkboxs') {
    var signname = '"checkbox"';
    var localid = 'checkboxs_' + checkTotal;
    html = "<div class='row' id='" + checkTotal + "'><div class='box box-solid'><div class='box-body'><div id='" + checkTotal + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Checkbox Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"checkbox_signer_" + checkTotal + "\"," + signname + "," + checkTotal + ");' id='signer_checkbox" + checkTotal + "' class='form-control'></select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label><input type='checkbox' name='checkbox_required' id='checkbox_required_" + checkTotal + "' onchange='getGenerateArray(" + signname + "," + checkTotal + ")' onclick='gerRequired(this.value," + checkTotal + "," + signname + ")'></label>Required Field</div></div><div class='row'><div class='form-group'><label><input type='checkbox' name='checkbox_read' id='checkbox_read_" + checkTotal + "' onchange='getGenerateArray(" + signname + "," + checkTotal + ")'></label>Read Only</div></div><hr></div></div>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".checkbox_wrapper");

    elementWidth = $('#' + localid).find(".checkbox_wrapper").width();
    elementHeight = $('#' + localid).find(".checkbox_wrapper").height();
    elementPosition = $('#' + localid).find(".checkbox_wrapper").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = checkTotal;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
  }
  if (texts == 'dropdowsns') {
    var signname = '"dropdown"';
    var localid = 'dropdowsns_' + drops;
    var tempname = '"addmore"';
    html = "<div class='row' id='" + drops + "'><div class='box box-solid'><div class='box-body'><div id='" + drops + "'><div class='form-group'><i class='fa fa-caret-down' aria-hidden='true'></i>&nbsp;&nbsp;<label>Dropdown</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"dropid" + drops + "\"," + signname + "," + drops + ");' id='signer_dropdown" + drops + "' class='form-control'></select></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label><input type='checkbox' name='DropRequired' id='drop_required_" + drops + "' onchange='getGenerateArray(" + signname + "," + drops + ")' onclick='gerRequired(this.value," + drops + "," + signname + ")' value='1'></label>Required Field</div></div><div class='row'><div class='form-group'><label><input type='checkbox' name='DropRead_" + drops + "' id='drops_read_" + drops + "' onchange='getGenerateArray(" + signname + "," + drops + ")' onclick='gerReadOnly(this.value," + drops + "," + signname + ")'></label>Read Only</div></div></div><hr></div><div class='col-md-12'><div class='row'><span>Fill in the list of options.</span><div id='multid" + drops + "'></div><a onclick='addmore(" + signname + "," + drops + "," + tempname + ")'><i class='fa fa-plus'></i>Add Option</a></div><div class='row'><label>Default Option</label><select class='drops_" + drops + "' onchange='selectValue(" + drops + ",this.value)'><option>Select</option></select></div></div></div></div>";

    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".drips");

    elementWidth = $('#' + localid).find(".drips").width();
    elementHeight = $('#' + localid).find(".drips").height();
    elementPosition = $('#' + localid).find(".drips").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = drops;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';

  }
  if (texts == 'fields') {
    var signname = '"fields"';
    var localid = 'caregiver_' + fields_caregiver;
    html = "<div class='row' id='" + fields_caregiver + "'><div class='box box-solid'><div class='box-body'><div id='" + fields_caregiver + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Caregiver Look Up fields</label></div><hr><div class=''><div class='form-group'><label>Dropdown</label><select class='caregiverId" + fields_caregiver + " form-control' onchange='caregiverWiseChange(this.value," + fields_caregiver + ")'></select></div></div></div></div><script>getAjax('" + fields_caregiver + "')</script>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad1");

    elementWidth = $('#' + localid).find(".writing-pad1").width();
    elementHeight = $('#' + localid).find(".writing-pad1").height();
    elementPosition = $('#' + localid).find(".writing-pad1").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = fields_caregiver;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';

  }

  /* Patient Lookup fields */
  if (texts == 'fields_patient') {

    var signname = '"patient"';
    var localid = 'patient_' + totalPatient;
    html = "<div class='row' id='" + totalPatient + "'><div class='box box-solid'><div class='box-body'><div id='" + totalPatient + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Patient Look Up fields</label></div><hr><div class=''><div class='form-group'><label>Dropdown</label><select class='patientId" + totalPatient + " form-control' onchange='patientWiseChange(this.value," + totalPatient + ")'></select></div></div></div></div><script>getPatientAjax('" + totalPatient + "')</script>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad1");

    elementWidth = $('#' + localid).find(".writing-pad1").width();
    elementHeight = $('#' + localid).find(".writing-pad1").height();
    elementPosition = $('#' + localid).find(".writing-pad1").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = totalPatient;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
    /**end caregiver AJax**/
  }

  /* Intake Lookup fields */
  if (texts == 'fields_intake') {

    var signname = '"intake"';
    var localid = 'intake_' + totalIntake;
    html = "<div class='row' id='" + totalIntake + "'><div class='box box-solid'><div class='box-body'><div id='" + totalIntake + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Enrollment Look Up fields</label></div><hr><div class=''><div class='form-group'><label>Dropdown</label><select class='intakeId" + totalIntake + " form-control' onchange='intakeWiseChange(this.value," + totalIntake + ")'></select></div></div></div></div><script>getIntakeAjax('" + totalIntake + "',this.value)</script>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad1");

    elementWidth = $('#' + localid).find(".writing-pad1").width();
    elementHeight = $('#' + localid).find(".writing-pad1").height();
    elementPosition = $('#' + localid).find(".writing-pad1").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = totalIntake;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
    /**end caregiver AJax**/
  }
  /* End Patient Lookup fields */

  if (texts == 'radios') {
    var signname = '"radios"';
    var localid = 'radios_' + radios;
    html = "<div class='row' id='" + radios + "'><div class='box box-solid'><div class='box-body'><div id='" + radios + "'><div class='form-group'><i class='fa fa-briefcase' aria-hidden='true'></i> <label>Radio</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"radio_signer_" + radios + "\"," + signname + "," + radios + ");' id='signer_radio" + radios + "' class='form-control'></select></div></div><hr><div class='row'><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='radios_required_" + radios + "' value='1' onchange='getGenerateArray(" + signname + "," + radios + ")' onclick='gerRequired(this.value," + radios + "," + signname + ")'>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='radios_read_" + radios + "' onchange='getGenerateArray(" + signname + "," + radios + ")' onclick='gerReadOnly(this.value," + radios + "," + signname + ")'  >Read Only</div></div></div></div>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".radio_wrap");

    elementWidth = $('#' + localid).find(".radio_wrap").width();
    elementHeight = $('#' + localid).find(".radio_wrap").height();
    elementPosition = $('#' + localid).find(".radio_wrap").offset();
    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = radios;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
  }


  if (texts == 'datesigned') {
    var signname = '"datesigned"';
    var localid = 'datesigned_' + totalDateSign;
    html = "<div class='row' id='" + totalDateSign + "'><div class='box box-solid'><div class='box-body'><div id='" + totalDateSign + "'><div class='form-group'><i class='fa fa-briefcase' aria-hidden='true'></i> <label>Date Signed</label></div><hr><div class='row'><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='dates_required_" + totalDateSign + "' value='1' onchange='getGenerateArray(" + signname + "," + totalDateSign + ")' onclick='gerRequired(this.value," + totalDateSign + "," + signname + ")'>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='dates_read_" + totalDateSign + "' onchange='getGenerateArray(" + signname + "," + totalDateSign + ")' onclick='gerReadOnly(this.value," + totalDateSign + "," + signname + ")'  >Read Only</div></div></div></div>";
    ActionType = $('#' + localid).attr('type');
    textHolder = $('#' + localid).find(".writing-pad1");

    elementWidth = $('#' + localid).find(".writing-pad1").width();
    elementHeight = $('#' + localid).find(".writing-pad1").height();
    elementPosition = $('#' + localid).find(".writing-pad1").offset();

    xposs = elementPosition.left - viewerPositions.left;
    yposs = elementPosition.top - viewerPositions.top;
    page = $('#' + localid).attr('page');
    deleteid = $('#' + localid).attr('id');
    deleteids = totalDateSign;
    var did = "'" + deleteid + "'";
    html += '<hr><a href="javascript:void(0)" onclick="getDeleteFields(' + did + ',' + deleteids + ')">Delete</a></div></div></div>';
  }

  $('#vishal123').append('');

  //	data.push(testingArray);

  if (localid === undefined) {

  } else {
    var ids = localid.split('_');

    ResponseList = { "tempId": ids[1], "id": localid, "type": texts, "Xpos": xposs, "Ypos": yposs, "Obj": html, "Action": ActionType, "page": page, "width": elementWidth, "height": elementHeight };

    var element = Object.assign({}, ResponseList);
    vishaldata.push(element);
  }

}
function getAjax(id) {
  $.ajax({
    url: careginer,
    type: "GET",
    success: function (response) {
      if (response != '') {

        $('.caregiverId' + id).html(response);
      }
    }
  });
}
function getPatientAjax(id) {
  $.ajax({
    url: patient,
    type: "GET",
    success: function (response) {
      if (response != '') {

        $('.patientId' + id).html(response);
      }
    }
  });
}
function getIntakeAjax(id, keys) {


  $.ajax({
    url: intake,
    type: "GET",
    data: { 'values': keys },
    success: function (response) {
      if (response != '') {

        $('.intakeId' + id).html(response);
      }
    }
  });
}

function ChangesName(val, id) {
  $("#fid" + id).attr('placeHolder', val);
}
function Scall(val) {
  var scall = $('.scall_id').val();
  if (scall >= 50 && scall <= 100) {

  } else {
    $('.scall_id').val(100)
    alert('Valid scale values: 50 - 100%');
  }
}
/*
 *  Delete selected element
 */
function deleteElement() {
  if (isDrawMode()) {
    modules.erase();
  }
  if ($(".signer-element.selected-element").length) {
    $(".signer-element.selected-element").remove();
    selectLastElement();
  }
}


/*
 *  Select the last element
 */
function selectLastElement() {
  if ($(".signer-element").length) {
    $(".signer-element[page=" + pageNum + "]").last().addClass("selected-element");
  }
}


/*
 *  Deselect all elements
 */
function deselectElements() {
  $(".signer-element").removeClass("selected-element");
}


/*
 *  hide all elements
 */
function hideElements() {
  $(".signer-element").hide();
}


/*
 *  Deactivate active tools
 */
function deactivateTools() {
  $(".signer-tool").removeClass("active");
}


/*
 *  Disable all tools
 */
function disableTools() {
  $(".signer-tool").addClass("disabled");
}


/*
 *  Enable all tools
 */
function enableTools(group) {

  if (inviting) { group = "request"; }
  disableTools();
  $(".signer-tool[tool=delete], .signer-tool[action=true]").removeClass("disabled");
  if (group === "text") {
    $(".signer-tool[group=text], .signer-tool[tool=color], .signer-tool[tool=duplicate], .signer-tool[tool=fontsize]").removeClass("disabled");
  } else if (group === "symbol" || group === "shape") {
    $(".signer-tool[tool=color], .signer-tool[tool=duplicate]").removeClass("disabled");
  } else if (group === "image") {
    $(".signer-tool[tool=duplicate]").removeClass("disabled");
  } else if (group === "draw") {
    $(".signer-tool[tool=color], .signer-tool[tool=fontsize]").removeClass("disabled");
  } else if (group === "request") {
    disableTools();
    $(".signer-tool[tool=input], .signer-tool[group=text], .signer-tool[tool=color], .signer-tool[tool=duplicate], .signer-tool[tool=fontsize], .signer-tool[tool=delete]").removeClass("disabled");
  } else {
    $(".signer-tool").removeClass("disabled");
  }
}


/*
 *  Show active text tools
 */
function showActiveTextTools() {
  //alert("vishal");
  var elem = $(".signer-element.selected-element[type=text]");
  if (elem.attr("bold") === "true") {
    $(".signer-tool[tool=bold]").addClass("active");
  } else {
    $(".signer-tool[tool=bold]").removeClass("active");
  }
  if (elem.attr("italic") === "true") {
    $(".signer-tool[tool=italic]").addClass("active");
  } else {
    $(".signer-tool[tool=italic]").removeClass("active");
  }
  if (elem.attr("underline") === "true") {
    $(".signer-tool[tool=underline]").addClass("active");
  } else {
    $(".signer-tool[tool=underline]").removeClass("active");
  }
  if (elem.attr("strikethrough") === "true") {
    $(".signer-tool[tool=strikethrough]").addClass("active");
  } else {
    $(".signer-tool[tool=strikethrough]").removeClass("active");
  }
  if (elem.attr("align") === "left") {
    $(".signer-tool[tool=alignleft]").addClass("active");
  } else {
    $(".signer-tool[tool=alignleft]").removeClass("active");
  }
  if (elem.attr("align") === "left") {
    $(".signer-tool[tool=alignleft]").addClass("active");
  } else {
    $(".signer-tool[tool=alignleft]").removeClass("active");
  }
  if (elem.attr("align") === "right") {
    $(".signer-tool[tool=alignright]").addClass("active");
  } else {
    $(".signer-tool[tool=alignright]").removeClass("active");
  }
  if (elem.attr("align") === "center") {
    $(".signer-tool[tool=aligncenter]").addClass("active");
  } else {
    $(".signer-tool[tool=aligncenter]").removeClass("active");
  }
}


/*
 *  Highlight document canvas
 */
function highlightCanvas() {
  $("#document-viewer").addClass("active");
}


/*
 *  Un highlight document canvas
 */
function unHighlightCanvas() {
  $("#document-viewer").removeClass("active");
}


/*
 *  Enable text mode
 */
function enableTextMode() {
  $(".signer-tool[tool=text]").addClass("active");
  $("#document-viewer").css('cursor', 'text');
  updateSelectedFontSize(14, "Font Size");
  highlightCanvas();
  enableTools("text");
}


/*
 *  Enable drawing mode
 */
function enableDrawMode() {
  $(".signer-tool[tool=draw]").addClass("active");
  $("#document-viewer").css('cursor', 'pointer');
  updateSelectedFontSize(5, "Stroke Size");
  highlightCanvas();
  initEditor();
  enableTools("draw");
  if (modules.original === undefined) {
    modules.original = $('#document-viewer').getCanvasImage("image/png");
  }
}


/*
 *  Check if draw mode is active
 */
function isDrawMode() {
  if ($(".signer-tool[tool=draw]").hasClass("active")) {
    return true;
  } else {
    return false;
  }
}


/*
 *  Initialize editor on scroll
 */
$('.signer-overlay').off('scroll').on('scroll', function () {
  if (isDrawMode()) {
    initEditor();
  }
});


/*
 *  Get styling used by user
 */
function currentTextStyle() {
  style = '';
  if ($(".signer-tool[tool=bold]").hasClass("active")) {
    style = style + ' bold="true"';
  }
  if ($(".signer-tool[tool=italic]").hasClass("active")) {
    style = style + ' italic="true"';
  }
  if ($(".signer-tool[tool=underline]").hasClass("active")) {
    style = style + ' underline="true"';
  }
  if ($(".signer-tool[tool=strikethrough]").hasClass("active")) {
    style = style + ' strikethrough="true"';
  }
  if ($(".signer-tool[tool=alignleft]").hasClass("active")) {
    style = style + ' align="left"';
  }
  if ($(".signer-tool[tool=alignright]").hasClass("active")) {
    style = style + ' align="right"';
  }
  if ($(".signer-tool[tool=aligncenter]").hasClass("active")) {
    style = style + ' align="center"';
  }
  return style;
}


/*
 *  Get selected color
 */
function selectedColor() {
  color = $(".signer-tool[tool=color]").attr("color");
  return color;
}


/*
 *  Get selected font
 */
function selectedFont() {
  font = {
    "font": $(".font-item.selected").attr("font"),
    "family": $(".font-item.selected").attr("family")
  };
  return font;
}


/*
 *  Updated selected value of color picker
 */
function updateColorPicker(color) {
  colorValue = color.replace("#", "");
  document.getElementById('color-picker').jscolor.fromString(colorValue);
  $(".signer-tool[tool=color]").attr("color", color);
  return true;
}


/*
 *  Get selected font size
 */
function selectedFontSize() {
  fontSize = $(".font-size").val();
  return fontSize;
}


/*
 *  Updated selected font size
 */
function updateSelectedFontSize(fontSize, label) {
  $(".font-size").val(fontSize);
  if (label !== undefined) {
    $(".font-size-label").text(label);
  }
  return true;
}


/*
 *  Updated selected font 
 */
function highlightSelectedFont(font) {
  $(".font-item").removeClass("selected");
  $(".font-item[font=" + font + "]").addClass("selected");
  return true;
}

/*
 *  Add text to canvas
 */
function addText(xPos, yPos, text, style, color, fontSize, font, page) {
  deselectElements();
  if (text === undefined) { text = ""; }
  if (style === undefined) { style = currentTextStyle(); }
  if (color === undefined) { color = selectedColor(); }
  if (font === undefined) { font = selectedFont(); }
  if (fontSize === undefined) { fontSize = selectedFontSize(); }
  if (page === undefined) { page = pageNum; }
  if ($(window).width() < 1101) {
    topOffset = 225;
  } else {
    topOffset = 185;
  }
  currentOffset = $(".signer-overlay-previewer").offset();
  yPos = parseInt(yPos - currentOffset.top + topOffset);
  $('<div class="signer-element selected-element" type="text" page="' + page + '" ' + style + ' font="' + font.font + '" color="' + color + '" font-size="' + fontSize + '" style="left:' + parseInt(xPos - 5) + 'px;top:' + parseInt(yPos - 15) + 'px;position:absolute;"><div class="writing-pad" contenteditable="true" style="color:' + color + ';font-size:' + fontSize + 'px;font-family:' + font.family + '"  spellcheck="false">' + text + '</div></div>').appendTo(".signer-builder");
  initElementsDrag();
  focusText();
}


/*
 *  Update selected element color
 */
function updateColor(color) {
  element = $(".signer-element.selected-element");
  $(".signer-tool[tool=color]").attr("color", "#" + color);
  if (element.attr("type") === "text") {
    element.attr("color", "#" + color);
    element.find(".writing-pad").css("color", "#" + color);
  } else if (element.attr("type") === "symbol" || element.attr("type") === "shape") {
    element.find("path").css("fill", "#" + color)
    element.attr("color", "#" + color);
  } else if (isDrawMode()) {
    modules.color(color);
  } else if (element.length == 0) {
    $(".signer-element[type=text]").attr("color", "#" + color);
    $(".signer-element[type=text]").find(".writing-pad").css("color", "#" + color);
  }
}


/*
 *  Update selected element font size
 */
function updateTextSize(fontSize) {
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  } else {
    elem = $(".signer-element[type=text]");
  }
  elem.attr("font-size", fontSize);
  elem.find(".writing-pad").css("font-size", fontSize + "px");
}


/*
 *  Focus on selected text
 */
function focusText() {
  $(".signer-element.selected-element[type=text]").find(".writing-pad").focus();
}


/*
 *  Style text
 */
function styleText(style, value) {
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  } else {
    elem = $(".signer-element[type=text]");
  }
  if (style === "bold") {
    if (elem.attr("bold") === "true") {
      elem.removeAttr("bold");
    } else {
      elem.attr("bold", "true");
    }
  }
  if (style === "italic") {
    if (elem.attr("italic") === "true") {
      elem.removeAttr("italic");
    } else {
      elem.attr("italic", "true");
    }
  }
  if (style === "underline") {
    if (elem.attr("underline") === "true") {
      elem.removeAttr("underline");
    } else {
      elem.attr("underline", "true");
    }
  }
  if (style === "strikethrough") {
    if (elem.attr("strikethrough") === "true") {
      elem.removeAttr("strikethrough");
    } else {
      elem.attr("strikethrough", "true");
    }
  }
  if (style === "alignleft") {
    elem.attr("align", "left");
  }
  if (style === "aligncenter") {
    elem.attr("align", "center");
  }
  if (style === "alignright") {
    elem.attr("align", "right");
  }
  showActiveTextTools();
}


/*
 *  When any area on the overlay is clicked
 */
$(".signer-overlay").click(function (event) {
  if ($(event.target).closest("#vishal123").length > 0) {
    return true;
  }
  event.preventDefault();
  if ($(".signer-element[status=drop]").length > 0) {
    if (event.target.id === "document-viewer") {
      $(".signer-element[status=drop]").css("top", parseInt(event.pageY + $(".signer-overlay").scrollTop()));
      $(".signer-element").removeAttr("status");
      $(".signer-element").css('position', 'absolute');
      $(".signer-element img").css('opacity', '1');

      enableTools($(".signer-element.selected-element").attr("type"));
      unHighlightCanvas();
      initElementsDrag();
      initElementsResize();
    }
  } else if ($(".signer-tool.active[tool=text]").length && event.target.id === "document-viewer") {
    addText(event.pageX, event.pageY);
  }
});

/*
 *  Add custom fields
 */
function addField() {
  $("#addField").modal("hide");
  fieldValue = $("input[name=fieldvalue]").val();
  fieldLabel = $("input[name=fieldlabel]").val();
  fieldId = random();
  $(".field-list").append('<div class="field-item field-' + fieldId + '"><a class="delete-field" id="delete-field" href=""><i class="ion-ios-trash-outline" id="delete-field"></i></a><div>' + fieldValue + '</div> <span class="text-muted text-xs">' + fieldLabel + '</span> </div>');
  server({
    url: saveFieldsUrl,
    data: {
      "fieldId": fieldId,
      "fieldvalue": fieldValue,
      "fieldlabel": fieldLabel,
      "csrf-token": Cookies.get("CSRF-TOKEN")
    },
    loader: false
  });
}

/*
 *  Add input fields
 */
function addInputField() {
  $("#addInputField").modal("hide");
  inputfieldlabel = $("input[name=inputfieldlabel]").val();
  fieldId = random();
  $(".input-field-list").append('<div class="input-field-item input-field-' + fieldId + '"><a class="delete-input-field" id="delete-input-field" href=""><i class="ion-ios-trash-outline" id="delete-input-field"></i></a><div>' + inputfieldlabel + '</div></div>');
  if ($("input[name=savefield]").prop("checked1")) {
    server({
      url: saveFieldsUrl,
      data: {
        "fieldId": fieldId,
        "type": "input",
        "fieldlabel": inputfieldlabel,
        "fieldvalue": '',
        "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: false
    });
  }
}

/*
 *  Field response
 */
function fieldResponse(chatKey, chatId) {
  $('.fields-list').find(".field-" + chatKey).closest(".field-item").attr("id", chatId);
  $("input[name=fieldvalue], input[name=fieldlabel]").val('');
}

/*
 *  Field response
 */
function inputFieldResponse(chatKey, chatId) {
  $('.input-field-list').find(".input-field-" + chatKey).closest(".input-field-item").attr("id", chatId);
  $("input[name=inputfieldlabel]").val('');
}

/*
 *  Create Template copy
 */
function createTemplate() {
  server({
    url: createTemplateUrl,
    data: {
      "document_key": document_key,
      "csrf-token": Cookies.get("CSRF-TOKEN")
    },
    loader: true
  });
}


/*
 *  Scale dimesions compared to the previous render
 */
function signerScale(dimesion) {
  templateWidth = $("#document-viewer").width();
  templateScale = parseFloat(templateWidth / savedWidth).toFixed(3);

  scaled = parseFloat(templateScale * dimesion).toFixed(3);
 
  //totals = parseFloat(scaled) + 3.5;
  totals = parseFloat(scaled);
  return parseFloat(totals).toFixed(3);
}


/*
 *  Scale dimesions compared to the previous render (Accept request)
 */
function signerScaler(dimesion) {
  templateWidth = $("#document-viewer").width();
  templateScale = parseFloat(templateWidth / requestWidth).toFixed(3);
  scaled = parseFloat(templateScale * dimesion).toFixed(3);
  return parseFloat(scaled).toFixed(3);
}


/*
 *  Show Template Fields
 */

function showTemplateFields() { //  
  var fruits = [];
  var resizeElement = 0;
  if (isTemplate === "docusing" && templateFields !== '' && $("body").hasClass("editor") && $(".signer-builder").is(':empty')) {


    if ($(window).width() < 1101) {
      topOffset = 225;
      maridss = 0;
    } else {
      topOffset = 185;

      maridss = 3;
    }



    currentOffset = $(".signer-overlay-previewer").offset();
    currentDocOffset = $("#document-viewer").offset();
    currentPosition = $("#document-viewer").position();
    var cnt = 1;
    var j = 0;
    var tempHeaderForHidearry = [];

    $.each(templateFields, function (i, field) {

      var tempHeaderForHide = [];
      var font = 10;
      if (field.font != 'undefined') {
        font = field.font;
      }
      var assignId = '';
      var assignVal = '';
      var receiverid = '';
      var receiverid2 = '';
      var conditionarules = '';
      var globalText;

      if (field.required == 'true') {
        required = 'required="required"';
        colors = 'error';
        bgrequired = 'border:2px solid #FF0000';
      } else {
        colors = '';
        required = '';
        bgrequired = '';
      }

      if (field.readOnly == 'readonly') {
        readonly = 'readonly="readonly"';
      } else {
        readonly = '';
      }


      xPos = parseFloat((parseFloat(signerScale(field.xPos)) + currentDocOffset.left) - 5).toFixed(2);
      yPos = parseFloat((parseFloat(signerScale(field.yPos)) + currentDocOffset.top) - 5).toFixed(2);

      Width = (parseFloat(field.width));
      height = (parseFloat(field.height));
   
      if (field.type == "image") {
       
        if (docusignId == field.signer_id) {

          bgrequired = 'border:2px solid #9f9f9f';
          var str = field.image;
      
          var imgSvg = 'data:image/svg+xml,%3Csvg%20width%3D%2218%22%20height%3D%2217%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22%20d%3D%22M18.7929%201.29289C19.1834%200.902369%2019.8166%200.902369%2020.2071%201.29289L22.7071%203.79289C23.0976%204.18342%2023.0976%204.81658%2022.7071%205.20711L15.2071%2012.7071C15.0196%2012.8946%2014.7652%2013%2014.5%2013H12C11.4477%2013%2011%2012.5523%2011%2012V9.5C11%209.23478%2011.1054%208.98043%2011.2929%208.79289L18.7929%201.29289ZM13%209.91421V11H14.0858L20.5858%204.5L19.5%203.41421L13%209.91421ZM1%2014.5C1%2012.567%202.567%2011%204.5%2011H8C8.55228%2011%209%2011.4477%209%2012C9%2012.5523%208.55228%2013%208%2013H4.5C3.67158%2013%203%2013.6716%203%2014.5C3%2015.3284%203.67158%2016%204.5%2016H19.5C21.433%2016%2023%2017.567%2023%2019.5C23%2021.433%2021.433%2023%2019.5%2023H9C8.44772%2023%208%2022.5523%208%2022C8%2021.4477%208.44772%2021%209%2021H19.5C20.3284%2021%2021%2020.3284%2021%2019.5C21%2018.6716%2020.3284%2018%2019.5%2018H4.5C2.567%2018%201%2016.433%201%2014.5Z%22%20fill%3D%22%23000000%22%3E%3C/path%3E%3C/svg%3E';
       
          if (field.signer_id != 'StampUser' && field.signer_id != 'Patient' && field.signer_id != 'Stamp' && field.user_type != 'Patient') {
          
            if(field.height == field.width){
              var width = (parseFloat(signerScale(100)));
              var height = (parseFloat(signerScale(20)));
              
            }else{
              var height = (parseFloat(signerScale(field.height)));
              var width = (parseFloat(signerScale(field.width)));
            }
			      $('<div onclick="mySign(' + cnt + ')" class="signer-element" type="image" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img src="' + imgSvg + '" id="img' + cnt + '" style="height:'+height+'px;width:'+width+'px;background-color: #FF0000;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '"></div>').appendTo(".signer-builder");
          } else if (field.signer_id == 'Patient' || field.signer_id == 'Sign' || field.signer_id == 'SignStamp') {
            if(field.height == field.width ){
              var width = (parseFloat(signerScale(100)));
              var height = (parseFloat(signerScale(40)));
            }else{
              var height = (parseFloat(signerScale(field.height)));
              var width = (parseFloat(signerScale(field.width)));
            }
           
            $('<div  onclick="mySign(' + cnt + ')" class="signer-element" type="image" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img class="doctor-signature" dataids="1" src="" id="img' + cnt + '" style="opacity:1.5;height:'+height+'px;width:'+width+'px;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '" ><input type="hidden" name="doctor_signature" id="doctor_signature" value=""></div>').appendTo(".signer-builder");
          }else if (field.signer_id != 'StampUser' && field.signer_id != 'Patient' && field.signer_id != 'Stamp' && field.user_type == 'Patient') {
            if(field.height == field.width){
              var width = (parseFloat(signerScale(100)));
              var height = (parseFloat(signerScale(40)));
            }else{
              var height = (parseFloat(signerScale(field.height)));
              var width = (parseFloat(signerScale(field.width)));
            }
            
            $('<div onclick="mySign(' + cnt + ')" class="signer-element"  type="image" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img src="' + imgSvg + '" id="img' + cnt + '" style="height:'+height+'px;width:'+width+'px;background-color: #FF0000;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '"></div>').appendTo(".signer-builder");
          }  else {
            if (field.user_type == 'Patient') {
              if(field.height == field.width){
                var width = (parseFloat(signerScale(100)));
                var height = width * 0.363636;
              }else{
                
                var height = (parseFloat(signerScale(field.height)));
                var width = (parseFloat(signerScale(field.width)));
              }

              $('<div onclick="myStampUser(' + cnt + ')" class="signer-element"  type="image" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img class="doctor-stamp" src="" id="img' + cnt + '" style="opacity:1.5;height:'+height+'px;width:'+width+'px;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '"><input type="hidden" name="doctor_stamp" id="doctor_stamp" value=""></div>').appendTo(".signer-builder");
            } else {
              $('<div onclick="myStampUser(' + cnt + ')" class="signer-element"  type="image" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img src="' + imgSvg + '" id="img' + cnt + '" style="height:30px;width:40px;background-color: #FF0000;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '"></div>').appendTo(".signer-builder");
            }

          }
        }

      }else if (field.type == "stamp") {
       
        if (docusignId == field.signer_id) {
          resizeElement =1;
          bgrequired = 'border:2px solid #9f9f9f';
          var str = field.image;
          var res = str.replace("https://smsandus.com/qa.exmedc.com/public/", "https://web.exmedc.com");
          var imgSvg = 'data:image/svg+xml,%3Csvg%20width%3D%2218%22%20height%3D%2218%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22%20d%3D%22M12%202C10.3431%202%209%203.34315%209%205V10H6C5.44772%2010%205%2010.4477%205%2011V14H19V11C19%2010.4477%2018.5523%2010%2018%2010H15V5C15%203.34315%2013.6569%202%2012%202ZM4%2016C3.44772%2016%203%2016.4477%203%2017V19C3%2020.1046%203.89543%2021%205%2021H19C20.1046%2021%2021%2020.1046%2021%2019V17C21%2016.4477%2020.5523%2016%2020%2016H4Z%22%20fill%3D%22%23000000%22/%3E%3C/svg%3E';
          
          $('<div onclick="myStamp(' + cnt + ')" class="signer-element selected-element tttts all_status_id " status="drop"  type="stamp" page="' + field.page + '" style="left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;display:none;" id="' + field.id + '" dataid="' + cnt + '"  page="' + pageNum + '"><img src="' + imgSvg + '" id="img' + cnt + '" style="height:30px;width:40px;background-color: #FF0000;' + bgrequired + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '"></div>').appendTo(".signer-builder");
          
          initElementsDrag('stamp');
        }

      }
      else if (field.type == "text") {
		resizeElement =0;
        if (field.align !== '') { field.align = ' align="' + field.align + '"'; }
        if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
        if (field.underline !== '') { field.underline = ' underline="true"'; }
        if (field.bold !== '') { field.bold = ' bold="true"'; }
        if (field.italic !== '') { field.italic = ' italic="true"'; }


        if (field.temp1 != '' && field.temp3 != '') {
          var widthss = 'auto';
          var fonts = field.font;
          yPos = parseFloat(yPos) - 5;

          var word_break = '';
          var marigns = '';


          $('<div class="signer-element" type="look" id="'+field.id +'" page="' + field.page + '" style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;z-index:99;"><input class="writing-pad1" type="hidden" id="' + field.id + '" dataid="' + field.temp1 + '" ><label class="writing-pad1" id="int' + field.id + '" vishald style="font-size:' + fonts + 'px;' + marigns + ';width:' + widthss + '; ' + word_break + '"></label></div>').appendTo(".signer-builder");


          if (field.temp1 == 'caregiver' || field.temp1 == 'patient') {


            if (pdfGenerateOrNot == '') {
              var response = GetCaregiverRequeestFileds(field.temp3, sessionIds, field.id);
            }
          } else if (field.temp1 == 'staff') {
            if (pdfGenerateOrNot == '') {

              var response = GetStaffRequeestFileds(field.temp3, sessionIds, field.id);
            }
          }


        } else {
          if (field.readOnly == 'readonly') {
          var response = field.text;
          }else{
            var response = "";
            if (field.fieldText != null && field.fieldText !== "undefined") {
                response = field.fieldText;
            }
          }
          var place = '';
          if (field.temp1 == '') {
            if (field.text != '') {
              place = field.text;
            } else {
              place = field.placeHolder;
            }
          }
          if (field.conditionaRules != undefined) {
            tempHeaderForHide.push(field.conditionaRules);

            TextArray = tempHeaderForHide;

          }
          if (field.required == 'true') {
            required = 'required="required"';
            colors = 'error';
            bgrequired = 'border:2px solid #FF0000';
          } else {
            colors = '';
            required = '';
            bgrequired = '';
          }
          var explode = field.id.split('_');
          if (explode[0] == 'text') {
            textid = 'checks' + explode[1];
          }
          if (explode[0] == 'datesigned') {
            if (pdfGenerateOrNot == '') {
              var addClasss = 'signeeddate';
              $('<div  id="' + field.id + '" class="signer-element" type="text"  group="input"   page="' + field.page + '" style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;"><textarea disabled type="text"   placeHolder="' + field.placeHolder + '" class="writing-pad1 ' + addClasss + '" id="' + field.id + '" style="width:' + signerScale(Width) + 'px;height:' + height + 'px;color:' + field.color + ';font-size:' + field.font + 'px;font-family:' + field.fontfamily + ';color:' + field.color + ';' + colors + '" value="' + DateSingDate + '">' + DateSingDate + '</textarea></div>').appendTo(".signer-builder");
            }
          }


          if (docusignId == field.signer_id) {

            var resize = '';
            var padding = '';
            var heights = height;
            if (field.textsmall == 1) {
              resize = "resize:none;";
              padding = 'padding:0;';
              heights = '13';
              yPos = parseFloat(yPos) + 4;
            }

            $.each(tempHeaderForHide[0], function (i, kes) {

              if (kes.SenderId == field.id) {

                HideShow = 'HideShow("' + kes.SenderId + '")';

              }

              setTimeout(function () { $('#' + field.id).keyup(); }, 2000);

              if (kes.ReceiverId == textid) {

                if (field.text != kes.value) {
                  $('#' + kes.ReceiverId).addClass('Depending');
                  $('#' + kes.ReceiverDivId).addClass('rules');
                }
              }

            });

            var textLimitAttr = '';
           
            if (typeof isPatient !== 'undefined' && isPatient === true && typeof textLimitForPatient !== 'undefined') {
              textLimitAttr = ' data-text-limit="' + textLimitForPatient + '" onblur="trimToMaxChars(this)"';
            }
            $('<div  class="signer-element " ' + assignVal + ' id="' + textid + '" type="text"  group="input"   page="' + field.page + '" style="width:auto;border:' + bgrequired + ';position:absolute;left:' + xPos + 'px;top:' + yPos + 'px;display:none !importants;"><textarea type="text"  onchange="getChange(this.id);" placeHolder="' + place + '" class="writing-pad1 ' + colors + '" id="' + field.id + '" style="' + bgrequired + ';width:' + signerScale(Width) + 'px;height:' + heights + 'px;color:' + field.color + ';font-size:' + font + 'px;font-family:' + field.fontfamily + ';color:' + field.color + ';' + colors + ' ;' + resize + padding + '" onkeyup=' + HideShow + ' contenteditable="true" spellcheck="false"  value="' + response + '" ' + readonly + ' ' + assignId + ' ' + assignVal + ' ' + receiverid + ' ' + receiverid2 + ' ' + conditionarules + textLimitAttr + '>' + response + '</textarea></div>').appendTo(".signer-builder");




          }

        }



      }
      else if (field.type == "checkbox") {
		resizeElement =0;
        var ress = 0;
        if (field.readOnly == 'readonly' || field.checked_defualt == 1) {
          readonly = 'readonly="readonly"';
          disabled = 'disabled';
          checked = 'checked="checked"';
          ress = 1;
        } else {
          if(field.agency_form_id != null){
            readonly = '';
            disabled = 'disabled';
            checked = '';
          }else{
            readonly = '';
            disabled = '';
            checked = '';
          }
        }
        var tempHeaderForHide = [];
        var headersNew = [];
        if (field.conditionaRules != undefined) {
          tempHeaderForHide.push(field.conditionaRules);
        }
        var checkboxid = "'" + field.id + "'";
        if (docusignId == field.signer_id) {


          HideShowNew = '';

          $('#' + field.bold).prop("checked", false);
          if (ress == 0) {
            if (field.checked == 1) {
              checked = 'checked="checked"';
              setTimeout(function () { $('#' + field.bold).prop("checked", true); }, 2000);
            } else {
              checked = '';
              setTimeout(function () { $('#' + field.bold).prop("checked", false); }, 2000);
            }

          }


          $.each(tempHeaderForHide[0], function (i, kes) {
            if (kes.type == "checkbox") {
              headers.push(kes);
            }
            if (kes.SenderId == field.bold) {
              test = "'" + kes.value + "'";
              ReceiverIds = "'" + kes.ReceiverId + "'";
              SenderIds = "'" + kes.SenderId + "'";
              HideShowNew = " HideShowCheck(" + ReceiverIds + "," + SenderIds + "," + test + ")";
            }

            if (kes.ReceiverDivId == field.id) {

              $('#' + kes.ReceiverDivId).addClass('Depending');

            }
          });
          setTimeout(function () { HideShowCheck("", field.bold, "") }, 2000);
          //var inlineStyles =  (disabled === 'disabled') ? 'background-color: black; width: 12.99px; height: 12.99px; appearance: none; -webkit-appearance: none; border-radius: 4px; cursor: pointer;' : '';
          var inlineStyles = "";
          if (field.checked_defualt == 1) {
            inlineStyles = 'background-color: black; width: 12.99px; height: 12.99px; appearance: none; -webkit-appearance: none; border-radius: 4px; cursor: pointer';
          }
          if (field.required == "true") {

            $('<div class="signer-element ' + field.bold + '" id="' + field.id + '" type="checkbox"  group="input"   page="' + field.page + '"  style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;"><input onclick="getChange(' + checkboxid + ');' + HideShowNew + '" style="' + inlineStyles + '" type="checkbox" class="checkbox_wrapper" contenteditable="true" style="" name="' + field.name + '"  value="' + field.checked + '" ' + checked + ' id="' + field.bold + '" ' + readonly + ' ' + disabled + '></div>').appendTo(".signer-builder");
          } else {
            $('<div class="signer-element ' + field.bold + '" id="' + field.id + '" type="checkbox"  group="input"   page="' + field.page + '"  style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;"><input  onclick="getChange(' + checkboxid + ');' + HideShowNew + '" style="' + inlineStyles + '" type="checkbox" class="checkbox_wrapper" contenteditable="true" style="" name="' + field.name + '"  value="' + field.checked + '" ' + checked + ' id="' + field.bold + '" ' + readonly + ' ' + disabled + ' ></div>').appendTo(".signer-builder");
          }


        }
      } else if (field.type == "radio") {
		resizeElement =0;
        var explode = field.id.split('_');
        var ress = 0;
        if (field.readOnly == 'readonly' || field.checked_defualt_radio == 1) {
          readonly = 'readonly="readonly"';
          disabled = 'disabled';
          checked = 'checked="checked"';
          ress = 1;
        } else {
          readonly = '';
          disabled = '';
          checked = '';
        }

        var tempHeaderForHide = [];
        if (field.conditionaRules != undefined) {
          tempHeaderForHide.push(field.conditionaRules);

        }

        if(field.checked ==field.normalValueRadio){
					//checked = 'checked="checked"';
				}
       
        if (field.checked == 1 && ress == 0) {
          checked = "checked='checked'";

        }else{
          checked = "";
        }

        
        var checkboxid = "'" + field.id + "'";

        var inlineStyles = "";
        if (field.checked_defualt_radio == 1) {
          inlineStyles = 'background-color: black; width: 12.99px; height: 12.99px; appearance: none; -webkit-appearance: none; border-radius: 4px; cursor: pointer';
        }

        if (docusignId == field.signer_id) {
          HideShow = ' onclick = HideShowRadio("' + field.bold + '",this.value)';
          $('<div class="signer-element ' + field.groupNames + '" id="' + field.id + '" type="radio" groupName="' + field.groupNames + '" group="input"   page="' + field.page + '"  style="' + bgrequired + ';position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;"><input onchange="getChange(' + checkboxid + ')" ' + checked + ' ' + disabled + ' type="radio" id = "' + field.bold + '" class="radio_wrap"  value="' + field.checked + '" ' + HideShow + '  style="' + inlineStyles + '"  name="' + field.name + '" ' + readonly + ' ></div>').appendTo(".signer-builder");
          $.each(tempHeaderForHide[0], function (i, kes) {

            if (kes.type == "radio") {
              RadiosArray.push(kes);
            }
            if (kes.SenderDivId == field.bold) {
              HideShow = ' onclick = HideShowRadio("' + kes.SenderDivId + '",this.value)';
            }
            if (kes.ReceiverId == field.bold) {

              $('#' + kes.ReceiverId).addClass('Depending');
              $('#' + kes.ReceiverId).addClass('rules');
            }
            setTimeout(function () { HideShowRadio(kes.SenderDivId, kes.value) }, 2000);
          });
        }
      } else if (field.type == "dropdown") {
		    resizeElement =0;
        var dropsdownList = '';
        if (field.drops_valeus != 'undefined') {
          dropsdownList = '<option value="">Select</option>';

          $.each(field.drops_valeus, function (i, val) {
            selected = '';
            if (field.text == val.value) {
              selected = "selected='selected'";
            }
            dropsdownList += '<option value="' + val.value + '" ' + selected + '>' + val.value + '</option>';
          });
        }
        var explode = field.id.split('_');

        var responseid = "'" + field.id + "'";
        var HideShow = '=""';
        if (docusignId == field.signer_id) {
          var tempHeaderForHide = [];
          if (field.conditionaRules != undefined) {
            tempHeaderForHide.push(field.conditionaRules);

            ConditionalSTempArray = tempHeaderForHide;
          }
          HideShowNew = '';
          $.each(tempHeaderForHide[0], function (i, kes) {
            Sends = "'" + kes.SenderDivId + "'";


            if (kes.SenderDivId == field.id) {
              HideShowNew = "HideShowDrop(" + Sends + ",this.value)";
            }

          });

          HideShowd = 'getChange(' + responseid + ');' + HideShowNew;
          $('<div class="signer-element"   id="' + field.id + '" type="dropdown"    page="' + field.page + '" style="position:absolute;display:none;z-index:99;left:' + xPos + 'px;top:' + yPos + 'px;" ><select name="dropdownd" class="drips" id="dropid' + explode[1] + '"  style="' + bgrequired + ';width:' + field.width + 'px;height:' + field.height + 'px;background-color:' + field.background_color + '" background_color="' + field.background_color + '" signer_id="' + field.signer_id + '" onchange="' + HideShowd + '">' + dropsdownList + '</select></div>').appendTo(".signer-builder");
          $.each(tempHeaderForHide[0], function (i, kes) {
            setTimeout(function () { HideShowDrop(kes.SenderDivId, kes.value) }, 2000);
            if (field.text != kes.value) {
              if (kes.ReceiverDivId == field.id) {
                $('#' + kes.ReceiverId).addClass('Depending');
                $('#' + kes.ReceiverDivId).addClass('rules');
              }
            }


          });
        }
      }
      hideElements();
      
      cnt++;
      j++;
    });


  }
  $("[page=" + pageNum + "]").show();
}

/*
 *  When accept request is clicked
 */
$(".accept-request").click(function (event) {
  event.preventDefault();
  $("body").addClass("accept");
  inviting = false;
  launchEditor();
})

/*
 *  Accept request
 */
function acceptRequest() {
  if ($("body").hasClass("accept") && requestPositions.length) {
    showLoader();
    if ($(window).width() < 1101) {
      topOffset = 225;
    } else {
      topOffset = 185;
    }
    currentOffset = $(".signer-overlay-previewer").offset();
    currentDocOffset = $("#document-viewer").offset();
    currentPosition = $("#document-viewer").position();
    textInputs = [];
    $.each(requestPositions, function (i, field) {
      xPos = parseFloat(parseFloat(signerScaler(field.xPos)) + currentDocOffset.left - 5).toFixed(3);
      yPos = parseFloat((parseFloat(signerScaler(field.yPos)) + currentDocOffset.top) - currentOffset.top + topOffset - 5).toFixed(3);
      if (field.type == "image") {
        $('<div class="signer-element" type="signature" signed="false" group="field" page="' + field.page + '" style="display:none;left:' + xPos + 'px;top:' + yPos + 'px;position:absolute;"><img src="' + baseUrl + '/assets/images/signhere.png" style="width:' + signerScaler(field.width) + 'px;"></div>').appendTo(".signer-builder");
      } else if (field.type == "text") {
        elementId = random({ case: "lower" });
        textInputs.push({ label: field.text, element: elementId });
        if (field.align !== '') { field.align = ' align="' + field.align + '"'; }
        if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
        if (field.underline !== '') { field.underline = ' underline="true"'; }
        if (field.bold !== '') { field.bold = ' bold="true"'; }
        if (field.italic !== '') { field.italic = ' italic="true"'; }
        $('<div class="signer-element element-' + elementId + '" type="text" group="field" ' + field.align + field.italic + field.bold + field.underline + field.strikethrough + '  page="' + field.page + '" font="' + field.font + '" color="' + field.color + '" font-size="' + field.fontsize + '" style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;"><div class="writing-pad" contenteditable="true" style="width:' + signerScaler(field.width) + 'px;height:' + signerScaler(field.height) + 'px;color:' + field.color + ';font-size:' + field.fontsize + 'px;font-family:' + field.fontfamily + ';color:' + field.color + ';"  spellcheck="false">' + field.text + '</div></div>').appendTo(".signer-builder");
      }
      hideElements();
    });
    if (textInputs.length) {
      $.each(textInputs, function (i, input) {
        $(".requested-fields").append('<div class="col-md-6"><div class="form-group"><label>' + input.label + '</label><input type="text" data-id="' + input.element + '" class="form-control" placeholder="' + input.label + '" required></div></div>')
      });
      $("#requestFields").modal({ show: true, backdrop: 'static', keyboard: false });
    }
    $("[page=" + pageNum + "]").show();
    hideLoader();
  }
}

/*
 *  Put data from requsted fields form to the PDF
 */
function updateRequestFields() {
  $(".requested-fields input").each(function (i, input) {
    elementId = $(this).attr("data-id");
    $(".signer-element.element-" + elementId).find(".writing-pad").text($(this).val());
  });
  $("#requestFields").modal("hide");
  $("body").removeClass("accept");
  disableTools();
}

/*
 *  Login restricted
 */
function loginRequired() {
  notify("Login Required", "You need to login to access this feature.", "warning", "Login Now", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('" + loginPage + "')" });
  return false;
}

function DragDops() {
  var tess = $("input[name=duplicate]").prop("checked");
}

function getKeys(vals) {

  var textarea1 = $(".textare" + vals).val();
  if (textarea1 == '') {
    $('#checks' + vals).val(" ");
  } else {
    $('#checks' + vals).val(textarea1);
  }

}
var final_array = [];
var new_array = [];
function getGenerateArray(key, val) {

  getGenerateArray(key, val, null);

}

function getGenerateArray(key, val, temp) {
  deleteids = val;

  if (key == 'signature') {
    var did = "sign_" + val;
    var signname = '"signature"';
    var selected = $('#signer_signatures' + val).html();
    var signRequired = $('#check_sign_' + val).prop('checked');
    if (signRequired == true) {
      var signname = '"signature"';
      required = "<input type='checkbox' class='' name='signature" + val + "' id='check_sign_" + val + "' onclick='getGenerateArray('signature'," + val + ")' value='1' checked='checked'>";
      signRequired = 1;

    } else {
      required = "<input type='checkbox' class='' name='signature" + val + "' id='check_sign_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1'>";
      signRequired = 0;

    }
    var signRead = $('#check_read_' + val).prop('checked');
    if (signRead == true) {
      signReadOnly = 1;
      readOnly = "<input type='checkbox' class='minimal'  name='signature_read" + sign + "' id='check_read_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1' checked='checked'>";
    } else {
      signReadOnly = 0;
      readOnly = "<input type='checkbox' class='minimal'  name='signature_read" + val + "' id='check_read_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1'>";
    }
    html = "<div class='row' id=" + val + "><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'> <label>Signature</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"signatures_signer_" + val + "\"," + signname + "," + val + ");' id='signer_signatures" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='form-group'><label>" + required + "</label>Required Field</div></div><div class='col-md-12'><div class='form-group'><label>" + readOnly + "</label>Read Only</div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + did + "," + deleteids + ")'>Delete</a></div></div></div>";
  }

  if (key == 'initial') {
    var signname = '"initial"';
    var localid = 'initial_' + val;

    var selected = $('#initial_signer' + val).html();
    var signRequired = $('#check_intial_' + val).prop('checked');
    if (signRequired == true) {
      required = "<input type='checkbox' class='minimal' name='intialize" + val + "' id='check_intial_" + val + "' onclick='getGenerateArray(" + signname + "," + val + ")' value='1' checked='checked'>";
    } else {
      required = "<input type='checkbox' class='minimal' name='intialize" + val + "' id='check_intial_" + val + "' onclick='getGenerateArray(" + signname + "," + val + ")' value='1'>";
    }


    html = "<div class='row' id=" + val + "><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><label>Initial</label></div><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"initial_signer_" + val + "\"," + signname + "," + val + ");' id='signer_initail" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='form-group'><label>" + required + "</label>Required Field</div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";
  }


  if (key == 'company') {
    var signname = '"company"';
    var localid = 'company_' + val;

    var companyRequired = $('#company_required_' + val).prop('checked');

    if (companyRequired == true) {
      requiredCompany = "<input type='checkbox' name='company_required_" + val + "' id='company_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1' checked='checked'>";
      requireds = 1;
    } else {
      requiredCompany = "<input type='checkbox' name='company_required_" + val + "' id='company_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
      requireds = 0;
    }

    var companyRead = $('#company_reads_' + val).prop('checked');

    if (companyRead == true) {
      readOnlyCompany = "<input type='checkbox' name='company_read_" + val + "' id='company_reads_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerReadOnly(this.value," + val + "," + signname + ")' checked='checked'>";
      readOnlys = 1;
    } else {
      readOnlyCompany = "<input type='checkbox' name='company_read_" + val + "' id='company_reads_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerReadOnly(this.value," + val + "," + signname + ")'  >";
      readOnlys = 0;
    }


    html = "<div class='row'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class=''><i class='fa fa-building' aria-hidden='true'></i> <label>Company</label></div><hr><div class=''><div class='col-md-12'><div class='form-group'><label>" + requiredCompany + "</label>Required Field</div></div><div class='col-md-12'><div class='form-group'><label>" + readOnlyCompany + "</label>Read Only</div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";


  }

  if (key == 'text') {
    var signname = '"text"';
    var localid = 'text_' + val;
    var selected = $('#textss' + val).html();
    var titleRequired = $('#text_required_' + val).prop('checked');
    if (titleRequired == true) {
      textRequired = 1;
      requiredName = "<input type='checkbox' name='title_required" + val + "' id='text_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerRequired(this.value," + val + "," + signname + ")'  value='1' checked='checked'>";
    } else {
      textRequired = 0;
      requiredName = "<input type='checkbox' name='title_required" + val + "' id='text_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerRequired(this.value," + val + "," + signname + ")'  value='1'>";
    }

    var titleRead = $('#text_read_' + val).prop('checked');
    if (titleRead == true) {
      textReadOnly = 1;
      readOnlyText = "<input type='checkbox' name='title_required" + val + "' id='text_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerReadOnly(this.value," + val + "," + signname + ")' checked='checked'>";
    } else {
      textReadOnly = 0;
      readOnlyText = "<input type='checkbox' name='title_required" + val + "' id='text_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerReadOnly(this.value," + val + "," + signname + ")' >";
    }
    var textare = $('.textare' + val).val();
    if (textare != '') {
      textareaDetails = textare;
    } else {
      textareaDetails = "";
    }

    html = "<div class='row' id=" + val + "><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-text-width' aria-hidden='true'></i>&nbsp;&nbsp;<label>Text</label></div><hr><div class='col-md-12'><div class='form-group'><select name='changesColor' onchange='setSigner(this.id,\"checks" + val + "\"," + signname + "," + val + ");' id='textss" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredName + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnlyText + "</label>Read Only</div></div><hr></div><div class='form-group'><label>Add Text</label><div class=''><textarea class='textare" + val + "'onkeyup='getKeys(" + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")'>" + textareaDetails + "</textarea></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";


  }

  if (key == 'checkbox') {
    var signname = '"checkbox"';
    var localid = 'checkboxs_' + val;
    var selected = $('#signer_checkbox' + val).html();
    var titleRequired = $('#checkbox_required_' + val).prop('checked');

    if (titleRequired == true) {
      checRequired = 1;
      requiredTitle = "<input type='checkbox' checked='checked' name='checkbox_required" + val + "' id='checkbox_required_" + val + "' onclick='gerRequired(" + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    } else {
      checRequired = 0;
      requiredTitle = "<input type='checkbox' name='checkbox_required" + val + "' id='checkbox_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    }

    var titleRead = $('#checkbox_read_' + val).prop('checked');
    if (titleRead == true) {
      checReadOnly = 1;
      readOnlyTitle = "<input type='checkbox' checked='checked' name='checkbox_read" + val + "' id='checkbox_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerReadOnly(this.value," + val + "," + signname + ")' checked='checked'>";
    } else {
      checReadOnly = 0;
      readOnlyTitle = "<input type='checkbox' name='checkbox_read" + val + "' id='checkbox_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    }



    html = "<div class='row' id='" + val + "'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Checkbox Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"checkbox_signer_" + val + "\"," + signname + "," + val + ");' id='signer_checkbox" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredTitle + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnlyTitle + "</label>Read Only</div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";

  }

  if (key == 'radios') {
    deleteids = val;
    var signname = '"radios"';
    var localid = 'radios_' + val;
    var selected = $('#signer_radio' + val).html();
    var titleRequired = $('#radios_required_' + val).prop('checked');

    if (titleRequired == true) {
      checRequired = 1;
      requiredTitle = "<input type='checkbox' checked='checked' name='radios_required_" + val + "' id='radios_required_" + val + "' onclick='gerRequired(" + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    } else {
      checRequired = 0;
      requiredTitle = "<input type='checkbox' name='radios_required_" + val + "' id='radios_required_" + val + "' onchange='getGenerateArray(" + signname + "," + val + ")' value='1' onclick='gerRequired(this.value," + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")'>";
    }

    var titleRead = $('#checkbox_read_' + val).prop('checked');
    if (titleRead == true) {
      checReadOnly = 1;
      readOnlyTitle = "<input type='checkbox' checked='checked' name='checkbox_read" + val + "' id='checkbox_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' checked='checked' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    } else {
      checReadOnly = 0;
      readOnlyTitle = "<input type='checkbox' name='checkbox_read" + val + "' id='checkbox_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    }



    html = "<div class='row' id='" + val + "'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Radio Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"radio_signer_" + val + "\"," + signname + "," + val + ");' id='signer_radio" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredTitle + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnlyTitle + "</label>Read Only</div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";

  }

  if (key == 'fields') {
    var signname = '"fields"';
    var localid = 'caregiver_' + val;
    var titleRequired = $('#caregiber_patient_' + val).prop('checked');

    if (titleRequired == true) {
      checRequired = 1;
      requiredTitle = "<input type='checkbox' checked='checked' name='caregiber_patient" + val + "' id='caregiber_patient_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    } else {
      checRequired = 0;
      requiredTitle = "<input type='checkbox' name='caregiber_patient" + val + "' id='caregiber_patient_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    }

    var titleRead = $('#caregiber_patient_read_' + val).prop('checked');

    if (titleRead == true) {
      checReadOnly = 1;
      readOnlyTitle = "<input type='checkbox' checked='checked' name='checkbox_read" + val + "' id='caregiber_patient_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' checked='checked' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    } else {
      checReadOnly = 0;
      readOnlyTitle = "<input type='checkbox' name='checkbox_read" + val + "' id='caregiber_patient_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    }



    html = "<div class='row' id='" + val + "'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Caregiver Look Up fields</label></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredTitle + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnlyTitle + "</label>Read Only</div></div><hr><div class='row'><div class='form-group'><label>Dropdown</label><select class='caregiverId" + fields_caregiver + " form-control' onchange='caregiverWiseChange(this.value," + fields_caregiver + ")'></select></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div><script>getAjax('" + fields_caregiver + "')</script>";

  }

  if (key == 'patient') {
    var signname = '"patient"';
    var localid = 'patient_' + val;
    var titleRequired = $('#patient_required_' + val).prop('checked');

    if (titleRequired == true) {
      checRequired = 1;
      requiredTitle = "<input type='checkbox' checked='checked' name='patient" + val + "' id='patient_required_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    } else {
      checRequired = 0;
      requiredTitle = "<input type='checkbox' name='patient" + val + "' id='patient_required_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    }

    var titleRead = $('#patient_read_' + val).prop('checked');

    if (titleRead == true) {
      checReadOnly = 1;
      readOnlyTitle = "<input type='checkbox' checked='checked' name='patient_read_" + val + "' id='patient_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' checked='checked' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    } else {
      checReadOnly = 0;
      readOnlyTitle = "<input type='checkbox' name='patient_read_" + val + "' id='patient_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    }



    html = "<div class='row' id='" + val + "'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Patient Look Up fields</label></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredTitle + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnlyTitle + "</label>Read Only</div></div><hr><div class='row'><div class='form-group'><label>Dropdown</label><select class='patientId" + val + " form-control' onchange='patientWiseChange(this.value," + val + ")'></select></div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div><script>getPatientAjax('" + val + "')</script>";

  }


  if (key == 'intake') {
    var signname = '"intake"';
    var localid = 'intake_' + val;
    var titleRequired = $('#intake_required_' + val).prop('checked');

    if (titleRequired == true) {
      checRequired = 1;
      requiredTitle = "<input type='checkbox' checked='checked' name='patient" + val + "' id='intake_required_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='1'>";
    } else {
      checRequired = 0;
      requiredTitle = "<input type='checkbox' name='intake" + val + "' id='intake_required_" + val + "' onclick='gerRequired(this.value," + signname + "," + val + ")' onchange='getGenerateArray(" + signname + "," + val + ")' onclick='gerRequired(this.value," + val + "," + signname + ")' value='0'>";
    }

    var titleRead = $('#intake_read_' + val).prop('checked');

    if (titleRead == true) {
      checReadOnly = 1;
      readOnlyTitle = "<input type='checkbox' checked='checked' name='intake_read_" + val + "' id='intake_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")' checked='checked' onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    } else {
      checReadOnly = 0;
      readOnlyTitle = "<input type='checkbox' name='intake_read_" + val + "' id='intake_read_" + val + "' value='1' onchange='getGenerateArray(" + signname + "," + val + ")'  onclick='gerReadOnly(this.value," + val + "," + signname + ")'>";
    }



    html = "<div class='row' id='" + val + "'><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Intake Look Up fields</label></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + requiredTitle + "</label>Required Field</div></div><div class='row'><div class='form-group'>label>" + readOnlyTitle + "</label>Read Only</div></div><hr><div class='row'><div class='form-group'><label>Dropdown</label><select class='patientId" + val + " form-control' onchange='patientWiseChange(this.value," + val + ")'></select></div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div><script>getPatientAjax('" + val + "')</script>";

  }

  if (key == 'dropdown') {
    var selected = $('#signer_dropdown1' + val).html();
    var signRequired = $('#drop_required_' + val).prop('checked');
    var selected = $('#signer_dropdown' + val).html();
    var signname = '"dropdown"';
    if (signRequired == true) {
      required = "<input type='checkbox' class='' name='DropRequired' id='drop_required_" + val + "' onclick='getGenerateArray('signature'," + val + ")' value='1' checked='checked'>";
      signRequired = 1;

    } else {
      required = "<input type='checkbox' class='' name='signature" + val + "' id='drop_required_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1'>";
      signRequired = 0;

    }
    var signRead = $('#check_read_' + val).prop('checked');
    if (signRead == true) {
      signReadOnly = 1;
      readOnly = "<input type='checkbox' class='minimal'  name='DropRead_" + val + "' id='drops_read_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1' checked='checked'>";
    } else {
      signReadOnly = 0;
      readOnly = "<input type='checkbox' class='minimal'  name='DropRead_" + val + "' id='drops_read_" + val + "' onclick='getGenerateArray(signature," + val + ")' value='1'>";
    }

    var jsons = localStorage.getItem(val);
    var final_obj = [];

    $.each(final_array, function (index, val) {

      final_obj.push(val.response);

    });
    html = "<div class='row' id=" + val + "><div class='box box-solid'><div class='box-body'><div id='" + val + "'><div class='form-group'><label>Dropdown</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"dropid" + val + "\"," + signname + "," + val + ");' id='signer_dropdown" + val + "' class='form-control'>" + selected + "</select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>" + required + "</label>Required Field</div></div><div class='row'><div class='form-group'><label>" + readOnly + "</label>Read Only</div></div></div><hr><div class='col-md-12'><span>Fill in the list of options.</span><div id='multid" + val + "'>" + final_obj + "</div><a onclick='addmore(" + signname + "," + val + "," + temp + ")'><i class='fa fa-plus'></i>Add Option</a></div><div class='col-md-12'><label>Default Option</label><select class='drops_" + val + "' onchange='selectValue(" + val + ",this.value)'><option>Select</option></select></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields(" + localid + "," + deleteids + ")'>Delete</a></div></div></div>";
  }

  var tempread = [];
  $.each(vishaldata, function (index, val) {
    if (val.id == updatenewselecte.id) {
      val.Obj = html;
    }
    tempread.push(val);
  });

  vishaldata = tempread;

}

var updatenewselecte;
var selectedValue;
$('.signer-builder').on('click', '.selected-element', function () {
  var id = $('.selected-element').attr('id');
  var keysvale = id.split("_");

  $('#vishal123').empty();

  $.each(vishaldata, function (index, value) {
    if (id == value.id) {

      updatenewselecte = value;
      selectedValue = value.signer_id;
    }

  });

  var response = updatenewselecte.Obj;
  $('#vishal123').append(response);
  getType(keysvale[1], keysvale[0], selectedValue);
  if (keysvale[0] == 'intake') {
    var selectedValue = $('#intakes_' + keysvale[1]).attr('title');

    getIntakeAjax(keysvale[1], selectedValue);
  }
});

function caregiverWiseChange(val, id) {
  var text = $('.caregiverId' + id + ' option:selected').text();
  $('#caregivers_' + id).attr('placeHolder', text);
  $('#caregivers_' + id).attr('title', val);

}

function patientWiseChange(val, id) {

  var text = $('.patientId' + id + ' option:selected').text();
  $('#patients' + id).attr('placeHolder', text);
  $('#patients' + id).attr('title', val);
}
function intakeWiseChange(val, id) {
  var text = $('.intakeId' + id + ' option:selected').text();
  $('#intakes_' + id).attr('placeHolder', text);
  $('#intakes_' + id).attr('title', val);
}




/*
 *  Custom tools select Rotation
 */
$(".signer-tools").click(function (event) {
  event.preventDefault();
  if ($(this).hasClass("disabled")) {
    return false;
  }
  if ($(this).attr("action") === "true") {
    deselectElements();
    deactivateTools();
  }

  var tool = $(this).attr("tool");
  if (tool !== "rotate" && $('action[type=rotate]').length) {
    toastr.warning("Save rotation changes before editing document.", "Hmm!", { timeOut: 2000, closeButton: true, progressBar: false });
    return false;
  }
  if (tool === "rotate") {
    if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
      toastr.warning("Save changes before rotating.", "Hmm!", { timeOut: 2000, closeButton: true, progressBar: false })
    } else {
      rotatePage(pageNum);
    }
  } else if (tool === "image") {
    $("#selectImage").modal({ show: true, backdrop: 'static', keyboard: false });
  } else if (tool === "delete") {
    deleteElement();
  } else if (tool === "text") {
    enableTextMode();
  } else if (tool === "font") {
    $(".right-bar.font-list").toggleClass("open");
  } else if (tool === "symbol") {
    $(".right-bar.symbol-list").toggleClass("open");
  } else if (tool === "shape") {
    $(".right-bar.shape-list").toggleClass("open");
  } else if (tool === "fields") {
    if (auth) {
      $(".right-bar.fields-list").toggleClass("open");
    } else {
      loginRequired();
    }
  } else if (tool === "input") {
    if (auth) {
      if (isTemplate === "Yes" || inviting) {
        $(".right-bar.input-fields-list").toggleClass("open");
      } else {
        notify("Template Only", "Inputs are added to templates only. Do you want to create a template copy of this file?", "warning", "Yes, Create", { showCancelButton: true, closeOnConfirm: true, callback: "createTemplate()" });
      }
    } else {
      loginRequired();
    }
  } else if (tool === "color") {
    document.getElementById('color-picker').jscolor.show();
  } else if (tool === "duplicate") {
    duplicateSelected();
  } else if (tool === "signature") {
    enableSignatureMode();
  } else if (tool === "draw") {
    enableDrawMode();
  } else if (tool === "bold" || tool === "italic" || tool === "underline" || tool === "strikethrough" || tool === "alignright" || tool === "aligncenter" || tool === "alignleft") {
    styleText(tool);
  }
});

/*Confirm message */
var final_arrays = [];
var ttesss = [];
var signature = null;

function GetLoadComponents() {

  $.ajax({
    url: baseUrl + "/esign/template/esign-lookup-fields-new1/" + document_key,
    type: "GET",

    success: function (response) {
      if (response != '') {
        var json = JSON.parse(response);
        signature = json.doctor_signature;
        stamp = json.doctor_stamp;
        var imagePath = json.doctor_signature;
        $('.doctor-signature').attr('dataids', '');

        let staticUrlold = baseUrl+'/assets/images/new_favicon_01.png';
        let staticUrl = "data:image/svg+xml,%3Csvg%20width%3D%2218%22%20height%3D%2217%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22%20d%3D%22M18.7929%201.29289C19.1834%200.902369%2019.8166%200.902369%2020.2071%201.29289L22.7071%203.79289C23.0976%204.18342%2023.0976%204.81658%2022.7071%205.20711L15.2071%2012.7071C15.0196%2012.8946%2014.7652%2013%2014.5%2013H12C11.4477%2013%2011%2012.5523%2011%2012V9.5C11%209.23478%2011.1054%208.98043%2011.2929%208.79289L18.7929%201.29289ZM13%209.91421V11H14.0858L20.5858%204.5L19.5%203.41421L13%209.91421ZM1%2014.5C1%2012.567%202.567%2011%204.5%2011H8C8.55228%2011%209%2011.4477%209%2012C9%2012.5523%208.55228%2013%208%2013H4.5C3.67158%2013%203%2013.6716%203%2014.5C3%2015.3284%203.67158%2016%204.5%2016H19.5C21.433%2016%2023%2017.567%2023%2019.5C23%2021.433%2021.433%2023%2019.5%2023H9C8.44772%2023%208%2022.5523%208%2022C8%2021.4477%208.44772%2021%209%2021H19.5C20.3284%2021%2021%2020.3284%2021%2019.5C21%2018.6716%2020.3284%2018%2019.5%2018H4.5C2.567%2018%201%2016.433%201%2014.5Z%22%20fill%3D%22%23000000%22%3E%3C/path%3E%3C/svg%3E";
        if(imagePath !="" && imagePath != null){
          staticUrl = imagePath;
         
          $('.doctor-signature').attr('dataids', 1);
        }
        $('.doctor-signature').attr('src', staticUrl);
        if(imagePath == "" || imagePath == null){
          $('.doctor-signature').css({
            'height': '61.83px',
            'width': '167.161px',
            'background-color': '#FF0000',
            'border': '2px solid #9f9f9f'
          });
        }
        $('#doctor_signature').val(signature);

        var imagePathStamp = json.doctor_stamp;
        $('.doctor-stamp').attr('dataids', '');

        let staticStamp ='data:image/svg+xml,%3Csvg%20width%3D%2218%22%20height%3D%2217%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22%20d%3D%22M18.7929%201.29289C19.1834%200.902369%2019.8166%200.902369%2020.2071%201.29289L22.7071%203.79289C23.0976%204.18342%2023.0976%204.81658%2022.7071%205.20711L15.2071%2012.7071C15.0196%2012.8946%2014.7652%2013%2014.5%2013H12C11.4477%2013%2011%2012.5523%2011%2012V9.5C11%209.23478%2011.1054%208.98043%2011.2929%208.79289L18.7929%201.29289ZM13%209.91421V11H14.0858L20.5858%204.5L19.5%203.41421L13%209.91421ZM1%2014.5C1%2012.567%202.567%2011%204.5%2011H8C8.55228%2011%209%2011.4477%209%2012C9%2012.5523%208.55228%2013%208%2013H4.5C3.67158%2013%203%2013.6716%203%2014.5C3%2015.3284%203.67158%2016%204.5%2016H19.5C21.433%2016%2023%2017.567%2023%2019.5C23%2021.433%2021.433%2023%2019.5%2023H9C8.44772%2023%208%2022.5523%208%2022C8%2021.4477%208.44772%2021%209%2021H19.5C20.3284%2021%2021%2020.3284%2021%2019.5C21%2018.6716%2020.3284%2018%2019.5%2018H4.5C2.567%2018%201%2016.433%201%2014.5Z%22%20fill%3D%22%23000000%22%3E%3C/path%3E%3C/svg%3E';
        if(imagePathStamp !=""){
          staticStamp = imagePathStamp;
          
          $('.doctor-stamp').attr('dataids', 1);
          
        }
        
        $('.doctor-stamp').attr('src', staticStamp);
        $('#doctor_stamp').val(stamp);
        $.each(json, function (index, value) {
          var deletedId = '';
          if(value !=null){
            var ids = value.id?.split('_');
            if (ids?.[0] == 'sign') {
              texts = 'signature';
            }
  
            if (ids?.[0] == 'stamp') {
              texts = 'stamp';
            }
  
            if (ids?.[0] == 'text') {
              texts = 'text';
              deletedId = ids[1];
  
            }
            if (ids?.[0] == 'fields') {
              texts = 'fields';
            }
            if (ids?.[0] == 'checkboxs') {
              texts = 'checkboxs';
            }
  
            if (ids?.[0] == 'radios') {
              texts = 'radios';
            }
            if (ids?.[0] == 'staff') {
              texts = 'fields_staff';
              deletedId = ids[1];
  
            }
            if (ids?.[0] == 'patient') {
              texts = 'fields_patient';
            }
            if (ids?.[0] == 'caregiver') {
              texts = 'fields';
              deletedId = ids[1];
            }
            if (ids?.[0] == 'datesigned') {
              texts = 'datesigned';
              deletedId = ids[1];
            }
            if (ids?.[0] == 'dropdowsns') {
              texts = 'dropdowsns';
              deletedId = ids[1];
            }
            
            if(typeof ids?.[1] !="undefined"){
              if (value.groupSmapleId != undefined && (ids[0] != 'datesigned' && ids[0] != 'staff' && ids[0] != 'caregiver' && ids[0] != 'text' && ids[0] != 'dropdowsns')) {
                var deletedIds = value.groupSmapleId.split('_');
                deletedId = deletedIds[1];
              }
    
              // ResponseList = { "tempId": ids[1], "id": value.id, "deleteids": deletedId, "groupname": value.groupNames, "type": texts, "Xpos": value.xPos, "Ypos": value.yPos, "Obj": value.obj, "Action": value.type, "page": value.page, "width": value.width, "height": value.height, "signer_id": value.signer_id, "signer_id": value.signer_id };
              ResponseList = { "tempId": ids[1], "id": value.id, "deleteids": deletedId, "groupname": value.groupNames, "type": texts, "Xpos": value.xPos, "Ypos": value.yPos, "Obj": value.obj, "Action": value.type, "page": value.page, "width": value.width, "height": value.height, "signer_id": value.signer_id, "signer_id": value.signer_id, "doctor_signature": response.doctor_signature };
              var element = Object.assign({}, ResponseList);
    
              vishaldata.push(element);
              if (value.conditionaRules != undefined) {
                ConditionalTempArray.push(value.conditionaRules);
    
              }
              if (removeScript == "") {
                if (value.drops_valeus != undefined) {
                  ttesss.push(value.drops_valeus);
                }
              }
            }
            
          }
          
        });
        if (removeScript == "") {

          $.each(ConditionalTempArray[0], function (i, l) {

            if (l.opponent == 'dropdown' || l.opponent == 'radio') {
              $('#' + l.ReceiverDivId).addClass('Depending');
            } else {
              $('#' + l.ReceiverId).addClass('Depending');
            }

          });
        } else {
          $.each(ConditionalTempArray[0], function (i, l) {
            if (l.opponent == 'dropdown' || l.opponent == 'radio') {
              if (l.opponent == 'radio') {

                var explode = $('#' + l.ReceiverDivId).attr('groupName');
                var SenderExplode = $('#' + l.SenderId).attr('groupName');
                if (explode == SenderExplode) {
                  $("#" + l.ReceiverDivId).addClass('Depending');
                } else {
                  $.each($("input[name='" + explode + "']"), function (i, ls) {
                    var TotalRadioId = $(this).parent().attr('id');
                    $("#" + TotalRadioId).addClass('Depending');
                  })

                }

              } else {
                $('#' + l.ReceiverId).addClass('Depending');
              }

            } else {
              if (l.opponent == 'checkbox') {
                $('#' + l.ReceiverDivId).addClass('Depending');
              } else {
                $('#' + l.ReceiverId).addClass('Depending');
              }

            }

            headers.push(l);
          });
        }

        var tttse = '';
        if (ttesss != undefined) {
          final_arrays = ttesss;
          $.each(final_arrays, function (index, values) {
            if (values != 'undefined') {
              tttse = $.merge(final_array, values);
            }
          });
          if (final_arrays.length === 0) {

          } else {
            final_array = tttse;
          }
        }

      }

    }

  });
}
var globalIntake;


function NewGetIntakeRequeestFileds(key, tid) {

  $.each(LookUpResponses, function (index, value) {

    $.each(value, function (i, k) {

      if (i == key) {

        ks = k;
        newKes = k;
        $('#int' + tid).text(ks);
        $('#int' + tid).attr('dataid', newKes);
        $('#' + tid).val(newKes);
      }
    })

  });
}


/*End lookup field get By Parameter Intake */

/*lookup field get By Parameter Name */
function GetCaregiverRequeestFileds(key, uid, tid) {
  globalIntake = key;
  NewGetIntakeRequeestFileds(globalIntake, tid);
  var urls = baseUrl + "/api/v1/caregiverFieldsResponse";
  /*$.ajax({
       url:urls,
       type:"GET",
       data:{'fields':key,'user_id':uid},
       success:function(response){  
	
     $('#int'+tid).text(response);
     $('#int'+tid).attr('dataid',response);
     $('#'+tid).val(response);
         
       }
   });*/

}

function GetStaffRequeestFileds(key, uid, tid) {
  globalIntake = key;
  NewGetIntakeRequeestFileds(globalIntake, tid);
}

/*End lookup field get By Parameter Intake */

/*delete FIelds */
function getDeleteFields(id, vals) {
  var tempread = [];
  var total = 1;
  $.each(vishaldata, function (index, val) {

    if (val.id == id) {
      val.Obj = '';
      $('#' + id).remove();
      $('#' + vals).remove();

    }
    if (val.id != id) {
      tempread.push(val);
      total++;
    }
  });
  elemtcount = total;
  vishaldata = tempread;

}

function getType(id, key, selectedValue) {

  var colors = ['red', 'blue', 'gree', 'yellow', 'orange', 'blue', 'gree', 'yellow', 'orange'];
  $.ajax({

    url: baseUrl + "/getTypeByTemplate",
    type: "GET",
    data: { 'id': document_key, "_token": tokens, "selected": selectedValue },
    success: function (response) {
      $('#textss' + id).html(" ");
      if (key == 'sign') {
        $('#signer_signatures' + id).html(" ");
        $('#signer_signatures' + id).append(response);
      }
      if (key == 'initial') {
        $('#signer_initail' + id).html(" ");
        $('#signer_initail' + id).append(response);
      }
      if (key == 'text') {
        $('#textss' + id).html(" ");
        $('#textss' + id).append(response);
      }
      if (key == 'checkboxs') {
        $('#signer_checkbox' + id).html(" ");
        $('#signer_checkbox' + id).append(response);
      }

      if (key == 'dropdowsns') {
        $('#signer_dropdown' + id).html(" ");
        $('#signer_dropdown' + id).append(response);
      }
      if (key == 'radios') {
        $('#signer_radio' + id).html(" ");
        $('#signer_radio' + id).append(response);
      }
      if (key == 'caregiver') {
        $('#caregiverDrop' + id).html(" ");
        $('#caregiverDrop' + id).append(response);
      }

    }
  })

}
function setSigner(elementId, selectedElementId, key, normalid) {
  var element = $('#' + elementId);

  if (key == 'checkbox') {
    var color = $('option:selected', element).attr('data-style');

    $('input[group="multiplecheck' + normalid + '"]').css("background-color", color);


    var color = $('option:selected', element).attr('data-style');


    $('input[group="multiplecheck' + normalid + '"]').attr("signer_id", element.val());
    $('input[group="multiplecheck' + normalid + '"]').attr("background_color", color);

  } else if (key == 'radios') {
    var color = $('option:selected', element).attr('data-style');
    $('input[group="multipleradio' + normalid + '"]').css("background-color", color);


    var color = $('option:selected', element).attr('data-style');


    $('input[group="multipleradio' + normalid + '"]').attr("signer_id", element.val());
    $('input[group="multipleradio' + normalid + '"]').attr("background_color", color);
  } else {


    var color = $('option:selected', element).attr('data-style');

    $('#' + selectedElementId).css("background-color", color);

    $('#' + selectedElementId).attr("signer_id", element.val());
    $('#' + selectedElementId).attr("background_color", color);
  }

  getGenerateArray(key, normalid);

}
function formsCard() {
  $('.page-container').each(function () {
    let pageTotalCount = 0;
    let pageFilledCount = 0;

    const pageContainer = $(this);
    const fieldsContainer = pageContainer.find('.fields-container');

    fieldsContainer.find('.forms-card').each(function () {
      const dataId = $(this).data('id');
      const dataName = $(this).data('name');
      const dataType = $(this).data('type');

      const inputField = $(`#${dataId}`);
      const inputFieldValue = $(`input[name='${dataName}']`);

      $(`[data-id="${dataId}"]`).addClass('hide');

      if (inputField.length > 0) {
        pageTotalCount++;
        const statusSpan = $(this).find('.status');
        const formsInfo = $(this).find('.forms-info');
        const statusIcon = $(this).find('.status-icon');
        const isFilled = $(this).data('filled');

        $(`[data-id="${dataId}"]`).removeClass('hide');

        function updateStatus() {
          if (isFilled == 1) {
            pageFilledCount++;
            statusSpan.text('Filled');
            statusSpan.css('color', '#C7C7C7');
            formsInfo.addClass('filled').removeClass('not-filled');
            statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                          <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                      </svg>
                  `);
          } else if (isFilled == 0 || isFilled == "") {
            if (dataType == "text") {
              const inputValue = inputField.val().trim();
              if (inputValue.length > 0) {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                              <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                          </svg>
                      `);
              } else {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                      <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                      <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                      </svg>
                      `);
              }
            } else if (dataType == "radio") {
              const radios = inputFieldValue;
              let checkedValue = null;

              radios.each(function () {
                if ($(this).prop('checked')) {
                  checkedValue = $(this).val();
                }
              });

              if (checkedValue) {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                          <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                      </svg>
                  `);
              } else {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                          <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                          <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                      </svg>
                  `);
              }

              $(document).on('change', inputFieldValue, function () {
                checkedValue = null;

                radios.each(function () {
                  if ($(this).prop('checked')) {
                    checkedValue = $(this).val();
                  }
                });

                if (checkedValue) {
                  pageFilledCount++;
                  statusSpan.text('Filled');
                  statusSpan.css('color', '#C7C7C7');
                  formsInfo.addClass('filled').removeClass('not-filled');
                  statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                              <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                          </svg>
                      `);
                } else {
                  statusSpan.text('Not Filled');
                  statusSpan.css('color', '#C7C7C7');
                  formsInfo.addClass('not-filled').removeClass('filled');
                  statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                              <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                              <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                          </svg>
                      `);
                }
              });
            } else if (dataType == "checkbox") {
              const checkboxes = inputFieldValue;
              let checkedValues = [];

              checkboxes.each(function () {
                if ($(this).prop('checked')) {
                  checkedValues.push($(this).val());
                }
              });

              if (checkedValues.length > 0) {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                          <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                      </svg>
                  `);
              } else {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                          <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                          <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                      </svg>
                  `);
              }

              $(document).on('change', inputFieldValue, function () {
                checkedValues = [];

                checkboxes.each(function () {
                  if ($(this).prop('checked')) {
                    checkedValues.push($(this).val());
                  }
                });

                if (checkedValues.length > 0) {
                  pageFilledCount++;
                  statusSpan.text('Filled');
                  statusSpan.css('color', '#C7C7C7');
                  formsInfo.addClass('filled').removeClass('not-filled');
                  statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                              <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                          </svg>
                      `);
                } else {
                  statusSpan.text('Not Filled');
                  statusSpan.css('color', '#C7C7C7');
                  formsInfo.addClass('not-filled').removeClass('filled');
                  statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                              <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                              <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                          </svg>
                      `);
                }
              });
            } else if (dataType == "image") {
              const divElement = inputField;
              const imgElement = divElement.find('img');
              const imgSrc = imgElement.attr('src');

              var imgSvg = 'data:image/svg+xml,%3Csvg%20width%3D%2218%22%20height%3D%2217%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22%20d%3D%22M18.7929%201.29289C19.1834%200.902369%2019.8166%200.902369%2020.2071%201.29289L22.7071%203.79289C23.0976%204.18342%2023.0976%204.81658%2022.7071%205.20711L15.2071%2012.7071C15.0196%2012.8946%2014.7652%2013%2014.5%2013H12C11.4477%2013%2011%2012.5523%2011%2012V9.5C11%209.23478%2011.1054%208.98043%2011.2929%208.79289L18.7929%201.29289ZM13%209.91421V11H14.0858L20.5858%204.5L19.5%203.41421L13%209.91421ZM1%2014.5C1%2012.567%202.567%2011%204.5%2011H8C8.55228%2011%209%2011.4477%209%2012C9%2012.5523%208.55228%2013%208%2013H4.5C3.67158%2013%203%2013.6716%203%2014.5C3%2015.3284%203.67158%2016%204.5%2016H19.5C21.433%2016%2023%2017.567%2023%2019.5C23%2021.433%2021.433%2023%2019.5%2023H9C8.44772%2023%208%2022.5523%208%2022C8%2021.4477%208.44772%2021%209%2021H19.5C20.3284%2021%2021%2020.3284%2021%2019.5C21%2018.6716%2020.3284%2018%2019.5%2018H4.5C2.567%2018%201%2016.433%201%2014.5Z%22%20fill%3D%22%23000000%22%3E%3C/path%3E%3C/svg%3E';

              if (imgSrc == imgSvg) {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                           <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                        <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                        <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                        </svg>
                      `);
              } else {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                        <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                            <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                        </svg>
                    `);
              }
            } else if (dataType == "dropdown") {
              const divElement = inputField;
              const selectElement = divElement.find('select');
              const selectedValue = selectElement.val();

              if (!selectedValue || selectedValue === "") {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                          <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                          <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                      </svg>
                  `);
              } else {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                          <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                      </svg>
                  `);
              }
            } else {
              const inputValue = inputField.val().trim();
              if (inputValue.length > 0) {
                pageFilledCount++;
                statusSpan.text('Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('filled').removeClass('not-filled');
                statusIcon.html(`
                          <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="7" cy="7" r="6.4" fill="#4CD964" stroke="#4CD964" stroke-width="1.2"></circle>
                              <path d="M4 7.1L6.0625 8.9L10 5" stroke="white"></path>
                          </svg>
                      `);
              } else {
                statusSpan.text('Not Filled');
                statusSpan.css('color', '#C7C7C7');
                formsInfo.addClass('not-filled').removeClass('filled');
                statusIcon.html(`
                      <svg class="ml-1 filled-icon filled" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="7" cy="7" r="6.4" fill="#FF3B30" stroke="#FF3B30" stroke-width="1.2"></circle>
                      <path d="M4 4L10 10" stroke="white" stroke-width="1.2"></path>
                      <path d="M4 10L10 4" stroke="white" stroke-width="1.2"></path>
                      </svg>
                      `);
              }
            }
          }
        }

        updateStatus();

        inputField.on('keyup click change', function () {
          pageFilledCount++;
          updateStatus();
        });

        $(this).on('click', function () {
          const imgElement = inputField.find('img');
          const selectElement = inputField.find('select');

          if (imgElement.length) {
              imgElement.attr('tabindex', '-1');
          }
          const scrollTo = Math.min(
              inputField.offset().top || Infinity,
              inputFieldValue.offset().top || Infinity,
              imgElement.offset()?.top || Infinity,
              selectElement.offset()?.top || Infinity
            );
    

          $('html, body').animate({
            scrollTop: scrollTo - 100
          }, 500);
      
          if (inputField.length) inputField.focus();
          if (inputFieldValue.length) inputFieldValue.focus();
          if (imgElement.length) imgElement.focus();
          if (selectElement.length) selectElement.focus();
        });
      }
    });

    pageContainer.find('.filled-count').text(pageFilledCount);
    pageContainer.find('.total-count').text(pageTotalCount);
  });
}

// Text limit validation for patient eSign fields

  function trimToMaxChars(textarea) {
  
    if (typeof isPatient !== 'undefined' && isPatient === true) {
      var $textarea = $(textarea);
      var val = $textarea.val();
      if (val.length > textLimitForPatient) {
          var trimmed = val.substring(0, textLimitForPatient); // correct for characters
          $textarea.val(trimmed);
          showCharLimitWarning();
      }
    }
  }

  function showCharLimitWarning() {
      toastr.error('You have exceeded the character limit. Only '+textLimitForPatient+' characters are allowed. Additional text will not be saved.');
  }

$(document).on('blur', '.writing-pad1', function () {
    trimToMaxChars(this);
});