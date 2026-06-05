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
        /* padding: 0.5rem 1rem;
        border-radius: 4px; */
    }

    #sortableTable {
        tbody tr:hover {
            cursor: grab;
        }
    }
</style>
<div class="">
    <div class="">
        <div class=" mb-4">
            <div class="">
                <div class="table-responsive">
                    <table id="sortableTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Character Limit</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="refreshDiv">
                            <?php
                            $i = ($page != "") ? ($page * 10) - 9 : 1;
                            foreach ($formFields as $key => $row) {  ?>
                            <tr class="field-list-class" id="<?php echo $row->id; ?>">

                                <td><span id="rowIndex"><?= $key + 1 ?></span></td>
                                <td><?php echo ucfirst($row->label); ?></td>
                                <td><?php echo ucfirst($row->type); ?></td>
                                <td><?php echo ucfirst($row->size); ?></td>
                                <td><?php echo $row->set_character_limit ?? '-'; ?></td>
                                <td>
                                    @can('agency-create-form-show')
                                        <a href="javascript:void(0);" data-eid="<?php echo $row->id; ?>"
                                            data-aid="<?php echo $agency_id; ?>" data-name="<?php echo $row->label; ?>"
                                            class="pull-left ml-1 viewAgencyMaster">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan

                                    @can('agency-create-form-delete')
                                        <a class="pull-left ml-1 deleteAgencyMaster" href="javascript:void(0)"
                                            data-did="<?php echo $row->id; ?>" data-aid="<?php echo $agency_id; ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endcan
                                    <input class="sortID" type="hidden" name="sortID[]" value="" />
                                    <input class="formFieldsID" type="hidden" name="formFieldsID[]"
                                        value="{{ $row->id }}" />
                                    <input class="agencyId" type="hidden" name="agencyId[]"
                                        value="{{ $agency_id }}" />
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


<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script>
    var _FORM_GROUP_URL = "{{ url('get-form-groups') }}";
</script>
<script>
    // Sortable rows, helps maintain column widths a little better
    var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width());
        });
        return $helper;
    };

    var sortArray = [];

    function updateSort(table) {
        sortArray = [];

        $(table + ' tbody tr').each(function() {
            var row_index = $(this).index() + 1;
            var formFieldsID = $(this).find('.formFieldsID').val();
            var agencyId = $(this).find('.agencyId').val();

            $(this).find('span').text(row_index);
            $(this).find('.sortID').val(row_index);

            sortArray.push({
                id: formFieldsID,
                order: row_index,
                agencyId: agencyId
            });
        });
        return sortArray;
    }

    $(function() {
        $("#sortableTable tbody").sortable({
                helper: fixHelperModified,
                update: function(event, ui) {
                    updateSort('#sortableTable');
                    saveOrderToDatabase();
                }
            })
            .disableSelection();
    });

    function saveOrderToDatabase() {
        $.ajax({
            url: "/update-agencymaster-order",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                sortOrder: sortArray
            },
            success: function(response) {
                $(".successfully-saved").css("display", "block").delay(2000).fadeOut(400);
            },
            error: function(xhr) {
                console.error("Error updating sort order:", xhr.responseText);
            }
        });
    }
</script>
