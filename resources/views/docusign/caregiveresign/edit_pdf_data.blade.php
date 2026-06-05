<!DOCTYPE html>
<html lang="en" xml:lang="en">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="">
    <title>Nybest Medical Sign documents online</title>
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
    <link rel="stylesheet" href="<?= url('') ?>/assets/esign/libs/sweetalert/sweetalert.css">
    <link href="{{ asset('assets/modulejs/css/view_write_document.css')}}?{{ time()}}" rel="stylesheet">

</head>
<style>
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
        margin-right: 2%;
    }

    .nextButtons {
        float: left;
        margin-right: 2%;
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

    .custom-margin-left {
        margin-left: 0.5rem;
    }

    .custom-margin-right {
        margin-right: 0.5rem;
    }

    .form-class {
        font-weight: bold;
        font-size: 18px;
        color: #333;
    }

    .forms-card {
        background-color: #fafff733;
        border: 1px solid #55b12033;
        padding: 10px;
        margin-bottom: 10px;
        font-family: Arial, sans-serif;
    }

    .form-container {
        max-height: 400px;
        overflow-y: scroll;
        padding-right: 10px;
        margin-right: -10px;
    }

    .highlighted {
        background-color: #91cbee;
        padding: 2px;
        display: inline-block;
    }

    .forms-info {
        font-size: 15px;
        margin-right: 10px;
    }

    .dropdown-item {
        border: 1px solid #3bb00133;
        font-size: 30px;
    }

    .page-title {
        margin-left: 6px;
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }
    .hide{
        display: none !important;
    }

    .signature-wrapper {
        border: 1px solid #ccc;
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        position: relative;
        margin: 10px;
        text-align: center;
    }

    .signature-thumbnail {
        height: 100px;
        width: 100%;
        object-fit: contain;
        cursor: pointer;
    }

    .delete-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        color: #dc3545;
        font-size: 18px;
        cursor: pointer;
    }
    .signature-wrapper:hover {
        border-color: orange !important;
        background-color: rgba(236, 162, 23, 0.1) !important;
        box-shadow: 0 0 10px rgba(255, 165, 0, 0.5) !important;
    }

    .dashed-stamp-border,

    .dashed-stamp-border.ui-resizable {

        border: 1px dashed #ff0000 !important;
    }

</style>

<body>
    <div class="content" style="">
        <div class="pull-right page-actions">
            <button class="btn btn-success btn-responsive launch-editor" style="display:none;"><i
                    class="ion-edit"></i>Manage Fields & Edit</button>
        </div>

        <?php if (isset($old_pdf_data) && $old_pdf_data != '') { ?>
        <div class="row">
            <div class="col-md-8">
                <div class="light-card document">
                    <div class="signer-document">
                        <?php
							$temp = pathinfo($old_pdf_data, PATHINFO_EXTENSION);

							if ($temp == 'pdf') { ?>
                        <!-- open PDF docements -->
                        <div class="document-pagination">
                            <div class="pull-left">
                                <button id="prev" class="btn btn-default btn-round"><i
                                        class="ion-ios-arrow-left"></i></button>
                                <button id="next" class="btn btn-default btn-round"><i
                                        class="ion-ios-arrow-right"></i></button>
                                <span class="text-muted ml-15">Page <span id="page_num">0</span> of <span
                                        id="page_count">0</span></span>
                            </div>
                            <div class="pull-right">
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
                            <canvas id="document-viewer" height="836" width="591"></canvas>
                        </div>
                        <?php } else { ?>
                        <iframe
                            src='https://www.view.officeapps.live.com/op/embed.aspx?src=<?php echo URL::to('/'); ?>/public/uploads/<?php echo $old_pdf_data; ?>'
                            width='100%' height='1000px' border='0' title=""></iframe>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        <?php } ?>
    </div>
    <div class="signer-overlay">
        
        <div class="row">
        <div class="col-md-2"></div>
            <div class="col-md-8">
                    <div class="signer-overlay-previewer light-card test1"></div>
            </div>

        </div>
        <div class="row" style=" margin-left:10px; margin-top:10px;float:left;width:100%;">
            <div class="col-md-2"></div>
            <div class="col-md-8" style="margin-left: -17px;">
                <input type="button" value="Back" class="btn bg-orange prevButtons" onclick="onPrevPage()"
                        style="clear:both;  display:none;" id="previousid">
                <input type="button" value="Next" class="btn btn-success nextButtons" onclick="onNextPage()"
                    style="clear:both; display:block;" id="nextid">
               
                <button type="submit" style="display:none;" id="finish_id" class="btn btn-danger finishButtons"
                    onclick="submitResponse();"
                        style="clear:both;display:none;float: left;">
                    <i class="" id="loader_nnnn"></i>Finish
                </button>
            </div>
                    
        </div>

        <div class="signer-overlay-footer">

        </div>
        <div class="signer-assembler"></div>
        <div class="signer-builder"></div>
    </div>

    <div class="modal fade" id="modal-default" style="display: none;">
        <div class="modal-dialog">
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
                                <div class="col-md-4 text-center clickDivSection" data-value="Draw">
                                    <h4><input type="radio" name="typebod" checked
                                            value="0" /> Draw </h4>
                                </div>
                                <div class="col-md-4 text-center clickDivSection" data-value="Type">
                                    <h4><input type="radio" name="typebod"
                                            value="1" />Type</h4>
                                </div>
                                <div class="col-md-4 text-center clickDivSection" data-value="Upload">
                                    <h4><input type="radio" name="typebod" value="2" /> Upload</h4>
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
                                    <div class="col-md-10">
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
                    <button type="button" class="btn btn-default pull-left custom-margin-left"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary custom-margin-right" id="testingsSave">Save
                        changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <a class="signer-save" title=""></a>
</body>
@include('docusign.caregiveresign.stamp_user_modal')
@include('docusign.caregiveresign.stamp_modal')
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/simcify.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/clipboard/clipboard.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/select2/js/select2.min.js"></script>

<script src="<?php echo URL::to('/'); ?>/assets/esign/js/jquery.slimscroll.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jcanvas/jcanvas.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/touch-punch.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/jcanvas/editor.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/pdf.js"></script>
<script src="{{ asset('assets/modulejs/stamp_user.js') }}?time={{ time() }}"></script>


<script>
    var common_dates="{{ date('m/d/Y')}}";
    var url = "";
    var getChatUrl = "";

    function getCompls() {
        // alert("Document successfully submited");
        // return false;
    }
    var url = '{{ url("esign/aws-pdf-generate-edit")}}?template_id=<?php echo $template_id; ?>';

    getCompls();
</script>

<script type="text/javascript">
     var url = '{{ url("esign/aws-pdf-generate-edit") }}?template_id=<?php echo $template_id; ?>',

    isTemplate = 'docusing',
    postChatUrl = null,
    settingsPage = null,
    saveFieldsUrl = null,
    deleteFieldsUrl = null,
    getChatUrl = null,
    signDocumentUrl = '<?php echo URL::to('/'); ?>/template_send',
    sendRequestUrl = '<?php echo URL::to('/'); ?>/template_send1',
    createTemplateUrl = '',
    baseUrl = '<?= url('') ?>',
    auth = true;
    document_key = '<?php if (isset($document_all_details[0]->id) && $document_all_details[0]->id != '') {
    echo $document_all_details[0]->id;
    } ?>';
    permission: "permission";
    tokens = "{{ csrf_token() }}";
    PDFJS.disableWorker = true;
    PDFJS.workerSrc = '<?php echo URL::to('/'); ?>/assets/esign/js/signer.min.js?id=<?php echo time(); ?>';

 
    <?php if (isset($docWidth) && $docWidth != '') { ?>
    var savedWidth = <?php echo $docWidth; ?>;
    <?php } ?>

   
    <?php if ($Signinsert != '') { ?>
    var templateFields = <?php echo $Signinsert; ?>;
    <?php } ?>


    <?php if ($LookUpResponses != '') { ?>
    var LookUpResponses = <?php echo $LookUpResponses; ?>;
    <?php } ?>
    var sessionIds = '<?php echo $sessionIds; ?>';
    var main_intakeId = "<?php echo $main_intakeId; ?>";
    var docusignId = '<?php echo $sent_on; ?>';
    var maximumPgaes = "<?php if (isset($max) && $max != 0) {
        echo $max;
    } ?>";
    var pdfGenerateOrNot = "";
    var removeScript = 'docusign';
    var TestingArray = [];
    var name = '';
    var _CHECK_AUTH_LOGIN = "{{ $login_id}}";
    $.each(LookUpResponses, function(i, v) {
        $.each(v, function(i, c) {
            if (i == 'pm@p_full_name') {
                name = c;
            }
        })
    })

    if (name != "") {
        $('#textboxxs_id').val(name);

    }
    $.each(templateFields, function(i, vs) {
        if (vs.conditionaRules != undefined) {
            TestingArray.push(vs.conditionaRules)
        }
    });

    var finalTesting = [];
    $.each(TestingArray[0], function(i, ks) {
        finalTesting.push(ks);
    })
    var times = "<?php echo time(); ?>";
    var mainURL = "<?php echo URL::to('/'); ?>/";
    var templateID = "<?php echo $template_id; ?>";
    var document_report_id = "<?php echo $document_report_id; ?>";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var signaturePads = {};

</script>

<!-- custom scripts -->



<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.umd.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/app.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/appsignaturepad.js?<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signerNew12.js?maNameIsKhan=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/render_new.js?keyurs=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/custom.js?keyurs=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/sweetalert/sweetalert.min.js"></script>
<script>
    var globalNewResponse = [];
    var globalNewResponseImages = [];
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
    });
</script>
<script>
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
    setTimeout(function() {
        getLunchas()
    }, 2000);

    function getLunchas() {
        $('.launch-editor').click();
        showSignaturePad();
    }
    var data = [];
    var count = 1;

    function addmore(val) {
        var htmls = '';
        htmls += '<div class="form-group" id="remove' + count +
            '"><label for="inputEmail3" class="col-md-2 control-label">Option</label><div class="col-md-9"><input type="text" class="form-control" id="inputEmail' +
            count + '" onChange="getDropValue(' + count + ',' + val +
            ')"></div> <a href="javascript:void(0)" onclick="getRemove(' + count +
            ')"><i class="fa fa-times" aria-hidden="true"></i></a></div>';

        $('.copy_id').append(htmls);
        count++;
    }

    function getRemove(removeId) {
        var confirm1 = confirm("Are you sure move this row?");
        if (confirm1 == true) {
            $('#remove' + removeId).remove();
            $('#remove_' + removeId).remove();
        }

    }

    function getDropValue(Textid, MainId) {
        var text = $('#inputEmail' + Textid).val();
        var dropsResponse = '';
        if (text != '') {
            dropsResponse += '<option id="remove_' + Textid + '">' + text + '</option>';
            $('.drops_' + MainId).append(dropsResponse);
        }
    }

    function selectValue(id, val) {
        $('#dropid' + id).html('<option value="' + val + '">' + val + '</option>');
    }



    function gerRequired(val, id, name) {
        if (name == 'text') {
            if (val == 'No') {
                $('#checks' + id).attr('style', 'background-color:#fff');
            }
            if (val == 'Yes') {
                $('#checks' + id).attr('style', 'background-color:yellow');
            }

        }
        if (name == 'dropdown') {
            if (val == 'No') {
                $('#dropid' + id).attr('style', 'background-color:#fff');
            }
            if (val == 'Yes') {
                $('#dropid' + id).attr('style', 'background-color:yellow');
            }

        }
    }

    function gerReadOnly(val, id, name) {
        if (name == 'text') {
            if (val == 'No') {
                $("#fieldName")

                $('#checks' + id).prop("readonly", false);
            }
            if (val == 'Yes') {
                $('#checks' + id).prop("readonly", true);
            }

        }
        if (name == 'dropdown') {
            if (val == 'No') {
                $('#dropid' + id).prop("disabled", false);
            }
            if (val == 'Yes') {
                $('#dropid' + id).prop("disabled", true);
            }

        }
    }
</script>

<script>
    function getMeta(url, cb) {
        const img = new Image();
        img.onload = () => cb(null, img);
        img.onerror = (err) => cb(err);
        img.src = url;
    }
    function getLuncha() {
        inviting = false;
        //enableTools();
        launchEditor();
    }

    function getSignatureSuccess(filename, id, type = "",docusignType="") {
        var res = filename.replace(/\n/g, '');
        
        if (type === "upload" || type === "select" || type === "upload_stamp") {
            $("#img" + id).attr('src', res);

        } else {
            $("#img" + id).attr('src', '<?php echo URL::to('/'); ?>/dosusinguploads/docusign/' + res);
        }
        
        if (type !== "upload_stamp") {
            var width = $('#img'+id).css('width');
            var height = $('#img'+id).css('height');
            $("#img" + id).attr('style', 'width:'+width+';height:'+height);
        } else {
            $("#img" + id).attr('style', '');
        }
        
        $("#img" + id).attr('dataids', 1);
        $('.signeeddate').val(common_dates);
        var uploadSelect = 0;

        if (docusignType == '1'){
            uploadSelect =1;
        }
        $("#img" + id).attr('data-flag', uploadSelect);
    }

    function mySign(id) {


        getWebviewCanvas("<?php echo $id; ?>", "<?php echo $rand; ?>", id);
        getSearching();

    }

    var globalStamp =0;
    function myStamp(id) {
   
        if(globalStamp ==0){
            globalStamp = 1;
            getWebviewCanvasStamp("<?php echo $id; ?>", "<?php echo $rand; ?>", id);
        }
        
    }

    function myStampUser(id) {
        getStampUser("<?php echo $id; ?>", "<?php echo $rand; ?>", id);
    }

    //testing();

    function getWebviewCanvas(documentMentId, rand, imgid) {

        $('#modal-default').modal('show');
        $('#file_upload').val('');
        $('#modal-default #signature-pad .signature-pad--body canvas').attr('width', 550);
        $('#modal-default #signature-pad .signature-pad--body canvas').attr('height', 200);
        $('#imagesId').val(imgid);
        
        // Initialize signature pad for this specific imgid
        var canvas = document.querySelector('#rename_canvas');
        signaturePads[imgid] = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        
        getExistingSignatures();
    }

    function getWebviewCanvasStamp(documentMentId, rand, imgid) {
        $('#modal-default-stamp').modal('show');
        $('#stamp_upload').val('');
        $('#stampId').val(imgid);
        getExistingSignaturestamp();
    }

    function getStampUser(documentMentId, rand, imgid) {
        stampFormModal(documentMentId, rand, imgid);
    }

    function getSignature() {

        var ua = navigator.userAgent.toLowerCase();
        var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
        if (isAndroid) {
            AndroidFunction.getSignature("id", "rand", "");

        }

        var standalone = window.navigator.standalone,
            userAgent = window.navigator.userAgent.toLowerCase(),
            safari = /safari/.test(userAgent),
            ios = /iphone|ipod|ipad/.test(userAgent);

        if (ios) {
            window.webkit.messageHandlers["getSignature"].postMessage("");
        }
    }
    var globalResponse = <?php echo $Signinsert; ?>;
    if (globalResponse.length == '') {
        alert("Document successfully submited");
        $('#finish_id').prop('disabled', true);
        $('#submit_id').prop('disabled', true);
    }

    function submitResponse() {
        var final_array = [];
        var data = [];
        var i = 1;
        cnt = 0;
        var width;
        var height;
        $('#loader_nnnn').attr('class', 'fa fa-circle-o-notch fa-spin');

        $.each(globalResponse, function(index, val) {
            var text_val = $('#' + val.id).val();

            var value = "";
            var sThisVal = "";
            var updatedSelectType1 = 0;
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
                // if (val.signer_id == "Patient" || val.signer_id == "Sign") {
                //     imgRequired = $("#img" + i).attr('dataids', 1);
                // } else {
                    imgRequired = $("#img" + i).attr('dataids');
                // }
                width = val.width;
                height = val.height;
                value = $("#img" + i).attr('src');
                var selectDocumentType = $("#img" + i).attr('data-flag');
               
                if(typeof selectDocumentType !='undefined'){
                    updatedSelectType1 = selectDocumentType;
                }
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
                    if(val.placeHolder =='Date Signed'){
                        value = $('textarea[id="'+val.id+'"]').val();
                    }else{
                        value = text_val;
                    }
                }

            }
            if (val.type == "stamp") {
                imgRequired = $("#img" + i).attr('dataids');
                
                value = $("#img" + i).attr('src');
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
                    console.log(selectedid + "====" + val.bold);
                    checked = '';
                    if (selectedid == val.bold) {
                        checked = 1;
                    }

                }
                if(val.type == "stamp"){
                    globalNewResponseImages.push(val.type);
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
                    readonly: val.readOnly,
                    updatedSelectType:updatedSelectType1
                });


            
            i++;
        });
        if (cnt != 0) {

            return false;
        } else {

            if(globalNewResponseImages.length > 0){
                globalNewResponse = final_array
                $('.signer-save').click();
                return false;
            }else{
                var actions = JSON.stringify(final_array);
                showLoader();
                var href = "{{ url('esign/docusign/update-form')}}";

                $.ajax({
                    global: false,
                    async: false,
                    url: href,
                    type: "POST",
                    data: {
                        'id': '<?php echo $template_id; ?>',
                        'action': encodeURIComponent(actions),
                        "sessionId": "<?php echo $sessionIds; ?>",
                        'document_report_id': "<?php echo $document_report_id; ?>",
                        "groupId": "<?php echo $groupId; ?>",
                        "permission": headers,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(response) {

                        setTimeout(function(e) {
                            hideLoader();
                        }, 5000);
                        $('#finish_id').prop('disabled', true);
                        $('#submit_id').prop('disabled', true);
                        $('#exist_id').attr('style', '');

                        window.location.href = "{{ url('esign/thankyou-esign')}}";
                    },
                    error: function(jqXHR, exception) {
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
                        $('#finish_id').prop('disabled', false);
                        $('#submit_id').prop('disabled', false);
                    }
                });
            }
            
            
        }

    }

    function HideShow(txtId, value, divId) {
        var text = $('#' + txtId).val();

        $.each(TextArray[0], function(index, kl) {
            if (kl.type == 'text') {
                if (kl.opponent == 'text') {
                    if (txtId == kl.SenderId) {

                        if (text == kl.value) {
                            $('#' + kl.ReceiverId).removeClass('Depending');
                        } else {
                            $('#' + kl.ReceiverId).addClass('Depending');
                            $('#' + kl.ReceiverDivId).val('');

                        }
                    }
                }
                if (kl.opponent == 'checkbox') {

                    if (text == kl.value) {

                        $('#' + kl.ReceiverDivId).removeClass('Depending');
                    } else {
                        $('#' + kl.ReceiverDivId).addClass('Depending');
                        $("#" + kl.ReceiverId).prop("checked", false);
                        HideShowCheck("", kl.ReceiverId, "");
                    }

                }
                if (kl.opponent == 'radio') {
                    var explode = $('#' + kl.ReceiverDivId).attr('groupname');
                    if (text == kl.value) {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).removeClass('Depending');
                        })
                    } else {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).addClass('Depending');
                            $("#" + $(this).attr('id')).prop("checked", false);
                            HideShowRadio($(this).attr('id'), "")

                        })
                    }
                }
                if (kl.opponent == 'dropdown') {
                    if (text == kl.value) {
                        $('#' + kl.ReceiverId).removeClass('Depending');
                    } else {
                        $('#' + kl.ReceiverId).addClass('Depending');
                        var childId = $('#' + kl.ReceiverId).children().attr("id");

                        $("#" + kl.ReceiverId).val('');
                        HideShowDrop(kl.ReceiverDivId, "", "")

                    }
                }
            }
        });
    }

    function HideShowCheck(divId, txtId, textVal) {
        var check = $('#' + txtId).prop('checked');
        $.each(headers, function(i, kl) {
            if (kl.type == 'checkbox') {
                if (kl.opponent == 'text') {
                    if (txtId == kl.SenderId) {
                        if (check == true) {
                            $('#' + kl.ReceiverId).removeClass('Depending');
                        } else {
                            $('#' + kl.ReceiverId).addClass('Depending');
                            $('#' + kl.ReceiverDivId).val(" ");
                            HideShow(kl.ReceiverId, "");
                        }
                    }
                }
                if (kl.opponent == 'checkbox') {
                    if (txtId == kl.SenderId) {
                        if (check == true) {
                            $('#' + kl.ReceiverDivId).removeClass('Depending');
                        } else {
                            $('#' + kl.ReceiverDivId).addClass('Depending');
                            $('#' + kl.ReceiverDivId).prop('checked', false);
                            HideShowCheck("", kl.ReceiverId, "");
                            //AddDependencyFields(kl.SenderId)
                        }
                    }
                }
                if (kl.opponent == 'dropdown') {
                    if (txtId == kl.SenderId) {
                        if (check == true) {
                            $('#' + kl.ReceiverId).removeClass('Depending');

                        } else {
                            $('#' + kl.ReceiverId).addClass('Depending');
                            var childId = $('#' + kl.ReceiverId).children().attr("id");

                            $("#" + kl.ReceiverId).val('');
                            HideShowDrop(kl.ReceiverDivId, "", "")
                            //AddDependencyFields(kl.SenderId)
                        }
                    }
                }
                if (kl.opponent == 'radio' && divId == kl.ReceiverId) {

                    var explode = $('#' + kl.ReceiverDivId).attr('groupname');

                    if (check == true) {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).removeClass('Depending');
                        })


                    } else {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).addClass('Depending');

                            $("#" + $(this).attr('id')).prop("checked", false);
                            HideShowRadio($(this).attr('id'), "")
                        })
                        //AddDependencyFields(kl.SenderId)
                    }
                }
            }
        });
    }

    function HideShowDrop(drpId, text) {

        $.each(ConditionalSTempArray[0], function(i, kl) {
            if (kl.type == 'dropdown') {
                if (kl.opponent == 'text') {
                    if (drpId == kl.SenderId) {
                        if (text == kl.value) {
                            $('#' + kl.ReceiverId).removeClass('Depending');
                        } else {
                            $('#' + kl.ReceiverId).addClass('Depending');
                            HideShow(kl.ReceiverId, "");
                        }
                    }
                }
                if (kl.opponent == 'checkbox') {
                    if (text == kl.value) {
                        $('#' + kl.ReceiverDivId).removeClass('Depending');
                    } else {
                        $('#' + kl.ReceiverDivId).addClass('Depending');

                        $("#" + kl.ReceiverId).prop("checked", "")
                        HideShowCheck("", kl.ReceiverId, "")

                    }
                }
                if (kl.opponent == 'radio') {

                    var explode = $('#' + kl.ReceiverDivId).attr('groupname');

                    if (text == kl.value) {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).removeClass('Depending');
                        })

                    } else {
                        $.each($("input[name='" + explode + "']"), function(i, ls) {
                            var TotalRadioId = $(this).parent().attr('id');
                            $("#" + TotalRadioId).addClass('Depending');
                            $("#" + $(this).attr('id')).prop("checked", false);
                            HideShowRadio($(this).attr('id'), "")

                        })

                    }
                }
                if (kl.opponent == 'dropdown') {

                    if (text == kl.value) {
                        $('#' + kl.ReceiverId).removeClass('Depending');
                    } else {
                        $('#' + kl.ReceiverId).addClass('Depending');
                        var childId = $('#' + kl.ReceiverId).children().attr("id");

                        $("#" + kl.ReceiverId).val('');
                        HideShowDrop(kl.ReceiverDivId, "", "")

                    }
                }
            }
        })
    }


    function HideShowRadio(drpId, text) {
        var radioGroupArray = [];
        $.each($("input[name='" + $("#" + drpId).attr("name") + "']"), function(objIndex, elemen) {
            radioGroupArray.push(elemen.id);
        })
        $.each(RadiosArray[0], function(i, kl) {
            if (radioGroupArray.indexOf(kl.value) > -1) {
                var checkClickOrNot = $('#' + drpId).is(":checked");
                if (kl.type == 'radio') {
                    if (kl.opponent == 'dropdown' || kl.opponent == 'radio') {
                        if (kl.opponent == 'radio') {
                            var explode = $('#' + kl.ReceiverDivId).attr('groupname');
                            var ClickRadioGroup = $('#' + kl.SenderId).attr('groupname');

                            if (ClickRadioGroup == explode) {
                                if (drpId == kl.value && checkClickOrNot) {
                                    $('#' + kl.ReceiverDivId).removeClass('Depending');
                                } else {
                                    $('#' + kl.ReceiverDivId).addClass('Depending');
                                    $("#" + kl.ReceiverDivId).prop("checked", false);
                                    HideShowRadio(kl.ReceiverDivId, "")

                                }
                            } else {
                                if (drpId == kl.value && checkClickOrNot) {
                                    $.each($("input[name='" + explode + "']"), function(obj, ele) {
                                        var totalRadioId = $(this).parent().attr('id');
                                        $('#' + totalRadioId).removeClass('Depending');

                                    })
                                } else {
                                    $.each($("input[name='" + explode + "']"), function(obj, ele) {
                                        var totalRadioId = $(this).parent().attr('id');
                                        $('#' + totalRadioId).addClass('Depending');
                                        $("#" + $(this).attr('id')).prop("checked", false);
                                        HideShowRadio($(this).attr('id'), "")
                                    })

                                }
                            }
                        } else {
                            //for dropdown
                            if (drpId == kl.value && checkClickOrNot) {
                                $('#' + kl.ReceiverId).removeClass('Depending');


                            } else {
                                $('#' + kl.ReceiverId).addClass('Depending');
                                var childId = $('#' + kl.ReceiverId).children().attr("id");

                                $("#" + kl.ReceiverId).val('');
                                HideShowDrop(kl.ReceiverDivId, "", "")

                            }
                        }
                    } else {

                        if (drpId == kl.value && checkClickOrNot) {
                            if (kl.opponent == 'checkbox') {
                                $('#' + kl.ReceiverDivId).removeClass('Depending');
                            } else {
                                $('#' + kl.ReceiverId).removeClass('Depending');
                            }

                        } else {
                            if (kl.opponent == 'checkbox') {
                                $('#' + kl.ReceiverDivId).addClass('Depending');
                                $('#' + kl.ReceiverId).prop("checked", false);
                            } else {
                                $('#' + kl.ReceiverId).addClass('Depending');
                                $('#' + kl.ReceiverDivId).val('');
                            }




                        }
                    }
                }
            }
        });
    }

    function AddDependencyFields(id) {
        $.each(headers, function(index, depid) {
            if (id == depid.SenderId) {

                if (depid.opponent == 'radio') {
                    $('#' + depid.ReceiverId).prop('checked', false);
                    var explode = $('#' + depid.ReceiverDivId).attr('groupname');
                    $.each($("input[name='" + explode + "']"), function(obj, ele) {
                        var totalRadioId = $(this).parent().attr('id');

                        $('#' + totalRadioId).addClass('Depending');
                        $('#' + $(this).attr('id')).prop('checked', false);
                    })
                    AddDependencyFields(depid.ReceiverDivId);
                }
                if (depid.opponent == 'text') {

                    $('#' + depid.ReceiverId).addClass('Depending');
                    var getPlaceHolder = $('#' + depid.ReceiverDivId).attr('placeholder');
                    var placeHolder = getPlaceHolder.trim();
                    $('#' + depid.ReceiverDivId).attr('placeholder', placeHolder);
                    $('#' + depid.ReceiverDivId).val("");
                    AddDependencyFields(depid.ReceiverDivId);
                }
                if (depid.opponent == 'dropdown') {
                    $('#' + depid.ReceiverId).addClass('Depending');
                    AddDependencyFields(depid.ReceiverDivId);
                }
                if (depid.opponent == 'checkbox') {
                    $('#' + depid.ReceiverId).prop("checked", false);
                    $('#' + depid.ReceiverId).addClass('Depending');
                    AddDependencyFields(depid.ReceiverId);
                }
            }
        });
    }

    function getSubmit(blob) {
        var formData = new FormData();
        var imgid = $('#imagesId').val();
        
        // Get the specific signature pad instance for this imgid
        var signaturePadInstance = signaturePads[imgid];
        
        // Check if signature pad is empty for this specific instance
        if (!signaturePadInstance || signaturePadInstance.isEmpty()) {
            swal({
                title: "Error",
                text: "Please draw your signature before saving",
                icon: "error",
                button: "Ok",
            });
            return false;
        }

        formData.append("image", blob);
        formData.append("_token", '{{ csrf_token() }}');

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
                    $("#img" + imgid).attr('src', data);
                    $("#img" + imgid).attr('dataids', 1);
                
                    var width = $("#img" + imgid).css('width');
                    var height = $("#img" + imgid).css('height');
                    $("#img" + imgid).attr('style', 'width:'+width+';height:'+height);
                    
                    
                    $('.signeeddate').val(common_dates);
                    $('#modal-default').modal('hide');
                   

                } else {
                    swal({
                        title: "Error",
                        text: "Failed to save signature. Please try again.",
                        icon: "error",
                        button: "Ok",
                    });
                }
            },
            error: function(xhr, status, error) {
                swal({
                    title: "Error",
                    text: "An error occurred while saving the signature. Please try again.",
                    icon: "error",
                    button: "Ok",
                });
            }
        });
    }


    var jsonresp = <?php
   
    $test = ['Adinda Melia.otf', 'Agashi Signature Demo.otf', 'AlfridaDemoSignature.ttf', 'Bellisya.otf', 'AmarulaPersonalUse.ttf'];
    
    echo json_encode($test); ?>;

    function getCreateNewImages(id) {
        var textname = $('#textboxxs_id').val();

        if (textname != '') {
            $('#createNewImage').html("");
            $.each(jsonresp, function(i, v) {

                $.ajax({
					url: "{{ url('esign/upload_documentwebNew') }}", // Upload Script
                    type: "POST",

                    data: {
                        'textbox': textname,
                        'fontsize': v
                    },
                    success: function(response) {
                        const sid = crypto.randomUUID();
                        var ssid ="'"+sid+"'";
                        var htm =
                            '<div class="form-group" onclick="selectedTextValue('+ssid+')"><div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" data-vas="'+sid+'" data-id="' +
                            id + '" value="' + response +
                            '"><i class="input-helper"></i></label><img src="' + response +
                            '" style="width: 200px;"></div>';

                       
                        $('#createNewImage').append(htm);
                    }
                })

            });

        }
    }

    function getAdminSign() {
        var images = $('input[name="optionsRadios"]:checked').val();
        var imgid = $('input[name="optionsRadios"]:checked').attr('data-id');

        data = images;
        $('img').each(function(i, v) {

            var id = $(this).attr('id')
            if(typeof id !="undefined"){
                if(id.replace('img','') ==$('#imagesId').val()){
                   
                    $("#" + id).attr('src', data);
                        
                        var width = $("#" + id).width();
                        var height = $("#" + id).height();
                        $("#" + id).attr('style', 'width:'+width+'px;height:'+height+'px;');
                        $("#" + id).attr('dataids', 1);
                        $('.signeeddate').val(common_dates);
                }
            }
        });

        $('#modal-default').modal('hide');


        $('#modal-default').modal('hide');
    }


    function alert(val) {
        swal({
            title: "Alert",
            text: val,
            icon: "error",
            button: "Ok",
        });

    }

    function getSearching() {
        var names = $('#textboxxs_id').val();
        var imageId = $('#imagesId').val();
        getCreateNewImages(imageId);
    }

    $('#stampUserForm').click(function(i, v) {
        var data = $('select[name="stamp_user"]').val();
        
        $('img').each(function(i, v) {
            var id = $(this).attr('id')
            $("#" + id).attr('src', data);
            $("#" + id).attr('style', 'width:100px;');
            $("#" + id).attr('dataids', 1);
            $('.signeeddate').val(common_dates);
        });
        $("#stampUserFormModal").modal("hide");

    })


    function getSubmitFileUpload(file) {
        var formData = new FormData();
        formData.append("file_upload", file);
        formData.append("login_id", '{{ $login_id}}');
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
        let loginId = '{{ $login_id}}';

        $.ajax({
            url: '{{ url("esign/get-patient-signatures")}}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                login_id: loginId
            },
            success: function (response) {
                
                var htmlResponse ="<div class='row'>";
                if(response.data.length !=0){
                    $.each(response.data,function(i,v){
                        var signType = 0;
                        if(v.type !='esign'){
                            signType = 1;
                        }
                        htmlResponse +=`<div class="col-md-6">
                                            <div class="signature-wrapper"
                                                style="position: relative; margin: 10px; border: 1px solid #ccc; background: #fff; border-radius: 8px; padding: 10px; text-align: center;"
                                                data-id="${v.id}">
                                                
                                                <img src="${v.file_upload}" alt="Signature"
                                                    class="signature-thumbnail"
                                                    style="height: 100px; width: 100%; object-fit: contain; cursor: pointer;"
                                                    onclick="selectExistingSignature('${v.file_upload}','${signType}')" />
                                                
                                                <span class="delete-icon"
                                                    style="position: absolute; top: 5px; right: 5px; cursor: pointer; color: #dc3545; font-size: 18px;"
                                                    onclick="deleteSignature('${v.id}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>`;
                    })
                }
                htmlResponse +='</div>';
                $('#show_existing_signatures').html(htmlResponse);
            },
            error: function (xhr) {
                console.error("Error loading signatures:", xhr.responseText);
            }
        });
    }

    function getSubmitFileUploadStamp(file) {
        var formData = new FormData();
        formData.append("file_upload", file);
        formData.append("login_id", '{{ $login_id}}');
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

                    
                    $('#modal-default-stamp').modal('hide');
                    getMeta(response.url, (err, img) => {
                        if (!err) {
                            // Set natural dimensions directly
                            $(`#img${imgid}`).attr('width', img.naturalWidth);
                            $(`#img${imgid}`).attr('height', img.naturalHeight);
                            
                            // Call signature success function
                            getSignatureSuccess(response.url, imgid, 'upload_stamp')
                            
                            // Make the stamp resizable
                            makeStampResizable(imgid);
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("File upload error:", error);
            }
        });
    }
   
    function getExistingSignaturestamp() {
        // let loginId = $('#loginId').val();
        let loginId = '{{ $login_id}}';
        let type = $('#type').val();

        $.ajax({
            url: '{{  url("esign/get-patient-signatures") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                login_id: loginId,
                type: type
            },
            success: function (response) {
                var htmlResponse ="<div class='row'>";
                if(response.data.length !=0){
                    $.each(response.data,function(i,v){
                        htmlResponse +=`<div class="col-md-6">
                                            <div class="signature-wrapper"
                                                style="position: relative; margin: 10px; border: 1px solid #ccc; background: #fff; border-radius: 8px; padding: 10px; text-align: center;"
                                                data-id="${v.id}">
                                                
                                                <img src="${v.file_upload}" alt="Signature"
                                                    class="signature-thumbnail"
                                                    style="height: 100px; width: 100%; object-fit: contain; cursor: pointer;"
                                                    onclick="selectExistingStamp('${v.file_upload}')" />
                                                
                                                <span class="delete-icon"
                                                    style="position: absolute; top: 5px; right: 5px; cursor: pointer; color: #dc3545; font-size: 18px;"
                                                    onclick="deleteSignature('${v.id}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>`;
                    })
                }
                htmlResponse +='</div>';
                $('#show_existing_stamp').html(htmlResponse);
            },
            error: function (xhr) {
                console.error("Error loading stamp:", xhr.responseText);
            }
        });
    }
    function selectExistingSignature(imageUrl,type="") {
        var imgid = $('#imagesId').val();

        // Highlight selected signature
        $('.signature-thumbnail').removeClass('selected');
        $(`img[src="${imageUrl}"]`).addClass('selected');

        // Call signature success function
        getSignatureSuccess(imageUrl, imgid, 'select',type);
        $('#modal-default').modal('hide');
        $('#file_upload').val('');
    }

    function selectExistingStamp(imageUrl) {
        var imgid = $('#stampId').val();

                // Highlight selected signature
                $('.signature-thumbnail').removeClass('selected');
                $(`img[src="${imageUrl}"]`).addClass('selected');

                // Get image dimensions before applying
                getMeta(imageUrl, (err, img) => {
                    if (!err) {
                        // Set natural dimensions directly
 
                        $(`#img${imgid}`).attr('width', img.naturalWidth);
                        $(`#img${imgid}`).attr('height', img.naturalHeight);
                        
                        // Call signature success function
                        getSignatureSuccess(imageUrl, imgid, 'upload_stamp');
                        
                        // Make the stamp resizable
                       makeStampResizable(imgid);
                    }
                });

                $('#modal-default-stamp').modal('hide');
                $('#stamp_upload').val('');

globalStamp = 0;
    }

    function deleteSignature(signatureId) {
        if (confirm("Are you sure you want to delete this signature?")) {
            $.ajax({
                url: '/esign/delete-signature',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    signature_id: signatureId
                },
                success: function(response) {
                    if (response.status) {
                        $('[data-id="' + signatureId + '"]').remove();
                    } else {
                        alert("Failed to delete signature.");
                    }
                }
            });
        }
    }
    document.querySelectorAll('.page-header').forEach(header => {
        header.addEventListener('click', function() {
            const page = this.getAttribute('data-page');

            document.querySelectorAll('.fields-container').forEach(container => {
                if (container.getAttribute('data-page') !== page) {
                    container.style.display = 'none';
                }
            });

            const fieldsContainer = document.querySelector(`.fields-container[data-page="${page}"]`);
            if (fieldsContainer.style.display === 'none' || fieldsContainer.style.display === '') {
                fieldsContainer.style.display = 'block';
            } else {
                fieldsContainer.style.display = 'none';
            }
        });
    });

    $(".forms-card").click(function() {
        var cardId = $(this).data('id');
        var name = $(this).data('name');
        var type = $(this).data('type');

        highlightFieldById(cardId, name, type);
    });

    function highlightFieldById(id, name, type) {
        if (type == 'text') {
            $(".highlighted").removeClass("highlighted");
            $("#int" + id).addClass("highlighted");
            $("#" + id).addClass("highlighted");
        } else if (type == 'checkbox') {
            $(".highlighted").removeClass("highlighted");
            $("input[name='" + name + "']").closest("div").addClass("highlighted");
        } else if (type == 'radio') {
            $(".highlighted").removeClass("highlighted");
            $("input[name='" + name + "']").closest("div").addClass("highlighted");
        } else {
            $(".highlighted").removeClass("highlighted");
            $("#int" + id).addClass("highlighted");
            $("#" + id).addClass("highlighted");
        }
    }
    // Clear signature pad when modal is closed
    $('#modal-default').on('hidden.bs.modal', function () {
        var imgid = $('#imagesId').val();
        if (signaturePads[imgid]) {
            signaturePads[imgid].clear();
        }
    });
    
    function makeStampResizable(imgId) {
      
        $(`#img${imgId}`)
            .addClass('dashed-stamp-border')
            .resizable({
                aspectRatio: true,
                handles: 'all',
                resize: function(event, ui) {
                    // Update dimensions during resize
                    $(this).attr('width', ui.size.width);
                    $(this).attr('height', ui.size.height);
                },
                stop: function(event, ui) {
                    // Store final dimensions after resize
                    $(this).attr('width', ui.size.width);
                    $(this).attr('height', ui.size.height);
                }
            });
    }

    function saveAllData(data){
     
        var actions =data;

        showLoader();
        var href = "{{ url('esign/docusign/update-form')}}";

        $.ajax({
            global: false,
            async: false,
            url: href,
            type: "POST",
            data: {
                'id': '<?php echo $template_id; ?>',
                'action': encodeURIComponent(actions),
                "sessionId": "<?php echo $sessionIds; ?>",
                'document_report_id': "<?php echo $document_report_id; ?>",
                "groupId": "<?php echo $groupId; ?>",
                "permission": headers,
                '_token': '{{ csrf_token() }}',
                'sent_on': '{{ $sent_on }}',
                'docWidth':$('#document-viewer').attr('width')
            },
            success: function(response) {

                setTimeout(function(e) {
                    hideLoader();
                }, 5000);
                $('#finish_id').prop('disabled', true);
                $('#submit_id').prop('disabled', true);
                $('#exist_id').attr('style', '');
                window.location.href = "{{ url('esign/thankyou-esign')}}";
            },
            error: function(jqXHR, exception) {
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
                $('#finish_id').prop('disabled', false);
                $('#submit_id').prop('disabled', false);
            }
        });
    }

    $('#modal-default-stamp').on('hidden.bs.modal', function () {
        globalStamp =0;
    });
</script>

<script>
    document.getElementById('stampUserDropdown').addEventListener('change', function () {
        const selectedValue = this.value;
        const stampPreview = document.getElementById('stampImagePreview');

        if (selectedValue) {
            stampPreview.src = selectedValue;
            stampPreview.style.display = 'block';
        } else {
            stampPreview.style.display = 'none';
        }
    });

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

    function selectedTextValue(sid){
        $('input[data-vas="'+sid+'"]').prop("checked",true);
    }
</script>