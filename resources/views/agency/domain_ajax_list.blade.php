<style>
    .action-btns{
        padding-left:10px !important;
    }
</style>
<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Domain</th>
            <th>Created Date</th>
            <th>Action</th>

        </tr>
    </thead>
    <tbody>
        @php
            $i = $page * 50 - 49;
        @endphp
        @if (!empty($query[0]))
            @foreach ($query as $val)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td id="domain{{ $val->id}}">{{ $val->domain }}</td>
                    <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
                    <td class="d-flex action-btns">
                        @can('agency-edit-domain')
                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="edit-detail btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1"><i class="mdi mdi-pencil"></i></a>
                        @endcan

                        @can('agency-delete-domain')
                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="delete-detail pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1"><i class="mdi mdi-delete"></i></a>
                        @endcan
                    </td>
                
                </tr>
            @endforeach

        @endif
        @if (empty($query[0]))
                <tr>
                    <td>No record available</td>
                </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>