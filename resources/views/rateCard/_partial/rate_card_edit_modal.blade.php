<div class="modal fade" id="rateCardEditModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         style="display: none; z-index:1050 !important" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Update Rate Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/rate-card'); ?>" method="post" id="edit_rate_card_form" name="edit_rate_card_form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    
                    <input type="hidden" name="id" id="edit_id" value="">
                    <div class="col-md-12">
                        <div class="row">
                             <div class="col-md-12">
                                <div class="form-group">
                                    <label>Services <span style="color:red;">*</span></label>
                                    <select class="js-example-basic-single w-100" name="edit_service_id" id="edit_service_id">
                                        <option value="" > Select Services </option>
                                            @foreach ($serviceList as $service) 
                                                <option value="{{$service->id}}">{{$service->name}} ({{$service->types}})</option>
                                            @endforeach
                                    </select>
                                    <span id="service_edit_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount <span style="color:red;">*</span></label>
                                    <input type="text" name="amount" class="form-control" id="edit_amount" value="{{ old('amount') }}" onkeypress="return isNumber(event)">
                                    <span class="error" id="amount_edit_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="update();" id="rateCardUpdate">Update</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
                
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


