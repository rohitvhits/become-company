<div class="tab-pane" id="patient-medicine-list">
    <!-- Loader Overlay -->
    <div class="medication-loader-overlay" id="medicationLoaderOverlay" style="display:none;">
        <div class="medication-loader-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="medication-loader-text">Loading patient medications...</p>
        </div>
    </div>

    <div class="medication-header">
        <div class="d-flex align-items-center justify-content-between">
            <p class="card-title mb-0">Patient Medication Section</p>
            <span class="medication-count" id="total_medication_record"></span>
        </div>
    </div>

    <div class="medication-container">
        <!-- Shimmer Loader -->
        <div class="medication-shimmer-container" id="medicationShimmerLoader" style="display:none;">
            <div class="medication-shimmer-row">
                <div class="medication-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 20%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 12%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
            </div>
            <div class="medication-shimmer-row">
                <div class="medication-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 20%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 12%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
            </div>
            <div class="medication-shimmer-row">
                <div class="medication-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 20%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 12%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
            </div>
            <div class="medication-shimmer-row">
                <div class="medication-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 20%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 12%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
            </div>
            <div class="medication-shimmer-row">
                <div class="medication-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 20%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 12%;"></div>
                <div class="medication-shimmer-cell shimmer-effect" style="width: 15%;"></div>
            </div>
        </div>

        <div class="medication-table-wrapper" id="medicationTableWrapper">
            <table class="table medication-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Medication Name</th>
                        <th>Start Date</th>
                        <th>Dosage</th>
                        <th>Quantity</th>
                        <th>Frequency</th>
                    </tr>
                </thead>
                <tbody id="medication_id">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No medications available</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="medication-pagination" id="medication_pagination">
        </div>
    </div>
</div>
