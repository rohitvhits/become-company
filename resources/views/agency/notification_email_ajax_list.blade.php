<style>
   

</style>

  <table class="table" id="notification-table">
    <thead>
        <th>No</th>
        <th>Email</th>
        <th>Patient</th>
        <th>Caregiver</th>
        <th>Service</th>
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
                    <td>{{ $val->patients}}</td>
                    <td>{{ $val->caregivers}}</td>
                    <td>{{ $val->service_name}}</td>
                    <td>{{ $val->discipline_id}}</td>
                    <td>
                        <a onclick="deleteNotificationEmail('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                        <a onclick="editNotificationEmail('{{ $val->id}}')" ><i class="fa fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
        @if (empty($query[0]))
            <tr>
                <td colspan="5">No record available</td>
            </tr>
        @endif
    </tbody>

  </table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>