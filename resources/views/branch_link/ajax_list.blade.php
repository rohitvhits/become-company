<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Branch Name</th>
                <th>Agency</th>
                <th>Service</th>
                <th>Created Date</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($branchLinks as $key => $link)
                <tr>
                    <td>{{ $branchLinks->firstItem() + $key }}</td>
                    <td><strong>{{ $link->branch->branch_name ?? '-' }}</strong></td>
                    <td>{{ $link->agency->agency_name ?? '-' }}</td>
                    <td>{{ $link->service->name ?? '-' }} ({{ ($link->service->types) }}) </td>
                    <td>{{ $link->created_at ? $link->created_at->format('m/d/Y h:i A') : '-' }}</td>
                    <td>{{ $link->createdUsers->first_name ?? '' }} {{ $link->createdUsers->last_name ?? '' }}</td>
                    <td>
                        @can('branch-link-edit')
                        <a type="button" class="btn btn-info mr-2 badge badge-info" onclick="openEditLinkModal({{ $link->id }})" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </a>
                        @endcan
                        @can('branch-link-delete')
                        <a type="button" class="btn btn-danger mr-2 badge badge-danger" style="background-color: #cb0b0b" onclick="deleteBranchLink('{{ $link->id }}')" title="Delete">
                            <i class="mdi mdi-delete"></i>
                        </a>
                        @endcan
                        <input type="checkbox" id="is_val_mandatory_{{$link->id}}" name="is_val_mandatory" title="Mandatory" onchange="changeMandatoryVal('{{$link->id}}')" @if($link->is_val_mandatory == 1) checked @endif>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No branch links found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination pull-right pegination-margin">
        {{ $branchLinks->links() }}
    </div>
</div>
