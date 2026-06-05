
<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>NY Best Medical Care PC : Appointment</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.eot">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.ttf">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.woff">
 
  <!-- base:css -->

  <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">

  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css')}}">

  <!-- endinject -->

  <!-- plugin css for this page -->

  <!-- <link rel="stylesheet" href="{{ asset('assets/vendors/jqvmap/jqvmap.min.css')}}"> -->
  

  <!-- <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css')}}"> -->

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css')}}">

  <link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css')}}">
  <!-- endinject -->
  <script src="{{ asset('assets/js/jquery.min.js')}}"></script>
  <script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
  <link rel="shortcut icon" href="{{ asset('img/logo.png')}}" />
  
  <style>
    .compact-view .form-control {
      padding: 0 !important;
      height: 24px;
    }

    .compact-view td {
      padding: 5px 10px;
    }
	.img_new{
		width: 200px;
		margin-left:14px;
        margin-top:10px;
		
	}
    .page-body-wrapper{
        padding-top:0px !important;
    }
  .hide{
    display: none;
  }
  .error{
    color:red;
  }
  </style>

</head>

<body class="sidebar-dark sidebar-fixed">
  <div class="container-scroller">
    <!--close-top-Header-menu-->
    <div class="container-fluid page-body-wrapper">

      <!-- partial -->
      <div class="main-panel" style="margin-left:-3px !important">
      <div class="brand-logo" style="background-color: black;height: 50px;">
              
              <img class="img_new" src="https://www.nybestmedical.com/wp-content/uploads/2022/07/Ny-Best-Medical-Logo.svg" alt="logo" style="">
              
              </div>
        <div class="content-wrapper">
          <h1>Opt-in or opt-out of receiving SMS messages from Ny Best Medical</h1>
		
			  <p class="text-muted mb-3 tx-12">To opt in/out to receive text messages from Nybest Medicals please enter your cell phone number here.
</p>
				
			
  
          <div class="row" >
			
            <div id="add_form" class="col-12 grid-margin" style="">
              <div class="card">
                <div class="card-body">
                  
                  <form class="form-sample" action='' id="addusers" name="adduser" method="post">
                  
                   
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Mobile No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile" onkeypress="return isNumber(event)" name="mobile" value="" maxlength="15">
                                <span id="mobile_error" class="error mt-2"></span>
                            </div>
                        </div>
                      </div>
                      
                    </div> 
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">OPT In OUT</label>
                          <div class="col-sm-4">
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="opt_in_out" id="membershipRadios1" value="N">
                                Opt-In
                              <i class="input-helper"></i></label>
                            </div>
                          </div>
                          <div class="col-sm-5">
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="opt_in_out" id="membershipRadios2" value="Y">
                                Opt-Out
                              <i class="input-helper"></i></label>
                            </div>
                          </div>

                          
                        </div>
                        <span id="membershipRadios_error" class="error mt-2" style="padding-left: 25%;"></span>
                      </div>
                      
                    </div>
                    

                
                    <button type="button" class="btn btn-primary mr-2" id="ssid">Save</button>
                  </form>
                </div>
                <label>Ny Best Medical may contact me at this number via calls or texts (including through use of an automatic telephone dialing system and artificial or pre-recorded voicemail) to provide information about or to help me enroll with Ny Best Medical. Your consent is not required to enroll. Message and data rates may apply


</label>
              </div>
            </div>
			
          </div>
        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> 2019 - 2024 © Nybest Medical.
          </span></div>
        </footer>

        <script>

            function isNumber(evt) {

            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

                return false;
            }
            return true;
            }

            $('#ssid').click(function(e){
                var mobile = $('#mobile').val();
                var opt_in_out = $('input[name="opt_in_out"]').is(":checked");
                $('#mobile_error').html("");
                $('#membershipRadios_error').html("");

                var cnt =0;
                if(mobile.trim() ==''){
                    $('#mobile_error').html("Mobile No is Required");
                    cnt =1;
                }

                if(!opt_in_out){
                    $('#membershipRadios_error').html("OPT In OUT is Required");
                    cnt =1;
                }


                if(cnt ==1){

                    return false;
                }else{
                    $.ajax({
                        async:false,
                        global:false,
                        type:"POST",
                        url:"{{ url('opt-in-out-post')}}",
                        data:{
                            'mobile':mobile,
                            'opt_in_out':$('input[name="opt_in_out"]:checked').val(),
                            '_token':"{{ csrf_token()}}"
                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            $('#addusers')[0].reset()
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseText.error_msg)
                        }
                    })
                }
            })
        </script>