<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Required meta tags -->

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NY BEST MEDICAL</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.eot')}}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.ttf')}}">

    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.woff">
    <link href="{{ asset('/assets/css/vertical-layout-light/jquery-ui.css')}}" rel="stylesheet">
    <!-- base:css -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/mdi/css/materialdesignicons.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/css/vendor.bundle.base.css')}}">

    <!-- endinject -->

    <!-- plugin css for this page -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/jqvmap/jqvmap.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/css/horizontal-default-light/style.css')}}">

    <!-- endinject -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js')}}"></script>

    <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />
    <link href="<?php echo URL::to('/'); ?>/assets/esign/simcify.min.css" rel="stylesheet">
    <script src="<?php echo URL::to('/'); ?>/assets/esign/js/jscolor.js"></script>


    <style>
        .compact-view .form-control {
            padding: 0 !important;
            height: 24px;
        }

        .compact-view td {
            padding: 5px 10px;
        }

        .horizontal-menu .top-navbar {
            font-weight: 400;
            background: #1e1e2f;
            border-bottom: 1px solid #030303;
        }

        .horizontal-menu .top-navbar .navbar-menu-wrapper {
            color: #b1b1b5;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link .menu-title,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link .menu-title {
            color: #97C229 !important;
        }

        .horizontal-menu .bottom-navbar {
            background: #FFF;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item>.nav-link {
            color: #686868;
        }

        .agency-logo {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .agency-logo a {
            padding: 0 10px !important;
        }

        .text-danger {
            color: red !important;
        }

        .hide {
            display: none;
        }

        .select2-container--default .select2-selection--single {
            height: 39px;
        }
        @media (max-width: 991px) {

            .mobileView {
                padding-top: 60px;
            }
            .select2.select2-container{
                width: 100% !important;
            }
        }
        .margintop {
            margin-top: 8%;
        }

        .backchages {
            background-color: #000
        }

        .teval {
            background-color: yellow;
        }

        .drips {
            background-color: yellow;
        }

        .signer-overlay-previewer.light-card.test1 {
            margin: 0px;

        }

        .signer-element:hover {
            border: 0px !important;
        }

        .signer-element {
            max-height: 22px;

        }

        .prevButtons {
            float: left;
            margin-right: 5%;
        }

        .nextButtons {
            float: left;
            margin-right: 5%;
        }

        .finishButtons {
            float: left;
        }

        .document-pagination button {
            height: 40px !important;
            width: 35px !important;
            padding-left: 15px !important;

        }

        .signer-element[type="text"][group="input"],
        .signer-element[type="text"][group="field"] {
            border: 0px;
        }

        .errors {
            border: 2px solid #ff0000;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
            position: absolute;
            left: 45%;
            margin-top: 40%;

        }

        .loader_nnnn {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;

            left: 45%;

            margin-left: 20%;
        }

        .heightClass {
            height: 10px !important;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .hideshow,
        .Depending {
            display: none !important;
        }

        .testAssign_sub {
            border: 2px solid red;
        }

        #rename_canvas {
            border: 1px solid navy;
        }

        div[type="checkbox"] {
            padding: 0;
            width: 0px;
        }

        div[type="text"] {
            padding: 0;
            width: 0px;
        }

        .signer-element.selected-element {
            border: 0px dashed #ff0000 !important
        }

        .alert-primary {
            color: #fff;
            background: #007bff;
            border-color: #006fe6;
        }

        .fa,
        .far,
        .fas {
            font-family: "Font Awesome 5 Free" !important
        }

        .fa,
        .fas {
            font-weight: 900 !important;
        }

        /* .compact-view .form-control {
            padding: 0 !important;
            height: 24px;
        }

        .compact-view td {
            padding: 5px 10px;
        }

        .horizontal-menu .top-navbar {
            font-weight: 400;
            background: #1e1e2f;
            border-bottom: 1px solid #030303;
        }

        .horizontal-menu .top-navbar .navbar-menu-wrapper {
            color: #b1b1b5;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link .menu-title,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link .menu-title {
            color: #97C229 !important;
        }

        .horizontal-menu .bottom-navbar {
            background: #FFF;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item>.nav-link {
            color: #686868;
        }

        .agency-logo {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .agency-logo a {
            padding: 0 10px !important;
        }

        .text-danger {
            color: red !important;
        }

        .hide {
            display: none;
        }

        .select2-container--default .select2-selection--single {
            height: 39px;
        } */

        @media (max-width: 991px) {

            .mobileView {
                padding-top: 60px;
            }

            .select2.select2-container {
                width: 100% !important;
            }
            .mobileCanvas{
                width: 100%;

            }
        }
    </style>
</head>

<body class="sidebar-toggle-display sidebar-hidden">
    <div class="container-scroller">

        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">
                <!-- <div class="container"></div> -->
                <div class="container-fluid">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo" href="javascript:void(0)"><img
                                src="<?= URL::to('img/logo-ny.png') ?>"></a>
                        <a class="navbar-brand brand-logo-mini" href="javascript:void(0)"><img
                                src="<?= URL::to('img/favicon.png') ?>"></a>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                            data-toggle="horizontal-menu-toggle">
                            <span class="mdi mdi-menu"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>

        <div class="mobileView">
            <div class="container-fluid page-body-wrapper">
                <!-- partial -->
                <div class="">
                    <div class="content-wrapper">
                        <h2 class="card-title">Demographic Details</h2>

                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <form class="form-sample" action='{{ url("save-pdf-download")}}' id="form-submit" name="adduser" method="post">
                                    @csrf
                                    <input type="hidden" name="patient_id" value="{{ $getExistingData->id}}">
                                    <input type="hidden" name="agency_id" value="{{ $getExistingData->agency_id}}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 col-md-4">
                                                <div class="form-group row  mb-2">
                                                    <label class="col-lg-5  text-lg-right"><b>First Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->first_name}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b> Code:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->patient_code}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Date of Birth:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ date('m/d/Y',strtotime($getExistingData->dob))}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Gender:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->gender}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Apt/Suite/Floor:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->address2 }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Zip Code:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->zip_code }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>SSN:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->ssn }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>CIN/Medicaid Number:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->cin }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Emergency Contact Number:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->emergency_phone }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="form-group row  mb-2">
                                                    <label class="col-lg-5  text-lg-right"><b>Middle Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->middle_name}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Type:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->type}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Mobile No:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->mobile }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Email:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->email }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>State:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->state }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>County:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->county }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Insurance ID:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->insurance_id }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Language:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">@if(isset($getExistingData->languages) && $getExistingData->languages !=""){{ $getExistingData->languages->name }} @endif</span>
                                                    </div>



                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="form-group row  mb-2">
                                                    <label class="col-lg-5  text-lg-right"><b>Last Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->last_name}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Agency Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->agencyDetail->agency_name}}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Phone No:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->phone }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Address:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->address1 }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>City:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->city }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Insurance Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">@if(isset($getExistingData->insuranceDetails) && $getExistingData->insuranceDetails !=""){{ $getExistingData->insuranceDetails->insurance_name }} @endif</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Emergency Contact Name:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->emergency_contact_name }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Location / Branch:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">{{ $getExistingData->location_branch }}</span>
                                                    </div>

                                                    <label class="col-lg-5  text-lg-right"><b>Signature:</b></label>
                                                    <div class="col-lg-7">
                                                        <span class="">
                                                            <a data-toggle="modal" data-target="#modal-default" data-whatever="@mdo" id="showModals"><img style="height:60px;object-fit: contain" src="{{ asset('assets/images/sign.png')}}" id="imgPatient" onclick="getWebviewCanvas('Patient')"></a></p>
                                                        </span>
                                                        <span id="immPatient_error" class="text-danger"></span>
                                                    </div>
                                                    <input type="hidden" name="images" id="immPatient">
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary" >Submit</button>
                                    </div>

                                </form>


                            </div>

                        </div>
                    </div>
                    <!-- content-wrapper ends -->
                </div>
                <!-- main-panel ends -->
            </div>
        </div>

    </div>
    <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Signature Pad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
              
                <div class="modal-body" style="padding:21px 10px !important">

                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            <canvas width="450" height="200" id="rename_canvas" class="mobileCanvas" style="touch-action: none;"></canvas>
                        </div>
                        <input type="hidden" id="imagesId">

                        <div class="signature-pad--footer">
                            <div class="description">Sign above</div>

                            <div class="signature-pad--actions">
                                <div>
                                    <button type="button" class="button clear" data-action="clear">Clear</button>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="testingsSave">Save changes</button>
                </div>
             
            </div>
        </div>
    </div>
</body>

</html>


<script src="{{ asset('/assets/vendors/js/vendor.bundle.base.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/jquery.slimscroll.min.js"></script>

<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/signature_pad.umd.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/app.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/appsignaturepad.js?<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/signature_pad.min.js"></script>

<script>
    var docusignId = "Patient";
    var times = "<?php echo time(); ?>";
    var mainURL = "<?php echo URL::to('/'); ?>/";

    function getWebviewCanvas(documentMentId, rand, imgid) {

        
        $('#imagesId').val(documentMentId);
    }

    function getSubmit(blob) {

        var formData = new FormData();
        formData.append("image", blob);
        formData.append("_token", '{{ csrf_token()}}');
        //formData.append("_token",'7K04mjtA5BWzqQSrFdWNgKhYvw9KXpfb98Ij5wgE');

        $.ajax({
            url: mainURL + 'esign/docusign/esign-signature', // Upload Script
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {

                if (data != '') {
                    var imgid = $('#imagesId').val();

                    getSignatureSuccess(data, imgid)
                    $('#modal-default').modal('hide');
                }
            }
        });

    }

    function getSignatureSuccess(filename, id) {
        var res = filename;
        $("#img" + id).attr('src', '<?php echo URL::to("/"); ?>/dosusinguploads/docusign/' + res);
        $("#img" + id).attr('style', 'width:100px;');
        $("#img" + id).attr('dataids', 1);
        $("#immPatient").val('<?php echo URL::to("/"); ?>/dosusinguploads/docusign/' + res);
        //	console.log("test vishal"+$("#img"+id).attr('src'));
        $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');

    }

    $('#form-submit').submit(function(e){
        var immPatient = $('#immPatient').val();
        var cnt =0;
        
        if(immPatient.trim() ==""){
            $('#immPatient_error').html("Signature should be required");
            cnt =1;
        }

        if(cnt ==1){
            return false;
        }else{
            return true;
        }
    })
    
</script>