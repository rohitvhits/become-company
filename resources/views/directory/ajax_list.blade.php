<div class="table-responsive tableData" >
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <th>
                #
            </th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Ext</th>
        </thead>
        <tbody>
            @php 
                $i = ($page *50)-49;
            @endphp
            @if(!empty($query[0]))
                @foreach($query as $val)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$val->first_name.' '.$val->last_name}}</td>
                        <td>{{$val->email}}</td>
                        <td>{{$val->phone}}</td>
                        <td>{{$val->department}}</td>
                        <td>{{$val->ext}}</td>
                    </tr>
                @endforeach
            @endif

            @if(count($query) ==0)
            <tr>
                <td colspan="6" style="text-align: center;"><b>No record available</b></td>
            </tr>

            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>

<script>
    var total = "{{ $query->total()}}";
   
    $('#show_no_record').attr('style','margin-top:25px')
    if(total ==0){
        $('#show_no_record').attr('style','margin-top:10%')
    }
    </script>