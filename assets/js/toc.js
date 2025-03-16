/**
 * Simple and robust Table of Contents implementation
 */
(function($) {
    'use strict';
    
    // Wait for DOM ready
    $(document).ready(function() {
        initTOC();
    });
    
    function initTOC() {
        // Find TOC elements
        const $tocToggle = $('.custom-theme-toc-toggle');
        const $tocList = $('.custom-theme-toc-list-container');
        const $tocLinks = $('.custom-theme-toc-link');
        
        // Exit if elements aren't found
        if (!$tocToggle.length || !$tocList.length) {
            return;
        }
        
        // TOGGLE FUNCTIONALITY
        // Set initial state
        $tocToggle.attr('aria-expanded', 'true');
        
        // Check for saved state in session storage
        if (sessionStorage.getItem('tocCollapsed') === 'true') {
            $tocList.hide();
            $tocToggle.attr('aria-expanded', 'false');
        }
        
        // Add click handler
        $tocToggle.on('click', function() {
            console.log('TOC toggle clicked'); // For debugging
            
            // Get current state
            const isExpanded = $(this).attr('aria-expanded') === 'true';
            
            // Toggle state
            if (isExpanded) {
                $(this).attr('aria-expanded', 'false');
                $tocList.slideUp(300);
                sessionStorage.setItem('tocCollapsed', 'true');
                console.log('TOC collapsed'); // For debugging
            } else {
                $(this).attr('aria-expanded', 'true');
                $tocList.slideDown(300);
                sessionStorage.removeItem('tocCollapsed');
                console.log('TOC expanded'); // For debugging
            }
        });
        
        // SMOOTH SCROLLING
        $tocLinks.on('click', function(e) {
            e.preventDefault();
            
            const targetId = $(this).attr('href');
            const $target = $(targetId);
            
            if ($target.length) {
                // Calculate scroll position
                const headerHeight = $('#masthead').outerHeight() || 0;
                const scrollPosition = $target.offset().top - headerHeight - 20;
                
                // Scroll to target
                $('html, body').animate({
                    scrollTop: scrollPosition
                }, 400, function() {
                    // Update URL without causing a page jump
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                    
                    // Set focus to the heading for accessibility
                    $target.attr('tabindex', '-1').focus();
                    setTimeout(function() {
                        $target.removeAttr('tabindex');
                    }, 100);
                });
            }
        });
        
        // Handle initial hash in URL
        if (window.location.hash) {
            const hash = window.location.hash;
            const $target = $(hash);
            
            if ($target.length) {
                // Wait for page to be ready
                setTimeout(function() {
                    // Calculate scroll position
                    const headerHeight = $('#masthead').outerHeight() || 0;
                    const scrollPosition = $target.offset().top - headerHeight - 20;
                    
                    // Scroll to target
                    $('html, body').animate({
                        scrollTop: scrollPosition
                    }, 400);
                    
                    // Set active class on the TOC link
                    $tocLinks.removeClass('custom-theme-toc-active');
                    $tocLinks.filter(`[href="${hash}"]`).addClass('custom-theme-toc-active');
                }, 300);
            }
        }
    }
})(jQuery);