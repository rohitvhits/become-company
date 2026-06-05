
<div class="activity-container">
    <!-- Loader Overlay -->
    <div class="activity-loader-overlay" id="activityLoaderOverlay" style="display:none;">
        <div class="activity-loader-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="activity-loader-text">Loading activity log...</p>
        </div>
    </div>

    <div class="activity-header">
        <div class="d-flex align-items-center justify-content-between">
            <p class="card-title mb-0">Patient Activity Log</p>
            <span class="record-count"><span id="total_activity_record"></span> Records</span>
        </div>
    </div>

    <div class="activity-wrapper col-md-12">
       
        <!-- Shimmer Loader -->
        <div class="shimmer-container" id="shimmerActivityLoader" style="display:none">
            <div class="shimmer-timeline-item">
                <div class="shimmer-icon shimmer-effect"></div>
                <div class="shimmer-card">
                    <div class="shimmer-card-header">
                        <div class="shimmer-reason shimmer-effect"></div>
                        <div class="shimmer-date shimmer-effect"></div>
                    </div>
                    <div class="shimmer-card-body">
                        <div class="shimmer-text long shimmer-effect"></div>
                        <div class="shimmer-text medium shimmer-effect"></div>
                        <div class="shimmer-text long shimmer-effect"></div>
                        <div class="shimmer-text short shimmer-effect"></div>
                    </div>
                </div>
            </div>

            <div class="shimmer-timeline-item">
                <div class="shimmer-icon shimmer-effect"></div>
                <div class="shimmer-card">
                    <div class="shimmer-card-header">
                        <div class="shimmer-reason shimmer-effect"></div>
                        <div class="shimmer-date shimmer-effect"></div>
                    </div>
                    <div class="shimmer-card-body">
                        <div class="shimmer-text long shimmer-effect"></div>
                        <div class="shimmer-text medium shimmer-effect"></div>
                        <div class="shimmer-text long shimmer-effect"></div>
                        <div class="shimmer-text short shimmer-effect"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="activity-timeline" id="activityTimeline">
           
        </div>

        <!-- Empty State -->
        <div class="activity-empty-state" id="emptyActivityState" style="display:none">
            <div class="activity-empty-icon">
                <i class="mdi mdi-clipboard-text-outline"></i>
            </div>
            <p class="activity-empty-text">No activity logs found</p>
        </div>
    </div>

    <div class="activity-pagination" id="activityPagination" style="display:none">
        <button class="activity-pagination-btn prev" id="prevPageBtn" onclick="previousActivityPage()" disabled>
            <i class="mdi mdi-chevron-left"></i>
            <span>Previous</span>
        </button>

        <div class="pagination-info">
            <span>Page <strong id="currentPage">1</strong> of <strong id="totalPages">1</strong></span>
        </div>

        <button class="activity-pagination-btn next" id="nextPageBtn" onclick="nextActivityPage()">
            <span>Next</span>
            <i class="mdi mdi-chevron-right"></i>
        </button>
    </div>
</div>
