@if($cookieConsentConfig['enabled'] && ! $alreadyConsentedWithCookies)

    @include('cookie-consent::dialogContents')

    <script>

        window.laravelCookieConsent = (function () {

            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';
            const COOKIE_PREFERENCES_NAME = '{{ $cookieConsentConfig['cookie_name'] }}_preferences';

            function consentWithCookies(allCategories = true, preferences = {}) {
                // Set main consent cookie
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_VALUE, {{ $cookieConsentConfig['cookie_lifetime'] }});
                
                // Set preferences cookie
                if (allCategories) {
                    // Accept all - enable all categories
                    const allPreferences = {
                        necessary: true,
                        analytics: true,
                        marketing: true
                    };
                    setCookie(COOKIE_PREFERENCES_NAME, JSON.stringify(allPreferences), {{ $cookieConsentConfig['cookie_lifetime'] }});
                } else {
                    // Custom preferences
                    const customPreferences = {
                        necessary: true, // Always true
                        analytics: preferences.analytics || false,
                        marketing: preferences.marketing || false
                    };
                    setCookie(COOKIE_PREFERENCES_NAME, JSON.stringify(customPreferences), {{ $cookieConsentConfig['cookie_lifetime'] }});
                }
                
                hideCookieDialog();
                hideCustomiseModal();
            }

            function rejectAllCookies() {
                // Set main consent cookie with reject value
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', 'rejected', {{ $cookieConsentConfig['cookie_lifetime'] }});
                
                // Set preferences - only necessary cookies
                const rejectPreferences = {
                    necessary: true,
                    analytics: false,
                    marketing: false
                };
                setCookie(COOKIE_PREFERENCES_NAME, JSON.stringify(rejectPreferences), {{ $cookieConsentConfig['cookie_lifetime'] }});
                
                // Hide dialog immediately
                hideCookieDialog();
                hideCustomiseModal();
                
                // Prevent any analytics/marketing scripts from running
                console.log('Cookies rejected - only necessary cookies enabled');
            }

            function cookieExists(name) {
                const cookies = document.cookie.split('; ');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].split('=');
                    if (cookie[0].trim() === name && cookie[1] && cookie[1] !== 'rejected') {
                        return true;
                    }
                }
                return false;
            }
            
            function hasConsented() {
                const cookies = document.cookie.split('; ');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].split('=');
                    if (cookie[0].trim() === '{{ $cookieConsentConfig['cookie_name'] }}') {
                        const value = cookie[1] ? cookie[1].trim() : '';
                        // Return true if user has accepted (value = '1') OR rejected (value = 'rejected')
                        // This ensures dialog doesn't show again after user makes a choice
                        return value === '1' || value === COOKIE_VALUE.toString() || value === 'rejected';
                    }
                }
                return false;
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');
                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function showCustomiseModal() {
                const modal = document.getElementById('cookie-customise-modal');
                if (modal) {
                    modal.style.display = 'block';
                }
            }

            function hideCustomiseModal() {
                const modal = document.getElementById('cookie-customise-modal');
                if (modal) {
                    modal.style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value
                    + ';expires=' + date.toUTCString()
                    + ';domain=' + COOKIE_DOMAIN
                    + ';path=/{{ config('session.secure') ? ';secure' : null }}'
                    + '{{ config('session.same_site') ? ';samesite='.config('session.same_site') : null }}';
            }

            function getCookiePreferences() {
                const cookies = document.cookie.split('; ');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].split('=');
                    if (cookie[0] === COOKIE_PREFERENCES_NAME && cookie[1]) {
                        try {
                            return JSON.parse(decodeURIComponent(cookie[1]));
                        } catch (e) {
                            return null;
                        }
                    }
                }
                return null;
            }

            // Set up event listeners when DOM is ready
            function setupEventListeners() {
                console.log('Setting up cookie consent event listeners...');
                
                // Use event delegation on document for better reliability
                document.addEventListener('click', function(e) {
                    // Handle Accept All button
                    if (e.target && e.target.classList.contains('js-cookie-consent-agree')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Accept All clicked');
                        consentWithCookies(true);
                        return false;
                    }
                    
                    // Handle Reject All button
                    if (e.target && e.target.classList.contains('js-cookie-consent-reject')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Reject All clicked');
                        rejectAllCookies();
                        return false;
                    }
                    
                    // Handle Customise button
                    if (e.target && e.target.classList.contains('js-cookie-consent-customise')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Customise clicked');
                        showCustomiseModal();
                        return false;
                    }
                    
                    // Handle Save preferences button
                    if (e.target && e.target.classList.contains('js-cookie-save-preferences')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const analyticsInput = document.querySelector('input[data-category="analytics"]');
                        const marketingInput = document.querySelector('input[data-category="marketing"]');
                        
                        if (analyticsInput && marketingInput) {
                            consentWithCookies(false, {
                                analytics: analyticsInput.checked,
                                marketing: marketingInput.checked
                            });
                        }
                        return false;
                    }
                });

                // Also set up direct event listeners as backup
                const agreeButtons = document.getElementsByClassName('js-cookie-consent-agree');
                console.log('Found agree buttons:', agreeButtons.length);
                for (let i = 0; i < agreeButtons.length; ++i) {
                    agreeButtons[i].onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Accept All clicked (direct)');
                        consentWithCookies(true);
                        return false;
                    };
                }

                const rejectButtons = document.getElementsByClassName('js-cookie-consent-reject');
                console.log('Found reject buttons:', rejectButtons.length);
                for (let i = 0; i < rejectButtons.length; ++i) {
                    rejectButtons[i].onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Reject All clicked (direct)');
                        rejectAllCookies();
                        return false;
                    };
                }

                const customiseButtons = document.getElementsByClassName('js-cookie-consent-customise');
                console.log('Found customise buttons:', customiseButtons.length);
                for (let i = 0; i < customiseButtons.length; ++i) {
                    customiseButtons[i].onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Customise clicked (direct)');
                        showCustomiseModal();
                        return false;
                    };
                }

                // Save preferences button
                const saveButtons = document.getElementsByClassName('js-cookie-save-preferences');
                for (let i = 0; i < saveButtons.length; ++i) {
                    saveButtons[i].onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const analyticsInput = document.querySelector('input[data-category="analytics"]');
                        const marketingInput = document.querySelector('input[data-category="marketing"]');
                        
                        if (analyticsInput && marketingInput) {
                            consentWithCookies(false, {
                                analytics: analyticsInput.checked,
                                marketing: marketingInput.checked
                            });
                        }
                        return false;
                    };
                }

                    // Close modal on background click
                    const modal = document.getElementById('cookie-customise-modal');
                    if (modal) {
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                hideCustomiseModal();
                            }
                        });
                    }
                    
                    // Close modal button
                    const closeButtons = document.getElementsByClassName('js-cookie-modal-close');
                    for (let i = 0; i < closeButtons.length; ++i) {
                        closeButtons[i].onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            hideCustomiseModal();
                            return false;
                        };
                    }
                }

            // Check if user has already consented
            if (hasConsented()) {
                console.log('User has already consented, hiding dialog');
                hideCookieDialog();
            } else {
                console.log('User has not consented, setting up event listeners');
                // Wait for DOM to be ready, then set up event listeners
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(setupEventListeners, 50);
                    });
                } else {
                    // DOM is already ready
                    setTimeout(setupEventListeners, 50);
                }
            }

            const consentAPI = {
                consentWithCookies: consentWithCookies,
                rejectAllCookies: rejectAllCookies,
                hideCookieDialog: hideCookieDialog,
                hideCustomiseModal: hideCustomiseModal,
                getCookiePreferences: getCookiePreferences
            };
            
            return consentAPI;
        })();
    </script>

@endif
