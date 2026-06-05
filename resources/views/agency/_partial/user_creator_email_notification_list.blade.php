<table id="order-listing1" class="table table-bordered ">
        <thead>
            <tr>
                <th>No</th>
                <th>Email Notification Type</th>
                <th>Created Date/ Created By</th>
              
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $cnt =1;
            @endphp
            @if(count($user_creator_email_list) >0)
                @foreach($user_creator_email_list as $val)
                    <tr>
                        <td>{{ $cnt++ }}</td>
                        <td>{{ $val->data }}</td>
                        <td>{{ Common::convertMDYTime($val->created_date) }}<br>

                        {{$val->createdUserDetails->first_name.' '.$val->createdUserDetails->last_name}}
                        </td>
                        
                        <td>
                            <a href="javascript:void(0)" onclick="userEmailDelete('{{ $val->id}}')" title="Delete"><i class="fa fa-trash"></i></a>
                            <!-- <a href="javascript:void(0)" onclick="userEmailDelete('{{ $val->id}}')" title="Edit"><i class="fa fa-edit"></i></a> -->
                        </td>
                    </tr>
                @endforeach
            @endif

            @if(count($user_creator_email_list) ==0)
                <tr>
                    <td colspan="5">No record available</td>
                </tr>

            @endif
         
        </tbody>
    </table>