@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

<style>
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

    .modal-content {
        border-radius: 8px;
        padding: 20px;
    }

    .modal-header {
        background-color: #f7f7f7;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .modal-title {
        font-weight: bold;
    }

    .close {
        background: none;
        border: none;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    .form-check-input {
        margin-top: 0.3rem;
    }

    .form-check-label {
        margin-left: 1.25rem;
    }

    .modal-footer {
        display: flex;
        /* justify-content: space-between; */
        align-items: center;
    }

    .btn-secondary,
    .btn-primary {
        /* padding: 0.5rem 1rem; */
        /* border-radius: 4px; */
    }

    #sortableTable {
        tbody tr:hover {
            cursor: grab;
        }
    }
</style>
<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Form Wise Field Management</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('agency-form-setup-add-new-field')
                        <a class="btn btn-success btn-rounded btn-sm btn-fw pull-right addFieldMasterModal"
                            href="javascript:void(0)" onclick="getFormGroupData()"><i class="mdi mdi-plus"> </i>Add
                            Custom</a>
                    @endcan
                    @can('agency-form-setup-add-custom-field')
                        <a href="javascript:void(0);" class="btn btn-success btn-rounded btn-sm btn-fw pull-right mr-2"
                            data-toggle="modal" data-target="#addFieldModal" data-id="{{ $agency_id }}"
                            onclick="getFormGroupDataNewField()">
                            <i class="mdi mdi-plus"></i> Add New Field
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sortableTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Form Group</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Character Limit</th>
                                <th class="pull-left">Action</th>
                            </tr>
                        </thead>

                        <tbody id="refreshDiv">
                            <?php
                            $i = ($page != "") ? ($page * 10) - 9 : 1;
                            foreach ($formFields as $key => $row) {  ?>
                            <tr class="form-list-classs" id="<?php echo $row->id; ?>">

                                <td><span id="rowIndex"><?= $key + 1 ?></span></td>
                                <td><?php echo $row->form_group_title ?? '-'; ?></td>
                                <td><?php echo ucfirst($row->label); ?></td>
                                <td><?php echo ucfirst($row->type); ?></td>
                                <td><?php echo ucfirst($row->size); ?></td>
                                <td><?php echo $row->set_character_limit ?? '-'; ?></td>
                                <td>
                                    @can('agency-form-wise-field-delete')
                                        <a href="javascript:void(0);" data-eid="<?php echo $row->id; ?>"
                                            data-fid="<?php echo $form_id; ?>" data-aid="<?php echo $agency_id; ?>"
                                            data-name="<?php echo $row->label; ?>" class="pull-left viewAgencyMaster ml-1">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('agency-form-wise-field-show')
                                        <a class="pull-left deleteAgencyMaster ml-1" href="javascript:void(0)"
                                            data-did="<?php echo $row->id; ?>" data-fid="<?php echo $form_id; ?>"
                                            data-aid="<?php echo $agency_id; ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endcan
                                    <input class="sortID" type="hidden" name="sortID[]" value="" />
                                    <input class="formFieldsID" type="hidden" name="formFieldsID[]"
                                        value="{{ $row->id }}" />
                                    <input class="agencyId" type="hidden" name="agencyId[]"
                                        value="{{ $agency_id }}" />
                                    <input class="formID" type="hidden" name="formID[]" value="{{ $form_id }}" />
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (count($formFields) == 0) { ?>
                            <tr class="hide-no-record">
                                <td colspan="6"> No record available</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="pull-right pegination-margin">
            {{-- {{ $formFields->appends(request()->query())->links('pagination::bootstrap-4') }} --}}
        </div>
        <div class="modal fade" id="viewAgencyMasterModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">View Field Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card-body pl-0">
                            <div class="form-group row">
                                <label class="col-sm-3">Label:</label>
                                <div class="col-sm-9">
                                    <span class="label-html"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">Type:</label>
                                <div class="col-sm-9">
                                    <span class="type-html"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">Size:</label>
                                <div class="col-sm-9">
                                    <span class="size-html"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">Character Limit:</label>
                                <div class="col-sm-9">
                                    <span class="set-character-limit-html"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">Show in Portal:</label>
                                <div class="col-sm-9">
                                    <span class="show-in-portal-html"></span>
                                </div>
                            </div>
                            <div class="form-group row option-html-view d-none">
                                <label class="col-sm-3">Options:</label>
                                <div class="col-sm-9 ">
                                    <ul class="option-html">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('include/footer')
@include('fieldMaster._partial.create')
@include('fieldMaster._partial.agency_new_field_modal')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script>
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var editData = "{{ route('field-master.edit', 'id') }}";
    var storeNewFieldData = "{{ route('store-agency-master') }}";
    var _FORM_GROUP_URL = "{{ url('get-form-groups') }}";
</script>
<script>
    function resetForm() {
        $("#addFieldForm")[0].reset();
        $('#field_error').html("");
        $('.form_group_id_error').html("");
    }

    $("#addFieldModal").on("hidden.bs.modal", function() {
        resetForm();
    });

    $("#closeBtn").on("click", function() {
        resetForm();
    });

    $(".close").on("click", function() {
        resetForm();
    });
    $(document).on("click", ".deleteAgencyMaster", function() {
        var id = $(this).attr('data-did');
        var agency_id = $(this).attr('data-aid');
        var form_id = $(this).attr('data-fid');
        deleteAgencyMaster(id, agency_id, form_id);
    });

    function capitalizeFirstLetter(string) {
        if (string != undefined) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        return '';

    }

    function deleteAgencyMaster(id, agency_id, form_id) {

        var upUrl = `{{ url('agency-master-delete') }}/${id}`;
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this Agency Master?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                $.ajax({

                    url: upUrl,
                    type: "POST",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'agency_id': agency_id,
                        'form_id': form_id
                    },
                    success: function(response) {
                        if (response.status) {
                            $("#" + id).remove();
                            // Recalculate order and update the database
                            updateSort('#sortableTable');
                            saveOrderToDatabase();
                            toastr.success(response.msg);
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

    $('#submitFormId').on('click', function(event) {
        event.preventDefault();

        var $submitButton = $(this);
        $submitButton.prop('disabled', true);

        var isChecked = false;
        var formGroupSelected = $('#form_group_id').val();

        $('input[name="field_id[]"]').each(function() {
            if ($(this).is(':checked')) {
                isChecked = true;
                return false;
            }
        });

        if (!formGroupSelected) {
            $('.form_group_id_error').html("Please select a form group.");
            $submitButton.prop('disabled', false);
            return false;
        } else {
            $('.form_group_id_error').html("");
        }

        if (!isChecked) {
            $('#field_error').html("Please select at least one field.");
            $submitButton.prop('disabled', false);
            return false;
        } else {
            $('#field_error').html("");
        }

        $.ajax({
            headers: {
                "X-CSRF-Token": $("meta[name=_token]").attr("content"),
            },
            url: storeNewFieldData,
            type: "POST",
            cache: false,
            data: $("#addFieldForm").serialize(),
            beforeSend: function() {},
            success: function(response) {
                $("#addFieldModal").modal("hide");
                $("#addFieldForm")[0].reset();
                $('#field_error').html("");
                $('.form_group_id_error').html("");
                var responseData = response.data;
                $(".hide-no-record").hide();

                responseData.forEach(function(responsee) {
                    var idLength = $(".viewAgencyMaster").length;

                    var appendRow = `
                    <tr class="form-list-classs" id="${responsee.field_id}">
                        <td><span id="rowIndex">${idLength + 1}</span></td>
                        <td>${(responsee.form_group && responsee.form_group.title ? responsee.form_group.title : '-')}</td>
                        <td>${capitalizeFirstLetter(responsee.fields.label)}</td>
                        <td>${capitalizeFirstLetter(responsee.fields.type)}</td>
                        <td>${capitalizeFirstLetter(responsee.fields.size)}</td>
                        <td>${(responsee.fields.set_character_limit ? responsee.fields.set_character_limit : '-')}</td>
                        <td>
                             <a href="javascript:void(0);" class="pull-left ml-1 viewAgencyMaster" data-eid="${responsee.field_id}" data-aid="${responsee.agency_id}" data-name="${responsee.fields.label}" data-fid="${responsee.form_id}" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="javascript:void(0);" class="pull-left ml-1 deleteAgencyMaster" data-did="${responsee.field_id}" data-aid="${responsee.agency_id}" data-fid="${responsee.form_id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </a>
                            <input class="sortID" type="hidden" name="sortID[]" value="${responseData.sort_id}" />
                            <input class="formFieldsID" type="hidden" name="formFieldsID[]" value="${responsee.field_id}" />
                        </td>
                    </tr>`;
                    $("#refreshDiv").append(appendRow);
                });

                toastr.success(response.msg);
                $submitButton.prop('disabled', false);

            },

            error: function(error) {
                $submitButton.prop('disabled', false);
                toastr.error(error.responseJSON.errors);
            }
        });
    });


    function getFormGroupDataNewField() {
        var form_id = $('#form_id').val();

        $.ajax({
            url: _FORM_GROUP_URL,
            type: 'GET',
            dataType: 'json',
            data: {
                form_id: form_id,
            },
            success: function(data) {
                let formGroupSelect = $('#form_group_id');
                formGroupSelect.empty();
                formGroupSelect.append('<option value="">Select Type</option>');

                $.each(data, function(key, formGroup) {
                    formGroupSelect.append('<option value="' + formGroup.id + '">' + formGroup
                        .title + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching form groups: ", error);
            }
        });
    }
</script>
