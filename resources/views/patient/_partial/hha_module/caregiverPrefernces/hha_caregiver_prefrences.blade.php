<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Prefrences</p>
</div>
<div class="row">
    <div class="col-12">
        <div class="col-12 loader-calender" id="loader-caregiver" style="display:flex;justify-content:center;margin-top:10%">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loader-caregiver-prefrences" style="display:none">
        </div>
    </div>
    <div class="col-12">
        <table class="table table-bordered" id="prefrences-table">
            <thead>
                <th>#</th>
                <th>Preference ID</th>
                <th>Preference Name</th>
                <th>Preference Value</th>
                <th>Preference Type</th>
            </thead>
            <tbody id="prefrences-caregiver-table-data">
            </tbody>
        </table>
    </div>

</div>