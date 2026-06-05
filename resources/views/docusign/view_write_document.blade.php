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
    <link href="<?php echo URL::to('/'); ?>/assets/esign/bower_components/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL::to('/'); ?>/assets/esign/Ionicons/ionicons.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=B612+Mono:400,400i,700|Charm:400,700|EB+Garamond:400,400i,700|Noto+Sans+TC:400,700|Open+Sans:400,400i,700|Pacifico|Reem+Kufi|Scheherazade:400,700|Tajawal:400,700&amp;subset=arabic"
        rel="stylesheet">
    <!-- Bootstrap CSS -->

    <link href="<?php echo URL::to('/'); ?>/assets/esign/libs/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?php echo URL::to('/'); ?>/assets/esign/libs/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="<?php echo URL::to('/'); ?>/assets/esign/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->

    <script src="<?php echo URL::to('/'); ?>/assets/esign/js/jscolor.js"></script>
    <link href="<?php echo URL::to('/'); ?>/assets/esign/AdminLTE.min.css" rel="stylesheet">
    <link href="<?php echo URL::to('/'); ?>/assets/esign/libs/jquery-ui/jquery-ui.css" rel="stylesheet">
     
    <link href="<?php echo URL::to('/'); ?>/assets/esign/style1111.css?id=<?php echo time(); ?>" rel="stylesheet">
    <script src="<?php echo URL::to('/'); ?>/assets/esign/js/jquery-3.2.1.min.js"></script>
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/plugins/font-awesome-4.6.3/font-awesome.min.css">
    <input type="hidden" class="siteURL" value="<?php echo URL::to('/'); ?>/">
    <link href="{{ asset('assets/modulejs/css/view_write_document.css')}}?<?php echo time(); ?>" rel="stylesheet">
    <link href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}" rel="stylesheet">
   

</head>
<style>
    #modal-default-view-iframe{
        z-index: 999999
    }
</style>

<body>
    <div class="container">

    
    <div class="content">

        <div class="page-title" style="overflow:visible;">
            <div class="pull-right page-actions text-right">
                <button class="btn btn-success btn-responsive launch-editor"><i class="ion-edit"></i>Manage Fields &
                    Edit</button>
                <button class="btn btn-primary" onclick="window.location.href='{{ url()->previous()}}'">Back
                    List</button>

            </div>
            
            <h3 class="title-responsive text-left"><?php if(isset($document->document_name)) { echo $document->document_name; } ?><br></h3>
            <p class="text-muted text-left"></p>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="light-card document">
                    <div class="signer-document">
                        <!-- open PDF docements -->
                        <div class="document-pagination">
                            <div class="pull-left text-left">
                                <button id="prev" class="btn btn-default btn-round"><i
                                        class="ion-ios-arrow-left"></i></button>
                                <button id="next" class="btn btn-default btn-round"><i
                                        class="ion-ios-arrow-right"></i></button>
                                <span class="text-muted ml-15">Page <span id="page_num">0</span> of <span
                                        id="page_count">0</span></span>
                            </div>
                            <div class="pull-right text-right">
                                <button class="btn btn-default btn-round btn-zoom" zoom="plus"><i
                                        class="ion-plus"></i></button>
                                <button class="btn btn-default btn-round btn-zoom" zoom="minus"><i
                                        class="ion-minus"></i></button>

                            </div>
                        </div>
                        <div class="document-load">
                            <div class="loader-box">
                                <div class="circle-loader"></div>
                            </div>
                        </div>
                        <input type="hidden" name="" id="tempids" value="">
                        <div class="text-center">
                            <div class="document-map"></div>
                            <canvas id="document-viewer"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="signer-overlay">

        <div class="signer-overlay-header">
            <div class="signer-overlay-logo text-left">
                <a href="https://www.cdpasny.com"><img src="" class="img-responsive"></a>
            </div>
            <div class="signer-overlay-title text-center">
                <h4 class="header-document-title">Document Editor</h4>
                <p class="header-document-subtitle">Manage Fields & Signatures</p>
            </div>
            <div class="signer-overlay-action text-right" id="saves_id" style="margin-top:10px !important">
                <button class="btn btn-responsive btn-default close-editor-overlay"><i
                        class="ion-ios-close-outline"></i> Close </button>
                <button class="btn btn-responsive btn-primary signer-save"><i class="ion-ios-checkmark-outline"></i>
                    <span>Save</span> </button>
            </div>
            <div class="signer-overlay-actions" id="setheader_id" style="display:none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-5 text-left">
                            <label>Click on the fields to show when trigger field =</label>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="col-md-5">
                            <div id="txtId">
                                <div class="input-group input-group-lg">
                                    <input type="text" name="" class="form-control testAssign_sub"
                                        onblur="getAssignValues()">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-warning dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                            <span class="fa fa-cogs"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="getTexts('SpecifiedText')">Specified
                                                    Text</a></li>
                                            <li><a href="#" onclick="getTexts('anyText')">AnyText</a></li>

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
                        </div>
                        <div class="col-md-2 text-left"><button class="btn btn-primary" onclick="successConditional()"> Done
                            </button> <button class="btn btn-default" onclick="CloseConditional()"> Close </button>
                        </div>
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
        <input type="hidden" name="" id="mainCount" value="<?php echo $count; ?>">

        <!-- Modern Action Tools Section -->

        <!-- Document Canvas with Sidebar Section -->
        <div class="row modern-canvas-row">
            <div class="col-md-12">
                <!-- Hidden original sidebar (keep for drag functionality) -->
                <div class="col-md-2 margintop text-left" id="leftside">
                    <h3 class="text-left">Action</h3>
                    <div class="box box-solid">
                        <div class="box-body no-padding">
                            <ul class="nav nav-pills nav-stacked" id="dragdiv">
                                <li class="signer-tools signstatus" tool="signature" action="true"><a
                                        href="#">Signature</a></li>
                                <li class="signer-tools stampstatus" tool="stamp" action="true"><a
                                        href="#">Stamp</a></li>
                                <li class="signer-tools datesigned" tool="datesigned" action="true"
                                    style=""><a href="#">Date Signed </a></li>
                                <li class="signer-tools text" tool="text" action="true" style=""><a
                                        href="#">Text
                                    </a></li>
                                <li class="signer-tools checkboxs" tool="checkboxs" action="true"
                                    style="display: none"><a href="#">Checkbox</a></li>
                                <li class="dropdowsns" tool="dropdowsns" action="true" style="display: none"><a
                                        href="#">Dropdown</a></li>
                                <li class="signer-tools radios" tool="radios" action="true" style="display: none">
                                    <a href="#">Radio</a>
                                </li>
                                <li class="signer-tools signstatus_verify" tool="signstatus_verify" action="true">
                                    <a href="#">Verify By</a>
                                </li>
                                <li class="signer-tools eraser-tool" id="eraserToggleBtn"><a
                                        href="#"><i class="fa fa-eraser"></i> Eraser</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Canvas Area (Flexible Width) -->
                <div class="col-md-8 modern-canvas-area text-center">
                    <div class="signer-overlay-previewer light-card test1"></div>
                </div>

                <!-- Properties Panel (Right Side - Visible) -->
                <div class="col-md-2 margintop temp  text-left" id="vishal123" style="width:272px;margin-left:-30px !important;margin-right:-30px !important">
                </div>
            </div>
        </div>

        <div class="signer-overlay-footer">

        </div>
        <div class="signer-assembler"></div>
        <div class="signer-builder"></div>
    </div>
    </div>
    </div>
    

    <div class="modal fade" id="modal-default" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Signature Pad</h4>
                </div>
                <div class="modal-body">
                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            <div class="row" style="margin-left:-1px;margin-right:-1px">
                                <div class="col-md-4 text-center clickDivSection" data-value="Upload">
                                    <h4><input type="radio" name="typebod" checked
                                            value="2" /> Upload</h4>
                                </div>

                                <div class="col-md-4 text-center clickDivSection" data-value="Draw">
                                    <h4><input type="radio" name="typebod"
                                            value="0" /> Draw </h4>
                                </div>
                                <div class="col-md-4 text-center clickDivSection" data-value="Type">
                                    <h4><input type="radio" name="typebod" value="1"/>Type</h4>
                                </div>

                            </div>
                            <input type="hidden" id="imagesId">
                            <div id="signaturePageBody">
                                <canvas width="550" height="500" id="rename_canvas"
                                    style="touch-action: none;"></canvas>
                            </div>

                            <div id="TypeBody">
                                <div class="row">
                                    <div class="col-md-10 text-left">
                                        <input type="text" class="form-control" value="" id="textboxxs_id">
                                    </div>
                                    <div class="col-md-2 text-left">
                                        <button name="button" onclick="getSearching()"
                                            class="btn btn-primary">Go</button>
                                    </div>
                                </div>
                                <div class="" id="createNewImage">

                                </div>
                            </div>
                            <div id="fileUploadBody">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="login_id" id="login_id" value="{{$login_id}}">
                                        <input type="file" class="form-control" value="" name="file_upload"
                                            id="file_upload">
                                        <div id="error_message" style="color: red; margin-top: 5px;"></div>
                                    </div>
                                    <div class="col-md-1">

                                    </div>
                                </div>
                                <div id="show_existing_signatures"></div>
                            </div>
                        </div>


                        <div class="signature-pad--footer">
                            <div class="description">Sign above</div>

                            <div class="signature-pad--actions">
                                <div>
                                    <button type="button" class="button clear" id="clear"
                                        data-action="clear">Clear</button>


                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="testingsSave">Save
                </button>
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close</button>
                    
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- modal for stamp -->
    <div class="modal fade" id="modal-default-stamp" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Stamp</h4>
                </div>
                <div class="modal-body">
                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            <input type="hidden" id="stampId">
                            <input type="hidden" name="type" id="type" value="stamp">
                            <input type="hidden" name="login_id" id="loginId" value="{{$login_id}}">

                            <div id="fileUploadBody">
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="upload-section">
                                            <label for="stamp_upload" class="upload-label">
                                                <i class="ion-ios-cloud-upload"></i>
                                                <span>Upload Stamp Image</span>
                                                <small>Click to browse or drag and drop your stamp image</small>
                                            </label>
                                            <input type="file" class="form-control file-input" value="" name="file_upload"
                                                id="stamp_upload" accept="image/*">
                                            <div id="error_message" class="error-message"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="stamp_preview_container" style="display: none;">
                                            <div class="preview-wrapper">
                                                <div class="preview-header">
                                                    <span class="preview-title">Preview</span>
                                                    <button type="button" class="preview-close" id="clear_stamp_preview">
                                                        <i class="ion-close-round"></i>
                                                    </button>
                                                </div>
                                                <div class="preview-image-wrapper">
                                                    <img id="stamp_preview_image" src="" alt="Preview">
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div id="show_existing_stamp"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="testingsSaveStamp">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- modal for stamp -->
    <div class="modal fade" id="modal-default-view-iframe" tabindex="-1" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Review PDF</h4>
                </div>
                <div class="modal-body">
                    <iframe src="" id="iframe_classe_id" style="width:100%; height:400px;" title="<?php if(isset($document->document_name)) { echo $document->document_name; } ?>"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveDocumentDetails">
                    <i class="fa fa-spinner fa-spin d-none" id="create-document-history"></i>
                    Save</button>

                    <!-- <button type="button" class="btn btn-info" id="regenerateDocumentDetails">
                    <i class="fa fa-spinner fa-spin d-none" id="regenerate-document-history"></i>
                    Regenerate </button> -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Eraser Toolbar -->
    <div id="eraserToolbar" style="display:none;">
        <div class="eraser-toolbar-inner">
            <span class="eraser-toolbar-label"><i class="fa fa-eraser"></i> Eraser Mode</span>
            <span class="eraser-selection-count" id="eraserSelectionCount">0 selections</span>
            <button class="btn btn-sm btn-warning" id="eraserUndoBtn"><i class="fa fa-undo"></i> Undo</button>
            <button class="btn btn-sm btn-default" id="eraserClearBtn"><i class="fa fa-times"></i> Clear All</button>
            <button class="btn btn-sm btn-success" id="eraserDoneBtn">
                <i class="fa fa-check"></i> Apply Eraser
            </button>
            <button class="btn btn-sm btn-default" id="eraserCancelBtn"><i class="fa fa-ban"></i> Cancel</button>
        </div>
    </div>

    <!-- Eraser Canvas Overlay -->
    <canvas id="eraserCanvas" style="display:none;"></canvas>

    <!-- Eraser Loading Overlay -->
    <div id="eraserLoadingOverlay" style="display:none;">
        <div class="eraser-loading-content">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
            <p>Applying eraser and regenerating PDF...</p>
        </div>
    </div>

</body>
<!-- scripts -->

<script src="{{ asset('assets/esign/libs/dropify/js/dropify.min.js')}}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/simcify.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/clipboard/clipboard.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/select2/js/select2.min.js"></script>

<script src="<?php echo URL::to('/'); ?>/assets/esign/js/jquery.slimscroll.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jcanvas/jcanvas.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/touch-punch.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jcanvas/editor.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/pdf.js?time={{ time() }}"></script>
<script src="{{ asset('assets/modulejs/stamp_user.js') }}?time={{ time() }}"></script>
<!-- custom scripts -->

<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.umd.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/app.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/appsignaturepadForWriteDocument.js?<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/custom.js?keyurs=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/sweetalert/sweetalert.min.js"></script>
<script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
<script>
    var times = "<?php echo time(); ?>";
    var mainURL = "<?php echo URL::to('/'); ?>/";
    var tokens = "{{ csrf_token() }}";
</script>
<script type="text/javascript">
    var validationId = "1";
    var lookuptype = '<?php echo $document->lookup_fields; ?>';
    var caregivers = '';
    var staffs = '';
    caregivers = '<?php echo URL::to('/'); ?>/lookup/caregiver';
    if (lookuptype == 'caregiver') {

    } else if (lookuptype == 'applicant') {
        staffs = '<?php echo URL::to('/'); ?>/lookup/staff';
    }
    var url = '{{ url("esign/template/getpdfbyDocumentWriteid")}}?document_write_id={{ $document->id}}',
        // var url = '<?php echo URL::to('/'); ?>/dosusinguploads/docusign/<?php echo $document->upload_document; ?>',
        isTemplate = 'Yes',
        postChatUrl = null,
        settingsPage = null,
        saveFieldsUrl = null,
        deleteFieldsUrl = null,
        getChatUrl = null,
        signDocumentUrl = '{{ url("esign/write_document_send") }}',
        sendRequestUrl = '{{ url("/sendSignRequest") }}',
        createTemplateUrl = '',
        baseUrl = '<?php echo URL::to('/'); ?>',
        careginer = caregivers,
        staff = staffs,

        auth = true;
    document_key = '<?php echo $document->document_patient_id; ?>';
    permission: "permission";
    counter = <?php echo $count; ?>;
    tokens = "<?php echo csrf_token(); ?>";
    PDFJS.disableFontFace = true;
    PDFJS.disableWorker = true;
    PDFJS.workerSrc = '<?php echo URL::to('/'); ?>/assets/esign/js/signer.min.js?id=<?php echo time(); ?>';
    var signingKey = '<?php echo csrf_token(); ?>';
    var savedWidth = <?php if ($savedWidth != '') {
                            echo $savedWidth;
                        } else {
                            echo 799;
                        } ?>;

    // var templateFields = <?php echo $templateFields; ?>;
    var templateFields = '';

    var removeScript = '';
    var template_id = "<?php echo $document->id; ?>";
    var _VERIFIED_DATE_TIME ='{{ date("m/d/Y h:i A")}}';
    var _PORTAL_URL = "{{ env('HOST_WEB_URL')}}";
    var portalType = "{{ $portalType}}";
</script>


<script>
    function gerRequired(val, id, name) {
        if (name == 'text') {
            var textrequired = $('#text_required_' + id).prop('checked');
            if (textrequired == true) {
                $('#checks' + id).addClass('error');;
                $('#checks' + id).attr("required", "required");
                $('.signer-assembler #checks' + id).attr('vishalpatel', true);
            } else {
                $('#checks' + id).removeClass('error');
                $('#checks' + id).attr("required", false);
                $('.signer-assembler #checks' + id).attr('vishalpatel', false);
            }

        }
        if (name == 'checkbox') {
            var required = $('#checkbox_required_' + id).prop('checked');

            if (required == true) {
                $('.signer-builder .selected-element').each(function(index, value) {
                    $('#' + $(this).children().attr('id')).attr("required", "required");
                });
                $('#cid_' + id).prop('checked', true);
                $('#cid_' + id).attr("required", "required");
            } else {
                var i = 1;
                $.each($("input[name='cbox" + id + "']"), function() {
                    $('#cid_' + id + "" + i).attr("required", false);
                    i++;
                })
            }

        }
        if (name == 'radios') {
            var required = $('#radios_required_' + id).prop('checked');
            if (required == true) {
                $('.signer-builder .selected-element').each(function(index, value) {
                    $('#' + $(this).children().attr('id')).attr("required", "required");
                });
                $('#radio_wrap_' + id).prop('checked', true);

                $('#radio_wrap_' + id).attr("required", "required");
                $('#radios_' + id).addClass('error');
            } else {
                var i = 1;
                $.each($("input[name='radiogroup" + id + "']"), function() {
                    $('#radio_wrap_' + id + "" + i).attr("required", false);
                })
                $('#radio_wrap_' + id).prop('checked', false);
                $('#radio_wrap_' + id).attr("required", false);
                $('#radios_' + id).removeClass('error');
            }

        }
        if (name == 'fields') {
            var required = $('#caregiber_patient_' + id).prop('checked');

            if (required == true) {
                $('#caregivers_' + id).addClass('error');
                $('#caregivers_' + id).attr("required", "required");
                $('.signer-assembler #caregivers_' + id).attr('vishalpatel', true);
            } else {
                $('#caregivers_' + id).removeClass('error');
                $('#caregivers_' + id).attr("required", false);
                $('.signer-assembler #caregivers_' + id).attr('vishalpatel', false);
            }

        }

        if (name == 'dropdown') {
            var required = $('#drop_required_' + id).prop('checked');
            if (required == true) {
                $('#dropid' + id).addClass('error');
                $('#dropid' + id).attr("required", "required");

            } else {
                $('#dropid' + id).removeClass('error');
                $('#dropid' + id).attr("required", false);
            }
        }



    }

    function gerReadOnly(val, id, name) {

        if (name == 'text') {
            var textrequired = $('#text_read_' + id).prop('checked');
            if (textrequired == true) {
                $('#checks' + id).attr("readonly", true);
                $('#checks' + id).prop("readonly", true);
            } else {
                $('#checks' + id).prop("readonly", false);
                $('#checks' + id).attr("readonly", false);
            }

        }
        if (name == 'fields') {
            var readOnly = $('#caregiber_patient_read_' + id).prop('checked');
            if (readOnly == true) {
                $('#caregivers_' + id).prop("readonly", true);
            } else {
                $('#caregivers_' + id).prop("readonly", false);
            }
        }

        if (name == 'dropdown') {
            var required = $('#drops_read_' + id).prop('checked');

            if (required == true) {
                $('#dropid' + id).attr("readonly", true);
                $('#dropid' + id).prop("readonly", true);
            } else {
                $('#dropid' + id).attr("readonly", false);
                $('#dropid' + id).prop("readonly", false);
            }

        }
        if (name == 'checkbox') {
            var required = $('#checkbox_read_' + id).prop('checked');

            if (required == true) {
                $('.signer-builder .selected-element').each(function(index, value) {
                    $('#' + $(this).children().attr('id')).attr("readonly", "readonly");
                    $('#' + $(this).children().attr('id')).prop('checked', true);
                });

            } else {
                var i = 1;
                $.each($("input[name='checkbox_read" + id + "']"), function() {
                    $('#cid_' + id + "" + i).attr("readonly", false);
                    $('#cid_' + id + "" + i).prop('checked', false);
                })

            }

        }
        if (name == 'radios') {

            var required = $('#radios_read_' + id).prop('checked');

            if (required == true) {
                $('.signer-builder .selected-element').each(function(index, value) {
                    $('#' + $(this).children().attr('id')).attr("readonly", "readonly");
                    $('#' + $(this).children().attr('id')).prop('checked', true);
                });


            } else {
                var i = 1;
                $.each($("input[name='radiogroup" + id + "']"), function() {
                    $('#radio_wrap_' + id + "" + i).attr("readonly", false);
                    $('#radios_read_' + id).prop('checked', false);
                })


            }
        }

    }
</script>




<script src="{{ asset('/assets/esign/js/app.js') }}"></script>
<script src="{{ asset('/assets/esign/js/write-document-signer.js') }}?vvcanvas=<?php echo time(); ?>"></script>
<script src="{{ asset('/assets/esign/js/write-document-render.js') }}"></script>

<script>
    //setTimeout(function(){ GetLoadComponent(); }, 3000);
    var final_array = [];
    var temsd = [];
    var static = [];
    var RadioArray = [];
    var updatenewselecte;

    function addmore(key, val, temp) {
        var timestamp = new Date().getTime();
        var htmls = '';
        htmls += '<div class="copy_id" id="copy_id' + timestamp +
            '"><div class="row"><div class="form-group" id="remove' + timestamp +
            '"><label for="inputEmail3" class="col-md-2 control-label">Option</label><div class="col-md-9"><input type="text" class="form-control" id="inputEmail' +
            timestamp + '" onkeyup="getDropValue(this.id,' + timestamp + ',' + val +
            ')"></div> <a href="javascript:void(0)" onclick="getRemove(' + timestamp + ',' + val +
            ')"><i class="fa fa-times" aria-hidden="true"></i></a></div></div></div>';

        $('#multid' + val).append(htmls);
        var element = {
            "id": val,
            'mId': timestamp,
            'maId': 'dropdowsns_' + val,
            'response': '',
            "value": ''
        };

        var elements = Object.assign({}, element);
        final_array.push(elements);

        getGenerateArray(key, val);
    }

    function getRemove(removeId, mainId) {
        swal({
                title: 'Are you sure?',
                text: "You want to move this row",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }, function (isConfirm) {
                if(isConfirm){
                    $("#dropid" + mainId + " option[id='remove_" + removeId + "']").remove();
                    $('#remove' + removeId).remove();
                    $('#copy_id' + removeId).remove();
                    $('#remove_' + removeId).remove();
                    var test = localStorage.getItem(mainId);
                    var id = 'copy_id' + removeId;
                    var tempread = [];
                    $.each(final_array, function(index, vals) {
                        if (vals.mId != removeId) {
                            var elemt = {
                                "id": vals.id,
                                "mId": vals.mId,
                                'response': vals.response,
                                "value": vals.value
                            };
                            var mail = Object.assign({}, elemt);

                        }
                        if (mail != undefined) {
                            tempread.push(mail);
                        }
                    });

                    final_array = tempread;


                    getGenerateArray('dropdown', mainId);
                }
                
            }
        );
        
        // var confirm1 = confirm("Are you sure move this row?");
        // if (confirm1 == true) {

           

        // }

    }

    function getDropValue(Textid, countid, MainId) {

        var text = $('#' + Textid).val();

        var final = [];
        var dropsResponse = '';
        var keys = '';
        if (text != '') {
            $("#dropid" + MainId + " option[id='remove_" + countid + "']").remove();
            $(".drops_" + MainId + " option[id='remove_" + countid + "']").remove();
            dropsResponse = '<option id="remove_' + countid + '" value="' + text + '">' + text + '</option>';
            $('#' + Textid).val(text);
            $.each(final_array, function(index, vals) {
                if (vals.mId == countid && vals.id == MainId) {
                    vals.value = text;
                }
                final.push(vals);

            });

            final_array = final;

            $('.drops_' + MainId).append(dropsResponse);
            $('#dropid' + MainId).append(dropsResponse);
            getGenerateArray('dropdown', MainId);
        }


    }

    function selectValue(id, val) {
        $('#dropid' + id).append('<option value="' + val + '">' + val + '</option>');
    }

    /*Addmore of radio button option **/
    var radioGlobal = 1;
    var tempStoreSelectedId = "";
    var radioGroupId = "";

    function getAppend(id, val, name, bgcolor = null, signerid = null) {
        radioGlobal++;
        var clenght = $("input[name=" + name + "]").length;

        if (bgcolor != '') {
            bgcolor = bgcolor;
        }
        if (signerid != '') {
            signerid = signerid;
        }

        //		radios++;
        var radiosID = id + "" + radioGlobal; //new Date().getTime();
        radioGroupId = id;
        tempStoreSelectedId = id + "" + radioGlobal;
        $('<div class="signer-element selected-element radiogroup' + id + '" tempid="radiogroup_' + id +
            '" type="radio" page="' + pageNum + '" status="drop"    id="radios_' + radiosID +
            '"><input groupsName="radiogroup' + id + '" tempIds="radiogroup_' + id +
            '" type="radio" style="color:red;" name="radiogroup' + id + '" class="radio_wrap" id="radio_wrap_' +
            id + '' + radioGlobal + '" value="Radio' + radioGlobal + '" group="multipleradio' + id +
            '" backgound_color="' + bgcolor + '" signer_id="' + signerid + '"><br></div></div>').appendTo(
            ".signer-builder");
        var css = $('#radios_' + radiosID).attr('style');
        var main = css + "background-color:red;";
        clenght++;
        $('#radios_' + radiosID).attr('style', main);
        $('.next').val(radioGlobal);
        //createRadioResponse('radios',radioGlobal,radios);
    }
    /*Addmore of radio button option **/
    var CheckboxGlobal = 1;
    var CtempStoreSelectedId = "";
    var CheckGroupId = "";

    function getCheckAppend(id, val, name, bgcolor = null, signerid = null) {
        var temp = $("input[name=" + name + "]").length;


        CheckboxGlobal = temp + 1;

        if (checkTotal == undefined) {
            checkTotals = id;
        } else {
            checkTotals = checkTotal;
        }

        if (bgcolor != '') {
            bgcolor = bgcolor;
        }
        if (signerid != '') {
            signerid = signerid;
        }

        var CheckID = id + "" + CheckboxGlobal; //new Date().getTime();
        CheckGroupId = id;
        CtempStoreSelectedId = id + "" + CheckboxGlobal;

        $('<div class="signer-element"  tempId="checkgroup_' + id + '" type="checkbox" status="drop" page="' + pageNum +
            '"  id="checkboxs_' + CheckID + '"><input  tempIds="checkgroup_' + id +
            '" type="checkbox" class="checkbox_wrapper" groupsName="cbox' + checkTotal + '" tempIds="checkgroup_' +
            checkTotal + '" name="cbox' + checkTotals + '" id="cid_' + checkTotals + CheckboxGlobal + '" value="' +
            CheckboxGlobal + '" group="multiplecheck' + id + '" backgound_color="' + bgcolor + '" signer_id="' +
            signerid + '"></div><br>').appendTo(".signer-builder");
        var css = $('#checkboxs_' + CheckID).attr('style');
        var main = css + "background-color:red;";
        $('#checkboxs_' + CheckID).attr('style', main);

        var DynamicAddmoreResponse = [];
        var globalAdd = '';
        globalAdd += '<div class="mycheck' + checkTotals + '' + CheckboxGlobal +
            '"><div class="row"><div class="form-group"><div class="col-md-2"><label for="inputEmail3" class="multiplecom"><input type="checkbox" onclick="getOnClick()"></label></div><div class="col-md-10"><input type="text" class="form-control W-50" id="inputEmail3" placeholder="Checkbox value"></div></div></div></div>';

        $('.chkboxval' + id).append(globalAdd)


    }
    /* End Add More of radio button option */
</script>
<!-- custom scripts -->
<script>
    $('.signer-overlay').scroll(function() {

        var distance = $('.signer-overlay').scrollTop();
        var left = document.getElementById("leftside");
        if (distance > 5) {
            left.className = 'col-md-2 margintop stick';
        } else {
            left.className = 'col-md-2 margintop';
        }

        var rights = document.getElementById("vishal123");
        if (distance > 5) {
            rights.className = 'col-md-2 margintop temp stick';
        } else {
            rights.className = 'col-md-2 margintop temp';
        }

    });

    function getTexts(value) {

        $('.testAssign_sub').attr('disabled', false);
        if (value == 'anyText') {
            $('.testAssign_sub').attr('disabled', true);
        }
        $('#seperate_value').val(value);
    }
</script>
<script>
    var getCheckbox = "{{ route('get-form-by-checkbox') }}";
    var getRadio = "{{ route('get-form-by-radio') }}";
</script>
<script>
    $(document).ready(function() {
        $('#signaturePageBody').hide();
        $('#TypeBody').hide();
        $('#fileUploadBody').hide();

        var selectedType = $('input[name="typebod"]:checked').val();

        if (selectedType == 0) {
            showSignaturePad();
        } else if (selectedType == 1) {
            showTypePad();
        } else if (selectedType == 2) {
            showFileUpload();
        }

        // ==================== Modern Action Tools Functionality ====================

        // Toggle Action Tools Section
        $('#toggleActionTools').on('click', function() {
            var $grid = $('#actionToolsGrid');
            var $icon = $(this).find('i');

            if ($grid.is(':visible')) {
                $grid.slideUp(400);
                $icon.removeClass('ion-ios-arrow-up').addClass('ion-ios-arrow-down');
                $(this).addClass('collapsed');
            } else {
                $grid.slideDown(400);
                $icon.removeClass('ion-ios-arrow-down').addClass('ion-ios-arrow-up');
                $(this).removeClass('collapsed');
            }
        });

        // Action Tool Button Click - Trigger existing tool functionality
    });
</script>
<script>
    var jsonresp = <?php
                    //$test = array('eutemia-i.italic.ttf','Adinda Melia.otf','Aerotis.otf','Agashi Signature Demo.otf','Airin.ttf');
                    $test = ['Adinda Melia.otf', 'Agashi Signature Demo.otf', 'AlfridaDemoSignature.ttf', 'Bellisya.otf', 'AmarulaPersonalUse.ttf'];
                    echo json_encode($test); ?>;

    function getMeta(url, cb) {
        const img = new Image();
        img.onload = () => cb(null, img);
        img.onerror = (err) => cb(err);
        img.src = url;
    }

    function getSignatureSuccess(filename, id, type = "") {
        var res = filename.replace(/\n/g, '');


        if (type === "upload" || type === "select" || type === "upload_stamp") {
            $(`#signatures_signer_${id}`).attr('src', res);
           
            getMeta(res, (err, img) => {
             
                $(`#sign_${id}`).attr('width', img.naturalWidth);
                $(`#sign_${id}`).attr('height', img.naturalHeight);
            });
            


        } else {
            // $(`#signatures_signer_${id}`).attr('src', '<?php echo URL::to('/'); ?>/patientWriteDocument/' + res);
            $(`#signatures_signer_${id}`).attr('src', res );
        }
        if (type !== "upload_stamp") {
            //$(`#signatures_signer_${id}`).attr('style', 'width:80Px;');
        } else {
            $(`#signatures_signer_${id}`).attr('style', '');
        }
        $(`#signatures_signer_${id}`).attr('dataids', 1);
        $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');

    }

    function getStampSuccess(filename, id, type = "") {
        var res = filename.replace(/\n/g, '');

        if (type === "upload_stamp") {
            $(`#stamp_signer_${id}`).attr('src', res);
        } else {
            $(`#stamp_signer_${id}`).attr('src', '<?php echo URL::to('/'); ?>/patientWriteDocument/' + res);
        }

        $(`#stamp_signer_${id}`).attr('style', '');

        $(`#stamp_signer_${id}`).attr('dataids', 1);
        $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');

    }

    // Make sure these functions are globally accessible
    window.mySign = function(ids) {
        var id = ids;

        getWebviewCanvas(id);
        getSearching();
    }

    window.myStamp = function(ids) {
        var id = ids;

        getWebviewCanvasStamp(id);
    }

    // Add delegated event handlers for signature and stamp clicks
    $(document).on('click', '.img_wrap', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var imgId = $(this).attr('id');
        if (imgId && imgId.startsWith('signatures_signer_')) {
            var id = imgId.replace('signatures_signer_', '');
            $('input[name="typebod"]').prop("checked",false);
	        $('input[name="typebod"][value="2"]').prop("checked",true);
            $('#signaturePageBody').attr('style','display:none')
            $('#TypeBody').attr('style','display:none')
             $('#fileUploadBody').attr('style','')
            mySign(id);
        }
    });

    $(document).on('click', 'img[id^="stamp_signer_"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var imgId = $(this).attr('id');
        if (imgId && imgId.startsWith('stamp_signer_')) {
            var id = imgId.replace('stamp_signer_', '');
         
            myStamp(id);
        }
    });

    function getWebviewCanvasStamp(imgid) {
        $('#modal-default-stamp').modal('show');
        $('#stamp_upload').val('');
        $('#stampId').val(imgid);
        $('#stamp_preview_container').hide();
        getExistingSignaturestamp();
    }

    // Stamp Upload Preview Functionality
    $('#stamp_upload').on('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                $('#error_message').text('Please select a valid image file');
                $('#stamp_preview_container').hide();
                return;
            }

            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                $('#error_message').text('File size must be less than 5MB');
                $('#stamp_preview_container').hide();
                return;
            }

            // Clear any error messages
            $('#error_message').text('');

            // Read and display the file
            const reader = new FileReader();

            reader.onload = function(event) {
                $('#stamp_preview_image').attr('src', event.target.result);
                $('#stamp_file_name').text(file.name);
                $('#stamp_file_size').text(formatFileSize(file.size));
                $('#stamp_preview_container').fadeIn(300);
            };

            reader.readAsDataURL(file);
        }
    });

    // Clear preview button
    $('#clear_stamp_preview').on('click', function() {
        $('#stamp_upload').val('');
        $('#stamp_preview_container').fadeOut(300);
        $('#stamp_preview_image').attr('src', '');
        $('#error_message').text('');
    });

    // Format file size helper function
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Clear preview when modal is closed
    $('#modal-default-stamp').on('hidden.bs.modal', function() {
        $('#stamp_upload').val('');
        $('#stamp_preview_container').hide();
        $('#stamp_preview_image').attr('src', '');
        $('#error_message').text('');
    });

    function getWebviewCanvas(imgid) {
        $('#modal-default').modal('show');
        $('#file_upload').val('');
        $('#modal-default #signature-pad .signature-pad--body canvas').attr('width', 550);
        $('#modal-default #signature-pad .signature-pad--body canvas').attr('height', 200);
        $('#imagesId').val(imgid);
        getExistingSignatures();

        //getCreateNewImages(imgid);
    }

    function getSearching() {
        var names = $('#textboxxs_id').val();
        var imageId = $('#imagesId').val();
        getCreateNewImages(imageId);
    }


    function getCreateNewImages(id) {
        var textname = $('#textboxxs_id').val();

        if (textname != '') {
            $('#createNewImage').html("");
          
            $.each(jsonresp, function(i, v) {
                
                $.ajax({
                    url: mainURL + 'create-image-using-type', // Upload Script
                    type: "POST",

                    data: {
                        'textbox': textname,
                        'fontsize': v,
                        '_token': '{{ csrf_token() }}' // Add CSRF token
                    },
                    success: function(response) {
                        response = response.replace(/\n/g, '');
                        let cnt =Math.floor(100000000 + Math.random() * 900000000);
                        var htm =
                            '<div class="form-group types_response_click" data-attr-id="'+id+'" onclick="saveSelectedClick('+cnt+')"><div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios'+cnt+'" data-id="' +
                            id + '" value="' + response +
                            '"><i class="input-helper"></i></label><img  src="' + response +
                            '" style="width: 200px;"></div>';
                        $('#createNewImage').append(htm);
                    }
                })
              
            });

        }
    }

    function getAdminSign() {
        var department = "web";
        var images = $('input[name="optionsRadios"]:checked').val();
        var imgid = $('input[name="optionsRadios"]:checked').attr('data-id');

        data = images;

        // $('img').each(function(i, v) {

        //     var id = $(this).attr('id')
        //     if (department == 'web') {
        //         $("#" + id).attr('src', data);
        //     } else {
        //         $("#" + id).attr('src', data);
        //     }
        //     $("#" + id).attr('style', 'width:80px;');
        //     $("#" + id).attr('dataids', 1);
        //     $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');
        // });

        $("#signatures_signer_" + imgid).attr('src', images);
        $("#signatures_signer_" + imgid).attr('style', 'width:100px;');
        $("#signatures_signer_" + imgid).attr('dataids', 1);
        $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');
        $('#modal-default').modal('hide');
    }

    function getSubmit(blob) {
        var formData = new FormData();
        formData.append("image", blob);
        formData.append("_token", '{{ csrf_token() }}');
        //formData.append("_token",'7K04mjtA5BWzqQSrFdWNgKhYvw9KXpfb98Ij5wgE');

        $.ajax({
            url: mainURL + 'esign/docusign/esign-signature-write-document', // Upload Script
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

    function showSignaturePad() {
        $('#signaturePageBody').show();
        $('#TypeBody').hide();
        $('#fileUploadBody').hide();
        $('#clear').show();
        $('.description').show();
    }

    function showTypePad() {
        $('#signaturePageBody').hide();
        $('#TypeBody').show();
        $('#fileUploadBody').hide();
        $('#clear').css('display', 'none');
        $('.description').css('display', 'none');
    }

    function showFileUpload() {
        $('#fileUploadBody').show();
        $('#signaturePageBody').hide();
        $('#TypeBody').hide();
        $('#clear').css('display', 'none');
        $('.description').css('display', 'none');

    }


    $('#stampUserForm').click(function(i, v) {
        var department = "web";
        var data = $('input[name="stamp_user"]').val();
        $('img').each(function(i, v) {
            var id = $(this).attr('id')
            if (department == 'web') {
                $("#" + id).attr('src', data);
            } else {
                $("#" + id).attr('src', data);
            }
            $("#" + id).attr('style', 'width:80px;');
            $("#" + id).attr('dataids', 1);
            $('.signeeddate').val('<?php echo date('m/d/Y'); ?>');
        });
        $("#stampUserFormModal").modal("hide");

    })


    function getSubmitFileUpload(file) {
        var formData = new FormData();
        formData.append("file_upload", file);
        formData.append("login_id", $('#login_id').val());
        formData.append("_token", '{{ csrf_token() }}');

        $.ajax({
            url: mainURL + 'esign/docusign/upload-signature-write-document',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response != '') {
                    var imgid = $('#imagesId').val();

                    getSignatureSuccess(response.url, imgid, 'upload')
                    $('#modal-default').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error("File upload error:", error);
            }
        });
    }

    function getExistingSignatures() {
        let loginId = $('#login_id').val();

        $.ajax({
            url: '{{ url("esign/get-patient-signatures") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                login_id: loginId
            },
            success: function(response) {
                var htmlResponse = "<div class='row'>";
                if (response.data.length != 0) {
                    $.each(response.data, function(i, v) {
                        var labelName = "";
                        var signature_name ="'"+v.signature_name+"'";
                        var addNewStyle="";
                        if(v.signature_name ==""){
                            addNewStyle ='margin-top:28%'
                        }
                        if(loginId !=""){
                            labelName =`<h6 class="img-title"><span id="new_view_signature_name_${v.id}">${v.signature_name}</span></h6>
                            <div class="sign-action-buttons" style="${addNewStyle}">
                                <a class="sign-edis-a" onclick="editSignatureName(${v.id})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a class="sign-delete-a" onclick="deleteSignature('${v.id}','signature')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                    Delete
                                </a>
                            </div>`
                        }
                        htmlResponse += `<div class="col-md-6">
                                            <div class="signature-wrapper" style="position: relative; margin: 10px; border: 1px solid #ccc; background: #fff; border-radius: 8px; padding: 10px; text-align: center;"
                                                data-id="${v.id}">

                                                <img src="${v.file_upload}" alt="Signature"
                                                    class="signature-thumbnail"
                                                    style="height: 170px; width: 100%; object-fit: contain; cursor: pointer;"
                                                    onclick="selectExistingSignature('${v.file_upload}')" />
                                                    <input type="hidden" id="signature_names_${v.id}" value="${v.signature_name}">
                                                    <div class="cust-esign-template-css view_edit_images_${v.id}">${labelName}</div>
                                                    <div class="edit_images_${v.id} hide cust-edit-btn" style="margin-top:20%">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <input type="text" name="signature_name_${v.id}" id="signature_name_${v.id}" class="form-control" placeHolder="Signature Name" value="${v.signature_name}">
                                                                        <span id="signature_name_${v.id}_error" class="error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <div class="row">
                                                            <a class="btn btn-sm btn-primary" onclick="updateImages(${v.id})">Update</a>
                                                            <a class="btn btn-sm btn-default"  onclick="cancelImages(${v.id})">Cancel</a>
                                                        </div>

                                                    </div>
                                            </div>
                                        </div>`;
                    })
                }
                htmlResponse += '</div>';
                $('#show_existing_signatures').html(htmlResponse);

            },
            error: function(xhr) {
                console.error("Error loading signatures:", xhr.responseText);
            }
        });
    }

    function getSubmitFileUploadStamp(file) {
        var formData = new FormData();
        formData.append("file_upload", file);
        formData.append("login_id", $('#loginId').val());
        formData.append("type", $('#type').val());
        formData.append("_token", '{{ csrf_token() }}');
        $.ajax({
            url: mainURL + 'esign/docusign/upload-signature',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response != '') {
                    var imgid = $('#stampId').val();
                    getStampSuccess(response.url, imgid, 'upload_stamp')
                    $('#modal-default-stamp').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error("File upload error:", error);
            }
        });
    }

    function getExistingSignaturestamp() {
        let loginId = $('#loginId').val();
        let type = $('#type').val();
        $.ajax({
            url: '{{ url("esign/get-patient-signatures") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                login_id: loginId,
                type: type
            },
            success: function(response) {
                var htmlResponse = "<div class='row'>";
                if (response.data.length != 0) {
                    $.each(response.data, function(i, v) {
                        var labelName = "";
                        var signature_name ="";
                 
                        if(v.signature_name != "" && v.signature_name !=null ){
                            signature_name ="'"+v.signature_name+"'";
                        }
                     
                        if(loginId !="" ){
                          
                           // labelName =`<h6 class="img-title"><span id="new_view_signature_name_${v.id}">${v.signature_name}</span></h6><div class="sign-edis"><a  class="sign-edis-a" onclick="editSignatureName(${v.id})">Edit</a></div>`
                        }

                        var labelName = "";
                     
                        if(loginId !=""){
                            labelName =`<h6 class="img-title"><span id="new_view_signature_name_${v.id}">${v.signature_name}</span></h6>
                            <div class="sign-action-buttons">
                                <a class="sign-edis-a" onclick="editSignatureName(${v.id})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a class="sign-delete-a" onclick="deleteSignature('${v.id}','stamp')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                    Delete
                                </a>
                            </div>`
                        }
                        htmlResponse += `<div class="col-md-6">
                                            <div class="signature-wrapper"
                                                style="position: relative; margin: 10px; border: 1px solid #ccc; background: #fff; border-radius: 8px; padding: 10px; text-align: center;"
                                                data-id="${v.id}">

                                                <img src="${v.file_upload}" alt="Signature"
                                                    class="signature-thumbnail"
                                                    style="height: 100px; width: 100%; object-fit: contain; cursor: pointer;"
                                                    onclick="selectExistingStamp('${v.file_upload}')" />
                                                <input type="hidden" id="signature_names_${v.id}" value="${v.signature_name}">
                                                    <div class="cust-esign-template-css view_edit_images_${v.id}">
 ${labelName}
                                                    </div>
                                                <div class="edit_images_${v.id} hide cust-edit-btn">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <input type="text" name="signature_name_${v.id}" id="signature_name_${v.id}" class="form-control" placeHolder="Stamp Name" value="${v.signature_name}">
                                                                    <span id="signature_name_${v.id}_error" class="error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <div class="row">
                                                        <a class="btn btn-sm btn-primary" onclick="updateImages(${v.id})">Update</a>
                                                        <a class="btn btn-sm btn-default"  onclick="cancelImages(${v.id})">Cancel</a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>`;
                    })
                }
                htmlResponse += '</div>';
                $('#show_existing_stamp').html(htmlResponse);
            },
            error: function(xhr) {
                console.error("Error loading stamp:", xhr.responseText);
            }
        });
    }

    function selectExistingSignature(imageUrl) {
        var imgid = $('#imagesId').val();

        // Highlight selected signature
        $('.signature-thumbnail').removeClass('selected');
        $(`img[src="${imageUrl}"]`).addClass('selected');

        // Call signature success function
        getSignatureSuccess(imageUrl, imgid, 'select');
        $('#modal-default').modal('hide');
        $('#file_upload').val('');
    }

    function selectExistingStamp(imageUrl) {
        var imgid = $('#stampId').val();

        // Highlight selected signature
        $('.signature-thumbnail').removeClass('selected');
        $(`img[src="${imageUrl}"]`).addClass('selected');

        // Call signature success function
        getStampSuccess(imageUrl, imgid, 'upload_stamp');
        $('#modal-default-stamp').modal('hide');
        $('#stamp_upload').val('');
        $('#stamp_preview_container').hide();
        $('#stamp_preview_image').attr('src', '');
    }

    function deleteSignature(signatureId,type) {
        if(type =='signature'){
            var msg = "You want to delete this signature.";
        }else{
            var msg = "You want to delete this stamp.";
        }
        swal({
                title: 'Are you sure?',
                text:msg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }, function (isConfirm) {
                if(isConfirm){
                    $.ajax({
                        url: '{{ url("esign/delete-signature")}}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            signature_id: signatureId
                        },
                        success: function(response) {

                            if (response.status) {
                                $('[data-id="' + signatureId + '"]').remove();
                                if(type =='signature'){
                                    getExistingSignatures()
                                }else{
                                    getExistingSignaturestamp()
                                }
                            } else {
                                alert("Failed to delete signature.");
                            }
                        }
                    });
                }
            }
        );
        
        // if (confirm("Are you sure you want to delete this signature?")) {
            
        // }
    }

    var globalResponse = <?php echo $Signinsert; ?>;
    var globalFinalArray = [];
    var esignParameterID = [];
    function submitResponseWriteDocument(docusignAction="",docusignWidth="",docusignKey="",docusignSigningKey="",docusignHeight="") {
        var final_array = [];
        var data = [];
        var i = 1;
        cnt = 0;
        var width;
        var height;
        $('#loader_nnnn').attr('class', 'fa fa-circle-o-notch fa-spin');
        
        $.each(JSON.parse(docusignAction), function(index, val) {

            var text_val = $('#' + val.id).val();

            var value = "";
            var sThisVal = "";
            if (val.type == 'radio') {
                var checked = $("input[name='" + val.name + "']").is(":checked");

                var classes = $('.' + val.bold).attr('style');
                if (checked == false && val.required == 'true' && !$('#' + val.bold).parent().hasClass(
                        "Depending")) {
                    var ereturns = classes + ";border: 2px solid #ff0000;";
                    cnt++;

                    alert("Page " + val.page + " radio button  is missing information please review");

                    $('.' + val.bold).attr('style', ereturns);
                    return false;
                } else {
                    var ereturns = classes;
                }
            }
            if (val.type == 'dropdown') {
                text_val = $('#' + val.id + ' option:selected').val()
            }
            if (val.type == 'checkbox') {
                var checked = $("#" + val.bold).is(":checked");
                var classes = $('.' + val.bold).attr('style');
                if (checked == false && val.required == true && !$('#' + val.id).parent().hasClass(
                        "Depending")) {
                    var ereturns = classes + ";border: 2px solid #ff0000;";
                    cnt++;
                    $('.' + val.bold).attr('style', ereturns);
                    return false;
                }

            }

            if (val.type == "image") {
                if (val.signer_id == "Patient") {
                    imgRequired = $("#img" + i).attr('dataids', 1);
                } else {
                    imgRequired = $("#img" + i).attr('dataids');
                }
                width = val.width;
                height = val.height;
                value = $("#img" + i).attr('src');

                // if (imgRequired == undefined || imgRequired == 0 ) {
                // 	$('#img' + i).addClass('errors');
                // 	alert("Page " + val.page + " signatures  is missing information please review");
                // 	//alert("There is missing signatures in page "+val.page)

                // 	cnt++;
                // 	return false;
                // }

            } else {
                width = val.width;
                height = val.height;
                if (val.type == 'text' && val.readOnly == "readonly" && val.temp1 != 'intake' && val.temp1 !=
                    'caregiver') {
                    if (val.readOnly == "readonly" && val.placeHolder != 'TextBox') {
                        value = val.text;
                    } else {
                        value = val.placeHolder;
                    }

                } else {
                    value = text_val;
                }

            }
            if (val.type == "stamp") {
                imgRequired = $("#img" + i).attr('dataids');
                width = '';
                height = '';
                value = $("#img" + i).attr('src');
                // if (imgRequired == undefined || imgRequired == 0) {
                //     $('#img' + i).addClass('errors');
                //     alert("Page " + val.page + " stamp  is missing information please review");
                //     cnt++;
                //     return false;
                // }
            }
            if (val.type == 'dropdown') {

                var dropId = val.id.split('_');
                var dropIds = 'dropid' + dropId[1];
                var checktrueOrNot = $('#' + dropIds).hasClass("Depending");

                if (val.required == 'true' && text_val == 'select' && checktrueOrNot == false) {
                    $('#' + val.id).addClass('errors');
                    alert("Page " + val.page + " dropdown  is missing information please review");

                    cnt++;
                    return false;
                }
            }

            if (val.type == 'text') {
                if (val.required == "true" && text_val == '' && !$('#' + val.id).parent().hasClass(
                        "Depending")) {

                    //alert("All fields required!");
                    $('#' + val.id).addClass('errors');
                    //alert("This page required fields "+val.page)
                    alert("Page " + val.page + " textbox  is missing information please review");
                    cnt++;
                    return false;
                    cnt++;
                } else {
                    $('#' + val.id).removeClass('errors');
                }
            }

            if (val.type == 'radio') {
                var selectedid = $("input[name='" + val.name + "']:checked").attr('id');

                checked = '';
                if (selectedid == val.bold) {
                    checked = 1;
                }

            }

            final_array.push({
                name: val.name,
                id: val.id,
                type: val.type,
                page: val.page,
                degree: val.degree,
                xPos: val.xPos,
                yPos: val.yPos,
                width: width,
                height: height,
                text: value,
                placeHolder: val.placeHolder,
                align: val.align,
                bold: val.bold,
                italic: val.italic,
                fontsize: parseInt(val.fontsize),
                fontfamily: val.fontfamily,
                font: val.font,
                group: val.group,
                underline: val.underline,
                strikethrough: val.strikethrough,
                color: val.color,
                drawing: val.drawing,
                checked: checked,
                permission: headers,
                textsmall: val.textsmall,
                doctor_id: val.doctor_id,
                doctor_name: val.doctor_name
            });



            i++;
        });
        if (cnt != 0) {
            return false;
        } else {
            var actions = JSON.stringify(final_array);
            $('#create-new-document-history').removeClass('d-none')
           
            var href = "{{ url('esign/docusign/submit-form-write-document')}}";

            $.ajax({
                global: false,
                async: false,
                url: href,
                type: "POST",
                data: {
                    'id': '<?php echo $document->id; ?>',
                    'action': actions,
                    "permission": headers,
                    '_token': '{{ csrf_token() }}',
                    'docusignAction':docusignAction,
                    'docusignWidth':docusignWidth,
                    'docusignHeight':docusignHeight,
                    'docusignKey':docusignKey,
                    'docusignSigningKey':docusignSigningKey
                },
                success: function(response) {


                    setTimeout(function(e) {
                        $('#create-new-document-history').addClass('d-none')
                        hideLoader();
                    }, 1000);
                    $('#exist_id').attr('style', '');

             
                    $('#modal-default-view-iframe').modal('show');
                    $('#iframe_classe_id').attr('src',response.data?.file);
                    globalFinalArray = [];
                    var tempGlobalObject = {'id':'<?php echo $document->id; ?>','docusignAction':docusignAction,'docusignWidth':docusignWidth,'docusignKey':docusignKey,'docusignSigningKey':docusignSigningKey,'permission':headers,'file_name':response.data?.file_name,'demo_file':response.data?.demo_file,'converted_file':response.data?.converted}
                    globalFinalArray.push(tempGlobalObject)
                    
                },
                error: function(jqXHR, exception) {
                    $('#create-new-document-history').addClass('d-none')
                    hideLoader();
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                    $('#loader_nnnn').attr('class', 'fa fa-circle-o-notch fa-spin');
                }
            });
        }
    }
    function editSignatureName(id){
        $('.view_edit_images_'+id).addClass('hide');
        $('.edit_images_'+id).removeClass('hide');
        var signature_names = $('#signature_names_'+id).val();
        $('#signature_name_'+id).val(signature_names);
    }

    function cancelImages(id){
        $('#signature_name_'+id+'_error').html("");
        var signature_names = $('#signature_names_'+id).val();
        $('#new_view_signature_name_'+id).html(signature_names)
        $('.view_edit_images_'+id).removeClass('hide');
        $('.edit_images_'+id).addClass('hide');
    }

    function updateImages(id,type=""){
        var signature_name = $('#signature_name_'+id).val();
        var cnt =0;
        $('#signature_name_'+id+'_error').html("");

        if(signature_name.trim() ==''){
            if(type !=""){
                $('#signature_name_'+id+'_error').html("Please enter Stamp Name");
            }else{
                $('#signature_name_'+id+'_error').html("Please enter Signature Name");
            }
            
            cnt =1;
        }

        if(cnt ==1){
            return false;
        }else{
            $.ajax({
               
                url: "{{ url('esign/update-signature-name')}}",
                type: "POST",
                data: {
                    'id': id,
                    'signature_name': signature_name,
                    'object_id':"{{ $document->main_intakeId}}",
                    '_token': '{{ csrf_token() }}',
                   
                },
                success:function(response){
                    $('#new_view_signature_name_'+id).html(signature_name)
                    $('#signature_names_'+id).val(signature_name);
                    $('.view_edit_images_'+id).removeClass('hide');
                    $('.edit_images_'+id).addClass('hide');
                },
                error:function(jqr){
                    alert(jqr.responseJSON.error_msg);
                }

            });
        }
    }

    $('.clickDivSection').click(function(e){
       var section = $(this).attr('data-value');
       if(section =='Upload'){
            showFileUpload();
            $('input[value="2"]').prop("checked",true);
       }
       if(section =='Draw'){
            showSignaturePad()
            $('input[value="0"]').prop("checked",true);
        }
        if(section =='Type'){
            showTypePad()
            $('input[value="1"]').prop("checked",true);
        }

    })

    function saveSelectedClick(id){
      $('#optionsRadios'+id).prop("checked",true)
    }

    $('body').on('click','#saveDocumentDetails',function(e){
        $('#create-document-history').removeClass('d-none');
        $.ajax({
            global: false,
            async: false,
            url: '{{ url("esign/write_document_send") }}',
            type: "POST",
            data: {
                "actions": globalFinalArray[0].docusignAction,
                "_token": "{{ csrf_token()}}",
                "docWidth": globalFinalArray[0].docusignWidth,
                "document_key": globalFinalArray[0].docusignKey,
                "signing_key": globalFinalArray[0].docusignSigningKey,
                'file_name':globalFinalArray[0].file_name,
                'demo_file':globalFinalArray[0].demo_file,
                'converted_file':globalFinalArray[0].converted_file,
            },
            success: function(response) {

                setTimeout(function(e) {
                    hideLoader();
                    $('#create-document-history').addClass('d-none');
                }, 1000);
                $('#exist_id').attr('style', '');

                //document update ajax
                $.ajax({
                    global: false,
                    async: false,
                    url: "{{ url('/esign/update-document-patient') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        document_key: '{{ $document->id }}',
                        'file_name':globalFinalArray[0].file_name
                    },
                    success: function(res) {

                        window.location.href = "{{ url('esign/thankyou-esign')}}";
                    },
                    error: function(jqr) {
                        alert("Sorry,someting want wrong");
                    }
                });
            },
            error: function(jqXHR, exception) {
                $('#create-document-history').addClass('d-none');
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
                $('#loader_nnnn').attr('class', 'fa fa-circle-o-notch fa-spin');
            }
        });
    })

    $('#regenerateDocumentDetails').click(function(e){
        $.confirm({
            title: 'Confirm Action',
            content: 'Are you sure you want to regenerate this document?',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function(){
                        $('#regenerate-document-history').removeClass('d-none');
                        $.ajax({
                            url: '{{ url("esign/regenerate-write-document") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                document_id: '{{ $document->id }}',
                                'demo_file':globalFinalArray[0].demo_file,
                                'converted_file':globalFinalArray[0].converted_file,
                            },
                            success: function(response) {
                                $('#regenerate-document-history').addClass('d-none');
                                $('#iframe_classe_id').attr('src', response.data.file);
                                globalFinalArray[0].file_name = response.data.file_name
                                toastr.success(response.error_msg)
                               
                            },
                            error: function(jqXHR) {
                                $('#regenerate-document-history').addClass('d-none');
                                showErrorAndLoginRedirection(jqXHR);
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function(){}
                }
            }
        });
    });
    function showErrorAndLoginRedirection(xhr) {
      if (xhr.status === 401) {

          let countdown = 10;

          // Force toastr to stay visible (no auto close)
          toastr.options.timeOut = 0;
          toastr.options.extendedTimeOut = 0;
          toastr.options.closeButton = false;

          // Show message
          let $toast = toastr.error(
              `Session expired. Redirecting in <span id="countdown">${countdown}</span> seconds...`
          );

          // Countdown update
          let timer = setInterval(() => {
              countdown--;
              $("#countdown").text(countdown);

              if (countdown <= 0) {
                  clearInterval(timer);

                  // Remove toastr after 10 seconds
                  toastr.clear($toast);

                  window.location.href = "{{ url('/login')}}";
              }
          }, 1000);

      } else {
          // Restore normal toastr behavior
          toastr.options.timeOut = 5000;
          toastr.options.extendedTimeOut = 1000;
          let message = extractErrorMessage(xhr);
         
          toastr.error(message);
      }
  }
  function extractErrorMessage(xhr) {
      // If response is JSON object
      if (xhr.responseJSON) {
          return xhr.responseJSON.message ||
                xhr.responseJSON.error_msg ||
                JSON.stringify(xhr.responseJSON);
      }

      // If response is HTML or plain text
      if (xhr.responseText) {
          try {
              // Try to parse as JSON
              let json = JSON.parse(xhr.responseText);
              return json.message || json.error_msg || xhr.statusText;
          } catch (e) {
              // Fallback: return raw text
              return xhr.responseText;
          }
      }

      return xhr.statusText || "Something went wrong.";
  }
  $(document).on('hidden.bs.modal', '#modal-default-view-iframe', function () {
        $('.signer-assembler').html("");
    });

    // ==================== Eraser Functionality ====================
    var eraserActive = false;
    var eraserSelections = []; // {page, x, y, width, height}
    var eraserDrawing = false;
    var eraserStartX = 0, eraserStartY = 0;
    var eraserCanvas = document.getElementById('eraserCanvas');
    var eraserCtx = eraserCanvas.getContext('2d');

    // Toggle eraser mode
    $('#eraserToggleBtn').on('click', function() {
        if (eraserActive) {
            deactivateEraser();
        } else {
            activateEraser();
        }
    });

    function activateEraser() {
        eraserActive = true;
        eraserSelections = [];
        $('#eraserToggleBtn').addClass('eraser-active active');
        $('#eraserToolbar').fadeIn(200);
        updateEraserCount();
        positionEraserCanvas();
        $('#eraserCanvas').show();
        // Disable dragging of signer elements while in eraser mode
        $('.signer-element').draggable('disable');
        // Disable other sidebar tools while eraser is active
        $('#dragdiv .signer-tools').not('#eraserToggleBtn').addClass('eraser-disabled');
    }

    function deactivateEraser() {
        eraserActive = false;
        eraserSelections = [];
        eraserDrawing = false;
        $('#eraserToggleBtn').removeClass('eraser-active active');
        $('#eraserToolbar').fadeOut(200);
        $('#eraserCanvas').hide();
        eraserCtx.clearRect(0, 0, eraserCanvas.width, eraserCanvas.height);
        // Re-enable dragging
        try { $('.signer-element').draggable('enable'); } catch(e) {}
        // Re-enable other sidebar tools
        $('#dragdiv .signer-tools').removeClass('eraser-disabled');
    }

    function positionEraserCanvas() {
        var docCanvas = document.getElementById('document-viewer');
        if (!docCanvas) return;

        // Move eraser canvas into document-viewer's parent so it scrolls together
        var docParent = docCanvas.parentNode;
        if (eraserCanvas.parentNode !== docParent) {
            docParent.style.position = 'relative';
            docParent.appendChild(eraserCanvas);
        }

        eraserCanvas.width = docCanvas.width;
        eraserCanvas.height = docCanvas.height;
        eraserCanvas.style.position = 'absolute';
        eraserCanvas.style.left = docCanvas.offsetLeft + 'px';
        eraserCanvas.style.top = docCanvas.offsetTop + 'px';
        eraserCanvas.style.zIndex = '99999';
        eraserCanvas.style.cursor = 'crosshair';
        eraserCanvas.style.pointerEvents = 'auto';

        redrawEraserSelections();
    }

    // Reposition eraser canvas on resize
    $(window).on('resize', function() {
        if (eraserActive) positionEraserCanvas();
    });

    // Mouse events on eraser canvas
    eraserCanvas.addEventListener('mousedown', function(e) {
        if (!eraserActive) return;
        var rect = eraserCanvas.getBoundingClientRect();
        var clickX = e.clientX - rect.left;
        var clickY = e.clientY - rect.top;

        // Check if clicked on the X icon of any existing selection
        for (var i = eraserSelections.length - 1; i >= 0; i--) {
            var sel = eraserSelections[i];
            if (sel.page !== pageNum) continue;
            var xIconX = sel.x + sel.width - 14;
            var xIconY = sel.y;
            if (clickX >= xIconX - 4 && clickX <= xIconX + 16 && clickY >= xIconY && clickY <= xIconY + 18) {
                eraserSelections.splice(i, 1);
                updateEraserCount();
                redrawEraserSelections();
                return;
            }
        }

        eraserDrawing = true;
        eraserStartX = clickX;
        eraserStartY = clickY;
    });

    eraserCanvas.addEventListener('mousemove', function(e) {
        if (!eraserActive || !eraserDrawing) return;
        var rect = eraserCanvas.getBoundingClientRect();
        var currentX = e.clientX - rect.left;
        var currentY = e.clientY - rect.top;

        redrawEraserSelections();

        // Draw current selection rectangle
        eraserCtx.strokeStyle = 'rgba(255, 0, 0, 0.8)';
        eraserCtx.lineWidth = 2;
        eraserCtx.setLineDash([5, 3]);
        eraserCtx.fillStyle = 'rgba(255, 0, 0, 0.15)';
        var w = currentX - eraserStartX;
        var h = currentY - eraserStartY;
        eraserCtx.fillRect(eraserStartX, eraserStartY, w, h);
        eraserCtx.strokeRect(eraserStartX, eraserStartY, w, h);
        eraserCtx.setLineDash([]);
    });

    eraserCanvas.addEventListener('mouseup', function(e) {
        if (!eraserActive || !eraserDrawing) return;
        eraserDrawing = false;
        var rect = eraserCanvas.getBoundingClientRect();
        var endX = e.clientX - rect.left;
        var endY = e.clientY - rect.top;

        var x = Math.min(eraserStartX, endX);
        var y = Math.min(eraserStartY, endY);
        var w = Math.abs(endX - eraserStartX);
        var h = Math.abs(endY - eraserStartY);

        // Minimum selection size
        if (w < 5 || h < 5) {
            redrawEraserSelections();
            return;
        }

        eraserSelections.push({
            page: pageNum,
            x: x,
            y: y,
            width: w,
            height: h
        });

        updateEraserCount();
        redrawEraserSelections();
    });

    function redrawEraserSelections() {
        eraserCtx.clearRect(0, 0, eraserCanvas.width, eraserCanvas.height);
        eraserSelections.forEach(function(sel) {
            if (sel.page !== pageNum) return;
            // Draw white filled rectangle with red border
            eraserCtx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            eraserCtx.fillRect(sel.x, sel.y, sel.width, sel.height);
            eraserCtx.strokeStyle = 'rgba(255, 0, 0, 0.8)';
            eraserCtx.lineWidth = 2;
            eraserCtx.setLineDash([4, 3]);
            eraserCtx.strokeRect(sel.x, sel.y, sel.width, sel.height);
            eraserCtx.setLineDash([]);

            // Draw X icon
            eraserCtx.fillStyle = 'rgba(255, 0, 0, 0.6)';
            eraserCtx.font = '12px Arial';
            eraserCtx.fillText('✕', sel.x + sel.width - 14, sel.y + 14);
        });
    }

    function updateEraserCount() {
        var count = eraserSelections.length;
        $('#eraserSelectionCount').text(count + ' selection' + (count !== 1 ? 's' : ''));
    }

    // Undo last eraser selection
    $('#eraserUndoBtn').on('click', function() {
        eraserSelections.pop();
        updateEraserCount();
        redrawEraserSelections();
    });

    // Clear all eraser selections
    $('#eraserClearBtn').on('click', function() {
        eraserSelections = [];
        updateEraserCount();
        redrawEraserSelections();
    });

    // Cancel eraser mode
    $('#eraserCancelBtn').on('click', function() {
        deactivateEraser();
    });

    // Reposition canvas when page changes
    var origRenderPage = window.renderPage;
    if (typeof origRenderPage === 'undefined') {
        // Observe page changes via MutationObserver on page_num
        var pageObserver = new MutationObserver(function() {
            if (eraserActive) {
                setTimeout(function() { positionEraserCanvas(); }, 300);
            }
        });
        var pageNumEl = document.getElementById('page_num');
        if (pageNumEl) {
            pageObserver.observe(pageNumEl, { childList: true, characterData: true, subtree: true });
        }
    }

    // Also listen for prev/next button clicks to reposition
    $('#prev, #next').on('click', function() {
        if (eraserActive) {
            setTimeout(function() { positionEraserCanvas(); }, 500);
        }
    });

    // Apply eraser - AJAX request
    $('#eraserDoneBtn').on('click', function() {
        if (eraserSelections.length === 0) {
            swal('No Selections', 'Please select at least one area to erase.', 'warning');
            return;
        }

        swal({
            title: 'Apply Eraser?',
            text: 'This will permanently remove the selected content from the PDF. This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, erase it!'
        }, function(isConfirm) {
            if (isConfirm) {
                performEraserAjax();
            }
        });
    });

    function performEraserAjax() {
        var docCanvas = document.getElementById('document-viewer');
        $('#eraserLoadingOverlay').fadeIn(200);
        $('#eraserDoneBtn').prop('disabled', true);

        $.ajax({
            url: '{{ url("esign/eraser-apply-to-pdf") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                document_id: '{{ $document->id }}',
                eraser_areas: eraserSelections,
                canvas_width: docCanvas ? docCanvas.width : 0,
                canvas_height: docCanvas ? docCanvas.height : 0
            },
            success: function(response) {
                $('#eraserLoadingOverlay').fadeOut(200);
                $('#eraserDoneBtn').prop('disabled', false);

                if (response.status) {
                    deactivateEraser();

                    // Reload PDF with new URL
                    var newUrl = '{{ url("esign/template/fetch_eraser_pdf") }}?document_write_id={{ $document->id }}&t=' + new Date().getTime();
                    openDocument(newUrl, null);

                    swal('Success', 'Eraser applied successfully. PDF has been updated.', 'success');
                } else {
                    swal('Error', response.error_msg || 'Failed to apply eraser.', 'error');
                }
            },
            error: function(xhr) {
                $('#eraserLoadingOverlay').fadeOut(200);
                $('#eraserDoneBtn').prop('disabled', false);
                showErrorAndLoginRedirection(xhr);
            }
        });
    }
</script>
