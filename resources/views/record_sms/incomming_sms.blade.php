@include('include/header')
@include('include/sidebar')

<div id="content">
	<hr>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div class="widget-box">
					<div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
						<h5>Records List</h5>
						<a href="<?php echo URL::to('/record/add') ?>" class="btn btn-primary pull-right">Record Add</a>
					</div>
					<div class="widget-content nopadding">
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">{{ trans('sentence.name')}}</th>
									<th scope="col">{{ trans('sentence.email')}}</th>
									<th scope="col">{{ trans('sentence.phone')}}</th>

									<th scope="col">{{ trans('sentence.Action')}}</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1;
								foreach ($query as $row) {  ?>
									<tr>
										<th scope="row"><?= $i++ ?></th>
										<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"> <?= $row->first_name.' '.$row->middle_name.' '.$row->last_name ?> </a></td>
										<td><?= $row->email ?></td>
										<td><?= $row->phone ?></td>


										<td><a href="<?php echo URL::asset("/"); ?>record/edit/<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="icon-edit"></i></a> <a href="<?php echo URL::asset("/"); ?>record/delete/<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="icon-trash"></i></a></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<div>
							<?php echo $query->links(); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('include/footer')