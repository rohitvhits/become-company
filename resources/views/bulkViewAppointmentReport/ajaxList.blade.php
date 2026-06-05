<div class="table-responsive ">
<table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Agency Name</th>
                <th>Service Name</th>
                <th>County</th>
                <th>Book Date</th>
                <th>Created Date</th>

            </tr>
        </thead>
        <tbody>
            @php
                $i = ($page * 50) - 49;
            @endphp
            @forelse ($query as $item)
                <tr>
                    <td>{{ $i}}</td>
                    <td>{{ $item->full_name}}</td>
                    <td>{{ $item->phone}}</td>
                    <td>{{ $item->email}}</td>
                    <td>{{ $item->agency_name}}</td>
                    <td>{{ $item->service_name}}</td>
                    <td>{{ $item->county}}</td>
                    <td>
                        @if($item->book_date !="" && $item->book_date !='0000-00-00')
                        {{ Common::convertMDY($item->book_date)}}
                    @endif
                    </td>
                    <td>{{ Common::convertMDYTime($item->created_date)}}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @empty
               <tr>
                    <td colspan="9" class="text-center">No record available</td>
               </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pull-right pegination-margin">
        
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    var total = "{{ $query->total()}}";
    $('#blank_div').attr('style','margin-top:25px')

</script>