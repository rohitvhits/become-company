<div class="col-md-12 pl-0 table-responsive">
    <table id="weeklyMonthlyStatesReportTable" class="myDataTable table table-bordered">
        <thead>
            <tr>
                <th class="text-center">Week Of</th>
                @foreach($serviceNames as $service)
                    <th class="text-center">{{ $service }}</th>
                @endforeach
                <th class="text-center">Grand Total</th> <!-- Add column header -->
            </tr>
        </thead>
        <tbody>
            @foreach($structured as $week => $serviceCounts)
                <tr>
                    <td class="text-center">{{ $week }}</td>

                    @php $rowTotal = 0; @endphp

                    @foreach($serviceNames as $service)
                        @php
                            $count = $serviceCounts[$service] ?? 0;
                            $rowTotal += $count;
                        @endphp
                        <td class="text-center">{{ $count ?: '' }}</td>
                    @endforeach

                    <td class="text-center" style="font-weight:bold;">{{ $rowTotal }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td class="text-center">Grand Total</td>
                @php $grandRowTotal = 0; @endphp
                @foreach($serviceNames as $service)
                    @php $value = $totals[$service] ?? 0; $grandRowTotal += $value; @endphp
                    <td class="text-center">{{ $value }}</td>
                @endforeach
                <td class="text-center">{{ $grandRowTotal }}</td> <!-- Grand total of all -->
            </tr>
        </tbody>
    </table>
</div>