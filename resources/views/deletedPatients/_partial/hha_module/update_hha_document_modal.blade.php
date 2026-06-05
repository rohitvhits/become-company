<div class="modal fade" id="exampleModal-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title documens" id="ModalLabel">Update to HHX Document</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearData()">
						<span aria-hidden="true">&times;</span>
					</button>
				</div> 
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-hha-document') }}" name="adduser" method="post" id="formnew-hha-update">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="id" id="document_request_id" value="">
						<input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
						<input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">
						
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                    <select name="document_type" class="form-control" id="hha_document_type_id" ></select>
                                    <span id="hha_document_type_id_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Medical Name<span style="color:red">*</span>:</label>
                                    <select name="document_medical_type[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100 upload-hhax" id="hha_document_medical_id"    multiple="multiple"></select>
                                    <span id="hha_document_medical_id_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                            
                        </div>
                        
						

						
                        <span class="row" id="multipleMedicalResultId" style="display:none">
                            
                        </span>
                       
						<div class="form-group">
							<label for="recipient-name" class="col-form-label"> Date Performed<span style="color:red">*</span>:</label>
							<input type="text" name="completed_date" class="form-control perforrm-datepicker" id="completed_date">
							<span id="completed_date_error" style="color:red" class="error"></span>
						</div>
                        <div id="hha_due_date_div" style="display:none">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Due Date:</label>
                                   <input type="text" name="hha_due_date" id="hha_due_date" class="form-control">
                                    <span id="hha_due_date_div_error" style="color:red" class="error"></span>
                                </div>
                            </div>

						<div class="modal-footer">
							<button type="button" class="btn btn-success" id="update-hha-document-id">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearData()">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div> 
	</div>