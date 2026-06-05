<style>
    .cards {
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.08) !important;
        margin-bottom: 1.25rem;
    }

    .cards-header {
        background-color: #fff !important;
        border-bottom: 1px solid #f0f0f0 !important;
        padding: 0.75rem 1rem !important;
        border-radius: 8px 8px 0 0 !important;
    }

     .cards-header h5 {
        margin: 0 !important;
        font-weight: 600 !important;
        color: #2c3e50 !important;
        display: flex;
        align-items: center;
        font-size: 0.9375rem !important;
    }

    .cards-header h5 i {
        margin-right: 0.5rem;
        color: #667eea !important;
        font-size: 1rem !important;
    }

    .cards-body {
        padding: 1rem !important;
    }

   #hha_mdo_order_div #tableContainer {
        margin: 0 !important;
    }

    #hha_mdo_order_div #tableContainer .table-responsive {
        margin: 0 !important;
        padding: 0 !important;
    }

    #hha_mdo_order_div .table-responsive {
        border-radius: 6px !important;
        overflow: hidden;
        background: white;
    }

    #hha_mdo_order_div #documentsTable {
        margin-bottom: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
        background: white !important;
    }

    #hha_mdo_order_div #documentsTable thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    #hha_mdo_order_div #documentsTable thead tr {
        background: transparent !important;
    }

    #hha_mdo_order_div #documentsTable thead th {
        background: transparent !important;
        color: white !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.3px !important;
        border: none !important;
        padding: 0.625rem 0.75rem !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #hha_mdo_order_div #documentsTable tbody {
        background: white !important;
    }

    #hha_mdo_order_div #documentsTable tbody tr {
        transition: all 0.3s ease !important;
        background-color: white !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }

    #hha_mdo_order_div #documentsTable tbody tr:hover {
        background-color: #f8f9ff !important;
        transform: scale(1.01) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    #hha_mdo_order_div #documentsTable tbody tr:last-child {
        border-bottom: none !important;
    }

    #hha_mdo_order_div #documentsTable tbody td {
        padding: 0.5rem 0.625rem !important;
        vertical-align: middle !important;
        border-bottom: none !important;
        color: #4a5568 !important;
        background: transparent !important;
        font-size: 0.8125rem !important;
    }

    #hha_mdo_order_div #documentsTable tbody td:first-child {
        font-weight: 500 !important;
    }

    #hha_mdo_order_div #documentsTable tbody td strong {
        color: #2c3e50 !important;
        font-weight: 600 !important;
    }

    #hha_mdo_order_div #documentsTable tbody td .text-danger {
        color: #dc3545 !important;
    }

    #hha_mdo_order_div .badge {
        padding: 0.3rem 0.65rem !important;
        font-weight: 600 !important;
        border-radius: 12px !important;
        font-size: 0.65rem !important;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: none !important;
    }

    #hha_mdo_order_div .badge-status-current {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    #hha_mdo_order_div .badge-status-superseded {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    #hha_mdo_order_div .badge-docstatus-sent {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        color: white !important;
    }

    #hha_mdo_order_div .badge-docstatus-pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        color: white !important;
    }

    #hha_mdo_order_div .badge-docstatus-received {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        color: white !important;
    }

    /* Modern Action Buttons Container */
    #hha_mdo_order_div .action-buttons {
        display: flex !important;
        gap: 0.4rem !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        padding: 0.25rem 0;
    }

    /* Modern Action Button Base Styles */
    #hha_mdo_order_div .btn-action {
        position: relative;
        padding: 0.375rem 0.875rem !important;
        border-radius: 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        border: none !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        min-width: 40px;
        min-height: 30px;
        text-align: center;
        cursor: pointer;
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #hha_mdo_order_div .btn-action .btn-icon-text {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    #hha_mdo_order_div .btn-action i {
        font-size: 0.875rem;
        transition: transform 0.3s ease;
    }

    #hha_mdo_order_div .btn-action:hover i {
        transform: scale(1.1) rotate(-5deg);
    }

    /* Download Button - Modern Gradient Design */
    #hha_mdo_order_div .btn-action-download {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        position: relative;
        z-index: 1;
    }

    #hha_mdo_order_div .btn-action-download::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #5568d3 0%, #65408a 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 6px;
        z-index: -1;
    }

    #hha_mdo_order_div .btn-action-download:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4) !important;
    }

    #hha_mdo_order_div .btn-action-download:hover::before {
        opacity: 1;
    }

    #hha_mdo_order_div .btn-action-download:active {
        transform: translateY(0) !important;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3) !important;
    }

    /* Send Button - Modern Gradient Design */
    #hha_mdo_order_div .btn-action-send {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        color: white !important;
        position: relative;
        z-index: 1;
    }

    #hha_mdo_order_div .btn-action-send::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #e080ea 0%, #e4465b 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 6px;
        z-index: -1;
    }

    #hha_mdo_order_div .btn-action-send:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 10px rgba(245, 87, 108, 0.4) !important;
    }

    #hha_mdo_order_div .btn-action-send:hover::before {
        opacity: 1;
    }

    #hha_mdo_order_div .btn-action-send:active {
        transform: translateY(0) !important;
        box-shadow: 0 2px 4px rgba(245, 87, 108, 0.3) !important;
    }

    /* Spinner inside buttons */
    #hha_mdo_order_div .btn-action .spinner-border {
        width: 0.75rem;
        height: 0.75rem;
        border-width: 2px;
        margin-right: 0;
        position: absolute;
    }

    #hha_mdo_order_div .btn-action .spinner-border:not(.d-none) ~ .btn-icon-text {
        visibility: hidden;
    }

    #hha_mdo_order_div .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Legacy button styles for backward compatibility */
    #hha_mdo_order_div .btn-view {
        background-color: #17a2b8 !important;
        color: white !important;
        border: none !important;
        padding: 0.5rem 1rem !important;
        border-radius: 6px !important;
        transition: all 0.3s ease;
    }

    #hha_mdo_order_div .btn-view:hover {
        background-color: #138496 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3) !important;
    }

    #hha_mdo_order_div .btn-download {
        background-color: #28a745 !important;
        color: white !important;
        border: none !important;
    }

    #hha_mdo_order_div .btn-download:hover {
        background-color: #218838 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3) !important;
    }

    #hha_mdo_order_div .empty-state {
        text-align: center;
        padding: 2rem 0.75rem;
        color: #6c757d;
    }

    #hha_mdo_order_div .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    #hha_mdo_order_div .empty-state h5 {
        font-weight: 600;
        margin-bottom: 0.35rem;
        font-size: 0.9375rem;
    }

    #hha_mdo_order_div .empty-state p {
        font-size: 0.8125rem;
    }

    #statsSection .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        padding: 0.875rem !important;
        border-radius: 8px !important;
        margin-bottom: 0.75rem;
        border: none !important;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1) !important;
    }

    #statsSection .stats-card h3 {
        font-size: 1.75rem !important;
        font-weight: 700 !important;
        margin: 0 !important;
        color: white !important;
    }

    #statsSection .stats-card p {
        margin: 0 !important;
        opacity: 0.9;
        color: white !important;
        font-size: 0.75rem !important;
    }

    #statsSection .stats-card i {
        color: white !important;
        font-size: 1.25rem !important;
    }

    #hha_mdo_order_div .document-info {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.15rem;
    }

    #hha_mdo_order_div .document-id {
        font-weight: 600 !important;
        color: #2c3e50 !important;
        margin-bottom: 0.15rem !important;
        font-size: 0.8125rem !important;
    }

    #hha_mdo_order_div .document-title {
        font-size: 0.75rem !important;
        color: #6c757d !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.35rem;
    }

    #hha_mdo_order_div .document-title i {
        font-size: 0.875rem !important;
    }

    #hha_mdo_order_div .date-range {
        display: flex;
        flex-direction: column;
        font-size: 0.75rem;
    }

    #hha_mdo_order_div .date-range .date-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.15rem;
    }

    #hha_mdo_order_div .date-range .date-value {
        color: #6c757d;
    }

    /* Responsive Design for Action Buttons */
    @media (max-width: 768px) {
        #hha_mdo_order_div .action-buttons {
            flex-direction: column !important;
            gap: 0.5rem !important;
            padding: 0.5rem 0 !important;
        }

        #hha_mdo_order_div .btn-action {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 150px;
            margin: 0 auto;
        }

        #hha_mdo_order_div .action-buttons .btn {
            width: 100%;
            margin: 0.25rem 0;
        }

        #hha_mdo_order_div .btn-action .btn-text {
            display: inline !important;
        }
    }

    @media (max-width: 576px) {
        #hha_mdo_order_div .btn-action {
            padding: 0.3rem 0.75rem !important;
            font-size: 0.7rem !important;
            min-width: 85px !important;
        }

        #hha_mdo_order_div .btn-action i {
            font-size: 0.75rem;
        }
    }

    #hha_mdo_order_div .badge-docstatus-complete {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        color: white !important;
    }
    #hha_mdo_order_div .badge-docstatus-printed {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important; /* blue */
        color: white !important;
    }

    /* Loader styles */
    .hha-patient-mdo-order-section .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 8px;
    }

    .hha-patient-mdo-order-section .loader-overlay.active {
        display: flex;
    }

    .hha-patient-mdo-order-section .loader-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .hha-patient-mdo-order-section {
        position: relative;
    }
</style>

<!-- Statistics Cards -->
<div class="cards hha_mdo_class">
    <div class="cards-header">
        <h5><i class="fas fa-chart-bar"></i> Statistics Overview</h5>
    </div>
    <div class="cards-body">
        <div class="row" id="statsSection">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fa fa-file-o fa-2x mb-2"></i>
                    <h3 id="totalDocuments">0</h3>
                    <p>Total Documents</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h3 id="sentDocuments">0</h3>
                    <p>Sent Documents</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fa fa-clock-o fa-2x mb-2"></i>
                    <h3 id="pendingDocuments">0</h3>
                    <p>Pending Documents</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fa fa-paper-plane fa-2x mb-2"></i>
                    <h3 id="receivedDocuments">0</h3>
                    <p>Receive Documents</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #155724 100%);">
                    <i class="fa fa-pencil fa-2x mb-2"></i>
                    <h3 id="signedDocuments">0</h3>
                    <p>Signed Documents</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents Table -->
<div class="cards hha_mdo_class hha-patient-mdo-order-section" id="hha_mdo_order_div">
    <div class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>
    <div class="cards-header">
        <h5><i class="fas fa-list"></i> Patient Documents</h5>
    </div>
    <div class="cards-body">
        <div id="emptyState" class="empty-state" style="display: none;">
            <i class="fas fa-search"></i>
            <h5>No Documents Found</h5>
            <p>Select a patient and click search to view their documents</p>
        </div>

        <div id="tableContainer">
            <div class="table-responsive">
                <table id="documentsTable" class="table">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Doc Status</th>
                            
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="documentsTableBody">
                        <!-- Data will be populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>