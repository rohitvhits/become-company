<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">HHA Patient Coordinator </span></p> 
    <p class="mb-0 tx-13">
        <label class="col-form-label card-title">Search Date:</label>
        <input type="text" class="form-control"  value="{{ date('m/01/Y')}} - {{ date('m/t/Y')}}" name="hha_patient_coordinator_date" id="hha_patient_coordinator_date">
    </p>
    

</div>
<div class="row">
    <div class="col-12">
        <div class="col-12 loader-calender" id="logList8866" style="display:flex;justify-content:center;margin-top:10%">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertagmedication" style="display:none">
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            {{-- <table class="table table-bordered">
                <thead>
                    <th>No</th>
                    <th>Medication Name</th>
                    <th>Start Date</th>
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Frequency</th>
                </thead>
                <tbody id="hha_patient_coordinator_id">

                </tbody>
            </table> --}}
        </div>
        
        
    </div>
    <!-- <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousId" style="display:none" onClick="previous()">Prev</a></li>
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextId" style="display:none"   onClick="next()">Next</a></li>
    </div> -->
</div>


