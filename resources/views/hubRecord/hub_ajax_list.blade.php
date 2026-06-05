<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th> <input type="checkbox" id="cboxId"></th>
            <th>ID</th>
            <th>Company</th>
            <th>Name</th>
            <th>Mobile / Phone</th>
            <th>DOB</th>
            <th>Gender</th>
            @if(Auth()->user()->view_ssn_hub ==1)
            <th>SSN</th>
            @endif
            <th>Created Date / Created By</th>
            <th>Updated Date / Updated By</th>
        </tr>
    </thead>

    <tbody>
        @if(isset($query) && count($query) >0)
        @php
        $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
        @endphp

        @foreach($query as $val)
        @php $flagClasss = ''; @endphp
        @if($val->flag == 1)
        @php $flagClasss = "pale-yellow-color"; @endphp
        @endif

        <tr class="{{$flagClasss}}">
            <td>
                <input type="checkbox" class="form-check-input cbox ml-0" value="{{ $val->id }}">
            </td>
            <td>
                @if(isset($val->id))
                <a href="{{ url('hub-record/view')}}/{{ $val->id}}" target="_blank"> {{ $val->id}}</a>
                @endif
                <br />
                @if($val->import_flag == 1)
                <span class="badge bg-info">Imported</span>
                @else
                <span class="badge bg-success">Manual</span>
                @endif<br>

                @if($val->dependent_id !="")
                <span class="badge bg-primary">Dependent</span>
                @endif
            </td>
            <td>
                {{ $val->agency_name }}
            </td>
            <td>
                {{ $val->first_name }} {{ $val->last_name }}
            </td>
            <td>
                {{ $val->mobile }} <br> {{ $val->phone }}
            </td>
            <td>
                @if(isset($val->dob))
                {{ date('m/d/Y', strtotime($val->dob))}}
                @endif
            </td>
            <td>
                {{ ucfirst($val->gender) }}
            </td>
            @if(Auth()->user()->view_ssn_hub ==1)
            <td>
                {{Common::formatSSN($val->ssn)??'-'}}
            </td>
            @endif
            <td>
                {{ Common::convertMDYTime($val->created_date)}} <br>
                @if(isset($val->users->first_name))
                {{ $val->users->first_name.' '.$val->users->last_name}}
                @endif
            </td>
            <td>
                {{ Common::convertMDYTime($val->updated_date)}} <br>
                @if(isset($val->usersUpdate->first_name))
                {{ $val->usersUpdate->first_name.' '.$val->usersUpdate->last_name}}
                @endif
            </td>
        </tr>
        @endforeach
        @endif

        @if(count($query) == 0)
        <tr class="txt-center">
            @if(Auth()->user()->view_ssn_hub ==1)
            <td colspan="9">No record available</td>
            @else
            <td colspan="8">No record available</td>
            @endif

        </tr>
        @endif
    </tbody>
</table>

<div class="pull-right hub_list_paginate pegination-margin" id="hub_list_paginate">
    {{ $query->links() }}
</div>

<script>
    var total = "{{ $query->total()}}";
    $('#total_record_id').html(total)
    $('#blank_div').attr('style','margin-top:25px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }
      $('body').on('click','#cboxId',function(e){
            var cbox = $('#cboxId').is(":checked");
            if(cbox){
                $('.cbox').prop("checked",true);
            }else{
                $('.cbox').prop("checked",false);
            }
        })
</script>