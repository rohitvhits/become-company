<!-- AlayaCare Detail Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-cloud mr-2"></i>AlayaCare Detail
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        @can('update-alayacare-details')
                        <button class="btn btn-outline-light btn-sm edit-alaycare-details" style="margin-top:1px;height:30px;border-radius:5px" data-whatever="@mdo" href="javascript:void(0)">
                            <i class="fa fa-edit mr-1"></i>Edit
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal agency-detail1">
                            <dt><i class="fa fa-link mr-2 text-muted"></i>URL</dt>
                            <dd>
                                <span id="alaycare_url_id" class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->alayacare_url != "") ? $agencyDetails->alayacare_url : 'N/A' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-user mr-2 text-muted"></i>User Name</dt>
                            <dd>
                                <span id="alaycare_username_id" class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->alaycare_status == 1 && $agencyDetails->alaycare_username != "") ? $agencyDetails->alaycare_username : 'N/A' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-lock mr-2 text-muted"></i>Password</dt>
                            <dd>
                                <span id="alaycare_password_id" class="badge badge-light px-3 py-2">
                                    {{ ($agencyDetails->alaycare_status == 1 && $agencyDetails->alaycare_password != "") ? $agencyDetails->alaycare_password : 'N/A' }}
                                </span>
                            </dd>

                            <dt><i class="fa fa-toggle-on mr-2 text-muted"></i>Enabled AlayaCare</dt>
                            <dd>
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" name="alaycare-btn" class="alaycare-btn" {{ $agencyDetails->alaycare_status != 0 ? 'checked' : '' }}>
                                    <span class="toggle-slider round"></span>
                                </label>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6" id="hide_show_alayacare" style="display:@if($agencyDetails->alaycare_status == 1) @else none @endif">
                        <div class="alert alert-info border-left-primary mb-3">
                            <i class="fa fa-info-circle mr-2"></i>
                            <strong>AlayaCare Integration Status:</strong> Active - Sync your data below
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AlayaCare Sync Cards Section -->
<div class="row mb-4" id="hide_show_alayacare_cards" style="display:@if($agencyDetails->alaycare_status == 1) @else none @endif">
    <div class="col-12">
        <div class="row">
            <!-- Fetch All Employee Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-primary-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch All Employee</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 total_employee_id" style="display:@if($totalEmployee > 0) @else none @endif">Total Employee</p>
                            <p class="text-muted mb-0 total_employee_id" id="total_employee_id" style="display:@if($totalEmployee > 0) @else none @endif">
                                <span class="badge badge-primary badge-pill">{{ $totalEmployee }}</span>
                            </p>
                        </div>
                        <div class="text-center w-100">
                            <a href="javascript:void(0)" class="refresh_employee_id btn btn-outline-primary btn-sm" onclick="refreshEmployee()" style="display:@if($totalEmployee == 0) @else none @endif">
                                <i class="fa fa-refresh mr-1"></i>Refresh
                            </a>
                            <img src="{{ asset('ajax-loader.gif') }}" alt="loader" class="loader" id="loadertag1Employee" style="display:none;">
                            <a href="{{ url('sync-agency-employee') }}/{{ sha1($agencyDetails->id) }}" id="syncEmployee" target="_blank" style="display:@if($totalEmployee > 0) @else none @endif" class="btn btn-primary btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Employee
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fetch All Client Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-success-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-address-book fa-2x text-success"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch All Client</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 total_client_id" style="display:@if($totalClient > 0) @else none @endif">Total Client</p>
                            <p class="text-muted mb-0 total_client_id" id="total_client_id" style="display:@if($totalClient > 0) @else none @endif">
                                <span class="badge badge-success badge-pill">{{ $totalClient }}</span>
                            </p>
                        </div>
                        <div class="text-center w-100">
                            <a href="javascript:void(0)" class="refresh_client_id btn btn-outline-success btn-sm" onclick="refreshClient()" style="display:@if($totalClient == 0) @else none @endif">
                                <i class="fa fa-refresh mr-1"></i>Refresh
                            </a>
                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" class="loader" id="loadertag1client" style="display:none;">
                            <a href="{{ url('sync-agency-client') }}/{{ sha1($agencyDetails->id) }}" id="syncClient" target="_blank" style="display:@if($totalClient > 0) @else none @endif" class="btn btn-success btn-sm">
                                <i class="fa fa-refresh mr-1"></i>Sync Client
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fetch All Due Skill Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-4 grid-margin stretch-card">
                <div class="card d-flex align-items-center shadow-sm border-0 h-100 hover-shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-wrapper bg-info-light rounded-circle p-3 d-inline-block">
                                <i class="fa fa-certificate fa-2x text-info"></i>
                            </div>
                        </div>
                        <h4 class="card-title text-center font-weight-bold">Fetch All Due Skill</h4>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0">&nbsp;</p>
                        </div>
                        <div class="text-center w-100">
                            <a href="javascript:void(0)" class="btn btn-outline-info btn-sm" onclick="refreshSkill()">
                                <i class="fa fa-refresh mr-1"></i>Refresh
                            </a>
                            <img src="{{ asset('ajax-loader.gif') }}" alt="loader" class="loader" id="loadertag1Skill" style="display:none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skills Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-2 text-white" style="background-color:#2c3e50 !important">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fa fa-list-alt mr-2"></i>Skills
                    </h4>
                    <div class="col-sm-6 pull-right text-right">
                        @can('update-alayacare-skill-details')
                        <button class="btn btn-outline-light btn-sm" style="margin-top:1px;height:30px;border-radius:5px" onclick="addSkill()">
                            <i class="fa fa-save mr-1"></i>Save Skill
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0"><input type="checkbox" id="cboxid"></th>
                                <th class="border-0">No</th>
                                <th class="border-0">Branch Name</th>
                                <th class="border-0">Skill Name</th>
                                <th class="border-0">Category Name</th>
                            </tr>
                        </thead>
                        <tbody id="alayacare_skill_response">
                            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end p-3">
                    <a class="btn btn-outline-secondary btn-sm mr-2" href="javascript:void(0)" id="previousSkillId" style="display:none" onClick="previousSkill()">
                        <i class="fa fa-chevron-left mr-1"></i>Prev
                    </a>
                    <a class="btn btn-primary btn-sm" href="javascript:void(0)" id="nextSkillId" style="display:none" onClick="nextSkill()">
                        Next<i class="fa fa-chevron-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
