<div class="tab-content right-section-tab-content" id="hha-exchange">
    <!-- <div class="tab-pane" id="hha-calender-section">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Calendar</p>
        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag12" style="display: none; ">
                </div>
                <div id="calendar" class="full-calendar"></div>

            </div>

        </div>

    </div> -->

    <!-- <div class="tab-pane" id="hha-caregiver-notes">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Notes</p>
            @if($auth->agency_fk !=106)
            <p class="mb-0 tx-13">
                <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHACaregiverSubject()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                    Add</a>

            </p>
            @endif
        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                </div>

            </div>
            <div class="col-12">
                <table class="table table-bordered" id="chat-messages-news-dataTable">
                    <thead>
                        <th>No</th>
                        <th>Notes</th>

                        <th>Created Date</th>
                    </thead>
                    <tbody id="chat-messages-news">

                    </tbody>
                </table>
            </div>

        </div>
    </div> -->

    <!-- <div class="tab-pane" id="hha-caregiver-medical">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Medical</p>
            <p class="mb-0 tx-13 row pull-right">

                <span class="col-md-6">
                    <select class="form-control" id="hha_status_id" onChange="getMedicalalList()">
                        <option value="">Select</option>
                        @foreach($hhaStatusList as $val)
                        <option value="{{ $val->status  }}">{{ $val->status }}</option>
                        @endforeach
                    </select>
                </span>
                <span class="col-md-6">
                    <a class="btn btn-info btn-sm" onclick="refreshMedical()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                        SYNC Caregiver Medical</a>

                </span>


            </p>


        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                </div>

            </div>
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <th>No</th>
                        <th>Medical Name</th>
                        <th>Status</th>
                        <th>Medical Due Date</th>
                        <th>Date Perform</th>
                    </thead>
                    <tbody id="tbody_id">

                    </tbody>
                </table>
            </div>

        </div>


    </div> -->

    <!-- <div class="tab-pane" id="hha-caregiver-inservice">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">InService</p>
            @can('hha-calendar-add-inservice')
           
            @endcan

        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
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


    </div> -->

    <!-- <div class="tab-pane" id="hha-caregiver-other-compliance">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Other Compliance</p>
            <p class="mb-0 tx-13    row pull-right">
                
        </div>
        <div class="row">
            <div class="col-12">

                <div class="col-12 loader-calender" id="logList11" style="display:flex;justify-content:center;margin-top:10%">
                    <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag1211" style="display:none">
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


    </div> -->

    
</div>