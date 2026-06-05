@include('include/header')
@include('include/sidebar')
<style>
.page-title-main{
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0px 0 20px 0 ;
}
.table-width1 {
    background-color: #fff;
}
.notification-mail-tbl thead tr th:last-child{
    width: 15%;
}
.notification-mail-tbl thead tr th:nth-child(4){
    width: 15%;
}
.notification-mail-tbl thead tr{
    height: 46.03px;
}
</style>

<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Notification Email</h5>
            <div class="page-rightbtns">
                <a href="{{ route('notification-email.create') }}" class="btn btn-primary btn-sm btn-rounded btn-fw"><i
                        class="mdi mdi-plus"> </i> Add Notifaction Email</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table id="order-listing1" class="table table-bordered table-width1 notification-mail-tbl">
                    <thead>
                        <tr>

                            <th>#No</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (count($query) > 0)
                            <?php $i = 1; ?>

                            @foreach ($query as $value)
                                <tr>
                                    <td><?= $i++ ?></th>
                                    <td>{{ $value->title }}</td>
                                    <td>{{ $value->message }}</td>
                                    <td>{{ date('m-d-Y', strtotime($value->created_at)) }}</td>
                                    <td><a class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1 deletnotificationemail"
                                            href="javascript:void(0)" data-did="{{ $value->id }}"><i
                                                class="fa fa-trash"></i> Delete</a>

                                        <a href="{{ url('notification-email') }}/{{ $value->id }}/edit"
                                            class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1"><i
                                                class="mdi mdi-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                          
                        @endif

                    </tbody>
                </table>

                <div class="pull-right pegination-margin">
                    {{ $query->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).on("click", ".deletnotificationemail", function() {
            var id = $(this).attr('data-did');
            deleteNotificationEmail(id);
        });

        function deleteNotificationEmail(id) {
            var upUrl = "{{ route('notification-email.destroy', 'id') }}";
            var redirectUrl = "{{ url('notification-email') }}";
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to delete this Notification Email?",
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
                            toastr.success(r.msg);
                            window.location.href = redirectUrl;
                        }
                    });
                } else {
                    return false;
                }
            });
        }
    </script>


    @include('include/footer')
