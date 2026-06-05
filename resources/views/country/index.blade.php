@include('include/header')
@include('include/sidebar')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="row list-name">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">Country List</h4>
                </div>
                <div class="col-sm-6">
                    <a href="<?php echo URL::to("/"); ?>/country" class="btn btn-danger btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i> Reset</a>
                    <a href="<?php echo URL::to('/country/create') ?>" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-plus"> </i> Add Country </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <form method="get" action="{{route('country.index')}}">
                                <table id="" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>

                                            <td><input type="text" autocomplete="off" class="form-control" name="name" id="name" value="{{$name}}" autocomplete="off" placeholder="Name"></td>
                                            </td>
                                            <td><select class="form-control" name="status" id="status">
                                                    <option value="">Select Status</option>
                                                    <option value="block" <?php if ($status == 'block') {
                                                                                echo 'selected';
                                                                            } ?>>Block</option>
                                                    <option value="unblock" <?php if ($status == 'unblock') {
                                                                                echo 'selected';
                                                                            } ?>>UnBlock</option>
                                                </select></td>
                                            <td></td>
                                            <td><input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search"></td>
                                        </tr>
                                        <?php
                                        if ($list->total() != 0) {
                                            $i = 1 + (($list->currentPage() - 1) * $list->perPage());
                                            foreach ($list as $data) {  ?>
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$data->name}}</td>
                                                    <td>{{$data->status}}</td>
                                                    <td>{{$data->created_at}}</td>
                                                    <td><a href="<?php echo URL::asset("/"); ?>country/edit/<?= $data->id ?>" data-toggle="tooltip" title="Edit"><i class="mdi mdi-eyedropper"></i></a> <a href="<?php echo URL::asset("/"); ?>country/delete/<?= $data->id ?>" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure remove this record?')"><i class="mdi mdi-delete"></i></a> </td>
                                                </tr>
                                        <?php
                                                $i++;
                                            }
                                        } ?>

                                    </tbody>
                                </table>
                            </form>
                            <div class="pull-right pegination-margin">
                                {{$list->links("pagination::bootstrap-4")}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('include/footer')