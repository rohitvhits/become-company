<table class="table table-bordered" id="order-listing" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 20%;">Title</th>
            <th>Message</th>
            <th style="width: 8%;">Media</th>
            <th style="width: 10%;">Status</th>
            <th style="width: 12%;">Created Date</th>
            <th style="width: 10%;">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($query) && count($query) > 0)
        @php $i = ($query->currentPage() - 1) * $query->perPage() + 1; @endphp
        @foreach($query as $row)
        <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $row->title }}</td>
            <td>{!! Str::limit(strip_tags($row->description), 80) !!}</td>
            <td class="text-center">
                <span class="badge badge-info">{{ $row->media->count() }} files</span>
            </td>
            <td>
                @if($row->is_published == '1')
                <span class="badge badge-success">Published</span>
                @else
                <span class="badge badge-warning">Draft</span>
                @endif
            </td>
            <td>{{ date('m/d/Y h:i A', strtotime($row->created_date)) }}</td>
            <td>
                @can('announcement-master-edit')
                <a href="javascript:void(0);" onclick="viewAnnouncement({{ $row->id }})" class="pull-left ml-1"
                    title="View">
                    <i class="fa fa-eye"></i>
                </a>
                <a href="javascript:void(0);" onclick="editAnnouncement({{ $row->id }})" class="pull-left ml-1"
                    title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
                @if($row->is_published == '0')
                <a href="javascript:void(0);" onclick="publishAnnouncement({{ $row->id }})" class="pull-left ml-1"
                    title="Publish">
                    <i class="fa fa-check-circle text-success"></i>
                </a>
                @endif
                @endcan
                @can('announcement-master-delete')
                <a href="javascript:void(0);" onclick="deleteAnnouncement({{ $row->id }})" class="pull-left ml-1"
                    title="Delete">
                    <i class="fa fa-trash"></i>
                </a>
                @endcan
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="7">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        @endif
    </tbody>
</table>

@if(!empty($query) && count($query) > 0)
<div class="pull-right pegination-margin">
    {{ $query->links('pagination::bootstrap-4') }}
</div>
@endif