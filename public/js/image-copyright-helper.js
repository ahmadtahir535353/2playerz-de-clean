/**
 * Image Copyright Helper for TinyMCE Editor
 * Beautiful Modal for adding copyright to images
 */

(function() {
    'use strict';
    
    // Get translations from Laravel
    function getTranslations() {
        // Try to get from window.translations or fallback to English
        var trans = window.translations || {};
        return {
            title: trans.image_copyright_title || 'Image Copyright',
            label: trans.copyright_text_label || 'Copyright Text',
            placeholder: trans.copyright_placeholder || 'e.g., Nintendo, Getty Images, AP Photo',
            hint: trans.copyright_hint || 'Leave empty to remove copyright',
            cancel: trans.cancel || 'Cancel',
            save: trans.save_copyright || 'Save Copyright',
            added: trans.copyright_added || 'Copyright ":copyright" added successfully!',
            removed: trans.copyright_removed || 'Copyright removed'
        };
    }
    
    // Create beautiful modal HTML
    function createCopyrightModal() {
        var t = getTranslations();
        var modal = document.createElement('div');
        modal.id = 'copyright-modal';
        modal.style.cssText = 'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;';
        
        modal.innerHTML = `
            <div style="background:#fff;border-radius:12px;padding:0;width:90%;max-width:500px;box-shadow:0 10px 40px rgba(0,0,0,0.3);animation:modalFadeIn 0.3s ease;">
                <div style="background:rgb(176, 81, 176);color:#fff;padding:20px 24px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
                    <h3 id="modal-title" style="margin:0;font-size:20px;font-weight:600;display:flex;align-items:center;">
                        <svg style="width:24px;height:24px;margin-right:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        ${t.title}
                    </h3>
                    <button id="modal-close" style="background:rgba(255,255,255,0.2);border:none;color:#fff;cursor:pointer;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background 0.2s;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div style="padding:24px;">
                    <label id="modal-label" style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">
                        ${t.label}
                    </label>
                    <input 
                        type="text" 
                        id="copyright-input" 
                        placeholder="${t.placeholder}"
                        style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.2s;box-sizing:border-box;color:#000;background:#fff;"
                    />
                    <p id="modal-hint" style="margin-top:8px;font-size:13px;color:#6b7280;">
                        ${t.hint}
                    </p>
                </div>
                
                <div style="padding:0 24px 24px 24px;display:flex;gap:12px;justify-content:flex-end;">
                    <button id="modal-cancel" style="padding:10px 20px;border:2px solid #e5e7eb;background:#fff;color:#374151;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;transition:all 0.2s;">
                        ${t.cancel}
                    </button>
                    <button id="modal-save" style="padding:10px 20px;border:none;background:rgb(176, 81, 176);color:#fff;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.2s;box-shadow:0 2px 8px rgba(176, 81, 176, 0.4);">
                        ${t.save}
                    </button>
                </div>
            </div>
        `;
        
        // Add animations
        var style = document.createElement('style');
        style.textContent = `
            @keyframes modalFadeIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            #copyright-input:focus {
                outline: none;
                border-color: rgb(176, 81, 176);
                box-shadow: 0 0 0 3px rgba(176, 81, 176, 0.1);
            }
            #modal-cancel:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }
            #modal-save:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(176, 81, 176, 0.5);
                background: rgb(156, 61, 156);
            }
            #modal-close:hover {
                background: rgba(255,255,255,0.3);
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(modal);
        return modal;
    }
    
    // Show modal function
    function showCopyrightModal(currentCopyright, callback) {
        var modal = document.getElementById('copyright-modal') || createCopyrightModal();
        var input = document.getElementById('copyright-input');
        
        input.value = currentCopyright || '';
        modal.style.display = 'flex';
        
        // Focus input
        setTimeout(function() {
            input.focus();
            input.select();
        }, 100);
        
        // Handle close
        function closeModal() {
            modal.style.display = 'none';
        }
        
        // Handle save
        function saveAndClose() {
            var value = input.value.trim();
            closeModal();
            callback(value);
        }
        
        // Event listeners
        document.getElementById('modal-close').onclick = closeModal;
        document.getElementById('modal-cancel').onclick = closeModal;
        document.getElementById('modal-save').onclick = saveAndClose;
        
        // Enter key to save
        input.onkeypress = function(e) {
            if (e.key === 'Enter') {
                saveAndClose();
            }
        };
        
        // ESC key to close
        document.onkeydown = function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        };
        
        // Click outside to close
        modal.onclick = function(e) {
            if (e.target === modal) {
                closeModal();
            }
        };
    }
    
    // Wait for TinyMCE to be ready
    function initCopyrightHelper() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initCopyrightHelper, 500);
            return;
        }
        
        tinymce.on('AddEditor', function(e) {
            var editor = e.editor;
            
            editor.on('init', function() {
                console.log('✓ Copyright Helper Loaded');
                
                // Auto-open modal when image is inserted
                editor.on('NodeChange', function(e) {
                    // Check if a new image was just inserted
                    var nodes = e.parents;
                    for (var i = 0; i < nodes.length; i++) {
                        if (nodes[i].nodeName === 'IMG') {
                            var img = nodes[i];
                            // Check if image doesn't have copyright yet (newly inserted)
                            if (!img.hasAttribute('data-copyright') && !img.hasAttribute('data-copyright-checked')) {
                                img.setAttribute('data-copyright-checked', 'true');
                                
                                // Auto-open modal for new image
                                setTimeout(function() {
                                    showCopyrightModal('', function(copyright) {
                                        var t = getTranslations();
                                        if (copyright) {
                                            img.setAttribute('data-copyright', copyright);
                                            
                                            // Visual feedback
                                            img.style.border = '3px solid #10b981';
                                            setTimeout(function() {
                                                img.style.border = '';
                                            }, 1000);
                                            
                                            // Show success notification
                                            if (editor.notificationManager) {
                                                editor.notificationManager.open({
                                                    text: '✓ ' + t.added.replace(':copyright', copyright),
                                                    type: 'success',
                                                    timeout: 3000
                                                });
                                            }
                                        }
                                    });
                                }, 300);
                                break;
                            }
                        }
                    }
                });
                
                // Add double-click handler for editing copyright
                editor.on('dblclick', function(e) {
                    if (e.target.nodeName === 'IMG') {
                        e.preventDefault();
                        
                        var img = e.target;
                        var currentCopyright = img.getAttribute('data-copyright') || '';
                        
                        showCopyrightModal(currentCopyright, function(copyright) {
                            var t = getTranslations();
                            if (copyright) {
                                img.setAttribute('data-copyright', copyright);
                                
                                // Visual feedback
                                img.style.border = '3px solid #10b981';
                                setTimeout(function() {
                                    img.style.border = '';
                                }, 1000);
                                
                                // Show success notification
                                if (editor.notificationManager) {
                                    editor.notificationManager.open({
                                        text: '✓ ' + t.added.replace(':copyright', copyright),
                                        type: 'success',
                                        timeout: 3000
                                    });
                                }
                            } else if (copyright === '') {
                                img.removeAttribute('data-copyright');
                                if (editor.notificationManager) {
                                    editor.notificationManager.open({
                                        text: '✓ ' + t.removed,
                                        type: 'info',
                                        timeout: 2000
                                    });
                                }
                            }
                        });
                    }
                });
                
                // Add click info
                editor.on('click', function(e) {
                    if (e.target.nodeName === 'IMG') {
                        var copyright = e.target.getAttribute('data-copyright');
                        if (copyright) {
                            console.log('Image has copyright: ' + copyright);
                        } else {
                            console.log('Tip: Double-click image to add copyright');
                        }
                    }
                });
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCopyrightHelper);
    } else {
        initCopyrightHelper();
    }
})();

