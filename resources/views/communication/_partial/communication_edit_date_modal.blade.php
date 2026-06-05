<div class="modal fade" id="eventEditDateModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Activate the Popup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/event-master/'); ?>" method="post" id="eventPopupEdit" name="eventPopupEdit" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select Start Date" id="popup_edit_start_date" name="start_date" value="<?php echo old('start_date'); ?>" readonly>
                                <span id="popup_start_date_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="form-control bill_date datepicker" autocomplete="off" placeholder="Select End Date" id="popup_edit_end_date" name="end_date" value="<?php echo old('end_date'); ?>" readonly>
                                <span id="popup_end_date_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                    
                    
                    </div>
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderEditDateEvent" style="display:none">
                    <button type="button" class="btn btn-success" onclick="updateDate();" id="popupBtnUpdate">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="cancelDatePopup();">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $('#popup_edit_start_date').datepicker({
        minDate: 0
    });
    $('#popup_edit_end_date').datepicker({
        minDate: 0
    });
</script>