@if (in_array($user->user_type_fk, [5, 6]))
    @php
        $i = 1;
    @endphp
@else
    @php
        $i = 0;
    @endphp
@endif

<div class="table-responsive">
    <table id="" class="table recordtabletdwidth table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Service</th>
                <th>Agency</th>
                <th>Booking Date</th>
                <th>Created Date / Created By</th>
            </tr>
        </thead>
        <tbody id="nybest-total" data-nybest-total-record="{{ $list->total() }}">
            @php
                $i = 1 + ($list->currentPage() - 1) * $list->perPage();
            @endphp

            @forelse ($list as $row)
                <tr>
                    <td style="white-space:nowrap">{{ $i++ }}</td>
                    <td style="min-width:220px; white-space:nowrap">
                        {{ $row->f_name }}
                    </td>

                    <td style="min-width:220px; white-space:nowrap">{{ $row->type }}</td>
                    <td style="min-width:220px; white-space:nowrap">
                        @foreach ($row->patientServiceRequestRelationShip as $data)
                            {{ $data->requestService->name ?? '' }}<br>
                        @endforeach
                    </td>
                    <td style="min-width:220px; white-space:nowrap">
                        {{ $row->agency_name }}
                    </td>
                    <td>
                        {{ $row->booking_date != '' ? date('m/d/Y', strtotime($row->booking_date)) : '-' }}
                    </td>
                    <td style="min-width:220px;  white-space:nowrap">
                        {{ date('m/d/Y h:i A', strtotime($row->created_date)) }} <br>
                        {{ $row->userDetails != null ? $row->userDetails->first_name : '' }}
                        {{ $row->userDetails != null ? $row->userDetails->last_name : '' }}
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="12">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pull-right pegination-margin log-pagination">
        {{ $list->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>

</div>
