@include('include/header')

@include('include/sidebar')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<link rel="stylesheet" href="/resources/demos/style.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo URL::asset("/"); ?>assets/dist/bootstrap-clockpicker.min.css">

<!-- Breadcrumb -->

<div id="content">

	<div class="row no-margin-padding">

		<div class="col-md-6">

			<h3 class="block-title">{{ trans('sentence.Appointment Filter')}}</h3>

		</div>

		<div class="col-md-6">

			<ol class="breadcrumb">

				<li class="breadcrumb-item">

					<a href="<?php echo URL::to('/dashboard'); ?>">

						<span class="ti-home"></span>

					</a>

				</li>

				<li class="breadcrumb-item">{{ trans('sentence.Appointment Filter')}}</li>

			</ol>

		</div>

	</div>

	<!-- /Breadcrumb -->

	<div class="container-fluid">

		<div class="row">

			<!--General Table-->

			<div class="col-md-12">

				<div class="widget-area-2 proclinic-box-shadow">



					<h3 class="widget-title" style="border-bottom: 0px;">{{ trans('sentence.Appointment Filter')}}</h3>







					<form method="get" action='<?php echo URL::to('/appointment_filter') ?>' name="addappointment" enctype="multipart/form-data" onsubmit="return validation();">

						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

						<div class="form-row">



							<div class="form-group  col-md-4">

								<label for="bookingStartDate">{{ trans('sentence.start date')}} <span style="color:red;">*</span></label>

								<input type="text" class="form-control" id="bookingStartDate" name="bookingStartDate" placeholder="{{ trans('sentence.start date')}}" value="<?= $startdate ?>" autocomplete="off">

								<span id="sdate_error" style="color:red"><?php echo $errors->add_user->first('bookingStartDate'); ?></span>



							</div>

							<div class="form-group  col-md-4">

								<label for="bookingEndDate">{{ trans('sentence.end date')}} <span style="color:red;">*</span></label>

								<input type="text" class="form-control" id="bookingEndDate" name="bookingEndDate" placeholder="{{ trans('sentence.end date')}}" value="<?= $enddate ?>" autocomplete="off">

								<span id="edate_error" style="color:red"><?php echo $errors->add_user->first('bookingEndDate'); ?></span>



							</div>

							<?php if ($user->type == "superadmin") { ?>

								<div class="form-group col-md-4">

									<label for="Outlet">{{ trans('sentence.Outlet')}} </label>

									<select class="form-control" id="outlet" name="outlet">

										<option value="">{{ trans('sentence.Select')}} {{ trans('sentence.Outlet')}}</option>

										<?php foreach ($outlet as $row) { ?>

											<option value="<?= $row->id ?>" <?php if (isset($outlet_fk) && $outlet_fk != "") {
																				if ($outlet_fk == $row->id) {
																					echo "selected";
																				}
																			} ?>><?= $row->name ?></option>

										<?php } ?>

									</select>



								</div>

							<?php } ?>

							<div class="form-group col-md-4">

								<label for="customerName">{{ trans('sentence.Customer Name')}}</label>

								<input type="text" placeholder="{{ trans('sentence.Customer Name')}}" value="<?= $customerName ?>" class="form-control" id="customerName" name="customerName">



							</div>







							<div class="form-group col-md-4">

								<label for="status">{{ trans('sentence.status')}} </label>

								<select class="form-control" id="status" name="status">

									<option value="">{{ trans('sentence.Select')}} {{ trans('sentence.status')}}</option>

									<option value="Attended" <?php if (isset($status) && $status != "") {
																	if ($status == "Attended") {
																		echo "selected";
																	}
																} ?>>Attended</option>

									<option value="Missed" <?php if (isset($status) && $status != "") {
																if ($status == "Missed") {
																	echo "selected";
																}
															} ?>>Missed</option>

									<option value="All" <?php if (isset($status) && $status != "") {
															if ($status == "All") {
																echo "selected";
															}
														} ?>>All</option>

								</select>

							</div>



							<div class="form-group col-md-4">

								<label for="mob">{{ trans('sentence.booking mode')}} </label>

								<select class="form-control" id="mob" name="mob">

									<option value="">{{ trans('sentence.Select')}} {{ trans('sentence.booking mode')}}</option>

									<option value="Chatbot" <?php if (isset($booking_mode) && $booking_mode != "") {
																if ($booking_mode == "Chatbot") {
																	echo "selected";
																}
															} ?>>Chatbot</option>

									<option value="CallIN" <?php if (isset($booking_mode) && $booking_mode != "") {
																if ($booking_mode == "CallIN") {
																	echo "selected";
																}
															} ?>>Call ln</option>

									<option value="WalkIN" <?php if (isset($booking_mode) && $booking_mode != "") {
																if ($booking_mode == "WalkIN") {
																	echo "selected";
																}
															} ?>>Walk IN</option>

								</select>



							</div>



							<div class="form-group col-md-12 mb-3">

								<button type="submit" name="submit" class="btn btn-primary btn-lg">{{ trans('sentence.Search')}}</button>

								<input type="submit" name="export" value="{{ trans('sentence.Export')}}" class="btn btn-primary btn-lg">

							</div>



							<div class="form-group col-md-3">

								<label for="mob">{{ trans('sentence.totalattend')}} : <?php if ($startdate != "") {
																							echo $totalattend;
																						} else {
																							echo "0";
																						} ?></label>



							</div>

							<div class="form-group col-md-12">

								<label for="mob">{{ trans('sentence.totalmissed')}} : <?php if ($startdate != "") {
																							echo $totalmissed;
																						} else {
																							echo  '0';
																						} ?></label>



							</div>

						</div>

					</form>

					<div class="table-div">

						<table class="table">

							<thead>

								<tr>

									<th scope="col">{{ trans('sentence.outlet code')}}</th>

									<th scope="col">{{ trans('sentence.Appointment Id')}}</th>

									<th scope="col">{{ trans('sentence.Date')}}</th>

									<th scope="col">{{ trans('sentence.Time')}}</th>

									<th scope="col">{{ trans('sentence.Customer Name')}}</th>

									<th scope="col">{{ trans('sentence.Customer Phone Number')}}</th>

									<th scope="col">{{ trans('sentence.Customer Email')}}</th>

									<th scope="col">{{ trans('sentence.status')}}</th>

								</tr>

							</thead>

							<tbody>

								<?php if (isset($startdate) && $startdate != "") {
									$i = 1;
									foreach ($query as $row) { ?>

										<tr>

											<td><?= $row->outlet_code ?></td>

											<td><?= $row->appointment_id ?></td>

											<td><?= date('m/d/Y', strtotime($row->booking_date)) ?></td>

											<td><?= date('H:i', strtotime($row->booking_time)) ?></td>

											<td><?= $row->customer_name ?></td>

											<td><?= $row->customer_phone ?></td>

											<td><?= $row->customer_email ?></td>

											<td><?= $row->booking_status ?></td>



										</tr>

								<?php }
								} ?>

							</tbody>

						</table>

						<div><?php if (isset($startdate) && $startdate != "") {
									echo $query->links();
								} ?></div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>





<script>
	$("#bookingStartDate").datepicker();

	$("#bookingEndDate").datepicker();



	function validation() {



		var sdate = $('#bookingStartDate').val();

		var edate = $('#bookingEndDate').val();

		var temp = 0;

		if (sdate == "") {
			$('#sdate_error').html("Required");
			temp++;
		} else {
			$('#sdate_error').val("");
		}

		if (edate == "") {
			$('#edate_error').html("Required");
			temp++;
		} else {
			$('#edate_error').val("");
		}

		if (temp == 0) {

			return true;

		} else {

			return false;

		}

	}
</script>

@include('include/footer')