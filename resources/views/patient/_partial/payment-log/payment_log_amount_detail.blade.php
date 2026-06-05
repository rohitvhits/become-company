<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Services</th>
            <th>Amount(USD)</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @foreach($query as $val)
            <tr>
                    <td>{{ $cnt++}}</td>
                    <td>
                        {{ $val->services}}
                    </td>
                    <td>
                       ${{ number_format(floor(floatval($val->amount) * 100) / 100, 2) }}
                    </td>                    
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
