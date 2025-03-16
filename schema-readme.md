# Schema Markup for WordPress Themes

A comprehensive Schema.org markup implementation for WordPress themes, focused on SEO optimization and structured data best practices.

## Overview

This feature enhances your WordPress theme with advanced schema markup capabilities, helping search engines better understand your content and potentially improving your search engine rankings and visibility through rich results.

![Schema Markup Settings](schema-settings-screenshot.jpg)

## Features

- **Automatic Schema Generation**: Generates appropriate schema based on content type
- **20+ Schema Types**: Support for Articles, Products, Recipes, Local Businesses, Events, and more
- **Customizer Integration**: Global settings in the WordPress Customizer
- **Per-Content Settings**: Individual schema controls for each post and page
- **Schema Validator**: Built-in tool to validate schema against best practices
- **Advanced Customization**: JSON editor for custom schema properties
- **SEO Best Practices**: Follows Google's structured data guidelines

## Installation

1. Copy the following files to your theme:
   - `inc/schema-functions.php`
   - `assets/js/schema-validator.js`
   - `assets/js/schema-admin.js`
   - `assets/css/schema-validator.css`

2. Add the following line to the end of your `functions.php` file:
   ```php
   // Include schema markup functionality
   require get_template_directory() . '/inc/schema-functions.php';
   ```

3. Activate your theme and configure the schema settings.

## Usage

### Global Settings

1. Go to **Appearance > Customize > Schema Markup Settings**
2. Choose whether your website represents an Organization or Person
3. Fill in the required information:
   - For Organization: name, logo, social profiles
   - For Person: name, image, social profiles
4. Set default schema types for posts and pages
5. Save your changes

![Customizer Settings](schema-customizer-screenshot.jpg)

### Individual Content Settings

1. When editing a post or page, find the **Schema Markup Settings** meta box
2. Select the appropriate schema type for your content
3. For advanced users, add custom schema properties using the JSON editor
4. Publish or update your content

The schema meta box provides templates for different schema types, making it easier to add the required properties.

![Schema Meta Box](schema-metabox-screenshot.jpg)

### Schema Validation

1. Go to **Tools > Schema Validator**
2. Select any post or page to validate its schema markup
3. Review validation results, including:
   - Required properties check
   - Recommended properties check
   - Improvement suggestions
   - Formatted JSON preview

![Schema Validator](schema-validator-screenshot.jpg)

## Supported Schema Types

The following schema types are supported:

- Article
- BlogPosting
- NewsArticle
- Product
- Recipe
- Review
- FAQPage
- HowTo
- LocalBusiness
- Event
- Service
- Person
- Organization
- WebPage
- CollectionPage
- ItemPage
- AboutPage
- ContactPage
- ProfilePage

## Best Practices

This implementation follows these schema markup best practices:

1. **Use Specific Types**: Always use the most specific schema type for your content
2. **Include Required Properties**: Each schema type has specific required properties
3. **Add Rich Media**: Include images, videos, and other media when relevant
4. **Breadcrumb Navigation**: Automatically adds breadcrumb schema for better navigation
5. **Consistent Entity Representation**: Properly represent your website entity
6. **Validate Your Markup**: Regularly check your schema for errors and improvements

## Advanced Customization

For developers who want to extend this functionality:

- Add new schema types by modifying the `$schema_types` array in `Custom_Theme_Schema` class
- Define required and recommended properties for new types in `get_required_properties()` and `get_recommended_properties()`
- Add new schema templates in `schema-admin.js`
- Modify schema validation logic in `validate_schema()`

## Common Schema Use Cases

### Blog Post (BlogPosting)
```json
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Your Article Title",
  "author": {
    "@type": "Person",
    "name": "Author Name"
  },
  "datePublished": "2023-01-01T10:00:00+00:00",
  "dateModified": "2023-01-02T10:00:00+00:00",
  "publisher": {
    "@type": "Organization",
    "name": "Your Organization",
    "logo": {
      "@type": "ImageObject",
      "url": "https://example.com/logo.png"
    }
  },
  "image": "https://example.com/image.jpg",
  "description": "Article description here"
}
```

### Product
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Product Name",
  "image": "https://example.com/product.jpg",
  "description": "Product description",
  "offers": {
    "@type": "Offer",
    "price": "49.99",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  }
}
```

### Local Business
```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Business Name",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Main St",
    "addressLocality": "City",
    "addressRegion": "State",
    "postalCode": "12345"
  },
  "telephone": "+11234567890",
  "openingHours": "Mo,Tu,We,Th,Fr 09:00-17:00"
}
```

## Troubleshooting

If you encounter issues with your schema markup:

1. **Schema Doesn't Appear**: Make sure schema is enabled in the Customizer settings
2. **Validation Errors**: Check required properties for your selected schema type
3. **Rich Results Not Showing**: Test your page with [Google's Rich Results Test](https://search.google.com/test/rich-results)
4. **JSON Syntax Errors**: Use the "Format JSON" button to validate and format your custom schema

## Resources

- [Schema.org](https://schema.org/) - Official Schema.org documentation
- [Google's Structured Data Guidelines](https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data)
- [Rich Results Test](https://search.google.com/test/rich-results) - Test your structured data
- [Schema Markup Generator](https://technicalseo.com/tools/schema-markup-generator/) - Generate schema markup for testing

## License

This code is released under the same license as the theme it's included in.

---

*Note: Replace placeholder images (schema-settings-screenshot.jpg, schema-customizer-screenshot.jpg, schema-metabox-screenshot.jpg, schema-validator-screenshot.jpg) with actual screenshots once the feature is implemented.*
