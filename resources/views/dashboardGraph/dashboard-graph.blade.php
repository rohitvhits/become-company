@include('include/header')
@include('include/sidebar')
 
<style>

.card .card-body{
    padding: 9px 10px !important;
}
.card .card-title{
    font-size: 13px !important;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <div class="row">
                <div class="col-md-5">
                    <h5 class="mb-0 font-weight-bold">Dashboard Graph</h5>
                </div>
                <div class="col-md-7" style="margin-bottom:10px;">
                    <div class="col-md-12">
                    <img src="{{ asset('/ajax-loader.gif') }}"  class="" alt="loader" id="loaderDashboardGraph" style="display:none">
                        <div class="">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control" id="agencyId">
                                        <option value="">Select Agency</option>
                                        @foreach($agency_list   as  $agency)
                                            <option value="{{  $agency->id }}">{{  $agency->agency_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="record_type">
                                        <option value="">Select Record Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                        
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="location_id">
                                        <option value="">Select Location</option>
                                        @foreach($location_list as $lct)
                                            <option value="{{ $lct->id}}">{{ $lct->address1}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
       <div class="col-12 grid-margin-top"></div>
      
        <div class="row">
            <div class="col-6">
              <div class="row">
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                        
                        <div class="card">
                            
                                <div class="card-body">
                                <h4 class="card-title">Total Appointment</h4>
                                    <div class="d-flex justify-content-between">
                                        
                                        <p class="text-muted"><a href="javascript:void(0)" onclick="redirection()" target="_blank"><span id="total_id">0</span> </a></p>
                                    </div>
                                </div>
                        
                        </div>
                    
                    
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                           
                            <div class="card-body">
                            <h4 class="card-title">Total Pending</h4>
                                <div class="d-flex justify-content-between">
                                
                                    <p class="text-muted"> <a href="javascript:void(0)" onclick="redirection('Pending')" target="_blank"><span id="total_pending">0</span></a></p>
                                </div>
                            </div>
                        
                    </div>
                    
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Cancelled </h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('cancelled')" target="_blank"><span id="total_cancelled">0</span></a></p>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                            <div class="card-body">
                            <h4 class="card-title">Total Booked</h4>
                                <div class="d-flex justify-content-between">
                                
                                    <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('booked')" target="_blank"><span id="total_booked">0</span></a></p>
                                </div>
                            </div>
                       
                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                            <div class="card-body">
                            <h4 class="card-title">Total Completed</h4>
                                <div class="d-flex justify-content-between">
                                
                                    <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('completed')" target="_blank"><span id="total_completed">0</span></a></p>
                                </div>
                            </div>
                        
                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Noshow</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('noshow')"  target="_blank"><span id="total_noshow">0</span></a></p>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Arrived</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('arrived')" target="_blank"><span id="total_arrived">0</span></a></p>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                       
                        <div class="card-body">
                        
                        <h4 class="card-title">Total Processing</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"> <a href="javascript:void(0)" onclick="redirection('processing')" target="_blank"><span id="total_processing">0</span></a></p>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                       
                        <div class="card-body">
                        <h4 class="card-title">Total Not Interested</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"> <a href="javascript:void(0)" onclick="redirection('Not interested')"  target="_blank"><span id="total_Not_Interested">0</span></a></p>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Hospitalized/Rehab</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('hospitalized/rehab')"  target="_blank"><span id="total_hospitalized">0</span></a></p>
                            </div>
                        </div>

                    </div>
                    
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                       
                        <div class="card-body">
                        <h4 class="card-title">Total Unable To Contact</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"> <a href="javascript:void(0)" onclick="redirection('unableToContact')"  target="_blank"><span id="total_unableToContact">0</span></a></p>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Refused</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('refused')"  target="_blank"><span id="total_refused">0</span></a></p>
                            </div>
                        </div>
                       
                    </div>
                    
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        
                        <div class="card-body">
                        <h4 class="card-title">Total Mark as CheckIn</h4>
                            <div class="d-flex justify-content-between">
                              
                                <p class="text-muted"><a href="javascript:void(0)" onclick="redirection('checkin')"   target="_blank"><span id="total_checkin">0</span> </a></p>
                            </div>
                        </div>
                      
                    </div>
                    
                </div>
              </div>
            </div>
            <div class="col-lg-6 grid-margin grid-margin-lg-0 stretch-card">
               
                <div class="card">
                    
                  <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <h4 class="card-title">Appoinment Chart</h4>
                    <canvas id="pieChart" width="520" height="260" style="display: block; width: 520px; height: 260px;" class="chartjs-render-monitor"></canvas>
                  </div>
                </div>
              </div>
        </div>
        <div class="row">
            
        
        </div>
        
        
        
    </div>
</div>







@include('include/footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
{{-- //add by rohit --}}
<script src="{{ asset('assets/js/chart.js')}}?time={{ env('timestamp')}}"></script>
<script src="<?= URL::to('assets/js/chart.min.js') ?>"></script>

{{-- end by rohit --}}

<script>
    var _CHATURL ="{{ url('dashboard-graph-ajax')}}";
    $(function(){
        getData();
    });
    function getData(){
        $('#loaderDashboardGraph').attr('style','');
        var agencyId    =$('#agencyId').val();
        $.ajax({
            url: '{{  url("dashboard-graph-agency")  }}', 
            type: 'GET',
            data:{
                'agency_id':agencyId,
                'record_type':$('#record_type').val(),
                'location_id':$('#location_id').val()
            },
            success: function(data) {
                $('#loaderDashboardGraph').attr('style','display:none');
                var json    =data.data;
                var total = (json.total)?json.total:0;
                var booked = (json.booked)?json.booked:0;
                var cancelled = (json.cancelled)?json.cancelled:0;
                var completed = (json.completed)?json.completed:0;
                var noshow = (json.noshow)?json.noshow:0;
                var Notinterested = (json.Notinterested)?json.Notinterested:0;
                var pending = (json.Pending)?json.Pending:0;
                var processing = (json.processing)?json.processing:0;
                var refused = (json.refused)?json.refused:0;
                var unableToContact = (json.unableToContact)?json.unableToContact:0;
                var hospitalized = (json.hospitalized)?json.hospitalized:0;
                var arrived = (json.arrived)?json.arrived:0;
              

                $('#total_id').html(total)
                $('#total_pending').html(pending)
                $('#total_cancelled').html(cancelled)
                $('#total_booked').html(booked)
                $('#total_completed').html(completed)
                $('#total_noshow').html(noshow)
                $('#total_arrived').html(arrived)
                $('#total_processing').html(processing)
                $('#total_Not_Interested').html(Notinterested)
                $('#total_hospitalized').html(hospitalized)
                $('#total_unableToContact').html(unableToContact)
                $('#total_refused').html(refused)
            }
        });

    }

$('#agencyId,#record_type,#location_id').change(function(e){
    getData();
    loadChart();
})

function redirection(status=""){
    var agencyId = $('#agencyId').val();
    var url = "{{ url('patient')}}?status="+status+"&agency_fk="+agencyId+'&type='+$('#record_type').val()+'&locationId='+$('#location_id').val();
    window.open(url,'_blank'); 

}
</script>