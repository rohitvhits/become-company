<div class="right-section-main">
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs tabs-right sideways right-section-ul">
        <li class="active">
            <a href="#visiting-demographic"
               aria-controls="visiting-demographic"
               role="tab"
               data-toggle="tab"
               onclick="getVisitingDemographic()">
               Demographic Details
            </a>
        </li>

        <li>
            <a href="#visiting-pending-medical"
               aria-controls="visiting-pending-medical"
               role="tab"
               data-toggle="tab"
               onclick="getVisitingPendingMedical()">
               Medical
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content right-section-tab-content">

        <!-- Demographic Tab -->
        <div role="tabpanel" class="tab-pane active" id="visiting-demographic">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <p class="card-title mb-0">Demographic Details</p>
            </div>

            <!-- Loader -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center" id="visiting-demographic-loader" style="display:none; padding: 50px 0;">
                        <img src="{{ asset('/ajax-loader.gif') }}" alt="Loading..." style="width: 50px; height: 50px;">
                        <p class="mt-2 text-muted">Loading demographic details...</p>
                    </div>
                </div>
            </div>

            <!-- Content Container -->
            <div class="row" id="visiting-demographic-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="visiting-demographic-data">
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="mdi mdi-account-circle" style="font-size: 48px;"></i>
                                    <p class="mt-2">Click "Demographic Details" to load information</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Tab -->
        <div role="tabpanel" class="tab-pane" id="visiting-pending-medical">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <p class="card-title mb-0">Medical Information</p>
                <button class="btn btn-info btn-sm" onclick="refreshVisitingMedical()" data-whatever="@mdo">
                    <i class="mdi mdi-sync"></i> SYNC Medical
                </button>
            </div>

            <!-- Loader -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center" id="visiting-medical-loader" style="display:none; padding: 50px 0;">
                        <img src="{{ asset('/ajax-loader.gif') }}" alt="Loading..." style="width: 50px; height: 50px;">
                        <p class="mt-2 text-muted">Loading medical information...</p>
                    </div>
                </div>
            </div>

            <!-- Medical Table Container -->
            <div class="row" id="visiting-medical-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0" id="visiting-medical-content-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Medical ID</th>
                                            <th>Medical Name</th>
                                            <th>Medical Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="visiting_medical_tbody">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                <i class="mdi mdi-file-document-outline" style="font-size: 48px;"></i>
                                                <p class="mt-2">Click "Medical" to load information</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Shimmer Effect CSS */
.shimmer-wrapper {
    position: relative;
    overflow: hidden;
    background: #f6f7f8;
}

.shimmer {
    position: relative;
    overflow: hidden;
    background: linear-gradient(90deg, #f0f0f0 0%, #e0e0e0 20%, #f0f0f0 40%, #f0f0f0 100%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.shimmer-line {
    height: 16px;
    margin: 10px 0;
    border-radius: 4px;
}

.shimmer-table-row {
    display: table-row;
}

.shimmer-table-cell {
    display: table-cell;
    padding: 12px;
    vertical-align: middle;
}

.shimmer-table-cell .shimmer-line {
    height: 14px;
    margin: 0;
}

/* Table Enhancements */
#visiting-medical-content-table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

#visiting-medical-content-table tbody td {
    font-size: 14px;
    vertical-align: middle;
    padding: 12px;
}

#visiting-medical-content-table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
