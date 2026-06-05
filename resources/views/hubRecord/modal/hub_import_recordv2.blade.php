<div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Import CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="appps_id">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" name="adduser" method="post" id="formnew">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-checkbox" style="margin-bottom: 8px;">
                                    <label class="form-check-label" style="margin-left: 25px !important">
                                        <input type="radio" name="filetype" checked value="company_file"
                                            class="form-check-input">
                                        Company File
                                    </label>
                                    <label class="form-check-label" style="margin-left: 25px !important">
                                        <input type="radio" name="filetype" value="master_file"
                                            class="form-check-input">
                                        Master File
                                    </label>

                                </div>
                            </div>
                        </div>
                    </div>
                    @if($auth->agency_fk == '')
                    <div class="form-group" id="company_div">
                        <label for="message-text" class="col-form-label">Company<span
                                style="color:red">*</span>:</label>
                        <select name="agency_id" class="form-control" id="agency_ids">
                            <option value="">Select Company</option>
                            @if (count($agencyList) > 0)
                            @foreach ($agencyList as $vsl)
                            <option value="{{$vsl->id}}">{{$vsl->agency_name}}</option>
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="agency_error" for="file_name"></span>
                    </div>
                    @else
                    <input type="hidden" name="agency_id" value="{{$user->agency_fk}}">
                    @endif

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Upload CSV<span
                                style="color:red">*</span>:</label>
                        <input type="file" class="form-control" id="timeidnew" name="images">
                        <span class="error mt-2 text-danger" id="images_error" for="file_name"></span>
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Add / Deactivate records<span
                                style="color:red">*</span>:</label>
                        <select name="add_remove" class="form-control" id="add_remove">
                            <option value="">Select option</option>
                            <option value="add">Import Records</option>
                            <option value="add_remove">Import Records & Deactivate Others (Update if Exists)</option>
                            <option value="remove">Deactivate Records (Only If Matching)</option>

                        </select>
                        <span class="error mt-2 text-danger" id="add_remove_error" for="add_remove_name"></span>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">Unique Fields for Duplicate Check<span
                                style="color:red">*</span>:</label>
                        <p class="small text-muted">Select one or more fields to check for duplicate records:</p>
                        <div class="field-selection-group"
                            style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; background-color: #f9f9f9;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label" style="margin-left: 25px !important">
                                            <input type="checkbox" name="unique_fields[]" value="last_name"
                                                class="form-check-input">
                                            Last Name
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="first_name"
                                                class="form-check-input">
                                            First Name
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="middle_name"
                                                class="form-check-input">
                                            Middle Initial
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="dob"
                                                class="form-check-input">
                                            Birth Date
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="gender"
                                                class="form-check-input">
                                            Gender
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="email"
                                                class="form-check-input">
                                            Email Address
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="address1"
                                                class="form-check-input">
                                            Primary Address 1
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="address2"
                                                class="form-check-input">
                                            Primary Address 2
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="city"
                                                class="form-check-input">
                                            Primary City
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="state"
                                                class="form-check-input">
                                            Primary State
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="zip_code"
                                                class="form-check-input">
                                            Primary Zip Code
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="phone"
                                                class="form-check-input">
                                            Home Phone
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="mobile"
                                                class="form-check-input">
                                            Mobile Phone
                                        </label>
                                    </div>

                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="ssn"
                                                class="form-check-input">
                                            SSN
                                        </label>
                                    </div>

                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="work_contact"
                                                class="form-check-input">
                                            Work Contact
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="work_email"
                                                class="form-check-input">
                                            Work Email
                                        </label>
                                    </div>

                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="member_id"
                                                class="form-check-input">
                                            Member Id
                                        </label>
                                    </div>
                                    <div class="field-checkbox" style="margin-bottom: 8px;">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="unique_fields[]" value="employee_code"
                                                class="form-check-input">
                                            Employee Code
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="error mt-2 text-danger" id="unique_fields_error"></span>
                    </div>



                    <div class="form-group">
                        <p>Click here to download the <a id="sampleFileLink"
                                href="{{ URL::to('/hub_sample1.csv') }}">sample file.</a></p>
                    </div>
                </form>
                <div id="importLoader" style="display:none; padding:20px;">
                    <div class="shimmer-loader" style="height: 20px; width: 80%; margin: 0 auto 10px;"></div>
                    <div class="shimmer-loader" style="height: 20px; width: 90%; margin: 0 auto 10px;"></div>
                    <div class="shimmer-loader" style="height: 20px; width: 70%; margin: 0 auto;"></div>
                </div>
                <div id="importResponseMsg" style="display:none;text-align:center;padding:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="saveImport()" id="import-save" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>