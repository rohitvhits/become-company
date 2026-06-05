<div class="tab-pane" id="hha-caregiver-inservice">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">HHA Caregiver InService</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121"
                    style="display:none">
            </div>
        </div>
        <div class="col-12">
            <table class="table table-bordered" id="caregiver_inservice_datatable">
                <thead>
                    <th>No</th>
                    <th>Topic Name</th>
                    <th>InService Date</th>
                    <th>From Time</th>
                    <th>End Time</th>
                    <th>Description</th>
                </thead>
                <tbody id="caregiver_inservice_id">

                </tbody>
            </table>
        </div>
    </div>
</div>
