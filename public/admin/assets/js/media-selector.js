/**
 * Media Selector Helper
 * Allows opening the Media Manager in a popup to select files.
 */

window.openMediaManager = function(fieldId, route, previewId) {
    const width = 1000;
    const height = 700;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    
    // Default route if not provided
    if (!route) {
        if (window.ADMIN_MEDIA_INDEX_URL) {
            route = window.ADMIN_MEDIA_INDEX_URL;
        } else {
            const parts = window.location.pathname.split('/').filter(Boolean);
            const idx = parts.findIndex(s => s.toLowerCase() === 'administrator' || s.toLowerCase() === 'admin');
            if (idx !== -1) {
                route = '/' + parts.slice(0, idx + 1).join('/') + '/media';
            } else {
                route = '/admin/media';
            }
        }
    }

    const separator = route.includes('?') ? '&' : '?';
    let url = `${route}${separator}popup=1&field_id=${fieldId}`;
    if (previewId) {
        // Pass previewId if needed, or rely on naming convention (handled in parent window)
        // But for now, we just open the popup. The selectFile in popup handles field update.
    }
    
    window.open(
        url,
        'MediaManager',
        `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`
    );
};

// Default handler invoked by Media Library popup in parent window
// Allows immediate preview in forms without custom code per-module
window.onMediaFileSelected = window.onMediaFileSelected || function(fieldId, file) {
    try {
        if (!fieldId || !file) return;
        const input = document.getElementById(fieldId);
        if (input) {
            input.value = file.path || file;
            input.dispatchEvent(new Event('change'));
        }
        // Update readonly display input if present (commonly named "*_display")
        const displayInput = input ? input.parentElement?.querySelector('input[name$="_display"]') : null;
        if (displayInput) {
            displayInput.value = file.name || file.path || '';
        }
        // Preview IMG by convention: <img id="<fieldId>_preview">
        let previewEl = document.getElementById(fieldId + '_preview');
        if (!previewEl) {
            // Fallback: try to find an IMG inside a wrapper with id "<fieldId>_preview"
            const wrapper = document.getElementById(fieldId + '_preview');
            if (wrapper) {
                previewEl = wrapper.querySelector('img');
            }
        }
        if (previewEl) {
            previewEl.src = file.url || file;
            previewEl.style.display = 'block';
            // Ensure container is visible
            if (previewEl.parentElement && getComputedStyle(previewEl.parentElement).display === 'none') {
                previewEl.parentElement.style.display = 'block';
            }
        }
        // Show remove button if exists by convention: "<fieldId>_remove"
        const removeBtn = document.getElementById(fieldId + '_remove');
        if (removeBtn) removeBtn.style.display = 'inline-block';
    } catch (e) {
        console.error('onMediaFileSelected error:', e);
    }
};

// Clear just the selection and preview without deleting original file
window.clearMediaPreview = function(fieldId) {
    try {
        const input = document.getElementById(fieldId);
        if (input) {
            input.value = '';
            input.dispatchEvent(new Event('change'));
        }
        const previewImg = document.getElementById(fieldId + '_preview');
        if (previewImg) {
            previewImg.src = '';
            previewImg.style.display = 'none';
            if (previewImg.parentElement) previewImg.parentElement.style.display = 'none';
        }
        const removeBtn = document.getElementById(fieldId + '_remove');
        if (removeBtn) removeBtn.style.display = 'none';
        // Clear readonly text field if present
        if (input) {
            const displayInput = input.parentElement?.querySelector('input[name$="_display"]');
            if (displayInput) displayInput.value = '';
        }
    } catch (e) {
        console.error('clearMediaPreview error:', e);
    }
};
