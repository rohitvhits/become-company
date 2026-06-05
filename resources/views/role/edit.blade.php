@include('include/header')
@include('include/sidebar')
<style>
    .footer-btns {
        display: flex;
        justify-content: space-between;
    }

    .table-check {
        padding-left: 10px;
    }

  

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    td.row_td {
    padding: 0 5px 0px 5px;
    padding-left: 25px;
}
</style>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="content-wrapper">
        <!-- Page Heading -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Edit Role</h5>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form class="user" action="{{ route('roles.update', $role->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body pl-0">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-1">Name<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-user col-sm-11"
                                            id="name" name="name" value="{{ $role->name }}" maxlength="25"
                                            aria-describedby="nameHelp" placeholder="Enter Name">
                                        <span class="col-sm-11 ml-auto pl-0" style="color:red" id="name_error">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="card-body p-0">
                                        <h4 class="mb-2 card-title">Permission<span class="text-danger">*</span></h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-gray" width="100%"
                                                cellspacing="0">
                                                <tbody>
                                                    @php $i=1; @endphp
                                                    @foreach ($permission as $key => $role)
                                                    @php 
                                                        $divideVal = count($role['value'])/4;
                                                        $checkCount = count($role['value'])-1;
                                                    @endphp
                                                        <tr>
                                                            <td rowspan="{{ceil($divideVal)}}" style="background: #dadcdf4d;"><strong>{{ ucwords(str_replace('-', ' ', $role['module_name'])) }}</strong>
                                                            </td>

                                                            @for ($i = 0; $i < 51; $i++)
                                                                @if (!empty($role['value'][$i]))
                                                                @if ($i == 4)
                                                                <tr>
                                                                @endif
                                                                    <td class="row_td" style="white-space: nowrap;" colspan="{{$checkCount==$i ? (13-ceil($divideVal)):''}}">
                                                                        <div class="form-check custom-check table-check">
                                                                        <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input checkinput" name="permission[]" type="checkbox" id="permission" value="{{ isset($role['value'][$i]->id) ? $role['value'][$i]->id : '' }}" @if (in_array($role['value'][$i]->id, $rolePermissions)) checked @endif>
                                                                        {{ isset($role['value'][$i]->name) ? ucwords(str_replace('-', ' ', $role['value'][$i]->name)) : '' }}
                                                                        <i class="input-helper"></i></label>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                @if ($i == 3 || $i == 7 || $i == 11 || $i == 15 || $i == 19 || $i ==23 || $i ==27 || $i ==31 || $i ==35  || $i ==39  || $i ==43 || $i ==47 || $i ==51)
                                                                </tr>
                                                                @endif
                                                            @endfor
                                                        </tr>
                                                        @php $i++; @endphp
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer footer-btns">
                                    <a class="btn btn-primary" href="{{ route('roles.index') }}">Back</a>
                                    <button type="submit" id="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->
<script>
    $('#name').keypress(function(e) {
        var charCode = (e.which) ? e.which : event.keyCode;
        if (!String.fromCharCode(charCode).match(/^[a-zA-Z ]+$/))
            return false;
    });
    $('#submit').on('click', function() {
        temp = 0;
        var name = $('#name').val();
        var permission = $("[name='permission[]']:checked").length;

        if (name.trim() == '') {
            $('#name_error').html("Please enter Name");
            temp++;
        } else {
            $('#name_error').html("");
        }
        if (permission == 0) {
            $('#permission_error').html("Please select Permission");
            temp++;
        } else {
            $('#permission_error').html("");
        }
        if (temp == 0) {
            return true;
        } else {
            return false;
        }
    });
</script>
@include('include/footer')
