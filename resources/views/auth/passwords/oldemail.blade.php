<!DOCTYPE html>

<!-- saved from url=(0034)http://360.wonderpod.io/login.html -->

<html style="" class="js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers no-applicationcache svg inlinesvg smil svgclippaths gr__360_wonderpod_io"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>Bee Choo Origin Herbal Malaysia</title>

	<!-- Fav  Icon Link -->

	<link rel="shortcut icon" type="image/png" href="http://360.wonderpod.io/images/fav.png">

	<!-- Bootstrap core CSS -->

	<link rel="stylesheet" href="<?php echo URL::asset("/"); ?>assets/login_files/bootstrap.min.css">

	<!-- themify icons CSS -->

	<link rel="stylesheet" href="<?php echo URL::asset("/"); ?>assets/login_files/themify-icons.css">

	<!-- Main CSS -->

	<link rel="stylesheet" href="<?php echo URL::asset("/"); ?>assets/login_files/styles.css">

	<link rel="stylesheet" href="<?php echo URL::asset("/"); ?>assets/login_files/red.css" id="style_theme">

	<link rel="stylesheet" href="<?php echo URL::asset("/"); ?>assets/login_files/responsive.css">



	<script src="<?php echo URL::asset("/"); ?>/login_files/modernizr.min.js.download"></script>

</head>



<body class="auth-bg" data-gr-c-s-loaded="true">

	<!-- Pre Loader -->

	<div class="loading" style="display: none;">

		<div class="spinner">

			<div class="double-bounce1"></div>

			<div class="double-bounce2"></div>

		</div>

	</div>

	<!--/Pre Loader -->

	<div class="wrapper">

		<!-- Page Content  -->

		<div id="content">

			<div class="container-fluid">

				<div class="row">

					<div class="col-sm-6 auth-box">

						<div class="proclinic-box-shadow">

							<h3 class="widget-title">Forgot your password ?</h3>

							<form class="widget-form"  method="POST" action="{{ route('password.email') }}" >

							 {{ csrf_field() }}

								<!-- form-group -->

								<div class="form-group row {{ $errors->has('email') ? ' has-error' : '' }}">

									<div class="col-sm-12">

									 <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">



                                @if ($errors->has('email'))

                                    <span class="help-block" style="color:red;">

                                        <strong>{{ $errors->first('email') }}</strong>

                                    </span>

                                @endif

									</div>

								</div>

								

								<!-- /Check Box -->	

								<!-- Login Button -->			

								<div class="button-btn-block">

									<button type="submit" class="btn btn-primary btn-lg btn-block">Send Password Reset Link</button>

								</div>

								<!-- /Login Button -->	

								<!-- Links -->	

								

								<!-- /Links -->

							</form>

						</div>

					</div>

				</div>

			</div>

		</div>

		<!-- /Page Content  -->

	</div>

	<!-- Jquery Library-->

	<script src="<?php echo URL::asset("/"); ?>assets/login_files/jquery-3.2.1.min.js.download"></script>

	<!-- Popper Library-->

	<script src="<?php echo URL::asset("/"); ?>assets/login_files/popper.min.js.download"></script>

	<!-- Bootstrap Library-->

	<script src="<?php echo URL::asset("/"); ?>assets/login_files/bootstrap.min.js.download"></script>

	<!-- Custom Script-->

	<script src="<?php echo URL::asset("/"); ?>assets/login_files/custom.js.download"></script>





</body></html>



