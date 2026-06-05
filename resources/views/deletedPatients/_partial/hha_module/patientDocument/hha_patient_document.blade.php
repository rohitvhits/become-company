<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Document</p>
    <!-- <p class="mb-0 tx-13">
        <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getHHAPatientdocumentData()"
            data-whatever="@mdo"><i class="mdi mdi-plus"></i>
            Add</a>
    </p> -->
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered" id="document-patient-table">
            <thead>
                <th>No</th>
                <th>Doc Id</th>
                <th>Document Type</th>
                <th>Description</th>
                <th>File Name</th>
                <th>Created On / Created By</th>
                <th>Action</th>

            </thead>
            <tbody id="document-patient-table-data">
            </tbody>
        </table>
        <div id="patient-document-pagination" class="mt-2"></div>
    </div>

</div>