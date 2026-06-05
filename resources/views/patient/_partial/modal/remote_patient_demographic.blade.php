<style>
    #remote_patient_add_modal .modal-dialog .modal-content .modal-body{
        padding:8px 25px !important
    }
    #remote_patient_add_modal .change-form-group-css .form-group{
        margin-bottom:0.4rem !important
    }

    #remote_patient_add_modal .change-form-group-css .form-group label{
        margin-bottom:.0rem !important
    }

    #remote_patient_add_modal .modal-footer {
        padding: 12px 20px !important;
    }
    #remote_patient_add_modal .modal-header {
        padding: 8px 16px !important;
    }
    /* Button Styling */
    #remote_patient_add_modal #saveRemotePatientId {
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(80, 116, 170, 0.2);
    }

    #remote_patient_add_modal #saveRemotePatientId:hover {
        box-shadow: 0 4px 12px rgba(80, 116, 170, 0.4);
        transform: translateY(-2px);
    }

    #remote_patient_add_modal #saveRemotePatientId:active {
        transform: translateY(0);
    }

    #remote_patient_add_modal .btn-secondary {
        transition: all 0.3s ease;
    }

    #remote_patient_add_modal .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #remote_patient_add_modal .modal-header .close {
        opacity: 1;
        text-shadow: none;
    }

    #remote_patient_add_modal .modal-header .close:hover {
        opacity: 0.8;
    }

    /* Section Headers */
    #remote_patient_add_modal h5:not(.modal-title) {
        color: #000000;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(80, 116, 170, 0.2);
    }

    /* Form Control Focus */
    #remote_patient_add_modal .form-control:focus {
        border-color: #000000;
        box-shadow: 0 0 0 0.2rem rgba(80, 116, 170, 0.25);
    }

    /* HR Styling */
    #remote_patient_add_modal hr {
        border-top: 1px solid rgba(80, 116, 170, 0.15);
        margin: 1.5rem 0;
    }
</style>

<div class="modal fade" id="remote_patient_add_modal" aria-modal="true" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top:10px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #000000 0%, #000000 100%) !important">
                <h5 class="modal-title font-weight-bold" style="max-width:max-content;width:100%">
                    <i class="mdi mdi-account-plus mr-2"></i>Add Emmacare Demographic Detail
                </h5>

                <div class="pull-right ml-3">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="search-order-listing-loader1" alt="loader" id="searchLoader" style="display:none">
                </div>
                <button onclick="clearDemoModal()" type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_remote_demographic">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <input type="hidden" name="remote_ext_id" id="remote_ext_id">
                <input type="hidden" name="remote_id" id="remote_id">
                <input type="hidden" name="remote_agency_id" id="remote_agency_id">
                <div class="modal-body">
                    <div class="row change-form-group-css" >

                        <div class="col-md-3">
                            <div class="form-group ">
                                <label for="remote_patient_code" class="mb-0">Code</label>
                                <div class="row">
                                    <div class="col-sm-11">
                                        <div>
                                            <input type="text" name="remote_patient_code" value="" class="form-control form-control-sm" placeholder="Patient Code">

                                            <span id="remote_patient_code_error" class="error mt-2">{{ $errors->add_agency->first('remote_patient_code') }}</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_first_name" class="mb-0">First Name<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter First Name " id="remote_first_name" name="remote_first_name" value="{{ old('remote_first_name') }}">
                                <span id="remote_name_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_first_name') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_first_name" class="mb-0">Middle Name</label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Middle Name " id="remote_middle_name_id" name="remote_middle_name" value="{{ old('remote_middle_name') }}">
                                <span id="remote_middle_name_error" class="error mt-2">{{ $errors->add_agency->first('remote_middle_name') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_last_name" class="mb-0">Last Name<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Name " id="remote_last_name_id" name="remote_last_name" value="{{ old('remote_last_name') }}">
                                <span id="remote_last_name_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_last_name') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_dob" class="mb-0">Date of Birth<span class="text-danger mt-2">*</span></label>

                                <input type="date" class="form-control remote_bill_date form-control-sm " autocomplete="off" placeholder="Select  Date of Birth" id="remote_dob_id" name="remote_dob" value="{{ old('remote_dob') }}" min="1000-01-01" max="9999-12-31">
                                <span id="remote_dob_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_dob') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_mobile" class="mb-0">Phone<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Mobile" id="remote_mobile" onkeypress="return isNumber(event)" name="remote_mobile" value="{{ old('remote_mobile') }}" maxlength="15">
                                <span id="remote_mobile_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_mobile') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_gender" class="col-sm-3 mb-0">Gender<span class="text-danger mt-2">*</span></label>
                                <div  class="col-sm-9 row" style="margin-top:-10px">

                                    <div class="form-check mr-5">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="remote_msp" name="remote_gender" value="male" {{ old('remote_gender') == 'male' ? 'checked' : '' }}> Male <i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" id="remote_msp" name="remote_gender" value="female" {{ old('remote_gender') == 'female' ? 'checked' : '' }}> Female<i class="input-helper"></i></label>
                                    </div>
                                </div>
                                <span id="remote_address2_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_gender') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_language" class="mb-0">Language<span class="text-danger mt-2">*</span></label>

                                <select class="form-control form-control-sm" name="remote_language" id="remote_language_id">
                                    <option value="">Select Language</option>
                                    @foreach ($language_list as $language)
                                    <option value="{{ $language->name }}">{{ $language->name }}</option>
                                    @endforeach
                                </select>
                                <span id="remote_language_id_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_language') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_referral_source" class="mb-0">Referral Source<span class="text-danger mt-2">*</span></label>

                                <select class="form-control form-control-sm" name="remote_referral_source" id="remote_referral_source">
                                    <option value="">Select Referral Source</option>
                                    @foreach (Common::getRemoteReferralSourceId() as $key=>$rfl)
                                    <option value="{{ $key }}">{{ $rfl }}</option>
                                    @endforeach
                                </select>
                                <span id="remote_referral_source_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_referral_source') }}</span>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_insurance_name" class="mb-0">Insurance Type</label>
                                <select name="remote_insurance_name" id="remote_insurance_name" class="form-control form-control-sm">
                                        <option value="">Select Insurance Type</option>
                                </select>
                                <span id="remote_insurance_name_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_insurance_name') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_insurance_id" class="mb-0">Insurance ID</label>

                                <input type="text" id="remote_insurance_id" class="form-control form-control-sm" autocomplete="off" placeholder="Enter Insurance ID" name="remote_insurance_id" value="{{ old('remote_insurance_id')}}">
                                <span id="remote_insurance_id_error" class="error_html error mt-2"><?php echo $errors->add_agency->first('remote_insurance_id'); ?></span>
                            </div>
                        </div>
                        @php
                        $bestDayToCall = ['Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thusday'=>4,'Friday'=>5,'Other'=>"Other"]
                        @endphp
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_bestday_to_call" class="mb-0">Best Day To Call</label>

                                <select class="form-control form-control-sm js-example-basic-multiple select2" name="remote_bestday_to_call[]" id="remote_bestday_to_call" multiple>
                                    <option value="">Select Best Day To Call</option>
                                    @foreach ($bestDayToCall as $bkey=>$btd)
                                    <option value="{{ $btd }}">{{ $bkey }}</option>
                                    @endforeach
                                </select>
                                <span id="remote_bestday_to_call_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_bestday_to_call') }}</span>
                            </div>
                        </div>

                        @php
                        $bestTimeToCall = ['9am-12pm','12pm-2pm','2pm-5pm','Other'=>"Other"]
                        @endphp
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_best_time" class="mb-0">Best Time To Call</label>

                                <select class="form-control form-control-sm js-example-basic-multiple select2" name="remote_best_time[]" id="remote_best_time" multiple >
                                    <option value="">Select Best Time To Call</option>
                                    @foreach ($bestTimeToCall as $bttc)
                                    <option value="{{ $bttc }}">{{ $bttc }}</option>
                                    @endforeach
                                </select>
                                <span id="remote_best_time_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_best_time') }}</span>
                            </div>
                        </div>
                        @php
                        $referredForArray = ['CCM'=>1,'RPM'=>2,'TELEMEDICINE'=>3,'RTM'=>4,'BHI'=>5,'APCM'=>6]
                        @endphp
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_referred_to_far" class="mb-0">Referred For</label>

                                <select class="form-control form-control-sm js-example-basic-multiple" name="remote_referred_to_far[]" id="remote_referred_to_far" multiple>
                                    <option value="">Select Referred For</option>
                                    @foreach ($referredForArray as $key=>$rfa)
                                    <option value="{{ $rfa }}">{{ $key }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remote_notes" class="mb-0">Notes</label>

                                <textarea class="form-control form-control-sm" placeholder="Notes" name="remote_notes" style="height: 50px;margin-top:-1px">{{ old('message')}}</textarea>

                            </div>
                        </div>
                       
                        <div class="col-md-3 hide" id="other_best_call_day">
                            <div class="form-group">
                                <label for="remote_best_call_day_other" class="mb-0">Best Call Day Other</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Best Call Day Other" id="remote_best_call_day_other" name="remote_best_call_day_other" value="{{ old('remote_best_call_day_other')}}">
                               
                            </div>
                        </div>
                        <div class="col-md-3 hide" id="other_best_time_to_call">
                            <div class="form-group">
                                <label for="remote_best_time_to_call_other" class="mb-0">Best Time To Call Other</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Best Time To Call Other" id="remote_best_time_to_call_other" name="remote_best_time_to_call_other" value="{{ old('remote_best_time_to_call_other')}}">
                               

                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="">
                        <h5>Add Address Details</h5>
                        <div class="row change-form-group-css">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_address" class="mb-0">Address<span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Address" id="remote_address" name="remote_address" value="{{ old('remote_address')}}">
                                    <span id="remote_address_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_address') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_city" class="mb-0">City<span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter City" id="remote_city" name="remote_city" value="{{ old('remote_city')}}">
                                    <span id="remote_city_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_city') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_state" class="mb-0">State<span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter State" id="remote_state" name="remote_state" value="{{ old('remote_state')}}">
                                    <span id="remote_state_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_state') }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_zip" class="mb-0">Zip<span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Zip" id="remote_zip" name="remote_zip" value="{{ old('remote_zip')}}">
                                    <span id="remote_zip_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_zip') }}
                                        </span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_type" class="mb-0">Type<span class="text-danger mt-2">*</span></label>
                                    <select name="remote_type" id="remote_type" class="form-control form-control-sm">
                                        <option value="">Select Type</option>
                                    </select>
                                    <span id="remote_type_error" class="error_html error mt-2">
                                    {{ $errors->add_agency->first('remote_type') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="">
                        <h5>Add Diagnoses</h5>
                        <div class="row change-form-group-css">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_icd10" class="mb-0">Diagnoses (ICD - 10) <span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Diagnoses (ICD - 10)" id="remote_icd10" name="remote_icd10" value="{{ old('remote_icd10')}}">
                                    <span id="remote_icd10_error" class="error_html error mt-2">
                                        {{ $errors->add_agency->first('remote_icd10') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @php
                        $prognosisForArray = ['Fair','Good','Guarded','Poor']
                        @endphp
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_prognosis" class="mb-0">Prognosis <span class="text-danger mt-2">*</span></label>
                                    <select class="form-control form-control-sm js-example-basic-multiple" name="remote_prognosis" id="remote_prognosis">
                                        <option value="">Select Prognosis</option>
                                        @foreach ($prognosisForArray as $prognosis)
                                        <option value="{{ $prognosis }}">{{ $prognosis }}</option>
                                        @endforeach
                                    </select>
                                    <span id="remote_prognosis_error" class="error_html error mt-2">
                                    {{ $errors->add_agency->first('remote_prognosis') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_start_date" class="mb-0">Start Date <span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Enter Start Date" id="remote_start_date" name="remote_start_date" data-inputmask="'alias': 'datetime', 'inputFormat': 'mm/dd/yyyy'"  value="{{ old('remote_start_date')}}">
                                    <span id="remote_start_date_error" class="error_html error mt-2">
                                    {{ $errors->add_agency->first('remote_start_date') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remote_end_date" class="mb-0">End Date <span class="text-danger mt-2">*</span></label>
                                    <input type="text" class="form-control form-control-sm" data-inputmask="'alias': 'datetime', 'inputFormat': 'mm/dd/yyyy'"  placeholder="Enter End Date" id="remote_end_date" name="remote_end_date" value="{{ old('remote_end_date')}}">
                                    <span id="remote_end_date_error" class="error_html error mt-2">{{ $errors->add_agency->first('remote_end_date') }}</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <hr>
                    <div class="">
                        <h5>Document Upload</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="upload_document" class="mb-0">Document<span class="error mt-2">*</span></label>
                                    <input type="file" class="form-control" id="upload_document" name="upload_document">
                                    <span id="remote_document_upload_error" class="error mt-2 error_html">{{ $errors->add_agency->first('upload_document')}}</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2 text-white" id="saveRemotePatientId">
                          
                            <span>Save</span>
                        </button>
                        <button type="button" onclick="clearDemoModal()" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                           
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>