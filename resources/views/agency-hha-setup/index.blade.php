@include('include/header')
@include('include/sidebar')
<style>
  .custom-toggle-switch .switch {
    position: relative;
    display: inline-block;
    width: 53px;
    height: 28px;
  }

  .custom-toggle-switch .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .custom-toggle-switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .custom-toggle-switch .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .custom-toggle-switch input:checked+.slider {
    background-color: #2196F3;
  }

  .custom-toggle-switch input:focus+.slider {
    -webkit-box-shadow: 0 0 1px #2196F3;
    box-shadow: 0 0 1px #2196F3;
  }

  .custom-toggle-switch input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    transform: translateX(26px);
  }

  .custom-toggle-switch .slider.round {
    border-radius: 34px;
  }

  .custom-toggle-switch .slider.round:before {
    border-radius: 50%;
  }
</style>
<link href="{{ URL::to('/')}}/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<div class="main-panel">
  <div class="content-wrapper">
    
    <div class="card">
      <div class="row list-name">
        <div class="col-sm-6 card-title">
          <h4 class="card-title">Agencies List</h4>
        </div>
        <div class="col-sm-6">

          <a href="{{ URL::to('/') }}/nybest-agency" class="btn btn-danger btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i> Reset</a>

        </div>
        <div class="card-body table-responsive">
          <div class="row">
            <div class="col-12">

              <form method="get" action="{{ URL::to('/') }}/nybest-agency">
                <div class="">
                  <table id="" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Record#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Services</th>
                        <th>Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td></td>
                        <td></td>
                        <td><input autocomplete="off" type="text" class="form-control" name="agency_name" id="agency_name" value="<?php echo $agency_name ?>" autocomplete="off"></td>
                        <td><input type="text" autocomplete="off" class="form-control" name="email" id="email" value="<?php echo $email ?>" autocomplete="off"></td>
                        <td><input type="text" autocomplete="off" class="form-control" name="phone" id="phone" value="<?php echo  $phone ?>" autocomplete="off"></td>
                        <td><input type="text" autocomplete="off" class="form-control" name="city" id="city" value="<?php echo $city; ?>" autocomplete="off"></td>
                        <td></td>
                        <!-- 
                        <td><input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search"></td> 
                      -->

                      </tr>

                      @php $i = 1 + (($query->currentPage() - 1) * $query->perPage()); @endphp
                      @forelse ($query as $row)
                      <tr>
                        <th scope="row"><?= $i++ ?></th>
                        <td><a href="{{ url('/nybest-agency')}}/view/{{$row->id}}">{{ '#' . ' ' . $row->id }}</a></td>


                        <td>{{ucwords($row->agency_name) }}</td>

                        <td>{{ $row->email }}</td>

                        <td>{{ $row->phone }}</td>

                        <td>{{$row->city }}</td>

                        <td>
                          @php
                          $services = array();
                          @endphp
                          @if($row->service_expert_medicaid == "1")
                          @php $services[] = "Expert Medicaid"; @endphp
                          @endif
                          @if($row->service_md_appointment == "1")
                          @php $services[] = "MD Appointments"; @endphp
                          @endif
                          {{ implode(',',$services) }}

                        </td>

                        <?php /*
                        <td>
                          <a href="#" data-toggle="modal" data-target="#exampleModal-hha-update" data-whatever="@mdo" onclick="getTokan({{ $row->id}})" data-toggle="tooltip" title="HHA Setup"><i class="fa fa-edit"></i></a>
                          <div class="custom-toggle-switch">
                            <label class="switch m-0">
                              <input type="checkbox" class="hha-enable" data-id="{{ $row->id}}" name="enable" value="Y" <?= ($row->enable_disable == 'Y') ? 'checked' : ''; ?>>
                              <span class="slider round"></span>
                            </label>
                          </div>
                        </td>
                        */
                        ?>


                      </tr>
                      @empty
                      <tr>
                        <td colspan="9">
                          <center><b>Data not found</b></center>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </form>
              <div class="pull-right pegination-margin">
                {{$query->links("pagination::bootstrap-4")}}
              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- hha Start -->
    <div class="modal fade" id="exampleModal-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title documens" id="ModalLabel">Update HHA Setup</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-hha-setup') }}" name="adduser" method="post" id="formnew-hha-update">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="id" id="agency_id" value="">


              <div class="form-group">
                <label for="recipient-name" class="col-form-label">App Name<span style="color:red">*</span>:</label>
                <input type="text" name="app_name" class="form-control" id="app_name">
                <span id="app_name_error" style="color:red" class="error"></span>
              </div>

              <div class="form-group">
                <label for="recipient-name" class="col-form-label">App Key<span style="color:red">*</span>:</label>
                <input type="text" name="app_key" class="form-control" id="app_key">
                <span id="app_key_error" style="color:red" class="error"></span>
              </div>

              <div class="form-group">
                <label for="recipient-name" class="col-form-label">App Token<span style="color:red">*</span>:</label>
                <input type="text" name="app_token" class="form-control" id="app_token">
                <span id="app_token_error" style="color:red" class="error"></span>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-success" id="update-hha-setup-id">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- hha End -->
    <script src="{{URL::to('/') }}/assets/css/toastr/toastr.min.js"></script>
    <script>
      function validation() {

        var agency_name = $('#agency_name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var city = $('#city').val();
        if (agency_name == '' && email == '' && phone == '' && city == '') {
          alert('please select any one');
          return false;
        } else {
          return true;
        }
      }

      function getTokan(id) {
        $('#agency_id').val(id);
        $.ajax({
          async: false,
          global: false,
          url: "{{ url('agency-hha-tokan') }}",
          data: {
            'id': id,
          },
          success: function(response) {
            console.log(response.data.app_name);
            $('#app_name').val(response.data.app_name);
            $('#app_key').val(response.data.app_key);
            $('#app_token').val(response.data.app_token);
          }
        })
      }

      $('#update-hha-setup-id').click(function(e) {
        var app_name = $('#app_name').val();
        var app_key = $('#app_key').val();
        var app_token = $('#app_token').val();
        var cnt = 0;
        $('#app_name_error').html("");
        $('#app_key_error').html("");
        $('#app_token_error').html("");

        if (app_name.trim() == '') {
          $('#app_name_error').html("Required")
          cnt = 1;
        }
        if (app_key.trim() == '') {
          $('#app_key_error').html("Required")
          cnt = 1;
        }
        if (app_token.trim() == '') {
          $('#app_token_error').html("Required")
          cnt = 1;
        }

        if (cnt == 1) {
          return false;
        } else {
          var newForm = $('#formnew-hha-update')[0];
          var formData = new FormData(newForm);

          $.ajax({
            url: "{{ url('update-hha-setup') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
              console.log(response)
              toastr.success(response.error_msg);
              $('#formnew-hha-update')[0].reset();
              $('.close').click()
            },
            error: function(xhr, status, error) {
              toastr.error(xhr.responseJSON.error_msg);
            }


          })

        }
      })
      $(".hha-enable").change(function() {
        var status = "N";
        var id = $(this).attr("data-id");
        if (this.checked) {
          status = "Y";
        }

        $.ajax({
          async: false,
          global: false,
          url: "{{ url('agency-hha-enable-disable') }}",
          data: {
            'id': id,
            'status': status
          },
          success: function(response) {
            toastr.success(response.error_msg);
          },
          error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
          }
        })

      });

      toastr.options.closeButton = true;
      toastr.options.tapToDismiss = false;
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "500",
        "timeOut": "3000",
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
      };
    </script>

    @include('include/footer')