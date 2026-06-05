<style>
    .action-btns{
        padding-left:10px !important;
    }
</style>
<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>Message</th>
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
                    <td id="sms_type{{$val->id}}">{{ $val->type }}</td>
                    <td id="sms_msg{{$val->id}}">{{ $val->message }}</td>
                    {{-- <td></td> --}}
                    <td class="d-flex action-btns">
                       
                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="edit-sms-detail btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1"><i class="mdi mdi-pencil"></i></a>

                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="delete-sms-detail pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1"><i class="mdi mdi-delete"></i></a>
                    </td>
                
                </tr>
            @endforeach

        @endif
        @if (empty($query[0]))
                <tr>
                    <td colspan="12">No record available</td>
                </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>