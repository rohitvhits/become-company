@if(!empty($finalData))
    @foreach($finalData as $val)
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="d-flex align-items-center py-2 border-bottom">

									<div class="row ml-1">
                    <div class="col-md-6">
										  <h6 class="mb-1 tx-12" style="white-space:nowrap; margin-left:-8px; color:#007bff "> <b>{{$val->name}}</b></h6>
                    </div>
                    <div class="col-md-6">
                      {!! $val->type !!}
                    </div>
                    
										<p class="text-muted mb-0 tx-11" style="white-space: nowrap;"><i class="mdi mdi-map-marker mr-1"></i>{!! $val->address !!}</p>
                    </br>
										<p class="text-muted mb-0 tx-12"><i class="mdi mdi-road-variant"></i> <b>Distance: {{$val->distance}}</b><span class="distance-unit"> (Miles)</span></p>
										

									</div>
          </div>
          </div>
      </div>
    </div>
    &nbsp;
    @endforeach
@else
    <p class="distance">
        No record Found
    </p>
@endif