// SEO Analysis Box for Filament
// Works with or without yoastseo package
document.addEventListener('alpine:init', () => {
    Alpine.data('seoBox', (config) => ({
        seoScore: null,
        problems: [],
        improvements: [],
        goodPractices: [],
        isAnalyzing: false,
        analysisTimeout: null,
        observer: null,
        pollingInterval: null,
        
        init() {
            // Watch for changes in Livewire fields
            this.watchFields();
            
            // Run initial analysis multiple times to catch fields when they're ready
            // Filament fields load asynchronously
            setTimeout(() => this.analyze(), 300);
            setTimeout(() => this.analyze(), 800);
            setTimeout(() => this.analyze(), 1500);
            setTimeout(() => this.analyze(), 2500);
            
            // Also run when DOM is fully loaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => this.analyze(), 500);
                });
            }
        },
        
        watchFields() {
            // Watch for Livewire field changes (only once)
            if (window.Livewire && !this.livewireHookAdded) {
                this.livewireHookAdded = true;
                Livewire.hook('morph.updated', () => {
                    if (!this.isAnalyzing) {
                        clearTimeout(this.analysisTimeout);
                        this.analysisTimeout = setTimeout(() => {
                            this.analyze();
                        }, 500);
                    }
                });
                
                // Also watch for component updates
                Livewire.hook('message.processed', () => {
                    if (!this.isAnalyzing) {
                        clearTimeout(this.analysisTimeout);
                        this.analysisTimeout = setTimeout(() => {
                            this.analyze();
                        }, 300);
                    }
                });
            }
            
            // Watch for direct input changes using event delegation (only once per form)
            const form = this.$el?.closest('form') || document.querySelector('form');
            if (form && !form.hasAttribute('data-seo-watched')) {
                form.setAttribute('data-seo-watched', 'true');
                const handleInput = (e) => {
                    if (this.isAnalyzing) return;
                    
                    const target = e.target;
                    const fieldName = target.getAttribute('name') || 
                                    target.getAttribute('wire:model') ||
                                    target.getAttribute('wire:model.defer') ||
                                    target.id || '';
                    
                    // Check if it's a relevant field
                    const isRelevantField = fieldName && (
                        fieldName === 'title' ||
                        fieldName === 'description' ||
                        fieldName === 'focus_keyword' ||
                        fieldName.includes('title') ||
                        fieldName.includes('description') ||
                        fieldName.includes('focus_keyword')
                    );
                    
                    if (isRelevantField) {
                        // Debounce analysis
                        clearTimeout(this.analysisTimeout);
                        this.analysisTimeout = setTimeout(() => {
                            this.analyze();
                        }, 800);
                    }
                };
                
                form.addEventListener('input', handleInput, { passive: true });
                form.addEventListener('change', handleInput, { passive: true });
                form.addEventListener('keyup', handleInput, { passive: true });
            }
            
            // Watch for TinyEditor changes - Multiple methods
            try {
                // Method 1: Watch for TinyMCE editor instances
                if (window.tinymce) {
                    const watchTinyMCE = () => {
                        if (window.tinymce && window.tinymce.editors) {
                            window.tinymce.editors.forEach((editor) => {
                                // Check if already watched
                                if (editor._seoWatched) return;
                                editor._seoWatched = true;
                                
                                // Watch for content changes
                                editor.on('input', () => {
                                    if (!this.isAnalyzing) {
                                        clearTimeout(this.analysisTimeout);
                                        this.analysisTimeout = setTimeout(() => {
                                            this.analyze();
                                        }, 1000);
                                    }
                                });
                                
                                editor.on('change', () => {
                                    if (!this.isAnalyzing) {
                                        clearTimeout(this.analysisTimeout);
                                        this.analysisTimeout = setTimeout(() => {
                                            this.analyze();
                                        }, 1000);
                                    }
                                });
                                
                                editor.on('keyup', () => {
                                    if (!this.isAnalyzing) {
                                        clearTimeout(this.analysisTimeout);
                                        this.analysisTimeout = setTimeout(() => {
                                            this.analyze();
                                        }, 1500);
                                    }
                                });
                            });
                        }
                    };
                    
                    // Watch immediately and after delays
                    watchTinyMCE();
                    setTimeout(watchTinyMCE, 1000);
                    setTimeout(watchTinyMCE, 3000);
                    setTimeout(watchTinyMCE, 5000);
                    
                    // Also watch when new editors are initialized
                    const originalInit = window.tinymce.init;
                    if (originalInit) {
                        window.tinymce.init = function(settings) {
                            const result = originalInit.apply(this, arguments);
                            setTimeout(watchTinyMCE, 500);
                            return result;
                        };
                    }
                }
                
                // Method 2: Watch TinyEditor iframe directly
                const checkTinyEditor = () => {
                    const tinyEditorIframes = document.querySelectorAll('iframe[title*="Rich Text Area"], iframe[title*="TinyEditor"], iframe[id*="article_content"]');
                    tinyEditorIframes.forEach((tinyEditorIframe) => {
                        if (tinyEditorIframe._seoWatched) return;
                        tinyEditorIframe._seoWatched = true;
                        
                        try {
                            const editorDoc = tinyEditorIframe.contentDocument || tinyEditorIframe.contentWindow?.document;
                            if (editorDoc) {
                                const editorBody = editorDoc.body;
                                if (editorBody) {
                                    // Watch for content changes in TinyEditor
                                    const observer = new MutationObserver(() => {
                                        if (!this.isAnalyzing) {
                                            clearTimeout(this.analysisTimeout);
                                            this.analysisTimeout = setTimeout(() => {
                                                this.analyze();
                                            }, 1000);
                                        }
                                    });
                                    
                                    observer.observe(editorBody, {
                                        childList: true,
                                        subtree: true,
                                        characterData: true,
                                        attributes: true
                                    });
                                    
                                    // Also listen to input events in iframe
                                    editorBody.addEventListener('input', () => {
                                        if (!this.isAnalyzing) {
                                            clearTimeout(this.analysisTimeout);
                                            this.analysisTimeout = setTimeout(() => {
                                                this.analyze();
                                            }, 1000);
                                        }
                                    }, { passive: true });
                                    
                                    editorBody.addEventListener('keyup', () => {
                                        if (!this.isAnalyzing) {
                                            clearTimeout(this.analysisTimeout);
                                            this.analysisTimeout = setTimeout(() => {
                                                this.analyze();
                                            }, 1500);
                                        }
                                    }, { passive: true });
                                }
                            }
                        } catch (e) {
                            // Cross-origin or other iframe access issues - use polling as fallback
                            console.warn('TinyEditor iframe access issue, using polling fallback');
                        }
                    });
                };
                
                // Check for TinyEditor after delays (it loads asynchronously)
                setTimeout(checkTinyEditor, 1000);
                setTimeout(checkTinyEditor, 3000);
                setTimeout(checkTinyEditor, 5000);
                setTimeout(checkTinyEditor, 8000);
                
                // Method 3: Periodic polling as fallback (every 3 seconds)
                this.pollingInterval = setInterval(() => {
                    if (!this.isAnalyzing) {
                        this.analyze();
                    }
                }, 3000);
            } catch (e) {
                console.warn('Error setting up TinyEditor watchers:', e);
            }
            
            // Watch for focus_keyword field specifically with better detection
            try {
                const watchFocusKeyword = () => {
                    const keywordInputs = document.querySelectorAll(
                        'input[id="data.focus_keyword"], ' +
                        'input[name="focus_keyword"], ' +
                        'input[wire\\:model*="focus_keyword"], ' +
                        'input[wire\\:model*="data.focus_keyword"]'
                    );
                    
                    keywordInputs.forEach((input) => {
                        if (input._seoWatched) return;
                        input._seoWatched = true;
                        
                        ['input', 'change', 'keyup', 'paste'].forEach(eventType => {
                            input.addEventListener(eventType, () => {
                                if (!this.isAnalyzing) {
                                    clearTimeout(this.analysisTimeout);
                                    this.analysisTimeout = setTimeout(() => {
                                        this.analyze();
                                    }, 800);
                                }
                            }, { passive: true });
                        });
                    });
                };
                
                // Watch immediately and after delays
                watchFocusKeyword();
                setTimeout(watchFocusKeyword, 500);
                setTimeout(watchFocusKeyword, 1500);
                setTimeout(watchFocusKeyword, 3000);
            } catch (e) {
                console.warn('Error setting up focus_keyword watcher:', e);
            }
        },
        
        getFieldValue(fieldName) {
            if (!fieldName) return '';
            
            try {
                // Method 1: Try to get from Livewire/Alpine $wire
                if (this.$wire) {
                    try {
                        // Try direct access
                        const value = this.$wire.get(fieldName);
                        if (value !== undefined && value !== null && value !== '') {
                            return String(value).trim();
                        }
                        // Try with data prefix (Filament sometimes uses this)
                        const dataValue = this.$wire.get(`data.${fieldName}`);
                        if (dataValue !== undefined && dataValue !== null && dataValue !== '') {
                            return String(dataValue).trim();
                        }
                    } catch (e) {
                        // Continue to DOM fallback
                    }
                }
                
                // Method 2: Try to get from DOM - Filament form fields
                const form = this.$el?.closest('form');
                if (form) {
                    const escapedFieldName = fieldName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    
                    // Try multiple selectors for Filament fields
                    const selectors = [
                        // Direct name match
                        `input[name="${escapedFieldName}"], textarea[name="${escapedFieldName}"]`,
                        // Wire model match
                        `input[wire\\:model="${escapedFieldName}"], textarea[wire\\:model="${escapedFieldName}"]`,
                        `input[wire\\:model\\.defer="${escapedFieldName}"], textarea[wire\\:model\\.defer="${escapedFieldName}"]`,
                        // Contains match (for nested fields)
                        `input[name*="${escapedFieldName}"], textarea[name*="${escapedFieldName}"]`,
                        `input[wire\\:model*="${escapedFieldName}"], textarea[wire\\:model*="${escapedFieldName}"]`,
                        // Filament specific data attributes
                        `[data-field-name="${escapedFieldName}"] input, [data-field-name="${escapedFieldName}"] textarea`,
                        `[data-field-name*="${escapedFieldName}"] input, [data-field-name*="${escapedFieldName}"] textarea`,
                        // Try by ID
                        `#${escapedFieldName}`,
                    ];
                    
                    for (const selector of selectors) {
                        try {
                            const elements = form.querySelectorAll(selector);
                            for (const element of elements) {
                                if (element && (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA')) {
                                    const value = element.value || '';
                                    if (value && typeof value === 'string' && value.trim()) {
                                        return value.trim();
                                    }
                                }
                            }
                        } catch (e) {
                            // Continue to next selector
                        }
                    }
                }
                
                // Method 3: Try to access via Livewire component directly
                if (window.Livewire) {
                    try {
                        const components = Livewire.all();
                        for (const component of components) {
                            if (component.get && typeof component.get === 'function') {
                                try {
                                    const value = component.get(fieldName);
                                    if (value !== undefined && value !== null && value !== '') {
                                        return String(value).trim();
                                    }
                                    // Try with data prefix
                                    const dataValue = component.get(`data.${fieldName}`);
                                    if (dataValue !== undefined && dataValue !== null && dataValue !== '') {
                                        return String(dataValue).trim();
                                    }
                                } catch (e) {
                                    // Continue
                                }
                            }
                        }
                    } catch (e) {
                        // Ignore
                    }
                }
            } catch (error) {
                console.warn('Error getting field value:', error);
            }
            
            return '';
        },
        
        analyze() {
            // Prevent infinite loops
            if (this.isAnalyzing) {
                return;
            }
            
            this.isAnalyzing = true;
            
            try {
                // Get field values - DIRECT DOM ACCESS FIRST (most reliable)
                let title = '';
                let description = '';
                let articleContent = ''; // Main article content from TinyEditor
                let focusKeyword = '';
                
                // Method 1: DIRECT DOM ACCESS - Get by exact ID (Filament uses id="data.title")
                try {
                    const titleInput = document.getElementById('data.title');
                    if (titleInput && titleInput.value) {
                        title = String(titleInput.value).trim();
                    }
                    
                    const descTextarea = document.getElementById('data.description');
                    if (descTextarea && descTextarea.value) {
                        description = String(descTextarea.value).trim();
                    }
                    
                    const keywordInput = document.getElementById('data.focus_keyword');
                    if (keywordInput && keywordInput.value) {
                        focusKeyword = String(keywordInput.value).trim();
                    }
                } catch (e) {
                    // Continue to other methods
                }
                
                // Get article_content from TinyEditor (main content)
                try {
                    // Try to find TinyEditor iframe or content area
                    const tinyEditorIframe = document.querySelector('iframe[title*="Rich Text Area"], iframe[title*="TinyEditor"], iframe[id*="article_content"]');
                    if (tinyEditorIframe && tinyEditorIframe.contentDocument) {
                        const editorBody = tinyEditorIframe.contentDocument.body;
                        if (editorBody) {
                            articleContent = editorBody.innerText || editorBody.textContent || '';
                        }
                    }
                    
                    // Also try to get from wire:model with article_content
                    if (!articleContent) {
                        const articleContentInputs = document.querySelectorAll('textarea[wire\\:model*="article_content"], textarea[wire\\:model*="postArticle"], input[wire\\:model*="article_content"]');
                        for (const input of articleContentInputs) {
                            const wireModel = input.getAttribute('wire:model') || 
                                            input.getAttribute('wire:model.live') ||
                                            input.getAttribute('wire:model.defer');
                            if (wireModel && (wireModel.includes('article_content') || wireModel.includes('postArticle'))) {
                                if (input.value) {
                                    // Strip HTML tags for word count
                                    const tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = input.value;
                                    articleContent = tempDiv.innerText || tempDiv.textContent || '';
                                }
                            }
                        }
                    }
                    
                    // Try Alpine.js $wire for article_content
                    if (!articleContent && this.$wire) {
                        const articleVal = this.$wire?.data?.postArticle?.article_content || 
                                         this.$wire?.get?.('data.postArticle.article_content') ||
                                         this.$wire?.get?.('postArticle.article_content');
                        if (articleVal) {
                            // Strip HTML tags
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = String(articleVal);
                            articleContent = tempDiv.innerText || tempDiv.textContent || '';
                        }
                    }
                } catch (e) {
                    console.warn('Error getting article_content:', e);
                }
                
                // Method 2: Query by wire:model attribute
                if (!title || !description || !focusKeyword || !articleContent) {
                    try {
                        // Find all inputs/textarea with wire:model
                        const allInputs = document.querySelectorAll('input[wire\\:model], textarea[wire\\:model], input[wire\\:model\\.live], textarea[wire\\:model\\.live]');
                        
                        for (const input of allInputs) {
                            const wireModel = input.getAttribute('wire:model') || 
                                            input.getAttribute('wire:model.live') ||
                                            input.getAttribute('wire:model.defer');
                            
                            if (wireModel) {
                                if ((wireModel.includes('data.title') || wireModel === 'title') && !title && input.value) {
                                    title = String(input.value).trim();
                                }
                                if ((wireModel.includes('data.description') || wireModel === 'description') && !description && input.value) {
                                    description = String(input.value).trim();
                                }
                                if ((wireModel.includes('data.focus_keyword') || wireModel === 'focus_keyword') && !focusKeyword && input.value) {
                                    focusKeyword = String(input.value).trim();
                                }
                                // Check for article_content
                                if ((wireModel.includes('article_content') || wireModel.includes('postArticle')) && !articleContent && input.value) {
                                    // Strip HTML tags
                                    const tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = input.value;
                                    articleContent = tempDiv.innerText || tempDiv.textContent || '';
                                }
                            }
                        }
                    } catch (e) {
                        // Continue
                    }
                }
                
                // Method 3: Try Alpine.js $wire if available
                if ((!title || !description) && this.$wire) {
                    try {
                        // Try accessing via Alpine $wire
                        if (!title) {
                            const titleVal = this.$wire?.data?.title || 
                                           this.$wire?.get?.('data.title') ||
                                           this.$wire?.get?.('title');
                            if (titleVal) title = String(titleVal).trim();
                        }
                        
                        if (!description) {
                            const descVal = this.$wire?.data?.description || 
                                         this.$wire?.get?.('data.description') ||
                                         this.$wire?.get?.('description');
                            if (descVal) description = String(descVal).trim();
                        }
                        
                        if (!focusKeyword) {
                            const keywordVal = this.$wire?.data?.focus_keyword || 
                                            this.$wire?.get?.('data.focus_keyword') ||
                                            this.$wire?.get?.('focus_keyword');
                            if (keywordVal) focusKeyword = String(keywordVal).trim();
                        }
                    } catch (e) {
                        // Continue
                    }
                }
                
                // Method 4: Try getFieldValue function as fallback
                if (!title) title = (this.getFieldValue(config.seoTitleField) || this.getFieldValue(config.titleField) || '').trim();
                if (!description) description = (this.getFieldValue(config.seoDescriptionField) || this.getFieldValue(config.contentField) || '').trim();
                if (!focusKeyword) focusKeyword = (this.getFieldValue(config.focusKeywordField) || '').trim();
                
                // Method 3: Direct DOM search - most reliable for Filament
                // Filament uses wire:model="data.fieldName" format
                // Search entire document, not just form
                try {
                    // Search for title field - Filament uses data.title format
                    if (!title) {
                        const titleSelectors = [
                            'input[id="data.title"]',  // Exact ID match
                            'input[wire\\:model*="data.title"]',  // Wire model with data.title
                            'input[wire\\:model*="title"]',  // Any wire model with title
                            'input[name="title"]',
                            'input[name*="title"]',
                            'input[id*="title"]',
                            'input[data-field-name="title"]',
                            'input[data-field-name*="title"]',
                            'input[type="text"][name*="title"]',
                        ];
                        
                        for (const selector of titleSelectors) {
                            try {
                                const inputs = document.querySelectorAll(selector);
                                for (const input of inputs) {
                                    // Check if it's the title field by wire:model attribute
                                    const wireModel = input.getAttribute('wire:model') || 
                                                     input.getAttribute('wire:model.live') ||
                                                     input.getAttribute('wire:model.defer');
                                    const isTitleField = wireModel && (
                                        wireModel.includes('data.title') || 
                                        wireModel === 'title' ||
                                        wireModel === 'data.title'
                                    );
                                    
                                    if (input && input.value && typeof input.value === 'string' && input.value.trim()) {
                                        // If it has wire:model, check if it's title field
                                        if (wireModel && !isTitleField) {
                                            continue; // Skip if it's not title field
                                        }
                                        title = input.value.trim();
                                        break;
                                    }
                                }
                                if (title) break;
                            } catch (e) {
                                // Continue to next selector
                            }
                        }
                    }
                    
                    // Search for description field - Filament uses data.description format
                    if (!description) {
                        const descSelectors = [
                            'textarea[id="data.description"]',  // Exact ID match
                            'textarea[wire\\:model*="data.description"]',  // Wire model with data.description
                            'textarea[wire\\:model*="description"]',  // Any wire model with description
                            'textarea[name="description"]',
                            'textarea[name*="description"]',
                            'textarea[id*="description"]',
                            'textarea[data-field-name="description"]',
                            'textarea[data-field-name*="description"]',
                        ];
                        
                        for (const selector of descSelectors) {
                            try {
                                const inputs = document.querySelectorAll(selector);
                                for (const input of inputs) {
                                    // Check if it's the description field by wire:model attribute
                                    const wireModel = input.getAttribute('wire:model') || 
                                                     input.getAttribute('wire:model.live') ||
                                                     input.getAttribute('wire:model.defer');
                                    const isDescField = wireModel && (
                                        wireModel.includes('data.description') || 
                                        wireModel === 'description' ||
                                        wireModel === 'data.description'
                                    );
                                    
                                    if (input && input.value && typeof input.value === 'string' && input.value.trim()) {
                                        // If it has wire:model, check if it's description field
                                        if (wireModel && !isDescField) {
                                            continue; // Skip if it's not description field
                                        }
                                        description = input.value.trim();
                                        break;
                                    }
                                }
                                if (description) break;
                            } catch (e) {
                                // Continue to next selector
                            }
                        }
                    }
                    
                    // Search for focus_keyword field - Filament uses data.focus_keyword format
                    if (!focusKeyword) {
                        const keywordSelectors = [
                            'input[id="data.focus_keyword"]',  // Exact ID match
                            'input[wire\\:model*="data.focus_keyword"]',  // Wire model with data.focus_keyword
                            'input[wire\\:model*="focus_keyword"]',  // Any wire model with focus_keyword
                            'input[name="focus_keyword"]',
                            'input[name*="focus_keyword"]',
                            'input[id*="focus_keyword"]',
                        ];
                        
                        for (const selector of keywordSelectors) {
                            try {
                                const inputs = document.querySelectorAll(selector);
                                for (const input of inputs) {
                                    // Check if it's the focus_keyword field by wire:model attribute
                                    const wireModel = input.getAttribute('wire:model') || 
                                                     input.getAttribute('wire:model.live') ||
                                                     input.getAttribute('wire:model.defer');
                                    const isKeywordField = wireModel && (
                                        wireModel.includes('data.focus_keyword') || 
                                        wireModel === 'focus_keyword' ||
                                        wireModel === 'data.focus_keyword'
                                    );
                                    
                                    if (input && input.value && typeof input.value === 'string' && input.value.trim()) {
                                        // If it has wire:model, check if it's focus_keyword field
                                        if (wireModel && !isKeywordField) {
                                            continue; // Skip if it's not focus_keyword field
                                        }
                                        focusKeyword = input.value.trim();
                                        break;
                                    }
                                }
                                if (focusKeyword) break;
                            } catch (e) {
                                // Continue to next selector
                            }
                        }
                    }
                } catch (e) {
                    console.warn('Error in DOM search:', e);
                }
                
                // Use article_content as main content, fallback to description
                const mainContent = articleContent || description || '';
                
                // Debug: Log found values (always log for debugging)
                console.log('🔍 SEO Analysis Debug:', {
                    title: title ? title.substring(0, 50) + '...' : '❌ EMPTY',
                    titleLength: title ? title.length : 0,
                    description: description ? description.substring(0, 50) + '...' : '❌ EMPTY',
                    descriptionLength: description ? description.length : 0,
                    articleContent: articleContent ? articleContent.substring(0, 50) + '...' : '❌ EMPTY',
                    articleContentLength: articleContent ? articleContent.length : 0,
                    articleContentWords: articleContent ? articleContent.trim().split(/\s+/).filter(w => w.length > 0).length : 0,
                    focusKeyword: focusKeyword || '❌ EMPTY',
                    titleInputFound: !!document.getElementById('data.title'),
                    descTextareaFound: !!document.getElementById('data.description'),
                    tinyEditorFound: !!document.querySelector('iframe[title*="Rich Text Area"], iframe[title*="TinyEditor"]'),
                });
                
                // If no content at all, show 0% or null
                if (!title && !description && !mainContent && !focusKeyword) {
                    this.seoScore = 0;
                    this.problems = [];
                    this.improvements = [];
                    this.goodPractices = [];
                    this.isAnalyzing = false;
                    return;
                }
                
                // If no title and no description, show 0%
                if (!title && !description) {
                    this.seoScore = 0;
                    this.problems = [];
                    this.improvements = [];
                    this.goodPractices = [];
                    this.isAnalyzing = false;
                    return;
                }
                
                // Prepare text for analysis - use article_content as main content
                const text = `${title} ${mainContent}`.trim();
                
                // Run SEO analysis - pass article_content as content
                const results = this.runSeoAnalysis(text, title, description, mainContent, focusKeyword);
                
                // Update UI - ensure arrays contain only strings
                this.seoScore = results.score;
                this.problems = results.problems.map(p => String(p));
                this.improvements = results.improvements.map(i => String(i));
                this.goodPractices = results.goodPractices.map(g => String(g));
                
            } catch (error) {
                console.error('SEO Analysis Error:', error);
                // Reset on error
                this.seoScore = null;
                this.problems = [];
                this.improvements = [];
                this.goodPractices = [];
            } finally {
                this.isAnalyzing = false;
            }
        },
        
        runSeoAnalysis(text, title, description, content, focusKeyword) {
            const t = config.translations || {}; // Get translations from config
            
            const results = {
                score: 0,
                problems: [],
                improvements: [],
                goodPractices: []
            };
            
            // If everything is empty, return 0%
            if (!title && !description && !content && !focusKeyword) {
                results.score = 0;
                return results;
            }
            
            // Basic SEO checks (since YoastSEO.js might need more setup)
            // Start with 0 and add points for good practices, or start with 100 and subtract
            // Better approach: Start with 0 and build up
            let score = 0;
            const maxScore = 100;
            
            // Scoring system: Each element contributes points
            // Title: 25 points max (15 for length + 10 for keyword)
            // Description: 25 points max (15 for length + 10 for keyword)
            // Content: 30 points max (based on word count)
            // Focus Keyword: 20 points max (based on density)
            // Total: 100 points
            
            // Title checks - use title as SEO title (25 points)
            const seoTitle = title || '';
            if (!seoTitle || seoTitle.trim().length === 0) {
                results.problems.push(t.title_missing || 'Title is missing (used as SEO title)');
                // No points for missing title
            } else {
                if (seoTitle.length >= 30 && seoTitle.length <= 60) {
                    results.goodPractices.push(t.title_length_good || 'Title length is good for SEO');
                    score += 15; // Good length
                } else if (seoTitle.length < 30) {
                    results.improvements.push(t.title_too_short || 'Title is too short for SEO (aim for 30-60 characters)');
                    score += 8; // Partial points
                } else {
                    results.improvements.push(t.title_too_long || 'Title is too long for SEO (aim for 30-60 characters)');
                    score += 8; // Partial points
                }
                
                if (focusKeyword && focusKeyword.trim() && seoTitle.toLowerCase().includes(focusKeyword.toLowerCase())) {
                    results.goodPractices.push(t.keyword_in_title || 'Focus keyword found in title');
                    score += 10; // Bonus for keyword in title
                } else if (focusKeyword && focusKeyword.trim()) {
                    results.improvements.push(t.keyword_not_in_title || 'Focus keyword not found in title');
                    // No bonus points
                }
            }
            
            // Description checks - use description as SEO description (25 points)
            const seoDescription = description || '';
            if (!seoDescription || seoDescription.trim().length === 0) {
                results.problems.push(t.description_missing || 'Description is missing (used as SEO description)');
                // No points for missing description
            } else {
                if (seoDescription.length >= 120 && seoDescription.length <= 160) {
                    results.goodPractices.push(t.description_length_good || 'Description length is good for SEO');
                    score += 15; // Good length
                } else if (seoDescription.length < 120) {
                    results.improvements.push(t.description_too_short || 'Description is too short for SEO (aim for 120-160 characters)');
                    score += 8; // Partial points
                } else {
                    results.improvements.push(t.description_too_long || 'Description is too long for SEO (aim for 120-160 characters)');
                    score += 8; // Partial points
                }
                
                if (focusKeyword && focusKeyword.trim() && seoDescription.toLowerCase().includes(focusKeyword.toLowerCase())) {
                    results.goodPractices.push(t.keyword_in_description || 'Focus keyword found in description');
                    score += 10; // Bonus for keyword in description
                } else if (focusKeyword && focusKeyword.trim()) {
                    results.improvements.push(t.keyword_not_in_description || 'Focus keyword not found in description');
                    // No bonus points
                }
            }
            
            // Content checks - use actual content (article_content), check word count (30 points)
            const contentText = content || text || '';
            const wordCount = contentText.trim().split(/\s+/).filter(word => word.length > 0).length;
            
            if (!contentText || wordCount === 0) {
                // No points for empty content
            } else if (wordCount < 300) {
                const msg = (t.content_too_short || 'Content is too short (:words words, aim for at least 300 words)').replace(':words', wordCount);
                results.improvements.push(msg);
                score += Math.min(20, Math.floor((wordCount / 300) * 20)); // Proportional points up to 20
            } else if (wordCount >= 300 && wordCount < 1000) {
                const msg = (t.content_length_good || 'Content length is good (:words words)').replace(':words', wordCount);
                results.goodPractices.push(msg);
                score += 30; // Good content length - full points for 300+ words
            } else {
                const msg = (t.content_length_excellent || 'Content length is excellent (:words words)').replace(':words', wordCount);
                results.goodPractices.push(msg);
                score += 30; // Excellent content length - same points (max is 30)
            }
            
            // Focus keyword checks - search in actual content (20 points)
            if (focusKeyword && typeof focusKeyword === 'string' && focusKeyword.trim()) {
                try {
                    const escapedKeyword = focusKeyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const searchText = contentText || text || '';
                    const keywordCount = (searchText.toLowerCase().match(new RegExp(escapedKeyword.toLowerCase(), 'g')) || []).length;
                    if (keywordCount === 0) {
                        results.problems.push(t.keyword_not_in_content || 'Focus keyword not found in content');
                        score += 5; // Just having a keyword gives some points
                    } else if (keywordCount >= 2 && keywordCount <= 10) {
                        results.goodPractices.push(t.keyword_density_good || 'Focus keyword density is good');
                        score += 20; // Good keyword density
                    } else if (keywordCount === 1) {
                        results.improvements.push(t.keyword_appears_once || 'Focus keyword appears only once (aim for 2-5 times)');
                        score += 10; // Partial points
                    } else {
                        results.improvements.push(t.keyword_stuffing || 'Focus keyword appears too many times (keyword stuffing)');
                        score += 10; // Partial points (penalty for stuffing)
                    }
                } catch (e) {
                    // Ignore regex errors
                    score += 5; // Just having a keyword gives some points
                }
            } else {
                results.improvements.push(t.add_focus_keyword || 'Consider adding a focus keyword for better SEO');
                // No points for missing keyword
            }
            
            // Ensure score is between 0 and 100
            results.score = Math.max(0, Math.min(100, Math.round(score)));
            
            return results;
        },
        
        // Cleanup function
        destroy() {
            // Clear polling interval
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
            
            // Clear analysis timeout
            if (this.analysisTimeout) {
                clearTimeout(this.analysisTimeout);
                this.analysisTimeout = null;
            }
        }
    }));
});

