@php $count = count($servicesdata) > 0 ? count($servicesdata)/2 : 0; @endphp 
<div class="col-md-6 pl-0 overflow-cls">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Total Amount</th>
                <th>Total Remaining</th>
                <th>Total Received</th>
            </tr>
        </thead>
        <tbody>
            @if (count($servicesdata) > 0)
                @foreach($servicesdata as $key => $row)
                    @if($key < $count) 
                        <tr>
                            <th scope="row">{{$row['name']}}</th>
                            @if($row['total_amount'] > 0)
                                <td scope="row"><div class="badge badge-pill badge-info">${{number_format(floor(floatval($row['total_amount']) * 100) / 100, 2)}}</div></td>
                            @else
                                <td>$0.00</td>
                            @endif

                            @if($row['remaining_amount'] > 0)
                                <td scope="row"><div class="badge badge-pill badge-danger">${{number_format(floor(floatval($row['remaining_amount']) * 100) / 100, 2)}}</div></td>
                            @else
                                <td>$0.00</td>
                            @endif

                            @if($row['received_amount'] > 0)
                                <td scope="row"><div class="badge badge-pill badge-success">${{number_format(floor(floatval($row['received_amount']) * 100) / 100, 2)}}</div></td>
                            @else
                                <td>$0.00</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif           
        </tbody>
    </table>
</div>
<div class="col-md-6 pr-0 overflow-cls">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Total Amount</th>
                <th>Total Remaining</th>
                <th>Total Received</th>
            </tr>
        </thead>
        <tbody>
            @if (count($servicesdata) > 0)
                @foreach($servicesdata as $key => $row)
                @if($key >= $count) 
                <tr>
                    <th scope="row">{{$row['name']}}</th>
                    @if($row['total_amount'] > 0)
                        <td scope="row"><div class="badge badge-pill badge-info">${{number_format(floor(floatval($row['total_amount']) * 100) / 100, 2)}}</div></td>
                    @else
                        <td>$0.00</td>
                    @endif

                    @if($row['remaining_amount'] > 0)
                        <td scope="row"><div class="badge badge-pill badge-danger">${{number_format(floor(floatval($row['remaining_amount']) * 100) / 100, 2)}}</div></td>
                    @else
                        <td>$0.00</td>
                    @endif

                    @if($row['received_amount'] > 0)
                        <td scope="row"><div class="badge badge-pill badge-success">${{number_format(floor(floatval($row['received_amount']) * 100) / 100, 2)}}</div></td>
                    @else
                        <td>$0.00</td>
                    @endif
                </tr>
                @endif
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif           
        </tbody>
    </table>
</div>