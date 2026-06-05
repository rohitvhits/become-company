<div class="table-responsive">
    <table id="notification-table" class="table table-bordered table-width1">
        <thead>
            <th>No</th>
            <th>Email</th>
            <th>Patient</th>
            <th>Caregiver</th>
            <th nowrap>Service</th>
            <th>Discipline</th>
            <th>Action</th>
        </thead>
        <tbody>


            @if (!empty($query[0]))
            @php $i=1; @endphp
            @foreach ($query as $val)
            <tr>
                <td>{{ $i++}}</td>
                <td>{{ $val->email}}</td>
                <td>
                    @foreach($val->patients_exp as $key =>$patient)
                    <label class="badge badge-outline-{{$color[$key]}} ">{{$patient}}</label><br>
                    @endforeach
                </td>
                <td>
                    @foreach($val->caregivers_exp as $key =>$care)
                    <label class="badge badge-outline-{{$color[$key]}}">{{$care}}</label><br>
                    @endforeach
                </td>
                <td>
                    @if(isset($val->service_name))
                        @foreach($val->service_name as $key =>$service)
                            <label class="badge badge-{{$color[$key % count($color)]}}">{{$service}}</label><br>
                        @endforeach
                    @endif
                </td>
                <td>
                    @if(isset($val->discipline_id))
                        @foreach($val->discipline_id as $key =>$desc)
                            <label class="badge badge-{{$color[$key % count($color)]}} badge-pill">{{$desc}}</label><br>
                        @endforeach
                    @endif
                </td>
                <td>
                    <a class="mr-1" onclick="deleteNotificationEmail('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                    <a class="mr-1" onclick="editNotificationEmail('{{ $val->id}}')"><i class="fa fa-edit"></i></a>
                </td>
            </tr>

            @endforeach
            @endif
            @if (empty($query[0]))
            <tr class="">
                <td colspan="7"><center><b>No record available</b></center></td>
            </tr>
            @endif
        </tbody>

    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>