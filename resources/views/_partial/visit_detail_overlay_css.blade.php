<style>
    /* ── Task ID link ── */
    .th-task-id-link { font-weight:700; color:#007bff; text-decoration:none; }
    .th-task-id-link:hover { text-decoration:underline; color:#0056b3; }

    /* ── Overlay backdrop ── */
    .vd-overlay {
        display:none; position:fixed; z-index:1055;
        inset:0; background:rgba(0,0,0,.45);
    }
    .vd-overlay.show { display:flex; justify-content:flex-end; }

    /* ── Drawer panel ── */
    .vd-drawer {
        width:700px; max-width:100vw; height:100vh;
        background:#fff; display:flex; flex-direction:column;
        box-shadow:-6px 0 32px rgba(0,0,0,.18);
        transform:translateX(100%);
        transition:transform .3s cubic-bezier(.25,.8,.25,1);
    }
    .vd-overlay.show .vd-drawer { transform:translateX(0); }

    /* ── Header ── */
    .vd-header {
        padding:14px 18px;
        border-bottom:1px solid #e9ecef;
        display:flex; align-items:center; gap:10px;
        background:#fff; flex-shrink:0; flex-wrap:wrap;
    }
    .vd-header-left { display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; min-width:0; }
    .vd-avatar {
        width:44px; height:44px; border-radius:50%;
        background:linear-gradient(135deg,#667eea,#764ba2);
        color:#fff; font-size:15px; font-weight:700;
        display:flex; align-items:center; justify-content:center;
        flex-shrink:0; letter-spacing:1px;
    }
    .vd-header-info { display:flex; flex-direction:column; gap:1px; min-width:0; }
    .vd-header-info h4 { margin:0; font-size:15px; font-weight:700; color:#1a1a2e; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .vd-task-id { font-size:12px; color:#6c757d; font-weight:500; }
    .vd-badge-status { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; white-space:nowrap; }
    .vd-badge-status.info    { background:#17a2b8; }
    .vd-badge-status.success { background:#28a745; }
    .vd-badge-status.danger  { background:#dc3545; }
    .vd-badge-status.warning { background:#e0a800; color:#333; }
    .vd-badge-type {
        padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
        background:#e8f0fe; color:#1a73e8; border:1px solid #c5d9f9; white-space:nowrap;
    }
    .vd-header-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
    .vd-action-btn {
        width:32px; height:32px; border-radius:6px; border:1px solid #dee2e6;
        background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
        font-size:15px; transition:all .15s; padding:0;
    }
    .vd-action-danger  { color:#dc3545; border-color:#f5c6cb; }
    .vd-action-danger:hover  { background:#dc3545; color:#fff; border-color:#dc3545; }
    .vd-action-close   { color:#6c757d; }
    .vd-action-close:hover   { background:#f8f9fa; color:#343a40; }

    /* ── Horizontal tabs ── */
    .vd-tabs {
        display:flex; gap:2px; padding:0 18px;
        border-bottom:2px solid #e9ecef;
        background:#fff; flex-shrink:0;
    }
    .vd-tab {
        padding:10px 20px; border:none; background:none;
        font-size:13px; font-weight:500; color:#6c757d;
        cursor:pointer; border-bottom:3px solid transparent;
        margin-bottom:-2px; transition:all .15s;
    }
    .vd-tab:hover { color:#007bff; }
    .vd-tab.active { color:#007bff; font-weight:600; border-bottom-color:#007bff; }

    /* ── Body ── */
    .vd-body { flex:1; overflow-y:auto; background:#f7f8fc; }
    .vd-panel { display:none; }
    .vd-panel.active { display:block; animation:vdFade .2s; }
    @keyframes vdFade { from{opacity:0} to{opacity:1} }

    /* ── Sections ── */
    .vd-section {
        background:#fff; border-radius:8px;
        margin:14px 14px 0; border:1px solid #e9ecef;
        padding:14px 16px;
    }
    .vd-section:last-child { margin-bottom:14px; }
    .vd-section-title {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#6c757d;
        margin-bottom:12px; padding-bottom:8px;
        border-bottom:1px solid #f0f2f5;
        display:flex; align-items:center; gap:7px;
    }
    .vd-section-title::before {
        content:''; width:3px; height:13px;
        background:#007bff; border-radius:2px; flex-shrink:0;
    }

    /* ── 3-column grid ── */
    .vd-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .vd-cell-label {
        font-size:11px; font-weight:600; color:#9ca3af;
        text-transform:uppercase; letter-spacing:.3px; margin-bottom:3px;
    }
    .vd-cell-value { font-size:13px; font-weight:600; color:#1f2937; word-break:break-word; }

    /* ── Patient contact strip ── */
    .vd-patient-contact {
        margin-top:12px; padding:10px 13px;
        background:#f8f9fa; border-radius:6px;
        display:flex; align-items:center; gap:20px; flex-wrap:wrap;
        border:1px solid #e9ecef;
    }
    .vd-contact-name, .vd-contact-phone { font-size:13px; color:#495057; }
    .vd-contact-name i, .vd-contact-phone i { color:#007bff; margin-right:4px; }

    /* ── Data table ── */
    .vd-table { width:100%; border-collapse:collapse; font-size:12.5px; }
    .vd-table th {
        background:#f8f9fa; color:#6c757d; font-weight:600;
        font-size:11px; text-transform:uppercase; letter-spacing:.3px;
        padding:8px 10px; border-bottom:2px solid #dee2e6; text-align:left;
    }
    .vd-table td { padding:8px 10px; border-bottom:1px solid #f0f2f5; color:#1f2937; vertical-align:middle; }
    .vd-table tr:hover td { background:#fafbff; }
    .vd-table tr:last-child td { border-bottom:none; }

    /* ── Small action buttons ── */
    .vd-btn-sm {
        display:inline-flex; align-items:center; gap:3px;
        padding:3px 9px; border-radius:4px; font-size:11px;
        font-weight:500; cursor:pointer; border:none; text-decoration:none; margin-right:2px;
    }
    .vd-btn-info    { background:#17a2b8; color:#fff; }
    .vd-btn-success { background:#28a745; color:#fff; }
    .vd-btn-warning { background:#e0a800; color:#fff; }
    .vd-btn-info:hover    { background:#138496; color:#fff; }
    .vd-btn-success:hover { background:#218838; color:#fff; }
    .vd-btn-warning:hover { background:#c69500; color:#fff; }

    /* ── Shimmer loader ── */
    .shimmer-wrapper { padding:20px; }
    .shimmer-card { background:#fff; border-radius:8px; padding:20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .shimmer {
        background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
        background-size:200% 100%; animation:shimmerAnim 1.5s infinite; border-radius:4px;
    }
    @keyframes shimmerAnim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .shimmer-line { height:15px; margin-bottom:11px; }
    .shimmer-line.title  { height:22px; width:40%; margin-bottom:18px; }
    .shimmer-line.short  { width:55%; }
    .shimmer-line.medium { width:75%; }
    .shimmer-line.long   { width:100%; }
    .shimmer-header { height:58px; margin-bottom:20px; border-radius:8px; }

    /* ── Responsive ── */
    @media (max-width:768px) {
        .vd-drawer { width:100vw; }
        .vd-grid-3 { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:480px) {
        .vd-grid-3 { grid-template-columns:1fr; }
    }

    /* ── Patient Record Banner (in General tab) ── */
    .vd-pr-banner {
        margin:14px 14px 0; border-radius:8px;
        border:1px solid #e9ecef; overflow:hidden; background:#fff;
    }
    .vd-pr-banner-header {
        display:flex; align-items:center; gap:8px; padding:9px 14px;
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#6c757d;
        border-bottom:1px solid #f0f2f5; background:#f8f9fa;
    }
    .vd-pr-banner-header::before {
        content:''; width:3px; height:13px;
        background:#6f42c1; border-radius:2px; flex-shrink:0;
    }
    .vd-pr-banner-body { padding:12px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .vd-pr-banner-found   { border-left:4px solid #28a745; }
    .vd-pr-banner-missing { border-left:4px solid #f0ad4e; }
    .vd-pr-banner-loading { border-left:4px solid #dee2e6; }
    .vd-pr-info { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .vd-pr-icon { font-size:22px; flex-shrink:0; }
    .vd-pr-text-block { line-height:1.4; }
    .vd-pr-status-label { font-size:12px; font-weight:700; }
    .vd-pr-meta { font-size:11px; color:#6c757d; margin-top:2px; }
    .vd-pr-actions { display:flex; gap:6px; flex-shrink:0; }
    .vd-pr-btn {
        display:inline-flex; align-items:center; gap:4px;
        padding:5px 13px; border-radius:5px; font-size:12px;
        font-weight:600; cursor:pointer; border:none; text-decoration:none;
        white-space:nowrap; transition:all .15s;
    }
    .vd-pr-btn-green  { background:#28a745; color:#fff; }
    .vd-pr-btn-green:hover  { background:#218838; color:#fff; text-decoration:none; }
    .vd-pr-btn-orange { background:#f0ad4e; color:#fff; }
    .vd-pr-btn-orange:hover { background:#e09a2e; color:#fff; }
    .vd-pr-btn-outline { background:#fff; color:#6c757d; border:1px solid #dee2e6; }
    .vd-pr-btn-outline:hover { background:#f8f9fa; }
    .vd-pr-create-form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:8px; padding-top:8px; border-top:1px dashed #dee2e6; width:100%; }
    .vd-pr-create-form select { font-size:12px; height:30px; padding:0 8px; border-radius:4px; border:1px solid #dee2e6; min-width:180px; }
    .vd-pr-create-form .vd-pr-btn { padding:4px 12px; }

    /* ── Patient Record full panel ── */
    .vd-pr-card {
        background:#fff; border-radius:8px; border:1px solid #e9ecef;
        margin:14px 14px 0; padding:20px;
    }
    .vd-pr-card:last-child { margin-bottom:14px; }
    .vd-pr-card-title {
        font-size:13px; font-weight:700; color:#1a1a2e;
        margin-bottom:14px; padding-bottom:10px;
        border-bottom:2px solid #f0f2f5;
        display:flex; align-items:center; gap:8px;
    }
    .vd-pr-detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    .vd-pr-detail-label { font-size:10.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
    .vd-pr-detail-value { font-size:13px; font-weight:600; color:#1f2937; }
    @media (max-width:480px) { .vd-pr-detail-grid { grid-template-columns:1fr; } }

    /* ── ID Summary Strip ── */
    .vd-id-strip {
        display:flex; margin:14px 14px 0; border-radius:8px;
        border:1px solid #e9ecef; overflow:hidden; background:#fff;
    }
    .vd-id-pill {
        flex:1; padding:11px 14px; border-right:1px solid #e9ecef;
        display:flex; flex-direction:column; gap:3px; min-width:0;
    }
    .vd-id-pill:last-child { border-right:none; }
    .vd-id-pill-label {
        font-size:9.5px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#9ca3af; display:flex; align-items:center; gap:4px;
    }
    .vd-id-pill-value {
        font-size:15px; font-weight:700; color:#1f2937; font-family:monospace;
        letter-spacing:-.3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .vd-id-pill-value a { color:#007bff; text-decoration:none; }
    .vd-id-pill-value a:hover { text-decoration:underline; }
    .vd-id-pill.vd-id-linked { background:#f0fdf4; }
    .vd-id-pill.vd-id-linked .vd-id-pill-label { color:#15803d; }
    .vd-id-pill.vd-id-linked .vd-id-pill-value { color:#16a34a; }
    .vd-id-pill.vd-id-loading .vd-id-pill-value { color:#d1d5db; font-size:12px; font-family:inherit; }

    /* ── TH Patient ID badge in drawer header ── */
    .vd-th-patient-badge {
        display:none; font-size:11px; font-weight:600; color:#6f42c1;
        background:#f3e8ff; border:1px solid #d8b4fe; border-radius:4px;
        padding:1px 7px; letter-spacing:.2px; white-space:nowrap;
    }

    @media (max-width:480px) {
        .vd-id-strip { flex-direction:column; }
        .vd-id-pill { border-right:none; border-bottom:1px solid #e9ecef; }
        .vd-id-pill:last-child { border-bottom:none; }
    }
</style>
