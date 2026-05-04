// Universal Emoji Picker Initialization Function
// Uses emoji-picker-element library for native emojis
function initEmojiPicker(selector) {
    const textarea = typeof selector === 'string' ? document.querySelector(selector) : selector;
    
    if (!textarea) {
        console.warn('Textarea not found for selector:', selector);
        return;
    }
    
    // Check if already initialized
    if (textarea.dataset.emojiPickerInit === 'true') {
        return;
    }
    
    // Mark as initialized
    textarea.dataset.emojiPickerInit = 'true';
    
    // Create wrapper if it doesn't exist
    let wrapper = textarea.parentElement;
    if (!wrapper.classList.contains('emoji-picker-wrapper')) {
        const newWrapper = document.createElement('div');
        newWrapper.className = 'emoji-picker-wrapper';
        newWrapper.style.position = 'relative';
        newWrapper.style.display = 'block';
        textarea.parentNode.insertBefore(newWrapper, textarea);
        newWrapper.appendChild(textarea);
        wrapper = newWrapper;
        
        // Add padding to textarea to make room for emoji button
        if (!textarea.style.paddingRight) {
            textarea.style.paddingRight = '45px';
        }
    }
    
    // Create emoji button with SVG icons (emoji and cross)
    const emojiButton = document.createElement('button');
    emojiButton.type = 'button';
    emojiButton.className = 'emoji-picker-button';
    
    // Emoji icon SVG
    const emojiIcon = document.createElement('span');
    emojiIcon.className = 'emoji-icon';
    emojiIcon.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5,12 C20.5375661,12 23,14.4624339 23,17.5 C23,20.5375661 20.5375661,23 17.5,23 C14.4624339,23 12,20.5375661 12,17.5 C12,14.4624339 14.4624339,12 17.5,12 Z M12.0000002,1.99896738 C17.523704,1.99896738 22.0015507,6.47681407 22.0015507,12.0005179 C22.0015507,12.2637452 21.9913819,12.5245975 21.9714157,12.7827034 C21.5335438,12.3671164 21.0376367,12.012094 20.4972374,11.7307716 C20.3551544,7.16057357 16.6051843,3.49896738 12.0000002,3.49896738 C7.30472352,3.49896738 3.49844971,7.30524119 3.49844971,12.0005179 C3.49844971,16.6060394 7.16059249,20.3562216 11.7317296,20.4979161 C12.0124658,21.0381559 12.3673338,21.5337732 12.7825138,21.9716342 C12.5247521,21.9918733 12.2635668,22.0020684 12.0000002,22.0020684 C6.47629639,22.0020684 1.99844971,17.5242217 1.99844971,12.0005179 C1.99844971,6.47681407 6.47629639,1.99896738 12.0000002,1.99896738 Z M17.5,13.9992349 L17.4101244,14.0072906 C17.2060313,14.0443345 17.0450996,14.2052662 17.0080557,14.4093593 L17,14.4992349 L16.9996498,16.9992349 L14.4976498,17 L14.4077742,17.0080557 C14.2036811,17.0450996 14.0427494,17.2060313 14.0057055,17.4101244 L13.9976498,17.5 L14.0057055,17.5898756 C14.0427494,17.7939687 14.2036811,17.9549004 14.4077742,17.9919443 L14.4976498,18 L17.0006498,17.9992349 L17.0011076,20.5034847 L17.0091633,20.5933603 C17.0462073,20.7974534 17.207139,20.9583851 17.411232,20.995429 L17.5011076,21.0034847 L17.5909833,20.995429 C17.7950763,20.9583851 17.956008,20.7974534 17.993052,20.5933603 L18.0011076,20.5034847 L18.0006498,17.9992349 L20.5045655,18 L20.5944411,17.9919443 C20.7985342,17.9549004 20.9594659,17.7939687 20.9965098,17.5898756 L21.0045655,17.5 L20.9965098,17.4101244 C20.9594659,17.2060313 20.7985342,17.0450996 20.5944411,17.0080557 L20.5045655,17 L17.9996498,16.9992349 L18,14.4992349 L17.9919443,14.4093593 C17.9549004,14.2052662 17.7939687,14.0443345 17.5898756,14.0072906 L17.5,13.9992349 Z M8.46174078,14.7838355 C9.12309331,15.6232213 10.0524954,16.1974014 11.0917655,16.4103066 C11.0312056,16.7638158 11,17.1282637 11,17.5 C11,17.6408778 11.0044818,17.7807089 11.0133105,17.9193584 C9.53812034,17.6766509 8.21128537,16.8896809 7.28351576,15.7121597 C7.02716611,15.3868018 7.08310832,14.9152347 7.40846617,14.6588851 C7.73382403,14.4025354 8.20539113,14.4584777 8.46174078,14.7838355 Z M9.00044779,8.75115873 C9.69041108,8.75115873 10.2497368,9.3104845 10.2497368,10.0004478 C10.2497368,10.6904111 9.69041108,11.2497368 9.00044779,11.2497368 C8.3104845,11.2497368 7.75115873,10.6904111 7.75115873,10.0004478 C7.75115873,9.3104845 8.3104845,8.75115873 9.00044779,8.75115873 Z M15.0004478,8.75115873 C15.6904111,8.75115873 16.2497368,9.3104845 16.2497368,10.0004478 C16.2497368,10.6904111 15.6904111,11.2497368 15.0004478,11.2497368 C14.3104845,11.2497368 13.7511587,10.6904111 13.7511587,10.0004478 C13.7511587,9.3104845 14.3104845,8.75115873 15.0004478,8.75115873 Z" fill="currentColor"/></svg>';
    emojiIcon.style.cssText = 'position: absolute; width: 20px; height: 20px; transition: opacity 0.3s ease, transform 0.3s ease; display: flex; align-items: center; justify-content: center;';
    
    // Cross icon SVG
    const crossIcon = document.createElement('span');
    crossIcon.className = 'cross-icon';
    crossIcon.innerHTML = '<svg width="20" height="20" viewBox="0 0 455 455" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M227.5,0C101.761,0,0,101.75,0,227.5C0,353.239,101.75,455,227.5,455C353.239,455,455,353.25,455,227.5C455.001,101.761,353.251,0,227.5,0z M227.5,425.001c-108.902,0-197.5-88.599-197.5-197.5S118.599,30,227.5,30S425,118.599,425,227.5S336.402,425.001,227.5,425.001z"/><path d="M321.366,133.635c-17.587-17.588-46.051-17.589-63.64,0L227.5,163.86l-30.226-30.225c-17.588-17.588-46.051-17.589-63.64,0c-17.544,17.545-17.544,46.094,0,63.64L163.86,227.5l-30.226,30.226c-17.544,17.545-17.544,46.094,0,63.64c17.585,17.586,46.052,17.589,63.64,0l30.226-30.225l30.226,30.225c17.585,17.586,46.052,17.589,63.64,0c17.544-17.545,17.544-46.094,0-63.64L291.141,227.5l30.226-30.226C338.911,179.729,338.911,151.181,321.366,133.635z M300.153,176.062l-40.832,40.832c-2.813,2.813-4.394,6.628-4.394,10.606c0,3.979,1.581,7.793,4.394,10.606l40.832,40.832c5.849,5.849,5.849,15.365,0,21.214c-5.862,5.862-15.351,5.863-21.214,0l-40.832-40.832c-2.929-2.929-6.768-4.394-10.606-4.394s-7.678,1.464-10.606,4.394l-40.832,40.832c-5.861,5.861-15.351,5.863-21.213,0c-5.849-5.849-5.849-15.365,0-21.214l40.832-40.832c2.813-2.813,4.394-6.628,4.394-10.606c0-3.978-1.581-7.793-4.394-10.606l-40.832-40.832c-5.849-5.849-5.849-15.365,0-21.214c5.864-5.863,15.35-5.863,21.214,0l40.832,40.832c5.857,5.858,15.355,5.858,21.213,0l40.832-40.832c5.863-5.862,15.35-5.863,21.213,0C306.001,160.697,306.001,170.213,300.153,176.062z"/></svg>';
    crossIcon.style.cssText = 'position: absolute; width: 20px; height: 20px; opacity: 0; transform: rotate(-90deg); transition: opacity 0.3s ease, transform 0.3s ease; pointer-events: none; display: flex; align-items: center; justify-content: center;';
    
    emojiButton.appendChild(emojiIcon);
    emojiButton.appendChild(crossIcon);
    
    emojiButton.style.cssText = 'position: absolute; right: 10px; top: 10px; background: #fff; border: none; cursor: pointer; z-index: 10; padding: 0; line-height: 1; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; transition: all 0.2s ease;';
    emojiButton.title = 'Emoji hinzufügen';
    
    // Create emoji picker element
    const picker = document.createElement('emoji-picker');
    picker.style.cssText = 'position: absolute; bottom: 100%; right: 0; margin-bottom: 5px; z-index: 1000; display: none; width: 352px; max-width: 90vw;';
    picker.setAttribute('class', 'emoji-picker-element');
    
    // Ensure picker is properly initialized
    picker.setAttribute('locale', 'de'); // Set locale if needed
    
    wrapper.appendChild(emojiButton);
    wrapper.appendChild(picker);
    
    // Wait for custom element to be defined and ready
    function attachEmojiListener() {
        // Insert emoji into textarea at cursor position
        picker.addEventListener('emoji-click', function(event) {
            // console.log('Emoji clicked:', event.detail); // Debug log
            
            // Try multiple possible properties for emoji value
            let emoji = '';
            if (event.detail) {
                emoji = event.detail.unicode || 
                       event.detail.emoji || 
                       event.detail.native || 
                       (event.detail.emojiData && event.detail.emojiData.unicode) ||
                       (event.detail.emoji && event.detail.emoji.unicode) ||
                       '';
            }
            
            if (!emoji) {
                console.error('Emoji value not found in event.detail:', event.detail);
                // Try to get emoji from the clicked element
                if (event.target && event.target.textContent) {
                    emoji = event.target.textContent.trim();
                    console.log('Trying emoji from target:', emoji);
                }
                if (!emoji) {
                    return;
                }
            }
            
            // Get current cursor position - ensure textarea is focused
            if (document.activeElement !== textarea) {
                textarea.focus();
            }
            
            const cursorPos = textarea.selectionStart !== null ? textarea.selectionStart : textarea.value.length;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(textarea.selectionEnd !== null ? textarea.selectionEnd : cursorPos);
            
            // Insert emoji at cursor position
            textarea.value = textBefore + emoji + textAfter;
            
            // Set cursor position after emoji
            const newCursorPos = cursorPos + emoji.length;
            setTimeout(function() {
                textarea.setSelectionRange(newCursorPos, newCursorPos);
                textarea.focus();
            }, 10);
            
            // Trigger input event for any listeners
            const inputEvent = new Event('input', { bubbles: true, cancelable: true });
            textarea.dispatchEvent(inputEvent);
            
            // Also trigger change event
            const changeEvent = new Event('change', { bubbles: true, cancelable: true });
            textarea.dispatchEvent(changeEvent);
        });
    }
    
    // Wait for picker to be fully loaded before attaching event listener
    // Custom elements might need time to initialize
    try {
        if (typeof customElements !== 'undefined' && customElements.get('emoji-picker')) {
            // Element is already defined, attach immediately
            attachEmojiListener();
        } else if (typeof customElements !== 'undefined' && customElements.whenDefined) {
            // Wait for element to be defined
            customElements.whenDefined('emoji-picker').then(function() {
                attachEmojiListener();
            }).catch(function() {
                // If customElements.whenDefined fails, try after a delay
                setTimeout(function() {
                    attachEmojiListener();
                }, 200);
            });
        } else {
            // Fallback: attach after a short delay
            setTimeout(function() {
                attachEmojiListener();
            }, 100);
        }
    } catch (e) {
        // Fallback: attach after a short delay
        console.warn('Error checking custom element:', e);
        setTimeout(function() {
            attachEmojiListener();
        }, 100);
    }
    
    // Function to update icon based on picker state
    function updateIcon(isOpen) {
        if (isOpen) {
            // Show cross, hide emoji
            emojiIcon.style.opacity = '0';
            emojiIcon.style.transform = 'rotate(90deg)';
            crossIcon.style.opacity = '1';
            crossIcon.style.transform = 'rotate(0deg)';
            crossIcon.style.pointerEvents = 'auto';
            emojiButton.title = 'Emoji schließen';
        } else {
            // Show emoji, hide cross
            emojiIcon.style.opacity = '1';
            emojiIcon.style.transform = 'rotate(0deg)';
            crossIcon.style.opacity = '0';
            crossIcon.style.transform = 'rotate(-90deg)';
            crossIcon.style.pointerEvents = 'none';
            emojiButton.title = 'Emoji hinzufügen';
        }
    }
    
    // Add hover effect
    emojiButton.addEventListener('mouseenter', function() {
        this.style.background = '#f5f5f5';
        this.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';
    });
    emojiButton.addEventListener('mouseleave', function() {
        this.style.background = '#fff';
        this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
    });
    
    // Toggle picker visibility
    emojiButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const isVisible = picker.style.display === 'block';
        picker.style.display = isVisible ? 'none' : 'block';
        updateIcon(!isVisible);
    });
    
    // Hide picker when clicking outside
    document.addEventListener('click', function(event) {
        if (!wrapper.contains(event.target) && event.target !== emojiButton && !emojiButton.contains(event.target)) {
            picker.style.display = 'none';
            updateIcon(false);
        }
    });
    
    // Hide picker on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && picker.style.display === 'block') {
            picker.style.display = 'none';
            updateIcon(false);
            textarea.focus();
        }
    });
}

// Auto-initialize on page load for elements with data-emoji-picker attribute
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-emoji-picker="true"]').forEach(function(element) {
        initEmojiPicker(element);
    });
});
