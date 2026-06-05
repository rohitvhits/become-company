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
<!-- Rating Master Start -->
<div class="modal fade" id="addRatingMasterModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">Add Rating Master</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <form class="forms-sample" name="adduser" action="{{ route('rating-master.store') }}" method="post"
                    id="ratingMasterAdd" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" class="rating_master_id" value="">
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
                                    <label class="col-sm-3 col-form-label">Select Type<span
                                            class="error mt-2">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-user type-field col-sm-11 select2 select-class"
                                            id="type" name="type">
                                            <option value="">Select Type</option>
                                            <option value="rating">Rating</option>
                                            <option value="question">Question</option>
                                        </select>
                                        <span class="col-sm-11 ml-auto pl-0 mt-2 type_error" style="color:red">
                                            @error('type')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Is Text ?</label>
                                    <div class="col-sm-9">
                                        <input class="mt-2 is-text-field" type="checkbox" id="is_text" name="is_text" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="addRatingMaster" data-uid="">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Rating Master End -->

<script>
    var storeData = "{{ route('rating-master.store') }}";
    var _CSRF_TOKEN ='{{ csrf_token() }}';
</script>

<script src="{{ asset('assets/modulejs/ratingMaster.js') }}"></script>
