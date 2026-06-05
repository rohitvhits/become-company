<style>
    #other-complience-hha-update .modal-footer {
        padding: 4px 1px !important;
    }

	#other-complience-hha-update .modal-header{
		padding: 8px 16px !important;
	}

	#other-complience-hha-update .modal-header .close{
		padding: 0 !important;
		margin: 0 !important;
	}
	#other-complience-hha-update .select2-search--inline{
		width:100% !important;
	}
</style>

<div class="modal fade" id="other-complience-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content border-0 shadow-lg">
			<div class="modal-header text-white" style="background-color:#000000 !important">
				<h5 class="modal-title documens font-weight-bold" id="ModalLabel">
					<i class="mdi mdi-file-document-edit mr-2"></i>Update Other Compliance to HHX Document
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="hideOtherComplianceToHHXDocument()">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-other-complience-hha-document') }}" name="adduser" method="post" id="formnew-other-compienece-hha-update">
				<div class="modal-body p-4">

					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" id="document_request_complience_id" value="">
					<input type="hidden" name="record-id" id="document_complience_record_id" value="{{ $record->id }}">
					<input type="hidden" name="agencyId" id="document_complience_ids" value="{{ $record->agency_id }}">
					<div class="card mb-2 shadow-sm">
						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
							<strong class="small">HHA Section</strong>
						</div>
						<div class="card-body py-2 px-2">
							<div class="row mb-3">
								<div class="col-md-6">
									<div class="form-group mb-0">
										<label for="hha_document_complience_type_id" class="mb-0">
											HHX Document Type<span class="text-danger ml-1">*</span>
										</label>
										<select name="document_type" class="form-control" id="hha_document_complience_type_id"></select>
										<span id="hha_document_complience_type_id_error" class="text-danger small d-block mt-1 error"></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group mb-0">
												<label for="completed_date_complience"  class="mb-0">
													Date Performed<span class="text-danger ml-1">*</span>
												</label>
												<input type="text" name="completed_date" class="form-control" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" min="1000-01-01" max="9999-12-31" id="completed_date_complience" placeholder="Select date">
												<span id="complience_completed_date_error" class="text-danger small d-block mt-1 error"></span>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group mb-0">
												<label for="other_complience_due_date" class="mb-0">
													Due Date<span class="text-danger ml-1">*</span>
												</label>
												<input type="text" name="other_complience_due_date" class="form-control" id="other_complience_due_date"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" min="1000-01-01" max="9999-12-31" placeholder="Select date">
												<span id="other_complience_due_date_error" class="text-danger small d-block mt-1 error"></span>
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group form-check mt-4">
												<input type="checkbox" 
													class="form-check-input" 
													id="auto_update_next_due_date" 
													name="auto_update_next_due_date" 
													value="1">
												<label class="form-check-label" for="auto_update_next_due_date">
													Update Next Due Date Based on Performed Date
												</label>
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
							<strong class="small">Pending Other Compliance </strong><small class="form-text text-muted ml-2"><strong>(This will update Pending medical)</strong></small>
						</div>
						<div class="card-body py-2 px-2">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="hha_document_complience_id" class="mb-0">
											HHX Compliance Name<span class="text-danger ml-1">*</span>
										</label>
										<select name="document_other_complience_type[]" class="select2-design cal-padding-0 w-100 hha_complience_id" id="hha_document_complience_id" multiple></select>
										<span id="hha_document_complience_id_error" class="text-danger small d-block mt-1 error"></span>
									</div>
								</div>
							</div>
							<span class="row" id="multipleComplienceResultId" style="display:none"></span>
						</div>
						<div class="card-header bg-light d-flex align-items-center py-1 px-2">
							<input type="checkbox" name="show_new_other_compliance_need" id="show_new_other_compliance_need" value="1" class="mr-2"><strong class="small">New Other Compliance Needed </strong><small class="form-text text-muted ml-2"><strong>(This will create a new Other Compliance)</strong></small>
						</div>
						<div class="card-body py-2 px-2 hide" id="create_new_other_compliance_need">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="create_document_other_id" class="col-form-label small mb-1">Other Compliance<span style="color:red">*</span>:</label>
										<select name="create_document_other_compliance_type[]" class="form-control form-control-sm select2-design cal-padding-0 js-example-basic-multiple w-100 create_document_other_type" id="create_document_other_type" multiple="multiple" data-placeholder="Select Other Compliance" aria-describedby="create_document_other_type_error"></select>
										<small class="form-text text-muted">Type to add new Other Compliance items if not listed.</small>
										<span id="hha_document_other_id_error" style="color:red" class="error"></span>
									</div>
								</div>
							</div>

							<span class="row" id="createMultipleOtherComplianceResultId" style="display:none">
						
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer border-top-0 bg-light">
					<div class="d-flex justify-content-end align-items-center w-100">
						<button type="button" class="btn btn-success btn-sm px-4 mr-2" id="update-hha-complience-id">
						<span class="spinner-border spinner-border-sm d-none" id="create-hha-complience" role="status" aria-hidden="true"></span>
						<span id="btn-save-text-hha-complience">Save</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="hideOtherComplianceToHHXDocument()">
							Close
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
