<div class="tab-pane" id="patient-reading-list">
    <!-- Loader Overlay -->
    <div class="reading-loader-overlay" id="readingLoaderOverlay" style="display:none;">
        <div class="reading-loader-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="reading-loader-text">Loading patient readings...</p>
        </div>
    </div>

    <div class="reading-header">
        <div class="d-flex align-items-center justify-content-between">
            <p class="card-title mb-0">Patient Reading Section</p>
            <span class="reading-count" id="total_reading_record"></span>
        </div>
    </div>

    <div class="reading-container">
        <!-- Shimmer Loader -->
        <div class="reading-shimmer-container" id="readingShimmerLoader" style="display:none;">
            <div class="reading-shimmer-row">
                <div class="reading-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 25%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 20%;"></div>
            </div>
            <div class="reading-shimmer-row">
                <div class="reading-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 25%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 20%;"></div>
            </div>
            <div class="reading-shimmer-row">
                <div class="reading-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 25%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 20%;"></div>
            </div>
            <div class="reading-shimmer-row">
                <div class="reading-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 25%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 20%;"></div>
            </div>
            <div class="reading-shimmer-row">
                <div class="reading-shimmer-cell shimmer-effect" style="width: 60px;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 25%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 15%;"></div>
                <div class="reading-shimmer-cell shimmer-effect" style="width: 20%;"></div>
            </div>
        </div>

        <div class="reading-table-wrapper" id="readingTableWrapper">
            <table class="table reading-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Title</th>
                        <th>Units</th>
                        <th>Value</th>
                        <th>Answer Date</th>
                    </tr>
                </thead>
                <tbody id="reading_id">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No readings available</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="reading-pagination" id="pagin">
            <button class="reading-btn reading-btn-prev" id="previousId" style="display:none" onClick="previous()">
                <i class="mdi mdi-chevron-left"></i> Previous
            </button>
            <button class="reading-btn reading-btn-next" id="nextId" style="display:none" onClick="next()">
                Next <i class="mdi mdi-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
