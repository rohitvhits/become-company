<div class="tab-pane fade" id="visiting-detail-1" role="tabpanel" aria-labelledby="visiting-detail-tab">
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-hospital-o mr-2"></i>Visiting Aid Detail
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="openEditVisitingPopup()">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-tag mr-2 text-muted"></i>User Name</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2">
                                    @if(isset($visiting_client->app_user_key))
                                    {{ ($visiting_client->app_user_key)?substr ($visiting_client->app_user_key, -4):'-' }}
                                    @else
                                        -
                                    @endif
                                    
                                </span>
                            </dd>

                            <dt><i class="fa fa-key mr-2 text-muted"></i>Password</dt>
                            <dd>
                                <span class="badge badge-light px-3 py-2">
                                @if(isset($visiting_client->app_user_password))
                                {{ ($visiting_client->app_user_password)?substr ($visiting_client->app_user_password, -4):'-' }}
                                @else
                                    -
                                @endif
                                   
                                </span>
                            </dd>

                            @php
                            $status_new=0;
                                if(isset($visiting_client->status)){
                                    $status_new= $visiting_client->status;
                                }
                            @endphp
                            <dt><i class="fa fa-toggle-on mr-2 text-muted"></i>Enabled Visiting Aid &nbsp;</dt>
                            <input type="hidden" id="visiting_status" name="visiting_status" value="{{ $status_new }}">
                            <input type="hidden" id="visiting_app_detail" name="visiting_app_detail" value="@if(isset($visiting_client->app_user_key)){{ $visiting_client->app_user_key??'' }} @endif">
                            <input type="hidden" id="visiting_app_name" name="visiting_edit_app_name" value="@if(isset($visiting_client->app_user_password)) {{$visiting_client->app_user_password ?? ''}} @endif">
                            <dd>
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" name="enable_visiting" class="enable_visiting" @if(isset($visiting_client->status)){{ $visiting_client->status != 0 ? 'checked' : ''}} @endif>
                                    <span class="toggle-slider round"></span>
                                </label>
                            </dd>

                        </dl>
                    </div>
                    <div class="col-md-6">
                        
                        <div class="row" id="sync_visiting_details" style="display:@if(isset($visiting_client->status) && $visiting_client->status ==1) @else none @endif">
                            <!-- Caregiver Card -->
                            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="icon-wrapper bg-primary-light rounded-circle p-3 d-inline-block">
                                                <i class="fa fa-user-md fa-2x text-primary"></i>
                                            </div>
                                        </div>
                                        <h4 class="card-title text-center font-weight-bold">Fetch All Pending Medical</h4>
                                        
                                        <div class="pull-left text-center w-100">
                                           
                                            <a href="javascript:void(0)" id="syncVisitingPendingMedical" class="btn btn-primary btn-sm">
                                                <i class="fa fa-refresh mr-1"></i>Sync Employee
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HHA Sync Cards Section -->

<!-- HHA MDO Section -->

<style>
    /* Custom styles for improved UI */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-primary-light {
        background-color: rgba(0, 123, 255, 0.1);
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-secondary-light {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .border-left-primary {
        border-left: 4px solid #007bff;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107;
    }
    .agency-detail1 dt {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
    }
    .agency-detail1 dd {
        margin-bottom: 15px;
    }
    .card-title {
        font-size: 1.1rem;
    }
</style>
</div>