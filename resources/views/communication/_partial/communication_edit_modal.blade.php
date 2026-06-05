<div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Update Announcements</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form  method="post" id="eventEdit" name="eventEdit" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="form-group">
                        <label>Title <span style="color:red;">*</span></label>
                        <input type="text" name="title" class="form-control" id="event_edit_title" value="{{ old('title') }}">
                        <span class="error" id="title_error"></span>
                    </div>
                    <div class="form-group">
                        <label>Content <span style="color:red;">*</span></label>
                        <textarea type="text" name="content" class="form-control"  rows="10" cols="50" id="event_edit_content" value="{{ old('content') }}">{{ old('content') }}</textarea>
                        <span class="error" id="content_error"></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date <span style="color:red;">*</span></label>
                                <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select Start Date" id="event_edit_start_date" name="start_date" value="<?php echo old('start_date'); ?>" readonly>
                                <span id="start_date_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date <span style="color:red;">*</span></label>
                                <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select End Date" id="event_edit_end_date" name="end_date" value="<?php echo old('end_date'); ?>" readonly>
                                <span id="end_date_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                    
                    
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                           <div class="form-group">
                           <label for="message-text" class="col-form-label">Image:</label><br>
                                <input type="file" id="event_edit_event_image" name="image">
                                <span class="error mt-2" id="image_error" for="image"></span>
                           </div>
                                
                         
                        </div>
                        <div class="col-md-4">
                            <div id="imageDiv"></div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderEditEvent" style="display:none">
                    <button type="button" class="btn btn-success" onclick="update();" id="eventUpdate">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $('#event_edit_start_date').datepicker({
        startDate:new Date()
    });
    $('#event_edit_end_date').datepicker({
        startDate:new Date()
    });
</script>