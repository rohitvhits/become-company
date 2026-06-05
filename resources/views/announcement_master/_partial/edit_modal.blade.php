<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" method="post" id="announcementEditForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">

                    <div class="form-group">
                        <label for="edit_title" class="col-form-label">Title<span class="error">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" placeholder="Enter Title"
                            maxlength="255">
                        <span class="error-text edit_title_error error"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_description" class="col-form-label">Message<span class="error">*</span></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="5"
                            placeholder="Enter Message"></textarea>
                        <span class="error-text edit_description_error error"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_steps_summary" class="col-form-label"> Summary</label>
                        <textarea class="form-control" id="edit_steps_summary" name="steps_summary" rows="5"
                            placeholder="Enter  Summary"></textarea>
                        <span class="error-text edit_steps_summary_error error"></span>
                    </div>

                    <div class="form-group" id="existing-media-group" style="display:none;">
                        <label class="col-form-label">Existing Media</label>
                        <div id="existing-media" class="d-flex flex-wrap"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_media" class="col-form-label">Add More Photos/Videos</label>
                        <input type="file" class="form-control-file" id="edit_media" name="media[]"
                            accept="image/*,video/*" multiple>
                        <small class="form-text text-muted">You can upload multiple photos and videos.</small>
                        <span class="error-text edit_media_error error"></span>
                    </div>

                    <div id="edit-media-preview" class="form-group d-flex flex-wrap"></div>

                    <div class="modal-footer">
                        <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader"
                            id="loaderEdit" style="display:none">
                        <button type="button" class="btn btn-success" id="updateAnnouncement">Update</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>