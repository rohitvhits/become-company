<style>
    #streamlinedEsignLoader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        position: absolute;
        left: 50%;
        transform: translate(-50%);
    }
    .streamlined-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        transition: box-shadow 0.2s;
        cursor: pointer;
    }
    .streamlined-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .streamlined-card .card-body {
        padding: 12px;
    }
    .streamlined-card .card-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        min-height: 32px;
    }
    .streamlined-btn-group .btn {
        font-size: 11px;
        padding: 4px 8px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Esign Section</p>
            @can('esign-list')
                <p class="mb-0 tx-13">
                    <a class="btn btn-primary btn-sm pull-right" onclick="loadStreamlinedEsign()">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </a>
                </p>
            @endcan
        </div>
    </div>

    @can('esign-add')
    {{-- Doctor Selection --}}
    <div class="col-12 mb-3">
        <div class="form-group" style="max-width:300px;">
            <label>Select Doctor <span class="error" style="color:red">*</span></label>
            <select id="streamlined_doctor_id" class="form-control">
                <option value="">Select Doctor</option>
            </select>
            <span class="error" id="streamlined_doctor_error" style="color:red"></span>
        </div>
    </div>

    {{-- Available Forms --}}
    <div class="col-12 mb-3">
        <h6 class="mb-2">Available Forms</h6>
        <div class="loader-main" id="streamlinedEsignLoader" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" alt="loader">
            </div>
        </div>
        <div id="streamlined_template_list" class="row">
            {{-- Template cards loaded via AJAX --}}
        </div>
    </div>
    @endcan

    {{-- Sent Documents Section --}}
    <div class="col-12 mt-2">
        <h6 class="mb-2">Sent Documents</h6>
        <div class="loader-main" id="esignSectionLoader" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" alt="loader">
            </div>
        </div>
        <div id="esign_reponse_id" class="table-responsive1">
            <table id="order-listing1" class="table table-bordered esign-shimmer" style="display:none">
                <thead>
                    <tr>
                        <th style="width:100px;">Record</th>
                        <th>Template Name</th>
                        <th>Status</th>
                        <th>Sender</th>
                        <th>Review By</th>
                        <th>Signers</th>
                        <th>Added By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="shimmer-loader">
                    <tr>
                        <td colspan="8">Loading...</td>
                    </tr>
                </tbody>
            </table>
            <div id="esign_resp_id_new"></div>
            <div id="paginate_id"></div>
        </div>
    </div>
</div>

@include('patient._partial.esign.esign_modal_new')
@include('patient._partial.esign.upload_document_modal_new')
@include('patient._partial.esign.esign_sms_modal_new')
@include('patient._partial.esign.esign_log_modal')
