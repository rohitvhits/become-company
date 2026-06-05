<table id="order-listing1" class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Date of Birth</th>
            <th>Mobile / Phone</th>
            <th>SSN</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($query) && count($query) > 0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp

            @foreach ($query as $val)
                <tr>
                    <td>
                        {{ $val->first_name }} {{ $val->last_name }}
                    </td>
                    <td> {{ $val->email }}</td>
                    <td>@if ($val->dob != '0000-00-00')
                        {{ Common::convertMDY($val->dob) }}
                        @else
                        -
                        @endif
                   
                    </td>
                    <td>
                        {{ $val->mobile }} <br> {{ $val->phone }}
                    </td>

                    <td>
                        {{ common::formatSSN($val->ssn)?? '-' }}
                    </td>
                    <td>
                        <a href="#" id="edit-dependent-{{$val->id}}" data-id="{{$val->id}}" data-first-name="{{ $val->first_name }}" data-last-name="{{$val->last_name}}" data-email="{{$val->email}}" data-dob="{{$val->dob}}" data-phone="{{$val->phone}}" data-mobile="{{ $val->mobile }}"  data-ssn="{{$val->ssn}}" onclick="openEditChildForm('{{$val->id}}')" title="View"><i class="fa fa-edit"></i></a>
                    </td>

                </tr>
            @endforeach
        @endif

        @if (count($query) == 0)
            <tr class="txt-center">
                <td colspan="9">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right hub_list_paginate pegination-margin" id="hub_list_paginate">
    {{ $query->links() }}
</div>

<script>
    var total = "{{ $query->total() }}";
    $('#total_record_id').html(total)
    $('#blank_div').attr('style', 'margin-top:25px')
    if (total == 0) {
        $('#blank_div').attr('style', 'margin-top:10%')
    }
</script>
