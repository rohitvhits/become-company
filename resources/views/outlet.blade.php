@include('include/header')

@include('include/sidebar')



<!-- Breadcrumb -->

<div id="content">

	<div class="row no-margin-padding">

		<div class="col-md-6">

			<h3 class="block-title">{{ trans('sentence.Outlet Master list')}}</h3>

		</div>

		<div class="col-md-6">

			<ol class="breadcrumb">

				<li class="breadcrumb-item">

					<a href="<?php echo URL::to('/dashboard'); ?>">

						<span class="ti-home"></span>

					</a>

				</li>

				<li class="breadcrumb-item active"><a href="<?php echo URL::asset("/"); ?>outlet_add">{{ trans('sentence.Add Outlet')}}</a></li>

			</ol>

		</div>

	</div>

	<!-- /Breadcrumb -->
	<div class="container-fluid">

		<div class="row">

			<!--General Table-->

			<div class="col-md-12">

				<div class="widget-area-2 proclinic-box-shadow">

					<h3 class="widget-title" style="border-bottom: 0px;">{{ trans('sentence.Outlet Master list')}}</h3>



					<div class="table-div">

						<table class="table">

							<thead>

								<tr>

									<th scope="col">#</th>

									<th scope="col">{{ trans('sentence.Outlet Name')}}</th>

									<th scope="col">{{ trans('sentence.Outlet Code')}}</th>

									<th scope="col">{{ trans('sentence.Outlet State')}}</th>

									<th scope="col">{{ trans('sentence.Outlet Phone')}}</th>

									<th scope="col">{{ trans('sentence.Outlet Googleurl')}}</th>

									<th scope="col">{{ trans('sentence.Action')}}</th>

								</tr>

							</thead>

							<tbody>

								<?php $i = 1;
								foreach ($query as $row) { ?>

									<tr>

										<th scope="row"><?= $i++ ?></th>

										<td><?= $row->name ?></td>

										<td><?= $row->code ?></td>

										<td><?= $row->state ?></td>

										<td><?= $row->phone ?></td>

										<td><?= $row->google_url ?></td>

										<td><a href="<?php echo URL::asset("/"); ?>outlet_edit?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="<?php echo URL::asset("/"); ?>delete_outlet?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="fa fa-trash" aria-hidden="true"></i></a> <a href="<?php echo URL::asset("/"); ?>booking_outlet?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Outlet Appointment')}}"><i class="fa fa-clock-o" aria-hidden="true"></i></a></td>

									</tr>

								<?php } ?>

							</tbody>

						</table>

						<div><?php echo $query->links(); ?></div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

@include('include/footer')