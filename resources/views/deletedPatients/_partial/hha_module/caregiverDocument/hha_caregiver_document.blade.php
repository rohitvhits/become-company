<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Document</p>
    <p class="mb-0 tx-13">
        <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHAdocumentData()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
            Add</a>
    </p>
</div>
<div class="row">
    <div class="col-12">
        <div class="col-12 loader-calender" id="loader-caregiver-document" style="display:flex;justify-content:center;margin-top:10%">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loader_caregiver_doc" style="display:none">
        </div>
    </div>
    <div class="col-12">
        <table class="table table-bordered" id="document-table">
            <thead>
                <th>No</th>
                <th>Doc Id</th>
                <th>Document Type</th>
                <th>Description</th>
                <th>File Name</th>
                <th>Created On / Created By</th>
                <th>Action</th>
            </thead>
            <tbody id="document-caregiver-table-data">
            </tbody>
        </table>
    </div>

</div>