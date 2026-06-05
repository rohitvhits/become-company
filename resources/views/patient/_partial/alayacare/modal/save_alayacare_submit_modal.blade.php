<div class="modal fade" id="alayacare-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title documens" id="ModalLabel">Alayacare</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="">
						<span aria-hidden="true">&times;</span>
					</button>
				</div> 
				<div class="modal-body">
                    <form id="alayacare-form-data" class="" >
                        
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="patient_id" value="{{$record->id}}" id="alaycare-patient-id">
                            <label for="recipient-name" class="col-form-label"> Branch<span style="color:red">*</span>:</label> 
                            <select class="form-control" id="branchdata" name="branchId">
                                <option value="">Select Branch</option>

                            </select>
                            <span class="col-sm-11 ml-auto pl-0" style="color:red" id="title">
                                @error('branchId')
                                    {{ $message }}
                                @enderror
                            </span>
                            <span style="color:red" id="branchIderror"></span>
                            <br>
                            
                                <label for="recipient-name" class="col-form-label">Group<span style="color:red">*</span>:</label>
                            <select class="form-control" id="groupdata" name="groupId">
                                <option value="">Select Group</option>

                            </select>
                            <span class="col-sm-11 ml-auto pl-0" style="color:red" id="title">
                                @error('groupId')
                                    {{ $message }}
                                @enderror
                            </span>
                            <span style="color: red" id="groupIderror"></span>
                            
                            
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="alayacareSubmit()" id="alayacare-submit">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="clearDataModal()">Close</button>
                </div>
			</div>
		</div> 
	</div>