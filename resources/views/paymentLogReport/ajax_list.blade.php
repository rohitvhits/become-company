<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Agency Name</th>
            <th>Portal ID</th>
            <th>Name</th>
            <th>Payment Type</th>
            <th>Services</th>
            <th>Total Service Amount</th>
            <th>Total Received Amount</th>
            <th>Total Remaining Amount</th>
            <th>Location</th>
            <th>Created Date / Created By</th>
            <th>Action</th>
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
                        {{ $val->patientDetails->agencyDetail->agency_name }}
                    </td>
                    <td>
                        @if(isset($val->patientDetails->id))
                           <a href="{{ url('patient/view')}}/{{ $val->patientDetails->id}}" target="_blank"> {{ $val->patientDetails->id}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->patientDetails->id))
                            <a href="{{ url('patient/view')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->first_name.' '.$val->patientDetails->last_name}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->paymentDeatil->id))
                            {{ $val->paymentDeatil->name}}
                        @endif
                    </td>
                    <td>
                        @if(isset($val->serviceArr))
                            @foreach($val->serviceArr as $key =>$service)
                                <label class="badge badge-{{$color[$key % count($color)]}}">{{$service}}</label><br>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(isset($val->paymentDeatil->id) && $val->paymentDeatil->id == '866')
                            <div class="badge badge-pill badge-info">${{ number_format($val->totalAmount,2)}} </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <div class="badge badge-pill badge-success">${{ number_format($val->received_amount, 2) }}</div>
                    </td>
                    <td>
                        <div class="badge badge-pill badge-danger">${{ number_format($val->remaining_amount,2) }}</div>
                    </td>
                    <td>
                        @if(isset($val->locationDetails->id))
                            {{ $val->locationDetails->address1}}
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_at)}} <br>
                        @if(isset($val->users->id))
                        {{ $val->users->first_name.' '.$val->users->last_name}}
                        @endif
                    </td>
                    <td>
                        <a href="javascript:void(0)" class=""  onclick="showLogDetails('{{$val->id}}')" title="Show Payment Details"><i class="fa fa-eye"></i></a>
                    </td>
                    
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr>
                <td colspan="9">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right payment_log_paginate pegination-margin" id="payment_log_paginate">
{{ $query->links() }}
</div>