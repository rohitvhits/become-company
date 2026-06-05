 @include('include/header')
 @include('include/sidebar')
 <style>
     .error {
         color: red;
     }

     .box-header.with-border {
         padding: 5px 25px !important;
     }

     .lenght {
         margin-top: 25px;
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
 <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/jquery.tokeninput.js"></script>
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/token-input.css" type="text/css" />





 <div class="main-panel">

     <div class="content-wrapper">
         <div class="row">
             <div class="col-12">
                 <div class="card">
                     <div class="row">
                         <div class="col-md-12">
                             <div class="card-body">
                                 <h4 class="card-title">Signer Receipt </h4>
                                 <form method='post' action='<?php echo URL::to('/insertReceiptSigner'); ?>' name="addPhysician" role="form" id="addPhysician" enctype="multipart/form-data">
                                     <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                     <input type="hidden" name="template_id" value="<?php echo $id; ?>">
                                     <div class="col-md-12">
                                         <div id="mainid">
                                             <?php
                                                $cnt = 0;
                                                foreach ($oldRecordById as $val) {
                                                ?>
                                                 <div class="copy_id ">
                                                     <div class="col-md-12 row">
                                                         <div class="col-md-6">
                                                             <div class="form-group">
                                                                 <label for="FirstName">Signer Receipt </label>

                                                                 <select name="dropDown[]" class="form-control" onchange="OfficeStaff(<?php echo $cnt; ?>, this.value,'selected');">
                                                                     <option value="">Select Signer</option>
                                                                     <option value="Patient" <?php if ($val->name == 'Patient') {
                                                                                                    echo 'selected="selected"';
                                                                                                } ?>>Patient</option>
                                                                     <option value="RelatedPatient" <?php if ($val->name == 'RelatedPatient') {
                                                                                                        echo 'selected="selected"';
                                                                                                    } ?>>Related Patient</option>


                                                                     <option value="EmcUser" <?php if ($val->name == 'EmcUser') {
                                                                                                    echo 'selected="selected"';
                                                                                                } ?>>Emc User</option>
                                                                     <option value="OfficeStaff" <?php if ($val->name == 'OfficeStaff') {
                                                                                                        echo 'selected="selected"';
                                                                                                    } ?>>Admin</option>
                                                                 </select>
                                                             </div>

                                                         </div>

                                                         <div class="col-md-5">

                                                         </div>
                                                         <!-- <div class="col-md-5">
															<div class="office<?php echo $cnt; ?>"  style="<?php if ($val->user_id != '') { ?> display:block;<?php } else { ?> display:none;<?php } ?>">
                                                            <div class="form-group">
                                                                <label for="FirstName">UserName </label>
                                                                <input type="text" name="search[]" class="searchid<?php echo $cnt; ?>">

                                                            </div>
															</div>
                                                        </div>
                                                   
													<script>
													
													 $(".searchid<?php echo $cnt; ?>").tokenInput("<?php echo URL::to('/'); ?>/searchByUserList", { tokenLimit: 1, 
													 <?php if ($val->names != '') { ?>
													 prePopulate: [{id: <?php echo $val->id; ?>, name: "<?php echo $val->names; ?>"}]
													 <?php } ?>
													 });
													 
													</script>-->
                                                         <div class="col-md-1">
                                                             <label for="name" style="margin-top:40px;"></label>
                                                             <button class="btn btn-primary btn-sm remove_button" title="Remove field" type="button"><i class="fa fa-minus-circle" aria-hidden="true"></i></button>

                                                         </div>

                                                     </div>

                                                 </div>
                                             <?php $cnt++;
                                                } ?>
                                         </div>



                                     </div>
                                     <div class="row">

                                         <div class="col-md-12 text-right">
                                             <button class="btn btn-primary btn-sm add_button" style="margin-right:47px;" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>

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
         $(document).ready(function() {
             var next = 0;
             $(".add_button").click(function() {
                 var i = $('.copy_id').length;
                 var location = $('#reagent_id').html();
                 var fieldHTML = '<div class="copy_id "><div class="col-md-12 row padng-left"><div class="col-md-6 padng-left"><div class="form-group"><label for="name" class="label_font">Signer Receipt</label><select name="dropDown[]" class="form-control" onchange="OfficeStaff(' + i + ', this.value);">' + location + '</select></div></div><div class="col-md-5 "><div class="office' + i + '"  style="display:none;"><div class="form-group"><label for="FirstName">UserName </label><input type="text" name="search[]" class="searchid' + i + '"></div></div></div><div class="col-md-1" style="margin-top:20px;"><button class="btn btn-info btn-sm icon-btn  remove_button" title="Remove field" type="button"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></div></div></div></div>';
                 $('#mainid').append(fieldHTML);
                 $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", function() {
                     $.getScript("<?php echo URL::to('/'); ?>/assets/jquery.tokeninput.js", function() {
                         var j = 0;

                     })
                 });
                 i++;

             });
             $("#mainid").on('click', '.remove_button', function(e) {
                 e.preventDefault();
                 $(this).parents('.copy_id').remove(); //Remove field html

             });

         });
     </script>
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/jquery.repeater/jquery.repeater.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/form-repeater.js"></script>

     @include('include/footer')


     <script>
         function OfficeStaff(id, val, select = null) {

             if (val == 'OfficeStaff') {
                 //$(".office" + id).attr('style', "");
                 if (select == null) {
                     //getSearch(id);
                 }

             } else {
                 $(".office" + id).attr('style', "display:none;");
             }
         }
     </script>
     <script>
         function getSearch(id) {
             $(".office" + id + " .searchid" + id).tokenInput("<?php echo URL::to('/'); ?>/searchByUserList", {
                 tokenLimit: 1
             });
         }
     </script>