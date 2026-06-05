@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css') }}" type="text/css">

<style>
    .file-manager-wrapper {
        padding: 15px;
    }
    .fm-sidebar {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        min-height: 500px;
    }
    .fm-sidebar .fm-sidebar-header {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fm-content {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        min-height: 500px;
    }
    .fm-toolbar {
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .fm-breadcrumb {
        padding: 8px 15px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }
    .fm-breadcrumb a {
        color: #007bff;
        text-decoration: none;
        cursor: pointer;
    }
    .fm-breadcrumb a:hover {
        text-decoration: underline;
    }
    .fm-breadcrumb .separator {
        color: #999;
        margin: 0 5px;
    }
    .fm-body {
        padding: 15px;
    }
    .fm-item {
        display: flex;
        align-items: center;
        padding: 10px 12px 10px 28px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        margin-bottom: 8px;
        position: relative;
        /* cursor: pointer; */
        transition: background 0.15s;
    }
    .fm-item.tv-selected { background: #eef4ff; border-color: #b8d0ff; }
    .fm-item:hover {
        background: #f0f4ff;
        border-color: #007bff;
    }
    .fm-item .fm-icon {
        font-size: 24px;
        margin-right: 12px;
        min-width: 30px;
        text-align: center;
    }
    .fm-item .fm-icon.folder-icon {
        color: #ffc107;
    }
    .fm-item .fm-icon.file-icon {
        color: #6c757d;
    }
    .fm-item .fm-icon.file-icon.pdf { color: #dc3545; }
    .fm-item .fm-icon.file-icon.image { color: #28a745; }
    .fm-item .fm-icon.file-icon.doc { color: #007bff; }
    .fm-item .fm-icon.file-icon.excel { color: #28a745; }
    .fm-item .fm-info {
        flex: 1;
    }
    .fm-item .fm-name {
        font-weight: 500;
        font-size: 14px;
    }
    .fm-item .fm-meta {
        font-size: 12px;
        color: #999;
    }
    .fm-item .fm-actions {
        display: flex;
        gap: 5px;
    }
    .fm-item .fm-actions .btn {
        padding: 3px 8px;
        font-size: 12px;
    }
    .fm-empty {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .fm-empty i {
        font-size: 48px;
        margin-bottom: 10px;
        display: block;
    }
    /* Folder tree accordion */
    .folder-tree ul {
        list-style: none;
        padding-left: 16px;
        margin: 0;
        overflow: hidden;
    }
    .folder-tree > ul {
        padding-left: 0;
    }
    .folder-tree li {
        padding: 2px 0;
    }
    .folder-tree .tree-row {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .folder-tree .tree-toggle {
        cursor: pointer;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #999;
        font-size: 10px;
        border-radius: 3px;
        transition: transform 0.15s;
    }
    .folder-tree .tree-toggle:hover {
        background: #e9ecef;
        color: #333;
    }
    .folder-tree .tree-toggle.open {
        transform: rotate(90deg);
    }
    .folder-tree .tree-toggle.no-children {
        cursor: default;
        visibility: hidden;
    }
    .folder-tree .tree-item {
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        flex: 1;
    }
    .folder-tree .tree-item:hover {
        background: #e9ecef;
    }
    .folder-tree .tree-item.active {
        background: #007bff;
        color: #fff;
    }
    .folder-tree .tree-item i.fa-folder,
    .folder-tree .tree-item i.fa-folder-open {
        color: #ffc107;
    }
    .folder-tree .tree-item.active i {
        color: #fff;
    }
    .folder-tree .tree-children {
        display: none;
        padding-left: 16px;
    }
    .folder-tree .tree-children.open {
        display: block;
    }
    /* Drag & Drop Zone */
    .fm-dropzone {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        color: #999;
        margin-bottom: 15px;
        transition: all 0.2s;
        display: none;
    }
    .fm-dropzone.active {
        display: block;
    }
    .fm-dropzone.dragover {
        border-color: #007bff;
        background: #f0f4ff;
        color: #007bff;
    }
    /* Preview Modal */
    .preview-container {
        text-align: center;
        max-height: 70vh;
        overflow: auto;
    }
    .preview-container img {
        max-width: 100%;
        max-height: 65vh;
    }
    .preview-container iframe {
        width: 100%;
        height: 65vh;
        border: none;
    }
    .fm-loading {
        text-align: center;
        padding: 40px;
    }
    /* View toggle */
    .fm-view-toggle .btn.active {
        box-shadow: inset 0 2px 4px rgba(0,0,0,.15);
    }
    /* Table view */
    .fm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .fm-table th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 8px 10px;
        text-align: left;
        font-weight: 600;
        white-space: nowrap;
    }
    .fm-table td {
        padding: 7px 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .fm-table tr:hover td {
        background: #f8f9fa;
    }
    .fm-table .tbl-icon {
        font-size: 16px;
        width: 28px;
        text-align: center;
    }
    .fm-table .tbl-icon.folder-icon { color: #ffc107; }
    .fm-table .tbl-icon.file-icon.pdf { color: #dc3545; }
    .fm-table .tbl-icon.file-icon.image { color: #28a745; }
    .fm-table .tbl-icon.file-icon.doc { color: #007bff; }
    .fm-table .tbl-icon.file-icon.excel { color: #28a745; }
    .fm-table .tbl-actions { white-space: nowrap; }
    .fm-table .tbl-actions .btn { padding: 2px 7px; font-size: 11px; }
    /* MDO / Telehealth badges */
    .badge-mdo { background: #6f42c1; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 4px; }
    .badge-th  { background: #17a2b8; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 4px; }
    /* Archived items */
    .fm-item.archived {
        opacity: 0.7;
        background: #fff8f0;
        border-color: #ffc107;
    }
    .fm-item.archived:hover {
        background: #fff3e0;
    }
    .archived-badge {
        display: inline-block;
        background: #ffc107;
        color: #333;
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 3px;
        margin-left: 6px;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper file-manager-wrapper">
        <div class="page-title-main" style="display:flex; justify-content:space-between; align-items:center;">
            <h5 class="mb-0 font-weight-bold">File Manager</h5>
            @if(isset($isSuperAdmin) && $isSuperAdmin)
            <div class="d-flex align-items-center" style="gap:8px;">
                <select id="agencySelector" class="form-control form-control-sm" style="min-width: 250px;" onchange="switchAgency(this.value)">
                    <option value="">-- Select Agency --</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}" {{ (isset($selectedAgencyId) && $selectedAgencyId == $agency->id) ? 'selected' : '' }}>
                            {{ $agency->agency_name }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ url('/file-manager/all-files') }}" class="btn btn-sm btn-outline-primary" title="View all files across all agencies" style="width: 160px;">
                    <i class="fa fa-files-o"></i> All Files
                </a>
            </div>
            @endif
        </div>

        <div class="row mt-3">
            <!-- Sidebar: Folder Tree -->
            <div class="col-md-3">
                <div class="fm-sidebar">
                    <div class="fm-sidebar-header">
                        <span>Folders</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="showCreateFolderModal(null)" title="New Root Folder">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="folder-tree p-2" id="folderTree">
                        <div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="fm-content">
                    <!-- Toolbar -->
                    <div class="fm-toolbar">
                        <div>
                            <button class="btn btn-sm btn-primary" id="btnNewFolder" onclick="showCreateFolderModal(currentFolderId)">
                                <i class="fa fa-folder"></i> New Folder
                            </button>
                            <button class="btn btn-sm btn-success" id="btnUpload" onclick="toggleDropzone()">
                                <i class="fa fa-upload"></i> Upload File
                            </button>
                            <button class="btn btn-sm btn-warning" id="btnArchive" onclick="toggleArchivedView()">
                                <i class="fa fa-archive"></i> <span id="archiveBtnText">Archived</span>
                            </button>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <div class="btn-group fm-view-toggle" role="group" title="Switch view">
                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewGrid" onclick="setView('grid')" title="Grid view"><i class="fa fa-th-large"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewTable" onclick="setView('table')" title="Table view"><i class="fa fa-list"></i></button>
                            </div>
                            <div style="position:relative; width:240px;">
                                <input type="text" id="fmSearchInput" class="form-control form-control-sm" placeholder="Search files & folders..." style="padding-right:30px;">
                                <button type="button" id="fmSearchClear" title="Clear search" style="display:none;position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;line-height:1;color:#999;cursor:pointer;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <span class="text-muted" id="itemCount" style="font-size: 13px;"></span>
                        </div>
                    </div>

                    <!-- Breadcrumb -->
                    <div class="fm-breadcrumb" id="breadcrumbContainer">
                        <a onclick="navigateToFolder(null)"><i class="fa fa-home"></i> Root</a>
                    </div>

                    <!-- Hidden file input — kept outside dropzone so resetDropzone() never destroys it -->
                    <input type="file" id="fileInput" style="display:none;"
                           accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip,.rar" multiple>

                    <!-- Drag & Drop Upload Zone -->
                    <div class="fm-dropzone" id="dropzone">
                        <i class="fa fa-cloud-upload" style="font-size: 36px;"></i>
                        <p class="mb-1">Drag & drop files here or click to browse</p>
                        <p class="mb-0" style="font-size:12px;">Allowed: PDF, JPG, PNG, DOCX, XLSX, ZIP, RAR &bull; Max 120 files &bull; 500MB total</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('fileInput').click()">
                            Browse Files
                        </button>
                    </div>

                    <!-- Bulk download bar (grid & table view) -->
                    <div id="tvBulkBar" style="display:none;align-items:center;gap:10px;background:#e8f4fd;border:1px solid #b8d9f5;border-radius:5px;padding:6px 12px;margin:6px 8px 0;font-size:13px;">
                        <i class="fa fa-check-square-o text-primary"></i>
                        <span id="tvSelCount">0 file(s) selected</span>
                        <button class="btn btn-sm btn-success" onclick="tvBulkDownload()"><i class="fa fa-download"></i> Download Selected</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="tvClearSelection()"><i class="fa fa-times"></i> Deselect All</button>
                    </div>

                    <!-- File List -->
                    <div class="fm-body" id="fileListContainer">
                        <div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Create Folder</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="newFolderParentId">
                <div class="form-group">
                    <label>Folder Name</label>
                    <input type="text" class="form-control" id="newFolderName" placeholder="Enter folder name">
                </div>
                <div class="form-group mb-0">
                    <div class="custom-control custom-checkbox mb-1">
                        <input type="checkbox" class="custom-control-input" id="newFolderIsMdo">
                        <label class="custom-control-label" for="newFolderIsMdo">MDO Folder</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="newFolderIsTelehealth">
                        <label class="custom-control-label" for="newFolderIsTelehealth">Telehealth Folder</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="createFolder()">Create</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Rename</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="renameItemId">
                <input type="hidden" id="renameItemType">
                <div class="form-group">
                    <label>New Name</label>
                    <input type="text" class="form-control" id="renameInput" placeholder="Enter new name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="renameItem()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="previewFileName">Preview</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="preview-container" id="previewContainer">
                    <div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading preview...</div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    var currentFolderId  = null;
    var selectedAgencyId = '{{ $selectedAgencyId ?? "" }}';
    var csrfToken        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var currentView      = 'grid'; // 'grid' | 'table'
    var canLinkChart     = {{ auth()->user()->agency_fk == '' ? 'true' : 'false' }};

    function switchAgency(agencyId) {
        selectedAgencyId = agencyId;
        currentFolderId = null;
        if (agencyId) {
            loadFolderTree();
            loadFiles(null);
        } else {
            $('#folderTree').html('<div class="fm-empty" style="padding:20px;"><i class="fa fa-info-circle"></i> Select an agency</div>');
            $('#fileListContainer').html('<div class="fm-empty"><i class="fa fa-info-circle"></i>Please select an agency to view files</div>');
            $('#itemCount').text('');
            $('#breadcrumbContainer').html('<a onclick="navigateToFolder(null)"><i class="fa fa-home"></i> Root</a>');
        }
    }

    // Initialize
    $(document).ready(function () {
        updateUploadButtonState();
        // Check if a specific folder was requested via URL param
        var urlParams  = new URLSearchParams(window.location.search);
        var initFolder = urlParams.get('folder_id') ? parseInt(urlParams.get('folder_id')) : null;
        if (initFolder) currentFolderId = initFolder; // set before tree renders so it highlights correctly

        if (selectedAgencyId) {
            loadFolderTree();
            loadFiles(initFolder);
        } else {
            @if(isset($isSuperAdmin) && $isSuperAdmin)
                $('#folderTree').html('<div class="fm-empty" style="padding:20px;"><i class="fa fa-info-circle"></i> Select an agency</div>');
                $('#fileListContainer').html('<div class="fm-empty"><i class="fa fa-info-circle"></i>Please select an agency to view files</div>');
            @else
                loadFolderTree();
                loadFiles(initFolder);
            @endif
        }
        setupDragDrop();
    });

    // ========== FOLDER TREE ==========
    function loadFolderTree() {
        $.ajax({
            url: '/file-manager/folder/tree',
            type: 'GET',
            data: { agency_id: selectedAgencyId },
            success: function (res) {
                if (res.status && res.data) {
                    var html = '<ul>';
                    html += '<li>';
                    html += '  <div class="tree-row">';
                    html += '    <span class="tree-toggle no-children"></span>';
                    html += '    <span class="tree-item' + (currentFolderId === null ? ' active' : '') + '" onclick="navigateToFolder(null)"><i class="fa fa-home"></i> Root</span>';
                    html += '  </div>';
                    html += '</li>';
                    html += buildTreeHtml(res.data);
                    html += '</ul>';
                    $('#folderTree').html(html);
                }
            }
        });
    }

    function buildTreeHtml(folders) {
        var html = '';
        folders.forEach(function (folder) {
            var hasChildren = folder.children_recursive && folder.children_recursive.length > 0;
            var isActive    = currentFolderId == folder.id;
            var uid         = 'tree-children-' + folder.id;

            html += '<li>';
            html += '  <div class="tree-row">';

            // Toggle arrow — only shown when has children
            if (hasChildren) {
                html += '    <span class="tree-toggle' + (isActive ? ' open' : '') + '" onclick="toggleTreeNode(this, \'' + uid + '\')">';
                html += '      <i class="fa fa-chevron-right"></i>';
                html += '    </span>';
            } else {
                html += '    <span class="tree-toggle no-children"><i class="fa fa-chevron-right"></i></span>';
            }

            // Folder label
            var treeBadges = '';
            if (folder.is_mdo)        treeBadges += '<span class="badge-mdo" style="font-size:9px;padding:1px 4px;">MDO</span>';
            if (folder.is_telehealth) treeBadges += '<span class="badge-th" style="font-size:9px;padding:1px 4px;">Telehealth</span>';
            html += '    <span class="tree-item' + (isActive ? ' active' : '') + '" onclick="navigateToFolder(' + folder.id + ')">';
            html += '      <i class="fa fa-folder"></i> ' + escapeHtml(folder.name) + treeBadges;
            html += '    </span>';
            html += '  </div>';

            // Children — pre-expanded if active folder is inside
            if (hasChildren) {
                var childrenOpen = isActiveInChildren(folder.children_recursive, currentFolderId) || isActive;
                html += '  <ul class="tree-children' + (childrenOpen ? ' open' : '') + '" id="' + uid + '">';
                html += buildTreeHtml(folder.children_recursive);
                html += '  </ul>';
            }

            html += '</li>';
        });
        return html;
    }

    function isActiveInChildren(folders, targetId) {
        for (var i = 0; i < folders.length; i++) {
            if (folders[i].id == targetId) return true;
            if (folders[i].children_recursive && isActiveInChildren(folders[i].children_recursive, targetId)) return true;
        }
        return false;
    }

    function toggleTreeNode(toggleEl, uid) {
        var $toggle   = $(toggleEl);
        var $children = $('#' + uid);
        var isOpen    = $children.hasClass('open');
        $children.toggleClass('open', !isOpen);
        $toggle.toggleClass('open', !isOpen);
    }

    // ========== FILE LISTING ==========
    var searchTimer = null;

    function loadFiles(folderId) {
        currentFolderId = folderId;
        // Clear search when navigating to a folder
        $('#fmSearchInput').val('');
        $('#fmSearchClear').hide();
        updateUploadButtonState();
        doLoadFiles(folderId, '');
    }

    function doLoadFiles(folderId, search) {
        $('#fileListContainer').html('<div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

        var url = '/file-manager/files?agency_id=' + encodeURIComponent(selectedAgencyId);
        if (folderId && !search) url += '&folder_id=' + folderId;
        if (search) url += '&search=' + encodeURIComponent(search);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
                if (res.status && res.data) {
                    if (!search) {
                        renderBreadcrumb(res.data.breadcrumb);
                        loadFolderTree();
                    } else {
                        $('#breadcrumbContainer').html('<span class="text-muted"><i class="fa fa-search"></i> Search results for: <strong>' + escapeHtml(search) + '</strong></span>');
                    }
                    renderFileList(res.data.folders, res.data.files);
                }
            },
            error: function () {
                $('#fileListContainer').html('<div class="fm-empty"><i class="fa fa-exclamation-triangle"></i>Failed to load files</div>');
            }
        });
    }

    // Search input — debounced 300ms
    $(document).on('input', '#fmSearchInput', function () {
        var val = $(this).val().trim();
        $('#fmSearchClear').toggle(val.length > 0);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            if (isArchivedView) {
                loadArchivedItems(val);
                renderArchivedBreadcrumb();
                if (val) {
                    $('#breadcrumbContainer').append(' &nbsp;<span class="text-muted" style="font-size:12px;">— results for: <strong>' + escapeHtml(val) + '</strong></span>');
                }
            } else if (currentView === 'table') {
                loadTableView(val);
            } else {
                doLoadFiles(val ? null : currentFolderId, val);
            }
        }, 300);
    });

    // Clear search button
    $(document).on('click', '#fmSearchClear', function () {
        $('#fmSearchInput').val('');
        $('#fmSearchClear').hide();
        if (isArchivedView) {
            renderArchivedBreadcrumb();
            loadArchivedItems('');
        } else if (currentView === 'table') {
            loadTableView('');
        } else {
            doLoadFiles(currentFolderId, '');
        }
    });

    function renderBreadcrumb(breadcrumb) {
        var html = '<a onclick="navigateToFolder(null)"><i class="fa fa-home"></i> Root</a>';
        if (breadcrumb && breadcrumb.length > 0) {
            breadcrumb.forEach(function (item) {
                html += '<span class="separator">/</span>';
                html += '<a onclick="navigateToFolder(' + item.id + ')">' + escapeHtml(item.name) + '</a>';
            });
        }
        $('#breadcrumbContainer').html(html);
    }

    function renderFileList(folders, files) {
        if (currentView === 'table') {
            // table view fetches its own data via loadTableView — skip grid render
            return;
        }
        var html = '';
        var totalItems = (folders ? folders.length : 0) + (files ? files.length : 0);
        $('#itemCount').text(totalItems + ' item(s)');

        if (totalItems === 0) {
            html = '<div class="fm-empty"><i class="fa fa-folder-open-o"></i>This folder is empty</div>';
            $('#fileListContainer').html(html);
            return;
        }

        // Folders first
        if (folders && folders.length > 0) {
            folders.forEach(function (folder) {
                var badges = '';
                if (folder.is_mdo)       badges += '<span class="badge-mdo">MDO</span>';
                if (folder.is_telehealth) badges += '<span class="badge-th">Telehealth</span>';
                html += '<div class="fm-item" ondblclick="navigateToFolder(' + folder.id + ')">';
                html += '  <div class="fm-icon folder-icon"><i class="fa fa-folder"></i></div>';
                html += '  <div class="fm-info">';
                html += '    <div class="fm-name">' + escapeHtml(folder.name) + badges + '</div>';
                html += '    <div class="fm-meta">Folder &bull; ' + formatDate(folder.created_at) + '</div>';
                html += '  </div>';
                html += '  <div class="fm-actions">';
                html += '    <button class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation(); showRenameModal(\'folder\', ' + folder.id + ', \'' + escapeAttr(folder.name) + '\')" title="Rename"><i class="fa fa-pencil"></i></button>';
                html += '    <button class="btn btn-outline-danger btn-sm" onclick="event.stopPropagation(); deleteFolder(' + folder.id + ', \'' + escapeAttr(folder.name) + '\')" title="Archive"><i class="fa fa-archive"></i></button>';
                html += '  </div>';
                html += '</div>';
            });
        }

        // Files
        if (files && files.length > 0) {
            files.forEach(function (file) {
                var iconClass = getFileIconClass(file.file_type);
                var isChecked = tvSelectedIds.has(file.id) ? ' checked' : '';
                html += '<div class="fm-item" id="fm-item-' + file.id + '">';
                html += '  <input type="checkbox" class="tv-row-cb fm-grid-cb" data-id="' + file.id + '"' + isChecked + ' onclick="event.stopPropagation();" style="position:absolute;top:6px;left:6px;width:15px;height:15px;cursor:pointer;z-index:2;">';
                html += '  <div class="fm-icon file-icon ' + iconClass + '"><i class="fa ' + getFileIcon(file.file_type) + '"></i></div>';
                html += '  <div class="fm-info">';
                html += '    <div class="fm-name">' + escapeHtml(file.file_name) + '</div>';
                html += '    <div class="fm-meta">' + (file.file_type || '').toUpperCase() + ' &bull; ' + formatFileSize(file.file_size) + ' &bull; ' + formatDateTime(file.created_at) + (file.uploaded_by ? ' &bull; <i class="fa fa-user"></i> ' + escapeHtml(file.uploaded_by) : '') + '</div>';
                html += '  </div>';
                html += '  <div class="fm-actions">';
                if (isPreviewable(file.file_type)) {
                    html += '    <button class="btn btn-outline-info btn-sm" onclick="event.stopPropagation(); previewFile(' + file.id + ', \'' + escapeAttr(file.file_name) + '\', \'' + file.file_type + '\')" title="Preview"><i class="fa fa-eye"></i></button>';
                }
                html += '    <button class="btn btn-outline-success btn-sm" onclick="event.stopPropagation(); downloadFile(' + file.id + ')" title="Download"><i class="fa fa-download"></i></button>';
                if (canLinkChart && !file.patient_id) {
                    html += '    <button class="btn btn-outline-warning btn-sm" onclick="event.stopPropagation(); openLinkChartModal(' + file.id + ', \'' + escapeAttr(file.file_name) + '\','+file.agency_id+')" title="Link Chart"><i class="fa fa-link"></i></button>';
                }
                html += '    <button class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation(); showRenameModal(\'file\', ' + file.id + ', \'' + escapeAttr(file.file_name) + '\')" title="Rename"><i class="fa fa-pencil"></i></button>';
                html += '    <button class="btn btn-outline-danger btn-sm" onclick="event.stopPropagation(); deleteFile(' + file.id + ', \'' + escapeAttr(file.file_name) + '\')" title="Archive"><i class="fa fa-archive"></i></button>';
                html += '  </div>';
                html += '</div>';
            });
        }

        $('#fileListContainer').html(html);
    }

    function navigateToFolder(folderId) {
        if (isArchivedView) {
            isArchivedView = false;
            $('#archiveBtnText').text('Archived');
            $('#btnArchive').removeClass('btn-secondary').addClass('btn-warning');
            $('#btnNewFolder, #btnUpload').show();
        }
        if (currentView === 'table') {
            // In table view, folder navigation just reloads all files
            loadTableView($('#fmSearchInput').val().trim());
        } else {
            loadFiles(folderId);
        }
    }

    // ========== CREATE FOLDER ==========
    function showCreateFolderModal(parentId) {
        $('#newFolderParentId').val(parentId || '');
        $('#newFolderName').val('');
        $('#newFolderIsMdo').prop('checked', false);
        $('#newFolderIsTelehealth').prop('checked', false);
        $('#createFolderModal').modal('show');
        setTimeout(function () { $('#newFolderName').focus(); }, 300);
    }

    function createFolder() {
        var name = $('#newFolderName').val().trim();
        if (!name) {
            toastr.warning('Please enter a folder name');
            return;
        }

        var parentId     = $('#newFolderParentId').val() || null;
        var isMdo        = $('#newFolderIsMdo').is(':checked') ? 1 : 0;
        var isTelehealth = $('#newFolderIsTelehealth').is(':checked') ? 1 : 0;

        if (!isMdo && !isTelehealth) {
            toastr.warning('Please select at least one type: MDO Folder or Telehealth Folder');
            return;
        }

        $.ajax({
            url: '/file-manager/folder/create',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { name: name, parent_id: parentId, agency_id: selectedAgencyId, is_mdo: isMdo, is_telehealth: isTelehealth },
            success: function (res) {
                $('#createFolderModal').modal('hide');
                toastr.success('Folder created successfully');
                loadFiles(currentFolderId);
                loadFolderTree();
            },
            error: function (xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to create folder';
                
                showErrorAndLoginRedirection(xhr);
            }
        });
    }

    // ========== UPLOAD ==========
    function toggleDropzone() {
        $('#dropzone').toggleClass('active');
    }

    function setupDragDrop() {
        var dropzone = document.getElementById('dropzone');
        var fileInput = document.getElementById('fileInput');

        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                uploadFilesToServer(e.dataTransfer.files);
            }
        });

        dropzone.addEventListener('click', function (e) {
            if (e.target === dropzone || e.target.tagName === 'P' || e.target.tagName === 'I') {
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', function () {
            if (fileInput.files.length > 0) {
                uploadFilesToServer(fileInput.files);
                fileInput.value = '';
            }
        });
    }

    var allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'xlsx', 'zip', 'rar'];
    var maxTotalSizeBytes = 500 * 1024 * 1024; // 500MB total across all files
    var maxFileCount      = 120;

    function uploadFilesToServer(files) {
        // ---- Client-side validation before sending ----
        if (files.length > maxFileCount) {
            toastr.error('You can upload a maximum of 120 files at a time.');
            return;
        }

        var validFiles   = [];
        var invalidFiles = [];

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext  = file.name.split('.').pop().toLowerCase();

            if (allowedExtensions.indexOf(ext) === -1) {
                invalidFiles.push('"' + escapeHtml(file.name) + '" — invalid file type. Allowed: PDF, JPG, PNG, DOCX, XLSX, ZIP, RAR.');
            } else {
                validFiles.push(file);
            }
        }

        // Show validation errors for rejected files immediately
        if (invalidFiles.length > 0) {
            invalidFiles.forEach(function(msg) { toastr.error(msg); });
        }

        // Nothing valid to upload
        if (validFiles.length === 0) {
            return;
        }

        // Check total size of all valid files combined
        var totalSize = 0;
        for (var i = 0; i < validFiles.length; i++) {
            totalSize += validFiles[i].size;
        }
        if (totalSize > maxTotalSizeBytes) {
            toastr.error('Total upload size is ' + formatFileSize(totalSize) + '. Maximum allowed is 500MB total.');
            return;
        }

        var formData  = new FormData();
        var fileCount = validFiles.length;

        for (var i = 0; i < fileCount; i++) {
            formData.append('files[]', validFiles[i]);
        }
        if (currentFolderId) {
            formData.append('folder_id', currentFolderId);
        }
        if (selectedAgencyId) {
            formData.append('agency_id', selectedAgencyId);
        }

        // Show uploading state with progress bar
        var fileNames = [];
        for (var i = 0; i < Math.min(fileCount, 3); i++) {
            fileNames.push(escapeHtml(validFiles[i].name));
        }
        var displayText = fileNames.join(', ');
        if (fileCount > 3) displayText += ' and ' + (fileCount - 3) + ' more...';

        $('#dropzone').html(
            '<i class="fa fa-spinner fa-spin" style="font-size:36px;"></i>' +
            '<p>Uploading ' + fileCount + ' file(s)...</p>' +
            '<p style="font-size:12px;">' + displayText + '</p>' +
            '<div class="progress mt-2" style="max-width:400px;margin:0 auto;"><div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgressBar" style="width:0%">0%</div></div>'
        );

        $.ajax({
            url: '/file-manager/file/upload',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgressBar').css('width', percent + '%').text(percent + '%');
                    }
                });
                return xhr;
            },
            success: function (res) {
                resetDropzone();
                // Show per-file failures returned by the server
                if (res.data && Array.isArray(res.data)) {
                    var failed = res.data.filter(function(r) { return !r.status; });
                    failed.forEach(function(f) {
                        toastr.error('"' + f.file_name + '": ' + f.message);
                    });
                }
                if (res.status) {
                    toastr.success(res.message || 'Files uploaded successfully');
                } else if (!res.status && !(res.data && Array.isArray(res.data))) {
                    toastr.error(res.message || 'Upload failed');
                }
                reloadCurrentView();
            },
            error: function (xhr) {
                resetDropzone();
                var json = xhr.responseJSON;
                if (xhr.status === 422 && json) {
                    // Laravel validation errors — show each field error
                    if (json.errors) {
                        $.each(json.errors, function(field, messages) {
                            messages.forEach(function(msg) { toastr.error(msg); });
                        });
                    } else if (json.message) {
                        toastr.error(json.message);
                    } else {
                        toastr.error('Validation failed. Please check the files and try again.');
                    }
                } else if (json && json.message) {
                    toastr.error(json.message);
                } else {
                    toastr.error('Upload failed. Please try again.');
                    showErrorAndLoginRedirection(xhr);
                }
            }
        });
    }

    function resetDropzone() {
        $('#dropzone').html(
            '<i class="fa fa-cloud-upload" style="font-size: 36px;"></i>' +
            '<p class="mb-1">Drag & drop files here or click to browse</p>' +
            '<p class="mb-0" style="font-size:12px;">Allowed: PDF, JPG, PNG, DOCX, XLSX, ZIP, RAR &bull; Max 120 files &bull; 500MB total</p>' +
            '<button class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById(\'fileInput\').click()">Browse Files</button>'
        );
        // Do NOT call setupDragDrop() here — listeners are bound once on page load
        // and #fileInput lives outside #dropzone so it is never destroyed
    }

    // ========== RENAME ==========
    function showRenameModal(type, id, currentName) {
        $('#renameItemType').val(type);
        $('#renameItemId').val(id);
        $('#renameInput').val(currentName);
        $('#renameModal').modal('show');
        setTimeout(function () { $('#renameInput').focus().select(); }, 300);
    }

    function renameItem() {
        var type = $('#renameItemType').val();
        var id = $('#renameItemId').val();
        var newName = $('#renameInput').val().trim();

        if (!newName) {
            toastr.warning('Please enter a name');
            return;
        }

        $.ajax({
            url: '/file-manager/file/rename',
            type: 'PUT',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { type: type, id: id, name: newName, agency_id: selectedAgencyId },
            success: function (res) {
                $('#renameModal').modal('hide');
                toastr.success('Renamed successfully');
                reloadCurrentView();
            },
            error: function (xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Rename failed';
                toastr.error(msg);
            }
        });
    }

    // ========== CONFIRM MODAL ==========
    function showConfirm(title, message, btnClass, btnText, callback) {
        $.confirm({
            title: title,
            content: message,
            type: 'blue',
            buttons: {
                confirm: {
                    text: btnText,
                    btnClass: btnClass,
                    action: function () {
                        callback();
                    }
                },
                cancel: {
                    text: 'CANCEL'
                }
            }
        });
    }

    // ========== DELETE ==========
    function deleteFile(id, name) {
        showConfirm('Archive File', 'Are you sure you want to archive file <strong>"' + escapeHtml(name) + '"</strong>?', 'btn-warning', 'Archive', function () {
            $.ajax({
                url: '/file-manager/file/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    toastr.success('File archived successfully');
                    reloadCurrentView();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Delete failed';
                    toastr.error(msg);
                }
            });
        });
    }

    function deleteFolder(id, name) {
        showConfirm('Archive Folder', 'Are you sure you want to archive folder <strong>"' + escapeHtml(name) + '"</strong> and all its contents?', 'btn-warning', 'Archive', function () {
            $.ajax({
                url: '/file-manager/folder/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    toastr.success('Folder archived successfully');
                    reloadCurrentView();
                    loadFolderTree();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Delete failed';
                    toastr.error(msg);
                }
            });
        });
    }

    // ========== DOWNLOAD ==========
    function downloadFile(id) {
        window.location.href = '/file-manager/file/download/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId);
    }

    // ========== PREVIEW ==========
    function previewFile(id, fileName, fileType) {
        $('#previewFileName').text(fileName);
        $('#previewContainer').html('<div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading preview...</div>');
        $('#previewModal').modal('show');

        $.ajax({
            url: '/file-manager/file/preview/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId),
            type: 'GET',
            success: function (res) {
                if (res.status && res.data && res.data.url) {
                    var html = '';
                    if (['jpg', 'jpeg', 'png', 'gif'].indexOf(fileType) !== -1) {
                        html = '<img src="' + res.data.url + '" alt="' + escapeAttr(fileName) + '">';
                    } else if (fileType === 'pdf') {
                        html = '<iframe src="' + res.data.url + '"></iframe>';
                    }
                    $('#previewContainer').html(html);
                } else {
                    $('#previewContainer').html('<div class="fm-empty">Preview not available</div>');
                }
            },
            error: function () {
                $('#previewContainer').html('<div class="fm-empty">Failed to load preview</div>');
            }
        });
    }

    // ========== HELPERS ==========
    function getFileIcon(type) {
        switch ((type || '').toLowerCase()) {
            case 'pdf': return 'fa-file-pdf-o';
            case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image-o';
            case 'docx': case 'doc': return 'fa-file-word-o';
            case 'xlsx': case 'xls': return 'fa-file-excel-o';
            default: return 'fa-file-o';
        }
    }

    function getFileIconClass(type) {
        switch ((type || '').toLowerCase()) {
            case 'pdf': return 'pdf';
            case 'jpg': case 'jpeg': case 'png': case 'gif': return 'image';
            case 'docx': case 'doc': return 'doc';
            case 'xlsx': case 'xls': return 'excel';
            default: return '';
        }
    }

    function isPreviewable(type) {
        return ['jpg', 'jpeg', 'png', 'gif', 'pdf'].indexOf((type || '').toLowerCase()) !== -1;
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 B';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) +
               ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function escapeAttr(str) {
        if (!str) return '';
        return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // ========== ARCHIVED VIEW ==========
    var isArchivedView      = false;
    var archivedFolderStack = []; // breadcrumb stack [{id, name}, ...]
    var currentArchivedFolder = null;

    function toggleArchivedView() {
        isArchivedView = !isArchivedView;

        if (isArchivedView) {
            archivedFolderStack   = [];
            currentArchivedFolder = null;
            $('#fmSearchInput').val('');
            $('#fmSearchClear').hide();
            $('#fmSearchInput').prop('disabled', false).attr('placeholder', 'Search archived...');

            $('#archiveBtnText').text('Back to Files');
            $('#btnArchive').removeClass('btn-warning').addClass('btn-secondary');
            $('#btnNewFolder, #btnUpload').hide();
            $('#dropzone').removeClass('active');
            renderArchivedBreadcrumb();
            loadArchivedItems();
        } else {
            archivedFolderStack   = [];
            currentArchivedFolder = null;
            $('#fmSearchInput').val('');
            $('#fmSearchClear').hide();
            $('#fmSearchInput').prop('disabled', false).attr('placeholder', 'Search files & folders...');

            $('#archiveBtnText').text('Archived');
            $('#btnArchive').removeClass('btn-secondary').addClass('btn-warning');
            $('#btnNewFolder, #btnUpload').show();
            loadFiles(currentFolderId);
        }
    }

    function loadArchivedItems(search) {
        search = search || '';
        $('#fileListContainer').html('<div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading archived items...</div>');

        var url, data = { agency_id: selectedAgencyId };
        if (search) data.search = search;

        if (currentArchivedFolder) {
            url = '/file-manager/folder/archived/' + currentArchivedFolder;
        } else {
            url = '/file-manager/file/archived';
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function (res) {
                if (res.status && res.data) {
                    renderArchivedList(res.data.folders, res.data.files);
                }
            },
            error: function () {
                $('#fileListContainer').html('<div class="fm-empty"><i class="fa fa-exclamation-triangle"></i>Failed to load archived items</div>');
            }
        });
    }

    function browseArchivedFolder(id, name) {
        archivedFolderStack.push({ id: id, name: name });
        currentArchivedFolder = id;
        $('#fmSearchInput').val('');
        $('#fmSearchClear').hide();
        renderArchivedBreadcrumb();
        loadArchivedItems();
    }

    function navigateArchivedBreadcrumb(index) {
        if (index === -1) {
            // Root archived
            archivedFolderStack   = [];
            currentArchivedFolder = null;
        } else {
            archivedFolderStack   = archivedFolderStack.slice(0, index + 1);
            currentArchivedFolder = archivedFolderStack[archivedFolderStack.length - 1].id;
        }
        $('#fmSearchInput').val('');
        $('#fmSearchClear').hide();
        renderArchivedBreadcrumb();
        loadArchivedItems();
    }

    function renderArchivedBreadcrumb() {
        var html = '<a style="cursor:pointer;" onclick="navigateArchivedBreadcrumb(-1)"><i class="fa fa-archive"></i> Archived Items</a>';
        archivedFolderStack.forEach(function (item, idx) {
            html += '<span class="separator">/</span>';
            if (idx < archivedFolderStack.length - 1) {
                html += '<a style="cursor:pointer;" onclick="navigateArchivedBreadcrumb(' + idx + ')">' + escapeHtml(item.name) + '</a>';
            } else {
                html += '<span>' + escapeHtml(item.name) + '</span>';
            }
        });
        $('#breadcrumbContainer').html(html);
    }

    function renderArchivedList(folders, files) {
        var html = '';
        var totalItems = (folders ? folders.length : 0) + (files ? files.length : 0);
        $('#itemCount').text(totalItems + ' archived item(s)');

        if (totalItems === 0) {
            html = '<div class="fm-empty"><i class="fa fa-archive"></i>No archived items</div>';
            $('#fileListContainer').html(html);
            return;
        }

        // Archived folders
        if (folders && folders.length > 0) {
            folders.forEach(function (folder) {
                var path = folder.folder_path || 'Root';
                html += '<div class="fm-item archived">';
                html += '  <div class="fm-icon folder-icon"><i class="fa fa-folder"></i></div>';
                html += '  <div class="fm-info">';
                html += '    <div class="fm-name">' + escapeHtml(folder.name) + ' <span class="archived-badge">Archived</span></div>';
                html += '    <div class="fm-meta">';
                html += '      <i class="fa fa-map-marker" style="color:#aaa;"></i> ' + escapeHtml(path);
                html += '      &bull; Archived: ' + formatDate(folder.deleted_at);
                html += '    </div>';
                html += '  </div>';
                html += '  <div class="fm-actions">';
                html += '    <button class="btn btn-outline-primary btn-sm" onclick="browseArchivedFolder(' + folder.id + ', \'' + escapeAttr(folder.name) + '\')" title="Browse"><i class="fa fa-folder-open"></i> Open</button>';
                html += '    <button class="btn btn-outline-success btn-sm" onclick="restoreFolder(' + folder.id + ', \'' + escapeAttr(folder.name) + '\')" title="Restore"><i class="fa fa-undo"></i> Restore</button>';
                html += '  </div>';
                html += '</div>';
            });
        }

        // Archived files
        if (files && files.length > 0) {
            files.forEach(function (file) {
                var iconClass = getFileIconClass(file.file_type);
                var path = file.folder_path || 'Root';
                html += '<div class="fm-item archived">';
                html += '  <div class="fm-icon file-icon ' + iconClass + '"><i class="fa ' + getFileIcon(file.file_type) + '"></i></div>';
                html += '  <div class="fm-info">';
                html += '    <div class="fm-name">' + escapeHtml(file.file_name) + ' <span class="archived-badge">Archived</span></div>';
                html += '    <div class="fm-meta">';
                html += '      <i class="fa fa-map-marker" style="color:#aaa;"></i> ' + escapeHtml(path);
                html += '      &bull; ' + (file.file_type || '').toUpperCase() + ' &bull; ' + formatFileSize(file.file_size) + ' &bull; Archived: ' + formatDate(file.deleted_at);
                html += '    </div>';
                html += '  </div>';
                html += '  <div class="fm-actions">';
                html += '    <a class="btn btn-outline-info btn-sm" href="/file-manager/file/download/' + file.id + '?agency_id=' + encodeURIComponent(selectedAgencyId) + '" title="Download"><i class="fa fa-download"></i></a>';
                html += '    <button class="btn btn-outline-success btn-sm" onclick="restoreFile(' + file.id + ', \'' + escapeAttr(file.file_name) + '\')" title="Restore"><i class="fa fa-undo"></i> Restore</button>';
                html += '  </div>';
                html += '</div>';
            });
        }

        $('#fileListContainer').html(html);
    }

    function restoreFile(id, name) {
        showConfirm('Restore File', 'Are you sure you want to restore file <strong>"' + escapeHtml(name) + '"</strong>?', 'btn-success', 'Restore', function () {
            $.ajax({
                url: '/file-manager/file/restore/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    toastr.success('File restored successfully');
                    loadArchivedItems();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Restore failed';
                    toastr.error(msg);
                }
            });
        });
    }

    function restoreFolder(id, name) {
        showConfirm('Restore Folder', 'Are you sure you want to restore folder <strong>"' + escapeHtml(name) + '"</strong> and all its contents?', 'btn-success', 'Restore', function () {
            $.ajax({
                url: '/file-manager/folder/restore/' + id + '?agency_id=' + encodeURIComponent(selectedAgencyId),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    toastr.success('Folder restored successfully');
                    loadArchivedItems();
                    loadFolderTree();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Restore failed';
                    toastr.error(msg);
                }
            });
        });
    }

    // ========== RELOAD HELPER ==========
    function reloadCurrentView() {
        if (currentView === 'table') {
            loadTableView($('#fmSearchInput').val().trim());
        } else {
            loadFiles(currentFolderId);
        }
    }

    // ========== VIEW TOGGLE ==========
    function setView(view) {
        currentView = view;
        if (view === 'grid') {
            $('#btnViewGrid').addClass('active');
            $('#btnViewTable').removeClass('active');
            tvClearSelection();
            doLoadFiles(currentFolderId, $('#fmSearchInput').val().trim());
        } else {
            $('#btnViewTable').addClass('active');
            $('#btnViewGrid').removeClass('active');
            loadTableView($('#fmSearchInput').val().trim(), 1);
        }
    }

    // ========== UPLOAD ENFORCEMENT (folder required) ==========
    function updateUploadButtonState() {
        if (currentFolderId) {
            $('#btnUpload').prop('disabled', false).attr('title', 'Upload File');
        } else {
            $('#btnUpload').prop('disabled', true).attr('title', 'Select a folder to upload files');
            $('#dropzone').removeClass('active');
        }
    }

    // ========== TABLE VIEW ==========
    var tvSelectedIds = new Set();

    function loadTableView(search, page) {
        page = page || 1;
        $('#fileListContainer').html('<div class="fm-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        tvHideBulkBar();

        var url = '/file-manager/files/all?agency_id=' + encodeURIComponent(selectedAgencyId) + '&page=' + page;
        if (search) url += '&search=' + encodeURIComponent(search);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (html) {
                $('#fileListContainer').html(html);
                // Restore checked state for already-selected rows
                $('.tv-row-cb').each(function () {
                    if (tvSelectedIds.has(parseInt($(this).data('id')))) $(this).prop('checked', true);
                });
                tvSyncSelectAll();
                // Update item count
                var total = $('#tv-summary').data('total');
                if (total !== undefined) $('#itemCount').text(total + ' file(s)');
            },
            error: function () {
                $('#fileListContainer').html('<div class="fm-empty"><i class="fa fa-exclamation-triangle"></i> Failed to load files</div>');
            }
        });
    }

    // Pagination click — table view
    $('body').on('click', '#fileListContainer .pagination a', function (e) {
        e.preventDefault();
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var page = $(this).attr('href').split('page=')[1];
        loadTableView($('#fmSearchInput').val().trim(), page);
    });

    // ---- Bulk selection ----
    $(document).on('change', '#tvSelectAll', function () {
        var checked = $(this).is(':checked');
        $('.tv-row-cb').each(function () {
            var id = parseInt($(this).data('id'));
            checked ? tvSelectedIds.add(id) : tvSelectedIds.delete(id);
            $(this).prop('checked', checked);
        });
        tvUpdateBulkBar();
    });

    $(document).on('change', '.tv-row-cb', function () {
        var id = parseInt($(this).data('id'));
        if ($(this).is(':checked')) {
            tvSelectedIds.add(id);
            $('#fm-item-' + id).addClass('tv-selected');
        } else {
            tvSelectedIds.delete(id);
            $('#fm-item-' + id).removeClass('tv-selected');
        }
        tvSyncSelectAll();
        tvUpdateBulkBar();
    });

    function tvSyncSelectAll() {
        var visible = [];
        $('.tv-row-cb').each(function () { visible.push(parseInt($(this).data('id'))); });
        var allChecked = visible.length > 0 && visible.every(function (id) { return tvSelectedIds.has(id); });
        $('#tvSelectAll').prop('checked', allChecked);
    }

    function tvUpdateBulkBar() {
        if (tvSelectedIds.size > 0) {
            $('#tvSelCount').text(tvSelectedIds.size + ' file(s) selected');
            $('#tvBulkBar').css('display', 'flex');
        } else {
            tvHideBulkBar();
        }
    }

    function tvHideBulkBar() {
        $('#tvBulkBar').hide();
        $('#tvSelectAll').prop('checked', false);
    }

    function tvClearSelection() {
        tvSelectedIds.clear();
        $('.tv-row-cb').prop('checked', false);
        $('.fm-item').removeClass('tv-selected');
        tvHideBulkBar();
    }

    function tvBulkDownload() {
        if (tvSelectedIds.size === 0) return;
        var form = $('<form method="POST" action="/file-manager/files/bulk-download" style="display:none">');
        form.append('<input name="_token" value="' + csrfToken + '">');
        tvSelectedIds.forEach(function (id) { form.append('<input name="ids[]" value="' + id + '">'); });
        $('body').append(form);
        form.submit();
        form.remove();
    }
</script>

<!-- ===== Add to Patient Chart Modal ===== -->
<div class="modal fade" id="linkChartModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-paperclip mr-1"></i> Add Document — <span id="lcmFileName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="lcmFileId">
                <input type="hidden" id="lcmAgencyId">

                <div class="form-group" style="position:relative;">
                    <label class="col-form-label">Chart <span class="text-danger">*</span></label>
                    <input type="text" id="lcmPatientSearch" class="form-control" placeholder="Search by ID, Name, Mobile..." autocomplete="off">
                    <input type="hidden" id="lcmSelectedPatientId">
                    <div id="lcmSearchResults" style="position:absolute;z-index:9999;background:#fff;border:1px solid #dee2e6;border-top:none;width:100%;max-height:180px;overflow-y:auto;display:none;border-radius:0 0 4px 4px;"></div>
                    <span class="text-danger" id="lcmPatientError" style="font-size:12px;display:none;">Please select a Chart.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Document Name<span class="text-danger">*</span></label>
                    <input type="text" id="lcmDocumentName" class="form-control" placeholder="Enter document name">
                    <span class="text-danger" id="lcmDocumentNameError" style="font-size:12px;display:none;">Document name is required.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Request Services <span class="text-danger">*</span></label>
                    <select class="form-control w-100" id="lcmRequestService" onchange="lcmLoadServices()">
                        <option value="">Select Request Service</option>
                    </select>
                    <span class="text-danger" id="lcmRequestServiceError" style="font-size:12px;display:none;">Please select a request service.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Services <span class="text-danger">*</span></label>
                    <select class="js-example-basic-multiple w-100" multiple="multiple" id="lcmServices">
                    </select>
                    <span class="text-danger" id="lcmServicesError" style="font-size:12px;display:none;">Please select at least one service.</span>
                </div>

                <div class="form-group">
                    <label class="col-form-label">Document Completed Date <span class="text-danger">*</span></label>
                    <input type="text" id="lcmDocumentDate" class="form-control document_completed_date_lcm" placeholder="MM/DD/YYYY" autocomplete="off" readonly>
                    <span class="text-danger" id="lcmDocumentDateError" style="font-size:12px;display:none;">Please select a document completed date.</span>
                </div>

                <div class="form-check form-check-primary mb-2">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="lcmInfoOnly" value="1">
                        <i class="input-helper"></i> Upload for Info Only
                    </label>
                    <div class="text-muted" style="font-size:12px;">When checked, the document is for information only. When unchecked, signatures or stamps are allowed.</div>
                </div>

                @if(auth()->user()->agency_fk == '')
                <div class="form-check form-check-primary mb-0">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="lcmInternalUse" value="1">
                        <i class="input-helper"></i> Internal Use Only
                    </label>
                    <div class="text-muted" style="font-size:12px;">If this checkbox is selected, the agency will not receive any emails.</div>
                </div>

                <div class="form-group mt-2 mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lcmDocumentReview" value="1">
                            <i class="input-helper"></i> Choose for Document Approval
                        </label>
                    </div>
                </div>

                <div id="lcmDocumentApprovalWrap" style="display:none;">
                    <div class="form-group">
                        <label class="col-form-label">User:<span class="text-danger">*</span></label>
                        <input type="text" id="lcmDocumentApprovalUser" class="form-control" placeholder="Search user...">
                        <span class="text-danger" id="lcmDocumentApprovalUserError" style="font-size:12px;display:none;">Please select a user.</span>
                    </div>
                </div>

                <div class="form-group mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lcmMedicationList" value="1">
                            <i class="input-helper"></i> Medication List
                        </label>
                    </div>
                </div>

                <div class="form-group mb-1">
                    <div class="form-check form-check-primary mb-0">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="lcmInsuranceElg" value="1">
                            <i class="input-helper"></i> Insurance Elg
                        </label>
                    </div>
                </div>
                <span class="text-danger" id="lcmMedInsuranceError" style="font-size:12px;display:none;">Medication List and Insurance Elg cannot both be selected.</span>

                <div class="form-group row align-items-center mb-0 mt-1">
                    <div class="col-4">
                        <div class="form-check form-check-primary mb-0">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="lcmMdoTag" value="1">
                                <i class="input-helper"></i> MDO Tag
                            </label>
                        </div>
                    </div>
                    <div class="col-8">
                        <select class="form-control w-100" id="lcmMdoSource" style="display:none;">
                            <option value="">Select MDO Source</option>
                            @foreach($masterData as $master)
                                @if($master->master_type_fk == '35')
                                    <option value="{{ $master->id }}">{{ $master->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <span class="text-danger" id="lcmMdoTagError" style="font-size:12px;display:none;">Please select an MDO Source.</span>
                @endif
            </div>
            <div class="modal-footer">
                <img src="{{ asset('ajax-loader.gif') }}" id="lcmLoader" style="display:none;height:28px;">
                <button type="button" class="btn btn-success" id="lcmSaveBtn" onclick="lcmSubmit()">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>
<script>
    // ===== Add to Patient Chart (File Manager index) =====
    var lcmFileId        = null;
    var lcmSearchTimer   = null;
    var lcmPatientType   = '';
    var lcmFileNameOrig  = '';
    var _DYNAMIC_DOC_APPROVED_USERS = {!! json_encode($dynamicDocApprovedUsers) !!};

    function openLinkChartModal(fileId, fileName, agencyId) {
        lcmFileId        = fileId;
        lcmPatientType   = '';
        lcmFileNameOrig  = fileName || '';
        $('#lcmFileId').val(fileId);
        $('#lcmAgencyId').val(agencyId);
        $('#lcmFileName').text(fileName);
        $('#lcmPatientSearch').val('');
        $('#lcmSelectedPatientId').val('');
        $('#lcmDocumentName').val(fileName || '');
        $('#lcmRequestService').html('<option value="">Select Request Service</option>');
        if ($('#lcmServices').data('select2')) { $('#lcmServices').select2('destroy'); }
        $('#lcmServices').html('');
        $('#lcmDocumentDate').val('');
        $('#lcmInfoOnly').prop('checked', false);
        $('#lcmInternalUse').prop('checked', false);
        $('#lcmDocumentReview').prop('checked', false).trigger('change');
        $('#lcmMedicationList').prop('checked', false);
        $('#lcmInsuranceElg').prop('checked', false);
        $('#lcmMdoTag').prop('checked', false);
        $('#lcmMdoSource').hide().val('');
        $('#lcmPatientError, #lcmDocumentNameError, #lcmRequestServiceError, #lcmServicesError, #lcmDocumentDateError, #lcmDocumentApprovalUserError, #lcmMedInsuranceError, #lcmMdoTagError').hide();
        $('#lcmSearchResults').hide().empty();
        $('#linkChartModal').modal('show');
        setTimeout(function () {
            $('#lcmServices').select2({ dropdownParent: $('#linkChartModal'), placeholder: 'Select Service', allowClear: true, width: '100%' });
        }, 300);
    }

    // Init datepicker once (idempotent)
    $('#linkChartModal').on('shown.bs.modal', function () {
        if (!$('#lcmDocumentDate').hasClass('hasDatepicker')) {
            $('#lcmDocumentDate').datepicker({ dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true });
        }
    });

    function lcmLoadRequestServices(patientId) {
        $('#lcmRequestService').html('<option value="">Loading...</option>');
        $('#lcmServices').html('').trigger('change');
        $.get('/ajax-request-service', { id: patientId }, function (res) {
            // Response already contains <option> tags including first empty option
            $('#lcmRequestService').html(res || '<option value="">Select Request Service</option>');
        });
    }

    function lcmLoadServices() {
        var requestServiceId = $('#lcmRequestService').val();
        if (!requestServiceId) {
            $('#lcmServices').html('').trigger('change');
            return;
        }
        $.get('/ajax-service', { id: lcmPatientType, document_id: requestServiceId, agency_id: $('#lcmAgencyId').val() }, function (res) {
            if ($('#lcmServices').data('select2')) { $('#lcmServices').select2('destroy'); }
            $('#lcmServices').html(res || '');
            $('#lcmServices').select2({ dropdownParent: $('#linkChartModal'), placeholder: 'Select Service', allowClear: true, width: '100%' });
        });
    }

    $('#lcmPatientSearch').on('input', function () {
        var search = $(this).val().trim();
        $('#lcmSelectedPatientId').val('');
        lcmPatientType = '';
        if (lcmSearchTimer) clearTimeout(lcmSearchTimer);
        if (search.length < 2) { $('#lcmSearchResults').hide().empty(); return; }
        lcmSearchTimer = setTimeout(function () {
            $.get('/search-patient-details', { q: search, agency_id: selectedAgencyId }, function (data) {
                var results = typeof data === 'string' ? JSON.parse(data) : data;
                $('#lcmSearchResults').empty();
                if (!results || !results.length) {
                    $('#lcmSearchResults').html('<div class="px-3 py-2 text-muted">No patients found.</div>').show();
                    return;
                }
                $.each(results, function (i, p) {
                    $('<div class="px-3 py-2" style="cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;">')
                        .text(p.name)
                        .data('id', p.id).data('name', p.name).data('type', p.type || 'Patient')
                        .hover(function () { $(this).css('background','#f0f7ff'); }, function () { $(this).css('background',''); })
                        .on('click', function () {
                            var selPatientId = $(this).data('id');
                            $('#lcmSelectedPatientId').val(selPatientId);
                            $('#lcmPatientSearch').val($(this).data('name'));
                            lcmPatientType = $(this).data('type');
                            $('#lcmSearchResults').hide();
                            $('#lcmPatientError').hide();
                            // Append portal ID to document name
                            var ext      = lcmFileNameOrig.lastIndexOf('.') > 0 ? lcmFileNameOrig.slice(lcmFileNameOrig.lastIndexOf('.')) : '';
                            var baseName = ext ? lcmFileNameOrig.slice(0, lcmFileNameOrig.lastIndexOf('.')) : lcmFileNameOrig;
                            $('#lcmDocumentName').val(baseName + ' - ' + selPatientId + ext);
                            $('#lcmRequestService').html('<option value="">Select Request Service</option>');
                            $('#lcmServices').html('').trigger('change');
                            lcmLoadRequestServices(selPatientId);
                            // Auto-check Document Approval for Patient type
                            if (lcmPatientType === 'Patient') {
                                if (!$('#lcmDocumentReview').is(':checked')) {
                                    $('#lcmDocumentReview').prop('checked', true).trigger('change');
                                }
                            } else {
                                $('#lcmDocumentReview').prop('checked', false).trigger('change');
                            }
                        })
                        .appendTo('#lcmSearchResults');
                });
                $('#lcmSearchResults').show();
            });
        }, 300);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#lcmPatientSearch, #lcmSearchResults').length) {
            $('#lcmSearchResults').hide();
        }
    });

    function lcmSubmit() {
        var patientId      = $('#lcmSelectedPatientId').val();
        var documentName   = $('#lcmDocumentName').val().trim();
        var requestService = $('#lcmRequestService').val();
        var services       = $('#lcmServices').val() || [];
        var documentDate   = $('#lcmDocumentDate').val().trim();
        var medicationList = $('#lcmMedicationList').is(':checked') ? 1 : 0;
        var insuranceElg   = $('#lcmInsuranceElg').is(':checked') ? 1 : 0;
        var mdoTag         = $('#lcmMdoTag').is(':checked') ? 1 : 0;
        var mdoSource      = mdoTag ? $('#lcmMdoSource').val() : '';
        var documentReview = $('#lcmDocumentReview').is(':checked') ? 1 : 0;
        var approvalUserId = $('#lcmDocumentApprovalUser').val();
        var valid = true;

        $('#lcmPatientError, #lcmDocumentNameError, #lcmRequestServiceError, #lcmServicesError, #lcmDocumentDateError, #lcmDocumentApprovalUserError, #lcmMedInsuranceError, #lcmMdoTagError').hide();

        if (!patientId)      { $('#lcmPatientError').show();        valid = false; }
        if (!documentName)   { $('#lcmDocumentNameError').show();   valid = false; }
        if (!requestService) { $('#lcmRequestServiceError').show(); valid = false; }
        if (!services.length){ $('#lcmServicesError').show();       valid = false; }
        if (!documentDate)   { $('#lcmDocumentDateError').show();   valid = false; }
        if (medicationList && insuranceElg)    { $('#lcmMedInsuranceError').show(); valid = false; }
        if (mdoTag && !mdoSource)              { $('#lcmMdoTagError').show();       valid = false; }
        if (documentReview && !approvalUserId) { $('#lcmDocumentApprovalUserError').show(); valid = false; }
        if (!valid) return;

        // Confirmation dialog only for Patient type — skip for Caregiver
        if (lcmPatientType !== 'Patient') {
            $('#lcmLoader').show();
            $('#lcmSaveBtn').prop('disabled', true);
            $.ajax({
                url: '/file-manager/file/' + lcmFileId + '/add-to-chart',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    patient_id:                patientId,
                    document_name:             documentName,
                    request_service_id:        requestService,
                    document_service_id:       services,
                    document_completed_date:   documentDate,
                    upload_for_info_only:      $('#lcmInfoOnly').is(':checked') ? 1 : 0,
                    internal_use:              $('#lcmInternalUse').is(':checked') ? 1 : 0,
                    document_review:           documentReview,
                    document_approval_user_id: approvalUserId,
                    medication_list:           medicationList,
                    insurance_elg:             insuranceElg,
                    mdo_tag:                   mdoTag,
                    mdo_source:                mdoSource,
                    questions:                 [],
                },
                success: function (res) {
                    toastr.success(res.message || 'File added to patient chart successfully');
                    $('#linkChartModal').modal('hide');
                    reloadCurrentView();
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to add to chart');
                },
                complete: function () {
                    $('#lcmLoader').hide();
                    $('#lcmSaveBtn').prop('disabled', false);
                }
            });
            return;
        }

        // Fetch confirmation questions then show confirm dialog (Patient only)
        var confirmContent = '';
        var queCount = 0;
        $.ajax({
            url: '{{ url("get-doc-questions") }}',
            type: 'GET',
            async: false,
            success: function (res) {
                if (res.data && res.data.length) {
                    queCount = res.data.length;
                    $.each(res.data, function (i, q) {
                        confirmContent += '<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;"><input type="checkbox" value="1" id="lcm_question_' + i + '" data-value="' + q.id + '" style="width:16px;height:16px;min-width:16px;margin-top:2px;cursor:pointer;accent-color:#2196f3;"><label for="lcm_question_' + i + '" style="margin:0;cursor:pointer;font-size:13px;">' + q.question + '</label></div>';
                    });
                    confirmContent += '<div id="lcm_que_error" class="text-danger ml-2"></div>';
                }
            }
        });

        $.confirm({
            title: 'Are you sure?',
            content: confirmContent || 'Do you want to proceed?',
            type: 'blue',
            columnClass: 'col-md-6',
            onContentReady: function () {
                this.$content.closest('.jconfirm-content-pane').css('overflow', 'visible');
            },
            buttons: {
                submit: {
                    text: 'CONFIRM',
                    btnClass: 'btn-blue',
                    action: function () {
                        var self = this;
                        var question_ids = [];
                        if (queCount > 0) {
                            var allChecked = true;
                            for (var i = 0; i < queCount; i++) {
                                if (!$('#lcm_question_' + i).is(':checked')) {
                                    allChecked = false;
                                } else {
                                    question_ids.push($('#lcm_question_' + i).data('value'));
                                }
                            }
                            if (!allChecked) {
                                $('#lcm_que_error').html('Please check your confirmation');
                                return false;
                            }
                        }
                        self.buttons.submit.setText('<i class="fa fa-spinner fa-spin"></i> Confirm');
                        self.buttons.submit.disable();

                        $('#lcmLoader').show();
                        $('#lcmSaveBtn').prop('disabled', true);

                        $.ajax({
                            url: '/file-manager/file/' + lcmFileId + '/add-to-chart',
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            data: {
                                patient_id:                patientId,
                                document_name:             documentName,
                                request_service_id:        requestService,
                                document_service_id:       services,
                                document_completed_date:   documentDate,
                                upload_for_info_only:      $('#lcmInfoOnly').is(':checked') ? 1 : 0,
                                internal_use:1,
                                document_review:           documentReview,
                                document_approval_user_id: approvalUserId,
                                medication_list:           medicationList,
                                insurance_elg:             insuranceElg,
                                mdo_tag:                   mdoTag,
                                mdo_source:                mdoSource,
                                questions:                 question_ids,
                            },
                            success: function (res) {
                                self.close();
                                toastr.success(res.message || 'File added to patient chart successfully');
                                $('#linkChartModal').modal('hide');
                                reloadCurrentView();
                            },
                            error: function (xhr) {
                                self.buttons.submit.setText('CONFIRM');
                                self.buttons.submit.enable();
                                toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to add to chart');
                            },
                            complete: function () {
                                $('#lcmLoader').hide();
                                $('#lcmSaveBtn').prop('disabled', false);
                            }
                        });
                    }
                },
                cancel: { text: 'CANCEL' }
            }
        });
    }

    // ---- Choose for Document Approval toggle ----
    $('#lcmDocumentReview').on('change', function () {
        if ($(this).is(':checked')) {
            $('#lcmDocumentApprovalWrap').show();
            if (!$('#lcmDocumentApprovalUser').data('tokenInputObject')) {
                $('#lcmDocumentApprovalUser').tokenInput('{{ url("search-nybest-user") }}', {
                    tokenLimit: null,
                    zindex: 1060,
                    preventDuplicates: true,
                    onReady: function () {
                        setTimeout(function () {
                            $('.token-input-list').css({ 'width': '100%', 'border': '1px solid #ced4da', 'border-radius': '4px', 'padding': '2px 6px' });
                            $('.token-input-dropdown').css({ 'max-height': '180px', 'overflow-y': 'auto', 'z-index': '1060' });
                        }, 100);
                    }
                });
            }
        } else {
            $('#lcmDocumentApprovalWrap').hide();
            if ($('#lcmDocumentApprovalUser').data('tokenInputObject')) {
                $('#lcmDocumentApprovalUser').tokenInput('destroy');
            }
        }
    });

    // ---- MDO Tag source show/hide ----
    $('#lcmMdoTag').on('change', function () {
        if ($(this).is(':checked')) {
            $('#lcmMdoSource').show();
        } else {
            $('#lcmMdoSource').hide().val('');
        }
    });

    // ---- Auto-populate Document Approval users when services change (Patient type only) ----
    function lcmGetDynamicApprovalUsers(serviceIds) {
        var dataUser = [];
        if (serviceIds.length === 1) {
            if ($.inArray('181', serviceIds) === -1) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['without_service'] ? _DYNAMIC_DOC_APPROVED_USERS['without_service'][0] : {});
            } else {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['181'] ? _DYNAMIC_DOC_APPROVED_USERS['181'][0] : {});
            }
        } else if (serviceIds.length > 1) {
            var idx = serviceIds.indexOf('181');
            if (idx >= 0 && _DYNAMIC_DOC_APPROVED_USERS['181']) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['181'][0]);
            }
            if (_DYNAMIC_DOC_APPROVED_USERS['without_service']) {
                dataUser.push(_DYNAMIC_DOC_APPROVED_USERS['without_service'][0]);
            }
        }
        return dataUser;
    }

    $(document).on('change', '#lcmServices', function () {
        @if(auth()->user()->agency_fk == '')
        if (lcmPatientType === 'Patient') {
            var serviceIds = $(this).val() || [];
            serviceIds = Array.isArray(serviceIds) ? serviceIds : [serviceIds];
            serviceIds = serviceIds.map(String);
            if (serviceIds.length > 0 && serviceIds[0] !== '') {
                var users = lcmGetDynamicApprovalUsers(serviceIds);
                if ($('#lcmDocumentApprovalUser').data('tokenInputObject')) {
                    $('#lcmDocumentApprovalUser').tokenInput('clear');
                    $.each(users, function (i, userObj) {
                        if (userObj) {
                            $.each(userObj, function (userId, userName) {
                                $('#lcmDocumentApprovalUser').tokenInput('add', { id: userId, name: userName });
                            });
                        }
                    });
                }
            }
        }
        @endif
    });
</script>
