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
    .set-character-limit {
        display: none;
    }
    .modal-header {
        background-color: #f7f7f7;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
</style>
<!-- Field Master Start -->
<div class="modal fade" id="addFieldMasterModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">Add Field Master</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <form class="forms-sample" name="adduser" action="{{ route('field-master.store') }}" method="post"
                    id="fieldMasterAdd" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="agency_id" class="agency_id" id="agency_id" value="{{ isset($agency_id) ? $agency_id : '' }}">
                    <input type="hidden" name="form_id" class="form_id" id="form_id" value="{{ isset($form_id) ? $form_id : '' }}">
                    <input type="hidden" name="id" class="field_master_id" value="">
                    <div class="card-body pl-0">
                        <div class="row">
                            @if(isset($form_id) && $form_id !="")
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Select Form Group<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-user select-form-group-field col-sm-11 select2 select-class"
                                            id="form_group" name="form_group">
                                            <option value="">Select Form Group</option>
                                            
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 form_group_error" style="color:red">
                                            @error('form_group')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Label<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control charCls_new" placeholder="Enter Label"
                                            id="label" name="label" value="{{ old('label') }}">
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 label_error" style="color:red">
                                            @error('label')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Select Size<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-user col-sm-11 select2 select-class2"
                                            id="size" name="size">
                                            <option value="">Select Size</option>
                                            <option value="full">Full</option>
                                            <option value="half">Half</option>
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 size_error" style="color:red">
                                            @error('size')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Select Type<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-user type-field col-sm-11 select2 select-class"
                                            id="type" name="type">
                                            <option value="">Select Type</option>
                                            <option value="information">Information</option>
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="email">Email</option>
                                            <option value="date">Date</option>
                                            <option value="time">Time</option>
                                            {{-- <option value="file">File Upload</option> --}}
                                            {{-- <option value="checkbox">Checkbox Group</option> --}}
                                            {{-- <option value="radio">Radio Group</option> --}}
                                            <option value="select">Select</option>
                                            <option value="number">Number</option>
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 type_error" style="color:red">
                                            @error('type')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 set-character-limit">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Set Character Limit<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" placeholder="Enter Character Limit"
                                            id="set_character_limit" name="set_character_limit" min="1" step="1" value="{{ old('set_character_limit') }}">
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 set_character_limit_error" style="color:red">
                                            @error('set_character_limit')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 options-group" id="options-group">
                                <div class="form-group row">
                                    <label class="col-sm-3">Options<span
                                            class="error mt-2">*</span></label>
                                    <div class="optionDiv col-sm-9">
                                        <div class="row align-items-center">
                                            <div class="col-md-10">
                                                <input type="text" name="option[]"
                                                    class="form-control option option-field" value=""
                                                    placeholder="Enter Option">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-primary rowAdder"><i
                                                        class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <span class="text-danger option_error">{{ $errors->first('options.*') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="newInput"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Show in Portal</label>
                                    <div class="col-sm-9 d-flex align-items-center">
                                        <input type="checkbox" id="show_in_portal" name="show_in_portal" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="addFieldMaster" data-uid="">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Field Master End -->

<script>
    var storeData = "{{ route('field-master.store') }}";
</script>

<script src="{{ asset('assets/modulejs/fieldMaster.js') }}"></script>
