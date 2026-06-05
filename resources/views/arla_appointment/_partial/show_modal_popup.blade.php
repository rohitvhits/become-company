<div class="modal fade" id="show_modal_popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Appointment</h5>
                    <button type="button" class="close" id="close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="add-appointment" method="post" id="submitId">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="appointment_id" id="modal_appointment_ids" value="">
                       
                    <div class="row" id="hr2">

                        <div class="col-md-4">
                            <div class="box">

                                <dl class="dl-horizontal">
                                    <dt>Agency Name</dt>
                                    <dd class="agencyName"></dd>
                                    <dt>Date of Birth</dt>
                                    <dd class="dobId"></dd>

                                    <dt>Gender</dt>
                                    <dd class="gender">N/A</dd>
                                    <dt>Discipline</dt>
                                    <dd class="discipline"></dd>
                                    <dt>Status</dt>
                                    <dd class="statusid"></dd>
                                    <dt>Emergency Contact Name</dt>
                                    <dd class="emergency_contact_name"></dd>
                                </dl>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="box">

                                <dl class="dl-horizontal">
                                    <dt>Full Name</dt>
                                    <dd class="firstnName"></dd>

                                    <dt>Email</dt>
                                    <dd class="email"></dd>
                                    <dt>Full Address</dt>
                                    <dd class="full_address"></dd>
                                    <dt>Appointment Type</dt>
                                    <dd class="patient_type"></dd>
                                    <dt>CIN /Medicaid Number</dt>
                                    <dd class="cin"></dd>
                                    <dt>Emergency Contact No</dt>
                                    <dd class="emergency_contact_no"></dd>
                                </dl>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="box">

                                <dl class="dl-horizontal">
                                    <dt>Patient Code</dt>
                                    <dd class="patient_code"></dd>

                                    <dt>Mobile</dt>
                                    <dd class="mobile"></dd>
                                    <dt>Phone</dt>
                                    <dd class="phone"></dd>
                                    <dt>Language</dt>
                                    <dd class="language"></dd>

                                    <dt>SSN</dt>
                                    <dd class="ssn_no"></dd>
                                    
                                    <dt>Platform Id</dt>
                                    <dd class="platform_id"></dd>
                                </dl>
                            </div>

                        </div>
                    </div>
                    <hr>
                        <div class="form-group">
                           
                            <table class="table table-bordered">
                                <thead>
                                    <th>No</th>
                                    <th>Record Id</th>
                                   
                                    <th>Agency Name</th>
                               
                                    <th>Full Name</th>
                                    <th>Type</th>
                                    <th>Date of Birth</th>
                                    <th>Status</th>
                                </thead>
                                <tbody id="existing_record_id">
                                    <tr>
                                        <td colspan="4">No record available</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="saveId" class="btn btn-success">Create Record</button>
                            <button type="button" class="btn btn-light" id="link-modal-popup">Link</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>