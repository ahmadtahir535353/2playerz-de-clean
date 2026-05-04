<div class="js-cookie-consent cookie-consent" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; background: rgba(0, 0, 0, 0.85); padding: 15px 10px; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        <div class="cookie-consent-content" style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <div class="cookie-consent-header" style="margin-bottom: 15px;">
                <h4 class="cookie-consent-title" style="font-weight: 600; color: #333; font-size: 18px; margin: 0 0 10px 0; line-height: 1.4;">
                    {{ trans('cookie-consent::texts.title') }}
                </h4>
                <p class="cookie-consent__message" style="color: #666; font-size: 14px; line-height: 1.6; margin: 0;">
                    {!! trans('cookie-consent::texts.message') !!}
                </p>
            </div>
            <div class="cookie-consent-buttons" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
                <button type="button" class="js-cookie-consent-customise cookie-consent__customise" style="background: #ffffff; border: 2px solid #0066cc; color: #0066cc; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; min-width: 120px; transition: all 0.3s ease;">
                    {{ trans('cookie-consent::texts.customise') }}
                </button>
                <button type="button" class="js-cookie-consent-reject cookie-consent__reject" style="background: #dc3545; border: 2px solid #dc3545; color: #ffffff; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; min-width: 120px; transition: all 0.3s ease;">
                    {{ trans('cookie-consent::texts.reject') }}
                </button>
                <button type="button" class="js-cookie-consent-agree cookie-consent__agree" style="background: #28a745; border: 2px solid #28a745; color: #ffffff; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; min-width: 120px; transition: all 0.3s ease;">
                    {{ trans('cookie-consent::texts.agree') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Cookie Consent Styling */
    .cookie-consent__customise:hover {
        background: #f0f0f0 !important;
        transform: translateY(-1px);
    }
    
    .cookie-consent__reject:hover {
        background: #c82333 !important;
        border-color: #c82333 !important;
        transform: translateY(-1px);
    }
    
    .cookie-consent__agree:hover {
        background: #218838 !important;
        border-color: #218838 !important;
        transform: translateY(-1px);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .cookie-consent {
            padding: 10px 5px !important;
        }
        
        .cookie-consent-content {
            padding: 15px !important;
        }
        
        .cookie-consent-title {
            font-size: 16px !important;
        }
        
        .cookie-consent__message {
            font-size: 13px !important;
        }
        
        .cookie-consent-buttons {
            flex-direction: column;
        }
        
        .cookie-consent-buttons button {
            width: 100%;
            min-width: auto !important;
        }
    }
    
    @media (max-width: 480px) {
        .cookie-consent-title {
            font-size: 15px !important;
        }
        
        .cookie-consent__message {
            font-size: 12px !important;
        }
    }
</style>

<!-- Customise Modal -->
<div id="cookie-customise-modal" class="cookie-customise-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 10000; overflow-y: auto; padding: 20px;">
    <div class="cookie-customise-content" style="background: #ffffff; margin: 30px auto; padding: 30px; max-width: 700px; border-radius: 12px; box-shadow: 0 4px 30px rgba(0,0,0,0.4); position: relative;">
        <button type="button" class="cookie-modal-close js-cookie-modal-close" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; font-size: 24px; color: #999; cursor: pointer; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease;">&times;</button>
        
        <h4 style="font-weight: 600; color: #333; margin-bottom: 15px; font-size: 20px;">{{ trans('cookie-consent::texts.customise') }}</h4>
        <p style="color: #666; font-size: 14px; margin-bottom: 25px; line-height: 1.6;">{{ trans('cookie-consent::texts.message') }}</p>
        
        <div class="cookie-category mb-3" style="padding: 18px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; background: #f9f9f9;">
            <div class="d-flex justify-content-between align-items-center">
                <div style="flex: 1;">
                    <strong style="color: #333; font-size: 16px; display: block; margin-bottom: 5px;">{{ trans('cookie-consent::texts.necessary') }}</strong>
                    <p style="font-size: 13px; color: #666; margin: 0; line-height: 1.5;">{{ trans('cookie-consent::texts.necessary_desc') }}</p>
                </div>
                <input type="checkbox" class="cookie-category-toggle" data-category="necessary" checked disabled style="width: 24px; height: 24px; cursor: not-allowed; margin-left: 15px; flex-shrink: 0;">
            </div>
        </div>
        
        <div class="cookie-category mb-3" style="padding: 18px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div style="flex: 1;">
                    <strong style="color: #333; font-size: 16px; display: block; margin-bottom: 5px;">{{ trans('cookie-consent::texts.analytics') }}</strong>
                    <p style="font-size: 13px; color: #666; margin: 0; line-height: 1.5;">{{ trans('cookie-consent::texts.analytics_desc') }}</p>
                </div>
                <input type="checkbox" class="cookie-category-toggle" data-category="analytics" style="width: 24px; height: 24px; cursor: pointer; margin-left: 15px; flex-shrink: 0;">
            </div>
        </div>
        
        <div class="cookie-category mb-3" style="padding: 18px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div style="flex: 1;">
                    <strong style="color: #333; font-size: 16px; display: block; margin-bottom: 5px;">{{ trans('cookie-consent::texts.marketing') }}</strong>
                    <p style="font-size: 13px; color: #666; margin: 0; line-height: 1.5;">{{ trans('cookie-consent::texts.marketing_desc') }}</p>
                </div>
                <input type="checkbox" class="cookie-category-toggle" data-category="marketing" style="width: 24px; height: 24px; cursor: pointer; margin-left: 15px; flex-shrink: 0;">
            </div>
        </div>
        
        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4" style="gap: 10px;">
            <button type="button" class="js-cookie-save-preferences cookie-save-btn" style="background: #28a745; border: 2px solid #28a745; color: #ffffff; padding: 10px 25px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; min-width: 120px; transition: all 0.3s ease;">
                Speichern
            </button>
        </div>
    </div>
</div>

<style>
    .cookie-modal-close:hover {
        background: #f0f0f0 !important;
        color: #333 !important;
    }
    
    .cookie-save-btn:hover {
        background: #218838 !important;
        border-color: #218838 !important;
        transform: translateY(-1px);
    }
    
    .cookie-category:hover {
        border-color: #0066cc !important;
    }
    
    @media (max-width: 768px) {
        .cookie-customise-content {
            margin: 20px auto !important;
            padding: 20px !important;
        }
        
        .cookie-category {
            padding: 15px !important;
        }
    }
</style>
