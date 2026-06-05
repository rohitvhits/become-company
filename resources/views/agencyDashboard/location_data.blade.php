<table id="" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Zip Code</th>
            <th>City</th>
        </tr>
    </thead>
    <tbody>
        @if (count($locationData) > 0)
            @foreach($locationData as $location)
            <tr>
                <th scope="statistic"><?= '#' . '' . $location->id ?></th>
                <td>{{$location->zip_code}}</td>
                <td>{{$location->city}}</td>
            </tr>
            @endforeach
        @else
        <tr>
            <td colspan="7">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        @endif
    </tbody>
    </table>
    <div class="pull-right pegination-margin">
        {{ $locationData->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>