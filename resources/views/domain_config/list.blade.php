@include('include/header')
@include('include/sidebar')

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .color-swatch {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid #ccc;
        vertical-align: middle;
        margin-right: 6px;
    }
    .logo-thumb {
        height: 40px;
        max-width: 100px;
        object-fit: contain;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Company Master</h5>
            <a href="{{ route('domain-config.create') }}" class="btn btn-primary btn-sm px-4">
                <i class="fa fa-plus mr-1"></i> Add New
            </a>
        </div>
        <hr />

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Domain</th>
                                <th>Company Name</th>
                                <th>Logo</th>
                                <th>Title</th>
                                <th>Theme Color</th>
                                <th>Login BG</th>
                                <th style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configs as $i => $config)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $config->domain }}</td>
                                <td>{{ $config->company_name ?? '—' }}</td>
                                <td>
                                    @if($config->logo)
                                        <img src="{{ asset($config->logo) }}" class="logo-thumb" onerror="this.style.display='none'">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $config->title }}</td>
                                <td>
                                    <span class="color-swatch" style="background:{{ $config->theme_color }}"></span>
                                    {{ $config->theme_color }}
                                </td>
                                <td>
                                    <span class="color-swatch" style="background:{{ $config->login_bg }}"></span>
                                    {{ $config->login_bg }}
                                </td>
                                <td>
                                    <a href="{{ route('domain-config.edit', $config->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                        onclick="confirmDelete({{ $config->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-info-circle mr-1"></i> No domain configs found. <a href="{{ route('domain-config.create') }}">Add one now.</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this domain config?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '/domain-config/' + id;
    $('#deleteModal').modal('show');
}
</script>

@include('include/footer')
