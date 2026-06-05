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
  <link href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/jquery-ui.css" rel="stylesheet">
  <!-- base:css -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/css/vendor.bundle.base.css">

  <!-- endinject -->

  <!-- plugin css for this page -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/jqvmap/jqvmap.min.css">
  

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/style.css">

  <!-- endinject -->
  <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.min.js"></script>

  <link rel="shortcut icon" href="<?php echo URL::to('/'); ?>/img/logo.png" />
  
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
		margin-left:15%;
		margin-top: -19%;
	}
  </style>



</head>



<body class="sidebar-dark sidebar-fixed">



  <!--Header-part-->

  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->


    <script>
      $('a[data-notif-id]').click(function() {

        var notif_id = $(this).data('notifId');
        var targetHref = $(this).data('href');
        $.ajax({
          async: false,
          global: false,
          type: "GET",
          url: "<?php echo URL::to('/'); ?>/NotifMarkAsRead",
          data: {
            'notif_id': notif_id
          },
          succes: function(response) {
            if (response == 'success') {
              window.location.href = targetHref;
            } else {
              alert("Sorry, something went wrong. Please try again.");
              return false;
            }
          }

        })
      });
    </script>

    <!--close-Header-part-->





    <!--top-Header-menu-->



    <!--close-top-Header-menu-->
    <div class="container-fluid page-body-wrapper">

      <!-- partial -->
      <div class="main-panel" style="margin-left:-3px !important">
        <div class="content-wrapper">
		<div class="brand-logo">
              
              <img class="img_new"src="https://www.nybestmedical.com/wp-content/uploads/2020/12/Logo-Original-1.png" alt="logo" style="">
              
              </div>
			  <p class="text-muted mb-3 tx-14" style="color:red !important"> Link has been expired.  Please  contact to  Nybest  Medical</p>		
			

        </div>
        <!-- /Main Content -->  

        <!-- /Page Content -->

        


        <!-- End Date Picker -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> 2019 - 2021 &copy; Nybest Medicals.
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo URL::to('/'); ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  
  <script src="<?php echo URL::to('/'); ?>/assets/js/jquery-ui.min.js"></script>
 
  <link href="<?php echo URL::to('/');?>/assets/libs/sweetalert/sweetalert.css" rel="stylesheet"/>
<script src="<?php echo URL::to('/');?>/assets/libs/sweetalert/sweetalert.min.js"></script>

  <!-- End custom js for this page-->
</body>
<!-- Mirrored from www.urbanui.com/yoraui/template/demo/vertical-default-light/ by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 13 Dec 2019 05:50:45 GMT -->

</html>