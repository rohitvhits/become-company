@include('include/header')
@include('include/sidebar')

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="content-wrapper">

        <!-- Page Heading -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Edit Notification Email</h5>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form class="user" action="{{ route('notification-email.update', $notificationEmailData->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body  pl-0">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-1">Title<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-user col-sm-11"
                                            id="title" name="title" value="{{$notificationEmailData->title ?? ""}}" 
                                            placeholder="Enter Title">
                                        <span class="col-sm-11 ml-auto pl-0" style="color:red" id="title">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                               
                                </div>
                                <div class="card-body  pl-0">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-1">Message<span
                                                class="text-danger">*</span></label>
                                        
                                        <textarea class="form-control form-control-user col-sm-11" rows="4" cols="50" id="message" name="message" >{{$notificationEmailData->message ?? ""}}</textarea>
                                        <span class="col-sm-11 ml-auto pl-0" style="color:red" id="message">
                                            @error('message')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                               
                                </div>
                                <div class="card-footer footer-btns">
                                    <a class="btn btn-primary" href="{{ route('notification-email.index') }}">Back</a>
                                    <input type="submit" id="submit" class="btn btn-primary">
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

@include('include/footer')
