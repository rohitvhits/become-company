<style>
    /* #esign_reponse_id .table-responsive {
        overflow-x: auto !important;
    }

    .table-responsive td {
        position: static !important;
       
    }

   

    .table-responsive .dropdown-menu {
        position: absolute !important;

        will-change: transform;
        
    } */

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
            @if($addService == 1)
            <p class="mb-0 tx-13 pull-right">
                <a data-toggle="modal" class="btn btn-info btn-sm d-none d-md-block" data-target="#serviceByPatientTypeModal"
                    style="color:#fff" onclick="getPatientId('{{ $record->id}}','{{$record->type}}')">
                    <i class="mdi mdi-plus"></i> Add Service</a>
            </p>
            @endif
            
        </div>
    </div>
    <div class="col-12" >
        <table id="order-listing1" class="table table-bordered serviceLoader" style="display:''">
            <thead>
                <tr>
                    <th nowrap>#</th>
                    <th nowrap>Portal Id</th>
                    <th nowrap>Agency Name</th>
                    <th nowrap>Document Name</th>
                    <th nowrap>Service Name</th>
                    <th nowrap>Status</th>
                    <th nowrap>Followup Date</th>
                    <th nowrap>Due Date</th>
                    <th nowrap>Created Date / Created By</th>
                    <th nowrap>Completed Date / Completed By</th>
                    <th nowrap>Last Status Updated Date / Last Status Updated By</th>
                    @if(auth()->user()->agency_fk =="")
                        <th nowrap>Action</th>
                    @elseif(auth()->user()->agency_fk !="")
                        <th nowrap>Action</th>
                    @endif
                        
                </tr>
            </thead>

            <tbody id="">
                <tr>
                    <td class="line loading-shimmer" colspan="12"></td>
                </tr>
            </tbody>
        </table>
        <div id="esign_reponse_id" class="table table-responsive1 service_request_tab" style="display:none">

            <table id="order-listing1" class="table table-bordered">
                <thead>
                    <tr>
                        <th nowrap>#</th>
                        <th nowrap>Portal Id</th>
                        <th nowrap>Agency Name</th>
                        <th nowrap>Document Name</th>
                        <th nowrap>Service Name</th>
                        <th nowrap>Status</th>
                        <th nowrap>Followup Date</th>
                        <th nowrap>Due Date</th>
                        <th nowrap>Created Date / Created By</th>
                        <th nowrap>Completed Date / Completed By</th>
                        <th nowrap>Last Status Updated Date / Last Status Updated By</th>
                        @if(auth()->user()->agency_fk =="")
                            <th nowrap>Action</th>
                        @elseif(auth()->user()->agency_fk !="" && strtolower($record->type) =='caregiver')
                            <th nowrap>Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody id="service_requested_id">
                   
                </tbody>
            </table>

            <div class="pull-right pegination-margin" id="paginate_id">

            </div>
        </div>
    </div>
</div>
