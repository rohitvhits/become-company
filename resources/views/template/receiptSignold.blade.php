 @include('include/header')
@include('include/sidebar')
<style>
    .error{
        color:red;
    }
    .box-header.with-border{
        padding: 5px 25px !important;
    }
    .lenght{
        margin-top:25px;
    }

    ul.token-input-list li {
        list-style-type: none;

    }
    .referralInput {
        padding: 0 5px;
        line-height: 0;
        height: 30px;
    }
</style>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/jquery.tokeninput.js"></script>


<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/token-input.css" type="text/css" />
<div class="main-panel">

    <div class="content-wrapper">
		<div class="row">
            <div class="col-12">
				<div class="card">
					<div class="row">
						<div class="col-md-12">
							<div class = "card-body">
								<h4 class="card-title">Signer Receipt </h4>
								<form method='post' action='<?php echo URL::to('/insertReceiptSigner'); ?>' name="addPhysician" role="form" id="addPhysician" enctype="multipart/form-data">
									<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
									<input type="hidden" name="template_id" value="<?php echo $id; ?>">
									<div id="mainid">
										<div class="copy_id">
											<div class="col-md-12 row">
												<div class="col-md-6">
													<div class="form-group">
													
														<!--<input type="text" name="dropDown[]" class="form-control">	-->
														<select name="dropDown[]" class="form-control" onchange="OfficeStaff(0,this.value);">
                                                          
                                                            <option value="">Select Option</option>
                                                            <option value="Patient">Patient</option>
                                                            <option value="RelatedPatient">Related Patient</option>
                                                            <option value="EmcUser">Emc User</option>
                                                            <option value="OfficeStaff">Admin</option>

                                                        </select>
															
													</div>
													

												</div>
												
                                                    <div class="col-md-5 " style="margin-top: -3%;">
													 <div class="office0"  style="display:none;">
                                                        <div class="form-group">
                                                            <label for="FirstName">UserName </label>
															<input type="text" name="search[]" class="searchid0">
                                                           
															
                                                        </div>
                                                    </div>
                                                </div>
												<div class="col-md-1">
												<a href="javascript:void(0)" class="btn btn-info btn-sm icon-btn add_button"><i class="fa fa-plus"></i></a>
											</div>
											</div>
											
										</div>
									</div>
									<div class="row">
										
											<div class="col-md-12 text-right">
											
												
											</div>
											<div class="col-md-12 text-left">
												<button type="submit" class="btn btn-primary mr-2">Save</button>

											</div>
											
										</div>
									
								</form>
							</div>
							
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<span id="reagent_id" style="display:none;">

    <option value="">Select Option</option>
	
    <option value="Patient">Patient</option>
    <option value="RelatedPatient">Related Patient</option>
    <option value="EmcUser">Emc User</option>
	<option value="OfficeStaff">Admin</option>


</span>
<script type="text/javascript">

   $(document).ready(function () {
        var next = 0;
        $(".add_button").click(function () {
            var i = $('.copy_id').length;
            var location = $('#reagent_id').html();
           var fieldHTML = '<div class="copy_id "><div class="col-md-12 row"><div class="col-md-6 padng-left"><div class="form-group"><label for="name" class="label_font">Signer Receipt</label><select name="dropDown[]" class="form-control" onchange="OfficeStaff('+i+', this.value);">' + location + '</select></div></div><div class="col-md-5 "><div class="office'+i+'"  style="display:none;"><div class="form-group"><label for="FirstName">UserName </label><input type="text" name="search[]" class="searchid'+i+'"></div></div></div><div class="col-md-1" style="margin-top:30px;"><button class="btn btn-info btn-sm icon-btn  remove_button" title="Remove field" type="button"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></div></div></div></div>';
                $('#mainid').append(fieldHTML);
				
				$.getScript("https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", function () {
					$.getScript("<?php echo URL::to('/'); ?>/assets/jquery.tokeninput.js", function () { 
						var j = 0;
						
					})
				});
                i++;

        });
        $("#mainid").on('click', '.remove_button', function (e) {
            e.preventDefault();
            $(this).parents('.copy_id').remove(); //Remove field html

        });

    });
</script>
<script>

    function OfficeStaff(id, val) {

        if (val == 'OfficeStaff') {
           // $(".office" + id).attr('style', "");
			if(id !=0){
			//	getsearch(id);
			}
        } else { 
           // $(".office" + id).attr('style', "display:none;");
        }
    }
	
	function getsearch(id){
	var j=0;
	$(".office"+id+" .searchid"+id).tokenInput("<?php echo URL::to('/'); ?>/searchByUserList",{
		tokenLimit: 1
	});
			
		
	}
	getsearch(0)
	
</script>
 <script src="<?php echo URL::to('/');?>/assets/vendors/jquery.repeater/jquery.repeater.min.js"></script>
<script src="<?php echo URL::to('/');?>/assets/js/form-repeater.js"></script>
@include('include/footer')
