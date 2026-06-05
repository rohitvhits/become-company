<table id="" class="table table-bordered">
    <thead>
        <th>ID</th>
        <th>App Name</th>
        <th>Token</th>
        <th>Referral Type</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Created By</th>
        <th>Action</th>
    </thead>
    <tbody>
        @php
            $i = ($page * 50) - 49;
        @endphp

        @forelse($appTokens as $token)
            <tr id="row-{{ $token->id }}">
                <td>{{ $i++ }}</td>
                <td>{{ $token->app_name }}</td>
                <td><span class="token-display">{{ $token->token }}</span></td>
                <td>{{ $token->name}}</td>
                <td>{{ $token->description ?? '-' }}</td>
                <td>{{ $token->created_at->format('m/d/Y h:i A') }}</td>
                <td>{{ $token->first_name }} {{ $token->last_name }}</td>
                <td>
                    @can('edit-app-token-generate')
                        <a data-toggle="tooltip" href="javascript:void(0)" data-id="{{ $token->id }}" class="btn-edit" title="Edit"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('delete-app-token-generate')
                        <a data-toggle="tooltip" href="javascript:void(0)" data-id="{{ $token->id }}" title="Delete" onclick="appTokenDelete('{{ $token->id }}')"><i class="fa fa-trash"></i></a>
                    @endcan
                   
                </td>
            </tr>
        @empty
        <tr>
            <td colspan="8">No record available</td>
        </tr>
        @endforelse
    </tbody>
</table>
<script>
    $('#blank_div').attr('style','margin-top:15%')
    </script>