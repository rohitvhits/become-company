@include('include/header')
<style type="text/css">
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

    .hide {
        display: none;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Insurance List</h5>
            <div class="page-rightbtns">
                <div>
                    @can('insurance-master-create')
                        <a href="{{ route('insurance-master.create') }}"
                            class="btn btn-primary btn-rounded btn-fw btn-sm showModalInsurance"><i class="mdi mdi-plus"> </i>
                            Add
                            Insurance</a>
                    @endcan
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

        <div class="row">
            <div class="col-12">
                <table id="order-listing1" class="table table-bordered table-width1">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Insurance Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="refreshDiv">
                        <?php if ($query->total() != 0) {
                            
                            $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                            foreach ($query as $key => $row) {  ?>
                        <tr id="<?php echo $row->id; ?>">
                            <td><?= '#' . ' ' . $i++ ?></td>
                            <td id="Insuranceid<?php echo $row->id; ?>"><?php echo $row->insurance_name; ?></td>
                            <td>
                                @can('insurance-master-edit')
                                    <a href="javascript:void(0);" data-eid="{{ $row->id }}"
                                        data-name="{{ $row->insurance_name }}"
                                        class="btn btn-success btn-sm btn-rounded editInsurance"
                                        id="editln<?php echo $row->id; ?>" title="Edit"><i class="fa fa-edit"></i></a>
                                @endcan
                                @can('insurance-master-delete')
                                    <a href="javascript:void(0);" data-did="{{ $row->id }}"
                                        class="btn btn-danger btn-rounded btn-sm delInsurance" title="Delete"><i
                                            class="fa fa-trash"></i></a>
                                @endcan
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>

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
    <!-- Insurance Start -->
    <div class="modal fade" id="InsuranceModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Insurance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" method="post" id="insuranceAdd">
                        @csrf
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Name<span class="error">*</span></label>
                            <input type="text" class="form-control" id="insurance_name" name="insurance_name"
                                placeholder="Enter Name" maxlength="50">
                            <span class="error-text name_error error"></span>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="addInsurance"
                                data-uid="">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Insurance End -->

    <script>
        $(document).ready(function() {
            $('#InsuranceModal').on('hidden.bs.modal', function() {
                $('#insuranceAdd')[0].reset();
            });
        })

        $(document).on("click", ".showModalInsurance", function(e) {
            e.preventDefault();
            $("#insurance_name").val('');
            $("#saveInsurance").attr('id', 'addInsurance');
            $("#addInsurance").text('Save');
            $("#ModalLabel").text('Add Insurance');
            $("#addInsurance").attr('data-uid', '');
            $(".name_error").html('');
            $("#InsuranceModal").modal('show');
        });

        $(document).on("click", "#addInsurance", function(e) {
            e.preventDefault();
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('meta[name=_token]').attr('content')
                },
                url: "{{ route('insurance-master.store') }}",
                type: 'POST',
                cache: false,
                data: $("#insuranceAdd").serialize(),
                beforeSend: function() {
                    //something before send
                },
                success: function(response) {
                    if (response.status == false) {
                        $.each(response.error, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        $("#InsuranceModal").modal('hide');
                        var totalRecord = '{{ $query->total() }}';
                        if (totalRecord == 0) {
                            $('#hidedis').addClass('hide');
                        }

                        var upUrl = "{{ route('insurance-master.show', 'id') }}";

                        var id = $(this).data('uid');
                        var fnUrl = upUrl.replace('id', id);

                        var appandRow = '<tr  id="' + response.data.id + '"><td># ' + response.data.id +
                            '</td><td>' + response.data.insurance_name +
                            '</td><td><a href="javascript:void(0);" class="btn btn-success btn-sm btn-rounded editInsurance" data-eid="' +
                            response.data.id + '" data-name="' + response.data.insurance_name +
                            '"  title="Edit"><i class="fa fa-edit"></i></a> <a href="javascript:void(0);" class="btn btn-danger btn-rounded btn-sm delInsurance" data-did="' +
                            response.data.id +
                            '"  title="Delete"><i class="fa fa-trash"></i></a></td></tr>';

                        $("#refreshDiv").prepend(appandRow);
                        toastr.success('Insurance added successfully');
                        $('#insuranceAdd')[0].reset();
                    }
                }
            });
        });


        $(document).on("click", ".editInsurance", function() {
            $("#insurance_name").val($(this).attr('data-name'));
            $("#addInsurance").attr('id', 'saveInsurance');
            $("#saveInsurance").text('Update');
            $("#ModalLabel").text('Update Insurance');
            $("#saveInsurance").attr('data-uid', $(this).data('eid'));
            $(".name_error").html('');
            $("#InsuranceModal").modal('show');
        });

        $(document).on("click", "#saveInsurance", function() {
            var upUrl = "{{ route('insurance-master.update', 'id') }}";

            var id = $(this).data('uid');
            var fnUrl = upUrl.replace('id', id);
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('meta[name=_token]').attr('content')
                },
                url: fnUrl,
                type: 'PUT',
                cache: false,
                data: $("#insuranceAdd").serialize(),
                beforeSend: function() {
                    //something before send
                },
                success: function(response) {
                    if (response.status == false) {
                        $.each(response.error, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        $("#InsuranceModal").modal('hide');
                        toastr.success('Insurance updated successfully');
                        $('#Insuranceid' + id).html(response.data.insurance_name);
                        $('#editln' + id).attr('data-name', response.data.insurance_name);

                        $('#insuranceAdd')[0].reset();
                    }
                }
            });
        });

        // Delete Code
        $(document).on("click", ".delInsurance", function() {
            var id = $(this).attr('data-did');
            deleteInsurance(id);
        });

        function deleteInsurance(id) {
            var upUrl = "{{ route('insurance-master.destroy', 'id') }}";
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
                            toastr.success('Insurance deleted successfully');
                            console.log(response.data);
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