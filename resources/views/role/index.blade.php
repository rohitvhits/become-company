@include('include/header')
@include('include/sidebar')
<style>
    .add-role {
        display: flex;
        justify-content: end;
    }

    .add-role a {
        height: 36px;
        border-radius: 50px;
        line-height: 17px;
    }

    .radius-50 {
        border-radius: 50px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>
<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Role Management</h5>
            <div class="page-rightbtns">
                <div class="add-role">
                    <a class="btn btn-primary btn-rounded btn-fw btn-sm" href="{{ route('roles.create') }}"><i class="mdi mdi-plus"> </i>Add
                        Role</a>
                </div>
            </div>
        </div>
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 5% !important;">#</th>
                                <th style="width: 5% !important;">Record#</th>
                                <th style="width: 10% !important;">Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($roles) > 0)
                                @php $i=1; @endphp
                                @if($page !="")
                                    @php 
                                        $i =($page *10)-9;
                                    @endphp
                                @endif
                                @foreach ($roles as $key => $role)
                                    <tr id="{{ $role->id }}">
                                        <td>{{ $i }}</td>
                                        <td><a href="{{ route('roles.show', $role->id) }}">#{{ $role['id'] }}</a></td>
                                        <td>{{ $role->name }}</td>
                                        
                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-right pagination-margin">
    {{ $roles->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".deletRole", function() {
        var id = $(this).attr('data-did');
        deleteRole(id);
    });

    function deleteRole(id) {
        var upUrl = "{{ route('roles.destroy', 'id') }}";
        Swal.fire({
            title: 'Are you sure?',
            text: "you want to delete this role?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: !1
        }).then((result) => {
            var url = upUrl;
            url = url.replace('id', id);
            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    url: url,
                    type: "DELETE",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(r) {
                        $("#" + id).remove();
                        console.log($("#" + id).remove());
                    }
                });
            } else {
                return false;
            }
        });
    }
</script>
@include('include/footer')