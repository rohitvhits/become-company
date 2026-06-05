<div class="modal fade" id="branchLinkModal" tabindex="-1" role="dialog" aria-labelledby="branchLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color:#0f0f17 !important">
                <h5 class="modal-title" id="branchLinkModalLabel">Add Branch Link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white !important;">&times;</span>
                </button>
            </div>
            <form id="branchLinkForm">
                @csrf
                <input type="hidden" id="branch_link_id" name="branch_link_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="link_branch_id">Branch <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="link_branch_id" name="branch_id" style="width:100%">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="branch_id-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="link_agency_ids">Agencies <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="link_agency_ids" name="agency_ids[]" multiple="multiple" style="width:100%">
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="agency_ids-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="link_service_ids">Services <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="link_service_ids" name="service_ids[]" multiple="multiple" style="width:100%">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->types }}) </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="service_ids-error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="submit" class="btn btn-success btn-sm px-4 mr-2" id="submitLinkBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text submit-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
