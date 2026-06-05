<div class="tab-pane" id="hha-caregiver-medical">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">HHA Caregiver Medical</p>
        <p class="mb-0 tx-13 row pull-right">
            <span class="col-md-6">
                <select class="form-control" id="hha_status_id" onChange="getMedicalalList()">
                    <option value="">Select</option>
                    @foreach ($hhaStatusList as $val)
                        <option value="{{ $val->status }}">{{ $val->status }}</option>
                    @endforeach
                </select>
            </span>
            <span class="col-md-6">
                <a class="btn btn-info btn-sm" onclick="refreshMedical()" data-whatever="@mdo"><i
                        class="mdi mdi-plus"></i>
                    SYNC Caregiver Medical</a>
            </span>
        </p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="col-12 loader-calender" id="logList1"
                style="display:flex;justify-content:center;margin-top:10%">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121"
                    style="display:none">
            </div>
        </div>

        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <th>No</th>
                    <th>Medical Name</th>
                    <th>Status</th>
                    <th>Medical Due Date</th>
                </thead>
                <tbody id="tbody_id">

                </tbody>
            </table>
        </div>
    </div>
</div>
