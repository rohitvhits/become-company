<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <title>NY BEST MEDICAL CARE PC</title>
  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/css/vertical-layout-light/style.css">
  <!-- endinject -->
  
  <link rel="shortcut icon" href="/img/favicon.png" />

</head>
<style>
  .bgimage {
      width: 500px;
      height: auto;
      position: absolute;
    }

    .home-page-bg {
      display: flex;
      justify-content: center;
      height: 100vh;
      align-items: center;
      position: relative;
    }
</style>
<body class="sidebar-dark">
  <div class="container-scroller">  
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
        <div class="row flex-grow">
          <div class="col-lg-6 d-flex align-items-center justify-content-center" style="color: white;background: #0F0D0B;">
            <div class="auth-form-transparent text-left p-3">
              <div class="brand-logo">
                <img src="../img/logo.png" alt="logo" style='width:100%'>
              </div>
					@if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif 

              <h4>{{ __('Create your password') }}</h4>
              <h6 class="font-weight-light">Welcome to the NyBest Medicals Client Portal!</h6>
              <form id="submitId" class="widget-form"  method="POST" action="<?php echo URL::to('/');?>/acceptInvitation">
		        <input type="hidden" name="id" value="<?php echo $id;?>">
                <div class="form-group">
                  <label for="exampleInputPassword">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input id="password" type="password" class="md-input form-control" name="password"  autocomplete="new-password">                        
					<span class="password_error" style="color:red"></span> 
				  </div>
                </div>
				<div class="form-group">
                  <label for="exampleInputPassword">Confirm Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input  type="password" class="md-input form-control" name="password_confirmation"  autocomplete="new-password" id="cpassword">                        
					<span class="cpassword_error" style="color:red"></span>
				  </div>
                </div>
                <div class="my-3">
					<button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"  type="submit">Create Password</button>
                </div>
                
              </form>
            </div>
          </div>
          <div class="col-lg-6 home-page-bg">
          <img src="{{URL::to('/')}}/img/pana.png" class="bgimage" >

            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2023  All rights reserved.</p>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo URL::to('/');?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="<?php echo URL::to('/');?>/assets/js/off-canvas.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/hoverable-collapse.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/template.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/settings.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/todolist.js"></script>
  <!-- endinject -->
  
  <script>
	$('#submitId').submit(function(e){
		var password = $('#password').val();
		var cpassword = $('#cpassword').val();
		var cnt =0;
		if(password.trim() ==''){
			$('.password_error').html(" Required !");
			cnt =1;
		}
		if(cpassword.trim() ==''){
			$('.cpassword_error').html(" Required !");
			cnt =1;
		}
		
		if(password.trim() != '' && cpassword.trim() !=''){
			if(password.trim() != cpassword.trim()){
				$('.cpassword_error').html(" Password and confirm password does not match.");
				cnt =1;
			}
			
		}
		
		if(cnt ==1){
			return false;
		}else{
			return true;
		}
	});
  </script>
</body>
</html>