
<div class="cp-container">
    <!-- Loader Overlay -->
    <div class="cp-loader-overlay" id="carePlanLoaderOverlay" style="display:none;">
        <div class="cp-loader-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="cp-loader-text">Loading care plan...</p>
        </div>
    </div>

    <div class="cp-inner">
        <div class="cp-header">
            <div class="cp-header-title">
                📋 Care Plan Dashboard
            </div>
            <div class="cp-header-info" id="reviewInfo">
                Loading...
            </div>
        </div>

        <div class="cp-stats" id="statsBar">
            <!-- Stats will be dynamically loaded -->
        </div>

        <!-- Shimmer Loader -->
        <div class="cp-shimmer-container" id="carePlanShimmerLoader" style="display:none;">
            <!-- Shimmer Stats -->
            <div class="cp-shimmer-stats">
                <div class="shimmer-stat-card shimmer-effect"></div>
                <div class="shimmer-stat-card shimmer-effect"></div>
                <div class="shimmer-stat-card shimmer-effect"></div>
                <div class="shimmer-stat-card shimmer-effect"></div>
            </div>

            <!-- Shimmer Disease Cards -->
            <div class="shimmer-disease-card">
                <div class="shimmer-disease-header shimmer-effect"></div>
                <div class="shimmer-disease-content">
                    <div class="shimmer-goal-item">
                        <div class="shimmer-goal-number shimmer-effect"></div>
                        <div class="shimmer-goal-text shimmer-effect"></div>
                    </div>
                    <div class="shimmer-goal-item">
                        <div class="shimmer-goal-number shimmer-effect"></div>
                        <div class="shimmer-goal-text shimmer-effect"></div>
                    </div>
                </div>
            </div>

            <div class="shimmer-disease-card">
                <div class="shimmer-disease-header shimmer-effect"></div>
                <div class="shimmer-disease-content">
                    <div class="shimmer-goal-item">
                        <div class="shimmer-goal-number shimmer-effect"></div>
                        <div class="shimmer-goal-text shimmer-effect"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="cp-content" id="carePlanContent">
            <!-- Care plans will be dynamically loaded -->
        </div>
        <div class="cp-empty-state hide" id="emptyCPPlan">
            <div class="cp-empty-state-icon">
                <i class="mdi mdi-message-text-outline"></i>
            </div>
            <p class="cp-empty-state-text">No Care Plan Review</p>
        </div>
    </div>
</div>
