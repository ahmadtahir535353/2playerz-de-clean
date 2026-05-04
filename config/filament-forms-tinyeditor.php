<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of profiles to use it in your application.
    |
    */

    'profiles' => [

        'default' => [
            'plugins' => 'advlist autoresize codesample directionality emoticons fullscreen hr image imagetools link lists media table toc wordcount',
            'toolbar' => 'undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,
        ],

        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | link emoticons',
            'upload_directory' => null,
        ],

        'template' => [
            'plugins' => 'autoresize template',
            'toolbar' => 'template',
            'upload_directory' => null,
        ],

        'custom' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help',
            'valid_elements' => '*[*]',
            'extended_valid_elements' => 'div[id|class|style],span[*],iframe[src|width|height|frameborder|allowfullscreen|scrolling|allow|sandbox|loading|title|name|id|class|style],script[*],section[*]',
            
            'forced_root_block' => false,
            'entity_encoding' => 'raw',
            
            // Allow iframe embeds from external domains
            'allow_script_urls' => true,
            'allow_html_in_named_anchor' => true,
            
            // Media plugin configuration for better embed support
            'media_live_embeds' => true,
            'media_url_resolver' => 'function(data, resolve) { resolve({html: data.html}); }',
            
            // Custom configs - Link plugin configuration with 3 target options
            'custom_configs' => [
                'link_target_list' => [
                    ['title' => 'Current window', 'value' => '_self'],
                    ['title' => 'New window', 'value' => '_blank'],
                    ['title' => 'Link Cart', 'value' => '_self_linkcart']
                ]
            ],
            
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
                    // Handle link - convert _self_linkcart to _self with class
                    if (e.content && e.content.indexOf('target=\"_self_linkcart\"') !== -1) {
                        e.content = e.content.replace(/target=\"_self_linkcart\"/gi, 'target=\"_self\" class=\"link-cart\"');
                    }
                });
                
                // Handle link dialog to add link-cart class for 3rd option (Link Cart)
                editor.on('ExecCommand', function(e) {
                    if (e.command === 'mceLink') {
                        setTimeout(function() {
                            var dialog = editor.windowManager.getWindows()[0];
                            if (dialog) {
                                var targetList = dialog.find('#target');
                                var classField = dialog.find('#class');
                                
                                if (targetList && classField) {
                                    // Listen for target change
                                    targetList.on('change', function(e) {
                                        var targetValue = e.target.value();
                                        if (targetValue === '_self_linkcart') {
                                            // Auto-fill class field with link-cart
                                            classField.value('link-cart');
                                        } else if (targetValue !== '_self_linkcart' && classField.value() === 'link-cart') {
                                            // Clear class if switching away from Link Cart
                                            classField.value('');
                                        }
                                    });
                                    
                                    // Also check on dialog open if target is already _self_linkcart
                                    var currentTarget = targetList.value();
                                    if (currentTarget === '_self_linkcart') {
                                        classField.value('link-cart');
                                    }
                                }
                            }
                        }, 100);
                    }
                    
                    // Process when link is inserted/updated - convert _self_linkcart to _self
                    if (e.command === 'mceInsertLink' || e.command === 'mceUpdateLink') {
                        setTimeout(function() {
                            var node = editor.selection.getNode();
                            if (node && node.nodeName === 'A') {
                                var target = node.getAttribute('target');
                                if (target === '_self_linkcart') {
                                    node.setAttribute('target', '_self');
                                    var existingClass = node.getAttribute('class') || '';
                                    if (existingClass.indexOf('link-cart') === -1) {
                                        node.setAttribute('class', (existingClass ? existingClass + ' ' : '') + 'link-cart');
                                    }
                                }
                            }
                            // Also check all links in content
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
            }"
        ],
        /*
        |--------------------------------------------------------------------------
        | Custom Configs
        |--------------------------------------------------------------------------
        |
        | If you want to add custom configurations to directly tinymce
        | You can use custom_configs key as an array
        |
        */

        /*
          'default' => [
            'plugins' => 'advlist autoresize codesample directionality emoticons fullscreen hr image imagetools link lists media table toc wordcount',
            'toolbar' => 'undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'custom_configs' => [
                'allow_html_in_named_anchor' => true,
                'link_default_target' => '_blank',
                'codesample_global_prismjs' => true,
                'image_advtab' => true,
                'image_class_list' => [
                  [
                    'title' => 'None',
                    'value' => '',
                  ],
                  [
                    'title' => 'Fluid',
                    'value' => 'img-fluid',
                  ],
              ],
            ]
        ],
        */

    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of templates to use it in your application.
    |
    | https://www.tiny.cloud/docs/plugins/opensource/template/#templates
    |
    | ex: TinyEditor::make('content')->profiles('template')->template('example')
    */

    'templates' => [

        'example' => [
            // content
            ['title' => 'Some title 1', 'description' => 'Some desc 1', 'content' => 'My content'],
            // url
            ['title' => 'Some title 2', 'description' => 'Some desc 2', 'url' => 'http://localhost'],
        ],

    ],
];
