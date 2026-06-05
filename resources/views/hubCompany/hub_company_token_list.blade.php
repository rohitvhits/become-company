<table id="agn_token_id" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Token</th>
            <th>Notes</th>
            <th>Block Ip Address</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Action</th>

        </tr>
    </thead>
    <tbody>
        @php
            $cnt = ($page * 50)-49
        @endphp

        @if(!empty($token_list[0]))
            @foreach($token_list as $val)

            @php
            $out = strlen($val->notes) > 50 ? substr($val->notes,0,50)."..." : $val->notes;
            @endphp
                <tr>
                    <input type="hidden" name="token_name" id="token_name_{{ $val->id}}" value="{{ $val->notes_id}}">
                    <td>{{ $cnt++}}</td>
                    <td>{{ $val->token}}</td>
                    <td id="view_name{{$val->id }}" style="min-width:250px;max-width:250px;">{{ $out }}</td>
                    <td style="min-width:250px;max-width:250px;">{{ $val->ip_block}}</td>
                    <td>{{ date('m/d/Y h:i A',strtotime($val->created_date))}}</td>
                    <td>{{ $val->userDetails->first_name}} {{ $val->userDetails->last_name}}</td>
                    <td>
                        <!-- <a href="javascript:void(0)" onclick="tokenWiseDetailsShow('{{ $val->id }}')"><i class="fa fa-eye"></i></a> -->
                        <!-- <a href="javascript:void(0)" onclick="edit('{{ $val->id }}')"><i class="fa fa-edit"></i></a> -->
                    </td>
                </tr>

            @endforeach
        @endif

        @if(empty($token_list[0]))
            <tr>
                <td colspan="7">No record available</td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin agn_token_id">
            
            {{ $token_list->appends(request()->input())->links() }}
        </div>