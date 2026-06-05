<div class="modal fade" id="exampleModal-hha-update-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title documens" id="ModalLabel">Update to HHX Document</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearData()">
						<span aria-hidden="true">&times;</span>
					</button>
				</div> 
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post" id="update-hha-document-patient">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="id" id="main_id" value="">
						<input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
						<input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">
						
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                    <select name="document_type" class="form-control" id="hha_patient_document_type_id" ></select>
                                    <span id="doc_error" style="color:red" class="error"></span>
                                </div>
                            </div>
                           
                        </div>
                        
						
						<div class="modal-footer">
							<button type="button" class="btn btn-success" id="update-hha-document-patient-btn">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearDataHHA()">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div> 
	</div>