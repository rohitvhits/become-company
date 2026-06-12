<table class="table table-bordered table-hover mb-0">
    <thead class="thead-light">
        <tr>
            <th style="width:50px;">#</th>
            <th>Company Name</th>
            <th>Domain</th>
            <th style="width:100px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($query as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->domainConfig->company_name ?? '—' }}</td>
                <td>{{ $row->domainConfig->domain ?? '—' }}</td>
                <td>
                    <a href="javascript:void(0)"
                        class="editAgencyWiseCompany btn btn-warning btn-sm btn-rounded"
                        data-id="{{ $row->id }}"
                        data-domain-config-id="{{ $row->domain_config_id }}">
                        <i class="mdi mdi-square-edit-outline"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-3">No company assigned yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>
