<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Demographic Details</p>
    @can('hha-calendar-add-inservice')
    <!-- <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHACaregiverSubject()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                                                Add</a> -->
    @endcan

</div>
<div class="row">
    <div class="col-12">

        <div class="col-12 loader-calender" id="load-caregiver-demographics" style="display:flex;justify-content:center;margin-top:10%; background: rgba(102, 126, 234, 0.05); padding: 30px; border-radius: 8px;">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121Demo" style="display:none">
        </div>

    </div>
    <div class="col-12">

        <div class="row" id="hha_caregiver_basic" style="padding: 10px; margin-top: 15px;">

        </div>
    </div>

</div>