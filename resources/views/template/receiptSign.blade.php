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
     .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    /* Signer block */
    .copy_id {
        background: #fdfdfd;
        border: 1px solid #e8e8e8;
        border-left: 3px solid #00879E;
        border-radius: 4px;
        padding: 15px 15px 5px;
        margin-bottom: 12px;
        position: relative;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .copy_id:hover {
        border-left-color: #006d80;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    }
    .copy_id .col-md-12.row {
        align-items: flex-end;
    }
    .copy_id .remove_button {
        border-radius: 50%;
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* Signer number badge */
    .signer-num {
        display: inline-block;
        background: #00879E;
        color: #fff;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 600;
        margin-right: 6px;
        vertical-align: middle;
    }
    /* Add button */
    .btn-add-wrap {
        text-align: right;
        margin-bottom: 15px;
    }
    .btn-add-wrap .add_button {
        background: #00879E;
        border: 1px solid #00879E;
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 15px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-add-wrap .add_button:hover {
        background: #006d80;
        border-color: #006d80;
    }
    .btn-add-wrap .add_button:disabled,
    .btn-add-wrap .add_button.disabled {
        background: #ccc;
        border-color: #ccc;
        color: #fff;
        cursor: not-allowed;
    }
    /* Info strip */
    .signer-info-strip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f4f9fa;
        border: 1px solid #d9edf0;
        border-radius: 4px;
        padding: 10px 15px;
        margin-bottom: 15px;
        font-size: 13px;
    }
    .signer-info-strip .badge-workflow {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-normal { background: #e2e3e5; color: #383d41; }
    .badge-form_complete { background: #d4edda; color: #155724; }
    .badge-form_complete_with_sign { background: #cce5ff; color: #004085; }
 </style>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" type="text/css" />

 <div class="main-panel">
     <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Signer Receipt</h5>
        </div>
         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('success') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
             @endif
             @if (Session::has('error'))
                 <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('error') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
             @endif
         </div>
         <div class="row">
             <div class="col-12">
                 <div class="card">
                     <div class="row">
                         <div class="col-md-12">
                             <div class="card-body">
                                 <form method='post' action='<?php echo URL::to('/insertReceiptSigner'); ?>' name="addPhysician" role="form"
                                     id="addPhysician" enctype="multipart/form-data">
                                     <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                     <input type="hidden" name="template_id" value="<?php echo $id; ?>">
                                     <input type="hidden" id="esign_workflow" value="{{ $template_type->esign_workflow ?? 'normal' }}">

                                     @php
                                         $workflow = $template_type->esign_workflow ?? 'normal';
                                         $wfLabels = ['normal'=>'Normal','form_complete'=>'Form Complete','form_complete_with_sign'=>'Form Completed with Sign'];
                                     @endphp
                                     <div class="signer-info-strip">
                                         <div>
                                             <strong>{{ $template_type->template_name ?? '' }}</strong>
                                             <span class="badge-workflow badge-{{ $workflow }}" style="margin-left:8px;">{{ $wfLabels[$workflow] ?? 'Normal' }}</span>
                                         </div>
                                         <div>
                                             Signers: <strong><span id="signer-count">1</span></strong>
                                             @if($workflow === 'form_complete') / 1 max
                                             @elseif($workflow === 'form_complete_with_sign') / 2 max
                                             @endif
                                         </div>
                                     </div>

                                     <div id="mainid">
                                         <div class="copy_id">
                                             <div class="col-md-12 row">
                                                 <div class="col-md-6">
                                                     <div class="form-group">
                                                         <label><span class="signer-num">1</span> Signer Receipt</label>
                                                         <select name="dropDown[]" class="form-control"
                                                             onchange="OfficeStaff(0,this.value);">
                                                             <option value="">Select Option</option>
                                                             @if($template_type->lookup_fields =='caregiver')
                                                             <option value="Caregiver">Caregiver</option>
                                                             <option value="OfficeStaff">User</option>
                                                             <option value="StampUser">Stamp User</option>
                                                             <option value="Other">Other</option>
                                                             <option value="FormFill">Form Fill</option>
                                                             <option value="Sign">Sign</option>
                                                             <option value="Stamp">Stamp</option>
                                                             @else
                                                             <option value="Patient">Patient</option>
                                                             <option value="SignStamp">Sign & Stamp</option>
                                                             @endif
                                                         </select>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-5">
                                                     <div class="office0" style="display:none;">
                                                         <div class="form-group">
                                                             <label>UserName</label>
                                                             <input type="text" name="search[]" class="searchid0">
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-1" style="margin-bottom:15px;">
                                                     <button class="btn btn-danger btn-sm remove_button" title="Remove field" type="button"><i class="fa fa-minus-circle" aria-hidden="true"></i></button>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                     <div class="btn-add-wrap">
                                         <button class="add_button" type="button">
                                             <i class="fa fa-plus"></i> Add More Signer
                                         </button>
                                     </div>
                                     <hr>
                                     <div class="row">
                                         <div class="col-md-12 text-left">
                                             <button type="submit" class="btn btn-primary mr-2">Save</button>
                                             <a href="{{ url('template') }}" class="btn btn-light">Back</a>
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
	 <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
     <span id="reagent_id" style="display:none;">
         <option value="">Select Option</option>
         @if($template_type->lookup_fields =='caregiver')
         <option value="Caregiver">Caregiver</option>
         <option value="OfficeStaff">User</option>
         <option value="StampUser">Stamp User</option>
         <option value="Other">Other</option>
         <option value="FormFill">Form Fill</option>
         <option value="Sign">Sign</option>
         <option value="Stamp">Stamp</option>
         @else
         <option value="Patient">Patient</option>
         <option value="SignStamp">Sign & Stamp</option>
        @endif
     </span>
     <script type="text/javascript">
         $(document).ready(function() {
             var next = 0;

             function getMaxSigners() {
                 var workflow = $('#esign_workflow').val();
                 if (workflow === 'form_complete') return 1;
                 if (workflow === 'form_complete_with_sign') return 2;
                 return 0;
             }

             function updateSignerNumbers() {
                 $('.copy_id').each(function(idx) {
                     var $num = $(this).find('.signer-num');
                     if ($num.length) $num.text(idx + 1);
                 });
                 $('#signer-count').text($('.copy_id').length);
             }

             function checkRemoveButtons() {
                 if ($('.copy_id').length <= 1) {
                     $('.remove_button').hide();
                 } else {
                     $('.remove_button').show();
                 }
             }

             function checkSignerLimit() {
                 var max = getMaxSigners();
                 var current = $('.copy_id').length;
                 if (max > 0 && current >= max) {
                     $(".add_button").prop('disabled', true).addClass('disabled');
                 } else {
                     $(".add_button").prop('disabled', false).removeClass('disabled');
                 }
                 updateSignerNumbers();
                 checkRemoveButtons();
             }

             checkSignerLimit();

             $(".add_button").click(function() {
                 var max = getMaxSigners();
                 var current = $('.copy_id').length;
                 if (max > 0 && current >= max) {
                     alert('Maximum ' + max + ' signer(s) allowed for this workflow type.');
                     return false;
                 }

                 var i = $('.copy_id').length;
                 var location = $('#reagent_id').html();
                 var fieldHTML =
                     '<div class="copy_id"><div class="col-md-12 row"><div class="col-md-6"><div class="form-group"><label><span class="signer-num">' + (i + 1) + '</span> Signer Receipt</label><select name="dropDown[]" class="form-control" onchange="OfficeStaff(' +
                     i + ', this.value);">' + location +
                     '</select></div></div><div class="col-md-5"><div class="office' + i +
                     '" style="display:none;"><div class="form-group"><label>UserName</label><input type="text" name="search[]" class="searchid' +
                     i +
                     '"></div></div></div><div class="col-md-1" style="margin-bottom:15px;"><button class="btn btn-danger btn-sm remove_button" title="Remove field" type="button"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></div></div></div>';
                 $('#mainid').append(fieldHTML);

                 $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js",
             function() {
                     $.getScript("<?php echo URL::to('/'); ?>/assets/jquery.tokeninput.js", function() {
                         var j = 0;
                     })
                 });
                 i++;
                 checkSignerLimit();

             });
             $("#mainid").on('click', '.remove_button', function(e) {
                 e.preventDefault();
                 $(this).parents('.copy_id').remove();
                 checkSignerLimit();
             });

         });
     </script>
     <script>
         function OfficeStaff(id, val) {

             if (val == 'OfficeStaff') {
                 $(".office" + id).attr('style', "");
                 if (id != 0) {
                     getsearch(id);
                 }
             } else {
                 $(".office" + id).attr('style', "display:none;");
             }
         }

         function getsearch(id) {
             var j = 0;
             $(".office" + id + " .searchid" + id).tokenInput("<?php echo URL::to('/'); ?>/searchByUserList", {
                 tokenLimit: 1
             });
         }
         getsearch(0)
     </script>
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/jquery.repeater/jquery.repeater.min.js"></script>
     <script src="<?php echo URL::to('/'); ?>/assets/js/form-repeater.js"></script>
     @include('include/footer')
