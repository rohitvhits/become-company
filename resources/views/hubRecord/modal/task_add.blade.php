<div class="modal fade " id="exampleModal-task" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Task Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id_task">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="hub_task_patient_id">
                    @csrf
                    <input type="hidden" name="hdn_task_id" id="hdn_task_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom:0px !important">
                                    <label for="recipient-name" class="col-form-label">Task Name<span class="error">*</span>:</label>
                                    <input type="text" name="task_name" class="form-control" id="task_name_id" autocomplete="off" placeholder="Task Name">
                                    <span id="task_name_id_error" class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom:0px !important">
                                    <label for="recipient-name" class="col-form-label">Assign To<span class="error">*</span>:</label>

                                    <select class="form-control js-example-basic-single" name="assign_to" id="assign_to_id">
                                        <option value="">Select Assign To</option>
                                        @if (!empty($nyb_user_list[0]))
                                        @foreach ($nyb_user_list as $val)
                                        <option value="{{ $val->id }}">{{ $val->name }} 
                                        @if($val->agency_fk != NULL)
                                                (Agency User)
                                            @else
                                                (NyBest User)
                                            @endif
                                        </option>
                                        @endforeach
                                        @endif

                                    </select>
                                    <span id="assign_to_error" class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Start Date <span class="error">*</span></label>
                                        <input type="text" name="start_date" class="form-control" id="start_date" placeholder="Select Task Start Date" readonly>
                                        <span id="start_date_error" class="error"></span>
                                
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="">
                                    <label for="recipient-name" class="col-form-label">Due Date <span class="error">*</span></label>
                                    <!-- <input type="text" name="due_date" class="form-control date_input" id="due_date_task_model_id" placeholder="Select Task Due Date" readonly> -->
                                    <div class="input-group date" id="due_date_task_model_id_date">
                                        <input type="text" id="due_date_task_model_id" name="due_date" class="form-control date_input" required />
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="due_date_error" class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Priority <span class="error">*</span>:</label>
                                    <select name="priority" class="form-control" id="task_priority">
                                        <option value="">Select Priority</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>

                                    <span id="priority_error" class="error"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                                <label for="recipient-name" class="col-form-label">Description <span class="error">*</span>:</label>
                                <textarea type="text" class="form-control" name="task_description" placeholder="Enter Task Description" id="task_description_modal" rows="4" cols="50" value=""></textarea>
                                <span id="task_description_task_error" class="error"></span>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="submitTaskAdd" onclick="taskAdd()" style="padding: 4px 10px;border-radius: 5px;">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" style="padding: 4px 10px;border-radius: 5px;">Close</button>
                    </div>
                </form>
 
            </div> 
        </div>
    </div>