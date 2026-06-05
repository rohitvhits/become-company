<div class="col-md-12 pl-0 table-responsive">
    <table id="referralsAnalyticsDashboardTable" class="myDataTable table table-bordered">
        <thead>
            <tr >
                <th class="text-center">#</th>
                <th class="text-center">Agency Name</th>
                @foreach($services as $service)
                <th class="text-center" data-service-key="{{ str_replace(' ', '', $service) }}" >{{$service}}</th>
                @endforeach
                <th class="text-center">Grand Total</th>
            </tr>
        </thead>

        <tbody>
            @if(count($agencies) > 0)

            @php 
                $cnt =  1;
                $serviceTotals = array_fill_keys(array_keys($services->toArray()), 0); 
                @endphp
                @foreach($agencies as $agency)
                    <tr data-row-type="data">
                        <td class="text-center">{{$cnt++}}</td>
                        <td class="text-center">{{ $agency->agency_name }}</td>
                        @php $agencyTotal = 0; @endphp

                    @foreach($services as $key=> $service)
                    @php
                        $count = $agencytWiseServiceData[$agency->id][$key] ?? 0;
                        $agencyTotal += $count;
                        $serviceTotals[$key] += $count; 
                    @endphp
                    <td class="text-center" data-service-value="{{ str_replace(' ', '', $service) }}">{{ $count }}</td>
                @endforeach
                <td class="text-center" data-grand-total="{{$agencyTotal}}"><strong>{{ $agencyTotal }}</strong></td>
                    </tr>
                @endforeach
                <tr class="total-row" data-row-type="total">
                    <th colspan="2"><center>Total</center> </th>
                    @foreach($services as $key=> $service)
                   <td class="text-center" data-grand-total="{{$serviceTotals[$key]}}">{{ $serviceTotals[$key]  }}</td>
                    @endforeach
                    <td class="text-center" data-grand-total="{{array_sum($serviceTotals)}}">{{array_sum($serviceTotals)}}</td>
                </tr>
            @endif

            @if(count($agencies) == 0)
                <tr class="txt-center">
                    <td colspan="8">No record available</td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>


