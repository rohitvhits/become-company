@php $count = count($locationdata) > 0 ? count($locationdata)/2 : 0; @endphp 
<div class="col-md-6 pl-0 overflow-cls">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Location</th>
                <th>Total Amount</th>
                <th>Total Remaining</th>
                <th>Total Received</th>
            </tr>
        </thead>
        <tbody>
            @if (count($locationdata) > 0)
                @foreach($locationdata as $key => $row)
                @if($key < $count) 
                <tr>
                    <th scope="row">{{$row->address1}}</th>
                    @if(array_key_exists($row->id,$data))
                        @if(isset($data[$row->id]['total_amount']) && $data[$row->id]['total_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-info">${{number_format(floor(floatval($data[$row->id]['total_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                        @if(isset($data[$row->id]['remaining_amount']) && $data[$row->id]['remaining_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-danger">${{number_format(floor(floatval($data[$row->id]['remaining_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                        @if(isset($data[$row->id]['received_amount']) && $data[$row->id]['received_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-success">${{number_format(floor(floatval($data[$row->id]['received_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                    @else
                        <td class="text-center">$0.00</td>
                        <td class="text-center">$0.00</td>
                        <td class="text-center">$0.00</td>
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
                <th>Location</th>
                <th>Total Amount</th>
                <th>Total Remaining</th>
                <th>Total Received</th>
            </tr>
        </thead>
        <tbody>
            @if (count($locationdata) > 0)
                @foreach($locationdata as $key => $row)
                @if($key > $count) 
                <tr>
                    <th scope="row">{{$row->address1}}
                    </th>
                    @if(array_key_exists($row->id,$data))
                        @if(isset($data[$row->id]['total_amount']) && $data[$row->id]['total_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-info">${{number_format(floor(floatval($data[$row->id]['total_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                        @if(isset($data[$row->id]['remaining_amount']) && $data[$row->id]['remaining_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-danger">${{number_format(floor(floatval($data[$row->id]['remaining_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                        @if(isset($data[$row->id]['received_amount']) && $data[$row->id]['received_amount'] > 0)
                            <td class="text-center"><div class="badge badge-pill badge-success">${{number_format(floor(floatval($data[$row->id]['received_amount']) * 100) / 100, 2)}}</div></td>
                        @else
                            <td class="text-center">$0.00</td>
                        @endif
                    @else
                        <td class="text-center">$0.00</td>
                        <td class="text-center">$0.00</td>
                        <td class="text-center">$0.00</td>
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