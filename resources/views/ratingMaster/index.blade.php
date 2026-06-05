@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

<style>
    .add-field {
        display: flex;
        justify-content: end;
    }

    .add-field a {
        height: 36px;
        border-radius: 50px;
        line-height: 17px;
    }

    .hide {
        display: none;
    }

    .radius-50 {
        border-radius: 50px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .selection .select2-selection {
        height: 40px;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }
</style>
<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Rating Master Management</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('rating-master-create')
                        <a class="btn btn-primary btn-rounded btn-fw btn-sm addRatingMasterModal"
                            href="{{ route('rating-master.create') }}"><i class="mdi mdi-plus"> </i>Add
                            Rating Master</a>
                    @endcan
                </div>
            </div>
        </div>
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 5% !important;">ID</th>
                                <th style="width: 10% !important;">Title</th>
                                <th style="width: 10% !important;">Type</th>
                                <th style="width: 5% !important;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="refreshDivNew">
                            <?php if ($ratingMaster->total() != 0) {
                                $i = 1 + (($ratingMaster->currentPage() - 1) * $ratingMaster->perPage());
                                foreach ($ratingMaster as $key => $row) {  ?>
                            <tr class="rating-master-class" id="<?php echo $row->id; ?>">

                                <td>{{$key + 1}}</td>
                                <td id="title-{{ $row->id }}"><?php echo ucfirst($row->title); ?></td>
                                <td id="type-{{ $row->id }}"><?php echo ucfirst($row->type); ?></td>
                                <td>
                                    @can('rating-master-show')
                                        <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                            data-name="{{ $row->label }}"
                                            class="pull-left ml-1 viewRatingMaster">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('rating-master-edit')
                                        <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                            data-name="{{ $row->label }}"
                                            class="pull-left ml-1 editRatingMaster">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('rating-master-delete')
                                        <a class="pull-left ml-1 deleteRatingMaster"
                                            href="javascript:void(0)" data-did="{{ $row->id }}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endcan

                                    
                                </td>
                            </tr>
                            <?php }
                            }  ?>
                            <tr id="hidedis" class=" @if ($ratingMaster->total() != 0) hide @else @endif">
                                <td colspan="12">
                                    <center><b>Data not found</b></center>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <div class="pull-right pegination-margin">
                        {{ $ratingMaster->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    @include('ratingMaster._partial.create')
    @include('ratingMaster._partial.view_rating_master_modal')
    @include('include/footer')

    <script>
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var editData = "{{ route('rating-master.edit', 'id') }}";
        var _DELETE_URL = "{{ route('rating-master.destroy', 'id') }}";
    </script>

    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
