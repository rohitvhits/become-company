<style>
#exampleModal-hha-update .modal-header .closeUpdateHHAX {
    padding: 1rem 1rem;
    margin: -20px -25px -20px auto;
}

#exampleModal-hha-update button.closeUpdateHHAX {
    padding: 0;
    background-color: transparent;
    border: 0;
}

#exampleModal-hha-update .closeUpdateHHAX {
    float: right;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: .5;
}
	</style>
<div class="modal fade" id="exampleModal-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title documens" id="ModalLabel">Update HHA Document</h5>
				<button type="button" class="closeUpdateHHAX" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-hha-document') }}" name="adduser" method="post" id="formnew-hha-update">
				<div class="modal-body" style="height:calc(100vh - 250px)">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" id="document_request_id" value="">
					<input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
					<input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">

					<div class="card mb-2 shadow-sm">
						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
							<strong class="small">HHA Section</strong>
						</div>
						<div class="card-body py-2 px-2">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="hha_document_type_id" class="col-form-label">HHA Document Type<span style="color:red">*</span>:</label>
										<select name="document_type" class="form-control" id="hha_document_type_id" data-placeholder="Select document type" aria-describedby="hha_document_type_id_error" required></select>
										<span id="hha_document_type_id_error" style="color:red" class="error"></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
										<label for="completed_date" class="col-form-label">Date Performed<span style="color:red">*</span>:</label>
										<div class="input-group">
											<input type="text" name="completed_date" class="form-control" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" id="completed_date" placeholder="MM/DD/YYYY" aria-describedby="completed_date_error" required>
											
										</div>
												<span id="completed_date_error" style="color:red" class="error"></span>
											</div>
										</div>
										<div class="col-md-6" id="hha_due_date_div" style="display:none">
											<div class="form-group">
												<label for="hha_due_date" class="col-form-label">Due Date:</label>
												<div class="input-group">
													<input type="text" name="hha_due_date" id="hha_due_date" class="form-control" placeholder="MM/DD/YYYY" aria-describedby="hha_due_date_div_error">
													<div class="input-group-append">
														<span class="input-group-text"><i class="ti-calendar"></i></span>
													</div>
												</div>
												<span id="hha_due_date_div_error" style="color:red" class="error"></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
							<strong class="small">Pending HHA Medical </strong><small class="form-text text-muted ml-2"><strong>(This will update Pending medical)</strong></small>
						</div>
						<div class="card-body py-2 px-2">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="hha_document_medical_id" class="col-form-label">HHA Medical Name<span style="color:red">*</span>:</label>
										<select name="document_medical_type[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100 upload-hhax" id="hha_document_medical_id" multiple="multiple" data-placeholder="Select one or more medicals" aria-describedby="hha_document_medical_id_error" required></select>
										<span id="pending_hha_document_medical_id_error" style="color:red" class="error"></span>
									</div>
								</div>
							</div>
							<span class="row" id="multipleMedicalResultId" style="display:none"></span>
						</div>

						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
						<input type="checkbox" name="show_new_medical_need" id="show_new_medical_need" value="1" class="mr-2"><strong class="small">New Medical Needed </strong><small class="form-text text-muted ml-2"><strong>(This will create a new medical)</strong></small>
                        </div>
						<div class="card-body py-2 px-2 hide" id="create_new_medical_need">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="create_document_medical_id" class="col-form-label small mb-1">Medical Name<span style="color:red">*</span>:</label>
										<select name="create_document_medical_type[]" class="form-control form-control-sm select2-design cal-padding-0 js-example-basic-multiple w-100 create-upload-hhax" id="create_document_medical_id" multiple="multiple" data-placeholder="Select Medical Name" aria-describedby="create_document_medical_type_error"></select>
										<small class="form-text text-muted">Type to add new medical items if not listed.</small>
										<span id="hha_document_medical_id_error" style="color:red" class="error"></span>
									</div>
								</div>
							</div>

							<span class="row" id="createMultipleMedicalResultId" style="display:none">
						
							</span>
                        </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="update-hha-document-id">Save</button>
					<button type="button" class="btn btn-light" data-dismiss="modal"  onclick="ClearUpdateHHXData()">Close</button>
				</div>
			</form>
		
		</div>
	</div>
</div>