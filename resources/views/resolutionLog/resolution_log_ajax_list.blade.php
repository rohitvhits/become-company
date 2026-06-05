<table class="table table-bordered table-width1">
    <thead>
        <th>#</th>
        <th>Agency Name</th>
        <th>Portal Id</th>
        <th>Portal Name</th>
        <th>Team</th>
        <th>Resolution</th>
        <th>Cancel Reason</th>
        <th>Refuse Reason</th>
        <th>Notes</th>
        <th>Created Date/Created By</th>
    </thead>
    <tbody>

       
        @php 
        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        @endphp
        @forelse($query as $vas)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$vas->agency_name}}</td>
                    <td><a href="{{ url('patient/view')}}/{{ $vas->patient_id}}" target="_blank"> {{ $vas->patient_id}}</a></td>
                    <td>{{ $vas->p_fa_name}} {{ $vas->p_la_name}}</td>
                    <td>{{ $vas->team}}</td>
                    <td>
                        @php $resStatus = $vas->resolution; @endphp
                        @if($vas->resolution == 'unableToContact')
                            @php $resStatus = 'Unable To Contact'; @endphp
                        @endif
                        {{$resStatus}}
                    </td>
                    <td>
                        {{ $vas->cancel_reason}}
                        @if(!empty($vas->other_cancel_reason))
                            <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $vas->other_cancel_reason }}"></i>
                        @endif
                    </td>
                    <td>
                        {{ $vas->refuse_reason}}
                        @if(!empty($vas->other_refuse_reason))
                            <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $vas->other_refuse_reason }}"></i>
                        @endif
                    </td>
                    <td>{{ $vas->notes}}</td>
                    <td>{{ date('m/d/Y h:i:s', strtotime($vas->created_at))}} <br/> {{ $vas->first_name}} {{$vas->last_name}}</td>
                </tr>
                @php 
                $i++;
                @endphp
        @empty
            <tr>
            <td colspan="10">No record available</td>
            </tr>
        @endforelse

        
    </tbody>
</table>
<div class="pull-right pegination-margin">
                       
    {{ $query->links() }}
</div>
