<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Assign NY Best User To Appointments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="{{ route('patientAssign') }}" method="post" id="patientAssign" onsubmit="return Assignvalidation();">
                        @csrf
                        <input type="hidden" name="appoiment_id" value="{{ $record->id }}">
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Assign To<span class="error">*</span>:</label>

                            <select class="form-control" name="assign_id" id="assign_id">
                                <option value="">Select Assign To</option>
                                @if (!empty($nyBestUserList))
                                @foreach ($nyBestUserList as $val)
                                <option value="{{ $val->id }}">{{ $val->full_name }}</option>
                                @endforeach
                                @endif

                            </select>
                            <span id="assign_to_us_error" class="error"></span>
                        </div>
                        <div class="form-group mt-2">
                            <label for="assign_dept" class="small mb-1">Department:</label>
                            <select class="form-control assign_dept" name ="assign_department" id="assign_department_id">
                                <option value="">Select Department</option>
                            </select>
                            <span class="error assign_department_error"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>