<div class="tab-pane" id="alaycare-calendar">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Employee Schedule</p>
        <p class="mb-0 tx-13">
        <input type="text" class="form-control" value="{{ date('m/d/Y',strtotime('-6 days'))}}-{{ date('m/d/Y')}}"  id="created_date1">
        </p>
    </div>
    <div class="row">
        
        <div class="col-12">
            <div class="loader-main" id="loaderAlayaVisit" style="display:none">
                <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" >
                </div>
            </div>
            <table class="table table-bordered" >
                <thead>
                    <th>No</th>
                    <th>Visit Id</th>
                    <th>Visit Start</th>
                    <th>Visit End</th> 
                    <th>Status</th>
                    <th>Action</th>
                    
                </thead>
                <tbody id="alayacare_visit_id">

                </tbody>
            </table>
        </div>
        <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextVisitId" style="display:none"   onClick="nextVisit()">Next</a></li>
            <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousVisitId" style="display:none" onClick="previousVisit()">Prev</a></li>
            
        </div>

    </div>

</div>

<div class="modal fade" id="exampleModal-visit-details" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Visit Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmail()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="visit_details_id">

                
            </div>

            <div class="modal-footer">
                
                <button type="button" class="btn btn-light" data-dismiss="modal" >Close</button>
            </div>

          
        </div>
    </div>
</div>