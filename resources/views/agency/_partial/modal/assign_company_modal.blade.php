<div class="modal fade" id="company-config-modal" tabindex="-1" role="dialog" aria-labelledby="companyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyModalLabel">Assign Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Company Name</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="modal_domain_config_id">
                            <option value="">-- Select Company --</option>
                            @foreach($domainConfigs as $dc)
                                <option value="{{ $dc->id }}" {{ $agencyDetails->domain_config_id == $dc->id ? 'selected' : '' }}>
                                    {{ $dc->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveCompanyConfig" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
