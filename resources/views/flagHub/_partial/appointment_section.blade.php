<div class="tab-pane tabs__toggle--active" id="appointment-section">
    <div class="page-title-main">
        <h5 class="mb-0 font-weight-bold">Flag Hub Records List</h5>
    </div>
    <div class="row">
        <div class="col-12">
            <div class=" card search-card1" id="search-div">
                <div class="card-body">
                    <form method="get" id="formsubmit">
                        @csrf
                        <div class="row">
                           
                          
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">ID</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Status</label>
                                         <div class="col-sm-12">
                                            <select name="status" class="form-control" id="status">
                                                <option value="">Select Status</option>
                                                <option value="active">Active</option>
                                                <option value="deactivated">Deactivated</option>
                                            </select>
                                         </div>
                                     </div>
                                 </div>
                                     <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12">Company</label>
                                            <div class="col-sm-12">
                                                <select name="agency_fk[]" id="agency_fk"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">
                                                    <?php foreach ($agencyList as $rwAgency) { ?>
                                                    <option value="<?php echo $rwAgency->id; ?>">
                                                        <?php echo $rwAgency->agency_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Name</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Mobile / Phone</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Email</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="email" id="email" value="">
                                            </div>
                                        </div>
                                    </div>
                             <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">SSN</label>
                                                <div class="col-sm-12">
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="ssn" id="ssn" value="">
                                                </div>
                                            </div>
                                </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Created Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="created_date" class="datepickernn form-control" id="created_date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Created By</label>
                                    <div class="col-sm-12">
                                        @if(!empty($agency_user_list[0]))
                                        <select name="created_by" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="created_by">
                                            <option value="">Select Created By</option>
                                            @foreach($agency_user_list as $val)
                                            <option value="{{ $val->id}}">{{ $val->first_name}} {{ $val->last_name}}</option>

                                            @endforeach

                                        </select>
                                        @else
                                        <input type="text" name="created_by_ny" id="created_by_ny">
                                        <input type="hidden" name="created_by_ny_id" id="created_by_ny_id">
                                        <input type="hidden" name="created_by_ny_name" id="created_by_ny_name">

                                        @endif
                                    </div>
                                </div>
                            </div>
                         
                        </div>

                        <div class="search-main1">
                            <div class="search-inner">
                                <div>
                                    <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                    <a href="javascript::void();" onclick="resetHubRecords()" class="btn btn-light btn-rounded btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
                                    <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class=" card">
                <div class="card-body compact-view">
                   
                    <div class="col-12">
                        <span id="resp"></span>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>