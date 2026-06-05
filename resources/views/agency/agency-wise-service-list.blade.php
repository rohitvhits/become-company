
  <table class="table" id="notification-table">
    <thead>
        <th>No</th>
        <th>Type</th>
        <th>Name</th>
        <th>Action</th>
    </thead>
    <tbody>
  

        @if (!empty($query[0]))
        @php $i=1; @endphp
            @foreach ($query as $val)
                <tr>
                    <td>{{ $i++}}</td>
                    <td>{{ $val->type}}</td>
                    <td>{{ $val->name}}</td>
                    <td>
                        <a onclick="deleteService('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                        <a onclick="editService('{{ $val->id}}')" ><i class="fa fa-edit"></i></a>
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
 
<div class="pull-right pegination-margin pagination-service">
    
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>