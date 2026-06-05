<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Payment Log</p>
            <div class="">
                <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#add-payment-data" id="add_payment_data" data-whatever="@mdo" style="margin-right: 10px;"><i class="mdi mdi-plus mr-1"></i>Add</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="exportCsv()"><i class="mdi mdi-file-export mr-1"></i> Export</a>
            </div>
        </div>
    </div>
    <div class="col-12" >
    <table id="order-listing1" class="table table-bordered paymentLoader" style="display:''">
            <thead>
                <tr>
                    <th nowrap>#</th>
                    <th nowrap>Payment Type</th>
                    <th nowrap>Services</th>
                    <th nowrap>Total Service Amount</th>
                    <th nowrap>Total Received Amount</th>
                    <th nowrap>Total Remaining Amount</th>
                    <th nowrap>Location</th>
                    <th nowrap>Created Date / Created By</th>
                    <th nowrap>Action</th>
                </tr>
            </thead>
            <tbody id="">
                <tr>
                    <td class="line loading-shimmer" colspan="8"></td>
                </tr>
            </tbody>
        </table>

        <div id="payment_response_id" class="table table-responsive1 payment_table_tab" style="display:none">
        </div>
    </div>
</div>
