<div class="modal fade" id="ebookModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    style="display: none; z-index:1050 !important" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Ebook</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/ebook/store'); ?>" method="post" id="ebook" name="ebook" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title <span style="color:red;">*</span></label>
                                    <input type="text" name="title" class="form-control" id="ebook_title" value="{{ old('title') }}" placeholder="Enter Title">
                                    <span class="error" id="title_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type <span style="color:red;">*</span></label>
                                    <select class="js-example-basic-multiple w-100" multiple="multiple" name="type[]" id="ebook_type">
                                        <option value="" > Select Type </option>
                                        <option value="0" @if(old('content') == '0') selected @endif> All </option>
                                        <option value="1" @if(old('content') == '1') selected @endif> Super Admin </option>
                                        <option value="2" @if(old('content') == '2') selected @endif> NyBest User </option>
                                        <option value="3" @if(old('content') == '3') selected @endif> Agency User </option>
                                    </select>
                                    <span id="type_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Video Upload:<span style="color:red;">*</span></label>
                                    <input type="file" class="form-control" id="ebook_video" name="video">
                                    <span class="error mt-2" id="video_error" for="image"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description  <span style="color:red;">*</span></label>
                                    <textarea id="ebook_content" name="content">
                                        {{ old('content') }}
                                    </textarea>
                                    <span class="error" id="content_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderAddEbook" style="display:none">
                    <button type="button" class="btn btn-success" onclick="save()" id="ebookSave">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
