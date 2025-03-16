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
    
    // Supported schema types
    private static $schema_types = array(
        'none'              => 'None',
        'Article'           => 'Article',
        'BlogPosting'       => 'Blog Post',
        'NewsArticle'       => 'News Article',
        'Product'           => 'Product',
        'Recipe'            => 'Recipe',
        'Review'            => 'Review',
        'FAQPage'           => 'FAQ Page',
        'HowTo'             => 'How-To Guide',
        'LocalBusiness'     => 'Local Business',
        'Event'             => 'Event',
        'Service'           => 'Service',
        'Person'            => 'Person',
        'Organization'      => 'Organization',
        'WebPage'           => 'Web Page',
        'CollectionPage'    => 'Collection Page',
        'ItemPage'          => 'Item Page',
        'AboutPage'         => 'About Page',
        'ContactPage'       => 'Contact Page',
        'ProfilePage'       => 'Profile Page'
    );
    
    /**
     * Initialize the Schema functionality
     */
    public static function init() {
        // Register customizer settings
        add_action( 'customize_register', array( __CLASS__, 'customizer_settings' ) );
        
        // Add schema meta box to posts and pages
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_schema_meta_box' ) );
        
        // Save schema meta box data
        add_action( 'save_post', array( __CLASS__, 'save_schema_meta_box' ) );
        
        // Add schema to the head section
        add_action( 'wp_head', array( __CLASS__, 'output_schema_markup' ) );
        
        // Add admin menu for schema validator
        add_action( 'admin_menu', array( __CLASS__, 'add_schema_validator_menu' ) );
        
        // Register admin assets
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
        
        // Add AJAX handler for schema validation
        add_action( 'wp_ajax_validate_schema', array( __CLASS__, 'ajax_validate_schema' ) );
    }
    
    /**
     * Register customizer settings for schema
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    public static function customizer_settings( $wp_customize ) {
        // Add Schema Markup section
        $wp_customize->add_section( 'custom_theme_schema', array(
            'title'       => __( 'Schema Markup Settings', 'custom-theme' ),
            'description' => __( 'Configure schema markup for better SEO.', 'custom-theme' ),
            'priority'    => 170,
        ) );
        
        // Enable/Disable Schema
        $wp_customize->add_setting( 'custom_theme_schema_enable', array(
            'default'           => true,
            'sanitize_callback' => 'custom_theme_sanitize_checkbox',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_enable', array(
            'label'    => __( 'Enable Schema Markup', 'custom-theme' ),
            'section'  => 'custom_theme_schema',
            'type'     => 'checkbox',
        ) );
        
        // Organization/Person toggle
        $wp_customize->add_setting( 'custom_theme_schema_entity_type', array(
            'default'           => 'Organization',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_entity_type', array(
            'label'    => __( 'Website Represents', 'custom-theme' ),
            'section'  => 'custom_theme_schema',
            'type'     => 'radio',
            'choices'  => array(
                'Organization' => __( 'Organization', 'custom-theme' ),
                'Person'       => __( 'Person', 'custom-theme' ),
            ),
        ) );
        
        // Organization Name
        $wp_customize->add_setting( 'custom_theme_schema_org_name', array(
            'default'           => get_bloginfo( 'name' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_org_name', array(
            'label'           => __( 'Organization Name', 'custom-theme' ),
            'section'         => 'custom_theme_schema',
            'type'            => 'text',
            'active_callback' => function() {
                return get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' ) === 'Organization';
            },
        ) );
        
        // Organization Logo
        $wp_customize->add_setting( 'custom_theme_schema_org_logo', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'custom_theme_schema_org_logo', array(
            'label'           => __( 'Organization Logo', 'custom-theme' ),
            'description'     => __( 'Recommended size: 112x112px, at least 112px on the smallest side', 'custom-theme' ),
            'section'         => 'custom_theme_schema',
            'active_callback' => function() {
                return get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' ) === 'Organization';
            },
        ) ) );
        
        // Person Name
        $wp_customize->add_setting( 'custom_theme_schema_person_name', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_person_name', array(
            'label'           => __( 'Person Name', 'custom-theme' ),
            'section'         => 'custom_theme_schema',
            'type'            => 'text',
            'active_callback' => function() {
                return get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' ) === 'Person';
            },
        ) );
        
        // Person Image
        $wp_customize->add_setting( 'custom_theme_schema_person_image', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'custom_theme_schema_person_image', array(
            'label'           => __( 'Person Image', 'custom-theme' ),
            'description'     => __( 'Recommended size: 112x112px, at least 112px on the smallest side', 'custom-theme' ),
            'section'         => 'custom_theme_schema',
            'active_callback' => function() {
                return get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' ) === 'Person';
            },
        ) ) );
        
        // Social Profile URLs
        $social_profiles = array(
            'facebook'  => __( 'Facebook URL', 'custom-theme' ),
            'twitter'   => __( 'Twitter URL', 'custom-theme' ),
            'instagram' => __( 'Instagram URL', 'custom-theme' ),
            'linkedin'  => __( 'LinkedIn URL', 'custom-theme' ),
            'youtube'   => __( 'YouTube URL', 'custom-theme' ),
            'pinterest' => __( 'Pinterest URL', 'custom-theme' ),
        );
        
        foreach ( $social_profiles as $key => $label ) {
            $wp_customize->add_setting( 'custom_theme_schema_social_' . $key, array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            ) );
            
            $wp_customize->add_control( 'custom_theme_schema_social_' . $key, array(
                'label'    => $label,
                'section'  => 'custom_theme_schema',
                'type'     => 'url',
            ) );
        }
        
        // Default schema type for posts
        $wp_customize->add_setting( 'custom_theme_schema_default_post', array(
            'default'           => 'BlogPosting',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_default_post', array(
            'label'    => __( 'Default Schema for Posts', 'custom-theme' ),
            'section'  => 'custom_theme_schema',
            'type'     => 'select',
            'choices'  => self::$schema_types,
        ) );
        
        // Default schema type for pages
        $wp_customize->add_setting( 'custom_theme_schema_default_page', array(
            'default'           => 'WebPage',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_schema_default_page', array(
            'label'    => __( 'Default Schema for Pages', 'custom-theme' ),
            'section'  => 'custom_theme_schema',
            'type'     => 'select',
            'choices'  => self::$schema_types,
        ) );
    }
    
    /**
     * Add schema meta box to posts and pages
     */
    public static function add_schema_meta_box() {
        add_meta_box(
            'custom_theme_schema_meta_box',
            __( 'Schema Markup Settings', 'custom-theme' ),
            array( __CLASS__, 'render_schema_meta_box' ),
            array( 'post', 'page' ),
            'normal',
            'high'
        );
    }
    
    /**
     * Render the schema meta box
     *
     * @param WP_Post $post The post object.
     */
    public static function render_schema_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'custom_theme_schema_meta_box', 'custom_theme_schema_meta_box_nonce' );
        
        // Get meta values
        $schema_type = get_post_meta( $post->ID, '_custom_theme_schema_type', true );
        $schema_custom = get_post_meta( $post->ID, '_custom_theme_schema_custom', true );
        
        // Default schema type based on post type if not set
        if ( empty( $schema_type ) ) {
            if ( 'post' === $post->post_type ) {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_post', 'BlogPosting' );
            } else {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_page', 'WebPage' );
            }
        }
        
        ?>
        <div class="custom-theme-schema-settings">
            <p>
                <label for="custom_theme_schema_type">
                    <strong><?php esc_html_e( 'Schema Type:', 'custom-theme' ); ?></strong>
                </label>
                <select name="custom_theme_schema_type" id="custom_theme_schema_type" class="widefat">
                    <?php foreach ( self::$schema_types as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $schema_type, $value ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="description"><?php esc_html_e( 'Select the most appropriate schema type for this content.', 'custom-theme' ); ?></span>
            </p>
            
            <div id="schema-custom-fields" style="margin-top: 15px; <?php echo ( $schema_type === 'none' ) ? 'display: none;' : ''; ?>">
                <p>
                    <label for="custom_theme_schema_custom">
                        <strong><?php esc_html_e( 'Custom Schema Properties (JSON):', 'custom-theme' ); ?></strong>
                    </label>
                    <textarea name="custom_theme_schema_custom" id="custom_theme_schema_custom" class="widefat" rows="8"><?php echo esc_textarea( $schema_custom ); ?></textarea>
                    <span class="description">
                        <?php esc_html_e( 'Advanced: Add custom schema properties in JSON format. These will be merged with the automatically generated schema.', 'custom-theme' ); ?>
                        <br>
                        <?php esc_html_e( 'Example: {"author": {"@type": "Person", "name": "John Doe"}}', 'custom-theme' ); ?>
                    </span>
                </p>
            </div>
            
            <script>
                jQuery(document).ready(function($) {
                    // Toggle custom fields based on schema type
                    $('#custom_theme_schema_type').on('change', function() {
                        if ($(this).val() === 'none') {
                            $('#schema-custom-fields').hide();
                        } else {
                            $('#schema-custom-fields').show();
                        }
                    });
                });
            </script>
        </div>
        <?php
    }
    
    /**
     * Save the schema meta box data
     *
     * @param int $post_id The post ID.
     */
    public static function save_schema_meta_box( $post_id ) {
        // Check if our nonce is set
        if ( ! isset( $_POST['custom_theme_schema_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify the nonce
        if ( ! wp_verify_nonce( $_POST['custom_theme_schema_meta_box_nonce'], 'custom_theme_schema_meta_box' ) ) {
            return;
        }
        
        // If this is an autosave, we don't want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check the user's permissions
        if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        
        // Sanitize and save the schema type
        if ( isset( $_POST['custom_theme_schema_type'] ) ) {
            $schema_type = sanitize_text_field( $_POST['custom_theme_schema_type'] );
            update_post_meta( $post_id, '_custom_theme_schema_type', $schema_type );
        }
        
        // Sanitize and save custom schema JSON
        if ( isset( $_POST['custom_theme_schema_custom'] ) ) {
            $schema_custom = wp_kses_post( $_POST['custom_theme_schema_custom'] );
            update_post_meta( $post_id, '_custom_theme_schema_custom', $schema_custom );
        }
    }
    
    /**
     * Output schema markup in the head section
     */
    public static function output_schema_markup() {
        // Check if schema is enabled
        if ( ! get_theme_mod( 'custom_theme_schema_enable', true ) ) {
            return;
        }
        
        // Get schema data
        $schema_data = self::generate_schema_data();
        
        if ( ! empty( $schema_data ) ) {
            echo "\n<!-- Schema.org markup generated by Custom Theme -->\n";
            echo '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }
    
    /**
     * Generate schema data based on context
     *
     * @return array Schema data
     */
    private static function generate_schema_data() {
        $schema_data = array();
        
        // Add WebSite schema
        $schema_data[] = self::get_website_schema();
        
        // Add entity (Organization or Person) schema
        $schema_data[] = self::get_entity_schema();
        
        // Add BreadcrumbList schema for all pages except home
        if ( ! is_front_page() && ! is_home() ) {
            $schema_data[] = self::get_breadcrumb_schema();
        }
        
        // Add page-specific schema
        if ( is_singular() ) {
            $post_schema = self::get_post_schema( get_post() );
            if ( ! empty( $post_schema ) ) {
                $schema_data[] = $post_schema;
            }
        } elseif ( is_archive() ) {
            $schema_data[] = self::get_archive_schema();
        } elseif ( is_search() ) {
            $schema_data[] = self::get_search_schema();
        }
        
        return $schema_data;
    }
    
    /**
     * Get Website schema
     *
     * @return array Website schema data
     */
    private static function get_website_schema() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            '@id'      => esc_url( home_url( '/#website' ) ),
            'url'      => esc_url( home_url( '/' ) ),
            'name'     => esc_html( get_bloginfo( 'name' ) ),
            'description' => esc_html( get_bloginfo( 'description' ) ),
            'potentialAction' => array(
                '@type'       => 'SearchAction',
                'target'      => esc_url( home_url( '/?s={search_term_string}' ) ),
                'query-input' => 'required name=search_term_string'
            )
        );
        
        return $schema;
    }
    
    /**
     * Get Organization or Person schema
     *
     * @return array Entity schema data
     */
    private static function get_entity_schema() {
        $entity_type = get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' );
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => $entity_type,
            '@id'      => esc_url( home_url( '/#' . strtolower( $entity_type ) ) ),
            'url'      => esc_url( home_url( '/' ) ),
        );
        
        // Add social URLs if available
        $social_urls = array();
        $social_profiles = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest' );
        
        foreach ( $social_profiles as $profile ) {
            $url = get_theme_mod( 'custom_theme_schema_social_' . $profile, '' );
            if ( ! empty( $url ) ) {
                $social_urls[] = esc_url( $url );
            }
        }
        
        if ( ! empty( $social_urls ) ) {
            $schema['sameAs'] = $social_urls;
        }
        
        // Add entity-specific properties
        if ( 'Organization' === $entity_type ) {
            $schema['name'] = esc_html( get_theme_mod( 'custom_theme_schema_org_name', get_bloginfo( 'name' ) ) );
            
            $logo_url = get_theme_mod( 'custom_theme_schema_org_logo', '' );
            if ( ! empty( $logo_url ) ) {
                $schema['logo'] = array(
                    '@type' => 'ImageObject',
                    'url'   => esc_url( $logo_url ),
                );
            }
        } else { // Person
            $schema['name'] = esc_html( get_theme_mod( 'custom_theme_schema_person_name', '' ) );
            
            $image_url = get_theme_mod( 'custom_theme_schema_person_image', '' );
            if ( ! empty( $image_url ) ) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url'   => esc_url( $image_url ),
                );
            }
        }
        
        return $schema;
    }
    
    /**
     * Get schema for a post or page
     *
     * @param WP_Post $post The post object
     * @return array|null Post schema data
     */
    private static function get_post_schema( $post ) {
        // Get schema type
        $schema_type = get_post_meta( $post->ID, '_custom_theme_schema_type', true );
        
        // If not set, use default based on post type
        if ( empty( $schema_type ) ) {
            if ( 'post' === $post->post_type ) {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_post', 'BlogPosting' );
            } else {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_page', 'WebPage' );
            }
        }
        
        // If schema is disabled, return null
        if ( 'none' === $schema_type ) {
            return null;
        }
        
        // Base schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => $schema_type,
            '@id'      => esc_url( get_permalink( $post ) . '#' . strtolower( $schema_type ) ),
            'url'      => esc_url( get_permalink( $post ) ),
            'name'     => esc_html( get_the_title( $post ) ),
            'headline' => esc_html( get_the_title( $post ) ),
        );
        
        // Add description if excerpt exists
        $excerpt = get_the_excerpt( $post );
        if ( ! empty( $excerpt ) ) {
            $schema['description'] = esc_html( $excerpt );
        }
        
        // Add published and modified dates for posts
        if ( 'post' === $post->post_type ) {
            $schema['datePublished'] = esc_html( get_the_date( 'c', $post ) );
            $schema['dateModified'] = esc_html( get_the_modified_date( 'c', $post ) );
        }
        
        // Add author for posts
        if ( in_array( $schema_type, array( 'Article', 'BlogPosting', 'NewsArticle' ), true ) ) {
            $author_id = $post->post_author;
            $schema['author'] = array(
                '@type' => 'Person',
                'name'  => esc_html( get_the_author_meta( 'display_name', $author_id ) ),
                'url'   => esc_url( get_author_posts_url( $author_id ) ),
            );
            
            // Add publisher (the Organization or Person representing the site)
            $entity_type = get_theme_mod( 'custom_theme_schema_entity_type', 'Organization' );
            
            if ( 'Organization' === $entity_type ) {
                $schema['publisher'] = array(
                    '@type' => 'Organization',
                    'name'  => esc_html( get_theme_mod( 'custom_theme_schema_org_name', get_bloginfo( 'name' ) ) ),
                    'logo'  => array(
                        '@type' => 'ImageObject',
                        'url'   => esc_url( get_theme_mod( 'custom_theme_schema_org_logo', '' ) ),
                    ),
                );
            } else {
                $schema['publisher'] = array(
                    '@type' => 'Person',
                    'name'  => esc_html( get_theme_mod( 'custom_theme_schema_person_name', '' ) ),
                );
            }
        }
        
        // Add featured image
        if ( has_post_thumbnail( $post ) ) {
            $featured_img_url = get_the_post_thumbnail_url( $post, 'full' );
            
            $schema['image'] = array(
                '@type'  => 'ImageObject',
                'url'    => esc_url( $featured_img_url ),
                'width'  => 1200,
                'height' => 630,
            );
        }
        
        // Merge with any custom schema properties
        $custom_schema = get_post_meta( $post->ID, '_custom_theme_schema_custom', true );
        if ( ! empty( $custom_schema ) ) {
            $custom_data = json_decode( $custom_schema, true );
            if ( is_array( $custom_data ) ) {
                $schema = array_merge( $schema, $custom_data );
            }
        }
        
        return $schema;
    }
    
    /**
     * Get BreadcrumbList schema
     *
     * @return array Breadcrumb schema data
     */
    private static function get_breadcrumb_schema() {
        global $post;
        
        $breadcrumbs = array();
        $position = 1;
        
        // Add home
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'item'     => array(
                '@id'  => esc_url( home_url( '/' ) ),
                'name' => __( 'Home', 'custom-theme' ),
            ),
        );
        
        // Build breadcrumb trail
        if ( is_singular() ) {
            // Add post type archive if applicable
            if ( 'post' === $post->post_type ) {
                $position++;
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_post_type_archive_link( 'post' ) ),
                        'name' => __( 'Blog', 'custom-theme' ),
                    ),
                );
            } elseif ( 'page' !== $post->post_type && get_post_type_archive_link( $post->post_type ) ) {
                $position++;
                $post_type_obj = get_post_type_object( $post->post_type );
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_post_type_archive_link( $post->post_type ) ),
                        'name' => $post_type_obj->labels->name,
                    ),
                );
            }
            
            // Add categories for posts
            if ( 'post' === $post->post_type ) {
                $categories = get_the_category( $post->ID );
                if ( ! empty( $categories ) ) {
                    $category = $categories[0];
                    $position++;
                    $breadcrumbs[] = array(
                        '@type'    => 'ListItem',
                        'position' => $position,
                        'item'     => array(
                            '@id'  => esc_url( get_category_link( $category->term_id ) ),
                            'name' => esc_html( $category->name ),
                        ),
                    );
                }
            }
            
            // Add parent pages for hierarchical content
            if ( 'page' === $post->post_type && $post->post_parent ) {
                $parent_id = $post->post_parent;
                $parents = array();
                
                while ( $parent_id ) {
                    $parent = get_post( $parent_id );
                    $parents[] = $parent;
                    $parent_id = $parent->post_parent;
                }
                
                // Add parents in order
                $parents = array_reverse( $parents );
                foreach ( $parents as $parent ) {
                    $position++;
                    $breadcrumbs[] = array(
                        '@type'    => 'ListItem',
                        'position' => $position,
                        'item'     => array(
                            '@id'  => esc_url( get_permalink( $parent->ID ) ),
                            'name' => esc_html( get_the_title( $parent->ID ) ),
                        ),
                    );
                }
            }
            
            // Add current page
            $position++;
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => array(
                    '@id'  => esc_url( get_permalink( $post->ID ) ),
                    'name' => esc_html( get_the_title( $post->ID ) ),
                ),
            );
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            
            // Add taxonomy archive link if available
            $taxonomy = get_taxonomy( $term->taxonomy );
            if ( $taxonomy ) {
                $position++;
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_post_type_archive_link( 'post' ) ),
                        'name' => $taxonomy->labels->name,
                    ),
                );
            }
            
            // Add parent terms for hierarchical taxonomies
            if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent ) {
                $parent_id = $term->parent;
                $parents = array();
                
                while ( $parent_id ) {
                    $parent = get_term( $parent_id, $term->taxonomy );
                    $parents[] = $parent;
                    $parent_id = $parent->parent;
                }
                
                // Add parents in order
                $parents = array_reverse( $parents );
                foreach ( $parents as $parent ) {
                    $position++;
                    $breadcrumbs[] = array(
                        '@type'    => 'ListItem',
                        'position' => $position,
                        'item'     => array(
                            '@id'  => esc_url( get_term_link( $parent ) ),
                            'name' => esc_html( $parent->name ),
                        ),
                    );
                }
            }
            
            // Add current term
            $position++;
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => array(
                    '@id'  => esc_url( get_term_link( $term ) ),
                    'name' => esc_html( $term->name ),
                ),
            );
        } elseif ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            $post_type_obj = get_post_type_object( $post_type );
            
            // Add post type archive
            $position++;
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => array(
                    '@id'  => esc_url( get_post_type_archive_link( $post_type ) ),
                    'name' => esc_html( $post_type_obj->labels->name ),
                ),
            );
        } elseif ( is_author() ) {
            // Add author archive
            $position++;
            $author_id = get_query_var( 'author' );
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => array(
                    '@id'  => esc_url( get_author_posts_url( $author_id ) ),
                    'name' => esc_html( get_the_author_meta( 'display_name', $author_id ) ),
                ),
            );
        } elseif ( is_date() ) {
            // Add date archive
            $position++;
            if ( is_year() ) {
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_year_link( get_query_var( 'year' ) ) ),
                        'name' => esc_html( get_query_var( 'year' ) ),
                    ),
                );
            } elseif ( is_month() ) {
                // Add year
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_year_link( get_query_var( 'year' ) ) ),
                        'name' => esc_html( get_query_var( 'year' ) ),
                    ),
                );
                
                // Add month
                $position++;
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ) ),
                        'name' => esc_html( date( 'F', mktime( 0, 0, 0, get_query_var( 'monthnum' ), 1, get_query_var( 'year' ) ) ) ),
                    ),
                );
            } elseif ( is_day() ) {
                // Add year
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_year_link( get_query_var( 'year' ) ) ),
                        'name' => esc_html( get_query_var( 'year' ) ),
                    ),
                );
                
                // Add month
                $position++;
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ) ),
                        'name' => esc_html( date( 'F', mktime( 0, 0, 0, get_query_var( 'monthnum' ), 1, get_query_var( 'year' ) ) ) ),
                    ),
                );
                
                // Add day
                $position++;
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => array(
                        '@id'  => esc_url( get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) ) ),
                        'name' => esc_html( get_query_var( 'day' ) ),
                    ),
                );
            }
        } elseif ( is_search() ) {
            // Add search results
            $position++;
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => array(
                    '@id'  => esc_url( get_search_link( get_search_query() ) ),
                    'name' => sprintf( __( 'Search results for "%s"', 'custom-theme' ), esc_html( get_search_query() ) ),
                ),
            );
        }
        
        // Build schema array
        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            '@id'             => esc_url( trailingslashit( home_url() ) . '#breadcrumb' ),
            'itemListElement' => $breadcrumbs,
        );
        
        return $schema;
    }
    
    /**
     * Get Archive schema
     *
     * @return array Archive schema data
     */
    private static function get_archive_schema() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'CollectionPage',
            '@id'      => esc_url( get_permalink() . '#collectionpage' ),
            'url'      => esc_url( get_permalink() ),
        );
        
        if ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            $schema['name'] = esc_html( $term->name );
            if ( ! empty( $term->description ) ) {
                $schema['description'] = esc_html( $term->description );
            }
        } elseif ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            $post_type_obj = get_post_type_object( $post_type );
            $schema['name'] = esc_html( $post_type_obj->labels->name );
        } elseif ( is_author() ) {
            $author_id = get_query_var( 'author' );
            $schema['name'] = sprintf( __( 'Posts by %s', 'custom-theme' ), esc_html( get_the_author_meta( 'display_name', $author_id ) ) );
        } elseif ( is_date() ) {
            if ( is_year() ) {
                $schema['name'] = sprintf( __( 'Posts from %s', 'custom-theme' ), esc_html( get_query_var( 'year' ) ) );
            } elseif ( is_month() ) {
                $schema['name'] = sprintf( __( 'Posts from %s %s', 'custom-theme' ), esc_html( date( 'F', mktime( 0, 0, 0, get_query_var( 'monthnum' ), 1, get_query_var( 'year' ) ) ) ), esc_html( get_query_var( 'year' ) ) );
            } elseif ( is_day() ) {
                $schema['name'] = sprintf( __( 'Posts from %s %d, %s', 'custom-theme' ), esc_html( date( 'F', mktime( 0, 0, 0, get_query_var( 'monthnum' ), 1, get_query_var( 'year' ) ) ) ), esc_html( get_query_var( 'day' ) ), esc_html( get_query_var( 'year' ) ) );
            }
        }
        
        return $schema;
    }
    
    /**
     * Get Search schema
     *
     * @return array Search schema data
     */
    private static function get_search_schema() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'SearchResultsPage',
            '@id'      => esc_url( get_search_link( get_search_query() ) . '#searchresultspage' ),
            'url'      => esc_url( get_search_link( get_search_query() ) ),
            'name'     => sprintf( __( 'Search results for "%s"', 'custom-theme' ), esc_html( get_search_query() ) ),
        );
        
        return $schema;
    }
    
    /**
     * Add schema validator page to admin menu
     */
    public static function add_schema_validator_menu() {
        add_submenu_page(
            'tools.php',
            __( 'Schema Validator', 'custom-theme' ),
            __( 'Schema Validator', 'custom-theme' ),
            'manage_options',
            'custom-theme-schema-validator',
            array( __CLASS__, 'render_schema_validator_page' )
        );
    }
    
    /**
     * Render schema validator page
     */
    public static function render_schema_validator_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Schema Markup Validator', 'custom-theme' ); ?></h1>
            
            <div class="card">
                <h2><?php esc_html_e( 'Validate Schema Markup', 'custom-theme' ); ?></h2>
                <p><?php esc_html_e( 'Select a page or post to validate its schema markup against best practices.', 'custom-theme' ); ?></p>
                
                <div class="schema-validator-form">
                    <select id="schema-content-selector" class="widefat">
                        <option value=""><?php esc_html_e( '-- Select Content --', 'custom-theme' ); ?></option>
                        <optgroup label="<?php esc_attr_e( 'Pages', 'custom-theme' ); ?>">
                            <?php
                            $pages = get_pages( array(
                                'sort_column' => 'post_title',
                                'sort_order'  => 'ASC',
                            ) );
                            
                            foreach ( $pages as $page ) {
                                echo '<option value="' . esc_attr( $page->ID ) . '">' . esc_html( $page->post_title ) . '</option>';
                            }
                            ?>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e( 'Posts', 'custom-theme' ); ?>">
                            <?php
                            $posts = get_posts( array(
                                'post_type'      => 'post',
                                'posts_per_page' => -1,
                                'orderby'        => 'title',
                                'order'          => 'ASC',
                            ) );
                            
                            foreach ( $posts as $post ) {
                                echo '<option value="' . esc_attr( $post->ID ) . '">' . esc_html( $post->post_title ) . '</option>';
                            }
                            ?>
                        </optgroup>
                    </select>
                    
                    <button id="validate-schema-button" class="button button-primary"><?php esc_html_e( 'Validate Schema', 'custom-theme' ); ?></button>
                </div>
            </div>
            
            <div id="schema-validation-results" class="card" style="display: none;">
                <h2><?php esc_html_e( 'Validation Results', 'custom-theme' ); ?></h2>
                <div id="schema-validation-content"></div>
            </div>
            
            <div class="card">
                <h2><?php esc_html_e( 'Schema Markup Best Practices', 'custom-theme' ); ?></h2>
                <ul class="schema-best-practices">
                    <li><?php esc_html_e( 'Use the most specific schema type that applies to your content.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'Include all required properties for each schema type.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'Use structured data for your logo that is at least 112x112px.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'For articles, include author, publisher, and dates.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'For products, include price, availability, and reviews if applicable.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'For local businesses, include address, hours, and contact information.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'For events, include date, time, location, and offers if applicable.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'Avoid keyword stuffing in schema properties.', 'custom-theme' ); ?></li>
                    <li><?php esc_html_e( 'Ensure your schema markup is accurate and represents the actual content.', 'custom-theme' ); ?></li>
                </ul>
                
                <p>
                    <a href="https://schema.org/" target="_blank" class="button"><?php esc_html_e( 'Visit Schema.org', 'custom-theme' ); ?></a>
                    <a href="https://search.google.com/test/rich-results" target="_blank" class="button"><?php esc_html_e( 'Google Rich Results Test', 'custom-theme' ); ?></a>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook The current admin page
     */
    public static function admin_enqueue_scripts( $hook ) {
        // Only load on schema validator page
        if ( 'tools_page_custom-theme-schema-validator' === $hook ) {
            wp_enqueue_style(
                'custom-theme-schema-validator',
                get_template_directory_uri() . '/assets/css/schema-validator.css',
                array(),
                CUSTOM_THEME_VERSION
            );
            
            wp_enqueue_script(
                'custom-theme-schema-validator',
                get_template_directory_uri() . '/assets/js/schema-validator.js',
                array( 'jquery' ),
                CUSTOM_THEME_VERSION,
                true
            );
            
            wp_localize_script(
                'custom-theme-schema-validator',
                'schemaValidatorData',
                array(
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce'   => wp_create_nonce( 'schema_validator_nonce' ),
                    'loading' => __( 'Loading...', 'custom-theme' ),
                    'error'   => __( 'Error loading schema data.', 'custom-theme' ),
                )
            );
        }
        
        // Load on post/page edit screens
        if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
            wp_enqueue_script(
                'custom-theme-schema-admin',
                get_template_directory_uri() . '/assets/js/schema-admin.js',
                array( 'jquery' ),
                CUSTOM_THEME_VERSION,
                true
            );
        }
    }
    
    /**
     * AJAX handler for schema validation
     */
    public static function ajax_validate_schema() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'schema_validator_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'custom-theme' ),
            ) );
        }
        
        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'custom-theme' ),
            ) );
        }
        
        // Get post ID
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        if ( ! $post_id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid post ID.', 'custom-theme' ),
            ) );
        }
        
        // Get post
        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error( array(
                'message' => __( 'Post not found.', 'custom-theme' ),
            ) );
        }
        
        // Setup post data
        setup_postdata( $post );
        
        // Get schema type
        $schema_type = get_post_meta( $post_id, '_custom_theme_schema_type', true );
        
        // If not set, use default based on post type
        if ( empty( $schema_type ) ) {
            if ( 'post' === $post->post_type ) {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_post', 'BlogPosting' );
            } else {
                $schema_type = get_theme_mod( 'custom_theme_schema_default_page', 'WebPage' );
            }
        }
        
        // Get the schema data
        $schema_data = self::get_post_schema( $post );
        
        // Convert to pretty JSON
        $schema_json = json_encode( $schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        
        // Validate the schema
        $validation_results = self::validate_schema( $schema_data, $schema_type );
        
        // Restore original post data
        wp_reset_postdata();
        
        // Send response
        wp_send_json_success( array(
            'title'           => get_the_title( $post ),
            'permalink'       => get_permalink( $post ),
            'schema_type'     => $schema_type,
            'schema_json'     => $schema_json,
            'validation'      => $validation_results,
            'recommendations' => self::get_schema_recommendations( $schema_type, $schema_data ),
        ) );
    }
    
    /**
     * Validate schema markup based on type
     *
     * @param array  $schema Schema data
     * @param string $type   Schema type
     * @return array Validation results
     */
    private static function validate_schema( $schema, $type ) {
        $results = array(
            'status'  => 'success',
            'issues'  => array(),
            'success' => array(),
        );
        
        // Check if schema is disabled
        if ( 'none' === $type ) {
            $results['status'] = 'warning';
            $results['issues'][] = __( 'Schema markup is disabled for this content.', 'custom-theme' );
            return $results;
        }
        
        // Check if schema has proper @context
        if ( ! isset( $schema['@context'] ) || 'https://schema.org' !== $schema['@context'] ) {
            $results['status'] = 'error';
            $results['issues'][] = __( 'Missing or invalid @context property. Should be "https://schema.org".', 'custom-theme' );
        } else {
            $results['success'][] = __( 'Valid @context property.', 'custom-theme' );
        }
        
        // Check if schema has proper @type
        if ( ! isset( $schema['@type'] ) || $type !== $schema['@type'] ) {
            $results['status'] = 'error';
            $results['issues'][] = __( 'Missing or invalid @type property.', 'custom-theme' );
        } else {
            $results['success'][] = __( 'Valid @type property.', 'custom-theme' );
        }
        
        // Check for required properties based on type
        $required_props = self::get_required_properties( $type );
        foreach ( $required_props as $prop ) {
            if ( ! isset( $schema[$prop] ) || empty( $schema[$prop] ) ) {
                $results['status'] = 'error';
                $results['issues'][] = sprintf( __( 'Missing required property: %s', 'custom-theme' ), $prop );
            } else {
                $results['success'][] = sprintf( __( 'Has required property: %s', 'custom-theme' ), $prop );
            }
        }
        
        // Check for recommended properties
        $recommended_props = self::get_recommended_properties( $type );
        foreach ( $recommended_props as $prop ) {
            if ( ! isset( $schema[$prop] ) || empty( $schema[$prop] ) ) {
                if ( $results['status'] !== 'error' ) {
                    $results['status'] = 'warning';
                }
                $results['issues'][] = sprintf( __( 'Missing recommended property: %s', 'custom-theme' ), $prop );
            } else {
                $results['success'][] = sprintf( __( 'Has recommended property: %s', 'custom-theme' ), $prop );
            }
        }
        
        return $results;
    }
    
    /**
     * Get required properties for a schema type
     *
     * @param string $type Schema type
     * @return array Required properties
     */
    private static function get_required_properties( $type ) {
        $required = array(
            'name',
            'url',
        );
        
        // Add type-specific required properties
        switch ( $type ) {
            case 'Article':
            case 'BlogPosting':
            case 'NewsArticle':
                $required = array_merge( $required, array(
                    'headline',
                    'author',
                    'datePublished',
                    'publisher',
                ) );
                break;
                
            case 'Product':
                $required = array_merge( $required, array(
                    'description',
                    'image',
                ) );
                break;
                
            case 'Recipe':
                $required = array_merge( $required, array(
                    'recipeIngredient',
                    'recipeInstructions',
                ) );
                break;
                
            case 'Review':
                $required = array_merge( $required, array(
                    'itemReviewed',
                    'reviewRating',
                ) );
                break;
                
            case 'FAQPage':
                $required = array_merge( $required, array(
                    'mainEntity',
                ) );
                break;
                
            case 'HowTo':
                $required = array_merge( $required, array(
                    'step',
                ) );
                break;
                
            case 'LocalBusiness':
                $required = array_merge( $required, array(
                    'address',
                ) );
                break;
                
            case 'Event':
                $required = array_merge( $required, array(
                    'startDate',
                    'location',
                ) );
                break;
        }
        
        return $required;
    }
    
    /**
     * Get recommended properties for a schema type
     *
     * @param string $type Schema type
     * @return array Recommended properties
     */
    private static function get_recommended_properties( $type ) {
        $recommended = array(
            'description',
            'image',
        );
        
        // Add type-specific recommended properties
        switch ( $type ) {
            case 'Article':
            case 'BlogPosting':
            case 'NewsArticle':
                $recommended = array_merge( $recommended, array(
                    'dateModified',
                    'mainEntityOfPage',
                    'keywords',
                ) );
                break;
                
            case 'Product':
                $recommended = array_merge( $recommended, array(
                    'brand',
                    'offers',
                    'aggregateRating',
                    'review',
                ) );
                break;
                
            case 'Recipe':
                $recommended = array_merge( $recommended, array(
                    'cookTime',
                    'prepTime',
                    'totalTime',
                    'nutrition',
                    'recipeYield',
                    'recipeCategory',
                    'recipeCuisine',
                ) );
                break;
                
            case 'Review':
                $recommended = array_merge( $recommended, array(
                    'author',
                    'datePublished',
                    'reviewBody',
                ) );
                break;
                
            case 'FAQPage':
                break;
                
            case 'HowTo':
                $recommended = array_merge( $recommended, array(
                    'totalTime',
                    'supply',
                    'tool',
                ) );
                break;
                
            case 'LocalBusiness':
                $recommended = array_merge( $recommended, array(
                    'telephone',
                    'openingHours',
                    'priceRange',
                    'geo',
                ) );
                break;
                
            case 'Event':
                $recommended = array_merge( $recommended, array(
                    'endDate',
                    'performer',
                    'offers',
                    'organizer',
                ) );
                break;
                
            case 'Service':
                $recommended = array_merge( $recommended, array(
                    'provider',
                    'serviceType',
                    'areaServed',
                    'offers',
                ) );
                break;
        }
        
        return $recommended;
    }
    
    /**
     * Get schema recommendations
     *
     * @param string $type  Schema type
     * @param array  $schema Schema data
     * @return array Recommendations
     */
    private static function get_schema_recommendations( $type, $schema ) {
        $recommendations = array();
        
        // Basic recommendations for all schema types
        if ( empty( $schema['description'] ) ) {
            $recommendations[] = __( 'Add a description to provide more context.', 'custom-theme' );
        }
        
        if ( empty( $schema['image'] ) ) {
            $recommendations[] = __( 'Add an image to enhance visual representation.', 'custom-theme' );
        }
        
        // Type-specific recommendations
        switch ( $type ) {
            case 'Article':
            case 'BlogPosting':
            case 'NewsArticle':
                if ( ! isset( $schema['author'] ) || ! is_array( $schema['author'] ) || empty( $schema['author']['name'] ) ) {
                    $recommendations[] = __( 'Add complete author information.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['publisher'] ) || ! is_array( $schema['publisher'] ) || empty( $schema['publisher']['logo'] ) ) {
                    $recommendations[] = __( 'Add publisher logo for better representation in search results.', 'custom-theme' );
                }
                
                break;
                
            case 'Product':
                if ( ! isset( $schema['offers'] ) ) {
                    $recommendations[] = __( 'Add price and availability information using the offers property.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['brand'] ) ) {
                    $recommendations[] = __( 'Specify the product brand for better categorization.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['review'] ) && ! isset( $schema['aggregateRating'] ) ) {
                    $recommendations[] = __( 'Add reviews or ratings to enhance product credibility.', 'custom-theme' );
                }
                
                break;
                
            case 'LocalBusiness':
                if ( ! isset( $schema['address'] ) || ! is_array( $schema['address'] ) ) {
                    $recommendations[] = __( 'Add a complete address using PostalAddress type.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['telephone'] ) ) {
                    $recommendations[] = __( 'Add contact telephone number.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['openingHours'] ) ) {
                    $recommendations[] = __( 'Add business hours for better customer information.', 'custom-theme' );
                }
                
                break;
                
            case 'Recipe':
                if ( ! isset( $schema['recipeYield'] ) ) {
                    $recommendations[] = __( 'Specify how many servings this recipe yields.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['totalTime'] ) ) {
                    $recommendations[] = __( 'Add total preparation and cooking time.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['nutrition'] ) ) {
                    $recommendations[] = __( 'Add nutritional information for health-conscious users.', 'custom-theme' );
                }
                
                break;
                
            case 'Event':
                if ( ! isset( $schema['endDate'] ) ) {
                    $recommendations[] = __( 'Specify the end date/time of the event.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['offers'] ) ) {
                    $recommendations[] = __( 'Add ticket or registration information using the offers property.', 'custom-theme' );
                }
                
                if ( ! isset( $schema['organizer'] ) ) {
                    $recommendations[] = __( 'Specify the event organizer for better context.', 'custom-theme' );
                }
                
                break;
        }
        
        // General enhancement recommendations
        if ( count( $recommendations ) === 0 ) {
            $recommendations[] = __( 'Your schema markup looks good! Consider adding more optional properties for even richer results.', 'custom-theme' );
        }
        
        return $recommendations;
    }
}

// Initialize the Schema class
add_action( 'after_setup_theme', array( 'Custom_Theme_Schema', 'init' ) );
