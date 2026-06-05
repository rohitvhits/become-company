<div class="modal fade" id="eventMasterModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Popup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/event-master/store'); ?>" method="post" id="eventMaster" name="eventMaster" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title <span style="color:red;">*</span></label>
                        <input type="text" name="title" class="form-control" id="event_title" placeholder="Enter Title" value="{{ old('title') }}">
                        <span class="error" id="title_error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Content <span style="color:red;">*</span></label>
                        <textarea type="text" rows="4" cols="50" name="content" class="form-control" placeholder="Enter Description" rows="4" cols="50" id="event_content" value="{{ old('content') }}"></textarea>
                        <span class="error" id="content_error"></span>
                    </div>
                    
                   
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select Start Date" id="start_date" name="start_date" value="<?php echo old('start_date'); ?>" readonly>
                                        <span id="start_date_error" style="color:red" class="error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select End Date" id="end_date" name="end_date" value="<?php echo old('end_date'); ?>" readonly>
                                        <span id="end_date_error" style="color:red" class="error"></span>
                                    </div>
                                </div>
                            </div>
                            
                       
                    <div class="col-md-12 row">
                        <div class=" col-md-6 form-group" style="margin-left: -10px;">
                            <label for="message-text" class="col-form-label">Image:<span style="color:red;">*</span></label>
                            <input type="file"  id="event_image" name="image">
                            <span class="error mt-2" id="image_error" for="image"></span>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderAddEvent" style="display:none">
                    <button type="button" class="btn btn-success" onclick="save()" id="eventSave">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $("#start_date").datepicker({
        minDate: 0
    });
    $("#end_date").datepicker({
        minDate: 0
    });
</script>