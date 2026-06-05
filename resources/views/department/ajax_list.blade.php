<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th width="5%">#</th>
                <th width="10%">Department Name</th>
                <th>Assigned Users</th>
                <th width="15%">Created Date</th>
                <th width="15%">Created By</th>
                <th width="20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $key => $department)
                <tr>
                    <td>{{ $departments->firstItem() + $key }}</td>
                    <td><strong>{{ $department->name }}</strong></td>
                    <td>
                        @if($department->users->count() > 0)
                            <small class="text-muted">Total: {{ $department->users->count() }} user(s)</small>
                        @else
                            <span class="text-muted"><i>No users assigned</i></span>
                        @endif
                    </td>
                    <td>{{ $department->created_at ? $department->created_at->format('m/d/Y h:i A') : '-' }}</td>
                    <td>{{ $department->createdUsers->first_name ??''}}  {{ $department->createdUsers->last_name??'' }}</td>
                    <td>
                        @can('department-edit')
                        <a type="button" class="btn btn-info mr-2 badge badge-info" onclick="openEditModal({{ $department->id }})" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </a>
                        @endcan
                        @can('department-delete')
                        <a type="button" class="btn btn-danger mr-2 badge badge-danger" style="background-color: #cb0b0b" onclick="deleteDepartment({{ $department->id }})" title="Delete">
                            <i class="mdi mdi-delete"></i>
                        </a>
                        @endcan
                        <label class="toggle-switch toggle-switch-success">
                            <input type="checkbox" data-last-status="{{ $department->status}}" data-id="{{ $department->id}}" id="row_last_status{{ $department->id}}" name="status" value="1" @if($department->status =='1') checked @endif onchange="statusUpdate({{$department->id}},{{$department->status}})">
                            <span class="toggle-slider round"></span>
                        </label>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No departments found.</td>
                </tr>
                @endforelse
        </tbody>
    </table>
    <div class="pagination pull-right pegination-margin">
            {{ $departments->links() }}
        </div>
</div>