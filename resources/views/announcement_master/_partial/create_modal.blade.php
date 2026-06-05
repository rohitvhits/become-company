<div class="modal fade" id="createAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" method="post" id="announcementCreateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title" class="col-form-label">Title<span class="error">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title"
                            maxlength="255">
                        <span class="error-text title_error error"></span>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-form-label">Message<span class="error">*</span></label>
                        <textarea class="form-control" id="description" name="description" 
                            placeholder="Enter Message"></textarea>
                        <span class="error-text description_error error"></span>
                    </div>

                    <div class="form-group">
                        <label for="steps_summary" class="col-form-label">Summary</label>
                        <textarea class="form-control" id="steps_summary" name="steps_summary" 
                            placeholder="Enter Summary (Optional)"></textarea>
                        <span class="error-text steps_summary_error error"></span>
                    </div>

                    <div class="form-group">
                        <label for="media" class="col-form-label">Photos/Videos</label>
                        <input type="file" class="form-control-file" id="media" name="media[]" accept="image/*,video/*"
                            multiple>
                        <small class="form-text text-muted">You can upload multiple photos and videos.</small>
                        <span class="error-text media_error error"></span>
                    </div>

                    <div id="media-preview" class="form-group d-flex flex-wrap"></div>

                    <div class="modal-footer">
                        <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader"
                            id="loaderCreate" style="display:none">
                        <button type="button" class="btn btn-success" id="saveAnnouncement">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>