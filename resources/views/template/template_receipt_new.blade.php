@include('include/header_lte')

<link href="<?php echo URL::asset("/"); ?>assets/css/jquery.dataTables.css" rel="stylesheet"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
.loader{
	float:left;
}
    .tree, .tree ul {
        margin:0;
        padding:0;
        list-style:none
    }
    .tree ul {
        margin-left:1em;
        position:relative
    }
    .tree ul ul {
        margin-left:.5em
    }
    .tree ul:before {
        content:"";
        display:block;
        width:0;
        position:absolute;
        top:0;
        bottom:0;
        left:0;
        border-left:1px solid
    }
    .tree li {
        margin:0;
        padding:0 1em;
        line-height:2em;
        color:#369;
        font-weight:700;
        position:relative
    }
    .tree ul li:before {
        content:"";
        display:block;
        width:10px;
        height:0;
        border-top:1px solid;
        margin-top:-1px;
        position:absolute;
        top:1em;
        left:0
    }
    .tree ul li:last-child:before {
        background:#fff;
        height:auto;
        top:1em;
        bottom:0
    }
    .indicator {
        margin-right:5px;
    }
    .tree li a {
        text-decoration: none;
        color:#369;
    }
    .tree li button, .tree li button:active, .tree li button:focus {
        text-decoration: none;
        color:#369;
        border:none;
        background:transparent;
        margin:0px 0px 0px 0px;
        padding:0px 0px 0px 0px;
        outline: 0;
    }
    .error{
        color:red;
    }
.temps {
	margin-right:20px;
	margin-bottom:20px;
}
.ppdexpire_date{
	margin-left: -8px;
    width: 141px!important;
}
.ppdexpire_date1{
	    width: 127px;
    margin-left: -21px;
    float: left;
}
</style>
        <script src="<?php echo URL::to('/'); ?>/lte/bower_components/jquery/dist/jquery.min.js"></script>

<script src="<?php echo URL::to('/'); ?>/lte/bower_components/chosen/chosen.jquery.js"></script>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/lte/bower_components/chosen/chosen.css" />

<div class="content-wrapper"> 
    <div class="fluid-container"> 


        <div class="content">
            <div class="form-box-fff card-box">
                <div class="title-h3">
                    <!--<h1 id="fa" style="margin-left: 5%;color:#004080;"> </h1>-->
                </div>

                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box" >
                                <div class="box-body">
                                    
                                    <div class="form-group">
									<label>Filters</label>
                                        <select class='form-control chzn-select' id="total_id">
                                            <option value="">select</option>
                                            <option value="ccode">Caregiver Code</option>
                                            <option value="cname">Caregiver Name</option>
                                            <option value="cemail">Caregiver Email</option>
                                            <option value="cstatus">Status</option>
                                            <option value="cppddate">PPD Expire Date </option>
                                            <option value="cpdate">Physical Expire Date</option>
                                        </select>
                                        <span style="color:red" id='select_error'></span>
                                    </div>
                                    <div class="form-group">
                                        <div class="total_ids">

                                        </div>
                                    </div>
                                    <input type="button" value="Apply" class="btn btn-primary" onclick="getResponse();">

                                </div>




                            </div>


                        </div>
                        <div class="col-md-8 no-padding">
                            <div class="box">
				<form action="<?php echo URL::to('/');?>/templet/sent" method="post" enctype="multipart/form-data" id="submitid">
                               <div class="box-header with-border">
								  <h3 class="box-title">Caregiver Details</h3>

								  <div class="box-tools pull-right">
									<img src="https://www.cdpasny.com/assets/ajax-loader.gif" class="loader" data-pagespeed-url-hash="2547725156" style="display: none;"><input type="submit" value="Sent" class="btn btn-primary">
								  </div>
								</div>

								
								<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
								<input type="hidden" name="templete_id" value="<?php echo $query->id;?>">
								<input type="hidden" name="ccaregiver_code" class="ccodeid">
								<input type="hidden" name="ccaregiver_email" class="ccaregiveemail">
								<input type="hidden" name="ccaregiver_name" class="cnamei">
								<input type="hidden" name="status_name" class="status_class">
								<input type="hidden" name="between_id_ppd" class="between_id_ppd">
								<input type="hidden" name="ppd_date" class="ppd_dates">
								<input type="hidden" name="ppd_date1" class="ppd_dates1">
								<input type="hidden" name="between_ids_physical" class="between_ids_physical">
								<input type="hidden" name="physicals_date" class="physicals_dates">
								<input type="hidden" name="physicals_date1" class="physicals_dates1">
                                                                
								
							<div class="table-responsive">                              
							  <table class="table table-bordered" style="display:none;" id="testval">
                                    <thead>
                                    <th><input type="checkbox" name="checkbox" id="checkid"></th>
                                    <th>CaregiverCode</th>
                                    <th>Caregiver Name</th>
                                    <th>Caregiver Email</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>PPD Expire Date</th>
                                    <th>Physical Expire Date</th>
									<th>Action</th>
                                    </thead>
                                    <tbody id="table_response"></tbody>
									
										
                                </table>
								
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</section>

            </div>
        </div>
    </div>
</div>


<script>

    
</script>

<script src="<?php echo URL::asset("/"); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/assets/fancybox-master/dist/jquery.fancybox.min.css">
<script src="<?php echo URL::to('/');?>/assets/fancybox-master/dist/jquery.fancybox.min.js"></script>
<script>

$('#total_id').change(function (e) {
    var id = $('#total_id').val();
    var name = $('#total_id option:selected').text();
    var temp = [];
    temp = '<div class="col-md-12 no-padding" id="c'+id+'">';
    if (id == 'ccode') {
        temp += "<div class='form-group'>" + name + "&nbsp;&nbsp;<input type='text' name='caregiver_code' id='careid' class='form-control'><a href='javascript:void(0)' onclick=getRemove('ccode','careid')><i class='fa fa-close'></i></a><span id='caregivercode_error' class='error'></span></div>";
    }
    if (id == 'cname') {
        temp += "<div class='form-group'>" + name + "&nbsp;&nbsp;<input type='text' name='caregiver_code' id='cnameid' class='form-control'><a href='javascript:void(0)' onclick=getRemove('cname','cnameid')><i class='fa fa-close'></i></a><span id='caregivername_error' class='error'></span></div>";
    }
    if (id == 'cemail') {
        temp += "<div class='form-group'>" + name + "&nbsp;&nbsp;<input type='text' name='caregiver_code' id='cemailid' class='form-control'><a href='javascript:void(0)' onclick=getRemove('cemail','cemailid')><i class='fa fa-close'></i></a><span id='caregiveremail_error' class='error'></span></div>";
    }
    if (id == 'cstatus') {
        temp += "<div class='form-group'>" + name + "&nbsp;&nbsp;<select class='form-control chzn-select1' id='status_id'><option value='all'>All</option><option value='Inactive'>Inactive</option><option value='Active'>Active</option></select><a href='javascript:void(0)' onclick=getRemove('cstatus','status_id')><i class='fa fa-close'></i></a><span id='status_error' class='error'></span></div>";
		
    }
    if (id == 'cppddate') {
        
        temp +=  "<div class='form-group'>" + name + "<br/> <div class='col-md-4 no-padding'><select class='form-control' id='between_id' onchange='getChanges(this.value)'><option value=''>Select Expression</option><option value='='>=</option><option value='<'><=</option><option value='>'>>=</option><option value='BETWEEN'>BETWEEN</option></select><span id='between_error' class='error'></span></div><div class='col-md-4 ppdexpire_date'><input type='text' id='testingid' class='ppd_expire_date form-control'><span id='ppd_expireerror_error' class='error'></span></div><div class='col-md-4 ppdexpire_date1' style='display:none' id='btnid'><input type='text' name=''  class='ppd_expire_date1 form-control'><span id='ppd_expireerror1_error' class='error'></span></div></div><a href='javascript:void(0)' onclick=getRemove('cppddate','between_id')><i class='fa fa-close'></i></a><br><br>";
		
    }
    if (id == 'cpdate') {
		 temp +=  "<div class='form-group'>" + name + "<br/> <div class='col-md-4 no-padding'><select class='form-control' id='between_ids' onchange='getChangess(this.value)'><option value=''>Select Expression</option><option value='='>=</option><option value='<'><=</option><option value='>'>>=</option><option value='BETWEEN'>BETWEEN</option></select><span id='betweens_error' class='error'></span></div><div class='col-md-4 ppdexpire_date'><input type='text' name='' class='physical_date form-control'><span id='physica_date_error' class='error'></span></div><div class='col-md-4 ppdexpire_date1' style='display:none' id='btnids'><input type='text' name=''  class='physical_date1 form-control'><span id='physica_date1_error' class='error'></span></div></div><a href='javascript:void(0)' onclick=getRemove('cpdate','between_ids')><i class='fa fa-close'></i></a><br><br>";
		
		
        
    }
    temp += '</div>';
    console.log(temp)
    $('.total_ids').append(temp);

    $('.chzn-select :selected').prop('disabled', true).trigger("chosen:updated");
	$('#testingid').datepicker();
	$('.ppd_expire_date1').datepicker();
	$('.physical_date').datepicker();
	$('.physical_date1').datepicker();
});
$(".chzn-select").chosen().trigger("chosen:updated");
$(".chzn-select1").chosen();

function getResponse() {
    var id = $('#total_id').val();
	
  $('#caregivercode_error').html(" ");
  $('#caregivername_error').html(" ");
  $('#caregiveremail_error').html(" ");
  $('#status_error').html(" ");
  $('#caregivercode_error').html(" ");
  $('#ppd_expireerror_error').html(" ");
  $('#ppd_expireerror1_error').html(" ");
  $('#physica_date1_error').html(" ");
  $('#physica_date_error').html(" ");
  $('#between_error').html(" ");
  $('#betweens_error').html(" ");
  var temp_id = <?php echo $query->id;?>;
    var cnt =0;
    if(id ==''){
        $('#select_error').html("Please select option");
        cnt =1;
    }else{
       var caregiver_code = $('#careid').val();
		if(caregiver_code ==''){
			$('#caregivercode_error').html("Please enter caregiver code.");
			cnt =1;
		}
       var caregiver_name = $('#cnameid').val();
       if(caregiver_name ==''){
			$('#caregivername_error').html("Please enter caregiver name.");
			cnt =1;
		}
       var caregiver_email = $('#cemailid').val();
		if(caregiver_email ==''){
			$('#caregiveremail_error').html("Please enter caregiver email.");
			cnt =1;
		}
       var status_id = $('#status_id').val();
        if(status_id ==''){
				$('#status_error').html("Please select status.");
			cnt =1;
		}
        var ppd_expire_date  = $('.ppd_expire_date').val();
		if(ppd_expire_date ==''){
			$('#ppd_expireerror_error').html("Please enter ppd expire date.");
			cnt =1;
		}
		var between_id = $('#between_id').val();
		if(between_id ==''){
			$('#between_error').html("Please select expression.");
			cnt =1;
		}
		if(between_id =='BETWEEN'){
			var ppd_expire_date1  = $('.ppd_expire_date1').val();
			if(ppd_expire_date1 == ''){
				$('#ppd_expireerror1_error').html("Please enter date.");
					cnt =1;
			}

		}
		
          
        var physical_date  = $('.physical_date').val();
		if(physical_date ==''){
			$('#physica_date_error').html("Please enter physical date.");
			cnt =1;
		}
        
       
        var between_ids = $('#between_ids').val();
		if(between_ids ==''){
			$('#betweens_error').html(" Please select expression.");
			cnt =1;
		}
		if(between_ids =='BETWEEN'){
			var physical_date1  = $('.ppd_expire_date1').val();
			if(physical_date1 == ''){
				$('#physica_date1_error').html("Please enter physical date.");
					cnt =1;
			}

		}
		
    }
    
    if(cnt ==1){
     return false;   
    }else{ 
	
     $.ajax({
           type:"POST",
           url:"<?php echo URL::to('/');?>/SearchReponse",
           data:{"_token":"<?php echo csrf_token();?>","caregiver_code":caregiver_code,"caregiver_name":caregiver_name,"caregiver_email":caregiver_email,"status_id":status_id,"ppd_expire_date":ppd_expire_date,"physical_date":physical_date,"between_id":between_id,"between_ids":between_ids,'physical_date1':physical_date1,'ppd_expire_date1':ppd_expire_date1,'temp_id':temp_id},
           success:function(response){
              $('#testval').attr('style',"");
	      $('.temps').attr('style',"");
              $('#table_response').html(response);
              $('.ccodeid').val(caregiver_code)
            $('.cnamei').val(caregiver_name)
            $('.ccaregiveemail').val(caregiver_email)
            $('.status_class').val(status_id)
            $('.between_id_ppd').val(between_id)
            $('.ppd_dates').val(ppd_expire_date)
            $('.ppd_dates1').val(ppd_expire_date1)
            $('.between_ids_physical').val(between_ids)
            $('.physicals_dates').val(physical_date)
            $('.physicals_dates1').val(physical_date1)
            }
       })    
    }
}

function getChanges(val){
	
	if(val =='BETWEEN'){
		$('#btnid').attr('style','');
	}else{
		$('#btnid').attr('style','display:none;');
		$('.ppd_expire_date1').val(" ");
	}
}
function getChangess(val){

	if(val =='BETWEEN'){
		$('#btnids').attr('style','');
	}else{
		$('#btnids').attr('style','display:none;');
		$('.physical_date1').val(" ");
	}
}
$('#submitid').submit(function(e){
	$('.loader').show();
	var cnt =1;
	 var AppoveVisitIds = [];
        $.each($("input[name='receipt_code[]']"), function() {
            AppoveVisitIds.push($(this).val());
        });
	
if(AppoveVisitIds.length > 0){
	
$('#submitid').submit();
}else{ return false; } 
	
});

function getRemove(id,txtId){

	$('#c'+id).remove();
	//$("#list>optgroup>option[value='1']").removeAttr('disabled');
	$('.chzn-select option[value="'+id+'"]').removeAttr('disabled').trigger("chosen:updated");

	$(".chzn-select").chosen('data','')
}
</script>
@include('include/footer_lte')