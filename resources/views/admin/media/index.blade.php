@component('admin.layouts.main')

@slot('title')
    Media Library - {{ config('app.name') }}
@endslot

@php
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $initialPath = $initial_path ?? 'media';
@endphp

@if($isPopup ?? false)
<style>
    /* Popup Mode Overrides */
    .layout-menu, .layout-navbar, .footer, .content-footer, .layout-menu-toggle { display: none !important; }
    .layout-page { padding: 0 !important; margin: 0 !important; }
    .content-wrapper { padding: 0 !important; }
    .container-xxl { padding: 0 !important; max-width: 100% !important; }
    h4.mb-0 { display: none; }
    .file-manager-container { height: 100vh !important; min-height: 100vh !important; border: none !important; }
    .file-manager-wrapper { border: none !important; border-radius: 0 !important; }
</style>
@endif

<style>
.file-manager-container { height: calc(100vh - 180px); min-height: 600px; display: flex; flex-direction: column; }
.file-manager-wrapper { flex: 1; display: flex; overflow: hidden; position: relative; border: 1px solid #dbe3e6; border-radius: 0.5rem; background: #fff; }
.fm-sidebar { width: 260px; border-right: 1px solid #dbe3e6; display: flex; flex-direction: column; background: #fff; }
.fm-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; }
.fm-header { padding: 1rem; border-bottom: 1px solid #dbe3e6; background: #fff; }
.fm-body { flex: 1; padding: 1.5rem; overflow-y: auto; background: #f8f9fa; }

.folder-card { cursor: pointer; transition: all 0.2s; border: 1px solid #e1e4e8; background: #fff; }
.folder-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-color: #7367f0; }
.file-card { cursor: pointer; transition: all 0.2s; border: 1px solid #e1e4e8; position: relative; background: #fff; }
.file-card:hover, .file-card.active { border-color: #7367f0; box-shadow: 0 0 0 1px #7367f0; }

.drawer-overlay {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.2); z-index: 10; display: none;
}
.drawer-overlay.show { display: block; }

.drawer-panel {
    position: absolute; top: 0; right: 0; bottom: 0; width: 320px;
    background: #fff; z-index: 20; border-left: 1px solid #dbe3e6;
    transform: translateX(100%); transition: transform 0.3s ease;
    display: flex; flex-direction: column;
}
.drawer-panel.show { transform: translateX(0); }
.drawer-header { padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.drawer-body { padding: 1.5rem; overflow-y: auto; flex: 1; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3" id="media-main-header">
        <h4 class="mb-0">Media Library</h4>
    </div>

    <div class="file-manager-container">
        <div class="file-manager-wrapper">
            <!-- Sidebar -->
            <div class="fm-sidebar">
                <div class="p-3 border-bottom">
                    <label class="btn btn-primary w-100">
                        <input type="file" class="d-none" id="uploadInput" multiple>
                        <i class="ti tabler-upload me-2"></i> Upload Files
                    </label>
                </div>
                <div class="p-2 overflow-auto flex-grow-1">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="javascript:void(0)" class="nav-link active" id="navMyFiles" onclick="fetchListing('media')">
                                <i class="ti tabler-folder me-2"></i> My Files
                            </a>
                        </li>
                    </ul>
                    <div id="uploadProgressList" class="mt-3 px-2"></div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="fm-content">
                <div class="fm-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                         <button class="btn btn-icon btn-text-secondary rounded-pill" id="btnUp" title="Go Up">
                            <i class="ti tabler-arrow-up"></i>
                        </button>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" id="breadcrumbs"></ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" id="refreshBtn"><i class="ti tabler-refresh"></i></button>
                        <button class="btn btn-outline-primary btn-sm" id="newFolderBtn"><i class="ti tabler-folder-plus"></i> New Folder</button>
                    </div>
                </div>

                <div class="fm-body" id="fileArea">
                    <!-- Folders Section -->
                    <div id="foldersSection" class="mb-4 d-none">
                        <h6 class="text-muted text-uppercase fs-7 mb-3">Folders</h6>
                        <div class="row g-3" id="folderGrid"></div>
                    </div>

                    <!-- Files Section -->
                    <div id="filesSection" class="d-none">
                        <h6 class="text-muted text-uppercase fs-7 mb-3">Files</h6>
                        <div class="row g-3" id="fileGrid"></div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="d-none h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <div class="bg-light rounded-circle p-4 mb-3">
                            <i class="ti tabler-folder-off fs-1"></i>
                        </div>
                        <h5>No files found</h5>
                        <p>Upload files or create a folder to get started.</p>
                    </div>
                </div>
                
                <!-- Drawer Overlay -->
                <div class="drawer-overlay" id="drawerOverlay"></div>
                
                <!-- Details Drawer -->
                <div class="drawer-panel shadow-lg" id="detailsDrawer">
                    <div class="drawer-header bg-light">
                        <h6 class="mb-0">File Details</h6>
                        <button type="button" class="btn-close" id="closeDrawerBtn"></button>
                    </div>
                    <div class="drawer-body">
                        <div id="drawerContent">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endcomponent

<script>
(function(){
    // Popup Configuration
    const IS_POPUP = {{ $isPopup ? 'true' : 'false' }};
    const POPUP_FIELD_ID = "{{ $popupFieldId ?? '' }}";
    const CKEDITOR_FUNC_NUM = "{{ $ckEditorFuncNum ?? '' }}";

    const csrfToken = document.querySelector('meta[name="_token"]').getAttribute('content');
    const MEDIA_API = {
        files: '{{ route($routeName . ".media.api_files") }}',
        foldersCreate: '{{ route($routeName . ".media.api_folders_create") }}',
        foldersDelete: '{{ route($routeName . ".media.api_folders_delete") }}',
        filesDelete: '{{ route($routeName . ".media.api_files_delete") }}',
        filesAlt: '{{ route($routeName . ".media.api_files_alt") }}',
        filesChunk: '{{ route($routeName . ".media.api_files_chunk") }}',
    };
    
    // State
    let currentFolderId = null;
    let selectedFile = null;

    // Elements
    const breadcrumbs = document.getElementById('breadcrumbs');
    const folderGrid = document.getElementById('folderGrid');
    const fileGrid = document.getElementById('fileGrid');
    const foldersSection = document.getElementById('foldersSection');
    const filesSection = document.getElementById('filesSection');
    const emptyState = document.getElementById('emptyState');
    const drawer = document.getElementById('detailsDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const drawerContent = document.getElementById('drawerContent');
    const uploadInput = document.getElementById('uploadInput');
    const progressList = document.getElementById('uploadProgressList');
    
    // Init
    init();

    function init() {
        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            if(e.state && e.state.folderId !== undefined) {
                currentFolderId = e.state.folderId;
                fetchListing(currentFolderId, false);
            }
        });

        // Event Listeners
        document.getElementById('refreshBtn').onclick = () => fetchListing(currentFolderId);
        document.getElementById('btnUp').onclick = goUp;
        document.getElementById('newFolderBtn').onclick = createFolderPrompt;
        document.getElementById('closeDrawerBtn').onclick = closeDrawer;
        drawerOverlay.onclick = closeDrawer;
        
        uploadInput.addEventListener('change', handleUpload);

        fetchListing(null, false);
    }

    async function fetchListing(folderId, pushState = true) {
        try {
            const url = folderId ? `${MEDIA_API.files}?folder_id=${folderId}` : MEDIA_API.files;
            const res = await fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            });
            const json = await res.json();
            if (!json || !json.success) return;

            currentFolderId = json.current_folder_id;
            
            if (pushState) {
                const newUrl = new URL(window.location);
                if(currentFolderId) newUrl.searchParams.set('folder_id', currentFolderId);
                else newUrl.searchParams.delete('folder_id');
                window.history.pushState({ folderId: currentFolderId }, '', newUrl);
            }

            renderBreadcrumbs(json.breadcrumbs || []);
            renderFolders(json.folders || []);
            renderFiles(json.files || []);
            
            closeDrawer(); // Close drawer on navigation
        } catch (e) {
            console.error('Fetch error:', e);
        }
    }

    function renderBreadcrumbs(items) {
        breadcrumbs.innerHTML = '';
        items.forEach((b, idx) => {
            const li = document.createElement('li');
            li.className = `breadcrumb-item ${idx === items.length - 1 ? 'active' : ''}`;
            if (idx === items.length - 1) {
                li.textContent = b.label;
            } else {
                const a = document.createElement('a');
                a.href = 'javascript:void(0)';
                a.textContent = b.label;
                a.onclick = () => fetchListing(b.id);
                li.appendChild(a);
            }
            breadcrumbs.appendChild(li);
        });
        document.getElementById('btnUp').disabled = (items.length <= 1);
    }

    function renderFolders(items) {
        folderGrid.innerHTML = '';
        if (items.length > 0) {
            foldersSection.classList.remove('d-none');
            items.forEach(f => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 col-xl-2';
                col.innerHTML = `
                    <div class="card h-100 folder-card p-3 text-center" title="${f.name}">
                        <i class="ti tabler-folder text-warning fs-1 mb-2 d-block"></i>
                        <div class="text-truncate small fw-medium">${f.name}</div>
                        <div class="dropdown position-absolute top-0 end-0 m-1">
                             <button class="btn btn-icon btn-sm btn-text-secondary rounded-pill" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
                                <i class="ti tabler-dots-vertical"></i>
                             </button>
                             <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteFolder('${f.id}')">Delete</a></li>
                             </ul>
                        </div>
                    </div>
                `;
                col.querySelector('.folder-card').onclick = () => fetchListing(f.id);
                folderGrid.appendChild(col);
            });
        } else {
            foldersSection.classList.add('d-none');
        }
    }

    function renderFiles(items) {
        fileGrid.innerHTML = '';
        if (items.length > 0) {
            filesSection.classList.remove('d-none');
            emptyState.classList.add('d-none');
            items.forEach(f => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 col-xl-2';
                const isImage = (f.mime || '').startsWith('image/');
                const thumb = isImage 
                    ? `<img src="${f.url}" class="img-fluid rounded" style="height:100px; object-fit:contain;">` 
                    : `<i class="ti tabler-file-text text-secondary fs-1"></i>`;
                
                col.innerHTML = `
                    <div class="card h-100 file-card p-2" title="${f.name}">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded mb-2" style="height:120px;">
                            ${thumb}
                        </div>
                        <div class="text-truncate small fw-medium">${f.name}</div>
                        <div class="text-muted small" style="font-size:0.7rem;">${f.formatted_size || ''}</div>
                    </div>
                `;
                col.querySelector('.file-card').onclick = () => openDetails(f);
                fileGrid.appendChild(col);
            });
        } else {
            filesSection.classList.add('d-none');
            if (foldersSection.classList.contains('d-none')) {
                emptyState.classList.remove('d-none');
            }
        }
    }

    function openDetails(file) {
        selectedFile = file;
        drawer.classList.add('show');
        drawerOverlay.classList.add('show');
        
        // Highlight selected card
        document.querySelectorAll('.file-card').forEach(c => c.classList.remove('active'));

        const isImage = (file.mime || '').startsWith('image/');
        const preview = isImage 
            ? `<div class="text-center bg-light rounded p-3 mb-3"><img src="${file.url}" class="img-fluid rounded shadow-sm" style="max-height:200px;"></div>`
            : `<div class="text-center bg-light rounded p-3 mb-3"><i class="ti tabler-file-text fs-1 text-secondary"></i></div>`;

        let actionBtn = '';
        if (IS_POPUP) {
             actionBtn = `
                <div class="d-grid mb-3">
                    <button class="btn btn-success" onclick="selectFile()">
                        <i class="ti tabler-check me-1"></i> Select File
                    </button>
                </div>
                <hr>
             `;
        }

        drawerContent.innerHTML = `
            ${preview}
            ${actionBtn}
            <div class="mb-3">
                <label class="form-label small text-muted">File Name</label>
                <div class="form-control-plaintext pt-0 text-truncate" title="${file.name}"><strong>${file.name}</strong></div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label small text-muted">Size</label>
                    <div class="small">${file.formatted_size}</div>
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted">Type</label>
                    <div class="small text-truncate" title="${file.mime}">${file.mime}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small">Alt Text</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="altTextInput" value="${file.alt_text || ''}" placeholder="Enter alt text">
                    <button class="btn btn-primary" id="saveAltBtn"><i class="ti tabler-check"></i></button>
                </div>
                <div class="form-text text-success d-none" id="altSavedMsg">Saved!</div>
            </div>

            <hr>
            
            <div class="d-grid gap-2">
                <a href="${file.url}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="ti tabler-download me-2"></i> Download / View
                </a>
                <button class="btn btn-outline-secondary btn-sm" onclick="copyLink('${file.url}')">
                    <i class="ti tabler-copy me-2"></i> Copy Link
                </button>
                <button class="btn btn-label-danger btn-sm" onclick="deleteFile('${file.id}')">
                    <i class="ti tabler-trash me-2"></i> Delete File
                </button>
            </div>
        `;

        document.getElementById('saveAltBtn').onclick = saveAltText;
    }

    function closeDrawer() {
        drawer.classList.remove('show');
        drawerOverlay.classList.remove('show');
        selectedFile = null;
    }

    async function saveAltText() {
        if (!selectedFile) return;
        const input = document.getElementById('altTextInput');
        const btn = document.getElementById('saveAltBtn');
        const msg = document.getElementById('altSavedMsg');
        const newAlt = input.value.trim();

        btn.disabled = true;
        
        try {
            const res = await fetch(MEDIA_API.filesAlt, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: selectedFile.id, alt_text: newAlt })
            });
            const json = await res.json();
            if (json.success) {
                selectedFile.alt_text = newAlt; // Update local model
                msg.classList.remove('d-none');
                setTimeout(() => msg.classList.add('d-none'), 2000);
            } else {
                alert('Failed to save alt text');
            }
        } catch(e) {
            console.error(e);
            alert('Error saving alt text');
        } finally {
            btn.disabled = false;
        }
    }

    // Helper Functions
    function goUp() {
        // If currentFolderId is null, we are at root, do nothing
        if (!currentFolderId) return;
        
        // Find parent of current folder from breadcrumbs or re-fetch
        // Simplest: fetch current folder info again to get parent_id? 
        // Or just rely on breadcrumbs array in memory if we had it.
        // But fetchListing gets new breadcrumbs.
        // Let's just look at the breadcrumbs in DOM or global state.
        // A better way: The API returns breadcrumbs. The second to last item is the parent.
        // We can just rely on the API response structure or fetch listing of parent.
        
        // Actually, since we don't store the full tree, let's just use the breadcrumbs from the last fetch.
        // We can access the parent ID from the breadcrumbs array if we stored it.
        // Let's modify fetchListing to store breadcrumbs globally.
        
        // Fallback: reload parent by guessing? No.
        // Let's just click the "Up" button equivalent in breadcrumb.
        const items = breadcrumbs.querySelectorAll('li');
        if (items.length >= 2) {
             const parentLink = items[items.length - 2].querySelector('a');
             if (parentLink) parentLink.click();
             else fetchListing(null); // Fallback to root
        }
    }

    function createFolderPrompt() {
        const name = prompt("Enter folder name:");
        if (name) createFolder(name);
    }

    async function createFolder(name) {
        const body = new URLSearchParams({ _token: csrfToken, name });
        if (currentFolderId) body.append('parent_id', currentFolderId);

        const res = await fetch(MEDIA_API.foldersCreate, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        });
        const json = await res.json();
        if (json.success) fetchListing(currentFolderId);
        else alert(json.message || 'Error creating folder');
    }

    window.deleteFolder = async function(id) { // Global for inline onclick
        if (!confirm('Are you sure you want to delete this folder and all contents?')) return;
        const res = await fetch(MEDIA_API.foldersDelete, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ _token: csrfToken, id, _method: 'DELETE' })
        });
        if ((await res.json()).success) fetchListing(currentFolderId);
    };

    window.deleteFile = async function(id) {
        if (!confirm('Delete this file?')) return;
        const res = await fetch(MEDIA_API.filesDelete, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ _token: csrfToken, id, _method: 'DELETE' })
        });
        if ((await res.json()).success) {
            closeDrawer();
            fetchListing(currentFolderId);
        }
    };
    
    window.copyLink = function(url) {
        navigator.clipboard.writeText(url).then(() => alert('Link copied!'));
    };

    async function handleUpload() {
        const files = uploadInput.files;
        if (!files.length) return;
        for (const file of files) {
            await uploadFileChunked(file);
        }
        uploadInput.value = '';
    }

    function createProgressItem(name) {
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-truncate" style="max-width:150px;">${name}</span>
                <span class="percent">0%</span>
            </div>
            <div class="progress" style="height:4px;"><div class="progress-bar" style="width:0%"></div></div>
        `;
        progressList.appendChild(div);
        return {
            update: (p) => {
                div.querySelector('.progress-bar').style.width = p + '%';
                div.querySelector('.percent').textContent = p + '%';
            },
            done: () => {
                setTimeout(() => div.remove(), 2000);
            }
        };
    }

    async function uploadFileChunked(file) {
        const chunkSize = 2 * 1024 * 1024;
        const totalChunks = Math.ceil(file.size / chunkSize);
        const uploadKey = 'media_' + Date.now() + '_' + Math.random().toString(36).slice(2);
        const progress = createProgressItem(file.name);
        
        for (let i = 0; i < totalChunks; i++) {
            const start = i * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const blob = file.slice(start, end);
            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('file', blob, file.name);
            fd.append('chunk_index', i);
            fd.append('total_chunks', totalChunks);
            fd.append('upload_key', uploadKey);
            fd.append('file_name', file.name);
            if (currentFolderId) fd.append('folder_id', currentFolderId);
            
            const res = await fetch(MEDIA_API.filesChunk, { method: 'POST', body: fd });
            const json = await res.json();
            
            progress.update(Math.round(((i+1)/totalChunks)*100));
            
            if (i === totalChunks - 1 && (json.success || json.status === 'done')) {
                progress.done();
                fetchListing(currentFolderId);
            }
        }
    }
    // Expose selectFile globally for popup mode
    window.selectFile = function() {
        if (!selectedFile) return;

        // 1. Check if inside iframe (CKEditor "Choose From Library" implementation in admin.layouts.main)
        if (window.parent && window.parent !== window) {
            if (typeof window.parent.insertImageFromLibrary === 'function') {
                window.parent.insertImageFromLibrary(selectedFile.url);
                return;
            }
        }

        // 2. Standard Window Popup
        if (window.opener) {
            // Hook for custom handling in parent
            if (typeof window.opener.onMediaFileSelected === 'function') {
                window.opener.onMediaFileSelected(POPUP_FIELD_ID, selectedFile);
            }

            if (POPUP_FIELD_ID) {
                const field = window.opener.document.getElementById(POPUP_FIELD_ID);
                if (field) {
                    field.value = selectedFile.path;
                    
                    // Also try to update preview if it exists (convention: ID + '_preview')
                    const preview = window.opener.document.getElementById(POPUP_FIELD_ID + '_preview');
                    if (preview && preview.tagName === 'IMG') {
                        preview.src = selectedFile.url;
                        preview.style.display = 'block';
                    }
                    // Trigger change event
                    field.dispatchEvent(new Event('change'));
                }
            }
            if (CKEDITOR_FUNC_NUM) {
                 window.opener.CKEDITOR.tools.callFunction(CKEDITOR_FUNC_NUM, selectedFile.url);
            }
            window.close();
        }
    };

})();
</script>
