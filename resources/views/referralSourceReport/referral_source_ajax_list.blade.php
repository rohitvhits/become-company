<table class="table table-bordered table-width1">
    <thead>
        <th>#</th>
        <th>Referral Type</th>
        <th>Caregiver</th>
        <th>Patient</th>
    </thead>
    <tbody>

       
        @php 
        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        @endphp
        @forelse($query as $vas)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{ $vas->referral_type}}</td>
                    <td>{{ $vas->caregiver_count}}</td>
                    <td>{{ $vas->patient_count}}</td>
                    
                </tr>
                @php 
                $i++;
                @endphp
        @empty
            <tr>
            <td colspan="4">No record available</td>
            </tr>
        @endforelse

        
    </tbody>
</table>
<div class="pull-right pegination-margin">
                       
    {{ $query->links() }}
</div>
