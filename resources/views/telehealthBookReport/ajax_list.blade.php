<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Agency Name</th>
            <th>Type</th>
            <th>Portal ID</th>
            <th>Portal Name</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Nurse</th>
            <th>Language</th>
            <th>Created Date / Created By</th>
        </tr>
    </thead>
    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>{{ $cnt++}}</td>
                    <td>
                        {{ $val->patient->agencyDetail->agency_name }}
                    </td>
                    <td>
                        {{ $val->patient->type }}
                    </td>
                    <td>
                        @if(isset($val->patient->id))
                           <a href="{{ url('patient/view')}}/{{ $val->patient->id}}" target="_blank"> {{ $val->patient->id}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->patient->id))
                            <a href="{{ url('patient/view')}}/{{ $val->patient->id}}" target="_blank">{{ $val->patient->first_name.' '.$val->patient->last_name}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->telehealth_date))
                            {{ date('m/d/Y', strtotime($val->telehealth_date))}}
                        @endif
                    </td>
                    <td>
                        @if(isset($val->start_time))
                            {{ date('H:i A', strtotime($val->start_time))}} -
                            {{ date('H:i A', strtotime($val->end_time))}}                          
                        @endif
                    </td>
                    <td>
                        @if(isset($val->nurse_name))
                            {{ $val->nurse_name }}
                        @endif    
                    </td>
                    <td>
                        @if(isset($val->name))
                            {{ $val->name}}
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_at)}} <br>
                        @if(isset($val->created_by_name))
                        {{ $val->created_by_name}}
                        @endif
                    </td>                    
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr>
                <td colspan="10">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right tele_book_report_paginate pegination-margin" id="tele_book_report_paginate">
{{ $query->links() }}
</div>