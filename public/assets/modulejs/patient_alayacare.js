var skillPage;
var visitPage;
var notesPage;
var skillEditDetails=[];
$(function() {
  //  getEmployeeNotesType();
    
    
    var start = moment().subtract('-6', 'days');
    var end = moment();


    $('#created_date1').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],

        }
    }, function(chosen_date, end_date) {

        $('#created_date1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
            getAlyacareEmployeeSchedular(1)
    })


});
var fieldsResponseArray = [];
function getAlyacareSkill(page=1){
    $('#loaderAlayaSkill').attr('style','');
    $.ajax({ 
        url: _ALAYACARESKILLS,
        type: "get",
        data: {
            id: _ALAYACAREID,
            agency_id:_AGENCYID,
            page:page
        },
        success: function(response) {
         
            
            $('#loaderAlayaSkill').attr('style','display:none');
           
            var json = (response.data.items !=undefined)?response.data.items:[];
       
            var responseHtml = '';
           
            if(json.length !=0){
                var cnt =1;
                if(response.data.page !=1){
                    cnt = (response.data.page *10)-9;
                }
                $.each(json,function(i,v){
                   
                    var flag ="";

                    if(v.fields.length ==0 && v.skillDetails?.length ==0){
                        var flag ="<a class='btn btn-danger'  onclick='skillOn("+v.id+")'>Off</a>";
                    }else if(v.fields.length ==0 && v.skillDetails?.length !=0 && v.skillDetails?.fields.length ==0){

                        var flag ="<a class='btn btn-success'  onclick='removeSkill("+v.id+")'>On</a>";
                    }else if(v.fields.length !=0 && v.skillDetails?.length !=0 && v.skillDetails?.fields?.length !=0){

                        var flag ="<a class='btn btn-success' onclick='editSkill("+v.id+")'>Edit</a><br><a onclick='removeSkill("+v.id+")'>Remove</a>";
                    }else {
                        flag ="<a href='javascript:void(0)'  data-toggle='modal' data-whatever='@mdo' class='btn btn-primary' onclick='addSkillId("+v.id+")'>Set</a>";
                    }

                    if(v.fields.length !=0){
                        fieldsResponseArray[v.id] = v.fields;
                    }

                    var dueDate = 'N/A';
                    if(v.fields.length !=0 && v.skillDetails && v.skillDetails.fields){
                        var hasExpiredDate = v.fields.some(function(f){ return f.name === 'expired_date'; });
                        if(hasExpiredDate && v.skillDetails.fields.expired_date){
                            dueDate = moment(v.skillDetails.fields.expired_date).format('MM/DD/YYYY');
                        }
                    }
                    
                    var currentDateFormatted = moment().format('MM/DD/YYYY');
                    var color="";
                    if(dueDate !="N/A"){
                        if(dueDate < currentDateFormatted){
                            color = "red";
                        }
                    }
                    responseHtml +='<tr style="background-color:'+color+'"><td>'+cnt+++'</td><td>'+flag+'</td><td>'+v.name+'</td><td>'+v.category.name+'</td><td>'+v.branch.name+'</td><td>'+dueDate+'</td></tr>';
                })

                $('#previousSkillId').attr('style','');
                 $('#nextSkillId').attr('style','');

                 skillPage=response.data.page;

                if(response.data.total_pages ==1){

                
                    $('#previousSkillId').attr('style','display:none');
                    $('#nextSkillId').attr('style','display:none');
                
                }else{
                    if(response.data.total_pages == response.data.page){
                    
                        $('#previousSkillId').attr('style','');
                        $('#nextSkillId').attr('style','display:none');
                    }
                }
            }else{
                responseHtml ='<tr><td colspan="7">No record available</td></tr>';
                $('#previousSkillId').attr('style','display:none');
                $('#nextSkillId').attr('style','display:none');
            }
            $('#alayacare_skill_id').html("");
            $('#alayacare_skill_id').html(responseHtml);
            

           
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

function previousSkill(){
    var nextPage = skillPage-1;
    if(nextPage !=0){
        getAlyacareSkill(nextPage);
    }
  
}

function nextSkill(){
    var nextPage = skillPage+1;
    getAlyacareSkill(nextPage);
   
}

function getAlyacareEmployeeSchedular(page=1){
    $('#loaderAlayaVisit').attr('style','');
    var id = _ALAYACAREID;
    var url =_ALAYACARESCHEDULAR;
   var  agency_id =_AGENCYID;
    
    var created_date1 = $('#created_date1').val();
    var selected = created_date1.split('-');
    $.ajax({
                    
        url: url,
        type: "GET",
        data: {
            start: selected[0],
            end: selected[1],
            id:id,
            page:page,
            record_type:_RECORD_TYPE,
            agency_id:agency_id
        },
        success: function(response) {
            $('#previousVisitId').attr('style','');
            $('#nextVisitId').attr('style','');
            $('#loaderAlayaVisit').attr('style','display:none');
            var json = (response.data.items !=undefined)?response.data.items:[];
            var responseHtml = '';
            if(json.length !=0){
                var cnt =1;
                if(response.data.page !=1){
                    cnt = (response.data.page *100)-99; 
                }
                $.each(json,function(i,v){
                    responseHtml +='<tr class="'+v.status+'"><td>'+cnt+++'</td><td>'+v.alayacare_visit_id+'</td><td>'+v.start_at+'</td><td>'+v.end_at+'</td><td>'+v.status+'</td><td><a href="javascript:void(0)" onclick="viewVisitDetails('+v.alayacare_visit_id+')"><i class="fa fa-eye status-eye-icon"></i></a><img src="'+loaderImages+'" class="trLoader" alt="loader" id="loaderAlayaVisit'+v.alayacare_visit_id+'" style="display: none; "></td></tr>';
                })


               
            }else{
                responseHtml ='<tr><td colspan="4">No record available</td></tr>';
                $('#previousVisitId').attr('style','display:none');
                $('#nextVisitId').attr('style','display:none');
            }
            $('#alayacare_visit_id').html("");
            $('#alayacare_visit_id').html(responseHtml);
            if(json.length !=0){
                visitPage=response.data.page;

                if(response.data.total_pages ==1){

                
                    $('#previousVisitId').attr('style','display:none');
                    $('#nextVisitId').attr('style','display:none');
                
                }else{
                    if(response.data.total_pages == response.data.page){
                    
                        $('#previousVisitId').attr('style','');
                        $('#nextSkillId').attr('style','display:none');
                    }
                }
            }
        }
    });
}

function previousVisit(){
    var nextPage = visitPage-1;
    if(nextPage !=0){
        getAlyacareEmployeeSchedular(nextPage);
    }
  
}

function nextVisit(){
    var nextPage = visitPage+1;
    getAlyacareEmployeeSchedular(nextPage);
   
}

function viewVisitDetails(visitId){
    var id = _ALAYACAREID;
    var url =_ALAYACAREVISITDETAILS;
    $('#loaderAlayaVisit'+visitId).attr('style','');
    
    $.ajax({
                    
        url: url,
        type: "GET",
        data: {
            
            id:id,
            visit_id:visitId,
            agency_id:_AGENCYID
          
        },
        success: function(response) {
            $('#loaderAlayaVisit'+visitId).attr('style','display:none');
            $('#exampleModal-visit-details').modal('show')
            var htmlResponse =`<div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Visit Id :</b> </label> ${response.data.alayacare_visit_id}
                  
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Patient Name :</b> </label> ${response.data.client.full_name}
                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Visit Start Date :</b> </label> ${response.data.start_at}
                  
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Visit End Date :</b> </label> ${response.data.end_at}
                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Status :</b> </label> ${response.data.status}
                  
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="recipient-name"><b>Service Name :</b> </label> ${response.data.service.name}
                    
                </div>
            </div>
        </div>`;
        $('#visit_details_id').html("")
        $('#visit_details_id').html(htmlResponse)

        }
    });
}

function getAlyacareEmployeeNotes(page=1){
    $('#loaderAlayaNotes').attr('style','');
    $.ajax({
                    
        url: _ALAYACAREEMPLOYEENOTESLIST,
        type: "GET",
        data: {
            
            id:_ALAYACAREID,
            page:page,
            agency_id:_AGENCYID
          
        },
        success: function(response) {
           
           
            $('#loaderAlayaNotes').attr('style','display:none');
            var json = (response.data.items !=undefined)?response.data.items:[];
            var responseHtml = '';
            if(json.length !=0){
                var cnt =1;
                if(response.data.page !=1){
                    cnt = (response.data.page *100)-99;
                }
                $.each(json,function(i,v){
                   
                    responseHtml +='<tr><td>'+cnt+++'</td><td>'+v.content+'</td><td>'+v.note_type+'</td><td>'+v.created_at+'</td><td>'+v.status+'</td></tr>';
                })
            }else{
                responseHtml ='<tr><td colspan="4">No record available</td></tr>';
                $('#previousNotesId').attr('style','display:none');
                $('#nextNotesId').attr('style','display:none');
            }
            $('#alayacare_notes_id').html("");
            $('#alayacare_notes_id').html(responseHtml);
            notesPage=response.data.page;
            if(json.length !=0){
                if(response.data.total_pages ==1){

                
                    $('#previousNotesId').attr('style','display:none');
                    $('#nextNotesId').attr('style','display:none');
                
                }else{

                    if(response.data.total_pages == response.data.page){
                    
                        $('#previousNotesId').attr('style','');
                        $('#nextNotesId').attr('style','display:none');
                    }
                }
            
            }
        }
    });  
}


function previousNotes(){
    var nextPage = notesPage-1;
    if(nextPage !=0){
        getAlyacareEmployeeNotes(nextPage);
    }
  
}

function nextNotes(){
    var nextPage = notesPage+1;
    getAlyacareEmployeeNotes(nextPage);
   
}

function getEmployeeNotesType(){
    $.ajax({
                    
        url: _ALAYACAREEMPLOYEENOTESTYPE,
        type: "GET",
        data: {
            id:_ALAYACAREID,
            agency_id:_AGENCYID
        },
        success: function(response) {
            var json = response.data;
            var jsonResponse = '<option value="">Select Notes Type</option>';
            if(json.length !=0){
                $.each(json,function(i,v){
                    jsonResponse +='<option value="'+v.note_type+'">'+v.name+'</option>';
                })
            }

            $('#alaya_notes_type_id').html("");
            $('#alaya_notes_type_id').html(jsonResponse);
        }
    });
}

function submitEmployeeNotes(){
    
    var alaya_notes_type = $('#alaya_notes_type_id').val();
    var content = $('#alaya_notes_id').val();
    var cnt =0;
    $('#alaya_notes_type_id_error').html('');
    $('#alaya_notes_id_error').html('');
    if(alaya_notes_type ==''){
        $('#alaya_notes_type_id_error').html('Please select Note Type');
        cnt =1;
    }
    if(content.trim() ==''){
        $('#alaya_notes_id_error').html('Please enter Note');
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $('#loaderAlayaNotesSubmit').attr('style','')
        $.ajax({
                    
            url: _CREATEALAYACAREEMPLOYEENOTES,
            type: "POST",
            data: {
                id:_ALAYACAREID,
                agency_id:_AGENCYID,
                '_token':_CSRF_TOKEN,  
                'note_type':$('#alaya_notes_type_id').val(),
                'content':$('#alaya_notes_id').val()
            },
            success: function(response) {
                $('#loaderAlayaNotesSubmit').attr('style','display:none');
                getAlyacareEmployeeNotes(1);
                clearEmployeeNotes();
              
            },error:function(jqr){
            
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
    
}

function clearEmployeeNotes(){
    $('.error').html("");
    $('#alaya_notes_type_id').val("");
    $('#alaya_notes_id').val("");
    $('#exampleModal-alaya-notes').modal('hide');
}

function addSkillId(id){
    var responseHtml = "";
    var json = fieldsResponseArray[id];
    var editSkillDetails = (!skillEditDetails[id])?[]:skillEditDetails[id];
  
    $('#skill_object_id').val(id);
    responseHtml = '<input type="hidden" name="method_type" id="method_id">';
    
    $.each(json,function(i,v){
        var type = 'date';
        if(v.type =='Text'){
            type ="text";
         
            var value=(editSkillDetails.length !=0)?editSkillDetails.fields[v.name]:"";
        }else{
            var value=  (editSkillDetails.length !=0)?moment(editSkillDetails.fields[v.name]).format('YYYY-MM-DD'):"";
        }
        var vsl ="";
        if(value !=undefined){
            vsl = value;
        }
        var label ="";
        if(v.label !=null){
            label ='<label for="recipient-name" class="col-form-label">'+v.label+'<span class="error">*</span>:</label>'
        }

        
        responseHtml +=`<div class="form-group">
                        ${label}
                        <input type="${type}" id="d${v.name}" name="${v.name}" class="form-control" placeHolder="${v.label}" value="${vsl}">
                        <span class="error" id="${v.name}_error"></span>
                    </div>`
    })

    $('#skill_update_id').html("");
    $('#skill_update_id').html(responseHtml);
    $('#exampleModal-alaya-skill').modal('show');
   
}

function updateEmployeeSkill(){
    
    var skill_id = $('#skill_object_id').val();
    var json = fieldsResponseArray[skill_id];
    var cnt =0;
    var fields={};
    var finalFields = '';
    $.each(json,function(i,v){
        var value = $('#d'+v.name).val();
        $('#'+v.name+'_error').html("");
        if(value.trim() ==''){
            if(v.type =='Text'){
                $('#'+v.name+'_error').html("Please enter "+v.label);
            }else{
                $('#'+v.name+'_error').html("Please select "+v.label);
            }
            cnt=1;
        }else{
           if(v.type =='Date'){
            var value1 = value+'T00:00:00+00:00';
           }else{
           var value1 = value;
           }
            fields[v.name] = value1;
            
        }
        
    })
    finalFields=fields

    var content = $('#alaya_skill_id').val();
    
    
    $('#alaya_skill_id_error').html('');
    
    if(content.trim() ==''){
        $('#alaya_skill_id_error').html('Please enter Comments');
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
       
      
        $('#loaderAlayaSkillSubmit').attr('style','');

        $.ajax({
             
            url: _ALAYACARESKILLUPDATES,
            type: "POST",
            data: {
                'skill_id': skill_id,'fields':finalFields,'_token':_CSRF_TOKEN,'id':_ALAYACAREID,'content':$('#alaya_skill_id').val(),'method_type':$('#method_id').val(),
                agency_id:_AGENCYID,
            },
           
            success: function(response) {
                $('#loaderAlayaSkillSubmit').attr('style','display:none');
                toastr.success(response.error_msg);
                clearEmployeeSkill()
                getAlyacareSkill(1);
                $('.close').click();
            },error:function(jqr){
            
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function getAlyacareDocument(){
    $('#loaderAlayaAttachmentList').attr('style','');
    $.ajax({
                    
        url: _DOCUMENTATTACHMENTUPLOADSLIST,
        type: "GET",
        data: {
            id:_ALAYACAREID,
            folder:_AGENCY_NAME,
            agency_id:_AGENCYID,
        },
        
        success: function(response) {
            $('#loaderAlayaAttachmentList').attr('style','display:none');
            var json = response.data.entries;
            var htmlResponse = '';
            if(json.length !=0){
                var cnt =1;
                $.each(json,function(i,v){
                    var file = "'"+v.name+"'";
                    htmlResponse +='<tr><td>'+cnt+++'</td><td><a download onclick="downloadAlayaAttachment('+file+')">'+v.name+'</td><td>'+v.last_modified+'</td><td><a download onclick="downloadAlayaAttachment('+file+')"><i class="fa fa-download"></i></a></td></tr>'
                })
                
            }else{
                htmlResponse +='<tr><td colspan="3">No record available</td></tr>'
            }

            $('#alayacare_document_list').html("");
            $('#alayacare_document_list').html(htmlResponse);
         
            
        },error:function(jqr){
            $('#loaderAlayaAttachmentList').attr('style','display:none');
            toastr.error(jqr.responseJSON.error_msg);
        }
    });
}

function submitEmployeeDocument(){
    var alaya_document = $('input[name="alaya_document"]').prop('files');
    var cnt =0;
    $('#alaya_document_error').html("");
    if(alaya_document.length ==0){
        $('#alaya_document_error').html("Document is Required");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        var formData = new FormData($('#alayacare_document_upload_id')[0]);
        formData.append('_token',_CSRF_TOKEN);
        formData.append('id',_ALAYACAREID);
        formData.append('folder',_AGENCY_NAME);
        formData.append('agency_id',_AGENCYID);
       

        $.ajax({
                    
            url: _DOCUMENTATTACHMENTUPLOADS,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.close').click();
                
                
            },error:function(jqr){
            
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function clearEmployeeDocument(){
    $('#alayacare_document_upload_id')[0].reset()
}


function clearEmployeeSkill(){
    $('.error').html("");
    $('#update_skill_details')[0].reset();
}

function removeSkill(skill_id){
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to remove this skill?',
        type:'blue',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                $.ajax({
                                    
                        url: _DELETEALAYASKILL,
                        type: "POST",
                        data: {
                            id:_ALAYACAREID,
                            'skill_id':skill_id,  
                            '_token':_CSRF_TOKEN, 
                            agency_id:_AGENCYID, 
                        },
                        success: function(response) {
                            toastr.success(response.error_msg);
                            getAlyacareSkill()
                            
                        },error:function(jqr){
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel:function(){}
        }
    });
    
}

function editSkill(id){
    $.ajax({
                    
        url: _EDITALAYASKILL,
        type: "GET",
        data: {
            id:_ALAYACAREID,
            'skill_id':id,  
            agency_id:_AGENCYID,
            
        },
        success: function(response) {
            var skillObject = {};

            skillObject['comment'] = response.data?.comment;
            skillObject['fields'] = response.data?.fields;
            skillEditDetails[response.data.skill_id] =skillObject;
            $('#alaya_skill_id').val(response.data.comment)
            
            addSkillId(response.data.skill_id);
            $('#method_id').val('edit');
        },error:function(jqr){
        
            toastr.error(jqr.responseJSON.error_msg);
        }
    });
}

function skillOn(id){
    $('#method_id').val('');
    $.confirm({
        title: 'Are you sure ?',
        content: 'you want to enabled this skill?',
        type:'blue',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action:function(){
                    $.ajax({
             
                        url: _ALAYACARESKILLUPDATES,
                        type: "POST",
                        data: {
                            'skill_id': id,'_token':_CSRF_TOKEN,'id':_ALAYACAREID,'method_type':$('#method_id').val(),'flag':'auto',
                            agency_id:_AGENCYID,
                        },
                    
                        success: function(response) {
                            toastr.success(response.error_msg);
                            getAlyacareSkill(1);
                        },error:function(jqr){
                           showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel:function(){}
        }
    });
    
}

function uploadToAlayaCare(id){
    $('#alayacare_upload_doc_id').val(id);
    $('#alayacare_skill_select_id').html('').trigger('change');
    $('#alayacare_skill_select_error').html('');
    $('#exampleModal-alayacare-upload').modal('show');
    initAlayacareSkillSelect2();
    fetchAlayacareSkillList();
}

function initAlayacareSkillSelect2(){
    if ($('#alayacare_skill_select_id').hasClass('select2-hidden-accessible')) {
        $('#alayacare_skill_select_id').select2('destroy');
    }
    $('#alayacare_skill_select_id').select2({
        placeholder: 'Select Skill',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#exampleModal-alayacare-upload')
    });

    $('#alayacare_skill_select_id').on('change', function(){
        renderSkillExpireDates();
    });
}

var skillDetailsCache = {};
var skillDetailsCache = {};
var existingSkillsDetails = [];
function renderSkillExpireDates(){
     var newArray = $('#alayacare_skill_select_id').val() || [];

    var removedSkills = existingSkillsDetails.filter(function (id) {
        return !newArray.includes(id);
    });
  
    removedSkills.forEach(function (skillId) {
        $('#skill_expire_row_' + skillId).remove();
    });

    var newSkill = newArray.filter(function (id) {
        return !existingSkillsDetails.includes(id);
    });
   
    existingSkillsDetails = newArray;
   
    let skillId = newSkill[0];
    let skillFields = allskilArray[skillId];

if (skillId != undefined) {

    let skillName = "";

    $('#alayacare_skill_select_id option:selected').each(function () {
        if (skillId == $(this).val()) {
            skillName = $(this).text();
        }
    });

    let container = $('#alayacare_skill_expire_dates_container');

    // Main skill row
    let html = `
        <div class="row mb-3 border p-2 rounded" id="skill_expire_row_${skillId}">
            <div class="col-12 mb-2">
                <h6 class="font-weight-bold">${skillName}</h6>
            </div>
    `;

    // Dynamic fields
    if (skillFields && skillFields.length > 0) {

        skillFields.forEach(function(field) {

            html += `
                <div class="col-md-4">
                    <div class=" form-group">
                    <label>${field.label}</label>
            `;

            // Date field
            if (field.type === "Date") {

                html += `
                    <div class="input-group">

                        <input
                            type="text"
                            id="${field.name}_row_${skillId}"
                            name="${field.name}"
                            class="form-control skill-expire-date"
                            data-skill-id="${skillId}"
                            placeholder="mm/dd/yyyy"
                            data-inputmask="'alias': 'datetime', 'inputFormat': 'mm/dd/yyyy'"
                        >

                        <div class="input-group-append">
                            <span class="input-group-text skill-loader-${skillId}" style="display:none;">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </div>

                    </div>
                `;
            }else{
                html += `<div >
                            <input type="${field.type}" id="${field.name}" name="${field.name}" class="form-control" placeHolder="${field.label}">
                            
                        </div>`
            }

            html += `<span id="${field.name}_${skillId}_error" style="color:red"></span></div></div>`;
        });
    }

    html += `
            <div class="col-12">
                <span id="skill_notes_${skillId}" class="text-muted"></span>
            </div>
        </div>
    `;

    container.append(html);

    // Inputmask initialize
    $('#skill_expire_row_' + skillId + ' .skill-expire-date').inputmask(
        "mm/dd/yyyy",
        {
            placeholder: "mm/dd/yyyy",
            clearIncomplete: true
        }
    );

    fetchSkillDetails(skillId);
}
    
}


function fetchSkillDetails(skillId){
   
    
    $('.skill-loader-' + skillId).show();

    $.ajax({
        url: _FETCH_SKILL_WISE_DETAILS,
        type: "GET",
        data: {
            id: _ALAYACAREID,
            skill_id: skillId,
            agency_id: _AGENCYID
        },
        success: function(response){
            $('.skill-loader-' + skillId).hide();
            skillDetailsCache[skillId] = response.data;
            
            if(response.data == "Employee skill relationship not found"){
                console.log("New Skill Relationship Found: " + response.data);
               
            } else {
                applySkillDetails(skillId, response.data);
                
                if(response.error_msg !=""){

                    $('#skill_notes_'+skillId).html('<strong>'+response.error_msg+'</strong>')
                    
                }
               
            }
        },
        error: function(xhr){
           
            $('.skill-loader-' + skillId).hide();
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function applySkillDetails(skillId, data) {

    $.each(allskilArray[skillId], function(i, field) {

        if (field.type === "Date") {

            let rawDate = data?.fields?.[field.name]??"";

            if (rawDate) {

                var formattedDate = moment(rawDate).format('MM/DD/YYYY');

                $('#' + field.name + '_row_' + skillId).val(formattedDate).trigger('input');
            }

        } else {

            $('#'+field.name).val(data?.fields?.[field.name]);
        }

    });
}
var allskilArray = [];
function fetchAlayacareSkillList(){
    $.ajax({
        url: _ALAYACARESKILLLIST,
        type: "GET",
        data: {
            agency_id: _AGENCYID
        },
        success: function(response){
            var items = (response.data != undefined) ? response.data : [];
            var options = '<option value="">Select Skill</option>';
            if(items.length != 0){
                $.each(items, function(i, v){
                    allskilArray[v.id] = v.fields;
                    options += '<option value="'+v.id+'">'+v.name+'</option>';
                });
            } else {
                options = '<option value="">No skills found</option>';
            }
            $('#alayacare_skill_select_id').html(options).trigger('change');
        },
        error: function(xhr){
            $('#alayacare_skill_select_id').html('<option value="">Failed to load skills</option>').trigger('change');
        }
    });
}

function submitUploadToAlayaCare(){
    var docId = $('#alayacare_upload_doc_id').val();
    var skillIds = $('#alayacare_skill_select_id').val();
    let temp = 0;

    $('#alayacare_skill_select_error').html('');
    if(!skillIds || skillIds.length == 0){
        $('#alayacare_skill_select_error').html('Please select at least one skill');
        temp++;
    }

    $('#loaderAlayacareUploadSubmit').removeClass('d-none');

    var skillDetailsArray = [];
    var fieldTypeLabelErrors = [];
    $.each(skillDetailsCache, function(index, skillId){
        
        let fieldsData = {};
        let fieldsTypeData = {};

        let cnt =1;
        let fieldTypeLabelError = {};
        $.each(allskilArray[index], function(i, field){
           
            let field_name = field.name;
            let value = $('#'+field.name).val();

            if(field.type === "Date"){
                let dateValue = "";
                if($('#'+field.name+ '_row_' + index).val() !=""){
                    dateValue = moment($('#'+field.name+ '_row_' + index).val(), "MM/DD/YYYY")
        .format("YYYY-MM-DDTHH:mm:ssZ"); 
                }
                value =dateValue;

            }
            fieldsData[field.name] = value;
            fieldTypeLabelError[field.name] = field.label;
        });
       skillDetailsArray.push({
            skill_id: index,
            fields: fieldsData
        });
        fieldTypeLabelErrors.push(fieldTypeLabelError);
    });
    
    $.each(skillDetailsArray,function(i,v){
        $.each(v.fields,function(s){
            $('#'+s+'_'+v.skill_id+'_error').html('')
            if(v.fields[s].trim() ==''){
                $('#'+s+'_'+v.skill_id+'_error').html(fieldTypeLabelErrors[i][s] +" is required");
                 temp++;
            }
        })
    })

    if(temp !=0){
        return false;
    }
    var formData = new FormData();
    formData.append('_token', _CSRF_TOKEN);
    formData.append('id', docId);
    formData.append('folder', _AGENCY_NAME);
    formData.append('agency_id', _AGENCYID);
    formData.append('is_new_skill_relationship',JSON.stringify(skillDetailsArray));
   
    $.ajax({
        url: _UPLOAD_DOCUMENT_FOR_ALAYACARE,
        type: "POST",
        data: formData,
         processData: false,
    contentType: false,
        success: function(response){
            $('#loaderAlayacareUploadSubmit').addClass('d-none');
            toastr.success(response.error_msg);
            $('#exampleModal-alayacare-upload').modal('hide');
        },
        error: function(xhr){
            $('#loaderAlayacareUploadSubmit').addClass('d-none');
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function clearAlayacareUpload(){
    $('#alayacare_skill_select_id').val(null).trigger('change');
    $('#alayacare_skill_select_error').html('');
    $('#alayacare_skill_expire_dates_container').html('');
    $('#loaderAlayacareUploadSubmit').addClass('d-none');
    skillDetailsCache = {};
}


function downloadAlayaAttachment(fileName){
    $.ajax({
                    
        url: _DOWNLOADALAYAATTACHMENTFILES,
        type: "GET",
        data: {
            'id':_ALAYACAREID,
            'folder':_AGENCY_NAME,
            'alaya_document':fileName,
            agency_id:_AGENCYID,
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response) {
            var explode = fileName.split('/');
          
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            
            link.download = explode[2];
            link.click();
        },error:function(jqr){
        
            toastr.error(jqr.responseJSON.error_msg);
        }
    });
}


$('#alaycare-popup_old').click(function() {
    $('#lnkhhx_alaycare_id')[0].reset();
    $('.token-input-list').remove()
    $('#hha_alaycare_id').html("");;
    $('#hha_alaycare_name').html("");
     $('.token-input-delete-token').click()
    alaycareFunctionOld();
});



function alaycareFunctionOld() {

    var urlToken = _SEARCH_ALAYACARE_DATA+"?alaycare_id=" + empId+'&agency_id='+_AGENCYID;

    $("#hha_alaycare_id").tokenInput(urlToken, {

        tokenLimit: 1,
        zindex: 9999,
        prePopulate: empId !== "" && empName !== "" ? [{
            id: empId,
            name: empName
        }] : [],
        onAdd: function(item) {

            var selectedAlaycareId = item.emp_id;
            var name = item.name;
            $('#hha_alaycare_id').val(selectedAlaycareId);
            $('#hha_alaycare_name').val(name);
        },
    });
}

function CloseEmployeePopup() {
    $('.hha_alaycare_id_error').html("");
    $('#lnkhhx_alaycare_id')[0].reset();
    $('.token-input-list').remove();
    $('.token-input-delete-token').click()
}

$('#update-alaycare-idold').click(function() {
    var alaycareId = $('#hha_alaycare_id').val();
    var name = $('#hha_alaycare_name').val();

    $('.hha_alaycare_id_error').html("");
    var cnt = 0;
    if (alaycareId == '') {
        // $('.hha_alaycare_id_error').html("Please Select Employee");
        // cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {

        $.ajax({
            type: "post",
            url: _UPDATE_ALAYACARE_DATA,
            data: {
                'patient_id': _RECORD_ID,
                'alyacare_id': alaycareId,
                'name': name,
                '_token': _CSRF_TOKEN
            },
            success: function(res) {

                toastr.success(res.error_msg);
                $('#lnkhhx_alaycare_id')[0].reset();
                $('#exampleModal-link-alaycare-id').modal('hide');
                $('.token-input-delete-token').click()
                $('#hhx_alaycare_id').html('');
                $('.token-input-list').remove();
                var fullName = 'N/A';
                if(res.data[0].alaycare_name !=null){
                    var fullName = res.data[0].alaycare_name + ' (' + res.data[0].alaycare_id + ')';
                }
                
                var patientId = res
                empId = res.data[0].alaycare_id;
                empName = res.data[0].alaycare_name;
                $('#hhx_alaycare_id').html(fullName);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        })
    }
});

function alayacareAjaxOld() {
    $('#branchdata').html('');
    $.ajax({
        url:_ALAYACARE_BRANCH_DATA,
        type: "get",

        success: function(response) {

            $.each(response.data.items, function(index, value) {
                var optionElement = $('<option>').attr('value', value.id).text(value.name);
                $('#branchdata').append(optionElement);
            });

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

    $('#alayacare-popup').modal('show');
    $('#groupdatadiv').hide();
}

$(document).on('change', '#alayacare-popup .modal-body select', function() {
    var selectedValue = $(this).val();
    getGroupbyBranchId(selectedValue);
});

function getGroupbyBranchIdOld(branchId) {
    if (branchId) {

        $('#groupdata').html('');
        $.ajax({
            url: _GET_ALAYACARE_GROUP_BY_BRANCH_ID,
            type: "get",
            data: {
                branchId: branchId,
            },
            success: function(response) {

                $.each(response.data.items, function(index, value) {
                    var optionElement = $('<option>').attr('value', value.id).text(value.name);
                    $('#groupdata').append(optionElement);
                });

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    } else {
        return false;
    }
}

function alayacareSubmitOld() {
    var branchId = $("#branchdata").val();
    var groupId = $("#groupdata").val();
    var patient_id = $('#alaycare-patient-id').val();


    if (branchId === "") {
        $('#branchIderror').html('please select Branch');
        return false;
    } else if (groupId === "") {
        $('#branchIderror').html('');
        $('#groupIderror').html('please select Group');
        return false;
    } else {
        $('#branchIderror').html('');
        $('#groupIderror').html('');
        var newforms = $('#alayacare-form-data').serialize();
        $.ajax({
            type: "post",
            url:_ALAYACARE_UPDATE,

            data: newforms,
            success: function(response) {
                $('#alayacare-popup').modal('hide');
                $("#alayacare-form-data")[0].reset();
                toastr.success(response.error_msg);
            },
            error: function(error) {
                toastr.success(response.error_msg);
            }
        });
    }
}

function clearDataModal() {
    $("#alayacare-form-data")[0].reset();
}


// $('#alaycare-client-popup').click(function(){
//     $('#lnkhhx_alaycare_client_id')[0].reset();
//     $('.token-input-list').remove()
//     $('#hha_alaycareclient_id').html("");
//     $('#hha_alaycareclient_name').html("");
//     $('.token-input-delete-token').click()
//     alaycareClientFunction();
// });

// var empClientId = _ALAYACARE_CLIENT_ID;
// var empClientName = _ALAYACARE_CLIENT_NAME;
// function alaycareClientFunction(){
    
//     var urlToken = _ALAYACARE_CLIENT+"?alaycare_id="+empClientId; 
    
//     $("#hha_alaycare_client_id").tokenInput(urlToken, {
        
//         tokenLimit: 1,
//         zindex: 9999,
//         prePopulate: empClientId !== "" && empClientName !== "" ? [{ id: empClientId, name: empClientName }] : [],
//         onAdd: function (item) {
            
//         var selectedAlaycareId = item.emp_id;
//         var name = item.name;
//             $('#hha_alaycare_client_id').val(selectedAlaycareId);
//             $('#hha_alaycare_client_name').val(name);
            
//         },
//     });
// }


// function CloseEmployeePopup(){
//     $('.hha_alaycare_id_error').html("");
//     $('#lnkhhx_alaycare_id')[0].reset();
//     $('.token-input-list').remove();
//     $('.token-input-delete-token').click()
// }


// $('#update-alaycare-client-id').click(function(){
//     var alaycareId =  $('#hha_alaycare_client_id').val();
//     var name =  $('#hha_alaycare_client_name').val();
    
//     $('.hha_alaycare_client_id_error').html("");
//     var cnt =0;
//     if(alaycareId ==''){
//         $('.hha_alaycare_client_id_error').html("Please Select Client");
//         cnt =1;
//     }
//     if(cnt ==1){
//         return false;
//     }else{
        
//         $.ajax({
//             type:"post",
//             url:_UPDATE_ALAYACARE_CLIENT_NAME,
//             data:{
//                 'patient_id':_RECORD_ID,
//                 'alyacare_id':alaycareId,
//                 'name':name,
//                 '_token':_CSRF_TOKEN
//             },
//             success:function(res){
             
//                 toastr.success(res.error_msg);
//                 $('#lnkhhx_alaycare_client_id')[0].reset();
//                 $('#exampleModal-link-alaycare-client-id').modal('hide');
//                 $('.token-input-delete-token').click()
//                 $('#hhx_alaycare_client_id').html('');
//                 $('.token-input-list').remove();
//                 var fullName = res.data[0].alaycare_name + ' (' + res.data[0].alaycare_id + ')';
//                 var patientId = res
//                 empId = res.data[0].alaycare_id;
//                 empName = res.data[0].alaycare_name;
//                 $('#hhx_alaycare_client_id').html(fullName);
//             },
//             error:function(xhr){
//                 toastr.error(xhr.responseJSON.message);
//             }
//         })
//     }

// });

function getAlyacareEmployeeDemographic(){
    $.ajax({ 
        url: _ALAYACARE_EMP_DETAILS_ID,
        type: "get",
        data: {
            id: _ALAYACAREID,
            agency_id:_AGENCYID,
            
        },
        success: function(res) {
            $('#loadertag121Demo').attr('style', 'display:none');

            var htmlResponse = "";
            if (res.data.length != 0) {
                var genderText = "Female";
                if(res.data[0].gender =='M'){
                    genderText = "Male";
                }
                htmlResponse += `<div class="col-md-4">
                        <dl class="">
                            <dt>FullName</dt>
                            <dd>${res.data[0].first_name} ${res.data[0].last_name}<br></dd>
                            
                            <dt> Date of Birth</dt>
                            <dd> ${moment(res.data[0].birthday).format('MM/DD/YYYY')??"N/A"}<br></dd>
                           <dt>Phone</dt>
                            <dd> ${res.data[0].phone_main ??"N/A"}<br></dd>
                        </dl>
                </div>
                <div class="col-md-4">
                    <dl class="">
                        <dt>Email</dt>
                        <dd>${res.data[0].email}<br></dd>
                       
                        <dt>Gender</dt>
                        <dd>${genderText} <br></dd>
                         
                        <dt>Status</dt>
                        <dd> ${res.data[0].status ??"N/A"}<br></dd>

                        
                        
                    </dl>
                </div>
                <div class="col-md-4">
                    <dl class="">
                        <dt>External ID</dt>
                        <dd>${res.data[0].uid}<br></dd>

                         <dt> Full Address</dt>
                        <dd>${res.data[0].address}, ${res.data[0].city},${res.data[0].state},${res.data[0].zip}<br></dd>

                    </dl>
                </div>`;

            }

            $('#alayacare_emp_demographic_det').html("")
            $('#alayacare_emp_demographic_det').html(htmlResponse)
        }
    });
}