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
 var dat=d.getDate();
 var mon=d.getMonth() +1;
 var year=d.getFullYear();
 var DateSingDate = mon+"/"+dat+"/"+year;
     
 var urls = $('.siteURL').val();
 var dropArray = [];
 var dropdownResponse= [];
 var final_array = [];
 var ConditionalTempArray=[];
 var ConditionalSTempArray=[];
 var RadiosArray =[];
 var headers =[];
 var TextArray =[];
 
 //Testing 
 
 
$(".close-editor-overlay").click(function(){
 if ($('.signer-assembler action').length > 0) {
   notify("Discard Changes?", "Your changes will be lost.", "warning", "Discard Changes", { showCancelButton: true, closeOnConfirm: true, callback: "closeEditor()" });
 }else{
   closeEditor();
  
 }
});
 
/*
* function to close editor overlay
*/
function closeEditor(){ 
 $('.signer-assembler').empty();
 renderPage(pageNum);
   $(".signer-document").appendTo(".document");
   $("body").removeClass("editor");
 
 emptyBuilder();
 location.reload();
};


/*
* launch editor overlay
*/
$(".launch-editor").click(function(){  
 inviting = false;
 enableTools();
   launchEditor();
 GetLoadComponents();
});
/*Docusign for mobile app */

function getLuncha(){
     inviting = false;
 
     launchEditor();

 }
 
/*
* function to launch editor overlay
*/
function launchEditor(){
   
 if (inviting) {$(".signer-save span").text("Send"); }else{  $(".signer-save span").text("Save"); }
     $(".signer-document").appendTo(".signer-overlay-previewer");
     $("body").addClass("editor skin-blue sidebar-mini");
     $('#document-viewer').attr('width','1000');
 
     renderPage(pageNum);
 };

/*
* Copy to clipboard
*/
var clipboard = new Clipboard('.copy-link');
clipboard.on('success', function(e) {
   $('#sharefile').modal('hide');
       toastr.success("Link copied to clipboard.", "Copied!");
});
clipboard.on('error', function(e) {
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
$("input[name=send-select]").change(function(){
 var email = $(this).val();
 if ($(this).prop("checked")) {
   $('input[name=receivers]').tagsinput('add', email);
 }else{
   $('input[name=receivers]').tagsinput('remove', email);
 }
});

/*
* Select users to send request
*/
$("input[name=request-select]").change(function(){
 var email = $(this).val();
 if ($(this).prop("checked")) {
   $('input[name=recipients]').tagsinput('add', email);
 }else{
   $('input[name=recipients]').tagsinput('remove', email);
 }
});


/*
* Before an email is added to form
*/
$('input[name=receivers], input[name=recipients]').on('beforeItemAdd', function(event) {
 if (!isEmail(event.item)) {
   event.cancel = true;
   toastr.error("Enter a valid email address.","Oops!");
 }
});


/*
* After an email is added
*/
$('input[name=recipients]').on('itemAdded', function(event) {
requestOptions();
});

/*
* After an email is added
*/
$('input[name=recipients]').on('itemRemoved', function(event) {
requestOptions();
});

/*
* When request oprions are updated
*/
$("input[name=restricted], input[name=duplicate]").change(function(){
 requestOptions();
});


/*
* Signing request options
*/
function requestOptions(){ 
 recipients = $("input[name=recipients]").tagsinput('items');
 if (recipients.length > 1) {
   $(".duplicate-request").show();
   $(".restricted-request").hide();
   if ($("input[name=duplicate]").prop("checked")) {
     $(".restricted-request").show();
   }else{
     if ($("input[name=restricted]").prop("checked")) {
       $("input[name=restricted]").click();
     }
   }
 }else{
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
function validateRequest(){ 
 if ($("input[name=restricted]").prop("checked")) {
   $("#sendRequest").modal("hide");
   inviting = true;
   launchEditor();
   enableTools("request");
 }else{
   sendRequest();
 }
}

/*
* send request
*/
function sendRequest(){  
 var emails = JSON.stringify($("input[name=recipients]").tagsinput('items')),
       message = $("textarea[name=requestmessage]").val(),
       duplicate = "No", positions = docWidth = '';
 if ($("input[name=duplicate]").prop("checked")) { duplicate = "Yes"; }
 if (isTemplate === "Yes" && templateFields !== '') { 
   positions = JSON.stringify(templateFields); 
   docWidth = "set";
 }else if($("input[name=restricted]").prop("checked")){
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
         "_token":tokens
     },
     loader: true
 });
}

/*
* Set document password toggle
*/
$(".password-protect-toggle").change(function(){ 
 if ($(this).prop("checked")) {
   $('.protection-password').show();
   $('.protection-password').find("input").attr("required", true);
 }else{
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
$(".right-bar-toggle").click(function(event){
event.preventDefault(); 
$(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
$(".right-bar."+$(this).attr("bar")).toggleClass("open");
$(".right-bar-toggle").find("span").hide();
});

/*
* Close
*/
$(".close-right-bar").click(function(event){
event.preventDefault();
$(this).closest(".right-bar").removeClass("open");
});

$(function() {
var timeToAccelerate;
var clickedElement;
$(".arrow").on("mousedown", function() {
 clickedElement = $(this);
 updateValue(clickedElement);

 timeToAccelerate = setInterval(function() {
   updateValue(clickedElement);
 }, 150);
});
$(document).on("mouseup", function() {
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
 }else{
   updateTextSize(value);
 }
}
});

/*
* Post chat
*/ 
$(".new-message").keypress(function (e) { 
 if(e.which == 13) {
   $(".empty-chat").remove();
   var message = $(this).val(), avatar = $(".user-avatar").attr("src"), chatId = random();
   $(".chat-list").append("<div class='chat-message chat-message-sender'><img class='chat-image chat-image-default' src='"+avatar+"' />"+
   "<div class='chat-message-wrapper'><div class='chat-message-content'><p>"+message+"</p></div><div class='chat-details'>"+
   "<span class='chat-message-localization font-size-small chat-"+chatId+"'>Sending....</span></div></div></div>");
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
function chatResponse(sendTime, chatKey, chatId){ 
 $('.chat-list').find(".chat-"+chatKey).text(sendTime);
 $('.chat-list').find(".chat-"+chatKey).closest(".chat-message").attr("id", chatId);
}

/*
*  fetch chats 
*/
function getChats() { 
 var data =  {
           "lastChat": $('.chat-list').children().last().attr("id"),
           "document_key": document_key,
           "csrf-token": Cookies.get("CSRF-TOKEN")
       }
 var posting = $.post(getChatUrl, data);
 posting.done(function(data) {
   if (data != 'empty') {
     $(".chat-list").append(data);
     $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
     $(".empty-chat").remove();
     $('[data-toggle="tooltip"]').tooltip();
     if(!$(".right-bar").hasClass("open")){
       $(".right-bar").addClass("open");
     }
   }
 });
}


/*
*  check for new chats after 5seconds 
*/
if (getChatUrl !== '' ) { 
setInterval(function() {
 if($(".chat-list").length){
   getChats();
 }
}, 5000);
}


/*
*  Signer tools select
*/
$(".signer-tool").click(function(event){ 
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
 toastr.warning("Save rotation changes before editing document.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false});
 return false;
}
if (tool === "rotate") {
 if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
   toastr.warning("Save changes before rotating.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false})
 }else{
   rotatePage(pageNum);
 }
}else if(tool === "image"){
 $("#selectImage").modal({show: true, backdrop: 'static', keyboard: false});
}else if(tool === "delete"){
 deleteElement();
}else if(tool === "text"){
 enableTextMode();
}else if(tool === "font"){
 $(".right-bar.font-list").toggleClass("open");
}else if(tool === "symbol"){
 $(".right-bar.symbol-list").toggleClass("open");
}else if(tool === "shape"){
 $(".right-bar.shape-list").toggleClass("open");
}else if(tool === "fields"){
 if (auth) {
   $(".right-bar.fields-list").toggleClass("open");
 }else{
   loginRequired();
 }
}else if(tool === "input"){
 if (auth) {
   if (isTemplate === "Yes" || inviting) {
     $(".right-bar.input-fields-list").toggleClass("open");
   }else{
     notify("Template Only", "Inputs are added to templates only. Do you want to create a template copy of this file?", "warning", "Yes, Create", { showCancelButton: true, closeOnConfirm: true, callback: "createTemplate()" });
   }
 }else{
   loginRequired();
 }
}else if(tool === "color"){
 document.getElementById('color-picker').jscolor.show();
}else if(tool === "duplicate"){
 duplicateSelected();
}else if(tool === "signature"){
 enableSignatureMode();
}else if(tool === "draw"){
 enableDrawMode();
}else if(tool === "bold" || tool === "italic" || tool === "underline" || tool === "strikethrough" || tool === "alignright" || tool === "aligncenter" || tool === "alignleft"){
 styleText(tool);
}
});

/*
*  Rotate Page
*/

function objToString(obj, ndeep) { 
switch(typeof obj){
 case "string": return '"'+obj+'"';
 case "function": return obj.name || obj.toString();
 case "object":
   var indent = Array(ndeep||1).join('\t'), isArray = Array.isArray(obj);
   return ('{['[+isArray] + Object.keys(obj).map(function(key){
        return '\n\t' + indent +(isArray?'': key + ': ' )+ objToString(obj[key], (ndeep||1)+1);
      }).join(',') + '\n' + indent + '}]'[+isArray]).replace(/[\s\t\n]+(?=(?:[^\'"]*[\'"][^\'"]*[\'"])*[^\'"]*$)/g,'');
 default: return obj.toString();
}
}


function rotatePage(pageNum){
 var degree = parseInt(getActualRotation(pageNum) + 90);
 if (degree == 360 ) { degree = 0; }
 assemble({"type": "rotate", "page": pageNum, "degree": degree});
 renderPage(pageNum);
 $("#document-viewer").css("max-width", "100%");
}

/*
*  Get actual page rotation
*/
function getActualRotation(pageNumber){ 
 if($("action[type=rotate][page="+pageNumber+"]").length > 0){
   rotationDegree = parseInt($("action[type=rotate][page="+pageNumber+"]").attr("degree"));
 }else{
   rotationDegree = 0;
 }
return rotationDegree;
}

/*
*  Group completed actions
*/
function assemble(data, prepare){ 
console.log(data);
console.log("vishal");
 if (prepare === undefined) {
     send = true;
 }
 if (data.group === undefined) {
     data.group = "field";
 }
 if (data.type === "rotate") {
     if($("action[type=rotate][page="+data.page+"]").length > 0){
       if (data.degree == 0) {
         $("action[type=rotate][page="+data.page+"]").remove();
       }else{
         $("action[type=rotate][page="+data.page+"]").attr("degree", data.degree);
       }
     }else{
       $(".signer-assembler").append('<action type="rotate" group="'+data.group+'" page="'+data.page+'" degree="'+data.degree+'">');
     }
 }else if(data.type === "image" || data.type === "signature" || data.type === "symbol" || data.type === "shape"){
     if (data.group === "field") { 
         var testinf=data.tempArray;
         if(data.tempArray !=''){
             var new_array = data.tempArray;
         }else{
              var new_array ='';
         }
         var tttttt = '<action id="'+data.id+'" type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" image="'+data.image+'" value="'+new_array+'" datats="'+data.type+'" required="'+data.required+'" readOnly="'+data.readOnly+'" background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '">';
         $(".signer-assembler").append(tttttt);

     }else if (data.group === "input"){
     
       $(".signer-assembler").append('<action type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" required="'+data.required+'" readOnly="'+data.readOnly+'">');
     }
 }else if(data.type === "drawing"){
     $(".signer-assembler").append('<action type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" drawing="'+data.drawing+'" >');
 }else if(data.type === "text"){ 


     var testinf=data.tempArray;
     if(data.tempArray !=''){
         var new_array = data.tempArray;
     }else{
          var new_array ='';
     }
     var checklength = $('#'+data.id).length;
     var readOnly = '';
     if(data.readOnly !=''){
         readOnly = 'readOnly="' + data.readOnly + '"';
     }
     if (data.group === "field") {
           $(".signer-assembler").append('<action heightId="'+data.heightId+'" disabled="' + data.disabled + '" dataid="' + data.datats + '" id="' + data.id + '" placeHolder="' + data.palceHolder + '" type="text" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" value="' + data.text + '" datats="' + data.type + '" vishalpatel="' + data.required + '" '+readOnly+'  background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '" font="'+data.font+'" setvalue="'+data.setvalue+'" receiverid="'+data.receiverid+'" receiverid2="'+data.receiverid2+'" conditionarules="'+data.conditionarules+'" assignId="'+data.assignId+'" assignVal="'+data.assignValue+'" groupSmapleId="'+data.checkBoxsTemp+'" groupname="'+data.groupname+'">');
         
     }else if (data.group === "input"){ 
         
        $(".signer-assembler").append('<action heightId="'+data.heightId+'" disabled="' + data.disabled + '" dataid="' + data.datats + '" id="' + data.id + '"  placeHolder="' + data.palceHolder + '" type="text" group="' + data.group + '" page="' + data.page + '" xPos="' + data.xPos + '" yPos="' + data.yPos + '" width="' + data.width + '" height="' + data.height + '" text="' + data.text + '" bold="' + data.bold + '" italic="' + data.italic + '" font="' + data.font + '" fontsize="' + data.fontsize + '" fontfamily="' + data.fontfamily + '" underline="' + data.underline + '" strikethrough="' + data.strikethrough + '" color="' + data.color + '" align="' + data.align + '" value="' + data.text + '" font="'+data.font+'" datats="' + data.type + '" required="' + data.required + '" '+readOnly+'  background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '" setvalue="'+data.setvalue+'" receiverid="'+data.receiverid+'" receiverid2="'+data.receiverid2+'" conditionarules="'+data.conditionarules+'"assignId="'+data.assignId+'" assignVal="'+data.assignValue+'" groupSmapleId="'+data.checkBoxsTemp+'" groupname="'+data.groupname+'">');
     }
 }else if(data.type ==='checkbox'){

     var testinf=data.tempArray;
        if(data.tempArray !=''){
             var new_array = data.tempArray;
         }else{
           var new_array ='';
         }
         
         var readOnly = '';
         
         if(data.readOnly == true){
             readOnly = 'readOnly="' + data.readOnly + '"';
         }

     $(".signer-assembler").append('<action name="'+data.name+'" id="'+data.id+'" type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.InputId+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'" value="'+data.value+'" datats="'+data.type+'" vishalpatel="'+data.required+'" '+readOnly+' background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '"  datas_keys="'+data.datas_keys+'" groups_checkbox="'+data.groups_checkbox+'" groupSmapleId="'+data.checkBoxsTemp+'" groupname="'+data.groupname+'">');
}
else if(data.type ==='radio'){

 var testinf=data.tempArray;
    if(data.tempArray !=''){
  var new_array = data.tempArray;
}else{
   var new_array ='';
}
 var readOnly = '';
     if(data.readOnly !=false){
         readOnly = 'readOnly="' + data.readOnly + '"';
     }

$(".signer-assembler").append('<action name="'+data.name+'" id="'+data.id+'" type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.InputId+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'" value="'+data.value+'" datats="'+data.type+'" vishalpatel="'+data.required+'" '+readOnly+' background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '" datas_keys="'+data.datas_keys+'" groups_checkbox="'+data.groups_checkbox+'" groupSmapleId="'+data.checkBoxsTemp+'" groupname="'+data.groupname+'">');
}
else if(data.type ==='fields'){
  
    var testinf=data.tempArray;
     if(data.tempArray !=''){
         var new_array = data.tempArray;
     }else{
          var new_array ='';
     }
     

   $(".signer-assembler").append('<action id="'+data.id+'" type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.bold+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'" value="'+new_array+'" datats="'+data.type+'" required="'+data.required+'" readOnly="'+data.readOnly+'">');
 }else if(data.type ==='dropdown'){
     var drops ={"id":data.id,"temp":data.tempArray};
      
     var  element = Object.assign({},drops);
     dropArray.push(element);
     
     var dropsVal ={"id":data.id,"temps":data.addmoreArray};
     var  elements = Object.assign({},dropsVal);
     dropdownResponse.push(elements);
     var readonly = '';
     
     if(data.readOnly !=''){
             readonly = 'readOnly="' + data.readOnly + '"';
         }
     
     $(".signer-assembler").append('<action id="'+data.id+'" type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.bold+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'" datats="'+data.type+'" required="'+data.required+'" '+readonly+' background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '" background_color="' + data.background_color+ '"  signer_id="' + data.signer_id+ '" vishalpatel="'+data.required+'" groupSmapleId="'+data.checkBoxsTemp+'" groupname="'+data.groupname+'">');
 }
 if (prepare) { 
     prepareData();
 }
}

/*
*  Signer Save click
*/
$(".signer-save").click(function(event){

event.preventDefault();
var i=0;
$('.signer-builder .signer-element').each(function(index, value) {
   
     if( $(this).children().attr('datakey')==undefined  && $(this).children().attr('disabled')!="disabled"){
         if($(this).children().attr('signer_id') == undefined || $(this).children().attr('signer_id') == "null"){
             
             i++;
         }
     }
});
//alert(i);
if(i===0){
if (inviting) {
 sendRequest();
}else if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
  
 orgnizeData();
}else if ($('.signer-assembler action').length) {
 
 prepareData();
}else{
 toastr.warning("No changes to save.","Hmm!");
 return false;
}
}else{
   if(validationId ==1){
     toastr.warning("Some element are missing for select signer. ","Hmm!");
     return false;
   }
}
});




/*
*  On font/stroke size change
*/
$(".font-size").change(function(){
size = parseInt($(this).val());
if (isDrawMode()) {
 modules.stroke(size);
}else{
 updateTextSize(size);
}
});

/*
*  Font preview on mouseover
*/
$(".font-item").mouseover(function(){
if ($(".signer-element.selected-element[type=text]").length) {
 elem = $(".signer-element.selected-element[type=text]");
}else{
 elem = $(".signer-element[type=text]");
}
elem.find(".writing-pad").css("font-family", $(this).attr("family"));
});

/*
*  Exit font preview
*/
$(".font-item").mouseleave(function(){
if ($(".signer-element.selected-element[type=text]").length) {
 elem = $(".signer-element.selected-element[type=text]");
}else{
 elem = $(".signer-element[type=text]");
}
elem.each(function(){
 if(elem.attr("font") === undefined){
   elem.find(".writing-pad").css("font-family", "'Lato', sans-serif");
 }else{
   elem.find(".writing-pad").css("font-family", $(".font-item[font="+elem.attr("font")+"]").attr("family"));
 }
})
});

/*
*  Update font of text
*/
$(".font-item").click(function(){
if ($(".signer-element.selected-element[type=text]").length) {
 elem = $(".signer-element.selected-element[type=text]");
}else{
 elem = $(".signer-element[type=text]");
}
elem.attr("font", $(this).attr("font"));
elem.find(".writing-pad").css("font-family", $(this).attr("family"));
highlightSelectedFont($(this).attr("font"));
});

/*
*  select an element
*/
$(".signer-builder").on("click", ".signer-element", function(){
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
}else if ($(this).attr("type") === "signature") {
 if ($(this).attr("group") === "field") {
   if (!auth) {
     if(sessionStorage.getItem('signature') === null) {
       $("#updateSignature").modal({show: true, backdrop: 'static', keyboard: false});
     }else{
       $(this).attr("signed", "true")
       $(this).find("img").attr("src", sessionStorage.getItem('signature'));
     }
   }else if (signature !== '') {
     $(this).attr("signed", "true")
     $(this).find("img").attr("src", signature);
   }else{
       notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+settingsPage+"')" });
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
function orgnizeData(prepare){ 

stopOrganizing = false;

if (prepare === undefined) { prepare = true; }
 if (modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
   assemble({"type": "drawing", "page": pageNum, "drawing": $('#document-viewer').getCanvasImage("image/png") }, false);
 }
 $('.signer-builder .signer-element').each(function(index, value) {
     
     var signerElement = $(this), actionType = signerElement.attr('type'), thisImage;
     signerElement.show();
     viewerPosition = $("#document-viewer").offset();
     group = signerElement.attr('group');
     pageNumber = parseInt(signerElement.attr('page'));

     if (actionType === "image" || actionType === "signature" || actionType === "symbol" || actionType === "shape") { 
   
         if (group === "field") {
           if (signerElement.attr("signed") === "false") {
             emptyAssembler();
             renderPage(pageNumber);
             signerElement.addClass("selected-element");
             notify("Hmm!", "A signature is required on page "+pageNumber+". Please sign to continue.", "info", "Sign Now");
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
           textHolder = signerElement.find(".img_wrap");
         }else{
           thisImage = signerElement.find("img").attr('src');
           elementWidth = signerElement.find("img").width();
           elementHeight = signerElement.find("img").height();
           elementPosition = signerElement.find("img").offset();
             textHolder = signerElement.find(".img_wrap");
         }
         elementPosition ={left:signerElement[0].style.left.replace("px",""),top:signerElement[0].style.top.replace("px","")}
         
         elementXpos = elementPosition.left - 346;
         elementYpos = elementPosition.top - 176;
         assemble({"id":signerElement.attr('id'),"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "image": thisImage ,"tempArray":sessionArrays,"background_color":$(textHolder[0]).attr("background_color"),"signer_id":$(textHolder[0]).attr("signer_id")}, false);
     
     }else if(actionType === "text"){ 

         underline = italic = bold = strikethrough = align = fontfamily = '';
         fontsize = 14;
         font = "lato";
         textHolder = signerElement.find(".writing-pad1");
 
             elementWidth = textHolder[0].style.width.replace("px","");
             elementHeight =textHolder[0].style.height.replace("px","");

         
         var  elementPosition = signerElement.offset();
         elementPosition ={left:signerElement[0].style.left.replace("px",""),top:signerElement[0].style.top.replace("px","")}
         
         //elementXpos = elementPosition.left - viewerPosition.left;
         elementXpos = elementPosition.left - 346;
         
         //elementYpos =elementPosition.top - viewerPosition.top;
         elementYpos =elementPosition.top - 176;
     
     
         palceHolder = textHolder[0].placeholder;
         if(palceHolder=="Date Signed"){
             elementXpos=elementXpos;
             elementYpos=elementYpos; 
             
         }
         if (group === "field") {
           userInput = textHolder.text();
           if (!userInput.replace(/\s/g, '').length) {
             emptyAssembler();
             renderPage(pageNumber);
             signerElement.addClass("selected-element");
             notify("Hmm!", "An input on page "+pageNumber+" is empty. Please fill to continue.", "info", "Fill Now");
             stopOrganizing = true;
             return false
           }
         }
     
         color="";
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
         }else{
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
         disabled='';
 
         if(textHolder[0].disabled ===true){
           disabled = 'disabled';
         }
         texts = signerEscape(textBody);
         readOnly='';
         if(textHolder[0].readOnly == true){
                 readOnly =textHolder[0].readOnly;
         }

         assemble({"id": signerElement.attr('id'), 'disabled': disabled, "datats": textHolder[0].title, "required": textHolder[0].required, "readOnly": readOnly, "group": group, "palceHolder": palceHolder, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": texts, "align": align, "bold": bold, "italic": italic, "font": font, "fontsize": fontsize, "fontfamily": fontfamily, "underline": underline, "color": color, "strikethrough": strikethrough, "tempArray": sessionArrays,"background_color":$(textHolder[0]).attr("background_color"),"signer_id":$(textHolder[0]).attr("signer_id"),"font":$(textHolder[0]).attr("font"),'assignId':$(textHolder[0]).attr("assignid"),'assignValue':$(textHolder[0]).attr("assignVal"),'receiverid':$(textHolder[0]).attr("receiverid"),'receiverid2':$(textHolder[0]).attr("receiverid2"),'heightId':$(textHolder[0]).attr("heightid"),'conditionarules':$(textHolder[0]).attr("conditionarules"),'setvalue':$(textHolder[0]).attr("setvalue"),"checkBoxsTemp":signerElement.attr('tempid'),'groupname':$(textHolder[0]).attr("groupsName")}, false);
     }else if(actionType === "checkbox"){ 
         var checkHolder = signerElement.find(".checkbox_wrapper").offset();

         checkHolder ={left:signerElement[0].style.left.replace("px",""),top:signerElement[0].style.top.replace("px","")}
         

         elementWidth = signerElement.find(".checkbox_wrapper").width();
         elementHeight = signerElement.find(".checkbox_wrapper").height();
         

         //elementXpos = checkHolder.left - viewerPosition.left;
         //elementYpos = checkHolder.top - viewerPosition.top;
         
         elementXpos = checkHolder.left - 346;
         elementYpos = checkHolder.top - 176;
         
         
         var eid = signerElement.find('.checkbox_wrapper');
         
         if(eid[0].checked ==true){
           checkedval =1;
         }else{
           checkedval =0;
         }
         readOnly='';
         
     
         if(eid[0].readOnly ==true){
             readOnly =eid[0].readOnly;
         }
     
         assemble({"id":signerElement.attr('id'),"name":eid[0].name,"required":eid[0].required,"readOnly":readOnly,"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType,"value":checkedval,"InputId":eid[0].id,"background_color":$(eid[0]).attr("background_color"),"signer_id":$(eid[0]).attr("signer_id"),'datas_keys':$(eid[0]).attr("data-keys"),"group":$(eid[0]).attr("group"),"checkBoxsTemp":signerElement.attr('tempid'),'groupname':$(eid[0]).attr("groupsName")  }, false);
     
     }else if(actionType === "radio"){
         var checkHolder = signerElement.find(".radio_wrap").offset();
         checkHolder ={left:signerElement[0].style.left.replace("px",""),top:signerElement[0].style.top.replace("px","")}
         
         elementWidth = signerElement.find(".radio_wrap").width();
         elementHeight = signerElement.find(".radio_wrap").height();
          
         //elementXpos = checkHolder.left - viewerPosition.left+0.5;
         //elementYpos = checkHolder.top - viewerPosition.top+0.5;
          //elementYpos=elementYpos-4; //Because we set margin of 4px in design View
         elementXpos = checkHolder.left - 346;
         elementYpos = checkHolder.top - 176;
         
         var eid = signerElement.find('.radio_wrap');
          
         if(eid[0].checked ==true){
             checkedRadioval =1;
         }else{
            checkedRadioval =0; 
         }
          
         
         assemble({"id":signerElement.attr('id'),"required":eid[0].required,"readOnly":eid[0].readOnly,"InputId":eid[0].id,"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType,"value":eid[0].value ,'name':eid[0].name,"background_color":$(eid[0]).attr("background_color"),"signer_id":$(eid[0]).attr("signer_id"),'datas_keys':$(eid[0]).attr("data-keys"),"group":$(eid[0]).attr("group"),"checkBoxsTemp":$(eid[0]).attr("tempids"),'groupname':$(eid[0]).attr("groupsName") }, false);
  
     }else if(actionType === "fields"){ 
         textHolders = signerElement.find(".writing-pad");
         var checkHolder = signerElement.find(".writing-pad").offset();
         elementWidth = signerElement.find(".writing-pad").width();
         elementHeight = signerElement.find(".writing-pad").height();
     //	elementXpos = checkHolder.left - viewerPosition.left;
         //elementYpos = checkHolder.top - viewerPosition.top;
         
         elementXpos = checkHolder.left - 346;
         elementYpos = checkHolder.top - 176;
         
         var sessionArrays;
         assemble({"id":signerElement.attr('id'),"required":textHolders[0].required,"readOnly":textHolders[0].readOnly,"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType,"tempArray":sessionArrays }, false);
       
     }else if(actionType === "dropdown"){
         textHolders = signerElement.find(".drips");
         
         var checkHolder = signerElement.find(".drips").offset();
         checkHolder ={left:signerElement[0].style.left.replace("px",""),top:signerElement[0].style.top.replace("px","")}
         elementWidth = textHolders[0].style.width.replace("px","");
         elementHeight =textHolders[0].style.height.replace("px","");
         elementWidth = signerElement.find(".drips").width();
         elementHeight = signerElement.find(".drips").height();
      
         //elementXpos = checkHolder.left - viewerPosition.left;
         //elementYpos = checkHolder.top - viewerPosition.top;
         elementXpos = checkHolder.left - 346;
         elementYpos = checkHolder.top - 176;
         var sessionArrays;
         readOnly='';
         if(textHolders[0].readOnly ==true){
             readOnly =textHolders[0].readOnly;
         }
     
         assemble({"id":signerElement.attr('id'),"required":textHolders[0].required,"readOnly":readOnly,"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": actionType,"tempArray":textHolders[0].innerHTML,"background_color":$(textHolders[0]).attr("background_color"),"signer_id":$(textHolders[0]).attr("signer_id"),'addmoreArray':final_array,"checkBoxsTemp":$(textHolders[0]).attr("tempids"),'groupname':$(textHolders[0]).attr("groupsName") }, false);
     }

     if (pageNumber == pageNum) {
         signerElement.show();
     }else{
         signerElement.hide();
     }
 });
 if (stopOrganizing) {
   emptyAssembler();
   return false;
 }else{ 
  
   if (prepare) { prepareData(); }
 }
 
}

/*
*  Prepare data before sending to database
*/
function prepareData(save){

 if (save === undefined) { save = true; }
     var actions = [];
     var errors =[];
     var cnt =0;

 $('.signer-assembler action').each(function(index, value) {
 
     var ObjectArrays = [];
     $.each(dropArray,function(index, val){
       ObjectArrays[val.id] = val.temp;
       
     });
     
     var ObjectArray = [];
     $.each(vishaldata,function(index, values){
         ObjectArray[values.id] = values.Obj;
         
     });
     
     var ids = $(this).attr('id');
     names ='';
     var explode = ids.split('_');
     disabled='';
     if(explode[0] =='caregiver'){
         names = 'caregiver';
         disabled ='disabled';
     }else if(explode[0] =='staff'){
       names = 'staff';
       disabled ='disabled';
     }
     
     if($(this).attr('value') != "undefined"){
         textvalue = $(this).attr('value');
     }else{
         textvalue ='';
     }
     /*Start New added Logic permission*/
     var ConditionalRulesArray = [];
     var DependingRulesArray = [];
     
     $.each(headers,function(index, values){
         DependingRulesArray.push(values);
         
         if($(this).attr('type') =='checkbox'){
             
             ConditionalRulesArray[values.SenderDivId] = DependingRulesArray;
         }
         if($(this).attr('type') =='radio'){
             
             ConditionalRulesArray[values.SenderId] = DependingRulesArray;
             
         }
         if($(this).attr('type') =='text'){
             
             ConditionalRulesArray[values.SenderId] = DependingRulesArray;
         }
         if($(this).attr('type') =='dropdown'){
             
             ConditionalRulesArray[values.SenderId] = DependingRulesArray;
         }
         
     });
 
     
     /*end new added logic permission */
     var ArrayCreateById = [];
     $.each(final_array, function (i, value) {
         var temparray=ArrayCreateById[value.maId] ;
         if(!temparray){
             temparray=[];
         }
         temparray.push(value);
         ArrayCreateById[value.maId] = temparray;
     });
console.log($(this).attr('height'));

     actions.push({
         id:$(this).attr('id'),
         type: $(this).attr('type'),
         page: $(this).attr('page'),
         xPos: $(this).attr('xPos'),
         yPos: $(this).attr('yPos'),
         width: $(this).attr('width'),
         height: $(this).attr('height'),
         image: $(this).attr('image'),
         text: textvalue,
         checked:$(this).attr('value'),
         placeHolder:$(this).attr('placeHolder'),
         obj:ObjectArray[$(this).attr('id')],
         required:$(this).attr('vishalpatel'),
         readOnly:$(this).attr('readOnly'),
         bold:$(this).attr('bold'),
         temp1:names,
         temp2:disabled,
         temp3:$(this).attr('dataid'),
         name:$(this).attr('name'),
         background_color: $(this).attr('background_color'),
         signer_id: $(this).attr('signer_id'),
         temp4: ObjectArrays[$(this).attr('id')],
         addmore:$(this).attr('datas_keys'),
         groups_checkbox:$(this).attr('group'),
         drops_valeus : ArrayCreateById[$(this).attr('id')],
         font:$(this).attr('font'),
         assignId:$(this).attr('assignid'),
         assignVal:$(this).attr('assignval'),
         conditionaRules:ConditionalRulesArray[$(this).attr('id')],
         textsmall:$(this).attr('heightId'),
         receiverid:$(this).attr('receiverid'),
         receiverid2:$(this).attr('receiverid2'),
     
         setvalue:$(this).attr('setvalue'),
         groupSmapleId:$(this).attr('groupSmapleId'),
         groupNames:$(this).attr('groupname')			
     });	  
     
     
 });
     actions = JSON.stringify(actions);

     if (save) { saveChanges(actions); }else{ return actions; }
 
}

/*
*  send actions to server
*/
function saveChanges(actions){  

 swal({
           title: 'Are you sure?',
           text: "You won't be able to revert this!",
           type: 'warning',
           showCancelButton: true,
           confirmButtonColor: '#3085d6',
           cancelButtonColor: '#d33',
           confirmButtonText: 'Yes'
         },function (isConfirm) {
             if(isConfirm ==false){
                 $('.signer-assembler').html(" ");
                 return 
             }else{ 
             $.ajax({
             
                 url: signDocumentUrl,
                 type:"POST",
                 data: {"actions": actions,"_token":tokens,"docWidth": $("#document-viewer").width(),"document_key": document_key,"signing_key": signingKey,
                 },
                 success: function(response) { 
             
                 if(response ==1){
                     swal("Done!", "Template successfully updated.", "success");
                     setTimeout(function(){ window.location.href=baseUrl+"/document?id="+document_key; }, 2000);
                 }else{
                     swal("error!", "Error", "error");
                 }
                   
             }
         });
             }
     })
}

/*
*  escape string
*/
function signerEscape(string){
 
string = string.replace(/"/g, "%22");

return string;
}

/*
*  empty assembled data.
*/
function emptyAssembler(){
$(".signer-assembler").empty();
}

/*
*  empty builder data.
*/
function emptyBuilder(){
$(".signer-builder").empty();
}

/*
*  Duplicate selected element
*/
function duplicateSelected(){
 original = $('.signer-element.selected-element');
 duplicate = original.clone();
 duplicate.appendTo(".signer-builder");
 original.removeClass("selected-element")
 position = duplicate.position();
 if ($(window).width() < 1101) {
   topOffset = 225;
 }else{
   topOffset = 185;
 }
 currentOffset = $(".signer-overlay-previewer").offset();
 yPos = parseInt(position.top - currentOffset.top + topOffset);
 duplicate.css({top: parseInt(yPos + 30)+'px', left: parseInt(position.left + 30)+'px'});
 initElementsDrag();
focusText();
}

/*
*  Select image to add on PDF
*/
function selectDocImage(){
showLoader()
var reader  = new FileReader();
reader.readAsDataURL(document.querySelector('input[name=document-selected-image]').files[0]);
imageWidth = parseInt($("#document-viewer").width() - 30)
reader.addEventListener("load", function () {
 $("#selectImage").modal("hide");
 hideLoader();
 $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" page="'+pageNum+'"><img src="'+reader.result+'" style="max-width:'+imageWidth+'px;opacity:0.5;"></div>').appendTo(".signer-builder");
 $( document ).mousemove(function( event ) {
   $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
 });
 disableTools();
 highlightCanvas();
}, false);
}

/*
*  Enable signature mode
*/
function enableSignatureMode(){
if (!auth) {
     if(sessionStorage.getItem('signature') === null) {
       $("#updateSignature").modal({show: true, backdrop: 'static', keyboard: false});
     }else{
         imageWidth = parseInt($("#document-viewer").width() - 30);
         $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="'+pageNum+'"><img src="'+sessionStorage.getItem('signature')+'" style="max-width:'+imageWidth+'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
         $( document ).mousemove(function( event ) {
           $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
         });
         disableTools();
         highlightCanvas();
     }
}else if (signature !== '') {
 imageWidth = parseInt($("#document-viewer").width() - 30);
 $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="'+pageNum+'"><img src="'+signature+'" style="max-width:'+imageWidth+'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
 $( document ).mousemove(function( event ) {
   $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
 });
 disableTools();
 highlightCanvas();
}else{
 notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+settingsPage+"')" });
}
}

/*
*  When symbol is selected
*/
$(".symbol-item").click(function(){
deselectElements();
$(".right-bar.symbol-list").toggleClass("open");
$('<div class="signer-element selected-element" status="drop" resizeable="true" color="'+selectedColor()+'" type="symbol" page="'+pageNum+'" style="width:40px;height:40px;">'+$(this).html()+'</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
$( document ).mousemove(function( event ) { 
 $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
});

/*
*  When custom field is selected
*/
$(".field-list").on("click", ".field-item div", function(event){
event.preventDefault();
deselectElements();
$(".right-bar.fields-list").toggleClass("open");
font = selectedFont();
$('<div class="signer-element selected-element" status="drop" type="text" page="'+pageNum+'" '+currentTextStyle()+' font="'+font.font+'" color="'+selectedColor()+'" font-size="'+selectedFontSize()+'" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+selectedColor()+';font-size:'+selectedFontSize()+'px;font-family:'+font.family+'"  spellcheck="false">'+$(this).text()+'</div></div>').appendTo(".signer-builder");
$( document ).mousemove(function( event ) {
 $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
});

/*
*  When custom field is selected
*/
$(".input-field-list").on("click", ".input-field-item div", function(event){
event.preventDefault();
deselectElements();
$(".right-bar.input-fields-list").toggleClass("open");
font = selectedFont();
fieldLabel = $(this).text();
if (fieldLabel == "Signature") { 
 $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" group="input" page="'+pageNum+'"><img src="'+baseUrl+'/assets/images/signhere.png" style="width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
}else{
 $('<div class="signer-element selected-element" status="drop" type="text" resizeable="free" group="input" page="'+pageNum+'" '+currentTextStyle()+' font="'+font.font+'" color="'+selectedColor()+'" font-size="'+selectedFontSize()+'" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+selectedColor()+';font-size:'+selectedFontSize()+'px;font-family:'+font.family+'"  spellcheck="false">'+$(this).text()+'</div></div>').appendTo(".signer-builder");
}
$( document ).mousemove(function( event ) {
 $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
});

/*
*  When custom field is deleted
*/
$(".field-list").on("click", "#delete-field", function(event){
event.preventDefault();
fieldId = $(this).closest(".field-item").attr("id");
$(this).closest(".field-item").remove();
deleteField(fieldId);
});

/*
*  When input field is deleted
*/
$(".input-field-list").on("click", "#delete-input-field", function(event){
event.preventDefault();
fieldId = $(this).closest(".input-field-item").attr("id");
$(this).closest(".input-field-item").remove();
deleteField(fieldId);
});

/*
*  Delete custom or input field
*/
function deleteField(fieldId){
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
$(".shape-item").click(function(){  
deselectElements();
$(".right-bar.shape-list").toggleClass("open");
$('<div class="signer-element selected-element" status="drop" resizeable="true" color="'+selectedColor()+'" type="shape" page="'+pageNum+'" style="width:100px;height100px;">'+$(this).html()+'</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
$( document ).mousemove(function( event ) {
 $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
});
var sign;
$('.signstatus').click(function(){
 
 sign = Math.floor(100000000 + Math.random() * 900000000) + elemtcount++;
 
 deselectElements();
 $('.signstatus').addClass('active');
 var check = $('.deselected').val();
     if(check !=''){
         $('.'+check).removeClass('active');
     }
 
 $('.deselected').val('signstatus');
   $('<div class="signer-element selected-element tttts" resizeable="true" status="drop" type="image" id="sign_'+sign+'" page="'+pageNum+'"><img src="'+urls+'/assets/images/new_favicon_01.png" class="img_wrap" id="signatures_signer_'+sign+'"></div>').appendTo(".signer-builder");
     $( document ).mousemove(function( event ) {
       $('#tempids').val(1);
       $('.prev').val('signature');
     $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
     });
disableTools();
highlightCanvas();
})


var totalDateSign;
$('.datesigned').click(function(){

 totalDateSign  = Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;
  deselectElements();
 
 $('.totalDateSign').addClass('active');
 var check = $('.deselected').val();
     if(check !=''){
         $('.'+check).removeClass('active');
     }
 
 $('.deselected').val('datesigned');
   $('<div class="signer-element " type="text" status="drop" page="'+pageNum+'" id="datesigned_'+totalDateSign+'"><textarea disabled  style="width:200px;height:30px;" type="text" placeHolder="Date Signed" id="datesigneds_'+totalDateSign+'"  value="'+DateSingDate+'"  class="writing-pad1 signeeddate"  title=""></textarea></div>').appendTo(".signer-builder");
   $( document ).mousemove(function( event ) {
       $('#tempids').val(1);
       $('.prev').val('datesigned');
 $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
})



var totalText;
$('.text').click(function(){
 
     totalText = Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;
 
  deselectElements();
     $('.text').addClass('active');
     var check = $('.deselected').val();
     if(check !=''){
         $('.'+check).removeClass('active');
     }
     $('.deselected').val('text');
     var classses='';
     if(pageNum ==8){
         classses = 'heightClass';
     }
    $('<div class="signer-element"  tempId="textgroup_'+totalText+'"  status="drop" page="'+pageNum+'" id="text_'+totalText+'" type="text"><textarea groupsName="textgroup'+totalText+'" tempIds="textBox_'+totalText+'" class="writing-pad1 '+classses+'" style="width:200px;height:20px;" placeHolder="Textbox" id="checks'+totalText+'" type="text" onclick="getLogicPermission(this.id)"></textarea></div>').appendTo(".signer-builder");
     $( document ).mousemove(function( event ) {
       $('#tempids').val(1);
         $('.prev').val('text');
 
         $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
     });
disableTools();
highlightCanvas();
})
var checkTotal;
var chkcount =1;
$('.checkboxs').click(function(){

    checkTotal= elemtcount++;;
    var checkTes = "'cbox"+checkTotal+"'";
     deselectElements();
    $('.checkboxs').addClass('active');
    var check = $('.deselected').val();
    if(check !=''){
      $('.'+check).removeClass('active');
    } 
    $('.deselected').val('checkboxs');
    $('<div class="signer-element" tempId="checkgroup_'+checkTotal+'" type="checkbox" page="'+pageNum+'" status="drop" id="checkboxs_'+checkTotal+'"><input type="checkbox" groupsName="cbox'+checkTotal+'" tempIds="checkgroup_'+checkTotal+'" class="checkbox_wrapper " name="cbox'+checkTotal+'" id="cid_'+checkTotal+chkcount+'" value="'+chkcount+'" group="multiplecheck'+checkTotal+'" data-keys="addmore">&nbsp;&nbsp;<a href="javascript:void(0)" onclick="getCheckAppend('+checkTotal+','+chkcount+','+checkTes+')"><i class="fa fa-plus"></i></a></div>').appendTo(".signer-builder");
    $( document ).mousemove(function( event ) {
      $('#tempids').val(1);
       $('.prev').val('checkboxs');
       $('.next').val(chkcount);
    $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
  });
  disableTools();
  highlightCanvas();
})
var drops;
$('.dropdowsns').click(function(){ 

drops= Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;

deselectElements();
$('.dropdowsns').addClass('active');
var check = $('.deselected').val();
if(check !=''){
  $('.'+check).removeClass('active');
}
$('.deselected').val('dropdowsns');
$('<div class="signer-element ui-resizable" tempId="Dropgroup_'+drops+'" resizable="textbox" type="dropdown" page="'+pageNum+'" status="drop" id="dropdowsns_'+drops+'"><select  groupsName="dropdowngroup'+drops+'" tempIds="dropdowngroup_'+drops+'" id="dropid'+drops+'" class="drips" style="width:50px;"><option>select</option></select></div>').appendTo(".signer-builder");
$( document ).mousemove(function( event ) {
  $('#tempids').val(1);
   $('.prev').val('dropdowsns');
$(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();
})
var radios;
var rcount =1;
$('.radios').click(function(){

var rcounts = $('.next').val();


radios= Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;

$('#totalRadio').val(radios);	
deselectElements();
$('.prev').val('radios');
$('.radios').addClass('active');
var check = $('.deselected').val();
if(check !=''){
  $('.'+check).removeClass('active');
}
$('.deselected').val('radios');

var tennn = "'radiogroup"+radios+"'";

$('<div class="signer-element selected-element  radiogroup'+radios+'"  tempId="radiogroup_'+radios+'"  type="radio" page="'+pageNum+'" status="drop" id="radios_'+radios+'"><input type="radio"  groupsName="radiogroup'+radios+'" tempIds="radiogroup_'+radios+'" style="color:red;" name="radiogroup'+radios+'" class="radio_wrap" id="radio_wrap_'+radios+""+rcount+'" value="Radio'+rcount+'" group="multipleradio'+radios+'" data-keys="addmore">&nbsp&nbsp<a href="javascript:void(0)" onclick="getAppend('+radios+','+rcount+','+tennn+')"><i class="fa fa-plus"></i></a></div>').appendTo(".signer-builder");
$( document ).mousemove(function( event ) {
  $('#tempids').val(1);
  $('.next').val(rcount);
  $('.prev').val('radios');
$(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
});
disableTools();
highlightCanvas();

})


var fields_caregiver;
$('.fields_caregiver').click(function(){ 
 
     fields_caregiver = Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;
 
  deselectElements();
     $('.fields_caregiver').addClass('active');
     var check = $('.deselected').val();
     if(check !=''){
         $('.'+check).removeClass('active');
     }
     $('.deselected').val('fields');
     
    $('<div class="signer-element" status="drop" tempId="caregivergroup_'+fields_caregiver+'" page="'+pageNum+'" id="caregiver_'+fields_caregiver+'" type="text"><textarea groupsName="caregiver'+fields_caregiver+'" tempIds="caregiver_'+fields_caregiver+'" disabled  style="width:200px;height:30px;" type="text" placeHolder="Caregiver Lookup field" id="caregivers_'+fields_caregiver+'"  value=""  class="writing-pad1" datakey="caregiver" title=""></textarea></div>').appendTo(".signer-builder");
     $( document ).mousemove(function( event ) {
       $('#tempids').val(1);
         $('.prev').val('fields');
 
         $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
     });
disableTools();
highlightCanvas();
})
var fields_staff;
$('.fields_staff').click(function(){ 
 
     fields_staff = Math.floor(100000000 + Math.random() * 900000000) +elemtcount++;
 
  deselectElements();
     $('.fields_staff').addClass('active');
     var check = $('.deselected').val();
     if(check !=''){
         $('.'+check).removeClass('active');
     }
     $('.deselected').val('staffs');
     
    $('<div class="signer-element" status="drop" tempId="staffgroup_'+fields_staff+'" page="'+pageNum+'" id="staff_'+fields_staff+'" type="text"><textarea groupsName="staff'+fields_staff+'" tempIds="staffs_'+fields_staff+'" disabled  style="width:200px;height:30px;" type="text" placeHolder="Applicant Lookup field" id="staffs_'+fields_staff+'"  value=""  class="writing-pad1" datakey="staff" title=""></textarea></div>').appendTo(".signer-builder");
     $( document ).mousemove(function( event ) {
       $('#tempids').val(1);
         $('.prev').val('staffs');
 
         $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
     });
disableTools();
highlightCanvas();
})





/*
*  Make elements draggable
*/

function initElementsDrag() { 

$(".signer-element").draggable({
 containment: $("#document-viewer"),
 drag: function() { 
   highlightCanvas();
 },
 stop: function() { 
   unHighlightCanvas();
 },
 
});
}


/*
*  Make elements resizeable
*/
function initElementsResize() { 
$(".signer-element[resizeable=true]").resizable({
   aspectRatio: true,
   autoHide: false,
   handles: "n, e, s, w, se, sw, nw, ne",
   resize: function(event, ui){
     ui.helper.find("img").width(ui.size.width - 10);
     ui.helper.find("img").height(11);
   }
 });
$(".signer-element[resizeable=free]").resizable({ 
   autoHide: false,
   handles: "n, e, s, w, se, sw, nw, ne",
   resize: function(event, ui){
     ui.helper.find(".writing-pad").width(ui.size.width - 10);
     ui.helper.find(".writing-pad").height(11);
   }
 });
 
 $(".signer-element[resizeable=textbox]").resizable({
   aspectRatio: true,
   autoHide: false,
   handles: "n, e, s, w, se, sw, nw, ne",
   resize: function(event, ui){ 
     ui.helper.find(".writing-pad").width(ui.size.width - 10);
     ui.helper.find(".writing-pad").height(11);
   }
 });
 getResponse();
}

 var data = [];
 var vishaldata = [];
      var count =1;
     var total = 0;
     if(counter !=0){
         var total = counter;
     }

     var elemtcount =1+total; 
     var groupname = '';
     
     //dragandrop 
     function getResponse(){
         var testingArray = [];
         var tempi = $('#tempids').val();
         var texts = $('.prev').val();
         
         
         
         
         var ResponseList;
         viewerPositions = $("#document-viewer").offset();
         $('#vishal123').empty();
         
         
         
         
         var html ="";
         var deleteid;
         if(texts =='signature' && sign !== undefined){
             var signname = '"signature"';
             var localid = 'sign_'+sign;
             groupname = localid;
             html ="<div class='row' id='" + sign + "'><div class='box box-solid'><div class='box-body'><div id='"+sign+"'><div class='form-group'><label>Signature</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"signatures_signer_" + sign + "\","+signname+","+sign+");' id='signer_signatures" + sign + "' class='form-control'></select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Signature "+sign+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label><input type='checkbox' class=''  name='signature"+sign+"' id='check_sign_"+sign+"' onclick='getGenerateArray("+signname+","+sign+");gerRequired(this.value,"+sign+","+signname+")' value='1' ></label>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' class='minimal'  name='signature_read"+sign+"' id='check_read_"+sign+"' onclick='getGenerateArray("+signname+","+sign+");gerRequired("+signname+","+sign+")' value='1' >Read Only</div></div></div></div>";	
             ActionType =$('#'+localid).attr('type'); 
             page =$('#'+localid).attr('page');
             elementPosition = $('#'+localid).find("img").offset();
             elementWidth = $('#'+localid).find("img").width();
             elementHeight = $('#'+localid).find("img").height();
             xposs = elementPosition.left - viewerPositions.left;
             yposs = elementPosition.top - viewerPositions.top;
             deleteid = $('#'+localid).attr('id');
             deleteids = sign;
             var did = "'"+deleteid+"'";
         
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
         }

        
        console.log(texts);
         if(texts =='text' && totalText !== undefined){
             
             var signname = '"text"';
             var localid = 'text_' + totalText;
             var setText = '"checks"';
             groupname = localid;
             html = "<div class='row' id='" + totalText + "'><div class='box box-solid'><div class='box-body'><div id='" + totalText + "'><div class='form-group'><i class='fa fa-text-width' aria-hidden='true'></i>&nbsp;&nbsp;<label>Text</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"checks" + totalText + "\","+signname+","+totalText+");' id='textss" + totalText + "' class='form-control'></select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Text "+totalText+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label><input type='checkbox' name='text_required' id='text_required_" + totalText + "' onchange='getGenerateArray(" + signname + "," + totalText + ")' onclick='gerRequired(this.value," + totalText + "," + signname + ")' ></label>Required Field</div></div><div class=''><div class='form-group'><label><input type='checkbox' name='text_read' id='text_read_" + totalText + "' onchange='getGenerateArray(" + signname + "," + totalText + ")'  onclick='gerReadOnly(this.value," + totalText + "," + signname + ")' value='1'></label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Add Text</label><div class=''><textarea class='textare" + totalText + "'onkeyup='getKeys(" + totalText + ")' onchange='getGenerateArray(" + signname + "," + totalText + ")'></textarea></div></div><hr></div><div class='col-md-12'><div class='form-group font-div'><label>Font</label><div class=''><input type='text' id='font"+totalText+"' class='form-control font-size-box' onkeyup='SetFontSize("+totalText+","+signname+")'></div></div><hr></div><div class='col-md-12'><div class='form-group font-div'><label>Minimum Width</label><div class=''><input type='checkbox' id='minwidth"+totalText+"' class='form-control'  onchange='getGenerateArray(" + signname + "," + totalText + ")' ></div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div class=''><button onclick='showHeaders("+signname+","+totalText+","+setText+")'> Create Rules</button></div></div><hr></div></div>";
             ActionType = $('#' + localid).attr('type');
             textHolder = $('#' + localid).find(".writing-pad1");
             elementWidth = $('#' + localid).find(".writing-pad1").width();
             elementHeight = $('#' + localid).find(".writing-pad1").height();
             elementPosition = $('#' + localid).find(".writing-pad1").offset();
             xposs = elementPosition.left - viewerPositions.left;
             yposs = elementPosition.top - viewerPositions.top;
             page = $('#' + localid).attr('page');
             deleteid = $('#'+localid).attr('id');
             deleteids = totalText;
             var did = "'"+deleteid+"'";
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
         }
         
         if(texts =='checkboxs' && checkTotal !== undefined){ 
             var signname = '"checkbox"'; 
            var localid = 'checkboxs_'+checkTotal;
            if(CtempStoreSelectedId!=""){
         
                 var localid = 'checkboxs_'+CtempStoreSelectedId;
                 
             }
             
             CtempStoreSelectedId="";
            groupname="cbox"+checkTotal;
             if(CheckGroupId!=""){
                 groupname="cbox"+CheckGroupId;
                 CheckGroupId="";
             }	
             
             
            /*new Added code login permission */
             var i=1;
             checkBoxYsnamic = '';
             var ctempid;
             var OldTextArray = []; 
             var OldCheckArray = [];
         
             var  inputetext = '';
             var lengths = finalarray.length;
             var  textsd = '';
             if(lengths > 0){
                 $.each(finalarray,function(index,valsd){ 
                   if(valsd.text != undefined){
                   inputetext = valsd.text;
                   }else{ 
                     inputetext = '';
                   }

                   
                   OldTextArray[valsd.id] = inputetext;
                   if(valsd.checked ==true){
                     chktrue = valsd.checked;
                   }else{
                     chktrue =false;
                   }

                   OldCheckArray[valsd.id]= chktrue;
                 });
             }
             $.each($("input[name='"+groupname+"']"), function() {
             
                 subid = "'cid_"+checkTotal+i+"'";
                 inputEmail ="'inputEmail"+checkTotal+i+"'";
             
                 if(lengths >0){  
               
                     if(OldTextArray['cid_'+checkTotal+i] == undefined){ 
                         textsd ='';
                     }else{
                         textsd =  OldTextArray['cid_'+checkTotal+i];
                     }

                     if(OldCheckArray['cid_'+checkTotal+i] !='' || OldCheckArray['cid_'+checkTotal+i] != undefined){ 
                         var check = OldCheckArray['cid_'+checkTotal+i];
                         if(check ==true){
                             check = 'checked="checked"';
                         }else{
                             check = '';
                         }
                     }
                 }

                 var  tmpName = "'checkbox'";
                 checkBoxYsnamic += '<div class="mycheck'+checkTotal+''+i+'"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" '+check+' id="dymic_'+checkTotal+""+i+'" onclick="getOnClick('+tmpName+','+subid+',this.id,'+inputEmail+','+checkTotal+')"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail'+checkTotal+i+'" value="'+textsd+'" placeholder="Checkbox value" onkeyup="getSetValue('+subid+',this.id,'+checkTotal+')"></div></div></div></div>';
                 ctempid = i;
                 i++;
                 
                 
             });
             var setText = '"cid_'+checkTotal+ctempid+'"';
           /*end New added logic permission*/
            html ="<div class='row' id='" + checkTotal + "'><div class='box box-solid'><div class='box-body'><div id='"+checkTotal+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Checkbox Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"" + groupname + "\","+signname+","+checkTotal+");' id='signer_" + groupname + "' class='form-control'></select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Checkbox "+checkTotal+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label><input type='checkbox' name='checkbox_required' id='checkbox_required_"+checkTotal+"' onchange='getGenerateArray("+signname+","+checkTotal+")' onclick='gerRequired(this.value,"+checkTotal+","+signname+")'></label>Required Field</div></div><div class=''><div class='form-group'><label><input type='checkbox' name='checkbox_read"+checkTotal+"' id='checkbox_read_"+checkTotal+"' onchange='getGenerateArray("+signname+","+checkTotal+")' onclick='gerReadOnly(this.value,"+checkTotal+","+signname+")'></label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Checkbox Value</label><div class='chkboxval"+checkTotal+"'>"+checkBoxYsnamic+"</div><hr></div></div><div class='col-md-12'><div class='form-group'><label>Conditional Rules</label></div><div class=''><button onclick='showHeaders("+signname+","+checkTotal+","+setText+")'>Create Rules</button></div><hr></div></div>";
            ActionType =$('#'+localid).attr('type');
            textHolder = $('#'+localid).find(".checkbox_wrapper");
            
            elementWidth = $('#'+localid).find(".checkbox_wrapper").width();
            elementHeight = $('#'+localid).find(".checkbox_wrapper").height();
            elementPosition =  $('#'+localid).find(".checkbox_wrapper").offset();
            xposs = elementPosition.left - viewerPositions.left;
            yposs = elementPosition.top - viewerPositions.top;
            page =$('#'+localid).attr('page');
            deleteid = localid;
          
            deleteids = checkTotal;
            var did = "'"+deleteid+"'";
            
            html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
         }
         if(texts =='dropdowsns' && drops !== undefined){ 
        
            var signname = '"dropdown"'; 
            var localid = 'dropdowsns_'+drops;
            var tempname = '"addmore"';
            var setText = '"dropdowsns_"';
            groupname = localid;
            html ="<div class='row' id='" + drops + "'><div class='box box-solid'><div class='box-body'><div id='"+drops+"'><div class='form-group'><i class='fa fa-caret-down' aria-hidden='true'></i>&nbsp;&nbsp;<label>Dropdown</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"dropid" + drops + "\","+signname+","+drops+");' id='signer_dropdown" + drops + "' class='form-control'></select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>DropDown "+drops+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label><input type='checkbox' name='DropRequired' id='drop_required_"+drops+"' onchange='getGenerateArray("+signname+","+drops+")' onclick='gerRequired(this.value,"+drops+","+signname+")' value='1'></label>Required Field</div></div><div class=''><div class='form-group'><label><input type='checkbox' name='DropRead_"+drops+"' id='drops_read_"+drops+"' onchange='getGenerateArray("+signname+","+drops+")' onclick='gerReadOnly(this.value,"+drops+","+signname+")'></label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><span>Fill in the list of options.</span><div id='multid"+drops+"'></div><a onclick='addmore("+signname+","+drops+","+tempname+")'><i class='fa fa-plus'></i>Add Option</a></div><hr></div><div class='col-md-12'><div class='form-group'><label>Default Option</label><div class=''><select class='drops_"+drops+" form-control'  ><option>Select</option></select></div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div class=''><button onclick='showHeaders("+signname+","+drops+","+setText+")'> Create Rules</button></div></div><hr></div></div>";
        
            ActionType =$('#'+localid).attr('type');
            textHolder = $('#'+localid).find(".drips");

            elementWidth = $('#'+localid).find(".drips").width();
            elementHeight = $('#'+localid).find(".drips").height();
            elementPosition =  $('#'+localid).find(".drips").offset();
            xposs = elementPosition.left - viewerPositions.left;
            yposs = elementPosition.top - viewerPositions.top;
            page =$('#'+localid).attr('page');
            deleteid = $('#'+localid).attr('id');
            deleteids = drops;
            var did = "'"+deleteid+"'";
            html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
            
         }
         if(texts =='fields' && fields_caregiver !== undefined){
             
             var signname = '"fields"'; 
             var localid = 'caregiver_'+fields_caregiver;
             html ="<div class='row' id='" + fields_caregiver + "'><div class='box box-solid'><div class='box-body'><div id='"+fields_caregiver+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Caregiver Look Up fields</label></div><hr><div class=''><div class='form-group'><label>Dropdown</label><select class='caregiverId"+fields_caregiver+" form-control' onchange='caregiverWiseChange(this.value,"+fields_caregiver+")'></select></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Caregiver "+fields_caregiver+"</div></div><hr></div><div class=''><div class='form-group font-div'><label>Font</label><input type='text' id='font"+fields_caregiver+"' class='form-control font-size-box' onkeyup='SetFontSize("+fields_caregiver+","+signname+")'></div></div></div></div></div><script>getAjax('"+fields_caregiver+"')</script>";
             ActionType =$('#'+localid).attr('type');
             textHolder = $('#'+localid).find(".writing-pad1");

             elementWidth = $('#'+localid).find(".writing-pad1").width();
             elementHeight = $('#'+localid).find(".writing-pad1").height();
             elementPosition =  $('#'+localid).find(".writing-pad1").offset();
             xposs = elementPosition.left - viewerPositions.left;
             yposs = elementPosition.top - viewerPositions.top;
             page =$('#'+localid).attr('page');
             deleteid = $('#'+localid).attr('id');
             deleteids = fields_caregiver;
             var did = "'"+deleteid+"'";
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
             
         }
         if(texts =='staffs' && fields_staff !== undefined){
             
             var signname = '"staffs"'; 
             var localid = 'staff_'+fields_staff;
             html ="<div class='row' id='" + fields_staff + "'><div class='box box-solid'><div class='box-body'><div id='"+fields_staff+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Applicant Look Up fields</label></div><hr><div class=''><div class='form-group'><label>Dropdown</label><select class='staffId"+fields_staff+" form-control' onchange='staffWiseChange(this.value,"+fields_staff+")'></select></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Applicant "+fields_staff+"</div></div><hr></div><div class=''><div class='form-group font-div'><label>Font</label><input type='text' id='font"+fields_staff+"' class='form-control font-size-box' onkeyup='SetFontSize("+fields_staff+","+signname+")'></div></div></div></div></div><script>getAjaxStaff('"+fields_staff+"')</script>";
             ActionType =$('#'+localid).attr('type');
             textHolder = $('#'+localid).find(".writing-pad1");

             elementWidth = $('#'+localid).find(".writing-pad1").width();
             elementHeight = $('#'+localid).find(".writing-pad1").height();
             elementPosition =  $('#'+localid).find(".writing-pad1").offset();
             xposs = elementPosition.left - viewerPositions.left;
             yposs = elementPosition.top - viewerPositions.top;
             page =$('#'+localid).attr('page');
             deleteid = $('#'+localid).attr('id');
             deleteids = fields_staff;
             var did = "'"+deleteid+"'";
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
             console.log(html);
         }
         
        
         /* End Patient Lookup fields */
       
          if(texts =='radios' && radios !== undefined){
             
             var signname = '"radios"';
             var localid = 'radios_'+radios;
         
             if(tempStoreSelectedId!=""){
         
                 var localid = 'radios_'+tempStoreSelectedId;
                 
             }
             
             tempStoreSelectedId="";
            /*added new logic permission code */
             var i=1;
                 checkBoxYsnamic = '';
                 var ctempid;
                 var OldTextArray = [];
                 var OldCheckArray = [];
                 var  texts = '';
                 var lengths = RadioArray.length;
                 
                 var  textsd = '';
                 if(lengths > 0){
                     $.each(RadioArray,function(index,valsd){ 
                         if(valsd.text != undefined){
                             texts = valsd.text;
                         }else{ 
                                 texts = '';
                         }

                   
                         OldTextArray[valsd.id] = texts;
                         if(valsd.checked ==true){
                             chktrue = valsd.checked;
                         }else{
                             chktrue =false;
                         }

                         OldCheckArray[valsd.id]= chktrue;
                     })
               }
               
               var TempRadio =[];
             groupname="radiogroup"+radios;
             if(radioGroupId!=""){
                 groupname="radiogroup"+radioGroupId;
                 radioGroupId="";
             }
             $.each($("input[name='"+groupname+"']"), function(index,elementObj) {
             
                 subid = "'radio_wrap_"+radios+i+"'";
                 inputEmail ="'inputEmail"+radios+i+"'";
             
                 if(lengths >0){  
               
                     if(OldTextArray['radio_wrap_'+radios+i] == undefined){ 
                         textsd ='';
                     }else{
                         textsd =  OldTextArray['radio_wrap_'+radios+i];
                     }

                     if(OldCheckArray['radio_wrap_'+radios+i] !='' || OldCheckArray['radio_wrap_'+radios+i] != undefined){ 
                         var check = OldCheckArray['radio_wrap_'+radios+i];
                           if(check ==true){
                             check = 'checked="checked"';
                           }else{
                             check = '';
                           }
                     }

               
                 }
                 textsd=elementObj.value;

                 var ttmpName = "'radios'";
                 checkBoxYsnamic += '<div class="mycheck'+radios+''+i+'"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" '+check+' id="dymic_'+radios+""+i+'" onclick="getOnClickRadio('+ttmpName+','+subid+',this.id,'+inputEmail+','+radios+')"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail'+radios+i+'" value="'+textsd+'" placeholder="Checkbox value" onkeyup="getSetValueRadio('+subid+',this.id,'+radios+')"></div></div></div></div>';
                 ctempid = i;
                 i++; 
                 
             });
             
           var setText = '"radio_wrap_'+radios+ctempid+'"';
           /*end new logic permission */
            html ="<div class='row' id='" + radios + "'><div class='box box-solid'><div class='box-body'><div id='"+radios+"'><div class='form-group'><i class='fa fa-briefcase' aria-hidden='true'></i> <label>Radio</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"" + groupname + "\","+signname+","+radios+");' id='signer_" + groupname + "' class='form-control'></select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Radio "+radios+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><input type='checkbox' name='title_required"+radios+"' id='radios_required_"+radios+"' value='1' onchange='getGenerateArray("+signname+","+radios+")' onclick='gerRequired(this.value,"+radios+","+signname+")'>Required Field</div></div><div class=''><div class='form-group'><input type='checkbox' name='title_read"+radios+"' id='radios_read_"+radios+"' onchange='getGenerateArray("+signname+","+radios+")' onclick='gerReadOnly(this.value,"+radios+","+signname+")'  >Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Radio Value</label><div class='chkboxval"+radios+"'>"+checkBoxYsnamic+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Conditional Rules</label></div><div class=''><button onclick='showHeaders("+signname+","+radios+","+setText+")'> Create Rules</button></div><hr></div></div>";	
            ActionType =$('#'+localid).attr('type');
            textHolder = $('#'+localid).find(".radio_wrap");

            elementWidth = $('#'+localid).find(".radio_wrap").width();
            elementHeight = $('#'+localid).find(".radio_wrap").height();
            elementPosition =  $('#'+localid).find(".radio_wrap").offset();
            xposs = elementPosition.left - viewerPositions.left;
            yposs = elementPosition.top - viewerPositions.top;
            page =$('#'+localid).attr('page');
                deleteid = $('#'+localid).attr('id');
             deleteids = radios;
             var did = "'"+deleteid+"'";
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
             
         }
        
         
         if(texts =='datesigned' && totalDateSign !== undefined){ 
         
              var signname = '"datesigned"';
             var localid = 'datesigned_'+totalDateSign;
             groupname = localid;
             html ="<div class='row' id='" + totalDateSign + "'><div class='box box-solid'><div class='box-body'><div id='"+totalDateSign+"'><div class='form-group'><i class='fa fa-briefcase' aria-hidden='true'></i> <label>Date Signed</label></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Date Signerd "+totalDateSign+"</div></div><hr></div><div class=''><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='dates_required_"+totalDateSign+"' value='1' onchange='getGenerateArray("+signname+","+totalDateSign+")' onclick='gerRequired(this.value,"+totalDateSign+","+signname+")'>Required Field</div></div><div class='col-md-12'><div class='form-group'><input type='checkbox' name='title_required' id='dates_read_"+totalDateSign+"' onchange='getGenerateArray("+signname+","+totalDateSign+")' onclick='gerReadOnly(this.value,"+totalDateSign+","+signname+")'  >Read Only</div></div></div></div>";	
             ActionType =$('#'+localid).attr('type');
             textHolder = $('#'+localid).find(".writing-pad1");
             
             elementWidth = $('#'+localid).find(".writing-pad1").width();
             elementHeight = $('#'+localid).find(".writing-pad1").height();
             elementPosition =  $('#'+localid).find(".writing-pad1").offset();
         
             xposs = elementPosition.left - viewerPositions.left;
             yposs = elementPosition.top - viewerPositions.top;
             page =$('#'+localid).attr('page');
             deleteid = $('#'+localid).attr('id');
             deleteids = totalDateSign;
             var did = "'"+deleteid+"'";
             html +='<hr><a href="javascript:void(0)" onclick="getDeleteFields('+did+','+deleteids+')">Delete</a></div></div></div>';
         }
         
         $('#vishal123').append('');

         data.push(testingArray);

         if (localid === undefined) { 
         
         }else{ 
             var ids = localid.split('_');
             ResponseList ={"tempId":ids[1],"groupname":groupname,"deleteids":deleteids,"id":localid,"type":texts,"Xpos":xposs,"Ypos":yposs,"Obj":html,"Action":ActionType,"page":page,"width":elementWidth,"height":elementHeight};
             var  element = Object.assign({},ResponseList);
             
             vishaldata.push(element);
             var tempVishalData=[];
             $.each(vishaldata,function(index,objvalue){
                 if(objvalue.groupname==groupname){
                     objvalue.Obj=html;
                 }
                 tempVishalData.push(objvalue);
             });
             vishaldata=tempVishalData;
         }   
     }
     function getAjax(id,keys){ 
         $.ajax({
                 url:careginer,
                 type:"GET",
                 data:{'value':keys},
                 success:function(response){
                     if(response !=''){ 
                 
                         $('.caregiverId'+id).html(response);
                     }
                 }
             });
     }
     function getAjaxStaff(id,keys){ 
         $.ajax({
                 url:staff,
                 type:"GET",
                 data:{'value':keys},
                 success:function(response){
                     if(response !=''){ 
                 
                         $('.staffsId'+id).html(response);
                         //$('.select2').select2();
                     }
                 }
             });
     }
     

     function ChangesName(val,id){
         $("#fid"+id).attr('placeHolder',val);
     }
     function Scall(val){
             var scall = $('.scall_id').val();
         if(scall >=50 && scall <=100 ){
             
         }else{
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
         $(".signer-element[page="+pageNum+"]").last().addClass("selected-element");
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
       }else if(group === "symbol" || group === "shape"){
         $(".signer-tool[tool=color], .signer-tool[tool=duplicate]").removeClass("disabled");
       }else if(group === "image"){
         $(".signer-tool[tool=duplicate]").removeClass("disabled");
       }else if(group === "draw"){
         $(".signer-tool[tool=color], .signer-tool[tool=fontsize]").removeClass("disabled");
       }else if(group === "request"){
         disableTools();
         $(".signer-tool[tool=input], .signer-tool[group=text], .signer-tool[tool=color], .signer-tool[tool=duplicate], .signer-tool[tool=fontsize], .signer-tool[tool=delete]").removeClass("disabled");
       }else{
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
       }else{
         $(".signer-tool[tool=bold]").removeClass("active");
       }
       if (elem.attr("italic") === "true") {
         $(".signer-tool[tool=italic]").addClass("active");
       }else{
         $(".signer-tool[tool=italic]").removeClass("active");
       }
       if (elem.attr("underline") === "true") {
         $(".signer-tool[tool=underline]").addClass("active");
       }else{
         $(".signer-tool[tool=underline]").removeClass("active");
       }
       if (elem.attr("strikethrough") === "true") {
         $(".signer-tool[tool=strikethrough]").addClass("active");
       }else{
         $(".signer-tool[tool=strikethrough]").removeClass("active");
       }
       if (elem.attr("align") === "left") {
         $(".signer-tool[tool=alignleft]").addClass("active");
       }else{
         $(".signer-tool[tool=alignleft]").removeClass("active");
       }
       if (elem.attr("align") === "left") {
         $(".signer-tool[tool=alignleft]").addClass("active");
       }else{
         $(".signer-tool[tool=alignleft]").removeClass("active");
       }
       if (elem.attr("align") === "right") {
         $(".signer-tool[tool=alignright]").addClass("active");
       }else{
         $(".signer-tool[tool=alignright]").removeClass("active");
       }
       if (elem.attr("align") === "center") {
         $(".signer-tool[tool=aligncenter]").addClass("active");
       }else{
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
       $("#document-viewer").css( 'cursor', 'text' );
       updateSelectedFontSize(14, "Font Size");
       highlightCanvas();
       enableTools("text");
      }


     /*
      *  Enable drawing mode
      */
      function enableDrawMode() {  
       $(".signer-tool[tool=draw]").addClass("active");
       $("#document-viewer").css( 'cursor', 'pointer' );
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
       }else{
         return false;
       }
      }


     /*
      *  Initialize editor on scroll
      */
      $('.signer-overlay').off('scroll').on('scroll', function(){ 
       if (isDrawMode()) {
         initEditor();
       }
     });


     /*
      *  Get styling used by user
      */
      function currentTextStyle(){
       style = '';
       if ($(".signer-tool[tool=bold]").hasClass("active")) {
         style = style+' bold="true"';
       }
       if ($(".signer-tool[tool=italic]").hasClass("active")) {
         style = style+' italic="true"';
       }
       if ($(".signer-tool[tool=underline]").hasClass("active")) {
         style = style+' underline="true"';
       }
       if ($(".signer-tool[tool=strikethrough]").hasClass("active")) {
         style = style+' strikethrough="true"';
       }
       if ($(".signer-tool[tool=alignleft]").hasClass("active")) {
         style = style+' align="left"';
       }
       if ($(".signer-tool[tool=alignright]").hasClass("active")) {
         style = style+' align="right"';
       }
       if ($(".signer-tool[tool=aligncenter]").hasClass("active")) {
         style = style+' align="center"';
       }
       return style;
      }


     /*
      *  Get selected color
      */
     function selectedColor(){
       color = $(".signer-tool[tool=color]").attr("color");
       return color;
     }


     /*
      *  Get selected font
      */
     function selectedFont(){
       font = {
         "font": $(".font-item.selected").attr("font"),
         "family": $(".font-item.selected").attr("family")
       };
       return font;
     }


/*
*  Updated selected value of color picker
*/
function updateColorPicker(color){
colorValue = color.replace("#", "");
document.getElementById('color-picker').jscolor.fromString(colorValue);
$(".signer-tool[tool=color]").attr("color", color);
return true;
}


/*
*  Get selected font size
*/
function selectedFontSize(){
fontSize = $(".font-size").val();
return fontSize;
}


/*
*  Updated selected font size
*/
function updateSelectedFontSize(fontSize, label){
$(".font-size").val(fontSize);
if (label !== undefined) {
 $(".font-size-label").text(label);
}
return true;
}


/*
*  Updated selected font 
*/
function highlightSelectedFont(font){
$(".font-item").removeClass("selected");
$(".font-item[font="+font+"]").addClass("selected");
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
}else{
 topOffset = 185;
}
currentOffset = $(".signer-overlay-previewer").offset();
yPos = parseInt(yPos - currentOffset.top + topOffset);
$('<div class="signer-element selected-element" type="text" page="'+page+'" '+style+' font="'+font.font+'" color="'+color+'" font-size="'+fontSize+'" style="left:'+parseInt(xPos - 5)+'px;top:'+parseInt(yPos - 15)+'px;position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+color+';font-size:'+fontSize+'px;font-family:'+font.family+'"  spellcheck="false">'+text+'</div></div>').appendTo(".signer-builder");
initElementsDrag();
focusText();
}


/*
*  Update selected element color
*/
function updateColor(color){
element = $(".signer-element.selected-element");
$(".signer-tool[tool=color]").attr("color", "#"+color);
if (element.attr("type") === "text") {
 element.attr("color", "#"+color);
 element.find(".writing-pad").css("color", "#"+color);
}else if (element.attr("type") === "symbol" || element.attr("type") === "shape") {
 element.find("path").css("fill", "#"+color)
 element.attr("color", "#"+color);
}else if(isDrawMode()){
 modules.color(color);
}else if(element.length == 0){
 $(".signer-element[type=text]").attr("color", "#"+color);
 $(".signer-element[type=text]").find(".writing-pad").css("color", "#"+color);
}
}


/*
*  Update selected element font size
*/
function updateTextSize(fontSize){
if ($(".signer-element.selected-element[type=text]").length) {
 elem = $(".signer-element.selected-element[type=text]");
}else{
 elem = $(".signer-element[type=text]");
}
elem.attr("font-size", fontSize);
elem.find(".writing-pad").css("font-size", fontSize+"px");
}


/*
*  Focus on selected text
*/
function focusText(){
$(".signer-element.selected-element[type=text]").find(".writing-pad").focus();
}


/*
*  Style text
*/
function styleText(style, value){
if ($(".signer-element.selected-element[type=text]").length) {
 elem = $(".signer-element.selected-element[type=text]");
}else{
 elem = $(".signer-element[type=text]");
}
if (style === "bold") {
 if (elem.attr("bold") === "true") {
   elem.removeAttr("bold");
 }else{
   elem.attr("bold", "true");
 }
}
if (style === "italic") {
 if (elem.attr("italic") === "true") {
   elem.removeAttr("italic");
 }else{
   elem.attr("italic", "true");
 }
}
if (style === "underline") {
 if (elem.attr("underline") === "true") {
   elem.removeAttr("underline");
 }else{
   elem.attr("underline", "true");
 }
}
if (style === "strikethrough") {
 if (elem.attr("strikethrough") === "true") {
   elem.removeAttr("strikethrough");
 }else{
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
$(".signer-overlay").click(function(event){
 if($(event.target).closest("#vishal123").length>0)
 {
     return true;
 }
event.preventDefault();
if ($(".signer-element[status=drop]").length > 0) {
 if (event.target.id === "document-viewer") {
   $(".signer-element[status=drop]").css("top", parseInt(event.pageY + $( ".signer-overlay" ).scrollTop()));
   $(".signer-element").removeAttr("status");
   $(".signer-element").css('position', 'absolute');
   $(".signer-element img").css('opacity', '1');
    
   enableTools($(".signer-element.selected-element").attr("type"));
   unHighlightCanvas();
   initElementsDrag();
   initElementsResize();
 }
}else if($(".signer-tool.active[tool=text]").length && event.target.id === "document-viewer"){
 addText(event.pageX, event.pageY);
}
});

/*
*  Add custom fields
*/
function addField(){
$("#addField").modal("hide");
fieldValue = $("input[name=fieldvalue]").val();
fieldLabel = $("input[name=fieldlabel]").val();
fieldId = random();
$(".field-list").append('<div class="field-item field-'+fieldId+'"><a class="delete-field" id="delete-field" href=""><i class="ion-ios-trash-outline" id="delete-field"></i></a><div>'+fieldValue+'</div> <span class="text-muted text-xs">'+fieldLabel+'</span> </div>');
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
function addInputField(){
$("#addInputField").modal("hide");
inputfieldlabel = $("input[name=inputfieldlabel]").val();
fieldId = random();
$(".input-field-list").append('<div class="input-field-item input-field-'+fieldId+'"><a class="delete-input-field" id="delete-input-field" href=""><i class="ion-ios-trash-outline" id="delete-input-field"></i></a><div>'+inputfieldlabel+'</div></div>');
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
function fieldResponse(chatKey, chatId){
 $('.fields-list').find(".field-"+chatKey).closest(".field-item").attr("id", chatId);
 $("input[name=fieldvalue], input[name=fieldlabel]").val('');
}

/*
*  Field response
*/
function inputFieldResponse(chatKey, chatId){
 $('.input-field-list').find(".input-field-"+chatKey).closest(".input-field-item").attr("id", chatId);
 $("input[name=inputfieldlabel]").val('');
}

/*
*  Create Template copy
*/
function createTemplate(){
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
function signerScale(dimesion){ 
templateWidth = $("#document-viewer").width();
templateScale = parseFloat(templateWidth / savedWidth).toFixed(7);
scaled = parseFloat(templateScale * dimesion).toFixed(7);
totals =parseFloat(scaled)+3; 
return parseFloat(totals).toFixed(7);
}


/*
*  Scale dimesions compared to the previous render (Accept request)
*/
function signerScaler(dimesion){
templateWidth = $("#document-viewer").width();
templateScale = parseFloat(templateWidth / requestWidth).toFixed(3);
scaled = parseFloat(templateScale * dimesion).toFixed(3);
return parseFloat(scaled).toFixed(3);
}


/*
*  Show Template Fields
*/
var tempHeaderForHide=[];
function showTemplateFields(){ //

 if (isTemplate === "Yes" && templateFields !== '' && $("body").hasClass("editor") && $(".signer-builder").is(':empty')) {
     if ($(window).width() < 1101) {
         topOffset = 225;
     }else{
         topOffset = 185;
     }

     currentOffset = $(".signer-overlay-previewer").offset();
     currentDocOffset = $("#document-viewer").offset();
     currentPosition = $("#document-viewer").position();

     var j=0;
     
     $.each( templateFields, function( i, field ) {
     var clsaass='';	
             if(field.readOnly == 'readonly'){
                 readonly = 'readonly="readonly"';
             }else{
                 readonly ='';
             }
         
         Width =field.width;
         height =field.height;
         xPos = (parseFloat((field.xPos)) + currentDocOffset.left );
         
         yPos = (parseFloat((field.yPos)) + currentDocOffset.top) ;
         
         if (field.type == "image") {
             splitid = field.id.split('_');
             if(splitid[0] =='sign'){
               var ids = 'signatures_signer_'+splitid[1];
             }else{
                var ids = 'initial_signer_'+splitid[1];
             }
         
             $('<div class="signer-element " resizeable="true" id="'+field.id+'" type="image" group="field" page="'+field.page+'" style="display:none;left:'+xPos+'px;top:'+yPos+'px;position:absolute;"><img src="'+field.image+'" class="img_wrap"  id="'+ids+'" style="width:'+field.width+'px;background-color:'+field.background_color+';" background_color="'+field.background_color+'" signer_id="'+field.signer_id+'"></div>').appendTo(".signer-builder");
             initElementsDrag();
         }else if(field.type == "text"){
             clsaass='';
             if(field.textsmall ==1 ){ 
                 clsaass =' heightClass';
                 
             }
             
             
             if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
             if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
             if (field.underline !== '') { field.underline = ' underline="true"'; }
             if (field.bold !== '') { field.bold = ' bold="true"'; }
             if (field.italic !== '') { field.italic = ' italic="true"'; }
             var place = '';
             if(field.text !='undefined'){ 
                 vtext = field.text;
             }else{
                 vtext='';
             }
             splitid = field.id.split('_');
             var datesignss = '';
             
             if(splitid[0] =='datesigned'){
                 textid = 'datesigneds_'+splitid[1];

                 datesignss = 'signeeddate';
             }
             
             if(splitid[0] =='text'){
                 textid = 'checks'+splitid[1];
             }if(splitid[0] =='checkboxs'){
                 textid = 'cid_'+splitid[1];
             }
             if(splitid[0] =='radios'){
                 textid = 'radio_wrap_'+splitid[1];
             }
             if(splitid[0] =='caregiver'){
                 textid = 'caregivers_'+splitid[1];
             }
             if(splitid[0] =='staff'){
                 textid = 'staffs_'+splitid[1];
             }
         
             if(field.placeHolder =='Textbox'){ 
                 if(field.text != ''){
                         place = field.text;
                 }else{
                     place ="Textbox" ;
                 }
             }else{ 
                 place =field.placeHolder ;
             }
             
             if(field.required =='true'){
                 required = 'required="required"';
                 classes = 'error';
                 colors = 'error';
                 
             }else{  
                   classes = '';
                   required = '';
                   colors = 'error';
             }
             var font = 10;
             if(field.font !='undefined'){ 
                 font = field.font;
             }
             
             
             
             if(splitid[0] === 'caregiver' || splitid[0] === 'staff'){
                 
                 if(field.temp3 =='record@ssn' || field.temp3 =='related@ssn'){
                     heights = field.height;
                 }else{
                     heights = 20;
                 }
                 
                 $('<div class="signer-element" tempid="'+field.groupSmapleId+'"  id="' + field.id + '" type="text"    page="' + field.page + '" style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;" ><textarea heightId="'+field.textsmall+'" groupsName="'+field.groupNames+'" disabled="' + field.temp2 + '" type="text" id ="' + textid + '"type="' + field.type + '"  placeHolder="' + place.trim() + ' "class="writing-pad1  ' + classes + ' '+clsaass+'" style="width:'+Width+'px;height:'+height+'px; background-color:' + field.background_color + ';font-size:'+font+'" value="' + vtext + '" ' + readonly + ' ' + required + '  background_color="'+field.background_color+'"  title="'+field.temp3+'" font="'+font+'" datakey="'+field.temp1+'"></textarea> </div>').appendTo(".signer-builder");
             }
             else{
         
                 if(splitid[0] !='staff' && splitid[0] !='caregiver'){
                     
                     if(splitid[0] === 'datesigned'){ 
                         var addClasss = 'signeeddate';
                         $('<div class="signer-element" datass type="text" id="'+field.id+'" group="input"  tempid="'+field.groupSmapleId+'" page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><textarea type="text"  placeHolder="'+field.placeHolder.trim()+'" disabled class="writing-pad1 '+addClasss+'" id="'+textid+'" style="width:'+Width+'px;height:17px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" contenteditable="true" spellcheck="false" >'+DateSingDate+'</textarea></div>').appendTo(".signer-builder");
                     }else{ 
                         
                         /*start new Added logic permission */
                     
                     var tempHeaderForHide=[]; 
                     if(field.conditionaRules !=undefined){
                         tempHeaderForHide.push(field.conditionaRules);
                     }
                     $.each(tempHeaderForHide,function(i,kes){ 
                             
                             if(kes.ReceiverId == textid){ 
                                 $('#'+kes.ReceiverId).addClass('Depending');
                             }
                     });
                 /*end new added logic permission */
 
                         $('<div class="signer-element" tempid="'+field.groupSmapleId+'" id="' + field.id + '" type="text"    page="' + field.page + '" style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;" ><textarea heightId="'+field.textsmall+'" groupsName="'+field.groupNames+'" type="text" id ="' + textid + '"type="' + field.type + '"  placeHolder="' + place.trim() + ' "class="writing-pad1  ' + classes + ' '+clsaass+'" style="width:' + Width + 'px;height:' + height + 'px; background-color:' + field.background_color + ';font-size:'+font+'" value="' + vtext + '" ' + readonly + ' ' + required + '  background_color="'+field.background_color+'" signer_id="'+field.signer_id+'" title="'+field.temp3+'" font="'+font+'" datakey="'+field.temp1+'" ></textarea> </div>').appendTo(".signer-builder");
                     }
                 }
             }
     
         }else if(field.type=="dropdown"){
                 var explode = field.id.split('_');
                 var requireds = '';
                 classes = '';
                 colors = '';
                 
                 if(field.required =='true'){
                     requireds = "required='required'";
                     classes = 'error';
                     colors = 'error';
                 }
                 var dropsdownList  ='';
             
                 if(field.drops_valeus !='undefined'){
             
                     dropsdownList  ='<option>Select</option>';
                     $.each(field.drops_valeus,function(i,val){
                         dropsdownList += '<option id="remove_'+val.mId+'" value="'+val.value+'">'+val.value+'</option>';
                 
                     });
                 }
                 
                 console.log(field.drops_valeus);
                 var tempHeaderForHide=[]; 
                 if(field.conditionaRules !=undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                 }
                 $.each(tempHeaderForHide,function(i,kes){
                         if(kes.ReceiverDivId ==field.id){ 
                             $('#'+kes.ReceiverDivId).addClass('Depending');
                         }
                 });
                 
                 $('<div class="signer-element"   id="'+field.id+'" type="dropdown"  tempid="'+field.groupSmapleId+'"   page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;" ><select  groupsName="'+field.groupNames+'" tempIds="'+field.groupSmapleId+'" name="dropdownd" id="dropid'+explode[1]+'"  class="drips '+classes+'" style="background-color:'+field.background_color+';width:50px;" background_color="'+field.background_color+'" signer_id="'+field.signer_id+'" '+requireds+' '+readonly+' >'+dropsdownList+'</select></div>').appendTo(".signer-builder");
                     
         }
    
        
         else if(field.type == "checkbox"){
             if(field.checked ==1){
                 checked='checked="checked"';
             }else{
                 checked='';
             }
      
             if(field.required === "true"){ 
                 required = 'required="required"';
                 classes = 'error';
                 colors = 'error';
             }else{  
                   classes = '';
                   required = '';
                   colors = 'error';
             }
         
             colors='';

             if(field.readOnly === 'readonly'){
                 readonly = 'readonly="readonly"';
             }else{
                 readonly = '';
             }
             if(field.addmore =='addmore'){
                 var explode = field.id.split('_');
                 
                 var names = "'"+field.name+"'";
                 var bgcolor = "'"+field.background_color+"'";
                 var signer_id = "'"+field.signer_id+"'";
                 var onclicks = '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="getCheckAppend('+explode[1]+',1,'+names+','+bgcolor+','+signer_id+')"><i class="fa fa-plus"></i></a>'
             }else{ 
                 var onclicks = '';
             }
             var tempHeaderForHide=[];
             if(field.conditionaRules !=undefined){
                 tempHeaderForHide.push(field.conditionaRules);
             }
             
         
             //xPos =parseFloat(xPos)-3;
             $('<div class="signer-element ui-draggable ui-draggable-handle"  tempid="'+field.groupSmapleId+'" id="'+field.id+'" type="checkbox"  group="input"   page="'+field.page+'"  style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;'+colors+'"><input type="checkbox" class="checkbox_wrapper" contenteditable="true"  name="'+field.name+'" style=""  value="'+field.checked+'" '+checked+' id="'+field.bold+'"  background_color="'+field.background_color+'" signer_id="'+field.signer_id+'" '+required+' data-keys="'+field.addmore+'" group="'+field.groups_checkbox+'" '+readonly+' groupsName="'+field.groupNames+'" tempIds="'+field.groupSmapleId+'">'+onclicks+'</div>').appendTo(".signer-builder");
             $.each(tempHeaderForHide[0],function(i,kes){ 
             
                 if(kes.ReceiverDivId ==field.id){ 
                     $('#'+kes.ReceiverDivId).addClass('Depending');
                 }
             });
         }
         else if(field.type == "radio"){ 
                 
             var explode = field.id.split('_');
         
             colors='';
             if(field.required === "true"){ 
                 required = 'required="required"';
                 classes = 'error';
                 colors = 'error';
             }else{  
                   classes = '';
                   required = '';
                   colors = 'error';
             }
             if(field.readOnly === 'readonly'){
                 readonly = 'readonly="readonly"';
                 checked ='checked="checked"';
             }else{
                 readonly = '';
                 checked='';
             }
             if(field.addmore =='addmore'){
                 var explode = field.id.split('_');
                                 
                 var names = "'"+field.name+"'";
                 var bgcolor = "'"+field.background_color+"'";
                 var signer_id = "'"+field.signer_id+"'";
                 var onclicks = '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="getAppend('+explode[1]+','+explode[1]+','+names+','+bgcolor+','+signer_id+')"><i class="fa fa-plus"></i></a>'
             }else{
                     var onclicks = '';
             }
             $('<div class="signer-element ui-resizable '+classes+'" tempid="'+field.groupSmapleId+'" id="' + field.id + '" type="radio"    page="' + field.page + '"  style="position:absolute;display:none;left:' + xPos + 'px;top:' + yPos + 'px;' + colors + 'background-color:'+field.background_color+';"><input type="radio" id = "' + field.bold + '" class="radio_wrap"  value="' + field.checked + '" ' + checked + ' name="' + field.name + '"  data-keys="'+field.addmore+'" '+required+' group="'+field.groups_checkbox+'"  background_color="'+field.background_color+'" '+readonly+' signer_id="'+field.signer_id+'"  groupsName="'+field.groupNames+'" tempIds="'+field.groupSmapleId+'">'+onclicks+'</div>').appendTo(".signer-builder");
             /* Logic conditional */
             
             $.each(headers,function(i,kes){
                 if(kes.ReceiverDivId == field.id){
                     $('#'+kes.ReceiverDivId).addClass('Depending');
                 }
             });
             /* End Logic conditional */
     }
  hideElements();
j++;
});

}else if (isTemplate === "docusing" && templateFields !== '' && $("body").hasClass("editor") && $(".signer-builder").is(':empty')) { 

 if ($(window).width() < 1101) {
     topOffset = 225;
 }else{
     topOffset = 185;
 }


 currentOffset = $(".signer-overlay-previewer").offset();
 currentDocOffset = $("#document-viewer").offset();
 currentPosition = $("#document-viewer").position();
 var cnt = 1;
 
 $.each( templateFields, function( i, field ) { 
 
     var tempHeaderForHide= [];
     if(field.conditionaRules != undefined){
         tempHeaderForHide.push(field.conditionaRules);
     }

     /*start add new logic permission */
         var assignId='';
         var assignVal='';
         var receiverid='';
         var receiverid2= '';
         var conditionarules='';
         var globalText;
         if(field.assignId != 'undefined' ){ 
             assignId = 'assignId='+field.assignId;
         }
         if(field.assignVal != 'undefined'){
             assignVal = 'assignVal='+field.assignVal;
             globalText= field.assignVal;
         }
         if(field.receiverid != 'undefined'){
             receiverid = 'receiverid='+field.receiverid;
             $('#'+field.receiverid).addClass('hideShow');
             
         }
         if(field.receiverid2 != 'undefined'){
             receiverid2 = 'receiverid2='+field.receiverid2;
             $('#'+field.receiverid2).addClass('hideShow');
         }
         if(field.conditionarules != 'undefined'){
             conditionarules = 'conditionarules='+field.conditionarules;
         }
         var  HideShow='';
         
         
         
     
     /*End add new logic permission */
         if(field.required == 'true'){ 
             required = 'required="required"';
             colors = 'error';
             bgrequired ='border:2px solid #FF0000';
         }else{  
               colors = '';
               required = '';
                 bgrequired ='';
         }
         readonly='';
         if(field.readOnly =='readonly'){
             readonly = 'readonly="readonly"';
         }
     
         xPos = parseFloat((parseFloat(signerScale(field.xPos)) + currentDocOffset.left) - 5).toFixed(2);
         yPos = parseFloat((parseFloat(signerScale(field.yPos)) + currentDocOffset.top)  - 5).toFixed(2);
         yPos = parseFloat(yPos-4);
         Width =(parseFloat(field.width));
         height =(parseFloat(field.height));
 
         if (field.type == "image") {   
             if(docusignId ==field.signer_id){ 
                 bgrequired ='border:2px solid #FF0000';
                 $('<div onclick="mySign('+cnt+')" class="signer-element" resizeable="true" type="image" page="'+field.page+'" style="'+bgrequired+';left:'+xPos+'px;top:'+yPos+'px;position:absolute;display:none;" id="'+field.id+'" dataid="'+cnt+'"  page="'+pageNum+'"><img src="'+field.image+'" id="img'+cnt+'" style="height:'+height+'px;width:'+Width+'px; " background_color="'+field.background_color+'" signer_id="'+field.signer_id+'"></div>').appendTo(".signer-builder");
             }

         }else if(field.type == "text"){ 
         
             if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
             if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; } 
             if (field.underline !== '') { field.underline = ' underline="true"'; }
             if (field.bold !== '') { field.bold = ' bold="true"'; }
             if (field.italic !== '') { field.italic = ' italic="true"'; }
             var font = 10;
             if(field.font !='undefined'){
                 font = 10;
             }  
             if(field.temp1 !='' && field.temp3 !=''){
                 if(field.temp3 =='Referral@FULLADDRESS' || field.temp3 =='Referral@PATIENT_ADDRESS' || field.temp3 =='PA@ADDRESS_1' || field.temp3 =='PA@FULLADDRESS' || field.temp3 =='Consumer@FULLADDRESS' || field.temp3 =='Consumer@ADDRESS_1'){
                     $('<div class="signer-element"  page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;z-index:99;"><input type="hidden" id="'+field.id+'" dataid="'+field.temp1+'" ><label class="writing-pad1" id="int'+field.id+'" style="width:325px;height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'"></label></div>').appendTo(".signer-builder");
                 }else{
                     $('<div class="signer-element"  page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;z-index:99;"><input type="hidden" id="'+field.id+'" dataid="'+field.temp1+'" ><label class="writing-pad1" id="int'+field.id+'" style="height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'"></label></div>').appendTo(".signer-builder");
                 }
                 if(field.temp1 =='caregiver'){ 
                     var response = GetCaregiverRequeestFileds(field.temp3,sessionIds,field.id);
                 }else if(field.temp1 =='patient'){ 
                     //var response = GetCaregiverRequeestFileds(field.temp3);
                 }else if(field.temp1 =='intake'){  
                         var response = GetIntakeRequeestFileds(field.temp3,sessionIds,field.id);
                 }
                 
             }else{ 
                 
                 if(field.required == "true"){ 
                     required = 'required="required"';
                     colors = 'error';
                     bgrequired ='border:2px solid #FF0000';
                 }else{  
                       colors = '';
                       required = '';
                         bgrequired ='';
                 }
                 var response= field.text;
                 var place='';
                 if(field.text !=''){ 
                     place = field.text;
                 }else{
                     place = field.placeHolder;
                 }
                 var explode = field.id.split('_');
                 if(explode[0] =='text'){
                     textid = 'checks'+explode[1];
                 }
                 if(explode[0] == 'datesigned'){ 
                     var addClasss = 'signeeddate';
                     $('<div class="signer-element" type="text"  group="input"   page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><textarea disabled type="text"  placeHolder="'+field.placeHolder+'" class="writing-pad1 '+addClasss+'" id="'+field.id+'" style="width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+font+'px;font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" >'+DateSingDate+'</textarea></div>').appendTo(".signer-builder");
                 }
                 var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     field.conditionaRules.MainType = 'Text';
                     tempHeaderForHide.push(field.conditionaRules);
                     
                     TextArray = tempHeaderForHide;
                 }
                 if(docusignId ==field.signer_id ){  
                 
                     $.each(TextArray[0],function(i,kes){
                         if(kes.SenderId == field.id){
                              HideShow = ' onkeyup = HideShow("'+kes.SenderId+'","'+kes.value+'","'+kes.ReceiverId+'")';
                         }
                         
                     }); 
                     
                     $('<div  class="signer-element " '+assignVal+' id="'+textid+'" type="text"  group="input"   page="'+field.page+'" style="position:absolute;left:'+xPos+'px;top:'+yPos+'px;display:none !importants;"><textarea type="text"  placeHolder="'+place+'" class="writing-pad1" id="'+field.id+'" style="'+bgrequired+';width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" contenteditable="true" spellcheck="false" '+HideShow+' value="'+response+'" '+readonly+' '+assignId+' '+assignVal+' '+receiverid+' '+receiverid2+' '+conditionarules+'></textarea></div>').appendTo(".signer-builder");
                     $.each(headers,function(i,kes){
                         if(kes.ReceiverId == textid){
                                 $('#'+kes.ReceiverId).addClass('Depending');
                                 $('#'+kes.ReceiverDivId).addClass('rules');
                         }
                     });

                     //$('<div class="signer-element" '+assignVal+' '+assignId+' id="'+textid+'" type="text"  group="input"   page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><textarea type="text"  placeHolder="'+place+'" class="writing-pad1 '+colors+'" id="'+field.id+'" style="'+bgrequired+';width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+field.font+'px;font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" contenteditable="true" spellcheck="false" value="'+response+'" '+readonly+' '+HideShow+' '+assignId+' '+assignVal+' '+receiverid+' '+receiverid2+' '+conditionarules+' ></textarea></div>').appendTo(".signer-builder");
                 }
             }
         }
         else if(field.type == "checkbox"){ 
             if(field.readOnly =='readonly'){
                 readonly = 'readonly="readonly"';
                 disabled = 'disabled';
                 checked='checked="checked"';
             }else{
                 readonly = '';
                 disabled = '';
                 checked='';
             }
             var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     
                 }
                 
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                     
                         if(kes.SenderId == field.bold){
                              HideShow = ' onclick = HideShowCheck("'+kes.ReceiverId+'","'+kes.SenderId+'","'+kes.value+'")';
                         }
                         
                     });
             if(docusignId ==field.signer_id){
                 
                 $('<div class="signer-element '+field.bold+'" id="'+field.id+'" type="checkbox"  group="input"   page="'+field.page+'"  style="'+bgrequired+';position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><input type="checkbox" class="checkbox_wrapper" contenteditable="true" style="" name="'+field.name+'"  value="'+field.checked+'" '+checked+' id="'+field.bold+'" '+HideShow+' '+readonly+' '+disabled+'></div>').appendTo(".signer-builder");
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                         if(kes.SenderId == field.bold){
                              HideShow = ' onclick = HideShowCheck("'+kes.ReceiverId+'","'+kes.SenderId+'","'+kes.value+'")';
                         }
                         if(kes.ReceiverId == field.bold){
                             
                                 $('#'+kes.ReceiverId).addClass('Depending');
                                 $('#'+kes.ReceiverId).addClass('rules');
                         }
                     });
             }
         }else if(field.type == "radio"){ 
                 
             var explode = field.id.split('_');
         
             if(field.readOnly =='readonly'){
                 readonly = 'readonly="readonly"';
                 disabled = 'disabled';
                 checked='checked="checked"';
             }else{
                 readonly = '';
                 disabled = '';
                 checked='';
             }
             var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     RadiosArray = tempHeaderForHide;
                 }
             $.each(tempHeaderForHide[0],function(i,kes){ 
                 
                 if(kes.SenderDivId == field.bold){	
                      HideShow = ' onclick = HideShowRadio("'+kes.SenderDivId+'",this.value)';
                 }
             });
             if(docusignId ==field.signer_id){
                 
                  HideShow = ' onclick = HideShowRadio("'+field.bold+'",this.value)';
             
                 $('<div class="signer-element '+field.groupNames+'" id="'+field.id+'" type="radio"  group="input" groupName="'+field.groupNames+'" page="'+field.page+'"  style="'+bgrequired+';position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><input '+checked+' '+disabled+' type="radio" id = "'+field.bold+'" class="radio_wrap"  value="'+field.checked+'" '+HideShow+' name="'+field.name+'" '+readonly+'></div>').appendTo(".signer-builder");

             }
             
         }else if(field.type=="dropdown"){
             var dropsdownList  ='';
             if(field.drops_valeus !='undefined'){
                 //cnt = 1;
                 dropsdownList  ='<option value="">Select</option>';
                 $.each(field.drops_valeus,function(i,val){
                     dropsdownList += '<option  value="'+val.value+'">'+val.value+'</option>';
             //	cnt++; 
                 });
             }
             var explode = field.id.split('_');
              if(docusignId ==field.signer_id){
                 var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     ConditionalSTempArray =tempHeaderForHide;
                 } 
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                         if(kes.SenderDivId == field.id){
                              HideShow = ' onchange = HideShowDrop("'+kes.SenderDivId+'",this.value,"'+kes.ReceiverId+'")';
                         }
                 });
                 $('<div class="signer-element" id="'+field.id+'"   type="text"    page="'+field.page+'" style="position:absolute;display:none;z-index:99;left:'+xPos+'px;top:'+yPos+'px;" ><select name="dropdownd" id="dropid'+explode[1]+'" '+HideShow+' style="'+bgrequired+';background-color:'+field.background_color+'" background_color="'+field.background_color+'" signer_id="'+field.signer_id+'">'+dropsdownList+'</select></div>').appendTo(".signer-builder");
                     $.each(finalTesting,function(i,kes){ 
                         if(kes.ReceiverDivId == field.id){
                                     $('#'+kes.ReceiverId).addClass('Depending');
                                     $('#'+kes.ReceiverDivId).addClass('rules');
                             }
                         });
              }
         }
         hideElements();
        cnt++;
        j++;
    });
 
}else if (isTemplate === "referral" && templateFields !== '' && $("body").hasClass("editor") && $(".signer-builder").is(':empty')) { 

     if ($(window).width() < 1101) {
     topOffset = 225;
 }else{
     topOffset = 185;
 }


 currentOffset = $(".signer-overlay-previewer").offset();
 currentDocOffset = $("#document-viewer").offset();
 currentPosition = $("#document-viewer").position();
 var cnt = 1;
 
 $.each( templateFields, function( i, field ) { 
 
     var tempHeaderForHide= [];
     if(field.conditionaRules != undefined){
         tempHeaderForHide.push(field.conditionaRules);
     }

     /*start add new logic permission */
         var assignId='';
         var assignVal='';
         var receiverid='';
         var receiverid2= '';
         var conditionarules='';
         var globalText;
         if(field.assignId != 'undefined' ){ 
             assignId = 'assignId='+field.assignId;
         }
         if(field.assignVal != 'undefined'){
             assignVal = 'assignVal='+field.assignVal;
             globalText= field.assignVal;
         }
         if(field.receiverid != 'undefined'){
             receiverid = 'receiverid='+field.receiverid;
             $('#'+field.receiverid).addClass('hideShow');
             
         }
         if(field.receiverid2 != 'undefined'){
             receiverid2 = 'receiverid2='+field.receiverid2;
             $('#'+field.receiverid2).addClass('hideShow');
         }
         if(field.conditionarules != 'undefined'){
             conditionarules = 'conditionarules='+field.conditionarules;
         }
         var  HideShow='';
         
         
         
     
     /*End add new logic permission */
         if(field.required == 'true'){ 
             required = 'required="required"';
             colors = 'error';
             bgrequired ='border:2px solid #FF0000';
         }else{  
               colors = '';
               required = '';
                 bgrequired ='';
         }
         readonly='';
         if(field.readOnly =='readonly'){
             readonly = 'readonly="readonly"';
         }
     
         xPos = parseFloat((parseFloat(signerScale(field.xPos)) + currentDocOffset.left) - 5).toFixed(2);
         yPos = parseFloat((parseFloat(signerScale(field.yPos)) + currentDocOffset.top)  - 5).toFixed(2);
         yPos = parseFloat(yPos-4);
         Width =(parseFloat(field.width));
         height =(parseFloat(field.height));
 
         if (field.type == "image") {   
             if(docusignId ==field.signer_id){ 
                 bgrequired ='border:2px solid #FF0000';
                 $('<div onclick="mySign('+cnt+')" class="signer-element" resizeable="true" type="image" page="'+field.page+'" style="'+bgrequired+';left:'+xPos+'px;top:'+yPos+'px;position:absolute;display:none;" id="'+field.id+'" dataid="'+cnt+'"  page="'+pageNum+'"><img src="'+field.image+'" id="img'+cnt+'" style="height:'+height+'px;width:'+Width+'px; " background_color="'+field.background_color+'" signer_id="'+field.signer_id+'"></div>').appendTo(".signer-builder");
             }

         }else if(field.type == "text"){ 
         
             if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
             if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; } 
             if (field.underline !== '') { field.underline = ' underline="true"'; }
             if (field.bold !== '') { field.bold = ' bold="true"'; }
             if (field.italic !== '') { field.italic = ' italic="true"'; }
             var font = 12;
             if(field.font !='undefined'){
                 font = field.font;
             } 
             if(field.temp1 !='' && field.temp3 !=''){
                 if(field.temp3 =='Referral@FULLADDRESS' || field.temp3 =='Referral@PATIENT_ADDRESS' || field.temp3 =='PA@ADDRESS_1' || field.temp3 =='PA@FULLADDRESS' || field.temp3 =='Consumer@FULLADDRESS' || field.temp3 =='Consumer@ADDRESS_1'){
                     $('<div class="signer-element"  page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;z-index:99;"><input type="hidden" id="'+field.id+'" dataid="'+field.temp1+'" ><label class="writing-pad1" id="int'+field.id+'" style="width:325px;height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'"></label></div>').appendTo(".signer-builder");
                 }else{
                     $('<div class="signer-element"  page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;z-index:99;"><input type="hidden" id="'+field.id+'" dataid="'+field.temp1+'" ><label class="writing-pad1" id="int'+field.id+'" style="height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'"></label></div>').appendTo(".signer-builder");
                 }
                 if(field.temp1 =='caregiver'){ 
                     var response = GetCaregiverRequeestFileds(field.temp3,sessionIds,field.id);
                 }else if(field.temp1 =='patient'){ 
                     //var response = GetCaregiverRequeestFileds(field.temp3);
                 }else if(field.temp1 =='intake'){  
                         var response = GetIntakeRequeestFileds(field.temp3,sessionIds,field.id);
                 }
                 
             }else{ 
                 
                 if(field.required == "true"){ 
                     required = 'required="required"';
                     colors = 'error';
                     bgrequired ='border:2px solid #FF0000';
                 }else{  
                       colors = '';
                       required = '';
                         bgrequired ='';
                 }
                 var response= field.text;
                 var place='';
                 if(field.text !=''){ 
                     place = field.text;
                 }else{
                     place = field.placeHolder;
                 }
                 var explode = field.id.split('_');
                 if(explode[0] =='text'){
                     textid = 'checks'+explode[1];
                 }
                 if(explode[0] == 'datesigned'){ 
                     var addClasss = 'signeeddate';
                     $('<div class="signer-element" type="text"  group="input"   page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><textarea disabled type="text"  placeHolder="'+field.placeHolder+'" class="writing-pad1 '+addClasss+'" id="'+field.id+'" style="width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+font+'px;font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" >'+DateSingDate+'</textarea></div>').appendTo(".signer-builder");
                 }
                 var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     field.conditionaRules.MainType = 'Text';
                     tempHeaderForHide.push(field.conditionaRules);
                     
                     TextArray = tempHeaderForHide;
                 }
                 if(docusignId ==field.signer_id ){  
                 
                     $.each(TextArray[0],function(i,kes){
                         if(kes.SenderId == field.id){
                              HideShow = ' onkeyup = HideShow("'+kes.SenderId+'","'+kes.value+'","'+kes.ReceiverId+'")';
                         }
                         
                     }); 
                     
                     $('<div  class="signer-element " '+assignVal+' id="'+textid+'" type="text"  group="input"   page="'+field.page+'" style="position:absolute;left:'+xPos+'px;top:'+yPos+'px;display:none !importants;"><textarea type="text"  placeHolder="'+place+'" class="writing-pad1" id="'+field.id+'" style="'+bgrequired+';width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+font+';font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" contenteditable="true" spellcheck="false" '+HideShow+' value="'+response+'" '+readonly+' '+assignId+' '+assignVal+' '+receiverid+' '+receiverid2+' '+conditionarules+'></textarea></div>').appendTo(".signer-builder");
                     $.each(headers,function(i,kes){
                         if(kes.ReceiverId == textid){
                                 $('#'+kes.ReceiverId).addClass('Depending');
                                 $('#'+kes.ReceiverDivId).addClass('rules');
                         }
                     });

                     //$('<div class="signer-element" '+assignVal+' '+assignId+' id="'+textid+'" type="text"  group="input"   page="'+field.page+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><textarea type="text"  placeHolder="'+place+'" class="writing-pad1 '+colors+'" id="'+field.id+'" style="'+bgrequired+';width:'+Width+'px;height:'+height+'px;color:'+field.color+';font-size:'+field.font+'px;font-family:'+field.fontfamily+';color:'+field.color+';'+colors+'" contenteditable="true" spellcheck="false" value="'+response+'" '+readonly+' '+HideShow+' '+assignId+' '+assignVal+' '+receiverid+' '+receiverid2+' '+conditionarules+' ></textarea></div>').appendTo(".signer-builder");
                 }
             }
         }
         else if(field.type == "checkbox"){ 
             if(field.readOnly =='readonly'){
                 readonly = 'readonly="readonly"';
                 disabled = 'disabled';
                 checked='checked="checked"';
             }else{
                 readonly = '';
                 disabled = '';
                 checked='';
             }
             var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     
                 }
                 
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                     
                         if(kes.SenderId == field.bold){
                              HideShow = ' onclick = HideShowCheck("'+kes.ReceiverId+'","'+kes.SenderId+'","'+kes.value+'")';
                         }
                         
                     });
             if(docusignId ==field.signer_id){
                 
                 $('<div class="signer-element '+field.bold+'" id="'+field.id+'" type="checkbox"  group="input"   page="'+field.page+'"  style="'+bgrequired+';position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><input type="checkbox" class="checkbox_wrapper" contenteditable="true" style="" name="'+field.name+'"  value="'+field.checked+'" '+checked+' id="'+field.bold+'" '+HideShow+' '+readonly+' '+disabled+'></div>').appendTo(".signer-builder");
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                         if(kes.SenderId == field.bold){
                              HideShow = ' onclick = HideShowCheck("'+kes.ReceiverId+'","'+kes.SenderId+'","'+kes.value+'")';
                         }
                         if(kes.ReceiverId == field.bold){
                             
                                 $('#'+kes.ReceiverId).addClass('Depending');
                                 $('#'+kes.ReceiverId).addClass('rules');
                         }
                     });
             }
         }else if(field.type == "radio"){ 
                 
             var explode = field.id.split('_');
         
             if(field.readOnly =='readonly'){
                 readonly = 'readonly="readonly"';
                 disabled = 'disabled';
                 checked='checked="checked"';
             }else{
                 readonly = '';
                 disabled = '';
                 checked='';
             }
             var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     RadiosArray = tempHeaderForHide;
                 }
             $.each(tempHeaderForHide[0],function(i,kes){ 
                 
                 if(kes.SenderDivId == field.bold){	
                      HideShow = ' onclick = HideShowRadio("'+kes.SenderDivId+'",this.value)';
                 }
             });
             if(docusignId ==field.signer_id){
                 
                  HideShow = ' onclick = HideShowRadio("'+field.bold+'",this.value)';
             
                 $('<div class="signer-element '+field.groupNames+'" id="'+field.id+'" type="radio"  group="input" groupName="'+field.groupNames+'" page="'+field.page+'"  style="'+bgrequired+';position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><input '+checked+' '+disabled+' type="radio" id = "'+field.bold+'" class="radio_wrap"  value="'+field.checked+'" '+HideShow+' name="'+field.name+'" '+readonly+'></div>').appendTo(".signer-builder");

             }
             
         }else if(field.type=="dropdown"){
             var dropsdownList  ='';
             if(field.drops_valeus !='undefined'){
                 //cnt = 1;
                 dropsdownList  ='<option value="">Select</option>';
                 $.each(field.drops_valeus,function(i,val){
                     dropsdownList += '<option  value="'+val.value+'">'+val.value+'</option>';
             //	cnt++; 
                 });
             }
             var explode = field.id.split('_');
              if(docusignId ==field.signer_id){
                 var tempHeaderForHide= [];
                 if(field.conditionaRules != undefined){
                     tempHeaderForHide.push(field.conditionaRules);
                     ConditionalSTempArray =tempHeaderForHide;
                 } 
                 $.each(tempHeaderForHide[0],function(i,kes){ 
                         if(kes.SenderDivId == field.id){
                              HideShow = ' onchange = HideShowDrop("'+kes.SenderDivId+'",this.value,"'+kes.ReceiverId+'")';
                         }
                 });
                 $('<div class="signer-element" id="'+field.id+'"   type="text"    page="'+field.page+'" style="position:absolute;display:none;z-index:99;left:'+xPos+'px;top:'+yPos+'px;" ><select name="dropdownd" id="dropid'+explode[1]+'" '+HideShow+' style="'+bgrequired+';background-color:'+field.background_color+'" background_color="'+field.background_color+'" signer_id="'+field.signer_id+'">'+dropsdownList+'</select></div>').appendTo(".signer-builder");
                     $.each(finalTesting,function(i,kes){ 
                         if(kes.ReceiverDivId == field.id){
                                     $('#'+kes.ReceiverId).addClass('Depending');
                                     $('#'+kes.ReceiverDivId).addClass('rules');
                             }
                         });
              }
         }
         hideElements();
        cnt++;
        j++;
    });
         
}
$("[page="+pageNum+"]").show();
initElementsDrag();
initElementsResize();
}

/*
*  When accept request is clicked
*/
$(".accept-request").click(function(event){
event.preventDefault();
$("body").addClass("accept");
inviting = false;
launchEditor();
})

/*
*  Accept request
*/
function acceptRequest(){ 
if ($("body").hasClass("accept") && requestPositions.length) { 
 showLoader();
 if ($(window).width() < 1101) {
   topOffset = 225;
 }else{
   topOffset = 185;
 }
 currentOffset = $(".signer-overlay-previewer").offset();
 currentDocOffset = $("#document-viewer").offset();
 currentPosition = $("#document-viewer").position();
 textInputs = [];
 $.each( requestPositions, function( i, field ) {
   xPos = parseFloat(parseFloat(signerScaler(field.xPos)) + currentDocOffset.left - 5).toFixed(3);
   yPos = parseFloat((parseFloat(signerScaler(field.yPos)) + currentDocOffset.top) - currentOffset.top + topOffset - 5).toFixed(3);
   if (field.type == "image") {
     $('<div class="signer-element" type="signature" signed="false" group="field" page="'+field.page+'" style="display:none;left:'+xPos+'px;top:'+yPos+'px;position:absolute;"><img src="'+baseUrl+'/assets/images/signhere.png" style="width:'+signerScaler(field.width)+'px;"></div>').appendTo(".signer-builder");
   }else if(field.type == "text"){
     elementId = random({ case: "lower" });
     textInputs.push({ label: field.text, element: elementId });
     if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
     if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
     if (field.underline !== '') { field.underline = ' underline="true"'; }
     if (field.bold !== '') { field.bold = ' bold="true"'; }
     if (field.italic !== '') { field.italic = ' italic="true"'; }
     $('<div class="signer-element element-'+elementId+'" type="text" group="field" '+field.align+field.italic+field.bold+field.underline+field.strikethrough+'  page="'+field.page+'" font="'+field.font+'" color="'+field.color+'" font-size="'+field.fontsize+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><div class="writing-pad" contenteditable="true" style="width:'+signerScaler(field.width)+'px;height:'+signerScaler(field.height)+'px;color:'+field.color+';font-size:'+field.fontsize+'px;font-family:'+field.fontfamily+';color:'+field.color+';"  spellcheck="false">'+field.text+'</div></div>').appendTo(".signer-builder");
   }
   hideElements();
 });
 if (textInputs.length) {
   $.each( textInputs, function( i, input ) {
     $(".requested-fields").append('<div class="col-md-6"><div class="form-group"><label>'+input.label+'</label><input type="text" data-id="'+input.element+'" class="form-control" placeholder="'+input.label+'" required></div></div>')
   });
   $("#requestFields").modal({show: true, backdrop: 'static', keyboard: false});
 }
 $("[page="+pageNum+"]").show();
 hideLoader();
}
}

/*
*  Put data from requsted fields form to the PDF
*/
function updateRequestFields(){
$(".requested-fields input").each( function( i, input ) {
 elementId = $(this).attr("data-id");
 $(".signer-element.element-"+elementId).find(".writing-pad").text($(this).val());
});
$("#requestFields").modal("hide");
$("body").removeClass("accept");
disableTools();
}

/*
*  Login restricted
*/
function loginRequired(){
notify("Login Required", "You need to login to access this feature.", "warning", "Login Now", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+loginPage+"')" });
return false;
}

function DragDops(){
 var tess =$("input[name=duplicate]").prop("checked");
}

function getKeys(vals){

 var textarea1 =$(".textare"+vals).val();
     if(textarea1 ==''){
     $('#checks'+vals).val(" ");
 }else{
     $('#checks'+vals).val(textarea1);
 }

}
//hitendtata
var updatenewselecte;
var selectedValue;
//when we click on element
$('.signer-builder').on('click', '.selected-element', function () { 
 var id = $('.selected-element').attr('id'); 
 console.log(vishaldata);
 console.log("David");
 var keysvale = id.split("_");
 if(keysvale[0] != "datesigned"){
         var groupnametest="";
         $('#vishal123').empty();
         $.each(vishaldata, function( index, value ) {	 
               if(id == value.id){
                     updatenewselecte = value;
                  selectedValue = value.signer_id;
                  getGenerateArray(updatenewselecte.type,updatenewselecte.deleteids);		 
               }  
         });
     
         var response = updatenewselecte.Obj;
     
         $('#vishal123').append(response);
         if(keysvale[0] !='radios' && keysvale[0] !='checkboxs'){
                 getType(keysvale[1], keysvale[0],selectedValue);
         }else{
             getType(updatenewselecte.groupname, keysvale[0],selectedValue);
         }
         if(keysvale[0]=='staff'){
             var selectedValue = $('#staffs_'+keysvale[1]).attr('title');
             getAjaxStaff(keysvale[1],selectedValue);
         }
         if(keysvale[0]=='caregiver'){ 
             var selectedValue = $('#caregivers_'+keysvale[1]).attr('title');	
             getAjax(keysvale[1],selectedValue);
         }
     }
 });
 
 
 
var new_array = [];
var newGlobalId;
 var SenderTests="";
var AssignTests="";
// even we do any action
function getGenerateArray(key,val,temp=null){  
 html=""; 
 SenderTests=""; 
 AssignTests="";
 deleteids = val;
 if(key=='signature'){  
         var did = "sign_"+val;
         var did = '"'+did+'"';
     
         var signname = '"signature"';
         var selected = $('#signer_signatures'+val).html();
         var signRequired = $('#check_sign_'+val).prop('checked');
         if(signRequired ==true){
          var signname = '"signature"';
             required ="<input type='checkbox' class='' name='signature"+val+"' id='check_sign_"+val+"' onclick='getGenerateArray('signature',"+val+")' value='1' checked='checked'>";
             signRequired =1;
             
         }else{
             required ="<input type='checkbox' class='' name='signature"+val+"' id='check_sign_"+val+"' onclick='getGenerateArray(signature,"+val+")' value='1'>";
             signRequired =0;
             
         }
         var signRead = $('#check_read_'+val).prop('checked');
         if(signRead ==true){
         signReadOnly=1;
             readOnly ="<input type='checkbox' class='minimal'  name='signature_read"+sign+"' id='check_read_"+val+"' onclick='getGenerateArray(signature,"+val+")' value='1' checked='checked'>";
         }else{
         signReadOnly=0;
             readOnly="<input type='checkbox' class='minimal'  name='signature_read"+val+"' id='check_read_"+val+"' onclick='getGenerateArray(signature,"+val+")' value='1'>";
         }
         
         /*signer Seleted or not */
         
 
         html = "<div class='row' id="+val+"><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'> <label>Signature</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"signatures_signer_" + val + "\","+signname+","+val+");' id='signer_signatures"+val+"' class='form-control'>"+selected+"</select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label><div class=''>Signature "+val+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>"+required+"</label>Required Field</div></div><div class='col-md-12'><div class='form-group'><label>"+readOnly+"</label>Read Only</div></div></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields("+did+","+deleteids+")'>Delete</a></div></div></div>";	
 }
 
 if(key=='text'){
  
     var signname = '"text"';
     var localid = 'text_'+val;
     var localid = '"'+localid+'"';
     var selected = $('#textss'+val).html();
     var setText = '"checks"';
     var titleRequired = $('#checks'+val).attr('required');
     var placeHolder = $('#checks'+val).attr('placeHolder');
         if(titleRequired =="required"){
             textRequired = 1;
             requiredName ="<input type='checkbox' name='title_required"+val+"' id='text_required_"+val+"' onchange='getGenerateArray("+signname+","+val+")'  onclick='gerRequired(this.value,"+val+","+signname+")'  value='1' checked='checked'>";
         }else {
             textRequired = 0;
              requiredName ="<input type='checkbox' name='title_required"+val+"' id='text_required_"+val+"' onchange='getGenerateArray("+signname+","+val+")'  onclick='gerRequired(this.value,"+val+","+signname+")'  value='1'>";
         }
 
         var titleRead =$('#checks'+val).attr('readonly');; 
             
         if(titleRead =="readonly"){ 
             textReadOnly  = 1;
             readOnlyText ="<input type='checkbox' name='title_required"+val+"' id='text_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")'  onclick='gerReadOnly(this.value,"+val+","+signname+")' checked='checked'>";
             var placeHolders = placeHolder;
         }else{
             textReadOnly  = 0;
             readOnlyText ="<input type='checkbox' name='title_required"+val+"' id='text_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")'  onclick='gerReadOnly(this.value,"+val+","+signname+")' >";
             var placeHolders = '';
         }
         
         var minwidth = $('#minwidth'+val).is(":checked");
         if(minwidth ==true){
             var mins ="<input type='checkbox' id='minwidth"+val+"' onclick='getMinWidth(" + signname + "," + val + ",1)' checked='checked'>";
         }else{
             var mins = "<input type='checkbox' id='minwidth"+val+"' onclick='getMinWidth(" + signname + "," + val + ",1)' >";
         }
     placeHolder = 	placeHolder.replace(/\s/g, '');
     
         if( placeHolder !='Textbox'){
             textareaDetails = placeHolder;
         }else{
             textareaDetails ='';
         }
         
         
         
         var temps='';	
         if(temp ==null){
             font = '10';
         }else{
             font =temp;
         }
     
         var tests = 'text_'+val; 
         var flag ='No';
     
     
         $.each(headers,function(i,lg){
             newGlobalId = tests;
             if(lg.SenderId ==tests && lg.type == "text"){
                 var params = '"'+lg.value+'"';
                 SenderTests +="<ul  class='assigntest5'><li>If this field is then "+params+" show: "+lg.opponent+" id "+lg.ReceiverDivId+"</li><a onclick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
             
             }else if(lg.ReceiverDivId ==tests && lg.opponent == "text"){
                 $('#'+lg.ReceiverId).addClass('Depending');
                 AssignTests +="<ul  class='assigntest6'><li>This field is conditional.Trigger: "+lg.opponent+" id "+lg.SenderId+"</li><a onclick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
             }
         });
     html ="<div class='row' id="+val+"><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><i class='fa fa-text-width' aria-hidden='true'></i>&nbsp;&nbsp;<label>Text</label></div><hr><div class='col-md-12'><div class='form-group'><select name='changesColor' onchange='setSigner(this.id,\"checks" + val + "\","+signname+","+val+");' id='textss"+val+"' class='form-control'>"+selected+"</select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Text "+val+"</div></div><hr></div><div class='col-md-12'><div class='row'><div class='form-group'><label>"+requiredName+"</label>Required Field</div></div><div class='row'><div class='form-group'><label>"+readOnlyText+"</label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Add Text</label><div class=''><textarea class='textare"+val+"'onkeyup='getKeys("+val+")' onchange='getGenerateArray("+signname+","+val+")'>"+textareaDetails+"</textarea></div></div><hr></div><div class='col-md-12'><div class='form-group font-div'><label>Font</label><input type='text' id='font"+val+"' class='form-control font-size-box' onkeyup='SetFontSize("+val+","+signname+")' value='"+font+"'></div><hr></div><div class='col-md-12'><div class='form-group font-div'><div class=''>"+mins+"</div><label>Minimum Width</label></div><hr></div><div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div>"+temps+"</div><div class=''><button onclick='showHeaders("+signname+","+val+","+setText+")'> Create Rules</button></div></div><hr></div></div><hr>"+SenderTests+""+AssignTests+"<a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div>";
     
 
 }  
 
 if(key=='checkbox' || key=="checkboxs"){ 
     deleteids = val;
     var signname = '"checkbox"';
     var localid = 'checkboxs_'+val;
     var localid = '"'+localid+'"';
     groupname = 'cbox'+val;
     var selected = $('#signer_checkbox'+val).html();
         var titleRequired = $('#checkbox_required_'+val).prop('checked');
         
         if(titleRequired ==true){
             checRequired = 1;
              requiredTitle ="<input type='checkbox' checked='checked' name='checkbox_required"+val+"' id='checkbox_required_"+val+"'  onchange='getGenerateArray("+signname+","+val+")' onclick='gerRequired(this.value,"+val+","+signname+")' value='1'>";
         }else{
             checRequired = 0;
              requiredTitle ="<input type='checkbox' name='checkbox_required"+val+"' id='checkbox_required_"+val+"' onchange='getGenerateArray("+signname+","+val+")' onclick='gerRequired(this.value,"+val+","+signname+")' value='1'>";
         }
         var titleRead = $('#checkbox_read_'+val).prop('checked');
         if(titleRead ==true){
             checReadOnly = 1;
             readOnlyTitle ="<input type='checkbox' checked='checked' name='checkbox_read"+val+"' id='checkbox_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")' onclick='gerReadOnly(this.value,"+val+","+signname+")' checked='checked'>";
         }else{
             checReadOnly = 0;
             readOnlyTitle ="<input type='checkbox' name='checkbox_read"+val+"' id='checkbox_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")' onclick='gerReadOnly(this.value,"+val+","+signname+")'>";
         }
         var i=1;
         checkBoxYsnamic = '';
         var ctempid;
         var testid;
         var test = [];
         var textChecked = [];
         $.each($("input[name='cbox"+val+"']"), function(index,vals) {
             test[vals.id] = vals.value;
             textChecked[vals.id] = val.checked;
             
         });
         

         var  ConditionRules = '';
         $.each($("input[name='"+groupname+"']"), function() {
             
             subid = "'cid_"+val+i+"'";
             var tesztt  = test['cid_'+val+i];
             var chkBoxCheckOrNot =textChecked['cid_'+val+i];
           
             if(tesztt != undefined){
               tesztt =tesztt;
             }else{
               tesztt = '';
             } 
             if(chkBoxCheckOrNot ==true){
               chknull = 'checked="checked"'; 
             }else{
               chknull = ' ';
             }

             inputEmail ="'inputEmail"+val+i+"'";
             var tmpName  = "'checkbox'";
             checkBoxYsnamic += '<div class="mycheck'+val+''+i+'"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" id="dymic_'+val+""+i+'" '+chknull+' onclick="getOnClick('+tmpName+','+subid+',this.id,'+inputEmail+','+val+')"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail'+val+i+'" value="'+tesztt+'" placeholder="Checkbox value" onkeyup="getSetValue('+subid+',this.id,'+val+')"></div></div></div></div>';
             ctempid = i;
             testid = val+i;
             i++;
     
         });
         var setText = '"cid_'+val+ctempid+'"';
         $.each(headers,function(i,keys){
             newGlobalId = "checkboxs_"+val;
             newGlobalId = updatenewselecte.id;
             
             if( keys.SenderDivId==newGlobalId || keys.ReceiverDivId==newGlobalId ){
                 $.each(vishaldata,function (tempI,tempObj){	
                 
                     
                     if(keys.SenderDivId ==tempObj.id && keys.type=="checkbox" && keys.SenderDivId==newGlobalId ){
                         var params = '"'+keys.value+'"';
                         SenderTests +="<ul  class='assigntest7'><li>If this field is then "+params+" show: "+keys.opponent+" id "+keys.ReceiverDivId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
                     }
                     if(keys.ReceiverDivId ==tempObj.id  && keys.opponent == "checkbox"  && keys.ReceiverDivId==newGlobalId){
                         $('#'+keys.ReceiverDivId).addClass('Depending');
                         AssignTests +="<ul  class='assigntest8'><li>This field is conditional.Trigger: "+keys.opponent+" id "+keys.SenderId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
                         
                     }
                     
                 });
             }
         });
     
     html ="<div class='row' id='"+val+"'><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Checkbox Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"" + updatenewselecte.groupname + "\","+signname+","+val+");' id='signer_" + groupname + "' class='form-control'>"+selected+"</select></div></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Checkbox "+val+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label>"+requiredTitle+"</label>Required Field</div></div><div class=''><div class='form-group'><label>"+readOnlyTitle+"</label>Read Only</div></div></div><hr><div class='col-md-12'><div class='form-group'><label>Checkbox Value</label><div class='chkboxval"+val+"'>"+checkBoxYsnamic+"</div></div><hr></div>"+ConditionRules+"<div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div classs=''><button onclick='showHeaders("+signname+","+val+","+setText+")'> Create Rules</button></div></div></div>"+SenderTests+""+AssignTests+"<a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div>";	

 }
 
 if(key=='radios' || key =='radio'){ 
     deleteids = val;
     var signname = '"radios"';
     var localid = 'radios_'+val;
     var localid = '"'+localid+'"';
     var selected = $('#signer_radio'+val).html();
     groupname = 'radiogroup'+val;
     var titleRequired = $('#radios_required_'+val).prop('checked');
     
     if(titleRequired ==true){
         checRequired = 1;
          requiredTitle ="<input type='checkbox' checked='checked' name='radios_required_"+val+"' id='radios_required_"+val+"' onclick='gerRequired("+signname+","+val+")' onchange='getGenerateArray("+signname+","+val+")' onclick='gerRequired(this.value,"+val+","+signname+")' value='1'>";
     }else{
         checRequired = 0;
          requiredTitle ="<input type='checkbox' name='radios_required_"+val+"' id='radios_required_"+val+"' onchange='getGenerateArray("+signname+","+val+")' value='1'  onclick='gerRequired(this.value,"+val+","+signname+")'>";
     }
     
     var titleRead = $('#radios_read_'+val).prop('checked');
     if(titleRead ==true){
         checReadOnly = 1;
         readOnlyTitle ="<input type='checkbox' checked='checked' name='radiogroups"+val+"' id='radios_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")' checked='checked' onclick='gerReadOnly(this.value,"+val+","+signname+")'>";
     }else{
         checReadOnly = 0;
         readOnlyTitle ="<input type='checkbox' name='radiogroups"+val+"' id='radios_read_"+val+"' value='1' onchange='getGenerateArray("+signname+","+val+")' onclick='gerReadOnly(this.value,"+val+","+signname+")'>";
     }
     /*new added logic permission*/
     var i=1;
     checkBoxYsnamic = '';
     var ctempid;
     var test = [];
     var textChecked = [];
         $.each($("input[name='radiogroup"+val+"']"), function(index,vals) {
             test[vals.id] = vals.value;
             textChecked[vals.id] = val.checked;
             
         })
     
     $.each($("input[name='radiogroup"+val+"']"), function() {
         var previousR = $('#previousR').val();
         subid = "'radio_wrap_"+val+i+"'";
         var tesztt  = test['radio_wrap_'+val+i];
         var chkBoxCheckOrNot =textChecked['radio_wrap_'+val+i];
       
         if(tesztt != undefined){
           tesztt =tesztt;
         }else{ 
           tesztt = '';
         }
         var testsd = 'dymic_'+val+i;
         
         
         if(chkBoxCheckOrNot ==true && previousR == testsd){
           chknull = 'checked="checked"';
         }else{
           chknull = ' ';
         }

         inputEmail ="'inputEmail"+val+i+"'";
         var tmpName = "'radios'";
         checkBoxYsnamic += '<div class="mycheck'+val+''+i+'"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" id="dymic_'+val+""+i+'" '+chknull+' onclick="getOnClickRadio('+tmpName+','+subid+',this.id,'+inputEmail+','+val+')"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail'+val+i+'" value="'+tesztt+'" placeholder="Radio value" onkeyup="getSetValueRadio('+subid+',this.id,'+val+')"></div></div></div></div>';
         ctempid = i;
         i++;
     });
     var setText = '"cid_'+val+ctempid+'"';
           
          /* logic permission Set */
          var getAllGroupId=[];
          $.each(vishaldata,function (tempI,tempObj){
                  if(updatenewselecte.groupname ==tempObj.groupname){ 
                 getAllGroupId.push(tempObj.id);
                 }
          });
          
     $.each(headers,function(i,keys){
         newGlobalId = "radios_"+val;
                 if(getAllGroupId.indexOf(keys.SenderId)>-1 && (keys.type=="radio")){ 
                     
                     var params = '"'+keys.value+'"';
                     SenderTests +="<ul  class='assigntest2'><li>If this field is then "+params+" show: "+keys.opponent+" id "+keys.ReceiverDivId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
                 }
                 if(getAllGroupId.indexOf(keys.ReceiverDivId) >-1  && ( keys.opponent=="radio")){
                     $('#'+keys.ReceiverDivId).addClass('Depending');
                     AssignTests +="<ul class='assigntest1'><li>This field is conditional.Trigger: "+keys.opponent+" id "+keys.SenderId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
                 } 
         
         
     })
          
          /*end Logic permission Set*/
     html ="<div class='row' id='"+val+"'><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Radio Group</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"" + updatenewselecte.groupname + "\","+signname+","+val+");' id='signer_" + groupname + "' class='form-control'>"+selected+"</select></div></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Radio "+val+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label>"+requiredTitle+"</label>Required Field</div></div><div class=''><div class='form-group'><label>"+readOnlyTitle+"</label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Radio Value</label><div class='chkboxval"+val+"'>"+checkBoxYsnamic+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div classs=''><button onclick='showHeaders("+signname+","+val+","+setText+")'> Create Rules</button></div></div></div></div><hr>"+SenderTests+" "+AssignTests+"<a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div>";

 }
  
 if(key=='fields'){
     deleteids = val;
     var signname = '"fields"';
     var localid = 'caregiver_'+val;
      var localid = '"'+localid+'"';
         

     html ="<div class='row' id='"+val+"'><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Caregiver Look Up fields</label></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Caregiver "+val+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Dropdown</label><select class='select2 caregiverId"+val+" form-control' onchange='caregiverWiseChange(this.value,"+val+")'></select></div><hr></div><div class='col-md-12'><div class='form-group font-div'><label>Font</label><input type='text' id='font"+val+"' class='form-control font-size-box ' onkeyup='SetFontSize("+val+","+signname+")' value='"+temp+"'></div></div><hr></div><a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div><script>getAjax('"+fields_caregiver+"')</script>";	
     //setTimeout(() => {$('.select2').select2();$('.select2').trigger();	}, 1000);
     
 }
 if(key=='staffs' || key=='fields_statff'){
     deleteids = val;
     var signname = '"staffs"';
     var localid = 'staffs_'+val;
      var localid = '"'+localid+'"';
         

     html ="<div class='row' id='"+val+"'><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><i class='fa fa-check-square-o' aria-hidden='true'></i>&nbsp;&nbsp;<label>Applicant Look Up fields</label></div><hr><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Applicant "+val+"</div></div><hr></div><div class='col-md-12'><div class='form-group'><label>Dropdown</label><select class='select2 staffsId"+val+" form-control' onchange='staffWiseChange(this.value,"+val+")'></select></div><hr></div><div class='col-md-12'><div class='form-group font-div'><label>Font</label><input type='text' id='font"+val+"' class='form-control font-size-box ' onkeyup='SetFontSize("+val+","+signname+")' value='"+temp+"'></div></div><hr></div><a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div><script>getAjaxStaff('"+fields_staff+"')</script>";	
     //setTimeout(() => {$('.select2').select2();$('.select2').trigger();	}, 2000);
     
 }
 
 
 
 
 

 if(key=='dropdown'|| key=='dropdowsns'){ 
     deleteids = val;
     var signname = '"dropdown"';
     var localid = 'dropdowsns_'+val;
     var localid = '"'+localid+'"';
     var selected = $('#signer_dropdown'+val).html();
     var signRequired = $('#dropid'+val).attr('required');
 
     var selected = $('#signer_dropdown'+val).html();
     var signname = '"dropdown"';
     if(signRequired =='required'){
         required ="<input type='checkbox' class='' name='DropRequired' id='drop_required_"+val+"' onclick='gerRequired(this.value,"+val+","+signname+")' onchange='getGenerateArray("+signname+","+val+")' value='1' checked='checked'>";
         signRequired =1;
         
     }else{
         required ="<input type='checkbox' class='' name='signature"+val+"' id='drop_required_"+val+"' onclick='gerRequired(this.value,"+val+","+signname+")' onchange='getGenerateArray("+signname+","+val+")' value='1'>";
         signRequired =0;
         
     }
     var signRead = $('#dropid'+val).attr('readonly');

         if(signRead =="readonly"){
             signReadOnly=1;
             readOnly ="<input type='checkbox' class='minimal'  name='DropRead_"+val+"' id='drops_read_"+val+"' onclick='gerReadOnly(this.value,"+val+","+signname+")' onchange='getGenerateArray("+signname+","+val+")' value='1' checked='checked'>";
         }else{ 
         signReadOnly=0;
             readOnly="<input type='checkbox' class='minimal'  name='DropRead_"+val+"' id='drops_read_"+val+"' onclick='gerReadOnly(this.value,"+val+","+signname+")' onchange='getGenerateArray("+signname+","+val+")' value='1'>";
         }
         
         var temp_obj =[];
         var final_obj =[];
         var i=1;
         var AddmoreDynamic = '';
         
         $.each(final_array,function(index,vals){ 
             var  flag ='No';
             if(vals.id != undefined){ 
                 
                 if(vals.id == val){

                     final_obj.push('<option id= "remove_'+vals.mId+'" value="'+vals.value+'">'+vals.value+'</option>');
                     AddmoreDynamic += '<div class="copy_id" id="copy_id'+vals.mId+'"><div class="row"><div class="form-group" id="remove'+vals.mId+'"><label for="inputEmail3" class="col-md-2 control-label">Option</label><div class="col-md-9"><input type="text" class="form-control" id="inputEmail'+vals.mId+'"  onkeyup="getDropValue(this.id,'+vals.mId+','+val+')" value="'+vals.value+'"></div> <a href="javascript:void(0)" onclick="getRemove('+vals.mId+','+val+')"><i class="fa fa-times" aria-hidden="true"></i></a></div></div></div>';
                 }
                 i++;
             }
         });
     var setText = '"dropdowsns_"';
     
     $.each(headers,function(i,keys){
             newGlobalId = "dropdowsns_"+val;
             if(keys.SenderId =="dropdowsns_"+val && keys.type=="dropdown"){
                 var params = '"'+keys.value+'"';
                 SenderTests +="<ul  class='assigntest3'><li>If this field is then "+params+" show: "+keys.opponent+" id "+keys.ReceiverDivId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
             }
             if(keys.ReceiverDivId =="dropdowsns_"+val  && keys.opponent == "dropdown"){
                 $('#'+keys.ReceiverDivId).addClass('Depending');
                 AssignTests +="<ul  class='assigntest4'><li>This field is conditional.Trigger: "+keys.opponent+" id "+keys.SenderId+"</li><a href='javascript:void(0)' onClick='getLogicPermissionRemove("+i+")'>Remove</a></ul>";
             }
         })
         html = "<div class='row' id="+val+"><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><label>Dropdown</label></div><hr><div class='col-md-12'><div class='form-group'><label>Signer</label><select name='changesColor' onchange='setSigner(this.id,\"dropid" + val + "\","+signname+","+val+");' id='signer_dropdown"+val+"' class='form-control'>"+selected+"</select></div><hr></div><div class='col-md-12'><div class='form-group'><label>Data Label</label> <div class=''> Dropdown "+val+"</div></div><hr></div><div class='col-md-12'><div class=''><div class='form-group'><label>"+required+"</label>Required Field</div></div><div class=''><div class='form-group'><label>"+readOnly+"</label>Read Only</div></div><hr></div><div class='col-md-12'><div class='form-group'><span>Fill in the list of options.</span><div id='multid"+val+"'>"+AddmoreDynamic+"</div><a onclick='addmore("+signname+","+val+","+temp+")'><i class='fa fa-plus'></i>Add Option</a></div><hr></div><div class='col-md-12'><div class='form-group'><label>Default Option</label><select class='drops_"+val+" form-control' ><option>Select</option>"+final_obj+"</select></div><hr></div></div><div class='col-md-12'><div class='form-group'><label>Conditional Logic</label><div classs=''><button onclick='showHeaders("+signname+","+val+","+setText+")'> Create Rules</button></div></div></div></div><hr>"+SenderTests+" "+AssignTests+"<a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div>";	
 }
 if(key=='datesigned'){ 
     deleteids = val;
     var signname = '"datesigned"';
     var localid = 'datesigned_'+val;
     var localid = '"'+localid+'"';
     var signRequired =  $('#dates_required_'+val).prop('checked');
     if(signRequired ==true){
         required ="<input type='checkbox' class='' name='title_required' id='dates_required_"+val+"' onchange='getGenerateArray('datesigned',"+val+");' onclick='gerRequired(this.value,"+val+","+signname+")' value='1' checked='checked'>";
         signRequired =1;
         
     }else{
         required ="<input type='checkbox' class='' name='title_required' id='drop_required_"+val+"' onchange='getGenerateArray('datesigned',"+val+")' onclick='gerRequired(this.value,"+val+","+signname+")' value='1'>";
         signRequired =0;
         
     }
     var signRead = $('#dates_read_'+val).prop('checked');
     if(signRead ==true){
     signReadOnly=1;
         readOnly ="<input type='checkbox' class='minimal'  name='title_required' id='dates_read_"+val+"' onchange='getGenerateArray("+signname+","+val+")' value='1' checked='checked' onclick='gerReadOnly(this.value,"+val+","+signname+")'>";
     }else{ 
     signReadOnly=0;
         readOnly="<input type='checkbox' class='minimal'  name='title_required' id='dates_read_"+val+"' onchange='getGenerateArray("+signname+","+val+")' value='0' onclick='gerReadOnly(this.value,"+val+","+signname+")' >";
     }	
     html = "<div class='row' id="+val+"><div class='box box-solid'><div class='box-body'><div id='"+val+"'><div class='form-group'><label>Dropdown</label></div><hr><div class='col-md-12'><div class='form-group'><label>Receipt</label><select name='changesColor' onchange='setSigner(this.id,\"dropid" + val + "\","+signname+","+val+");' id='signer_dropdown"+val+"' class='form-control'>"+selected+"</select></div></div><hr><div class='col-md-12'><div class='row'><div class='form-group'><label>"+required+"</label>Required Field</div></div><div class='row'><div class='form-group'><label>"+readOnly+"</label>Read Only</div></div></div><hr><div class='col-md-12'><span>Fill in the list of options.</span><div id='multid"+val+"'>"+final_obj+"</div><a onclick='addmore("+signname+","+val+","+temp+")'><i class='fa fa-plus'></i>Add Option</a></div><div class='col-md-12'><label>Default Option</label><select class='drops_"+val+"' onchange='selectValue("+val+",this.value)'><option>Select</option></select></div></div><hr><a href='javascript:void(0)' onclick='getDeleteFields("+localid+","+deleteids+")'>Delete</a></div></div></div>";	
 }
 var tempread=[];

 $.each(vishaldata,function(index,val){  
     var logic = $('#logicid').val();
     
     if(logic ==0 || logic  ==''){
         var newId = updatenewselecte.id;
     
     }else{
         var newId = newGlobalId;
     }
     
 
     if(val.id == newId  || updatenewselecte.groupname==val.groupname){
         if(html !=""){
             val.Obj = html;
             
         }			
         
     }
     
     
     tempread.push(val);
     
     
 });

  vishaldata = tempread;
  
  $('#logicid').val(0);
}



 
 function caregiverWiseChange(val,id){
      var text  =  $('.caregiverId'+id+' option:selected').text();
     $('#caregivers_'+id).attr('placeHolder',text);
     $('#caregivers_'+id).attr('title',val);	
 
 }

 function staffWiseChange(val,id){
  
    var text  =  $('.staffsId'+id+' option:selected').text();
    $('#staffs_'+id).attr('placeHolder',text);
    $('#staffs_'+id).attr('title',val);	
 }
 




/*
*  Custom tools select Rotation
*/
$(".signer-tools").click(function(event){ 
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
 toastr.warning("Save rotation changes before editing document.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false});
 return false;
}
if (tool === "rotate") {
 if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
   toastr.warning("Save changes before rotating.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false})
 }else{
   rotatePage(pageNum);
 }
}else if(tool === "image"){
 $("#selectImage").modal({show: true, backdrop: 'static', keyboard: false});
}else if(tool === "delete"){
 deleteElement();
}else if(tool === "text"){
 enableTextMode();
}else if(tool === "font"){
 $(".right-bar.font-list").toggleClass("open");
}else if(tool === "symbol"){
 $(".right-bar.symbol-list").toggleClass("open");
}else if(tool === "shape"){
 $(".right-bar.shape-list").toggleClass("open");
}else if(tool === "fields"){
 if (auth) {
   $(".right-bar.fields-list").toggleClass("open");
 }else{
   loginRequired();
 }
}else if(tool === "input"){
 if (auth) {
   if (isTemplate === "Yes" || inviting) {
     $(".right-bar.input-fields-list").toggleClass("open");
   }else{
     notify("Template Only", "Inputs are added to templates only. Do you want to create a template copy of this file?", "warning", "Yes, Create", { showCancelButton: true, closeOnConfirm: true, callback: "createTemplate()" });
   }
 }else{
   loginRequired();
 }
}else if(tool === "color"){
 document.getElementById('color-picker').jscolor.show();
}else if(tool === "duplicate"){
 duplicateSelected();
}else if(tool === "signature"){
 enableSignatureMode();
}else if(tool === "draw"){
 enableDrawMode();
}else if(tool === "bold" || tool === "italic" || tool === "underline" || tool === "strikethrough" || tool === "alignright" || tool === "aligncenter" || tool === "alignleft"){
 styleText(tool);
}
});

/*Confirm message */

var ttesss=[];

function GetLoadComponents(){

 $.ajax({
             url:baseUrl+"template/esign-lookup-fields/"+document_key,
             type:"GET",
             
             success:function(response){ 
                 if(response !=''){
                     var json= JSON.parse(response); 
                     $.each(json,function(index,value){
                         var deletedId='';
                         var ids = value.id.split('_');
                         if(ids[0] =='sign'){
                             texts ='signature';
                             deletedId = ids[1];
                         }
                         
                         if(ids[0] =='text'){
                             texts ='text';
                             deletedId = ids[1];
                             
                         }
                         if(ids[0] =='fields'){
                             texts ='fields';
                             deletedId = ids[1];
                         }
                         if(ids[0] =='checkboxs'){
                             texts ='checkboxs';
                         }
                         
                         if(ids[0] =='radios'){
                             texts ='radios';
                         }
                         if(ids[0] =='staff'){
                             texts ='fields_staff';
                             deletedId = ids[1];
                             
                         }
                         
                         if(ids[0] =='caregiver'){
                             texts ='fields';
                             deletedId = ids[1];
                         }
                         if(ids[0]  =='datesigned'){
                             texts = 'datesigned';
                             deletedId = ids[1];
                         }
                         if(ids[0] =='dropdowsns'){
                             texts ='dropdowsns';
                             deletedId = ids[1];
                         }
                     
                         if(value.groupSmapleId != undefined && (ids[0] !='datesigned' && ids[0] !='staff' && ids[0] !='caregiver' && ids[0] !='text' && ids[0] !='dropdowsns')){
                             var deletedIds = value.groupSmapleId.split('_');
                             deletedId = deletedIds[1];
                         }
                         
                         ResponseList ={"tempId":ids[1],"id":value.id,"deleteids":deletedId,"groupname":value.groupNames,"type":texts,"Xpos":value.xPos,"Ypos":value.yPos,"Obj":value.obj,"Action":value.type,"page":value.page,"width":value.width,"height":value.height,"signer_id":value.signer_id,"signer_id":value.signer_id};
                         var  element = Object.assign({},ResponseList);
                         vishaldata.push(element);
                         if(value.conditionaRules != undefined){
                             ConditionalTempArray.push(value.conditionaRules);
                         }
                         if(removeScript ==""){
                             if(value.drops_valeus != undefined){
                                     ttesss.push(value.drops_valeus);
                             }
                         }
                     });
                     if(removeScript ==""){
                         $.each(ConditionalTempArray[0],function(i,l){ 
                             if(l.opponent =='dropdown' || l.opponent =='radio' ){
                                 $('#'+l.ReceiverDivId).addClass('Depending');
                             }else{
                                 $('#'+l.ReceiverId).addClass('Depending');
                             }
                             headers.push(l);
                         });
                         
                         if(ttesss !=undefined){
                             $.each(ttesss,function(i,ls){
                                 $.each(ls,function(index,obj){
                                     final_array.push(obj);
                                 })
                             })
                         }
                         console.log(final_array);
                     }else{ 
                         $.each(ConditionalTempArray[0],function(i,l){ 
                             if(l.opponent =='dropdown' || l.opponent =='radio' ){
                                 if(l.opponent =='radio' ){
                                     var explode = $('#'+l.ReceiverDivId).attr('groupName');
                                     var SenderExplode = $('#'+l.SenderId).attr('groupName');
                                     if(explode == SenderExplode){
                                         $("#"+l.ReceiverDivId).addClass('Depending');
                                     }else{
                                         $.each($("input[name='"+explode+"']"),function(i,ls){
                                             var TotalRadioId =  $(this).parent().attr('id');
                                             $("#"+TotalRadioId).addClass('Depending');
                                         })
                                     }
                                 }else{
                                     $('#'+l.ReceiverId).addClass('Depending');
                                 }
                             }else{
                                 if(l.opponent =='checkbox'){
                                     $('#'+l.ReceiverDivId).addClass('Depending');
                                 }else{
                                     $('#'+l.ReceiverId).addClass('Depending');
                                 }
                                 
                             }
                             headers.push(l);
                         });
                     }
                 }
             }
     });
}

/*lookup field get By Parameter Name */
var globalIntake;
function GetIntakeRequeestFileds(key,uid,tid){ 
globalIntake = key
   NewGetIntakeRequeestFileds(globalIntake,tid); 
 /*var urls = baseUrl+"/api/v1/intakeFieldsResponse";
   $.ajax({
       url:urls,
       type:"GET",
       data:{'fields':key,'user_id':uid,'MainId':main_intakeId},
       success:function(response){  
     
             $('#int'+tid).text(response);
             $('#int'+tid).attr('dataid',response);
             $('#'+tid).val(response);
         
       }
   });
*/
}
function NewGetIntakeRequeestFileds(key,tid){
     $.each(LookUpResponses,function(index,value){
         
         $.each(value,function(i,k){
             console.log(value);
             if(i == key){
                 console.log(tid);
                 $('#int'+tid).text(k);
                 $('#int'+tid).attr('dataid',k);
                 $('#'+tid).val(k);
             }
         })
             
     });
}

/*End lookup field get By Parameter Intake */

/*lookup field get By Parameter Name */

function GetCaregiverRequeestFileds(key,uid,tid){
 
var urls = baseUrl+"/api/v1/caregiverFieldsResponse";
   $.ajax({
       url:urls,
       type:"GET",
       data:{'fields':key,'user_id':uid},
       success:function(response){  
 
             $('#int'+tid).text(response);
             $('#int'+tid).attr('dataid',response);
             $('#'+tid).val(response);
         
       }
   });

}


/*End lookup field get By Parameter Intake */

/*delete FIelds */
function getDeleteFields(id,vals){ 
 var tempread=[];
 var total =1;
 var max = 1;
 id=updatenewselecte.id;
 var tempHeader=[];
 
 $.each(headers,function(i,lg){
     if(lg.SenderDivId !=id && lg.ReceiverDivId !=id){
         tempHeader.push(lg); 
         
     }
 });
 headers=tempHeader;
     
     
 
 
 $.each(vishaldata,function(index,val){
     
     if(val.id == id){
         val.Obj = '';
         $('#'+id).remove();
         $('#'+vals).remove();
         
     }
     if(val.id != id){
         tempread.push(val); 
         total++;
          max = Math.max(val.tempId, max);
     }
 });

 elemtcount =max+1;
 vishaldata = tempread;	
 
 /*start new added logic permission*/
 var ConditionsArray = [];
 
 $.each(headers,function(index,valss){
     
     if(valss.SenderId != id){
         ConditionsArray.push(valss);
         //total++;
     }
 });
 headers = ConditionsArray; 	
 
 /*end new added logic permission*/
 
 var DropdownDelete  =[];
 $.each(final_array,function(index,Drop){
     if(Drop.id != vals){
         DropdownDelete.push(Drop);
         //total++;
     }
 });
 final_array = DropdownDelete; 
 //getGenerateArray(updatenewselecte.type,updatenewselecte.deleteids);
     getGenerateArray(updatenewselecte.type,updatenewselecte.deleteids);
 
}

function getType(id, key,selectedValue) {
var response = '<option value="Signers">Defualt</option>';

var colors=['red','blue','gree','yellow','orange','blue','gree','yellow','orange']; 
 $.ajax({
 
     url: baseUrl + "getTypeByTemplate",
     type: "GET",
     data: {'id': document_key,"selected":selectedValue},
     success: function (response) {
     console.log(response);
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
             $('#signer_' + id).html(" ");
             $('#signer_' + id).append(response);
         }
     
         if (key == 'dropdowsns') {
             $('#signer_dropdown' + id).html(" ");
             $('#signer_dropdown' + id).append(response);
         }
         if (key == 'radios') {
         //alert(id)
             $('#signer_' + id).html("");
             $('#signer_' + id).append(response);
         }
         if (key == 'caregiver') { 
             $('#caregiverDrop' + id).html(" ");
             $('#caregiverDrop' + id).append(response);
         }

     }
 })

}
function setSigner(elementId,selectedElementId,key,normalid){
 
     var actions=[];
     var element=$('#'+elementId);
     if(key =='text'){
         var eid = "text_"+normalid;
     }
     if(key =='initial'){
         var eid = "initial_"+normalid;
     }
     if(key =='signature'){
         var eid = "sign_"+normalid;
     }
     if(key =='dropdown'){
         var eid = "dropdowsns_"+normalid;
     }
     if(key == 'checkbox'){  
         var element=$('#signer_'+selectedElementId);
         var color=$('option:selected', element).attr('data-style');
         $('input[group="multiplecheck'+normalid+'"]').css("background-color", color);
         var color=$('option:selected', element).attr('data-style');
         $('input[group="multiplecheck'+normalid+'"]').attr("signer_id",element.val());
         $('input[group="multiplecheck'+normalid+'"]').attr("background_color",color);
         var eid = 'checkboxs_'+normalid;
         
         $.each(vishaldata, function( index, value ) {	
           if(selectedElementId == value.groupname){
             value.signer_id = element.val();			
           }  
         });
     }else if(key =='radios'){ 
         var element=$('#signer_'+selectedElementId);
         var color=$('option:selected', element).attr('data-style');
         $('input[group="multipleradio'+normalid+'"]').css("background-color", color);
         var color=$('option:selected', element).attr('data-style');
     
         $('input[group="multipleradio'+normalid+'"]').attr("signer_id",element.val());
         $('input[group="multipleradio'+normalid+'"]').attr("background_color",color);
         var eid = 'radios_'+normalid;
         $.each(vishaldata, function( index, value ) {	
     
           if(selectedElementId == value.groupname){
 
             value.signer_id = element.val();			
           }
           
     });
     }else{
         
         
         var color=$('option:selected', element).attr('data-style');
         $('#'+selectedElementId).css("background-color", color);
  
         $('#'+selectedElementId).attr("signer_id",element.val());
         $('#'+selectedElementId).attr("background_color",color);
     }
     $.each(vishaldata, function( index, value ) {	
     
           if(eid == value.id){
 
             value.signer_id = element.val();			
           }
           
     });
     
// getGenerateArray(key,normalid);

}

function SetFontSize(id,name){
 var value = $('#font'+id).val();
 var numbers = /^[0-9]+$/;
 var texts = value.trim();
 if(texts !=''){
     if(numbers.test(texts)){
         if(name  === 'intake'){			
             $('#intakes_'+id).css({'font-size':value});
             $('#intakes_'+id).attr('font',value);
         }
         if(name  === 'text'){			
             $('#checks'+id).css({'font-size':value});
             $('#checks'+id).attr('font',value);
         }
         if(name  === 'fields'){			
             $('#caregivers_'+id).css({'font-size':value});
             $('#caregivers_'+id).attr('font',value);
         }
         if(name  === 'patient'){			
             $('#patients'+id).css({'font-size':value});
             $('#patients'+id).attr('font',value);
         }
     }else{
         alert("Only number allowed");
         
     }
     
     getGenerateArray(name,id,value);
 }
}
/*start new added logic permission*/
function getOnClick(key,id,MainId,txtId,CheckMainId){
 var checkId = $('#'+MainId).prop("checked");
 var text = $('#'+txtId).val();
 if(checkId == true){
     $('#'+id).prop("checked",true);
     $('#'+id).val(text);
 }else{
     $('#'+id).prop("checked",false);
     $('#'+id).val(text);
 }

 var countArrays = finalarray.length;
 
 if(countArrays > 0){
     var tempts = [];
     $.each(finalarray,function(ind,vals){ 
         
             if(id  == vals.id ){
                 vals.checked = checkId;
             }
       
     });
 

 }else{ 
     ResponseList ={"cid":MainId,"id":id,"checked":checkId,"type":key};	
     var  element = Object.assign({},ResponseList);
     finalarray.push(element);
 }
 getGenerateArray(key,CheckMainId);
}

function getOnClickRadio(key,id,MainId,txtId,CheckMainId){
 var checkId = $('#'+MainId).prop("checked");
 var text = $('#'+txtId).val();
 if(checkId == true){
     $('#'+id).prop("checked",true);
     $('#'+id).val(text);
 }else{
     $('#'+id).prop("checked",false);
     $('#'+id).val("");
 }
 var countArrays = RadioArray.length;
 if(countArrays > 0){
     var tempts = [];
     $.each(RadioArray,function(ind,vals){ 
         
         if(id  == vals.id ){
             vals.checked = checkId;
         }
        
     });
 }
 else { ResponseList ={"cid":MainId,"id":id,"checked":checkId,"type":key};	
     var  element = Object.assign({},ResponseList);
     RadioArray.push(element);
 } 
 getGenerateArray(key,CheckMainId);
}

function getSetValueRadio(id,MainId,CheckMainId){
 
 var text = $('#'+MainId).val();
 temp = [];
 if(text !=''){
     text = text;
 }else{
     text = '';
 }
$('#'+id).val("");
 $('#'+id).val(text);
 var countArray = RadioArray.length;

 if(countArray > 0){
     var tempTs = [];
     var flag='No';
     $.each(RadioArray,function(ind,val){
         
         if(val.id == id ){
           val.text =  text;
           flag='Yes';
         }else{
          
         }
     });
     if(  flag=='No'){
          ResponseList ={"cid":MainId,"id":id,"text":text};	
           var  element = Object.assign({},ResponseList);
           RadioArray.push(element);
     }

 }else{
     ResponseList ={"cid":MainId,"id":id,"text":text};	
     var  element = Object.assign({},ResponseList);
     RadioArray.push(element);

 }
 
getGenerateArray('radios',CheckMainId);
 
}

finalarray = [];
finalDrodown=[];
function getSetValue(id,MainId,CheckMainId){ 
 var text = $('#'+MainId).val();
 temp = [];
 if(text !=''){
     text = text;
 }else{
     text = '';
 }

 $('#'+id).val(text);
 var countArray = finalarray.length;

 if(countArray > 0){
     var tempTs = [];
     $.each(finalarray,function(ind,val){
         if(val.id == id ){
           val.text =  text;
         }else{
           ResponseList ={"cid":MainId,"id":id,"text":text};	
           var  element = Object.assign({},ResponseList);
           finalarray.push(element);
         }
     });

 }else{
     ResponseList ={"cid":MainId,"id":id,"text":text};	
     var  element = Object.assign({},ResponseList);
     finalarray.push(element);

 }
 tempsSelect = '<select name="" class="form-control testAssign" onblur="getAssignValues()" id="asd"><option value=""> Select Option</option>';
 $.each(finalarray,function(did,vals){
     tempsSelect += '<option value="'+vals.text+'">'+vals.text+'</option>';
 })
 tempsSelect += '</select>';
 finalDrodown = tempsSelect;
 
 
getGenerateArray("checkbox",CheckMainId);
}

var DynamicId;
var globalId;
var SenderDivId;
function showHeaders(key,id,text){

 $('#setheader_id').attr('style','');
 $('#saves_id').attr('style','display:none');
 //$('#leftside').attr('style','display:none');
 $('#leftside').css("z-index", '');
 
 $('#vishal123').attr('style','display:none');
 
 $('#txtId').attr('style','display:none;');
 $('#radiosid').attr('style','display:none;');
 $('#chkids').attr('style','display:none;');	
 $('#diid').attr('style','display:none;');	
 $('.testAssign_sub').attr('style','')
 
 /*Set Allready selected Element */
     $.each(headers,function(i,ds){
         if(ds.opponent =='text' || ds.opponent =='dropdown'){
             $('#'+ds.ReceiverDivId).removeClass('signer-element');
             $('#'+ds.ReceiverId).attr('disabled',true);
             $('#'+ds.ReceiverId).attr('title','unavailable this field is already conditional');
         }	
         if(ds.opponent =='checkbox' || ds.opponent =='dropdown'){
             $('#'+ds.ReceiverDivId).removeClass('signer-element');
             $('#'+ds.ReceiverId).attr('disabled',true);
             $('#'+ds.ReceiverId).attr('title','unavailable this field is already conditional');
         }	
     })
 
 
 /*End set allready selected element */
 
 var gid = text+""+id;

 DynamicId = id;
 $('#'+gid).addClass('colors');
 if(key =="text"){ 
 
     $('#txtId').attr('style','');
     $('.testAssign_sub').val("");;
     globalId = key+'_'+id;
     var temp1 = gid;
     SenderDivId = temp1;
     
 }
 if(key =='checkbox'){
     $('#chkids').attr('style','');
     $('#chkids').html(" ");
     
         var selected = '<select name="check" id="checkId" class="form-control testAssign_sub" onchange="getAssignValuess(this.value)"><option value="">Select Option</option><option value="checked">Checked</option><option value="unchecked">Unchecked</option></select>';
         
     $('.signer-builder .selected-element').each(function(index, value) {
         var temp = $(this).attr('id');
         globalId = temp;
         var temp1 = $(this).children().attr('id');
         SenderDivId = temp1;
         
         $('.textid').val($(this).attr('tempid'));
     });
         $('#chkids').append(selected);
 }if(key =='radios'){
 
     $('.signer-builder .selected-element').each(function(index, value) {
         var temp = $(this).children().attr('id');
         
         globalId = temp;
         var temp1 = $(this).attr('id');
     
         SenderDivId = temp1;
     });
     $('#radiosid').html("");
     var Radio  = RadioArray.length;
     
     var selected = '<select class="testAssign_sub form-control" onchange="getAssignValuessRadio(this.value)" ><option value="">Select Option</option>';
     /*if(Radio > 0){
         $.each(RadioArray ,function(i,keys){
             selected +='<option value="'+keys.id+'">'+keys.text+'</option>';
         })
     }else{ 
         var count = 1;
         
         $.each($("input[name='radiogroup"+id+"']"), function(objIndex,elemen) {
                     selected +='<option value="'+elemen.id+'">Radio'+count+'</option>';
             count++;
         })
         
     }*/
     var count = 1;
     var HelloArray = [];
     
     $.each($("input[name='radiogroup"+id+"']"), function(objIndex,elemen) {
         if(elemen.value !=''){
             values = elemen.value;
         }else{
             values = 'Radio'+count;
         }
         
             
         selected +='<option value="'+elemen.id+'">'+values+'</option>';
         count++;
         
         
         if(RadioArray.length ==0){
             
             ResponseList ={"cid":'',"id":elemen.id,"checked":"","type":"radios","value":values,"group":'radiogroup'+id};	
             var  element = Object.assign({},ResponseList);
             HelloArray.push(element);
         
         }
             
         });
         RadioArray =HelloArray;
         
     selected +='</select>';
     
     console.log(RadioArray);
     
     
     
     
     $('#txtId').attr('style','display:none;');
     $('#radiosid').attr("style",'');
     $('#radiosid').append(selected); 
 }if(key =='dropdown'){
     
     $('#drops'+id).addClass('colors');
     globalId = text+''+id;
     var temp1 = gid;
     SenderDivId = temp1;
     
     $('#diid').html("");
 
     
     /*dropdown Response */
         var selected = '<select id="dropsDown" class="testAssign_sub form-control" onchange="getAssignValuess(this.value)" ><option value="">Select Option</option>';
         $.each(final_array,function(i,vs){ 
                 if(vs.id == id){
                 selected +='<option value="'+vs.value+'">'+vs.value+'</option>';
             
             }
         });
         selected +='</select>';
         $('#diid').attr("style",'');
         $('#diid').append(selected);
     /*End Dropdown Response*/
 }
 
 $('#clickOrNotId').val(key);
 $('#sender_id').val(globalId);
 $('#assign_id').val(SenderDivId);
 
 /*only for radio button */
 $('.textid').val(id);
 /*end only for radio button*/
 $('#logicid').val(1); 
}



function getAssignValues(){
 var assign_value = $('.testAssign_sub').val();

 if(assign_value !=''){
     $('#assign_value').val(assign_value);
 }
}
function getAssignValuess(assign_value){
 
 if(assign_value !=''){
     $('#assign_value').val(assign_value);
     
 }
}
function getAssignValuessRadio(assign_value){
 
 if(assign_value !=''){
     $('#assign_value').val(assign_value);
     $('#sender_id').val( $("#"+assign_value).attr('id')  );
     $('#assign_id').val($("#"+assign_value).parent().attr('id')  );
     
     
 }
}

function CloseConditional(){
 var id = $('#assign_id').val();
$('#'+id).removeClass('colors');
 /* Start  Remove disabled  logic permission in close time */
 $.each(headers,function(i,ll){
     if(ll.opponent =='text'){
         $('#'+ll.ReceiverDivId).attr('disabled',false);
         $('#'+ll.ReceiverId).attr('title','');
         $('#'+ll.ReceiverDivId).addClass('signer-element');
     }
     if(ll.opponent =='checkbox' || ll.opponent =='dropdown'){
         $('#'+ll.ReceiverDivId).addClass('signer-element');
         $('#'+ll.ReceiverId).attr('disabled',false);
         $('#'+ll.ReceiverId).attr('title','');
     }
 });
 /* End  Remove disabled  logic permission in close time */
 $('#txtId').attr('style','');
 $('#radiosid').attr("style",'display:none;');
 $('#setheader_id').attr('style','display:none');
 $('#saves_id').attr('style','');
 $('#leftside').attr('style','');
 $('#vishal123').attr('style','');
 
}

function getLogicPermission(id){
 var logic =$('#logicid').val();
 if(logic ==1){
     $('#receiver_id').val(id);
 }
}
var glsID;
var SglsID;
var Oppenenet;
var tempId;
var sperate;
function successConditional(){  
 var testAssignsubNew = $('#assign_value').val();
 var testAssignsub = testAssignsubNew;
 var clickOrNotId = $('#clickOrNotId').val();
 var flag=1;
 if(clickOrNotId =='text'){
     var checkText = $('#seperate_value').val();
     if(checkText =='anyText'){
         testAssignsubNew ='anyText';
     }
 }
 if(testAssignsubNew !=''){ 

     $('.signer-builder .selected-element').each(function(index, value) {
         glsID= $(this).attr('id'); 
         SglsID = $(this).children().attr('id'); 
         Oppenenet =$(this).attr('type');
         
         if(Oppenenet =='checkbox' || Oppenenet =='radio'){
             tempId = $(this).attr('tempid'); 
         
         }
     });
 
     if(clickOrNotId =='text' || clickOrNotId =='dropdown'){
         var senderId = $('#sender_id').val();
         var senderDiv = $('#assign_id').val();
         var ttsdf =senderId;
         $('#'+senderDiv).removeClass('colors');
         if(clickOrNotId =='text'){
             sperate = $('#seperate_value').val();
             
         }
         var sameId1 = senderId;
         var sameId2 = glsID;
         
     }else {
         var senderId = $('#assign_id').val();
         var senderDiv = $('#sender_id').val();
         var sameId1 = senderDiv;
         var sameId2 = SglsID;
     }
     
     if(sameId1 == sameId2){
             return false;
     }
     if(clickOrNotId =='radios'){
         clickOrNotId = 'radio';
     }
     var NewObject = {'SenderId':senderId,"SenderDivId":senderDiv,'ReceiverDivId':glsID,"ReceiverId":SglsID,'value':testAssignsub,'type':clickOrNotId,'opponent':Oppenenet,"sperate":sperate};
     var ElementObject = Object.assign({},NewObject);
     headers.push(ElementObject); 

     $('#setheader_id').attr('style','display:none');
     $('#saves_id').attr('style','');
     $('#leftside').attr('style','');
     $('#vishal123').attr('style','');

     if(clickOrNotId =='text' || clickOrNotId =='dropdown'){
         var exp1 = senderId.split('_');
         var MainId = exp1[1];
     }if(clickOrNotId =='radios' || clickOrNotId =='checkbox'){
         var explode = $('.textid').val();
         var MainId =explode; 
     }
     
     getSenderArray(clickOrNotId,MainId);
 /*Receiver Opposite  */
     if(Oppenenet =='text' || Oppenenet =='dropdown'){
         var exp =glsID.split('_');
         var OppositeId = exp[1];
     }
     
     if(Oppenenet =='checkbox' || Oppenenet =='radio'){ 
         var explode = tempId.split('_');
     
         var OppositeId =explode[1]; 
     }
     getReceiptArray(Oppenenet,OppositeId);
     $('#seperate_value').val("");
     $('#assign_id').val("")
     $('.signer-element .selected-element').click()
      $('.textid').val("");
 }else{ 
     $('.testAssign_sub').attr('style','border:2px solid red !important')
 }	 
}

function getSenderArray(clickOrNotId,MainId){
 
 if(clickOrNotId =='radios'){
     clickOrNotId ='radios';
 }
 getGenerateArray(clickOrNotId, MainId);
 $('.signer-builder .selected-element').click()
}
function getReceiptArray(Oppenenet,OppositeId){
 if(Oppenenet =='radio'){
     Oppenenet ='radios';
 }
 getGenerateArray(Oppenenet, OppositeId);
 $.each(headers,function(i,ll){
     if(ll.opponent =='text'){
         $('#'+ll.ReceiverDivId).attr('disabled',false);
         $('#'+ll.ReceiverId).attr('title','');
         $('#'+ll.ReceiverDivId).addClass('signer-element');
     }
     if(ll.opponent =='checkbox' || ll.opponent =='dropdown'){
         $('#'+ll.ReceiverDivId).addClass('signer-element');
         $('#'+ll.ReceiverId).attr('disabled',false);
         $('#'+ll.ReceiverId).attr('title','');
     }
     if(ll.opponent =='radio'){
         $('#'+ll.ReceiverDivId).addClass('signer-element');
         $('#'+ll.ReceiverId).attr('disabled',false);
         $('#'+ll.ReceiverId).attr('title','');
     }
 });
 
 $('.signer-builder .selected-element').click()
 
}

$('.testAssign_sub').change(function(){ 
 var checkId = $('.testAssign_sub').val();
 
 if(checkId !=''){
     $('#assign_value').val(checkId);
 }
})

 
 function getLogicPermissionRemove(index){
     
     var tempHeader=[];
     var confirms = confirm("Are you sure want  remove this Logic permission?");
     if(confirms ==true){
         $.each(headers,function(i,lg){
             if(i !=index){
                 tempHeader.push(lg); 
                 
             }else{
                 
                 $("#"+lg.ReceiverId).removeClass("Depending");
                 $("#"+lg.ReceiverId).parent().removeClass("Depending");
             //getGenerateArray(updatenewselecte.type,updatenewselecte.tempId);
             }
     
         });
         headers=tempHeader;
         getGenerateArray(updatenewselecte.type,updatenewselecte.tempId);
         $('.signer-builder .selected-element').click()
     }
     
 }
 
 function GetIntakeRequeestFiledsReferral(key,uid,tid){
     
     globalIntake = key
     NewGetIntakeRequeestFileds(globalIntake,tid); 
     
 }
 
 function getMinWidth(key,id,textId){
     
     var minwidth = $('#minwidth'+id).is(":checked");
     if(textId ==1){
         textId='checks';
     }else{
         textId='intakes_';
     }
     if(minwidth ==true){
         
     $('#'+textId+id).addClass('heightClass');
     $('#'+textId+id).attr('heightId',1);
     }else{
         $('#'+textId+id).removeClass('heightClass');
         $('#'+textId+id).attr('heightId',0);
     }
     getGenerateArray(key,id);
 }