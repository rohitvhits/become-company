<div class="modal fade" id="ratecardModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    style="display: none; z-index:1050 !important" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Rate Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/rate-card/store'); ?>" method="post" id="rate_card_form" name="rate_card_form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Services <span style="color:red;">*</span></label>
                                    <select class="js-example-basic-single w-100" name="add_service_id" id="add_service_id">
                                        <option value="" > Select Services </option>
                                            @foreach ($serviceList as $service) 
                                                <option value="{{$service->id}}">{{$service->name}} ({{$service->types}})</option>
                                            @endforeach
                                    </select>
                                    <span id="service_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount <span style="color:red;">*</span></label>
                                    <input type="text" name="amount" class="form-control" id="amount" value="{{ old('amount') }}" placeholder="Enter Amount" onkeypress="return isNumber(event)">
                                    <span class="error" id="amount_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="save()" id="rateCardSave">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
