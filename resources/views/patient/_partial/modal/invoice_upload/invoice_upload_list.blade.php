<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Invoice List</p>
    <p class="mb-0 tx-13">
        @can('invoice-upload-add')
        <a data-toggle="modal"
        class="pull-right btn btn-info btn-sm d-none d-md-block addInvoice"
        data-target="#exampleModal-invoice" data-whatever="@mdo" data-type="Invoice"
        onclick="viewServices();requestsServices();"><i class="mdi mdi-plus"></i>
        Add</a>
        @endcan
    </p>
</div>

<div class="row">
    <div class="col-12">
        <div id="invoice_upload_id" class="table-responsive ">
            <table id="" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Attachment</th>
                        <th>Service</th>
                        <th>Created Date/ Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="invoice_table_id">
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
<script>
    var deletePermission = @json(auth()->user()->can('invoice-upload-delete'));
</script>
@include('patient._partial.modal.invoice_upload.invoice_upload_document_modal')
@include('patient._partial.modal.invoice_upload.document_upload_modal')
