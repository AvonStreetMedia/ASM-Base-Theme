/**
 * Custom JavaScript for the theme
 */

// Wait for the DOM to be loaded
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements for better performance
    const menuToggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.main-navigation');
    
    // Mobile menu toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navigation.classList.toggle('toggled');
            if (navigation.classList.contains('toggled')) {
                menuToggle.setAttribute('aria-expanded', 'true');
            } else {
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // Add smooth scrolling to all links
    const links = document.querySelectorAll('a[href*="#"]:not([href="#"])');
    
    for (const link of links) {
        link.addEventListener('click', function(e) {
            // Only prevent default if the link is on the same page
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
                location.hostname === this.hostname) {
                
                // Figure out element to scroll to
                const target = document.querySelector(this.hash);
                if (target) {
                    e.preventDefault();
                    window.scrollTo({
                        top: target.offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Update the URL hash
                    history.pushState(null, null, this.hash);
                    
                    // Add focus to the target for accessibility
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                }
            }
        });
    }
});
