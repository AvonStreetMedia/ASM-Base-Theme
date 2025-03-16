/**
 * Schema Validator JavaScript
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        const $contentSelector = $('#schema-content-selector');
        const $validateButton = $('#validate-schema-button');
        const $resultsContainer = $('#schema-validation-results');
        const $resultsContent = $('#schema-validation-content');
        
        // Handle validation button click
        $validateButton.on('click', function() {
            const postId = $contentSelector.val();
            
            if (!postId) {
                alert('Please select a post or page to validate.');
                return;
            }
            
            validateSchema(postId);
        });
        
        /**
         * Validate schema for a specific post
         */
        function validateSchema(postId) {
            // Show loading state
            $resultsContainer.show();
            $resultsContent.html('<p class="loading">' + schemaValidatorData.loading + '</p>');
            
            // Send AJAX request
            $.ajax({
                url: schemaValidatorData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'validate_schema',
                    post_id: postId,
                    nonce: schemaValidatorData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderResults(response.data);
                    } else {
                        $resultsContent.html('<p class="error">' + response.data.message + '</p>');
                    }
                },
                error: function() {
                    $resultsContent.html('<p class="error">' + schemaValidatorData.error + '</p>');
                }
            });
        }
        
        /**
         * Render validation results
         */
        function renderResults(data) {
            let html = '';
            
            // Content info
            html += '<div class="schema-content-info">';
            html += '<h3>' + data.title + '</h3>';
            html += '<p><strong>URL:</strong> <a href="' + data.permalink + '" target="_blank">' + data.permalink + '</a></p>';
            html += '<p><strong>Schema Type:</strong> ' + data.schema_type + '</p>';
            html += '</div>';
            
            // Validation results
            html += '<div class="schema-validation-status">';
            
            if (data.validation.status === 'success') {
                html += '<div class="notice notice-success"><p>All schema requirements passed!</p></div>';
            } else if (data.validation.status === 'warning') {
                html += '<div class="notice notice-warning"><p>Schema validation completed with warnings.</p></div>';
            } else {
                html += '<div class="notice notice-error"><p>Schema validation failed with errors.</p></div>';
            }
            
            // Issues list
            if (data.validation.issues.length > 0) {
                html += '<div class="schema-issues">';
                html += '<h4>Issues Found:</h4>';
                html += '<ul>';
                
                data.validation.issues.forEach(function(issue) {
                    html += '<li>' + issue + '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
            }
            
            // Success list
            if (data.validation.success.length > 0) {
                html += '<div class="schema-successes">';
                html += '<h4>Valid Properties:</h4>';
                html += '<ul>';
                
                data.validation.success.forEach(function(success) {
                    html += '<li>' + success + '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
            }
            
            html += '</div>';
            
            // Recommendations
            if (data.recommendations.length > 0) {
                html += '<div class="schema-recommendations">';
                html += '<h4>Recommendations:</h4>';
                html += '<ul>';
                
                data.recommendations.forEach(function(recommendation) {
                    html += '<li>' + recommendation + '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
            }
            
            // Schema JSON
            html += '<div class="schema-json">';
            html += '<h4>Schema JSON:</h4>';
            html += '<pre>' + syntaxHighlight(data.schema_json) + '</pre>';
            html += '</div>';
            
            // Update results container
            $resultsContent.html(html);
        }
        
        /**
         * Syntax highlight JSON for better readability
         */
        function syntaxHighlight(json) {
            if (typeof json !== 'string') {
                json = JSON.stringify(json, undefined, 2);
            }
            
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
                let cls = 'number';
                
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }
    });
})(jQuery);
