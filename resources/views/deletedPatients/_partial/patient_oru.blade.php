<style>

#text-chat-messages-inner1 .msg {
    display: block;
    margin-top: 18px;
    border-top: 1px solid #dadada;
}
#text-chat-messages-inner1 .time {
    color: #999999;
    font-size: 11px;
    float: right;
}
</style>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Patient ORU/TRN Section</p>


    </div>
    <div class="row">
        <div class="col-12">
            <div class="col-12 loader-calender" id="logList8866" style="display:flex;justify-content:center;margin-top:10%">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag88661" style="display:none">
            </div>
        </div>
        <div class="col-12">
        <div class="text-chat-messages" id="sms-logss">
        </div>
        <div  class="text-center" id="hideLoadMoreId" style="display:none">
        <a  class="btn btn-info btn-rounded  btn-sm" href="javascript:void(0)" onclick="loadMore()">Load More</a>
        </div>
            
        </div>

    </div>


