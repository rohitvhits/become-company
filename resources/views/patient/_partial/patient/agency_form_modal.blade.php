<style>
    .table-check {
        padding-left: 10px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    td.row_td {
        padding: 0 5px 0px 5px;
        padding-left: 25px;
    }

    .options-group {
        display: none;
    }

    .modal-header {
        background-color: #f7f7f7;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
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
</style>

<div class="modal fade" id="addAgencyFormModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">Add Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <form class="forms-sample" name="adduser" action="" method="post" id="agencyFormAdd"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body pl-0">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="eid" value="{{ $record->id }}">
                                <input type="hidden" name="eidc" value="{{ $record->patient_code }}">
                                <input type="hidden" name="receipt_name" value="{{ $record->first_name . ' ' . $record->last_name }}">
                                <input type="hidden" name="type" value="caregiver">
                                <input type="hidden" name="agency_id" id="agency_id" value="{{ $record->agency_id }}">
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ $record->id }}">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Form Name<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control select_class select2" id="f_id" name="f_id">
                                            <option value="" selected>Select Form</option>
                                            @foreach ($agencyAllFormList as $agencyAllForm)
                                                <option value="{{ $agencyAllForm->id }}">
                                                    {{ ucfirst($agencyAllForm->title) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 form_id_error" style="color:red">
                                            @error('form_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="addAgencyForm" data-uid="">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


