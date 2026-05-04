<?php

return [
    'version' => [
        'tiny' => '7.3.0',
        'language' => [
            // https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/
            'version' => '24.7.29',
            'package' => 'langs7',
        ],
        'licence_key' => env('TINY_LICENSE_KEY', 'no-api-key'),
    ],
    'provider' => 'cloud', // cloud|vendor
    // 'direction' => 'rtl',
    /**
     * change darkMode: 'auto'|'force'|'class'|'media'|false|'custom'
     */
    'darkMode' => 'auto',

    /** cutsom */
    'skins' => [
        // oxide, oxide-dark, tinymce-5, tinymce-5-dark
        'ui' => 'oxide',

        // dark, default, document, tinymce-5, tinymce-5-dark, writer
        'content' => 'default'
    ],

    'profiles' => [
        'default' => [
            'plugins' => 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignleft aligncenter alignright | numlist bullist outdent indent | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,
            // Paste settings to preserve formatting (like Ctrl+V)
            'paste_as_text' => false,
            'paste_data_images' => true,
            'paste_merge_formats' => true,
            'paste_remove_styles_if_webkit' => false,
        ],

        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | numlist bullist | link emoticons',
            'upload_directory' => null,
            // Paste settings to preserve formatting (like Ctrl+V)
            'paste_as_text' => false,
            'paste_data_images' => true,
            'paste_merge_formats' => true,
        ],

        'minimal' => [
            'plugins' => 'link wordcount',
            'toolbar' => 'bold italic link numlist bullist',
            'upload_directory' => null,
            // Paste settings to preserve formatting (like Ctrl+V)
            'paste_as_text' => false,
            'paste_data_images' => true,
            'paste_merge_formats' => true,
        ],

        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help',
            'upload_directory' => null,
            // Paste settings to preserve formatting (like Ctrl+V)
            'paste_as_text' => false,
            'paste_data_images' => true,
            'paste_merge_formats' => true,
            'paste_remove_styles_if_webkit' => false,
        ],
        'custom' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image imagecopyright link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help',
            'valid_elements' => '*[*]',
            'extended_valid_elements' => 'div[id|class|style],span[*],iframe[src|width|height|frameborder|allowfullscreen|scrolling|allow|sandbox|loading|title|name|id|class|style],script[*],section[*],img[*]',
            
            'forced_root_block' => false,
            'entity_encoding' => 'raw',
            
            // Allow iframe embeds from external domains
            'allow_script_urls' => true,
            'allow_html_in_named_anchor' => true,
            
            // Media plugin configuration for better embed support
            'media_live_embeds' => true,
            'media_url_resolver' => 'function(data, resolve) { resolve({html: data.html}); }',
            
            // Image plugin configuration
            'image_advtab' => true,
            'image_caption' => true,
            
            // Custom configs - Link plugin configuration with 3 target options
            'custom_configs' => [
                'link_target_list' => [
                    ['title' => 'Current window', 'value' => '_self'],
                    ['title' => 'New window', 'value' => '_blank'],
                    ['title' => 'Warenkorb verknüpfen', 'value' => '_self_linkcart']
                ]
            ],
            
            // Paste settings to preserve formatting (like Ctrl+V)
            'paste_as_text' => false,
            'paste_data_images' => true,
            'paste_merge_formats' => true,
            'paste_remove_styles_if_webkit' => false,
            
            // Additional iframe security and compatibility settings
            'iframe_attrs' => [
                'allowfullscreen' => 'true',
                'scrolling' => 'no',
                'sandbox' => 'allow-scripts allow-same-origin allow-presentation'
            ],
            
            'setup' => "function(editor) {
                editor.ui.registry.addButton('infobox', {
                    text: 'Info Box',
                    onAction: function() {
                        editor.insertContent('<div style=\"background: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px 0;\">Info Box Content</div>');
                    }
                });
                
                // Custom iframe and link handling
                editor.on('BeforeSetContent', function(e) {
                    // Handle iframe
                    if (e.content && e.content.indexOf('<iframe') !== -1) {
                        e.content = e.content.replace(/<iframe([^>]*)>/gi, function(match, attrs) {
                            return '<iframe' + attrs + '>';
                        });
                    }
                    // Handle link - convert _self_linkcart to _self with class (only this specific case)
                    if (e.content && e.content.indexOf('target=\"_self_linkcart\"') !== -1) {
                        e.content = e.content.replace(/target=\"_self_linkcart\"/gi, 'target=\"_self\" class=\"link-cart\"');
                    }
                    // Don't modify _blank or other target values - let them pass through as is
                });
                
                // Handle link dialog - add 3rd option and auto-fill class
                editor.on('OpenDialog', function(e) {
                    if (e.dialog && e.dialog.id === 'link') {
                        setTimeout(function() {
                            var dialogApi = e.dialog;
                            var targetList = dialogApi.find('#target');
                            var classField = dialogApi.find('#class');
                            
                            if (targetList) {
                                // Get current items
                                var items = targetList.items();
                                var hasLinkCart = false;
                                
                                // Check if Link Cart already exists
                                for (var i = 0; i < items.length; i++) {
                                    if (items[i].value === '_self_linkcart') {
                                        hasLinkCart = true;
                                        break;
                                    }
                                }
                                
                                // If not in config, add it manually via DOM
                                if (!hasLinkCart) {
                                    var dialogEl = document.querySelector('.tox-dialog');
                                    if (dialogEl) {
                                        var targetSelect = dialogEl.querySelector('select');
                                        if (targetSelect) {
                                            var option = document.createElement('option');
                                            option.text = 'Link Cart';
                                            option.value = '_self_linkcart';
                                            targetSelect.appendChild(option);
                                        }
                                    }
                                }
                                
                                // Setup change listener
                                if (targetList && classField) {
                                    targetList.on('change', function(e) {
                                        var targetValue = e.target.value();
                                        if (targetValue === '_self_linkcart') {
                                            classField.value('link-cart');
                                        } else if (targetValue !== '_self_linkcart' && classField.value() === 'link-cart') {
                                            classField.value('');
                                        }
                                    });
                                }
                            }
                        }, 100);
                    }
                });
                
                // Fallback: Also try ExecCommand event
                editor.on('ExecCommand', function(e) {
                    if (e.command === 'mceLink') {
                        setTimeout(function() {
                            var dialogEl = document.querySelector('.tox-dialog');
                            if (dialogEl) {
                                var targetSelect = dialogEl.querySelector('select');
                                if (targetSelect) {
                                    // Check if Link Cart exists
                                    var hasLinkCart = false;
                                    for (var i = 0; i < targetSelect.options.length; i++) {
                                        if (targetSelect.options[i].value === '_self_linkcart') {
                                            hasLinkCart = true;
                                            break;
                                        }
                                    }
                                    
                                    // Add if not present
                                    if (!hasLinkCart) {
                                        var option = document.createElement('option');
                                        option.text = 'Link Cart';
                                        option.value = '_self_linkcart';
                                        targetSelect.appendChild(option);
                                    }
                                    
                                    // Find class input
                                    var classInput = null;
                                    var inputs = dialogEl.querySelectorAll('input[type=\"text\"]');
                                    for (var inp = 0; inp < inputs.length; inp++) {
                                        var label = inputs[inp].closest('.tox-form__group') ? inputs[inp].closest('.tox-form__group').querySelector('label') : null;
                                        if (label && label.textContent && label.textContent.toLowerCase().indexOf('class') !== -1) {
                                            classInput = inputs[inp];
                                            break;
                                        }
                                    }
                                    
                                    // Add change listener
                                    if (classInput) {
                                        targetSelect.addEventListener('change', function() {
                                            if (this.value === '_self_linkcart') {
                                                classInput.value = 'link-cart';
                                            } else if (this.value !== '_self_linkcart' && classInput.value === 'link-cart') {
                                                classInput.value = '';
                                            }
                                        });
                                    }
                                }
                            }
                        }, 300);
                    }
                });
                
                // Process when link is inserted/updated - convert _self_linkcart to _self (only this case)
                // IMPORTANT: Preserve _blank and _self exactly as set by user
                editor.on('ExecCommand', function(e) {
                    if (e.command === 'mceInsertLink' || e.command === 'mceUpdateLink') {
                        setTimeout(function() {
                            // Get the link that was just inserted/updated
                            var node = editor.selection.getNode();
                            if (node && node.nodeName === 'A') {
                                var target = node.getAttribute('target');
                                
                                // Only convert _self_linkcart, preserve everything else
                                if (target === '_self_linkcart') {
                                    node.setAttribute('target', '_self');
                                    var existingClass = node.getAttribute('class') || '';
                                    if (existingClass.indexOf('link-cart') === -1) {
                                        node.setAttribute('class', (existingClass ? existingClass + ' ' : '') + 'link-cart');
                                    }
                                }
                                // If target is _blank, ensure it stays _blank (don't modify)
                                // If target is _self, ensure it stays _self (don't modify)
                                // If no target, leave it as is
                            }
                            
                            // Process all _self_linkcart links in content
                            var links = editor.getBody().querySelectorAll('a[target=\"_self_linkcart\"]');
                            links.forEach(function(link) {
                                link.setAttribute('target', '_self');
                                var existingClass = link.getAttribute('class') || '';
                                if (existingClass.indexOf('link-cart') === -1) {
                                    link.setAttribute('class', (existingClass ? existingClass + ' ' : '') + 'link-cart');
                                }
                            });
                        }, 50);
                    }
                });
                
                // Also ensure _blank is preserved when getting content
                editor.on('GetContent', function(e) {
                    if (e.content) {
                        // Don't modify _blank targets - preserve them as-is
                        // Only convert _self_linkcart if it somehow appears in content
                        if (e.content.indexOf('target=\"_self_linkcart\"') !== -1) {
                            e.content = e.content.replace(/target=\"_self_linkcart\"/gi, 'target=\"_self\" class=\"link-cart\"');
                        }
                    }
                });
            }"
        ],
    ],

    /**
     * this option will load optional language file based on you app locale
     * example:
     * languages => [
     *      'fa' => 'https://cdn.jsdelivr.net/npm/tinymce-i18n@24.7.29/langs7/fa.min.js',
     *      'es' => 'https://cdn.jsdelivr.net/npm/tinymce-i18n@24.7.29/langs7/es.min.js',
     *      'ja' => asset('assets/ja.min.js')
     * ]
     */
    'languages' => [],

    'extra' => [
        'toolbar' => [
            // 'fontsize' => '10px 12px 13px 14px 16px 18px 20px',
            // 'fontfamily' => 'Tahoma=tahoma,arial,helvetica,sans-serif;',
        ],
        'custom' => [
            'custom_configs' => [
                'link_target_list' => [
                    ['title' => 'Current window', 'value' => '_self'],
                    ['title' => 'New window', 'value' => '_blank'],
                    ['title' => 'Link Cart', 'value' => '_self_linkcart']
                ]
            ]
        ]
    ]
];
