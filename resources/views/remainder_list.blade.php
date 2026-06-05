@include('include/header')
@include('include/sidebar')
<?php

?>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet" type="text/css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style>
	.mini-card .form-control {
		height: 20px;
		padding: 2px;
	}

	dl {
		margin-top: 0;
		margin-bottom: 20px;
	}

	ul,
	ol,
	dl {
		padding-left: 0px !important;
	}

	.dl-horizontal dt {
		float: left;
		width: 72px;
		clear: left;
		text-align: right;
		/* overflow: hidden; */
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.dl-horizontal dt {
		float: left;
		width: 85px;
		clear: left;
		text-align: right;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	#otherupdated_id {
		width: 750px;
	}

	#other_id {
		width: 750px;
	}

	h6.fm_1 {
		/* text-align: end;*/
		font-size: 14px;
	}

	dt {
		font-weight: 700;
	}

	.dl-horizontal dd {
		margin-left: 90px;
		margin-bottom: 0px;
	}

	.ml-3,
	.rtl .settings-panel .sidebar-bg-options .rounded-circle,
	.rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
	.rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
	.mx-3 {
		margin-left: 1rem !important;
		width: 100%;
	}

	#hr2 .dl-horizontal dd {
		margin-left: 110px;
	}

	#hr2 .dl-horizontal dt {
		width: 101px;
	}

	.profile-feed-item.abc {
		padding: 0;
		border: none;
	}

	.profile-feed-item.border {
		border: none;
	}

	.htv {
		height: 50%;
	}

	.removeSpace {
		margin-top: 0px !important;
		margin-bottom: 0px !important
	}

	#loadersId {
		float: left
	}

	.tab-content {
		padding: 0.5rem;
	}

	.alert-warning {
		color: #856404;
		background-color: #fff3cd;
		border-color: #ffeeba;
	}

	.error {
		color: red;
	}

	#Commsas::first-letter {
		text-transform: uppercase;
	}
</style>
<!--main-container-part-->
<div class="main-panel">
	<div class="content-wrapper">
		<div class="row">
			<div class="col-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<p class="card-title mb-0">Notes Section</p>
							<div class="pull-right">
								<input type="radio" class="" value="1" name="radio1" onclick="getClickAble('Agency');">Agency
								<input type="radio" class="" value="0" checked='checked' name="radio1" onclick="getClickAble('Self');">Self
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="chat-messages" id="sms-messages">
									<div id="chat-messages-inner" class="notes-messages"></div>
								</div>
								<div class="chat-message  custom-chat">
									<form id="attachsubmits" method="post" onsubmit="return false;">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
										<button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendMessagefile()">Send</button>
										<span class="input-box">
											<!--   <input type="text" name="msg-box" id="text-msg-box" /> -->
											<textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="text-sms-box"></textarea>
											<input type="hidden" name="agency_id" value="">

										</span>
									</form>


								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


		</div>

	</div>


	@include('include/footer')
	<script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
	<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
	<script>
		toastr.options.closeButton = true;
		toastr.options.tapToDismiss = false;
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "500",
			"timeOut": "3000",
			"extendedTimeOut": 0,
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut",
			"tapToDismiss": false
		};

		function sendMessagefile() {
			var alldata = new FormData($('#attachsubmits')[0]);
			var id = '';
			var name = "you";
			var message = $('#text-sms-box').val();
			var radio1 = $('input[name="radio1"]:checked').val();

			if (id != 0 && message != "") {
				$.ajax({
					type: 'POST',
					data: alldata,
					url: "<?php echo URL::to('/'); ?>/patient/patient-notes/" + id,
					dataType: "json",
					mimeType: "multipart/form-data",
					contentType: false,
					processData: false,

					success: function(response) {

						addSMSmessage('You', 'Send', message, "", true);
						// You will get response from your PHP page (what you echo or print)
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
					}
				});
			}
		}

		function addSMSmessage(name, ctype, msg, file, clear) {

			smsCounter = smsCounter + 1;
			var inner = $('.notes-messages');
			var time = new Date();
			var hours = time.getHours();
			var minutes = time.getMinutes();
			if (hours < 10) hours = '0' + hours;
			if (minutes < 10) minutes = '0' + minutes;
			var id = 'sms-msg-' + smsCounter;
			var idname = name.replace(' ', '-').toLowerCase();
			inner.append('<p id="' + id + '" class="user-' + idname + '">' +
				'<span class="msg-block"> <strong>' + name + ' (' + ctype + ')</strong> <span class="time"> ' + hours + ':' + minutes + '</span>' +
				'<span class="msg">' + msg + ' ' + file + '</span></span></p>');

			$('#' + id).hide().fadeIn(800);
			if (clear) {
				$('#text-sms-box').val('').focus();
			}
			$('#sms-messages').animate({
				scrollTop: inner.height()
			}, 1000);
		}

		function loadAllNotes() {
			$('.notes-messages').html("");
			$('#loadersId').attr('style', 'display:block');
			var mess = $("input[name='agency_id']").val();
			var agency_id = "";

			$.ajax({
				url: "<?php echo URL::to('/'); ?>/patient/get-notes/<?php echo $record->id; ?>",
				type: "post",
				data: {
					_token: '<?php echo csrf_token(); ?>',
					'readMessage': mess,
					'agency_id': agency_id
				},
				success: function(response) {
					response.forEach(element => {
						add_message_obj(element.id, element.first_name, 'https://web.exmedc.com/img/demo/av1.jpg', element.message, element.created_date, element.type, element.sender_id);

					});
					$('#loadersId').attr('style', 'display:none;');
					// add_message('You', 'img/demo/av1.jpg', input.val(), true);
					// You will get response from your PHP page (what you echo or print)
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});
			return false;
		}
		loadAllNotes();
		var i = 0;
		var smsCounter = 0;

		function add_message_obj(mid, name, img, msg, date, type, sender_id, clear) {
			//alert(sender_id);
			i = i + 1;

			var inner = $('.notes-messages');
			var time = new Date(date);
			var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

			var hours = time.getHours();
			var minutes = time.getMinutes();
			if (hours < 10) hours = '0' + hours;
			if (minutes < 10) minutes = '0' + minutes;
			var id = 'msg-' + i;
			//  var type="Receive";
			var ondelete = '';


			var idname = "";
			inner.append('<p id="' + id + '" class="user-' + idname + '">' +
				'<span class="msg-block"><strong>' + name + '  (' + type + ') </strong><span class="time"> ' + date + ' ' + hours + ':' + minutes + '</span>' +
				'<span class="msg">' + msg + '<span class="pull-right">' + ondelete + '</span></span></span></p>');
			$('#' + id).hide().fadeIn(800);
			if (clear) {
				$('.chat-message textarea').val('').focus();
			}
			$('#sms-messages').animate({
				scrollTop: inner.height()
			}, 20);
		}
	</script>