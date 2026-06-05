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
            <h5 class="mb-0 font-weight-bold">Field Master Management</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('field-master-create')
                        <a class="btn btn-primary btn-rounded btn-fw btn-sm addFieldMasterModal"
                            href="{{ route('field-master.create') }}"><i class="mdi mdi-plus"> </i>Add
                            Field Master</a>
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
                                <th style="width: 10% !important;">Label</th>
                                <th style="width: 10% !important;">Type</th>
                                <th style="width: 10% !important;">Size</th>
                                <th style="width: 10% !important;">Character Limit</th>
                                <th style="width: 5% !important;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="refreshDivNew">
                            <?php if ($formFields->total() != 0) {
                                $i = 1 + (($formFields->currentPage() - 1) * $formFields->perPage());
                                foreach ($formFields as $key => $row) {  ?>
                            <tr class="field-master-class" id="<?php echo $row->id; ?>">

                                <td>{{$key + 1}}</td>
                                <td id="label-{{ $row->id }}"><?php echo ucfirst($row->label); ?></td>
                                <td id="type-{{ $row->id }}"><?php echo ucfirst($row->type); ?></td>
                                <td id="size-{{ $row->id }}"><?php echo ucfirst($row->size); ?></td>
                                <td id="set-character-limit-{{ $row->id }}"><?php echo $row->set_character_limit ?? '-'; ?></td>
                                <td>
                                    @can('field-master-show')
                                        <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                            data-name="{{ $row->label }}"
                                            class="pull-left ml-1 viewFieldMaster">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('field-master-edit')
                                        <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                            data-name="{{ $row->label }}"
                                            class="pull-left ml-1 editFieldMaster">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('field-master-delete')
                                        <a class="pull-left ml-1 deleteFieldMaster"
                                            href="javascript:void(0)" data-did="{{ $row->id }}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                            <?php }
                            }  ?>
                            <tr id="hidedis" class=" @if ($formFields->total() != 0) hide @else @endif">
                                <td colspan="12">
                                    <center><b>Data not found</b></center>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <div class="pull-right pegination-margin">
                        {{ $formFields->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    @include('fieldMaster._partial.create')
    @include('fieldMaster._partial.view_field_master_modal')
    @include('include/footer')

    <script>
        var _FORM_GROUP_URL = "{{ url('get-form-groups') }}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var editData = "{{ route('field-master.edit', 'id') }}";
        $(".agency_id").val('');

        $(document).on("click", ".deleteFieldMaster", function() {
            var id = $(this).attr('data-did');
            deleteFieldMaster(id);
        });

        function deleteFieldMaster(id) {
            var upUrl = "{{ route('field-master.destroy', 'id') }}";
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to delete this Field Master?",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: !1
            }).then((result) => {
                var url = upUrl;
                url = url.replace('id', id);
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        async: false,
                        url: url,
                        type: "DELETE",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status) {
                                $("#" + id).remove();                                
                                toastr.success(response.msg);
                                if (response.data == 0) {
                                    $('#hidedis').removeClass('hide');
                                }
                            } else {
                                toastr.error(response.msg);
                            }
                        }
                    });
                } else {
                    return false;
                }
            });
        }
    </script>

    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
