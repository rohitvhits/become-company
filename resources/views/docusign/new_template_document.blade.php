
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
	<meta http-equiv='cache-control' content='no-cache'> 
<meta http-equiv='expires' content='0'> 
<meta http-equiv='pragma' content='no-cache'> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Caring">
    <link rel="icon" type="image/png" sizes="16x16" href="">
    <title>NyBest Medical documents online</title>
    <!-- Ion icons -->
	<link href="<?php echo URL::to('/');?>/assets/esign/bower_components/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL::to('/');?>/assets/esign/Ionicons/ionicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=B612+Mono:400,400i,700|Charm:400,700|EB+Garamond:400,400i,700|Noto+Sans+TC:400,700|Open+Sans:400,400i,700|Pacifico|Reem+Kufi|Scheherazade:400,700|Tajawal:400,700&amp;subset=arabic" rel="stylesheet">
    <!-- Bootstrap CSS -->
   
    <link href="<?php echo URL::to('/');?>/assets/esign/libs/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?php echo URL::to('/');?>/assets/esign/libs/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="<?php echo URL::to('/');?>/assets/esign/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
   
    <script src="<?php echo URL::to('/');?>/assets/esign/js/jscolor.js"></script>
	 <link href="<?php echo URL::to('/');?>/assets/esign/AdminLTE.min.css" rel="stylesheet">
	 <link href="<?php echo URL::to('/');?>/assets/esign/libs/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="<?php echo URL::to('/');?>/assets/esign/style1111.css?id=<?php echo time();?>" rel="stylesheet">
    <script src="<?php echo URL::to('/');?>/assets/esign/js/jquery-3.2.1.min.js"></script>
	<link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/plugins/font-awesome-4.6.3/font-awesome.min.css">
	<input type="hidden" class="siteURL" value="<?php echo URL::to('/');?>/">

	
	
</head>
<style>
html, body{
	overflow-y:hidden;
}
.margintop{
	margin-top:8%;
}
.backchages{
	background-color:#000
}
.teval{
	background-color:yellow;
}
.drips{
	background-color:yellow;
}
.signer-overlay-previewer{
	width:auto !important;
}
.signer-element[type="text"][group="input"], .signer-element[type="text"][group="field"]{
	border:0px !important;
}
.error{
	background-color:#fbe385 !important;
	box-shadow:none;
	border:0;
}
.writing-pad{
    box-shadow:none;
	border:0;
}
.heightClass{
	height:10px !important;
}
.ui-state-active{
    
    font-weight: normal;
    color: #ffffff;
}
.signer-element{
    padding:0.5px !important;
	
}
.signer-element.ui-draggable.ui-draggable-handle:hover {
cursor: move;
}
#leftside.stick {
    position: sticky;
    top: 0;
    margin: 60px 0 0;

    float: left;
}
#vishal123{
	    position: sticky;
    top: 100px;
}
.signer-overlay-actions{

    float: center;
    text-align: center;
}
.multiplecom{
	text-align: left;position: relative; right: 10px;top: 8px;
}
.PermissionId{
	
}
.Depending{
	background: repeating-linear-gradient(
  45deg,
  rgba(0, 0, 0, 0.2),
  rgba(0, 0, 0, 0.2) 10px,
  rgba(0, 0, 0, 0.3) 10px,
  rgba(0, 0, 0, 0.3) 20px
),
url(http://s3-us-west-2.amazonaws.com/s.cdpn.io/3/old_map_@2X.png);
}
#vishal123 ul {
    /* background: red; */
    padding: 0px 5px;
    list-style: none;
}
#vishal123 .form-group {
    padding: 0px;
    margin: 0;
}
#vishal123 hr {
    margin: 5px 0px;
}
.font-div {
    display: inline-flex;
}
.font-div .font-size-box {
    width: 30px;
    padding: 5px;
    height: 30px;
    margin-left: 14px;
}
div[type="checkbox"] {
    
	height: 11px !important;
	
}

.signeeddatenew{
	width: 100% !important;
}
</style>
<body>
<div class="content">

        <div class="page-title" style="overflow:visible;">
            <div class="pull-right page-actions">
                <button  class="btn btn-success btn-responsive launch-editor"><i class="ion-edit"></i>Manage Fields & Edit</button>
				<button  class="btn btn-primary" onclick="window.location.href='<?php echo URL::to('/');?>/template'">Back List</button>
					
            </div>
            <h3 class="title-responsive">Document</h3>
            <p class="text-muted"><?php echo $document->template_name;?></p>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="light-card document">
                    <div class="signer-document">
                                                <!-- open PDF docements -->
                        <div class="document-pagination">
                            <div class="pull-left">
                                <button id="prev" class="btn btn-default btn-round"><i class="ion-ios-arrow-left"></i></button>
                                <button id="next" class="btn btn-default btn-round"><i class="ion-ios-arrow-right"></i></button>
                                <span class="text-muted ml-15">Page <span id="page_num">0</span> of <span id="page_count">0</span></span>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default btn-round btn-zoom" zoom="plus"><i class="ion-plus"></i></button>
                                <button class="btn btn-default btn-round btn-zoom" zoom="minus"><i class="ion-minus"></i></button>

                            </div>
                        </div>
                        <div class="document-load">
                            <div class="loader-box"><div class="circle-loader"></div></div>
                        </div>
                   <input type="hidden" name="" id="tempids" value="">
                        <div class="text-center">
                            <div class="document-map"></div>
                            <canvas id="document-viewer" ></canvas>
                        </div>
                                            </div>
                </div>
            </div>
       
        </div>
    </div>
	<div class="signer-overlay" >
	
       <div class="signer-overlay-header">
            <div class="signer-overlay-logo">
                <a href="https://www.cdpasny.com"><img src="" class="img-responsive"></a>
            </div>
            <div class="signer-overlay-action" id="saves_id">
                <button class="btn btn-responsive btn-default close-editor-overlay"><i class="ion-ios-close-outline"></i> Close </button>
                <button class="btn btn-responsive btn-primary signer-save"><i class="ion-ios-checkmark-outline"></i> <span>Save</span> </button>
            </div>
			 <div class="signer-overlay-actions" id="setheader_id" style="display:none;">
                <div class="col-md-12">
					<div class="form-group">
                            <div class="col-md-5">
                                <label>Click on the fields to show when trigger field =</label>&nbsp;&nbsp;&nbsp;
                            </div>
							<div class="col-md-5">
								<div id="txtId">
								<div class="input-group input-group-lg">
								<input type="text" name="" class="form-control testAssign_sub" onblur="getAssignValues()"> 
									<div class="input-group-btn">
									  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="fa fa-cogs"></span></button>
									  <ul class="dropdown-menu">
										<li><a href="#" onclick="getTexts('SpecifiedText')">Specified Text</a></li>
										<li><a href="#" onclick="getTexts('anyText')"">AnyText</a></li>
										
									  </ul>
									</div>
									<!-- /btn-group -->
									
								  </div>
									
								</div> 
								<div id="chkids" style="display:none;">
									
								</div>
								
								<div id="radiosid" style="display:none;">
									
									
								</div>
								<div id="diid" style="display:none;">
								</div> 
							</div> <div class="col-md-2"><button class="btn btn-primary" onclick="successConditional()"> Done </button> <button class="btn btn-default" onclick="CloseConditional()"> Close </button></div>
						<!--All hidden parameter used for logic permission -->
						<input type="hidden" id="assign_id">
						<input type="hidden" id="assign_value">	
						<input type="hidden" id="seperate_value">							
						<input type="hidden" id="sender_id">
						<input type="hidden" id="receiver_id">
						<input type="hidden" id="logicid">
						<input type="hidden" id="clickOrNotId">
						<input type="hidden" id="previousR">
						<!--End -->

						<!--End -->							
					</div>
				</div>
            </div>
            
        </div>
     
		<input type="hidden" name="" class="prev">
		<input type="hidden" name="" class="next">
		<input type="hidden" name="" class="textid">
		<input type="hidden" name="" class="deselected">
			<input type="hidden" name="" id="totolat">
			<input type="hidden" name="" id="totalDrop">
			<input type="hidden" name="" id="totalRadio">
			<input type="hidden" name="" id="totalSign">
			<input type="hidden" name="" id="totalInitia">
			<input type="hidden" name="" id="mainCount" value="<?php echo $count;?>">
			
			
	
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-2 margintop" id="leftside" style="width:300px;">
				<h3>Action</h3>
					<div class="box box-solid">
						
						<div class="box-body no-padding">
						  <ul class="nav nav-pills nav-stacked" id="dragdiv">
							<li class="signer-tools signstatus" tool="signature" action="true"><a href="#">Signature</a></li>
							<!--<li class="signer-tools initial" tool="initial" action="true"><a href="#">Initial </a></li>-->
							<li class="signer-tools datesigned" tool="datesigned" action="true"><a href="#">Date Signed </a></li>
							
							<!--<li class="signer-tools signer-tools company" tool="company" action="true"><a href="#">Compnay </a></li>-->
							 
							<li class="signer-tools text" tool="text" action="true"><a href="#">Text </a></li>
							<li class="signer-tools checkboxs" tool="checkboxs" action="true"><a href="#">Checkbox</a></li>
							<li class="dropdowsns" tool="dropdowsns" action="true"><a href="#">Dropdown</a></li>
							<li class="signer-tools radios" tool="radios" action="true"><a href="#">Radio</a></li>
						
							
						  </ul>
						</div>
						<!-- /.box-body -->
					  </div>
					  <h3>Lookup Field</h3>
					  <div class="box box-solid">
						<div class="box-body no-padding">
							
							<ul class="nav nav-pills nav-stacked" id="dragdiv">
                                <?php if(strtolower($document->lookup_fields) =='caregiver') { ?>
									<li class="signer-tools fields_caregiver" tool="fields_caregiver" action="true"><a href="#"><?php echo ucfirst($document->lookup_fields);?> Field</a></li>
                                <?php }else if(strtolower($document->lookup_fields) =='applicant'){ ?>
                                    <li class="signer-tools fields_staff" tool="fields_staff" action="true"><a href="#">Applicant Field</a></li>
                                    <?php }
									else if(strtolower($document->lookup_fields) =='patient'){ ?>
										<li class="signer-tools fields_caregiver" tool="fields_caregiver" action="true"><a href="#"><?php echo ucfirst($document->lookup_fields);?> Field</a></li>
										<?php } ?>
                            </ul>
						</div>
						<!-- /.box-body -->
					  </div>
				</div>
				<div class="col-md-8" style="width:700px;">
					 <div class="signer-overlay-previewer light-card test1"></div>
				</div>
				<div class="col-md-2 margintop temp" id="vishal123"> 
				
			
			</div>
				 
			</div>
			
		</div>
       
        <div class="signer-overlay-footer">
            
        </div>
        <div class="signer-assembler"></div>
        <div class="signer-builder"></div>
    </div>


   
</body>
<!-- scripts -->

    <script src="<?php echo URL::to('/');?>/assets/esign/libs/dropify/js/dropify.min.js"></script>
   <script src="<?php echo URL::to('/');?>/assets/esign/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/js/simcify.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/libs/clipboard/clipboard.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/libs/select2/js/select2.min.js"></script>
   
    <script src="<?php echo URL::to('/');?>/assets/esign/js/jquery.slimscroll.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/libs/jcanvas/jcanvas.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/js/touch-punch.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/libs/jcanvas/editor.min.js"></script>
    <script src="<?php echo URL::to('/');?>/assets/esign/js/pdf.js"></script>
	

    <script type="text/javascript">
 
		var validationId = "1";
		var lookuptype ='<?php echo $document->lookup_fields;?>';
		var caregivers='';
		var staffs='';
		caregivers ='<?php echo URL::to('/');?>/lookup/caregiver';
		if(lookuptype =='caregiver'){
			
		}else if(lookuptype =='applicant'){
			staffs = '<?php echo URL::to('/');?>/lookup/staff';
		}
       var url = '<?php echo URL::to("/");?>/template/getpdfbyTemplateid?template_id=<?php echo $document->id;?>',
      // var url = '<?php echo URL::to("/");?>/dosusinguploads/docusign/<?php echo $document->upload_document;?>',
              isTemplate = 'Yes',
              postChatUrl = null,
              settingsPage =null,
              saveFieldsUrl =null,
              deleteFieldsUrl =null,
              getChatUrl = null,
              signDocumentUrl ='{{ url("template_send")}}',
              sendRequestUrl = '{{ url("/sendSignRequest")}}',
              createTemplateUrl = '',
              baseUrl = '<?php echo URL::to('/');?>',
			  careginer=caregivers,
              staff=staffs,
             
              auth = true;
              document_key = '<?php echo $document->id;?>';
			  permission:"permission";
			  counter=<?php echo $count;?>;
			  tokens="<?php echo csrf_token();?>";
			  PDFJS.disableWorker = true;
			PDFJS.workerSrc = '<?php echo URL::to('/');?>/assets/esign/js/signer.min.js?id=<?php echo time();?>';
        var signingKey = '<?php echo csrf_token();?>';
				var savedWidth = <?php if($savedWidth !=''){  echo $savedWidth; } else{ echo 799;}?>;
			
        var templateFields =<?php echo $templateFields;?>;

		var removeScript ='';
		var template_id = "<?php echo $document->id;?>"
    </script>


	<script>
	
		 function gerRequired(val,id,name){
			if(name =='text'){
				var textrequired  =$('#text_required_'+id).prop('checked');
					if(textrequired ==true){
						  $('#checks'+id).addClass('error');;
                          $('#checks'+id).attr("required", "required");
                          $('.signer-assembler #checks'+id).attr('vishalpatel',true);
					}else{
						 $('#checks'+id).removeClass('error');
                         $('#checks'+id).attr("required", false);
                         $('.signer-assembler #checks'+id).attr('vishalpatel',false);
					}
				
			}
			if(name =='checkbox'){
				var required = $('#checkbox_required_'+id).prop('checked');
				
				if(required ==true){ 
					$('.signer-builder .selected-element').each(function(index, value) {
						$('#'+$(this).children().attr('id')).attr("required", "required");
					});
					$('#cid_'+id).prop('checked',true);
					$('#cid_'+id).attr("required", "required");
				}else{
					var i=1;
					$.each($("input[name='cbox"+id+"']"), function() {
						$('#cid_'+id+""+i).attr("required", false);
						i++;
					})
				}
				
			}
			if(name =='radios'){
				var required = $('#radios_required_'+id).prop('checked');
				if(required ==true){ 
					$('.signer-builder .selected-element').each(function(index, value) {
							$('#'+$(this).children().attr('id')).attr("required", "required");
						});
					$('#radio_wrap_'+id).prop('checked',true);
					
					$('#radio_wrap_'+id).attr("required", "required");
					$('#radios_'+id).addClass('error');
				}else{
					var i=1;
					$.each($("input[name='radiogroup"+id+"']"), function() {
						$('#radio_wrap_'+id+""+i).attr("required", false);
					})
					$('#radio_wrap_'+id).prop('checked',false);
					$('#radio_wrap_'+id).attr("required", false);
					$('#radios_'+id).removeClass('error');
				}
				
			}
			if(name =='fields'){
				var required = $('#caregiber_patient_'+id).prop('checked');
				
				if(required ==true){
						$('#caregivers_'+id).addClass('error');
                        $('#caregivers_'+id).attr("required", "required");
                        $('.signer-assembler #caregivers_'+id).attr('vishalpatel',true);
				}else{
					$('#caregivers_'+id).removeClass('error');
					$('#caregivers_'+id).attr("required", false);	
					$('.signer-assembler #caregivers_'+id).attr('vishalpatel',false);
				}
				
            }
            
			 if(name =='dropdown'){
				var required = $('#drop_required_'+id).prop('checked');
				if(required ==true){
						$('#dropid'+id).addClass('error');
                        $('#dropid'+id).attr("required", "required");
                        
				}else{
						$('#dropid'+id).removeClass('error');
						$('#dropid'+id).attr("required", false);	
				}
            }
			
			
			
        }
	
        function gerReadOnly(val,id,name){
		
			if(name =='text'){
				var textrequired  =$('#text_read_'+id).prop('checked');
					if(textrequired ==true){
						$('#checks'+id).attr("readonly", true);
						  $('#checks'+id).prop("readonly", true);
					}else{
						 $('#checks'+id).prop("readonly", false);
						 $('#checks'+id).attr("readonly", false);
					}
					
			}
			if(name =='fields'){
				var readOnly = $('#caregiber_patient_read_'+id).prop('checked');
				if(readOnly ==true){
					$('#caregivers_'+id).prop("readonly", true);
				}else{
					$('#caregivers_'+id).prop("readonly", false);
				}
            }
          
			if(name =='dropdown'){
				var required = $('#drops_read_'+id).prop('checked');
				
				if(required ==true){
						$('#dropid'+id).attr("readonly", true);
						$('#dropid'+id).prop("readonly", true);
				}else{
					$('#dropid'+id).attr("readonly", false);
					$('#dropid'+id).prop("readonly", false);
				}
				
            }
			if(name =='checkbox'){
				var required = $('#checkbox_read_'+id).prop('checked');
				
				if(required ==true){  
					$('.signer-builder .selected-element').each(function(index, value) {
						$('#'+$(this).children().attr('id')).attr("readonly", "readonly");
						$('#'+$(this).children().attr('id')).prop('checked',true);
					});
			
				}else{
					var i=1;
					$.each($("input[name='checkbox_read"+id+"']"), function() {
						$('#cid_'+id+""+i).attr("readonly", false);
						$('#cid_'+id+""+i).prop('checked',false);
					})

				}
			
			}
			if(name =='radios'){
				
				var required = $('#radios_read_'+id).prop('checked');
				
				if(required ==true){ 
					$('.signer-builder .selected-element').each(function(index, value) {
							$('#'+$(this).children().attr('id')).attr("readonly", "readonly");
							$('#'+$(this).children().attr('id')).prop('checked',true);
						});
					
					
				}else{
					var i=1;
					$.each($("input[name='radiogroup"+id+"']"), function() {
						$('#radio_wrap_'+id+""+i).attr("readonly", false);
						$('#radios_read_'+id).prop('checked',false);
					})
					
					
				}
			}

        }
	</script>




    <script src="{{ asset('/assets/esign/js/app.js')}}"></script>
	<script src="{{ asset('/assets/esign/js/signer.js')}}?vvcanvas=<?php echo time();?>"></script>
    <script src="{{ asset('/assets/esign/js/render.js')}}?time={{ time()}}"></script>

	<script>
	//setTimeout(function(){ GetLoadComponent(); }, 3000);
		
       
		var final_array = [];
		var temsd = [];
		var static = [];
		var RadioArray=[];
		var updatenewselecte;
		 function addmore(key,val,temp){
			var timestamp = new Date().getTime();
            var htmls='';
				htmls +='<div class="copy_id" id="copy_id'+timestamp+'"><div class="row"><div class="form-group" id="remove'+timestamp+'"><label for="inputEmail3" class="col-md-2 control-label">Option</label><div class="col-md-9"><input type="text" class="form-control" id="inputEmail'+timestamp+'" onkeyup="getDropValue(this.id,'+timestamp+','+val+')"></div> <a href="javascript:void(0)" onclick="getRemove('+timestamp+','+val+')"><i class="fa fa-times" aria-hidden="true"></i></a></div></div></div>';
                
				$('#multid'+val).append(htmls);
				var element ={"id":val,'mId':timestamp,'maId':'dropdowsns_'+val,'response':'',"value":''};
				
				console.log(element);
				var  elements = Object.assign({},element);
				final_array.push(elements);
				
        		getGenerateArray(key,val);
		} 
         
        function getRemove(removeId,mainId){
			console.log(removeId);
            var confirm1=confirm("Are you sure move this row?");
            if(confirm1 ==true){
			
				$("#dropid"+mainId+" option[id='remove_"+removeId+"']").remove();
                    $('#remove'+removeId).remove();
					$('#copy_id'+removeId).remove(); 
                    $('#remove_'+removeId).remove();
					var test = localStorage.getItem(mainId);
					var id ='copy_id'+removeId;		
					var tempread=[];					
					$.each(final_array,function(index,vals){	
						if(vals.mId != removeId){
							var elemt = {"id":vals.id,"mId":vals.mId,'response':vals.response,"value":vals.value};
							var mail =  Object.assign({},elemt);
							console.log(mail);
						}
						if(mail !=undefined){
							tempread.push(mail);
						}						
					});
					
					final_array = tempread;
					
					
					getGenerateArray('dropdown',mainId);

            }

        }
         
        function getDropValue(Textid,countid,MainId){
			console.log(countid);
            var text = $('#'+Textid).val();
			
			var final = [];
            var dropsResponse = '';
			var keys = '';
            if(text !=''){
				$("#dropid"+MainId+" option[id='remove_"+countid+"']").remove();
				$(".drops_"+MainId+" option[id='remove_"+countid+"']").remove();
                dropsResponse = '<option id="remove_'+countid+'" value="'+text+'">'+text+'</option>';
				$('#'+Textid).val(text);
					$.each(final_array,function(index,vals){ 		
						if(vals.mId == countid && vals.id ==MainId){
							vals.value = text;
						}
						final.push(vals);
							
					});  
						
			final_array = final;
			
                $('.drops_'+MainId).append(dropsResponse);
				 $('#dropid'+MainId).append(dropsResponse);
				getGenerateArray('dropdown',MainId);
			}
			
			
        }
     
        function selectValue(id,val){
            $('#dropid'+id).append('<option value="'+val+'">'+val+'</option>');
        }
       
        /*Addmore of radio button option **/
        var radioGlobal = 1; 
		var tempStoreSelectedId="";
		var radioGroupId="";
        function getAppend(id,val,name ,bgcolor=null,signerid=null){ 
		            radioGlobal++;
            var clenght = $("input[name="+name+"]").length;
			
			if(bgcolor !=''){
				 bgcolor=bgcolor;
			}
			if(signerid !=''){
				 signerid = signerid;
			}

	//		radios++;
			var radiosID=id+""+radioGlobal; //new Date().getTime();
			radioGroupId=id;
			tempStoreSelectedId=id+""+radioGlobal;
            $('<div class="signer-element selected-element radiogroup'+id+'" tempid="radiogroup_'+id+'" type="radio" page="'+pageNum+'" status="drop"    id="radios_'+radiosID+'"><input groupsName="radiogroup'+id+'" tempIds="radiogroup_'+id+'" type="radio" style="color:red;" name="radiogroup'+id+'" class="radio_wrap" id="radio_wrap_'+id+''+radioGlobal+'" value="Radio'+radioGlobal+'" group="multipleradio'+id+'" backgound_color="'+bgcolor+'" signer_id="'+signerid+'"><br></div></div>').appendTo(".signer-builder");
            var css = $('#radios_'+radiosID).attr('style');
            var main = css+"background-color:red;";
			clenght++;
            $('#radios_'+radiosID).attr('style',main);
            $('.next').val(radioGlobal); 
			
			
			
		
			//createRadioResponse('radios',radioGlobal,radios);
        }
           /*Addmore of radio button option **/
       var CheckboxGlobal=1;
		var CtempStoreSelectedId="";
		var CheckGroupId="";
        function getCheckAppend(id,val,name,bgcolor=null,signerid=null){  
			var temp =$("input[name="+name+"]").length;
			

			CheckboxGlobal = temp+1;
           
		   if(checkTotal ==undefined){  
			    checkTotals = id;
		   }else{
			   checkTotals = checkTotal;
		   }
		  
			if(bgcolor !=''){
				bgcolor=bgcolor;
			}
			if(signerid !=''){
				signerid = signerid;
			}
			
			var CheckID=id+""+CheckboxGlobal; //new Date().getTime();
			CheckGroupId=id;
			CtempStoreSelectedId=id+""+CheckboxGlobal;
			
			$('<div class="signer-element"  tempId="checkgroup_'+id+'" type="checkbox" status="drop" page="'+pageNum+'"  id="checkboxs_'+CheckID+'"><input  tempIds="checkgroup_'+id+'" type="checkbox" class="checkbox_wrapper" groupsName="cbox'+checkTotal+'" tempIds="checkgroup_'+checkTotal+'" name="cbox'+checkTotals+'" id="cid_'+checkTotals+CheckboxGlobal+'" value="'+CheckboxGlobal+'" group="multiplecheck'+id+'" backgound_color="'+bgcolor+'" signer_id="'+signerid+'"></div><br>').appendTo(".signer-builder");
			var css = $('#checkboxs_'+CheckID).attr('style');
			var main = css+"background-color:red;";
			$('#checkboxs_'+CheckID).attr('style',main);
            
            var DynamicAddmoreResponse =[];
			var globalAdd='';
			globalAdd += '<div class="mycheck'+checkTotals+''+CheckboxGlobal+'"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" onclick="getOnClick()"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail3" placeholder="Checkbox value"></div></div></div></div>';
			
			$('.chkboxval'+id).append(globalAdd)
          
		
        }
     
        
      
        /* End Add More of radio button option */
	</script>
	<!-- custom scripts -->	
	<script>
		$('.signer-overlay').scroll(function(){
			
			var distance = $('.signer-overlay').scrollTop();
			var left = document.getElementById("leftside");
			if(distance  > 5) {
				 left.className = 'col-md-2 margintop stick';
				}else{
					left.className = 'col-md-2 margintop';
				}
				
			
			var rights = document.getElementById("vishal123");
			if(distance  > 5) {
				 rights.className = 'col-md-2 margintop temp stick';
				}else{
					rights.className = 'col-md-2 margintop temp';
				}
			
		});
		
		function getTexts(value){
			
			$('.testAssign_sub').attr('disabled',false);
			if(value =='anyText'){
				$('.testAssign_sub').attr('disabled',true);
			}
			$('#seperate_value').val(value);
		}
		
		  
</script>
<script>
	var getCheckbox="{{ route('get-form-by-checkbox') }}";
	var getRadio="{{ route('get-form-by-radio') }}";
</script>
