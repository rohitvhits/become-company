@if (in_array($user->user_type_fk, [5, 6]))
    @php
        $i = 1;
    @endphp
@else
    @php
        $i = 0;
    @endphp
@endif

<div class="table-responsive">
    <table id="" class="table recordtabletdwidth">
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Document</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($serviceList->currentPage() - 1) * $serviceList->perPage();
            @endphp

            @forelse ($serviceList as $row)
                   @php
                  $serviceName =  $row->services[0]->name;
                    
                    $status = $row->status == "Completed"
                    ? '<label class="badge badge-success">Completed</label>'
                    : '<label class="badge badge-warning">Pending</label>';

                    $document = $row->document == ""
                    ? '<input type="file" id="fileInput_' . $row->id . '"   class="form-control" /><span class="error document_upload_'.$row->id.'_error"></span>'
                    : '<a target="_blank" href="{{ url('/')}}dpp/{{ $row->id}}"><i class="fa fa-download"></i>Download</a>';
                  
                    $saveBtn = $row->document == ""
                    ?'<a type="button" id="uploadDocument" onclick="uploadDocumentServices(' . $row->id . ')"   class="btn btn-info btn-sm">save</a>'
                    :''

                   @endphp

                   

                <tr>
                    <td style="white-space:nowrap">{{ $i++ }}</td>
                    <td style="min-width:220px; white-space:nowrap">{{ $serviceName }}</td>
                    <td style="min-width:220px; white-space:nowrap">{!! $document !!}</td>
                    <td style="min-width:220px; white-space:nowrap">{!! $status !!}</td>
                    <td style="min-width:220px; white-space:nowrap">{!! $saveBtn !!}</td>

                   
                </tr>
            @empty
                <tr>
                    <td colspan="12">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pull-right pegination-margin log-pegination">
        {{ $serviceList->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>
