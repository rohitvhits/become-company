@include('include.header_new')
<style>
.main-panel ul li{
    margin-left: 16px;
}
</style>
<div class="content-wrapper main-panel"> 
<p class="text-muted">Last Updated :{{ date('m/d/Y h:i A',strtotime($details->updated_at))}}</p>
    {!! $details->message !!}
</div>