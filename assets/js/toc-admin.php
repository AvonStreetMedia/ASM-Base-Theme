/**
 * Table of Contents Admin JavaScript
 * Handles TOC interactions in the WordPress editor
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Target editor TOC if present
        const $editorToc = $('.block-editor-table-of-contents');
        
        if ($editorToc.length) {
            // Fix issue with editor TOC toggle buttons
            const fixEditorTocButtons = function() {
                // Find TOC toggle buttons in the editor
                const $tocButtons = $('.block-editor-table-of-contents__popover button');
                
                $tocButtons.each(function() {
                    const $button = $(this);
                    
                    // Remove any problematic click handlers
                    $button.off('click.wp-toc-disappear');
                    
                    // Add our own click handler if needed
                    if (!$button.data('custom-handler-added')) {
                        $button.on('click.custom-toc', function(e) {
                            // Make sure clicking the button doesn't cause the TOC to disappear
                            e.stopPropagation();
                        });
                        
                        $button.data('custom-handler-added', true);
                    }
                });
            };
            
            // Apply fix immediately and whenever the editor might re-render
            fixEditorTocButtons();
            
            // Monitor for changes to the editor that might affect the TOC
            const observer = new MutationObserver(function(mutations) {
                for (const mutation of mutations) {
                    if (mutation.type === 'childList' || mutation.type === 'attributes') {
                        fixEditorTocButtons();
                    }
                }
            });
            
            // Start observing the editor area
            observer.observe(document.querySelector('.edit-post-layout__content'), {
                childList: true,
                subtree: true,
                attributes: true
            });
        }
    });
    
})(jQuery);
