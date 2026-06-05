@include('include/header')
@include('include/sidebar')
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
</style>
<div class="main-panel">
    <div class="content-wrapper ">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">User List</h5>
            <div class="page-rightbtns">
                <div>
                    @can('user-create')
                        <a href="<?php echo URL::to('/adduser'); ?>" class="btn btn-primary btn-sm btn-rounded btn-fw"><i
                                class="mdi mdi-plus"> </i> Add User</a>
                    @endcan
                    <a href="<?php echo URL::to('/'); ?>/user" class="btn btn-light btn-sm btn-rounded btn-fw ml-1"><i
                            class="mdi mdi-reload"></i>
                        Reset</a>
                    @can('user-export')
                        <a href="" class="btn btn-success btn-sm btn-rounded btn-fw ml-1" id="test_user"
                            onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                    @endcan
                    <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                            class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div" style="display: none;">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">First Name</label>
                                        <div class="col-sm-12 ">
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="first_name" id="first_name" value="<?php echo $first_name; ?>">
                                        </div>
                                        <span class="error ml-2" id="error_all"></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Last Name</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" autocomplete="off"
                                                name="last_name" id="last_name" value="<?php echo $last_name; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Email</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" autocomplete="off" name="email"
                                                id="email" value="<?php echo $email; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Record Type</label>
                                        <div class="col-sm-12">
                                            <select name="record_access" class="form-control">
                                                <option value="All">All</option>
                                                <option value="Caregiver" @if(isset($record_access) && $record_access =='Caregiver') selected @endif>Caregiver</option>
                                                <option value="Patient" @if(isset($record_access) && $record_access =='Patient') selected @endif>Patient</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Role</label>
                                        <div class="col-sm-12">
                                            <select name="roles_name" class="form-control">
                                                <option value="">Select Role</option>
                                                @forelse($role_list as $role)
    <option value="{{ $role->name }}" @if($role->name ==$roles_name) selected @endif>{{ $role->name }}</option>
@empty
    <option value="">No roles available</option>
@endforelse
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search"
                                            class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                            value="Search">
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
                            <th style="width:20px;">#</th>
                            <th>Record #</th>
                            <th nowrap>Record Type</th>
                           
                            <th nowrap>Full Name</th>
                           
                            <th nowrap>Email</th>
                            <th nowrap>Phone No</th>
                            <th nowrap>EXT No</th>
                            <th nowrap>Status</th>
                            <th nowrap>Role</th>
                            <!-- <th>Agency Name</th> -->
                            <th nowrap>Last User Login<br>Last Ip Address</th>
                         
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($query) > 0) { ?>
                        <?php $i = 1 + (($query->currentPage() - 1) * $query->perPage());
										foreach ($query as $row) {  ?>
                        <tr>
                            <th><?= $i++ ?></th>
                            <td nowrap><a
                                    href="<?php echo URL::asset('/'); ?>user-view/<?= $row->id ?>"><?= '#' . ' ' . $row->id ?></a>
                            </td>
                            <td nowrap><?= ucfirst($row->record_access) ?></td>
                            
                            
                            <td nowrap><?= ucfirst($row->first_name.' '.$row->last_name) ?></td>
                        
                            <td nowrap><?= $row->email ?></td>
                            <td nowrap><?= $row->phone ?></td>
                            <td nowrap><?= $row->ext ?></td>
                            <td nowrap><?php if($row->active =='active'){ echo "<span class='badge badge-success'>Active</span>"; }else{ echo "<span class='badge badge-danger'>".ucfirst($row->active)."</span>";}  ?></td>
                            <td nowrap>
                                <?php 
                                $names = array('badge-primary','badge-success','badge-info','badge-warning','badge-dark');
                                $tempCountercc = 0;
                                if(!empty($roles[$row->id][0])){
                                        foreach($roles[$row->id] as $val){?>
<span
class="badge <?php echo $names[$tempCountercc % count($names)]; ?>"><?php echo $val; ?></span>
                                        <?php 
                                        $tempCountercc++;
                                    }
                                    
                                }?>
                            </td>
                            <td><?php if ($row->last_login_at != '') {
                                echo date('m-d-Y h:i:s', strtotime($row->last_login_at)).'<br>'.$row->last_login_ip;
                            } ?></td>
                         
                        </tr>
                        <?php }
									} else { ?>
                        <tr>
                            <td colspan="12">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="pull-right pegination-margin">
                {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    <div class="modal fade" id="agencymodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Assign To</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/'); ?>/updateagencyRecord" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="prev" id="previd" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Agency User</label><span style="color:red">*</span>
                            <select name="newuser" id="selectId" class="form-control">
                                <option value="">Selecr Agency User</option>
                            </select>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="emcmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Assign To</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/'); ?>/updateEmcRecord" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="emcprev" id="emcprevid" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>EMC User</label><span style="color:red">*</span>
                            <select name="newemcuser" id="emcselectId" class="form-control">
                                <option value="">Selecr EMC User</option>
                            </select>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /* ..Start.. For page refresh when search data then show search area */
        $(document).ready(function() {
            var url = window.location.search;
            var arguments = url.split('?')[1];
            var searchText = arguments.split('=')[0];
            if (searchText == 'first_name') {
                $("#search-div").show();
            }
        });
        /* ..End.. For page refresh when search data then show search area */
        $("#searchbtns").click(function() {
            $("#search-div").toggle();
        });

        $(document).on("click", ".searchAppoinment", function() {

            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var record_access = $('select[name="record_access"]').val();
            var roles = $('select[name="roles_name"]').val();
            $("#error_all").html('');
            if (first_name == '' && last_name == '' && email == '' && record_access =='') {
                $("#error_all").html('Please enter any one search text');
                 return false;
            } else {

                first_name = first_name != null ? first_name : '';
                last_name = last_name != null ? last_name : '';
                email = email != null ? email : '';
                record_access = record_access != null ? record_access : '';
                roles = roles !=null?roles:"";

                var links = "<?php echo URL::to('/'); ?>/user?first_name=" + first_name + "&last_name=" + last_name +
                    "&email=" +
                    email + "&record_access="+record_access+"&roles_name="+roles ;
                window.location.href = links;
            }
        });
    </script>
    <script>
        function export_data() {

            var agency_fk = $('#agency_fk').val();
            var login_type = "Agency Rep";
            var user_type = "Agency";
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var record_access = $('select[name="record_access"]').val();
            var roles_name = $('select[name="roles_name"]').val();
            var temp1 = '<?php echo URL::to('/'); ?>/user-export?agency_fk=' + agency_fk + '&login_type=' + login_type +
                '&user_type=' + user_type + '&first_name=' + first_name + '&last_name=' + last_name + '&email=' + email+'&record_access='+record_access+"&roles_name="+roles_name;
            //  var temp = temp1.replace("http://", "https://");
            $('#test_user').attr("style", '');
            $('#test_user').attr("href", temp1);
        }


        function getAgencyUpdateRecord(id, agencyId) {

            $('#previd').val(id);
            $.ajax({
                async: false,
                global: false,
                url: "<?php echo URL::to('/'); ?>/getUserListByAgencyId/" + agencyId + '/' + id,
                type: "GET",
                success: function(response) {
                    console.log(response);

                    $('#selectId').html(response);
                }
            });


            $('#agencymodal').modal('show');

        }

        function getEMCUpdateRecord(id) {
            $('#emcprevid').val(id);
            $.ajax({
                async: false,
                global: false,
                url: "<?php echo URL::to('/'); ?>/getUserListByEmcId/" + id,
                success: function(response) {
                    $('#emcselectId').html(response);
                }
            });
            $('#emcmodal').modal('show');

        }
    </script>


    @include('include/footer')