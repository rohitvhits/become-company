<style>
   

    #mqOrderLoader{
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
            <p class="card-title mb-0">MD Order List</p>
            @if(auth()->user()->agency_fk =="")
            <p class="mb-0 tx-13 pull-right">
                <a data-toggle="modal" class="btn btn-info btn-sm d-none d-md-block" data-target="#exampleModal-create-mq-order"
                    style="color:#fff" onclick="createMDOrders()">
                    <i class="mdi mdi-plus"></i> Add MD Order</a>
            </p>
            @endif
        </div>
    </div>
    <div class="col-12" >
        <div class="loader-main" id="mqOrderLoader" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class=""
                    alt="loader">
            </div>
        </div>
        <div id="mqorder_reponse_id" class="table table-responsive1">

            
        </div>
    </div>
</div>
