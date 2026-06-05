<div class="tab-pane" id="alaycare-skill">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Skill</p>
        
    </div>
    <div class="row">
        <!-- <div class="col-12">
            
            <div class="col-12" id="logList8866">
                
           
            </div>
        </div> -->
        <div class="col-12">
            <div class="loader-main" id="loaderAlayaSkill" style="display:none">
                <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" >
                </div>
            </div>
            <table class="table table-bordered" >
                <thead>
                    <th>No</th>
                    <th></th>
                    <th>Skill Name</th>
                    <th>Category Name</th>
                    <th>Branch Name</th>
                    <th>Due Date</th>
                </thead>
                <tbody id="alayacare_skill_id">

                </tbody>
            </table>
        </div>
        <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextSkillId" style="display:none"   onClick="nextSkill()">Next</a></li>
            <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousSkillId" style="display:none" onClick="previousSkill()">Prev</a></li>
            
        </div>

    </div>
</div>

<div class="modal fade" id="exampleModal-alaya-skill" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmployeeSkill()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="update_skill_details">
                <div class="modal-body">
                    <span id="skill_update_id"></span>
                    <input type="hidden" name="" id="skill_object_id">
                    
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Comments<span class="error">*</span>:</label>
                        <textarea class="form-control" id="alaya_skill_id"  rows="4" cols="50"></textarea>
                        <span class="error" id="alaya_skill_id_error"></span>
                    </div>

                    
                </div>
            </form>

            <div class="modal-footer">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loaderAlayaSkillSubmit" style="display: none; ">
            <button type="button" class="btn btn-primary"   onclick="updateEmployeeSkill()">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearEmployeeSkill()">Close</button>
            </div>

          
        </div>
    </div>
</div>

