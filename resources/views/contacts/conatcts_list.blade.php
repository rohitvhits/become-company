 @include('include/header')
 @include('include/sidebar')
 <style>
     .directory-list-table td,
     .directory-list-table th {
         white-space: initial;
     }

     .directory-list-table th:nth-child(4) {
         width: 15%;
     }

 </style>
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('success') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
             @if (Session::has('error'))
                 <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('error') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
         </div>
         <div class="card">
             <div class="row list-name">
                 <div class="col-sm-6 card-title">
                     <h4 class="card-title">Directory List</h4>
                 </div>
                 <div class="col-sm-6">
                     <a href="javascript:void(0)" data-toggle="modal"
                         class="btn btn-danger btn-rounded btn-sm btn-fw pull-right" data-target="#exampleModal-5"
                         data-whatever="@mdo"><i class="mdi mdi-file-export"></i>Import</a>
                     <a href="{{ URL('directory/create') }}"
                         class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-plus"> </i>
                         Add Directory </a>
                 </div>
             </div>
             <div class="card-body">
                 <div class="row">
                     <div class="col-12">
                         <div class="table-responsive">
                             <form method="post" action="<?php echo URL::to('/'); ?>/search-agency"
                                 onsubmit="return validation();">
                                 <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                 <table id="" class="table table-bordered directory-list-table">
                                     <thead>
                                         <tr>
                                             <th>#</th>
                                             <th>Name</th>
                                             <th>Email</th>
                                             <th>Mobile No</th>
                                             <th>Image</th>
                                             <th>Action</th>

                                         </tr>
                                     </thead>
                                     <tbody>
                                         @php
                                             $i = 1;
                                         @endphp
                                         @if ($contacts_list->total() != 0)
                                             @if (isset($page) && $page != '')
                                                 $i = ($page *50)-49;
                                             @endif
                                             @foreach ($contacts_list as $val)

                                                 <tr>
                                                     <td>{{ $i++ }}</td>
                                                     <td>{{ $val->name }}</td>
                                                     <td>{{ $val->email }}</td>
                                                     <td>{{ $val->mobile }}</td>
                                                     <td>
                                                         @if ($val->image != '')
                                                             <img src="{{ asset('contacts') }}/{{ $val->image }}"
                                                                 style="width:50px;height:50px">
                                                         @endif
                                                     </td>

                                                     <td><a
                                                             href="{{ URL::to('/') }}/directory/edit/{{ $val->id }}"><i
                                                                 class="fa fa-edit"></i></a>
                                                         <a href="{{ url('/directory/delete') }}/{{ $val->id }}"
                                                             data-toggle="tooltip" title="Delete"
                                                             onclick="return confirm('Are you sure delete this directory?')"><i
                                                                 class="fa fa-trash"></i></a>


                                                     </td>
                                                 </tr>
                                             @endforeach
                                         @endif


                                         @if (count($contacts_list) == 0)
                                             <tr>
                                                 <td colspan="10">No record available</td>
                                             </tr>
                                         @endif
                                     </tbody>
                                 </table>
                             </form>
                             <div class="pull-right pegination-margin">
                                 {{ $contacts_list->links('pagination::bootstrap-4') }}
                             </div>

                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- Rate Start -->

     <!-- Rate End -->
     <div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Contacts List</h5>


                     <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="appps_id">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form class="forms-sample" name="adduser" method="post" id="formnew">
                         <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                         <div class="form-group">
                             <label for="message-text" class="col-form-label">Upload CSV<span
                                     style="color:red">*</span>:</label>
                             <input type="file" class="form-control" id="timeidnew" name="images">
                             <span class="error mt-2 text-danger" id="images_error" for="file_name"></span>
                         </div>

                         <div class="form-group">
                             <p>Click here to download the <a href="{{ URL::to('/contacts.csv') }}">sample file.</a>
                             </p>
                         </div>


                         <div class="modal-footer">
                             <div class="dot-opacity-loader" id="loaderss_id" style="display:none">
                                 <span></span>
                                 <span></span>
                                 <span></span>
                             </div>
                             <button type="button" onclick="getSubmit()" id="seacu"
                                 class="btn btn-success">Save</button>
                             <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <div class="modal fade" id="exampleModal-import" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Contacts Update</h5>


                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>

                 <form action="<?php echo URL::to('/'); ?>/directory/contacts-import" method="post"
                     enctype="multipart/form-data" id="submitId">
                     <input type="hidden" name="order_data" value="" id="order_data">
                     <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                     <div class="modal-body" id="formnewNN">

                     </div>
                     <div class="modal-footer">
                         <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                         <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>


                     </div>

                 </form>
             </div>
         </div>
     </div>


     @include('include/footer')
     <script>
         function getSubmit() {
             $('#loaderss_id').attr('style', 'display:block');
             var agency_ids = $('#agency_ids').val();
             var fimagesG = $('input[name="images"]').prop('files');
             var cnt = 0;
             $('#images_error').html("");
             $('#agency_error').html("");

             if (fimagesG.length == 0) {
                 $('#images_error').html("Required");
                 cnt = 1;
             } else {
                 var FileUploadPath = fimagesG[0].name;
                 var Extension = FileUploadPath.substring(
                     FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
                 if (Extension == 'xlsx' || Extension == 'csv' || Extension == 'xls') {

                 } else {
                     $('#images_error').html("Only csv or excel file allowed");
                     cnt = 1;

                 }
             }

             if (cnt == 1) {
                 return false;
             } else {
                 var foms = $('#formnew')[0];
                 var formData = new FormData(foms);
                 formData.append("_token", "<?php echo csrf_token(); ?>");

                 $.ajax({
                     async: false,
                     global: false,
                     processData: false,
                     contentType: false,
                     type: "POST",
                     url: "<?php echo URL::to('/directory/importdata'); ?>",
                     data: formData,
                     success: function(res) {

                         $('#seacu').attr('data-target', "#exampleModal-import");
                         $('#seacu').attr('data-toggle', "modal");
                         $('#formnewNN').html(res);

                         setTimeout(function(e) {
                             $('#loaderss_id').attr('style', 'display:none');
                         }, 1000);
                         $('#appps_id').click();
                     }
                 })
             }
         }
         $('#submitId').submit(function(e) {
             $('#row_error').html("");
             var selected = [];
             var selected_data = [];
             $.each($(".selectvalues option:selected"), function() {
                 selected.push($(this).val());
                 if ($(this).val() != "") {
                     selected_data.push($(this).val());
                 }
             });
             console.log(selected_data.length);

             $('#order_data').val(selected.join());

             if (selected_data.length < 3) {
                 $('#row_error').html('Required.');
                 return false;
             }



         });
     </script>
