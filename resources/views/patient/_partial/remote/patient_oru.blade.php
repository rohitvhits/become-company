<div class="oru-container">
    <div class="oru-header">
        <div class="d-flex align-items-center justify-content-between">
            <p class="card-title mb-0">Patient ORU/TRN Messages</p>
            <span class="record-count" ><span id="total_oru_record"></span> Records</span>
        </div>
    </div>

    <div class="oru-table-wrapper col-md-12">
        

        <!-- Shimmer Loader -->
        <div class="shimmer-container" id="shimmerLoader" style="display:none">
            <div class="shimmer-card">
                <div class="shimmer-header">
                    <div class="shimmer-badge shimmer-effect"></div>
                    <div class="shimmer-time shimmer-effect"></div>
                </div>
                <div class="shimmer-body">
                    <div class="shimmer-content shimmer-effect"></div>
                </div>
                <div class="shimmer-footer">
                    <div class="shimmer-status shimmer-effect"></div>
                    <div class="shimmer-actions">
                        <div class="shimmer-btn shimmer-effect"></div>
                        <div class="shimmer-btn shimmer-effect"></div>
                    </div>
                </div>
            </div>

        </div>

        <div id="sms-logss">

        </div>

        <!-- Empty State -->
        <div class="oru-empty-state" id="emptyState" style="display:none">
            <div class="oru-empty-state-icon">
                <i class="mdi mdi-message-text-outline"></i>
            </div>
            <p class="oru-empty-state-text">No ORU/TRN messages found</p>
        </div>
    </div>

    <div class="oru-pagination" id="hideLoadMoreId" style="display:none">
        <button class="oru-load-more" onclick="loadMore()">Load More Messages</button>
    </div>
</div>
