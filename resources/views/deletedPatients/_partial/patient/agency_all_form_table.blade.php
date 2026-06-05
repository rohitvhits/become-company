<style>
    .custom-card-size {
        height: 70px;
        padding: 10px;
    }

    .custom-card-size .card-body {
        padding: 5px;
    }

    .custom-class{
        max-width: 15%;
    }
</style>
<div class="row">
    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card custom-class">
        <div class="card custom-card-size">
            <div class="card-body">
                <h4 class="card-title">Completed</h4>
                <div class="d-flex justify-content-between">
                    <p class="badge badge-outline-success badge-pill completed-count"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card custom-class">
        <div class="card custom-card-size">
            <div class="card-body">
                <h4 class="card-title">Pending</h4>
                <div class="d-flex justify-content-between">
                    <p class="badge badge-outline-warning badge-pill pending-count"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="">
            <div>
                <label class="mr-2"><input type="radio" name="status" value="all" checked> All</label>
                <label class="mr-2"><input type="radio" name="status" value="pending"> Pending</label>
                <label class="mr-2"><input type="radio" name="status" value="completed"> Completed</label>
            </div>
        </div>
    </div>
</div>


<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Forms</p>

    <p class="mb-0 tx-13 pull-right">
        @can('agency-all-form-create')
            <a class="btn btn-info btn-fw btn-sm addFormModal" href="javascript:void(0)">
                <i class="mdi mdi-plus"></i>Add Form</a>
        @endcan
    </p>
</div>

<div class="row">
    <div class="col-12">
        <div id="esign_reponse_id" class="table-responsive ">
            <table id="order-listing1" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Form Name</th>
                        <th>Status</th>
                        <th>Created Date/ Created By</th>
                        <th>Mark As Completed Date/ By</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="agency_all_form_table_id">
                    <tr>
                        <td colspan="6">No record available</td>
                    </tr>
                </tbody>
            </table>

            <div class="pull-right pegination-margin" id="paginate_id">

            </div>
        </div>
    </div>
</div>

@include('patient._partial.patient.agency_form_modal')
