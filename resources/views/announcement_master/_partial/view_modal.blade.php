<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Message:</strong></label>
                    <div id="view_description"></div>
                </div>

                <div class="form-group" id="view_steps_group" style="display:none;">
                    <label><strong> Summary:</strong></label>
                    <div id="view_steps_summary"></div>
                </div>

                <div class="form-group" id="view_media_group" style="display:none;">
                    <label><strong>Media:</strong></label>
                    <div id="view_media" class="d-flex flex-wrap"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>