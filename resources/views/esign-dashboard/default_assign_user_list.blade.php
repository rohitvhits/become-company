<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Assigned Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($list) && count($list) > 0)
                @foreach($list as $key => $val)
                <tr>
                    <td>{{ $list->firstItem() + $key }}</td>
                    <td>{{ $val->first_name }} {{ $val->last_name }}</td>
                    
                    <td>{{ Common::convertMDY($val->created_at)}}</td>
                    <td>
                        <a class="btn-remove-assign-user" data-id="{{ $val->id }}"> <i class="mdi mdi-delete"></i></a>
                        
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">No users assigned.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@if(!empty($list) && count($list) > 0)
    <div class="d-flex justify-content-center">
        {!! $list->links() !!}
    </div>
@endif
