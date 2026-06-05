 @include('include/header')
 @include('include/sidebar')

 <style type="text/css">
     .error {
         color: Red;
     }

     .hide {
         display: none;
     }

     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 {
         background-color: #fff;
     }

     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }

     .search-main1 {
         border-top: 1px solid #eeeeee;
         margin-left: -20px;
         margin-right: -20px;
     }

     .search-btn1,
     .search-btn1:hover,
     .search-btn1:active,
     .search-btn1:focus {
         background: #007bff !important;
         border: #007bff !important;
         border-radius: 20px;
         height: 36px;
     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .search-card1 {
         margin-bottom: 20px;
     }

     .search-card1 .form-group {
         margin-bottom: 0.5rem;
     }

     .search-card1 label {
         margin-bottom: 0;
     }

     .search-card1 .card-body {
         padding-bottom: 10px;
     }

     .search-card1 input[type=text] {
         border-radius: 4px;
         border-color: #aaa;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }
 </style>
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Language List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('language-add')
                     <a href="{{ route('language.create') }}" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1 showModalLang"><i class="mdi mdi-plus"> </i> Add Language </a>
                     @endcan
                     <a href="<?php echo URL::to('/'); ?>/language" class="btn btn-light btn-sm btn-rounded btn-fw"><i class="mdi mdi-reload"></i>
                         Reset</a>

                     <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i class="fa fa-search"></i></button>
                 </div>
             </div>
         </div>

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

         <div class="row ">
             <div class="col-sm-12">
                 <div class="card search-card1" id="search-div" style="display: none;">
                     <div class="card-body">
                         <form method="get" id="formsubmit">
                             <input type="hidden" name="_token" value="T2fdzK1ShOFrIaDGtfR43XwT91A6Ahjq88isXJeQ">
                             <div class="row">
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Language Name</label>
                                         <div class="col-sm-12 ">
                                             <input autocomplete="off" type="text" class="form-control" name="name" id="language_name" value="{{ $name }}">
                                             <span id="language_error" class="error mt-2 text-danger"></span>
                                         </div>
                                        
                                     </div>
                                 </div>
                             </div>
                             <div class="search-main1">
                                 <div class="search-inner">
                                     <div>
                                         <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                     </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>


         <div class="row">
             <div class="col-12">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Name</th>
                             <th></th>
                         </tr>
                     </thead>
                     <tbody id="refreshDiv">

                         <?php if ($query->total() != 0) {
                                $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                                foreach ($query as $key => $row) {  ?>
                                 <tr id="<?php echo $row->id; ?>">

                                     <td><?= '#' . ' ' . $row->id ?></td>
                                     <td id="langid<?php echo $row->id; ?>"><?php echo $row->name; ?></td>
                                     <td>
                                         @can('language-edit')
                                         <a href="javascript:void(0);" data-eid="{{ $row->id }}" data-name="{{ $row->name }}" class="btn btn-success btn-sm btn-rounded editLanguage" id="editln<?php echo $row->id; ?>" title="Edit"><i class="fa fa-edit"></i></a>
                                         @endcan
                                         @can('language-delete')
                                         <a href="javascript:void(0);" data-did="{{ $row->id }}" class="btn btn-danger btn-rounded btn-sm delLanguage" title="Delete"><i class="fa fa-trash"></i></a>
                                         @endcan
                                         <a href="{{ route('language.show', $row->id) }}" data-lid="{{ $row->id }}" class="btn btn-dark btn-sm btn-rounded logLanguage" id="logln<?php echo $row->id; ?>" title="Log List"><i class="fa fa-list"></i></a>
                                     </td>
                                 </tr>
                         <?php }
                            }  ?>

                         <tr id="hidedis" class=" @if ($query->total() != 0) hide @else @endif">
                             <td colspan="12">
                                 <center><b>Data not found</b></center>
                             </td>
                         </tr>
                     </tbody>
                 </table>

                 <div class="pull-right pegination-margin">
                     {{ $query->links('pagination::bootstrap-4') }}
                 </div>
             </div>
         </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     <!-- Language Start -->
     <div class="modal fade" id="languageModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Add Language</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form class="forms-sample" name="adduser" method="post" id="languageAdd">
                         @csrf
                         <div class="form-group">
                             <label for="message-text" class="col-form-label">Name<span class="error">*</span></label>
                             <input type="name" class="form-control" id="name" name="name" placeholder="Enter Name" maxlength="50">
                             <span class="error-text name_error error"></span>

                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-success" id="addLanguage" data-uid="">Save</button>
                             <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <!-- Language End -->

     <script>
         /* ..Start.. For page refresh when search data then show search area */
         $(document).ready(function() {
             var url = window.location.search;
             var arguments = url.split('?')[1];
             var searchText = arguments.split('=')[0];
             if (searchText == 'language_name') {
                 $("#search-div").show();
             }
         });
         /* ..End.. For page refresh when search data then show search area */
         $("#searchbtns").click(function() {
             $("#search-div").toggle();
         });
         $(document).on("click", ".searchAppoinment", function() {
             var language_name = $('#language_name').val();

             if (language_name == '') {
                 $("#language_error").html('Please enter Language Name');
                 return false;
             } else {
                 language_name = language_name != null ? language_name : '';
                 var links = "<?php echo URL::to('/'); ?>/language?language_name=" + language_name + "";
                 window.location.href = links;
             }
         });
     </script>

     <script>
         //  function export_data() {

         //    var language_name = $('#language_name').val();
         //    var email = $('#email').val();
         //    var phone = $('#phone').val();
         //    var city = $('#city').val();
         //    var temp1 = '<?php echo URL::to('/'); ?>/doctor/doctor-export?full_name=' + language_name + '&email=' + email + '&phone=' + phone;
         //    //  var temp = temp1.replace("http://", "https://");
         //    $('#test_agency').attr("style", '');
         //    $('#test_agency').attr("href", temp1);
         //  }
     </script>
     <script>
         $(document).ready(function() {
             $('#languageModal').on('hidden.bs.modal', function() {
                 $('#languageAdd')[0].reset();
             });
         })

         

         $(document).on("click", ".showModalLang", function(e) {
            e.preventDefault();
             $("#name").val('');
             $("#saveLanguage").attr('id', 'addLanguage');
             $("#addLanguage").text('Save');
             $("#ModalLabel").text('Add Language');
             $("#addLanguage").attr('data-uid', '');
             $(".name_error").html('');
             $("#languageModal").modal('show');
         });

         $(document).on("click", "#addLanguage", function(e) {
            e.preventDefault();
             $.ajax({
                 headers: {
                     'X-CSRF-Token': $('meta[name=_token]').attr('content')
                 },
                 url: "{{ route('language.store') }}",
                 type: 'POST',
                 cache: false,
                 data: $("#languageAdd").serialize(),
                 beforeSend: function() {
                     //something before send
                 },
                 success: function(response) {
                     if (response.status == false) {
                         $.each(response.error, function(prefix, val) {
                             $('span.' + prefix + '_error').text(val[0]);
                         });
                     } else {
                         $("#languageModal").modal('hide');
                         var totalRecord = '{{ $query->total() }}';
                         if (totalRecord == 0) {
                             $('#hidedis').addClass('hide');
                         }

                         var upUrl = "{{ route('language.show', 'id') }}";

                         var id = $(this).data('uid');
                         var fnUrl = upUrl.replace('id', id);

                           var appandRow = '<tr  id="' + response.data.id + '"><td># ' + response.data.id + '</td><td>' + response.data.name + '</td><td><a href="javascript:void(0);" class="btn btn-success btn-sm btn-rounded editLanguage" data-eid="' + response.data.id + '" data-name="' + response.data.name + '"  title="Edit"><i class="fa fa-edit"></i></a> <a href="javascript:void(0);" class="btn btn-danger btn-rounded btn-sm delLanguage" data-did="' + response.data.id + '"  title="Delete"><i class="fa fa-trash"></i></a>  <a href="' + fnUrl + '" data-lid="' + response.data.id + '" class="btn btn-dark btn-sm btn-rounded logLanguage" id="logln' + response.data.id + '" title="Log List"><i class="fa fa-list"></i></a></td></tr>';

                           $("#refreshDiv").prepend(appandRow);
                           toastr.success('Language added successfully');
                         $('#languageAdd')[0].reset();
                     }
                 }
             });
         });

         $(document).on("click", ".editLanguage", function() {
             $("#name").val($(this).attr('data-name'));
             $("#addLanguage").attr('id', 'saveLanguage');
             $("#saveLanguage").text('Update');
             $("#ModalLabel").text('Update Language');
             $("#saveLanguage").attr('data-uid', $(this).data('eid'));
             $(".name_error").html('');
             $("#languageModal").modal('show');

         });
         $(document).on("click", "#saveLanguage", function() {
             var upUrl = "{{ route('language.update', 'id') }}";

             var id = $(this).data('uid');
             var fnUrl = upUrl.replace('id', id);
             $.ajax({
                 headers: {
                     'X-CSRF-Token': $('meta[name=_token]').attr('content')
                 },
                 url: fnUrl,
                 type: 'PUT',
                 cache: false,
                 data: $("#languageAdd").serialize(),
                 beforeSend: function() {
                     //something before send
                 },
                 success: function(response) {
                     if (response.status == false) {
                         $.each(response.error, function(prefix, val) {
                             $('span.' + prefix + '_error').text(val[0]);
                         });
                     } else {
                         $("#languageModal").modal('hide');
                         toastr.success('Language updated successfully');
                         $('#langid' + id).html(response.data.name);
                         $('#editln' + id).attr('data-name', response.data.name);
                        
                         $('#languageAdd')[0].reset();
                     }
                 }
             });
         });

         $(document).on("click", ".delLanguage", function() {
             var id = $(this).attr('data-did');
             deleteLanguage(id);
         });

         function deleteLanguage(id) {
             var upUrl = "{{ route('language.destroy', 'id') }}";
             Swal.fire({
                 title: 'Are you sure?',
                 text: "you want to delete this record?",
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
                         success: function(response) {
                             $("#" + id).remove();
                             toastr.success('Language deleted successfully');

                             if (response.data == 0) {
                                 $('#hidedis').removeClass('hide');
                             }
                         }
                     });
                 } else {
                     return false;
                 }
             });
         }
     </script>

     @include('include/footer')