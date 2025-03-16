/**
 * Table of Contents JavaScript
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initTableOfContents();
    });
    
    /**
     * Initialize Table of Contents functionality
     */
    function initTableOfContents() {
        const $tocContainer = $('.custom-theme-toc-container');
        
        // If no TOC exists, exit
        if ($tocContainer.length === 0) {
            return;
        }
        
        const $tocToggle = $('.custom-theme-toc-toggle');
        const $tocListContainer = $('.custom-theme-toc-list-container');
        const $tocLinks = $('.custom-theme-toc-link');
        const $headings = $('h2, h3, h4');
        
        // Set up toggle functionality
        $tocToggle.on('click', function() {
            const isExpanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !isExpanded);
            
            if (isExpanded) {
                // Store state in sessionStorage to persist during page navigation
                sessionStorage.setItem('tocCollapsed', 'true');
            } else {
                sessionStorage.removeItem('tocCollapsed');
            }
        });
        
        // Check if TOC was previously collapsed
        if (sessionStorage.getItem('tocCollapsed')) {
            $tocToggle.attr('aria-expanded', false);
        }
        
        // Add smooth scrolling to TOC links
        $tocLinks.on('click', function(e) {
            e.preventDefault();
            
            const targetId = $(this).attr('href');
            const $target = $(targetId);
            
            if ($target.length) {
                // Calculate offset for fixed headers if any
                const headerHeight = $('#masthead').outerHeight() || 0;
                const scrollPosition = $target.offset().top - headerHeight - 20;
                
                $('html, body').animate({
                    scrollTop: scrollPosition
                }, 600, 'swing', function() {
                    // After scrolling, set focus to the heading for accessibility
                    $target.attr('tabindex', '-1').focus();
                    
                    // Remove tabindex after focus to prevent keyboard navigation issues
                    setTimeout(function() {
                        $target.removeAttr('tabindex');
                    }, 100);
                });
                
                // Update URL hash without scrolling
                history.pushState(null, null, targetId);
            }
        });
        
        // Highlight TOC items on scroll
        let headingPositions = [];
        
        // Calculate heading positions
        function calculateHeadingPositions() {
            headingPositions = [];
            const headerHeight = $('#masthead').outerHeight() || 0;
            
            $headings.each(function() {
                const id = $(this).attr('id');
                if (id) {
                    headingPositions.push({
                        id: id,
                        position: $(this).offset().top - headerHeight - 30
                    });
                }
            });
        }
        
        // Call initially and on window resize
        calculateHeadingPositions();
        $(window).on('resize', debounce(calculateHeadingPositions, 150));
        
        // Check which heading is in view on scroll
        $(window).on('scroll', debounce(function() {
            const scrollPosition = $(window).scrollTop();
            
            // Find the current heading
            let currentHeadingId = null;
            
            // Iterate backwards to find the heading above our scroll position
            for (let i = headingPositions.length - 1; i >= 0; i--) {
                if (scrollPosition >= headingPositions[i].position) {
                    currentHeadingId = headingPositions[i].id;
                    break;
                }
            }
            
            // If we're at the top of the page and no heading is in view
            if (scrollPosition < headingPositions[0]?.position) {
                currentHeadingId = headingPositions[0]?.id;
            }
            
            // Update active class on TOC links
            $tocLinks.removeClass('custom-theme-toc-active');
            
            if (currentHeadingId) {
                $(`.custom-theme-toc-link[href="#${currentHeadingId}"]`).addClass('custom-theme-toc-active');
            }
        }, 100));
        
        // Handle initial hash in URL
        if (window.location.hash) {
            const hash = window.location.hash;
            const $targetHeading = $(hash);
            
            if ($targetHeading.length) {
                // Scroll after a slight delay to ensure page is fully loaded
                setTimeout(function() {
                    const headerHeight = $('#masthead').outerHeight() || 0;
                    const scrollPosition = $targetHeading.offset().top - headerHeight - 20;
                    
                    $('html, body').animate({
                        scrollTop: scrollPosition
                    }, 400);
                    
                    // Update active TOC item
                    $tocLinks.removeClass('custom-theme-toc-active');
                    $(`.custom-theme-toc-link[href="${hash}"]`).addClass('custom-theme-toc-active');
                }, 300);
            }
        }
    }
    
    /**
     * Debounce function to limit how often a function is called
     *
     * @param {Function} func The function to debounce
     * @param {number} wait The time to wait in milliseconds
     * @return {Function} Debounced function
     */
    function debounce(func, wait) {
        let timeout;
        
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
})(jQuery);
