<table id="agn_token_id" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Webhook Url</th>
            <th>Authentication Type</th>
            <th>Username</th>
            <th>Password</th>
            <th>Token</th>
            
            <th>Created Date / Created By</th>
            <th>Updated Date / Updated By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $cnt = ($page * 50)-49
        @endphp

        @if(!empty($query[0]))
            @foreach($query as $val)

            
                <tr>
                   
                    <td>{{ $cnt++}}</td>
                    <td>{{ $val->webhook_url}}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $val->authentication_type)) }}</td>
                    <td>{{ $val->user_name}}</td>
                    <td>{{ $val->password}}</td>
                    <td>{{ $val->token}}</td>
                    <td>{{ date('m/d/Y h:i A',strtotime($val->created_date))}} <br> {{$val->users->first_name.' '. $val->users->last_name }}</td>

                    @php
                        $updateUserFirstName = "";
                        $updateUserLastName = "";
                    @endphp
                    @if(isset($val->updatedUser))
                    @php
                        $updateUserFirstName = $val->updatedUser->first_name;
                        $updateUserLastName = $val->updatedUser->last_name;
                    @endphp
                    @endif
                    <td>
                    @if($val->updated_date !="")    
                    {{ date('m/d/Y h:i A',strtotime($val->updated_date))}} <br> {{$updateUserFirstName.' '. $updateUserLastName }}
                @endif
                    </td>
                    <td>
                        <a onclick="editWebHook('{{ $val->id}}')"><i class="fa fa-edit"></i></a>
                        <a onclick="deleteWebHook('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                    </td>
                   
                </tr>

            @endforeach
        @endif

        @if(empty($query[0]))
            <tr>
                <td colspan="9">No record available</td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin webhook_generate_token">
            
            {{ $query->appends(request()->input())->links() }}
        </div>