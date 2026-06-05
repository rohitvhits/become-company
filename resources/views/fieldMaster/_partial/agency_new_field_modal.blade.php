<style>
    .check-class{
        margin-left: 2px;
    }
    .label-class{
        margin-left: 24px;
    }
</style>
<!-- Agency New Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1" role="dialog" aria-labelledby="addFieldModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="addFieldModalLabel">Add New Field</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">

            <form id="addFieldForm" action="" method="POST">
                @csrf
                <input type="hidden" name="agency_id" id="agency_id" value="{{ $agency_id }}">
                <input type="hidden" name="form_id" id="form_id" value="{{ $form_id ?? '' }}">
               
                <div class="modal-body">
                    @if(isset($form_id) && $form_id !="")
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><b>Select Form Group</b><span
                                class="error mt-2">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-user select-form-group-field col-sm-11 select2 select-class"
                                id="form_group_id" name="form_group_id">
                                <option value="">Select Type</option>
                                
                            </select>
                            <span class="col-sm-11 ml-auto pl-0 mt-2 form_group_id_error" style="color:red">
                                @error('form_group_id')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-form-label"><b>Field Name</b></label>
                        <div class="row">
                            @foreach ($fieldMasterData as $field)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input check-class"
                                            id="field_{{ $field->id }}" name="field_id[]"
                                            value="{{ $field->id }}">
                                        <label for="field_{{ $field->id }}" class="label-class">{{ $field->label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <span class="text-danger" id="field_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitFormId">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" id="closeBtn">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>