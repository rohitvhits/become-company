<div class="tab-pane" id="hha-caregiver-other-compliance">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">HHA Caregiver Other Compliance</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="col-12 loader-calender" id="logList11" style="display:flex;justify-content:center;margin-top:10%">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag1211"
                    style="display:none">
            </div>
        </div>

        <div class="col-12" style="max-height: 500px;overflow-y: auto;">
            <table class="table table-bordered">
                <thead>
                    <th>No</th>
                    <th>Medical Name</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </thead>
                <tbody id="tbody_compliance_id">

                </tbody>
            </table>
        </div>
    </div>
</div>
