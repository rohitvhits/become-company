<div class="modal fade" id="show_link_patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Link Appointment</h5>
                <button type="button" class="close" id="close-modal-patient" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='' name="add-appointment" method="post" id="form_link_patient_reset">
                <div class="modal-body">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="show_modal_id" id="show_modal_id" value="">
                    <input type="hidden" name="agencyId" id="agencyId" value="">
                    <div class="title  mb-3">
                        <h5>Basic Detail</h5>
                    </div>
                    <div class="row" id="hr2">

                        <div class="col-md-3">
                            <div class="box">
                                <dt>Agency Name</dt>
                                <dd class="agencyName"></dd>
                                <dt>Email</dt>
                                <dd class="email"></dd>
                                <dt>Date of Birth</dt>
                                <dd class="dobId"></dd>
                                <dt>Language</dt>
                                <dd class="language"></dd>
                                <dt>Emergency Contact Name</dt>
                                <dd class="emergency_contact_name"></dd>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="box">
                                <dt>Full Name</dt>
                                <dd class="firstnName"></dd>
                                <dt>Full Address</dt>
                                <dd class="full_address"></dd>
                                <dt>Mobile</dt>
                                <dd class="mobile"></dd>
                                <dt>Created Date</dt>
                                <dd class="created_date"></dd>
                                <dt>CIN /Medicaid Number</dt>
                                <dd class="cin"></dd>
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="box">
                                <dt>Gender</dt>
                                <dd class="gender">N/A</dd>
                                
                                <dt>Type</dt>
                                <dd class="patient_type"></dd>
                                <dt>Services</dt>
                                <dd class="serviceName"></dd>
                                <dt>SSN</dt>
                                <dd class="ssn_no"></dd>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="box">
                                <dt>Phone</dt>
                                <dd class="phone"></dd>
                                <dt>Discipline</dt>
                                <dd class="discipline"></dd>
                                <dt>Status</dt>
                                <dd class="statusid"></dd>
                                <dt>Emergency Contact No</dt>
                                <dd class="emergency_contact_no"></dd>
                            </div>
                        </div>
                        
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name" class="col-form-label">Search<span class="error">*</span>:</label><br>
                                        <input type="text" class="form-control search_patient" name="search_patient" id="search_patient" placeholder="Search Patient (Id,First Name,Last Name,Mobile No)"><br>
                                    
                                        <span class="search_patient_error error"></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">

                        <table class="table table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Record Id</th>

                                <th>Full Name</th>
                                <th>Date of Birth</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody id="existing_patient_record_id">
                                <tr>
                                    <td colspan="4">No record available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->



                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-light" id="linkToPatientVisitModal">Link</button>
                </div>
            </form>
        </div>
    </div>
</div>