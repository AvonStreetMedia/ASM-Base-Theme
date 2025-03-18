/**
 * Schema Admin JavaScript
 * Enhances the schema markup metabox in the post editor
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        const $schemaType = $('#custom_theme_schema_type');
        const $customFields = $('#schema-custom-fields');
        const $customJson = $('#custom_theme_schema_custom');
        
        // Toggle custom fields based on schema type
        $schemaType.on('change', function() {
            if ($(this).val() === 'none') {
                $customFields.hide();
            } else {
                $customFields.show();
                updateJsonTemplate($(this).val());
            }
        });
        
        // Add JSON template helpers
        function updateJsonTemplate(schemaType) {
            // Only add template if field is empty
            if ($customJson.val().trim() === '') {
                const template = getSchemaTemplate(schemaType);
                if (template) {
                    $customJson.val(template);
                }
            }
        }
        
        // Get schema template based on type
        function getSchemaTemplate(type) {
            let template = '';
            
            switch(type) {
                case 'Article':
                case 'BlogPosting':
                case 'NewsArticle':
                    template = '{\n' +
                        '  "author": {\n' +
                        '    "@type": "Person",\n' +
                        '    "name": "Author Name"\n' +
                        '  },\n' +
                        '  "keywords": "keyword1, keyword2, keyword3",\n' +
                        '  "headline": "Your headline here (max 110 characters)"\n' +
                        '}';
                    break;
                    
                case 'Product':
                    template = '{\n' +
                        '  "brand": {\n' +
                        '    "@type": "Brand",\n' +
                        '    "name": "Brand Name"\n' +
                        '  },\n' +
                        '  "offers": {\n' +
                        '    "@type": "Offer",\n' +
                        '    "price": "49.99",\n' +
                        '    "priceCurrency": "USD",\n' +
                        '    "availability": "https://schema.org/InStock"\n' +
                        '  }\n' +
                        '}';
                    break;
                    
                case 'Recipe':
                    template = '{\n' +
                        '  "recipeIngredient": [\n' +
                        '    "Ingredient 1",\n' +
                        '    "Ingredient 2",\n' +
                        '    "Ingredient 3"\n' +
                        '  ],\n' +
                        '  "recipeInstructions": [\n' +
                        '    {\n' +
                        '      "@type": "HowToStep",\n' +
                        '      "text": "Step 1 instructions"\n' +
                        '    },\n' +
                        '    {\n' +
                        '      "@type": "HowToStep",\n' +
                        '      "text": "Step 2 instructions"\n' +
                        '    }\n' +
                        '  ],\n' +
                        '  "recipeYield": "4 servings",\n' +
                        '  "cookTime": "PT30M",\n' +
                        '  "totalTime": "PT45M"\n' +
                        '}';
                    break;
                    
                case 'FAQPage':
                    template = '{\n' +
                        '  "mainEntity": [\n' +
                        '    {\n' +
                        '      "@type": "Question",\n' +
                        '      "name": "Question 1",\n' +
                        '      "acceptedAnswer": {\n' +
                        '        "@type": "Answer",\n' +
                        '        "text": "Answer to question 1"\n' +
                        '      }\n' +
                        '    },\n' +
                        '    {\n' +
                        '      "@type": "Question",\n' +
                        '      "name": "Question 2",\n' +
                        '      "acceptedAnswer": {\n' +
                        '        "@type": "Answer",\n' +
                        '        "text": "Answer to question 2"\n' +
                        '      }\n' +
                        '    }\n' +
                        '  ]\n' +
                        '}';
                    break;
                    
                case 'HowTo':
                    template = '{\n' +
                        '  "step": [\n' +
                        '    {\n' +
                        '      "@type": "HowToStep",\n' +
                        '      "text": "Step 1 instructions"\n' +
                        '    },\n' +
                        '    {\n' +
                        '      "@type": "HowToStep",\n' +
                        '      "text": "Step 2 instructions"\n' +
                        '    }\n' +
                        '  ],\n' +
                        '  "supply": [\n' +
                        '    {\n' +
                        '      "@type": "HowToSupply",\n' +
                        '      "name": "Supply 1"\n' +
                        '    },\n' +
                        '    {\n' +
                        '      "@type": "HowToSupply",\n' +
                        '      "name": "Supply 2"\n' +
                        '    }\n' +
                        '  ],\n' +
                        '  "totalTime": "PT2H"\n' +
                        '}';
                    break;
                    
                case 'LocalBusiness':
                    template = '{\n' +
                        '  "address": {\n' +
                        '    "@type": "PostalAddress",\n' +
                        '    "streetAddress": "123 Main St",\n' +
                        '    "addressLocality": "City",\n' +
                        '    "addressRegion": "State",\n' +
                        '    "postalCode": "12345",\n' +
                        '    "addressCountry": "US"\n' +
                        '  },\n' +
                        '  "telephone": "+11234567890",\n' +
                        '  "openingHours": "Mo,Tu,We,Th,Fr 09:00-17:00",\n' +
                        '  "priceRange": "$$"\n' +
                        '}';
                    break;
                    
                case 'Event':
                    template = '{\n' +
                        '  "startDate": "2023-12-31T19:00:00-08:00",\n' +
                        '  "endDate": "2024-01-01T01:00:00-08:00",\n' +
                        '  "location": {\n' +
                        '    "@type": "Place",\n' +
                        '    "name": "Event Venue",\n' +
                        '    "address": {\n' +
                        '      "@type": "PostalAddress",\n' +
                        '      "streetAddress": "123 Main St",\n' +
                        '      "addressLocality": "City",\n' +
                        '      "addressRegion": "State",\n' +
                        '      "postalCode": "12345",\n' +
                        '      "addressCountry": "US"\n' +
                        '    }\n' +
                        '  },\n' +
                        '  "offers": {\n' +
                        '    "@type": "Offer",\n' +
                        '    "price": "25.00",\n' +
                        '    "priceCurrency": "USD",\n' +
                        '    "url": "https://example.com/tickets",\n' +
                        '    "availability": "https://schema.org/InStock"\n' +
                        '  }\n' +
                        '}';
                    break;
                    
                default:
                    // For other types, provide a simple template
                    template = '{\n' +
                        '  "description": "Add your custom properties here"\n' +
                        '}';
            }
            
            return template;
        }
        
        // Format JSON button
        const $formatButton = $('<button type="button" class="button" id="format-json-button">Format JSON</button>');
        $customJson.after($formatButton);
        
        // Format JSON when button is clicked
        $formatButton.on('click', function(e) {
            e.preventDefault();
            
            const jsonText = $customJson.val().trim();
            if (jsonText === '') {
                return;
            }
            
            try {
                const jsonObj = JSON.parse(jsonText);
                const formattedJson = JSON.stringify(jsonObj, null, 2);
                $customJson.val(formattedJson);
            } catch(error) {
                alert('Invalid JSON: ' + error.message);
            }
        });
    });
})(jQuery);