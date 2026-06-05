<style>
    #esign_reponse_id .table-responsive {
        overflow-x: auto !important;
    }

    .table-responsive td {
        position: static !important;
        /* Prevent dropdown clipping */
    }

    .table-responsive {
        overflow: visible !important;
    }

    .table-responsive .dropdown-menu {
        position: absolute !important;
        /* Ensures proper rendering */
        will-change: transform;
        /* Fix dropdown positioning */
    }

    #serviceLoader{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        position: absolute;
        left: 50%;
        transform: translate(-50%);
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Service Requested</p>
           
        </div>
    </div>
    <div class="col-12 row" style="width:999px;margin-left:1px">
        <div class="loader-main" id="serviceLoader" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class=""
                    alt="loader">
            </div>
        </div>
        <div id="esign_reponse_id" class="table table-bordered table-responsive">

            <table id="order-listing1" class="table table-bordered">
                <thead>
                    <tr>
                        <th nowrap>#</th>
                        <th nowrap>Portal Id</th>
                        <th nowrap>Agency Name</th>
                        <th nowrap>Service Name</th>
                        <th nowrap>Status</th>
                        <th nowrap>Followup Date</th>
                        <th nowrap>Due Date</th>
                        <th nowrap>Created Date / Created By</th>
                        <th nowrap>Completed Date / Completed By</th>

                        @if(auth()->user()->agency_fk =="")
                          
                            @endif
                    </tr>
                </thead>

                <tbody id="service_requested_id">
                    <tr>
                    <td colspan="10">Loading...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pull-right pegination-margin" id="paginate_id">

            </div>
        </div>
    </div>
</div>
