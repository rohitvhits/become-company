<table class="table table-bordered" id="order-listing" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th>Name</th>
            <th style="width:12%;">Type</th>
            <th style="width:12%;">Key</th>
            <th style="width:15%;">Created By</th>
            <th style="width:15%;">Created Date</th>
            <th style="width:10%;">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($query) && count($query) > 0)
        @php $i = ($query->currentPage() - 1) * $query->perPage() + 1; @endphp
        @foreach($query as $row)
        <tr id="row-{{ $row->id }}">
            <td>{{ $i++ }}</td>
            <td>{{ $row->name }}</td>
            <td>
                <span class="badge badge-primary text-capitalize">{{ $row->type ?? '-' }}</span>
            </td>
            <td>
                @if($row->key == '181')
                    <span class="badge badge-info">With MDO</span>
                @else
                    <span class="badge badge-secondary">All Service</span>
                @endif
            </td>
            <td>{{ $row->createdBy ? trim($row->createdBy->first_name . ' ' . $row->createdBy->last_name) : '-' }}</td>
            <td>{{ $row->created_date ? date('m/d/Y h:i A', strtotime($row->created_date)) : '-' }}</td>
            <td>
                <a href="javascript:void(0);" onclick="editRecord({{ $row->id }})" class="pull-left ml-1" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
                <a href="javascript:void(0);" onclick="deleteRecord({{ $row->id }})" class="pull-left ml-1" title="Delete">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="7"><center><b>Data not found</b></center></td>
        </tr>
        @endif
    </tbody>
</table>

@if(!empty($query) && count($query) > 0)
<div class="pull-right pegination-margin">
    {{ $query->links('pagination::bootstrap-4') }}
</div>
@endif

<script>
    $('#blank_div').attr('style','margin-top:15%')
</script>
