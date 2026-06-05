<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Create Digital signatures and Sign PDF documents online.">
	<meta name="author" content="Simcy Creative">
	<link rel="icon" type="image/png" sizes="16x16" href="">
	<title>Nybest Medical Sign documents online</title>
	<!-- Ion icons -->
	<link href="<?php echo URL::to('/'); ?>/assets/esign/bower_components/bootstrap/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/plugins/fontawesome-free/css/all.min.css">
	<link href="<?php echo URL::to('/'); ?>/assets/esign/Ionicons/ionicons.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=B612+Mono:400,400i,700|Charm:400,700|EB+Garamond:400,400i,700|Noto+Sans+TC:400,700|Open+Sans:400,400i,700|Pacifico|Reem+Kufi|Scheherazade:400,700|Tajawal:400,700&amp;subset=arabic" rel="stylesheet">
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
	<link rel="stylesheet" href="<?= url(""); ?>/assets/esign/libs/sweetalert/sweetalert.css">


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
	.custom-margin-left {
		margin-left: 0.5rem; 
	}

	.custom-margin-right {
		margin-right: 0.5rem; 
	}
</style>

<body>
	<div class="content" style="">
		<div class="pull-right page-actions">
			<button class="btn btn-success btn-responsive launch-editor" style="display:none;"><i class="ion-edit"></i>Manage Fields & Edit</button>


		</div>

		<?php if (isset($document_all_details->upload_document) && $document_all_details->upload_document != '') { ?>
			<div class="row">
				<div class="col-md-8">
					<div class="light-card document">
						<div class="signer-document">
							<?php
							$temp = pathinfo($document_all_details->upload_document, PATHINFO_EXTENSION);

							if ($temp == 'pdf') { ?>
								<!-- open PDF docements -->
								<div class="document-pagination">
									<div class="pull-left">
										<button id="prev" class="btn btn-default btn-round"><i class="ion-ios-arrow-left"></i></button>
										<button id="next" class="btn btn-default btn-round"><i class="ion-ios-arrow-right"></i></button>
										<span class="text-muted ml-15">Page <span id="page_num">0</span> of <span id="page_count">0</span></span>
									</div>
									<div class="pull-right">
										<button class="btn btn-default btn-round btn-zoom" zoom="plus"><i class="ion-plus"></i></button>
										<button class="btn btn-default btn-round btn-zoom" zoom="minus"><i class="ion-minus"></i></button>

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
								<iframe src='https://www.view.officeapps.live.com/op/embed.aspx?src=<?php echo URL::to('/'); ?>/public/uploads/<?php echo $document_all_details->upload_document; ?>' width='100%' height='1000px' frameborder='0' title=""></iframe>
							<?php } ?>
						</div>
					</div>
				</div>

			</div>
		<?php } ?>
	</div>
	<div class="signer-overlay">
		<?php if ($mobile_type != 'web') { ?>
			<!-- <div class="alert alert-primary" role="alert">
								<a href="<?php echo URL::to('/'); ?>/esign/c/<?php echo $document_all_details->main_intakeId; ?>?mobiletype="> <i aria-hidden="true" class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>Esign
							</div> -->
		<?php } ?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">


				<div class="signer-overlay-previewer light-card test1"></div>

				<div class="row" style=" margin-left:10px; margin-top:10px;float:left;width:100%;">
					<input type="button" value="Back" class="btn bg-orange prevButtons" onclick="onPrevPage()" style="clear:both;  display:none;" id="previousid">
					<input type="button" value="Next" class="btn btn-success nextButtons" onclick="onNextPage()" style="clear:both; display:block;" id="nextid">
					<button type="submit" style="display:none;" id="finish_id" class="btn btn-danger finishButtons" <?php
																													if ($document_all_details->status == 'Completed') { ?> disabled<?php } else { ?>onclick="submitResponse();" <?php } ?> style="clear:both;display:none;float: left;">
						<i class="" id="loader_nnnn"></i>Finish
					</button>
				</div>
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
							<div class="row">
								<div class="col-md-4">
									<h4><input type="radio" name="typebod" checked onclick="showSignaturePad()" value="0" /> Draw </h4>
								</div>
								<div class="col-md-4">
									<h4><input type="radio" name="typebod" onclick="showTypePad()" value="1" />Type</h4>
								</div>
								<div class="col-md-4">
									<h4><input type="radio" name="typebod" onclick="showFileUpload()" value="2" /> Upload</h4>
								</div>
							</div>
							<input type="hidden" id="imagesId">
							<div id="signaturePageBody">
								<canvas width="550" height="500" id="rename_canvas" style="touch-action: none;"></canvas>
							</div>

							<div id="TypeBody">
								<div class="row">
									<div class="col-md-10">
										<input type="text" class="form-control" value="" id="textboxxs_id">
									</div>
									<div class="col-md-1">
										<button name="button" onclick="getSearching()" class="btn btn-primary">Go</button>
									</div>
								</div>
								<div class="" id="createNewImage">

								</div>
							</div>
							<div id="fileUploadBody">
								<div class="row">
									<div class="col-md-10">
										<input type="file" class="form-control" value="" name="file_upload" id="file_upload">
										<div id="error_message" style="color: red; margin-top: 5px;"></div>
									</div>
									<div class="col-md-1">
										
									</div>
								</div>
							</div>
						</div>


						<div class="signature-pad--footer">
							<div class="description">Sign above</div>

							<div class="signature-pad--actions">
								<div>
									<button type="button" class="button clear" id="clear" data-action="clear">Clear</button>


								</div>

							</div>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left custom-margin-left" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary custom-margin-right" id="testingsSave">Save changes</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
</body>
@include('docusign.caregiveresign.stamp_user_modal')

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
<script src="{{ asset('assets/modulejs/stamp_user.js')}}?time={{ time()}}"></script>

<?php

if (strtolower($document_all_details->status) == 'completed') {


?>
	<script>
		var url ="";
		var getChatUrl="";
		function getCompls() {
			// alert("Document successfully submited");
			// return false;
		}
		getCompls();
	</script>

<?php 	}
?>

<script type="text/javascript">
	var common_dates_mdy ='{{ date("m/d/Y") }}'
	<?php if (isset($document_all_details->sourceFile) && $document_all_details->sourceFile != '') { ?>

	<?php } else { ?>
		alert("Please wait for <?php if (isset($errorSigner) && $errorSigner != '') {
									echo $errorSigner;
								} ?> to complete signatures.");

	<?php } ?>
	<?php if (strtolower($document_all_details->status) == 'completed') { ?>

	<?php } else { ?>
		var url = '{{ url("esign/aws-pdf-generate")}}?template_id={{ $document_all_details->id}}',

			isTemplate = 'docusing',
			postChatUrl = null,
			settingsPage = null,
			saveFieldsUrl = null,
			deleteFieldsUrl = null,
			getChatUrl = null,
			signDocumentUrl = '<?php echo URL::to('/'); ?>/template_send',
			sendRequestUrl = '<?php echo URL::to('/'); ?>/template_send1',
			createTemplateUrl = '',
			baseUrl = '<?= url(""); ?>',
			auth = true;
		document_key = '<?php if (isset($document_all_details->id) && $document_all_details->id != '') {
							echo $document_all_details->id;
						} ?>';
		permission: "permission";
		tokens = "{{ csrf_token()}}";
		PDFJS.disableWorker = true;
		PDFJS.workerSrc = '<?php echo URL::to('/'); ?>/assets/esign/js/signer.min.js?id=<?php echo time(); ?>';

	<?php } ?>
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
	var pdfGenerateOrNot = "<?php if (isset($document_all_details->pdfgenerate) && $document_all_details->pdfgenerate != '') {
								echo $document_all_details->pdfgenerate;
							} ?>";
	var removeScript = 'docusign';
	var TestingArray = [];
	var name = '';
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
	var templateID = "<?php echo $document_all_details->templete_id; ?>";
	var document_report_id = "<?php echo $document_report_id; ?>";
	var _CSRF_TOKEN = "{{ csrf_token()}}";
</script>

<!-- custom scripts -->



<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.umd.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/app.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/appsignaturepad.js?<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signature_pad.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/signerNew.js?maNameIsKhan=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/render.js?keyurs=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/js/custom.js?keyurs=<?php echo strtotime(now()); ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/esign/libs/sweetalert/sweetalert.min.js"></script>
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
		htmls += '<div class="form-group" id="remove' + count + '"><label for="inputEmail3" class="col-md-2 control-label">Option</label><div class="col-md-9"><input type="text" class="form-control" id="inputEmail' + count + '" onChange="getDropValue(' + count + ',' + val + ')"></div> <a href="javascript:void(0)" onclick="getRemove(' + count + ')"><i class="fa fa-times" aria-hidden="true"></i></a></div>';

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
	function getLuncha() {
		inviting = false;
		//enableTools();
		launchEditor();
	}

	function getSignatureSuccess(filename, id,type="") {
		var res = filename;
		
		if(type !=""){
			$("#img" + id).attr('src', res);
		
		}else{
			$("#img" + id).attr('src', '<?php echo URL::to("/"); ?>/dosusinguploads/docusign/' + res);
		
		}
		$("#img" + id).attr('style', 'width:100px;');
		$("#img" + id).attr('dataids', 1);
		$('.signeeddate').val(common_dates_mdy);

	}

	function mySign(id) {


		getWebviewCanvas("<?php echo $id; ?>", "<?php echo $rand; ?>", id);
		getSearching();

	}

	function myStampUser(id) {
		getStampUser("<?php echo $id; ?>", "<?php echo $rand; ?>", id);
	}

	//testing();

	function getWebviewCanvas(documentMentId, rand, imgid) {

		$('#modal-default').modal('show');

		$('#modal-default #signature-pad .signature-pad--body canvas').attr('width', 550);
		$('#modal-default #signature-pad .signature-pad--body canvas').attr('height', 200);
		$('#imagesId').val(imgid);



		//getCreateNewImages(imgid);



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
			console.log(val);
			var text_val = $('#' + val.id).val();

			var value = "";
			var sThisVal = "";
			if (val.type == 'radio' && ('<?php echo $sent_on; ?>' == val.signer_id)) {


				var checked = $("input[name='" + val.name + "']").is(":checked");

				var classes = $('.' + val.bold).attr('style');
				if (checked == false && val.required == 'true' && !$('#' + val.bold).parent().hasClass("Depending")) {
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
			if (val.type == 'checkbox' && ('<?php echo $sent_on; ?>' == val.signer_id)) {
				var checked = $("#" + val.bold).is(":checked");
				var classes = $('.' + val.bold).attr('style');
				if (checked == false && val.required == true && !$('#' + val.id).parent().hasClass("Depending")) {
					var ereturns = classes + ";border: 2px solid #ff0000;";
					cnt++;
					$('.' + val.bold).attr('style', ereturns);
					return false;
				}
				
			}

			if (val.type == "image" && ('<?php echo $sent_on; ?>' == val.signer_id)) {
				// if (val.signer_id == "Patient" || val.signer_id == "Sign") {
				// 	imgRequired = $("#img" + i).attr('dataids',1);
				// }else{
					imgRequired = $("#img" + i).attr('dataids');
				// }
				width = val.width;
				height = val.height;
				value = $("#img" + i).attr('src');
				
				if (imgRequired == undefined || imgRequired == 0) {
					$('#img' + i).addClass('errors');
					alert("Page " + val.page + " signatures  is missing information please review");
					
					cnt++;
					return false;
				}

			} else {
				width = val.width;
				height = val.height;
				if (val.type == 'text' && val.readOnly == "readonly" && val.temp1 != 'intake' && val.temp1 != 'caregiver') {
					if (val.readOnly == "readonly" && val.placeHolder != 'TextBox') {
						value = val.text;
					} else {
						value = val.placeHolder;
					}

				} else {
					value = text_val;
				}

			}

			if (val.type == 'dropdown' && '<?php echo $sent_on; ?>' == val.signer_id) {
			
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

			if (val.type == 'text' && '<?php echo $sent_on; ?>' == val.signer_id) {
				if (val.required == "true" && text_val == '' && !$('#' + val.id).parent().hasClass("Depending")) {

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

			if ('<?php echo $sent_on; ?>' == val.signer_id || (val.temp1 == 'intake' || val.temp1 == 'caregiver') || val.placeHolder == "Date Signed") {
				if (val.type == 'radio') {
					var selectedid = $("input[name='" + val.name + "']:checked").attr('id');
					console.log(selectedid + "====" + val.bold);
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
					textsmall: val.textsmall
				});


			}
			i++;
		});
		if (cnt != 0) {

			return false;
		} else {
			var actions = JSON.stringify(final_array);

			showLoader();
			var href = "{{ url('esign/docusign/submit-form')}}";

			$.ajax({
				global: false,
				async: false,
				url: href,
				type: "POST",
				data: {
					'id': '<?php echo $document_all_details->templete_id; ?>',
					'action': actions,
					"sessionId": "<?php echo $sessionIds; ?>",
					'document_report_id': "<?php echo $document_report_id; ?>",
					"groupId": "<?php echo $groupId; ?>",
					"permission": headers,
					'_token': '{{ csrf_token()}}',
					'sent_on':'{{ $sent_on}}'
				},
				success: function(response) {


					setTimeout(function(e) {
						hideLoader();
					}, 5000);
					$('#finish_id').prop('disabled', true);
					$('#exist_id').attr('style', '');

					<?php if ($mobile_type == 'Android') { ?>
						AndroidFunction.success();
					<?php
					}
					if ($mobile_type == "IOS") { ?>
						window.webkit.messageHandlers["successExit"].postMessage('');
					<?php }
					if ($mobile_type == "web") { ?>
						window.location.href = "{{ url('esign/thankyou-esign')}}";
					<?php }
					?>
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
				}
			});
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


	var jsonresp = <?php
					
					$test = array('Adinda Melia.otf', 'Agashi Signature Demo.otf', 'AlfridaDemoSignature.ttf', 'Bellisya.otf', 'AmarulaPersonalUse.ttf');

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
						var htm = '<div class="form-group" onclick="selectedTextValue('+ssid+')"><div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" data-id="' + id + '" value="' + response + '" data-vas="'+sid+'" ><i class="input-helper"></i></label><img src="' + response + '" style="width: 200px;"></div>';

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
			$("#" + id).attr('src', data);
			$("#" + id).attr('style', 'width:100px;');
			$("#" + id).attr('dataids', 1);
			$('.signeeddate').val(common_dates_mdy);
		});



		$('#modal-default').modal('hide');
	}


	function alert(val) {
		swal({
			title: "Error",
			text: val,
			icon: "error",
			button: "Ok",
		});

	}

	function getSearching() {
		var names = $('#textboxxs_id').val();
		getCreateNewImages(names);
	}

	$('#stampUserForm').click(function(i,v){
		var data  = $('select[name="stamp_user"]').val();
		$('img').each(function(i, v) {
			var id = $(this).attr('id')
			$("#" + id).attr('src', data);
			$("#" + id).attr('style', 'width:100px;');
			$("#" + id).attr('dataids', 1);
			$('.signeeddate').val(common_dates_mdy);
		});
		$("#stampUserFormModal").modal("hide");

	})

	
	function getSubmitFileUpload(file) {
		var formData = new FormData();
		formData.append("file_upload", file);
		formData.append("_token", '{{ csrf_token()}}');

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

					getSignatureSuccess(response.url, imgid,'upload')
					$('#modal-default').modal('hide');
				}
			},
			error: function(xhr, status, error) {
				console.error("File upload error:", error);
			}
		});
	}

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

	function selectedTextValue(sid){
        $('input[data-vas="'+sid+'"]').prop("checked",true);
    }
</script>