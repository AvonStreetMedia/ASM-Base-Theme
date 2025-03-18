<?php
/**
 * Schema Markup Functions
 *
 * @package Custom_Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class to handle Schema Markup functionality
 */
class Custom_Theme_Schema {

    private static $cache_key = 'custom_theme_schema_cache_';
    private static $cache_time = 12 * HOUR_IN_SECONDS; // Cache duration

    /**
     * Initialize Schema functionality
     */
    public static function init() {
        add_action( 'wp_head', array( __CLASS__, 'output_schema_markup' ) );
        add_action( 'save_post', array( __CLASS__, 'clear_schema_cache' ) );
    }

    /**
     * Output schema markup in the head section
     */
    public static function output_schema_markup() {
        if ( ! get_theme_mod( 'custom_theme_schema_enable', true ) ) {
            return;
        }

        global $post;
        if ( ! $post ) {
            return;
        }

        // Check cached schema
        $cached_schema = get_transient( self::$cache_key . $post->ID );
        if ( $cached_schema ) {
            echo $cached_schema;
            return;
        }

        $schema_data = self::generate_schema_data();

        if ( ! empty( $schema_data ) ) {
            $json_schema = '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
            
            // Cache the schema output
            set_transient( self::$cache_key . $post->ID, $json_schema, self::$cache_time );

            echo $json_schema;
        }
    }

    /**
     * Generate schema data based on context
     *
     * @return array Schema data
     */
    private static function generate_schema_data() {
        global $post;

        $schema_data = [];

        // Add per-post schema if manually overridden
        $custom_schema = get_post_meta( $post->ID, '_custom_theme_schema_custom', true );
        if ( ! empty( $custom_schema ) ) {
            $custom_data = json_decode( $custom_schema, true );
            if ( is_array( $custom_data ) ) {
                return $custom_data;
            }
        }

        // Detect and generate schema type
        $schema_type = get_post_meta( $post->ID, '_custom_theme_schema_type', true );

        switch ( $schema_type ) {
            case 'Article':
            case 'BlogPosting':
            case 'NewsArticle':
                $schema_data[] = self::get_article_schema( $post );
                break;
            case 'Product':
                $schema_data[] = self::get_product_schema( $post );
                break;
            case 'FAQPage':
                $schema_data[] = self::get_faq_schema( $post );
                break;
            case 'LocalBusiness':
                $schema_data[] = self::get_localbusiness_schema();
                break;
            default:
                $schema_data[] = self::get_webpage_schema( $post );
                break;
        }

        return $schema_data;
    }

    /**
     * Article Schema
     */
    private static function get_article_schema( $post ) {
        return [
            "@context" => "https://schema.org",
            "@type"    => "Article",
            "headline" => get_the_title( $post ),
            "author"   => [
                "@type" => "Person",
                "name"  => get_the_author()
            ],
            "datePublished" => get_the_date( 'c', $post ),
            "dateModified"  => get_the_modified_date( 'c', $post ),
            "mainEntityOfPage" => get_permalink( $post )
        ];
    }

    /**
     * Product Schema
     */
    private static function get_product_schema( $post ) {
        return [
            "@context" => "https://schema.org",
            "@type"    => "Product",
            "name"     => get_the_title( $post ),
            "image"    => get_the_post_thumbnail_url( $post, 'full' ),
            "description" => get_the_excerpt( $post ),
            "offers"   => [
                "@type" => "Offer",
                "priceCurrency" => get_woocommerce_currency(),
                "price" => get_post_meta( $post->ID, '_price', true ),
                "availability" => "https://schema.org/InStock",
                "url" => get_permalink( $post )
            ]
        ];
    }

    /**
     * FAQ Schema
     */
    private static function get_faq_schema( $post ) {
        $faq_data = [];
        preg_match_all( '/<h2>(.*?)<\/h2>\s*<p>(.*?)<\/p>/', get_the_content( $post ), $matches, PREG_SET_ORDER );

        foreach ( $matches as $match ) {
            $faq_data[] = [
                "@type" => "Question",
                "name"  => strip_tags( $match[1] ),
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text"  => strip_tags( $match[2] )
                ]
            ];
        }

        return [
            "@context" => "https://schema.org",
            "@type"    => "FAQPage",
            "mainEntity" => $faq_data
        ];
    }

    /**
     * Local Business Schema
     */
    private static function get_localbusiness_schema() {
        return [
            "@context" => "https://schema.org",
            "@type"    => "LocalBusiness",
            "name"     => get_bloginfo( 'name' ),
            "address"  => [
                "@type"    => "PostalAddress",
                "streetAddress" => "123 Example St",
                "addressLocality" => "City",
                "addressRegion" => "State",
                "postalCode" => "12345",
                "addressCountry" => "US"
            ],
            "telephone" => "+1-800-123-4567"
        ];
    }

    /**
     * WebPage Schema
     */
    private static function get_webpage_schema( $post ) {
        return [
            "@context" => "https://schema.org",
            "@type"    => "WebPage",
            "name"     => get_the_title( $post ),
            "url"      => get_permalink( $post )
        ];
    }

    /**
     * Clear schema cache when post is updated
     */
    public static function clear_schema_cache( $post_id ) {
        delete_transient( self::$cache_key . $post_id );
    }
}

// Initialize Schema class
add_action( 'after_setup_theme', array( 'Custom_Theme_Schema', 'init' ) );