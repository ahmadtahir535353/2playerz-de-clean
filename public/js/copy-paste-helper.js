/**
 * Copy/Paste Helper for TinyMCE Editor
 * Shows Copy, Cut, Paste options on right-click
 */

(function() {
    'use strict';
    
    // Get translations
    function getCopyPasteTranslations() {
        var trans = window.translations || {};
        return {
            title: trans.copy_paste_options || 'Copy / Paste Options',
            copy: trans.copy || 'Copy',
            cut: trans.cut || 'Cut',
            paste: trans.paste || 'Paste',
            text_copied: trans.text_copied || 'Text copied to clipboard',
            text_cut: trans.text_cut || 'Text cut to clipboard',
            text_pasted: trans.text_pasted || 'Text pasted',
            please_select_text: trans.please_select_text || 'Please select text first',
            paste_failed: trans.paste_failed || 'Paste failed. Please use Ctrl+V',
            use_ctrl_v: trans.use_ctrl_v || 'Please use Ctrl+V to paste'
        };
    }
    
    // Check if dark mode is active
    function isDarkMode() {
        // Check Filament dark mode class
        if (document.documentElement.classList.contains('dark')) {
            return true;
        }
        // Check if body has dark class
        if (document.body.classList.contains('dark')) {
            return true;
        }
        // Check computed style
        var bodyStyle = window.getComputedStyle(document.body);
        var bgColor = bodyStyle.backgroundColor;
        // If background is dark (RGB values are low)
        if (bgColor) {
            var rgb = bgColor.match(/\d+/g);
            if (rgb && rgb.length >= 3) {
                var r = parseInt(rgb[0]);
                var g = parseInt(rgb[1]);
                var b = parseInt(rgb[2]);
                // If average is less than 128, it's dark
                if ((r + g + b) / 3 < 128) {
                    return true;
                }
            }
        }
        return false;
    }
    
    // Create beautiful modal HTML
    function createCopyPasteModal() {
        var modal = document.createElement('div');
        modal.id = 'copypaste-modal';
        modal.style.cssText = 'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;';
        
        var t = getCopyPasteTranslations();
        var darkMode = isDarkMode();
        var bodyBg = darkMode ? '#1f2937' : '#fff';
        var bodyText = darkMode ? '#f3f4f6' : '#374151';
        var borderColor = darkMode ? '#374151' : '#e5e7eb';
        var buttonBg = darkMode ? '#374151' : '#fff';
        var buttonHoverBg = darkMode ? '#4b5563' : '';
        
        modal.innerHTML = `
            <div class="copypaste-modal-container" style="background:${bodyBg};border-radius:12px;padding:0;width:90%;max-width:400px;box-shadow:0 10px 40px rgba(0,0,0,0.3);animation:modalFadeIn 0.3s ease;">
                <div style="background:rgb(59, 130, 246);color:#fff;padding:20px 24px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="margin:0;font-size:20px;font-weight:600;display:flex;align-items:center;">
                        <svg style="width:24px;height:24px;margin-right:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        ${t.title}
                    </h3>
                    <button id="copypaste-modal-close" style="background:rgba(255,255,255,0.2);border:none;color:#fff;cursor:pointer;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background 0.2s;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div style="padding:24px;">
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <button id="copypaste-copy" class="copypaste-button" style="padding:14px 20px;border:2px solid ${borderColor};background:${buttonBg};color:${bodyText};border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;text-align:left;">
                            <svg style="width:20px;height:20px;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span style="flex:1;">${t.copy}</span>
                        </button>
                        
                        <button id="copypaste-cut" class="copypaste-button" style="padding:14px 20px;border:2px solid ${borderColor};background:${buttonBg};color:${bodyText};border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;text-align:left;">
                            <svg style="width:20px;height:20px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span style="flex:1;">${t.cut}</span>
                        </button>
                        
                        <button id="copypaste-paste" class="copypaste-button" style="padding:14px 20px;border:2px solid ${borderColor};background:${buttonBg};color:${bodyText};border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;text-align:left;">
                            <svg style="width:20px;height:20px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span style="flex:1;">${t.paste}</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add animations and dark mode styles
        var style = document.createElement('style');
        style.textContent = `
            @keyframes modalFadeIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            .copypaste-button:hover {
                transform: translateY(-2px);
            }
            #copypaste-copy:hover {
                background: #eff6ff;
                border-color: #3b82f6;
            }
            .dark .copypaste-modal-container #copypaste-copy:hover,
            html.dark .copypaste-modal-container #copypaste-copy:hover {
                background: #1e3a8a;
                border-color: #3b82f6;
            }
            #copypaste-cut:hover {
                background: #fef2f2;
                border-color: #ef4444;
            }
            .dark .copypaste-modal-container #copypaste-cut:hover,
            html.dark .copypaste-modal-container #copypaste-cut:hover {
                background: #7f1d1d;
                border-color: #ef4444;
            }
            #copypaste-paste:hover {
                background: #f0fdf4;
                border-color: #10b981;
            }
            .dark .copypaste-modal-container #copypaste-paste:hover,
            html.dark .copypaste-modal-container #copypaste-paste:hover {
                background: #14532d;
                border-color: #10b981;
            }
            #copypaste-modal-close:hover {
                background: rgba(255,255,255,0.3);
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(modal);
        return modal;
    }
    
    // Show modal function
    function showCopyPasteModal(editor, callback) {
        var modal = document.getElementById('copypaste-modal');
        if (modal) {
            // Update modal for current theme
            var darkMode = isDarkMode();
            var container = modal.querySelector('.copypaste-modal-container');
            if (container) {
                var bodyBg = darkMode ? '#1f2937' : '#fff';
                var bodyText = darkMode ? '#f3f4f6' : '#374151';
                var borderColor = darkMode ? '#374151' : '#e5e7eb';
                var buttonBg = darkMode ? '#374151' : '#fff';
                
                container.style.background = bodyBg;
                var buttons = container.querySelectorAll('.copypaste-button');
                buttons.forEach(function(btn) {
                    btn.style.background = buttonBg;
                    btn.style.color = bodyText;
                    btn.style.borderColor = borderColor;
                });
            }
        } else {
            modal = createCopyPasteModal();
        }
        modal.style.display = 'flex';
        
        // Handle close
        function closeModal() {
            modal.style.display = 'none';
        }
        
        // Handle Copy
        document.getElementById('copypaste-copy').onclick = function() {
            var selectedText = editor.selection.getContent({format: 'text'});
            if (selectedText) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(selectedText).then(function() {
                        closeModal();
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: '✓ ' + t.text_copied,
                                type: 'success',
                                timeout: 2000
                            });
                        }
                        if (callback) callback('copy');
                    }).catch(function(err) {
                        console.error('Copy failed:', err);
                        closeModal();
                    });
                } else {
                    var textArea = document.createElement('textarea');
                    textArea.value = selectedText;
                    textArea.style.position = 'fixed';
                    textArea.style.opacity = '0';
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        closeModal();
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: '✓ ' + t.text_copied,
                                type: 'success',
                                timeout: 2000
                            });
                        }
                        if (callback) callback('copy');
                    } catch (err) {
                        console.error('Copy failed:', err);
                        closeModal();
                    }
                    document.body.removeChild(textArea);
                }
            } else {
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: t.please_select_text,
                                type: 'warning',
                                timeout: 2000
                            });
                        }
            }
        };
        
        // Handle Cut
        document.getElementById('copypaste-cut').onclick = function() {
            var selectedText = editor.selection.getContent({format: 'text'});
            var selectedHtml = editor.selection.getContent();
            if (selectedText || selectedHtml) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(selectedText).then(function() {
                        editor.selection.setContent('');
                        closeModal();
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: '✓ ' + t.text_cut,
                                type: 'success',
                                timeout: 2000
                            });
                        }
                        if (callback) callback('cut');
                    }).catch(function(err) {
                        console.error('Cut failed:', err);
                        closeModal();
                    });
                } else {
                    var textArea = document.createElement('textarea');
                    textArea.value = selectedText;
                    textArea.style.position = 'fixed';
                    textArea.style.opacity = '0';
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        editor.selection.setContent('');
                        closeModal();
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: '✓ ' + t.text_cut,
                                type: 'success',
                                timeout: 2000
                            });
                        }
                        if (callback) callback('cut');
                    } catch (err) {
                        console.error('Cut failed:', err);
                        closeModal();
                    }
                    document.body.removeChild(textArea);
                }
            } else {
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: t.please_select_text,
                                type: 'warning',
                                timeout: 2000
                            });
                        }
            }
        };
        
        // Handle Paste - Preserve formatting like Ctrl+V
        document.getElementById('copypaste-paste').onclick = function() {
            closeModal();
            editor.focus();
            
            // Method 1: Use Clipboard API to read HTML (preserves formatting)
            if (navigator.clipboard && navigator.clipboard.read) {
                navigator.clipboard.read().then(function(clipboardItems) {
                    var htmlFound = false;
                    
                    // Try to get HTML first (preserves formatting)
                    for (var i = 0; i < clipboardItems.length; i++) {
                        var item = clipboardItems[i];
                        var types = item.types || [];
                        
                        // Check if HTML is available
                        if (types.indexOf('text/html') !== -1 || types.indexOf('text/html;charset=utf-8') !== -1) {
                            htmlFound = true;
                            item.getType('text/html').then(function(blob) {
                                return blob.text();
                            }).then(function(html) {
                                // Use TinyMCE's paste processing to preserve formatting
                                // This ensures HTML goes through TinyMCE's paste filters
                                try {
                                    // Method 1: Use TinyMCE's paste event system
                                    var pasteEvent = {
                                        content: html,
                                        preventDefault: function() {},
                                        stopPropagation: function() {}
                                    };
                                    
                                    // Fire paste event so TinyMCE processes it
                                    editor.fire('paste', pasteEvent);
                                    
                                    // If event wasn't prevented, insert the content
                                    if (pasteEvent.content) {
                                        // Use insertContent which respects TinyMCE settings
                                        editor.insertContent(pasteEvent.content);
                                    } else {
                                        // Fallback: insert directly
                                        editor.insertContent(html);
                                    }
                                } catch (e) {
                                    // Fallback: insert HTML directly
                                    console.log('Paste event failed, inserting directly:', e);
                                    editor.insertContent(html);
                                }
                                
                                if (editor.notificationManager) {
                                    var t = getCopyPasteTranslations();
                                    editor.notificationManager.open({
                                        text: '✓ ' + t.text_pasted,
                                        type: 'success',
                                        timeout: 2000
                                    });
                                }
                                if (callback) callback('paste');
                            }).catch(function(err) {
                                console.error('HTML paste failed:', err);
                                // Fallback to plain text
                                fallbackToPlainText();
                            });
                            break;
                        }
                    }
                    
                    // If no HTML found, try plain text
                    if (!htmlFound) {
                        fallbackToPlainText();
                    }
                }).catch(function(err) {
                    console.error('Clipboard read failed:', err);
                    // Try alternative method: simulate paste event
                    tryAlternativePaste();
                });
            } else {
                // Fallback: Try alternative paste method
                tryAlternativePaste();
            }
            
            function fallbackToPlainText() {
                if (navigator.clipboard && navigator.clipboard.readText) {
                    navigator.clipboard.readText().then(function(text) {
                        editor.insertContent(text);
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: '✓ ' + t.text_pasted + ' (plain text)',
                                type: 'success',
                                timeout: 2000
                            });
                        }
                        if (callback) callback('paste');
                    }).catch(function(err) {
                        console.error('Plain text paste failed:', err);
                        tryAlternativePaste();
                    });
                } else {
                    tryAlternativePaste();
                }
            }
            
            function tryAlternativePaste() {
                // Try to trigger TinyMCE's paste event
                try {
                    // Create a paste event and fire it
                    var pasteEvent = new ClipboardEvent('paste', {
                        clipboardData: new DataTransfer()
                    });
                    
                    // Focus editor first
                    editor.getBody().focus();
                    
                    // Try to use execCommand (may not work in all browsers)
                    if (document.execCommand && document.execCommand('paste', false, null)) {
                        setTimeout(function() {
                            if (editor.notificationManager) {
                                var t = getCopyPasteTranslations();
                                editor.notificationManager.open({
                                    text: '✓ ' + t.text_pasted,
                                    type: 'success',
                                    timeout: 2000
                                });
                            }
                            if (callback) callback('paste');
                        }, 100);
                    } else {
                        // Last resort: show message
                        if (editor.notificationManager) {
                            var t = getCopyPasteTranslations();
                            editor.notificationManager.open({
                                text: t.use_ctrl_v,
                                type: 'info',
                                timeout: 3000
                            });
                        }
                    }
                } catch (e) {
                    console.error('Alternative paste method failed:', e);
                    if (editor.notificationManager) {
                        var t = getCopyPasteTranslations();
                        editor.notificationManager.open({
                            text: t.use_ctrl_v,
                            type: 'info',
                            timeout: 3000
                        });
                    }
                }
            }
        };
        
        // Event listeners
        document.getElementById('copypaste-modal-close').onclick = closeModal;
        
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
    function initCopyPasteHelper() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initCopyPasteHelper, 500);
            return;
        }
        
        tinymce.on('AddEditor', function(e) {
            var editor = e.editor;
            
            editor.on('init', function() {
                console.log('✓ Copy/Paste Helper Loaded');
                
                // Add right-click handler for copy/paste options
                editor.on('contextmenu', function(e) {
                    // Don't trigger on images (let copyright helper handle that)
                    if (e.target.nodeName === 'IMG') {
                        return;
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    
                    showCopyPasteModal(editor, function(action) {
                        console.log('Copy/Paste action:', action);
                    });
                });
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCopyPasteHelper);
    } else {
        initCopyPasteHelper();
    }
})();

