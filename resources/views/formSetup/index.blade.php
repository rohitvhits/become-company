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

    .radius-50 {
        border-radius: 50px;
    }

    .hide {
        display: none;
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

    .status-dropdoown .btn-warning {
        border-radius: 20px;
        padding: 5px 15px !important;
        display: flex;
        align-items: center;
    }
</style>
<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Form Setup Management</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('form-setup-create')
                        <a class="btn btn-primary btn-rounded btn-fw btn-sm addFormSetupModal"
                            href="{{ route('form-setup.create') }}"><i class="mdi mdi-plus"> </i>Add
                            Form Setup</a>
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
                                <th style="width: 10% !important;">Is Default</th>
                                <th style="width: 10% !important;">Form Type</th>
                                <th style="width: 10% !important;">Agency</th>
                                <th style="width: 10% !important;">Template Name</th>
                                <th style="width: 5% !important;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="refreshDivNew">
                            <?php if ($formSetup->total() != 0) {
                                $i = 1 + (($formSetup->currentPage() - 1) * $formSetup->perPage());
                                foreach ($formSetup as $key => $row) {  ?>
                            <tr class="form-setup-class" id="<?php echo $row->id; ?>">

                                <td><?= $key + 1 ?></td>
                                <td id="title-{{ $row->id }}">{{ ucfirst($row->title) }}</td>
                                <td id="is-default-{{ $row->id }}">
                                    <?php echo $row->is_default == 1 ? 'Yes' : 'No'; ?>
                                </td>
                                <td id="form-type-{{ $row->id }}">
                                    <?php echo $row->form_type == 1 ? 'Patient' : 'Caregiver'; ?>
                                </td>
                                <td id="agency-{{ $row->id }}">{{ $row->agencyValue->agency_name ?? '-' }}</td>
                                <td id="template-{{ $row->id }}">
                                    {{ $row->getTemplateById[0]['template_name'] ?? '-' }}
                                </td>

                                <td>
                                    <div class="btn-group pull-right status-dropdoown mr-2">
                                        <button type="button" class="btn btn-warning" title="Action">Action</button>
                                        <button type="button"
                                            class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                            id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                            @can('form-setup-show')
                                                <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                                    data-name="{{ $row->title }}"
                                                    class="pull-left ml-1 viewFormSetup dropdown-item" data-toggle="tooltip"
                                                    title="View">View</a>
                                            @endcan
                                            @can('form-setup-edit')
                                                <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                                    data-name="{{ $row->title }}"
                                                    class="pull-left ml-1 editFieldMaster dropdown-item"
                                                    data-toggle="tooltip" title="Edit">
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('form-setup-template-link')
                                                <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                                    data-name="{{ $row->title }}"
                                                    class="pull-left ml-1 viewTemplate dropdown-item" data-toggle="tooltip"
                                                    title="Link Template">
                                                    Link Template
                                                </a>
                                            @endcan
                                            @can('form-setup-delete')
                                                <a class="pull-left ml-1 deleteFormSetup dropdown-item"
                                                    data-toggle="tooltip" title="Delete" href="javascript:void(0)"
                                                    data-did="{{ $row->id }}">
                                                    Delete
                                                </a>
                                            @endcan
                                            @can('agency-form-setup-show')
                                                <a href="{{ route('agency-master-list') }}?agency_id={{ $row->agency }}&form_id={{ $row->id }}"
                                                    class="pull-left ml-1 dropdown-item" target="_blank"
                                                    data-toggle="tooltip" title="View Agency Wise Field">
                                                    View Agency Wise Field
                                                </a>
                                            @endcan
                                            @can('form-group-list')
                                                <a href="{{ url('form-group') }}?form_id={{ $row->id }}"
                                                    class="pull-left ml-1 dropdown-item" target="_blank"
                                                    data-toggle="tooltip" title="View Form Group">
                                                    View Form Group
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <?php }
                            }  ?>
                            <tr id="hidedis" class=" @if ($formSetup->total() != 0) hide @else @endif">
                                <td colspan="12">
                                    <center><b>Data not found</b></center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pull-right pegination-margin">
                        {{ $formSetup->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    @include('formSetup._partial.create')
    @include('formSetup._partial.view_form_setup_modal')
    @include('include/footer')

    <script>
        var createTemplatePermission = @json(auth()->user()->can('template-add'));
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var editData = "{{ route('form-setup.edit', 'id') }}";
        var getTemplateData = "{{ route('get.templates') }}";
        var storeTemplateData = "{{ route('store-template') }}";
        var addTemplateUrl = "{{ url('template-add') }}";
        var agencyMasterListUrl = "{{ route('agency-master-list') }}";
        var formGroupUrl = "{{ url('form-group') }}";
        var formSetupShowPermission = @json(auth()->user()->can('form-setup-show'));
        var formSetupEditPermission = @json(auth()->user()->can('form-setup-edit'));
        var formSetupDeletePermission = @json(auth()->user()->can('form-setup-delete'));
        var formGroupListPermission = @json(auth()->user()->can('form-group-list'));
        var formSetupTemplatePermission = @json(auth()->user()->can('form-setup-template-link'));
        var formSetupAgencyShowPermission = @json(auth()->user()->can('agency-form-setup-show'));

        $(".agency_id").val('');

        $(document).on("click", ".deleteFormSetup", function() {
            var id = $(this).attr('data-did');
            deleteFormSetup(id);
        });

        function deleteFormSetup(id) {
            var upUrl = "{{ route('form-setup.destroy', 'id') }}";
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to delete this Form Setup?",
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
                                console.log(id);
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
