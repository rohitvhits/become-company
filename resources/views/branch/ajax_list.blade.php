<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th width="5%">#</th>
                <th width="25%">Branch Name</th>
                <th width="15%">Status</th>
                <th width="20%">Created Date</th>
                <th width="15%">Created By</th>
                <th width="20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($branches as $key => $branch)
                <tr>
                    <td>{{ $branches->firstItem() + $key }}</td>
                    <td><strong>{{ $branch->branch_name }}</strong></td>
                    <td>
                        @if($branch->status == 1)
                            <span class="badge badge-success">Enable</span>
                        @else
                            <span class="badge badge-danger">Disable</span>
                        @endif
                    </td>
                    <td>{{ $branch->created_at ? $branch->created_at->format('m/d/Y h:i A') : '-' }}</td>
                    <td>{{ $branch->createdUsers->first_name ?? '' }} {{ $branch->createdUsers->last_name ?? '' }}</td>
                    <td>
                        @can('branch-edit')
                        <a type="button" class="btn btn-info mr-2 badge badge-info" onclick="openEditModal({{ $branch->id }})" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </a>
                        @endcan
                        @can('branch-delete')
                        <a type="button" class="btn btn-danger mr-2 badge badge-danger" style="background-color: #cb0b0b" onclick="deleteBranch({{ $branch->id }})" title="Delete">
                            <i class="mdi mdi-delete"></i>
                        </a>
                        @endcan
                        <label class="toggle-switch toggle-switch-success">
                            <input type="checkbox" data-last-status="{{ $branch->status }}" data-id="{{ $branch->id }}" id="row_last_status{{ $branch->id }}" name="status" value="1" @if($branch->status == '1') checked @endif onchange="statusUpdate({{ $branch->id }}, {{ $branch->status }})">
                            <span class="toggle-slider round"></span>
                        </label>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No branches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination pull-right pegination-margin">
        {{ $branches->links() }}
    </div>
</div>
