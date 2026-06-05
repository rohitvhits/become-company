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

    .agency-dropdown {
        display: none;
    }

    .modal-header {
        background-color: #f7f7f7;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .radio_error {
        color:red; 
        margin-left:110px;
    }
</style>
<!-- Field Master Start -->
<div class="modal fade" id="addFormSetupModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">Add Form Setup</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <form class="forms-sample" name="adduser" action="{{ route('form-setup.store') }}" method="post"
                    id="formSetupAdd" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" class="form_setup_id" value="">
                    <div class="card-body pl-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Title<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control charCls_new" placeholder="Enter Title"
                                            id="title" name="title" value="{{ old('title') }}">
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 title_error" style="color:red">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Is This Default? <span class="error mt-2">*</span></label>
                                    <div class="col-sm-9 d-flex align-items-center">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_default_yes" name="is_default" value="1" {{ old('is_default') == '1' ? 'checked' : '' }}>
                                            Yes
                                        </div>
                                        <div class="form-check form-check-inline ml-3">
                                            <input type="radio" class="form-check-input" id="is_default_no" name="is_default" value="0" {{ old('is_default') == '0' ? 'checked' : '' }}>
                                            No
                                        </div>
                                    </div>
                                    <span class="col-sm-12 is_default_error radio_error">
                                        @error('is_default')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 agency-dropdown" id="agency-dropdown">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Select Agency<span
                                        class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-user agency-field col-sm-11 select2"
                                            id="agency" name="agency">
                                            <option value="">Select Agency</option>
                                            @foreach ($agencyList as $agency)
                                            <option value="{{ $agency->id }}">
                                            {{ $agency->agency_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 agency_error" style="color:red">
                                            @error('agency_error')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Form Type<span class="error mt-2">*</span></label>
                                    <div class="col-sm-9 d-flex align-items-center">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="form_type_patient" name="form_type" value="1" {{ old('form_type') == '1' ? 'checked' : '' }}>
                                            Patient
                                        </div>
                                        <div class="form-check form-check-inline ml-3">
                                            <input type="radio" class="form-check-input" id="form_type_cargiver" name="form_type" value="0" {{ old('form_type') == '0' ? 'checked' : '' }}>
                                            Caregiver
                                        </div>
                                    </div>
                                    <span class="col-sm-12 form_type_error radio_error">
                                        @error('form_type')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="addFormSetup" data-uid="">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Field Master End -->

<script>
    var storeData = "{{ route('form-setup.store') }}";
</script>

<script src="{{ asset('assets/modulejs/formSetup.js') }}?time={{ env('timestamp')}}"></script>
