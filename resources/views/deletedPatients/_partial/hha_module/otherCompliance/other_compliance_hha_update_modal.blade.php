<div class="modal fade" id="other-complience-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title documens" id="ModalLabel">Update Other Complience to HHX Document</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="hideOtherComplianceToHHXDocument()">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-other-complience-hha-document') }}" name="adduser" method="post" id="formnew-other-compienece-hha-update">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="id" id="document_request_complience_id" value="">
						<input type="hidden" name="record-id" id="document_complience_record_id" value="{{ $record->id }}">
						<input type="hidden" name="agencyId" id="document_complience_ids" value="{{ $record->agency_id }}">
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                    <select name="document_type" class="form-control" id="hha_document_complience_type_id" ></select>
                                    <span id="hha_document_complience_type_id_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Complience Name<span style="color:red">*</span>:</label>
                                    <select name="document_medical_type[]" class="select2-design cal-padding-0 js-example-basic-multiple w-100 hha_complience_id" id="hha_document_complience_id" multiple></select>
                                    <span id="hha_document_complience_id_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                        </div>
                        

						
                        <span class="row" id="multipleComplienceResultId" style="display:none">
                            
                        </span>
						

						<div class="form-group">
							<label for="recipient-name" class="col-form-label"> Date Performed<span style="color:red">*</span>:</label>
							<input type="text" name="completed_date" class="form-control perforrm-datepicker" id="completed_date_complience">
							<span id="complience_completed_date_error" style="color:red" class="error"></span>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-success" id="update-hha-complience-id">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal" onclick="hideOtherComplianceToHHXDocument()">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div> 
	</div>