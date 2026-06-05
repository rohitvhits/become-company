<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>County</th>
                <th>Zip Code</th>
                @can('zipcode-change-status')
                    <th>Enable SMS</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @forelse($zipcode as $key => $zcode)
                <tr>
                    <td>{{ $zipcode->firstItem() + $key }}</td>
                    <td>{{ $zcode->county }}</td>
                    <td>{{ $zcode->zip_code}}</td>
                    @can('zipcode-change-status')
                    <td>
                        <label class="toggle-switch toggle-switch-success">
                            <input type="checkbox" data-last-status="{{ $zcode->sms_status}}" data-id="{{ $zcode->id}}" id="row_last_status{{ $zcode->id}}" name="sms_status" value="1" @if($zcode->sms_status =='1') checked @endif onchange="statusUpdate({{$zcode->id}},{{$zcode->sms_status}})">
                            <span class="toggle-slider round"></span>
                        </label>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No zipcode found.</td>
                </tr>
                @endforelse
        </tbody>
    </table>
    <div class="pagination pull-right pegination-margin">
            {{ $zipcode->links() }}
        </div>
</div>