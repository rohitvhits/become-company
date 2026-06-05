<div class="modal fade" id="show_modal_popup" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                       
                        <div class="form-group">
                           
                            <table class="table table-bordered">
                                <thead>
                                    <th>No</th>
                                    <th>Record Id</th>
                                    <th>Agency Name</th>
                               
                                    <th>Full Name</th>
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