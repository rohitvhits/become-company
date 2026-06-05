@include('include/header')

@include('include/sidebar')



<!-- Breadcrumb -->

<div id="content">

	<div class="row no-margin-padding">

		<div class="col-md-6">

			<h3 class="block-title">{{ trans('sentence.Appointment list')}}</h3>

		</div>

		<div class="col-md-6">

			<ol class="breadcrumb">

				<li class="breadcrumb-item">

					<a href="<?php echo URL::to('/dashboard'); ?>">

						<span class="ti-home"></span>

					</a>

				</li>

				<li class="breadcrumb-item active"><a href="<?php echo URL::asset("/"); ?>appointment_add">{{ trans('sentence.Appointment Add')}}</a></li>

			</ol>

		</div>

	</div>

	<!-- /Breadcrumb -->
	<div class="container-fluid">

		<div class="row">

			<!--General Table-->

			<div class="col-md-12">

				<div class="widget-area-2 proclinic-box-shadow">

					<h3 class="widget-title" style="border-bottom: 0px;">{{ trans('sentence.Appointment list')}}</h3>



					<div class="table-div" style="overflow-x:auto;">

						<table class="table">

							<thead>

								<tr>

									<th scope="col">#</th>

									<th scope="col">{{ trans('sentence.Date')}}</th>

									<th scope="col">{{ trans('sentence.Time')}}</th>

									<th scope="col">{{ trans('sentence.Appointment Id')}}</th>

									<th scope="col">{{ trans('sentence.Customer Name')}}</th>

									<th scope="col">{{ trans('sentence.Customer Phone Number')}}</th>

									<th scope="col">{{ trans('sentence.Customer Email')}}</th>

									<th scope="col">{{ trans('sentence.service type')}}</th>

									<th scope="col">{{ trans('sentence.status')}}</th>

									<th scope="col">{{ trans('sentence.booking mode')}}</th>

									<th scope="col">{{ trans('sentence.Action')}}</th>

								</tr>

							</thead>

							<tbody>

								<?php $i = 1;
								foreach ($query as $row) { ?>

									<tr>

										<th scope="row"><?= $i++ ?></th>

										<td><?= date('m/d/Y', strtotime($row->booking_date)) ?></td>

										<td><?= date('H:i', strtotime($row->booking_time)) ?></td>

										<td><?= $row->appointment_id ?></td>

										<td><?= $row->customer_name ?></td>

										<td><?= $row->customer_phone ?></td>

										<td><?= $row->customer_email ?></td>

										<td><?= $row->types_of_services ?></td>

										<td><?= $row->booking_status ?></td>

										<td><?= $row->booking_type ?></td>

										<td><a href="<?php echo URL::asset("/"); ?>appointment_edit?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="<?php echo URL::asset("/"); ?>appointment_delete?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>

									</tr>

								<?php } ?>

							</tbody>

						</table>





						<div class="row"><?php echo $query->links(); ?></div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

@include('include/footer')