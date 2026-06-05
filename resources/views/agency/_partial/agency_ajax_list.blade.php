<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 20px;">#</th>
            <th nowrap>Record#</th>
            <th nowrap>Agency Logo</th>
            <th nowrap>Name</th>
            <th nowrap>Email</th>
            <th nowrap>Phone</th>
            <th nowrap>City</th>
            <th nowrap>Enable SMS</th>
            <th nowrap>Integration</th>
        </tr>
    </thead>
    <tbody>
        @if($query->total() != 0)
            @php
                $i = 1 + (($query->currentPage() - 1) * $query->perPage());
            @endphp

            @foreach($query as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td style="white-space: nowrap;">
                        <a href="{{ url('agency-view/') }}/{{ $row->id }}"># {{ $row->id }}</a>
                    </td>
                    <td style="white-space: nowrap;">
                        @if($row->agency_logo != "")
                            <img src="{{ url('download-agency-images') }}?id={{ $row->id }}" style="height: 76px; width: 145px; border-radius: 5px;" alt="Logo">
                        @else
                            @php $logo = 'default.png'; @endphp
                            <img src="{{ asset('allupload/' . $logo) }}" style="height: 76px; width: 145px; border-radius: 5px;" alt="Logo">
                        @endif
                    </td>
                    <td style="white-space: nowrap;">{{ ucwords($row->agency_name) }}</td>
                    <td style="white-space: nowrap;">{{ $row->email }}</td>
                    <td style="white-space: nowrap;">{{ $row->phone }}</td>
                    <td style="white-space: nowrap;">{{ $row->city }}</td>
                    <td>
                        @if($row->is_sms == 1) Yes @else No @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($row->enable_hha == 1)
                            <img src="{{ asset('img/hha.png') }}" title="HHA" alt="HHA" style="height: 15px; width: 15px;">
                        @endif
                        @if($row->alaycare_status == 1)
                            <img src="{{ asset('img/alayacare.png') }}" title="AlayaCare" alt="AlayaCare" style="height: 15px; width: 15px;">
                        @endif
                        @if($row->is_sms == 1)
                            <img src="{{ asset('img/sms.png') }}" title="SMS" alt="SMS" style="height: 15px; width: 15px;">
                        @endif
                        @if($row->robort_status == 1)
                            <img src="{{ asset('img/emmacare.png') }}" title="Emmacare" alt="Emmacare" style="height: 25px; width: 25px;">
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

        @if($query->total() == 0)
            <tr>
                <td colspan="9">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
