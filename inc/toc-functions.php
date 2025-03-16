<?php
/**
 * Table of Contents Functions - Improved Implementation
 *
 * @package Custom_Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class to handle Table of Contents functionality
 */
class Custom_Theme_TOC {
    
    /**
     * Initialize the TOC functionality
     */
    public static function init() {
        // Register customizer settings
        add_action( 'customize_register', array( __CLASS__, 'customizer_settings' ) );
        
        // Add TOC to post content
        add_filter( 'the_content', array( __CLASS__, 'add_toc_to_content' ), 100 ); // Higher priority to run after other filters
        
        // Add meta box for enabling/disabling TOC on individual posts
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_toc_meta_box' ) );
        
        // Save meta box data
        add_action( 'save_post', array( __CLASS__, 'save_toc_meta_box' ) );
        
        // Enqueue TOC scripts and styles
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }
    
    /**
     * Register customizer settings for TOC
     */
    public static function customizer_settings( $wp_customize ) {
        // Add TOC section
        $wp_customize->add_section( 'custom_theme_toc', array(
            'title'       => __( 'Table of Contents', 'custom-theme' ),
            'description' => __( 'Customize the Table of Contents settings.', 'custom-theme' ),
            'priority'    => 160,
        ) );
        
        // Enable/Disable TOC by default
        $wp_customize->add_setting( 'custom_theme_toc_enable', array(
            'default'           => true,
            'sanitize_callback' => 'custom_theme_sanitize_checkbox',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_enable', array(
            'label'    => __( 'Enable Table of Contents', 'custom-theme' ),
            'section'  => 'custom_theme_toc',
            'type'     => 'checkbox',
        ) );
        
        // Minimum number of headings for TOC to appear
        $wp_customize->add_setting( 'custom_theme_toc_min_headings', array(
            'default'           => 3,
            'sanitize_callback' => 'absint',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_min_headings', array(
            'label'       => __( 'Minimum Headings', 'custom-theme' ),
            'description' => __( 'Minimum number of headings required for TOC to appear.', 'custom-theme' ),
            'section'     => 'custom_theme_toc',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
        ) );
        
        // TOC Title
        $wp_customize->add_setting( 'custom_theme_toc_title', array(
            'default'           => __( 'Table of Contents', 'custom-theme' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_title', array(
            'label'   => __( 'TOC Title', 'custom-theme' ),
            'section' => 'custom_theme_toc',
            'type'    => 'text',
        ) );
        
        // TOC position
        $wp_customize->add_setting( 'custom_theme_toc_position', array(
            'default'           => 'top',
            'sanitize_callback' => 'custom_theme_sanitize_select',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_position', array(
            'label'   => __( 'TOC Position', 'custom-theme' ),
            'section' => 'custom_theme_toc',
            'type'    => 'select',
            'choices' => array(
                'top'           => __( 'Top of post', 'custom-theme' ),
                'after-first-p' => __( 'After first paragraph', 'custom-theme' ),
            ),
        ) );
        
        // TOC Width
        $wp_customize->add_setting( 'custom_theme_toc_width', array(
            'default'           => '100',
            'sanitize_callback' => 'custom_theme_sanitize_select',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_width', array(
            'label'   => __( 'TOC Width', 'custom-theme' ),
            'section' => 'custom_theme_toc',
            'type'    => 'select',
            'choices' => array(
                '100' => __( 'Full Width', 'custom-theme' ),
                '75'  => __( '75%', 'custom-theme' ),
                '50'  => __( '50%', 'custom-theme' ),
            ),
        ) );
        
        // Show/Hide TOC toggle
        $wp_customize->add_setting( 'custom_theme_toc_toggle', array(
            'default'           => true,
            'sanitize_callback' => 'custom_theme_sanitize_checkbox',
        ) );
        
        $wp_customize->add_control( 'custom_theme_toc_toggle', array(
            'label'    => __( 'Show TOC Toggle Button', 'custom-theme' ),
            'section'  => 'custom_theme_toc',
            'type'     => 'checkbox',
        ) );
    }
    
    /**
     * Add meta box for TOC settings on individual posts
     */
    public static function add_toc_meta_box() {
        add_meta_box(
            'custom_theme_toc_meta_box',
            __( 'Table of Contents Settings', 'custom-theme' ),
            array( __CLASS__, 'render_toc_meta_box' ),
            array( 'post', 'page' ),
            'side',
            'default'
        );
    }
    
    /**
     * Render the TOC meta box
     */
    public static function render_toc_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'custom_theme_toc_meta_box', 'custom_theme_toc_meta_box_nonce' );
        
        // Get meta value
        $disable_toc = get_post_meta( $post->ID, '_custom_theme_disable_toc', true );
        
        ?>
        <p>
            <label for="custom_theme_disable_toc">
                <input type="checkbox" name="custom_theme_disable_toc" id="custom_theme_disable_toc" value="1" <?php checked( $disable_toc, 1 ); ?> />
                <?php esc_html_e( 'Disable Table of Contents for this post', 'custom-theme' ); ?>
            </label>
        </p>
        <?php
    }
    
    /**
     * Save the TOC meta box data
     */
    public static function save_toc_meta_box( $post_id ) {
        // Check if our nonce is set
        if ( ! isset( $_POST['custom_theme_toc_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify the nonce
        if ( ! wp_verify_nonce( $_POST['custom_theme_toc_meta_box_nonce'], 'custom_theme_toc_meta_box' ) ) {
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
        
        // Save the data
        if ( isset( $_POST['custom_theme_disable_toc'] ) ) {
            update_post_meta( $post_id, '_custom_theme_disable_toc', 1 );
        } else {
            delete_post_meta( $post_id, '_custom_theme_disable_toc' );
        }
    }
    
    /**
     * Enqueue TOC scripts and styles
     */
    public static function enqueue_scripts() {
        // Only load TOC assets on singular posts and pages
        if ( is_singular() && ! is_admin() ) {
            // Check if TOC is enabled in customizer and not disabled for this post
            if ( 
                get_theme_mod( 'custom_theme_toc_enable', true ) && 
                ! get_post_meta( get_the_ID(), '_custom_theme_disable_toc', true )
            ) {
                wp_enqueue_style(
                    'custom-theme-toc',
                    get_template_directory_uri() . '/assets/css/toc.css',
                    array(),
                    CUSTOM_THEME_VERSION
                );
                
                wp_enqueue_script(
                    'custom-theme-toc',
                    get_template_directory_uri() . '/assets/js/toc.js',
                    array( 'jquery' ),
                    CUSTOM_THEME_VERSION,
                    true
                );
            }
        }
    }
    
    /**
     * Add TOC to post content
     */
    public static function add_toc_to_content( $content ) {
        // Bail if not a single post or page, or if in the admin area
        if ( ! is_singular() || is_admin() ) {
            return $content;
        }
        
        // Check if TOC is enabled in customizer
        if ( ! get_theme_mod( 'custom_theme_toc_enable', true ) ) {
            return $content;
        }
        
        // Check if TOC is disabled for this post
        if ( get_post_meta( get_the_ID(), '_custom_theme_disable_toc', true ) ) {
            return $content;
        }
        
        // Extract headings and add IDs if missing
        $result = self::process_content_headings( $content );
        $headings = $result['headings'];
        $content = $result['content'];
        
        // Bail if not enough headings
        $min_headings = get_theme_mod( 'custom_theme_toc_min_headings', 3 );
        if ( count( $headings ) < $min_headings ) {
            return $content;
        }
        
        // Generate TOC HTML
        $toc_html = self::generate_toc_html( $headings );
        
        // Insert TOC at appropriate position
        $position = get_theme_mod( 'custom_theme_toc_position', 'top' );
        
        if ( 'top' === $position ) {
            return $toc_html . $content;
        } elseif ( 'after-first-p' === $position ) {
            // Find the end of the first paragraph
            $pos = strpos( $content, '</p>' );
            
            if ( false !== $pos ) {
                $pos += 4; // Length of </p>
                return substr( $content, 0, $pos ) . $toc_html . substr( $content, $pos );
            }
        }
        
        // Default to top if position logic fails
        return $toc_html . $content;
    }
    
    /**
     * Process content to extract headings and add IDs where missing
     */
    private static function process_content_headings( $content ) {
        $headings = array();
        $used_ids = array();
        
        // Regular expression to extract headings (h2, h3, h4)
        $pattern = '/<h([2-4])(.*?)>(.*?)<\/h[2-4]>/i';
        
        // Find all headings in the content
        if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                $level = (int) $match[1];       // Heading level (2, 3, 4)
                $attrs = $match[2];             // Attributes in the heading tag
                $heading_text = $match[3];      // Inner text of the heading
                $clean_text = strip_tags( $heading_text ); // Text without HTML tags
                
                // Check if the heading already has an ID
                if ( preg_match( '/id=(["\'])(.*?)\1/i', $attrs, $id_match ) ) {
                    $id = $id_match[2];
                } else {
                    // Create an ID from the heading text
                    $id = self::generate_heading_id( $clean_text, $used_ids );
                    
                    // Add the ID to the heading in the content
                    $new_heading = "<h{$level} id=\"{$id}\"{$attrs}>{$heading_text}</h{$level}>";
                    $content = str_replace( $match[0], $new_heading, $content );
                }
                
                // Store the heading data
                $headings[] = array(
                    'level' => $level,
                    'text'  => $clean_text,
                    'id'    => $id
                );
                
                // Track used IDs to avoid duplicates
                $used_ids[] = $id;
            }
        }
        
        return array(
            'headings' => $headings,
            'content'  => $content
        );
    }
    
    /**
     * Generate a unique ID for a heading
     */
    private static function generate_heading_id( $text, $used_ids ) {
        // Create a base ID from the heading text
        $base_id = 'toc-' . sanitize_title( $text );
        
        // Make sure it's unique
        $id = $base_id;
        $counter = 1;
        
        while ( in_array( $id, $used_ids ) ) {
            $id = $base_id . '-' . $counter++;
        }
        
        return $id;
    }
    
    /**
     * Generate TOC HTML
     */
    private static function generate_toc_html( $headings ) {
        $toc_title = get_theme_mod( 'custom_theme_toc_title', __( 'Table of Contents', 'custom-theme' ) );
        $toc_width = get_theme_mod( 'custom_theme_toc_width', '100' );
        $show_toggle = get_theme_mod( 'custom_theme_toc_toggle', true );
        
        // Start TOC HTML with container
        $html = '<div class="custom-theme-toc-container" style="width: ' . esc_attr( $toc_width ) . '%;">';
        
        // Add TOC header with title and toggle button
        $html .= '<div class="custom-theme-toc-header">';
        $html .= '<h2>' . esc_html( $toc_title ) . '</h2>';
        
        if ( $show_toggle ) {
            $html .= '<button class="custom-theme-toc-toggle" aria-expanded="true" aria-label="' . esc_attr__( 'Toggle Table of Contents', 'custom-theme' ) . '">';
            $html .= '<span class="screen-reader-text">' . esc_html__( 'Toggle', 'custom-theme' ) . '</span>';
            $html .= '<span class="custom-theme-toc-icon"></span>';
            $html .= '</button>';
        }
        
        $html .= '</div>'; // End .custom-theme-toc-header
        
        // List container
        $html .= '<nav class="custom-theme-toc-list-container">';
        $html .= self::build_hierarchical_toc_list( $headings );
        $html .= '</nav>'; // End .custom-theme-toc-list-container
        
        $html .= '</div>'; // End .custom-theme-toc-container
        
        return $html;
    }
    
    /**
     * Build the hierarchical TOC list
     */
    private static function build_hierarchical_toc_list( $headings ) {
        // Early exit if no headings
        if ( empty( $headings ) ) {
            return '';
        }
        
        // Initialize variables
        $html = '<ol class="custom-theme-toc-list">';
        $stack = array(); // Stack to track the current nesting level
        
        foreach ( $headings as $heading ) {
            $level = $heading['level'];
            
            // Handle the first heading
            if ( empty( $stack ) ) {
                $stack[] = $level;
                $html .= '<li class="custom-theme-toc-list-item custom-theme-toc-level-' . esc_attr( $level ) . '">';
                $html .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="custom-theme-toc-link">' . esc_html( $heading['text'] ) . '</a>';
                continue;
            }
            
            // Current level in the stack
            $current_level = end( $stack );
            
            // If this heading is at a deeper level, open a new sub-list
            if ( $level > $current_level ) {
                $html .= '<ol class="custom-theme-toc-list-child">';
                $html .= '<li class="custom-theme-toc-list-item custom-theme-toc-level-' . esc_attr( $level ) . '">';
                $stack[] = $level;
            } 
            // If this heading is at a higher level (less nested), close the appropriate number of lists
            elseif ( $level < $current_level ) {
                // Close lists until we reach the appropriate level
                while ( !empty( $stack ) && end( $stack ) > $level ) {
                    $html .= '</li></ol>';
                    array_pop( $stack );
                }
                
                // Close the current item
                $html .= '</li>';
                
                // Start a new item at the current level
                $html .= '<li class="custom-theme-toc-list-item custom-theme-toc-level-' . esc_attr( $level ) . '">';
            } 
            // If this heading is at the same level, close the previous item and start a new one
            else {
                $html .= '</li><li class="custom-theme-toc-list-item custom-theme-toc-level-' . esc_attr( $level ) . '">';
            }
            
            // Add the link for this heading
            $html .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="custom-theme-toc-link">' . esc_html( $heading['text'] ) . '</a>';
        }
        
        // Close any remaining open lists
        while ( !empty( $stack ) ) {
            $html .= '</li>';
            
            // Only add </ol> if not the last item in the stack (main list)
            if ( count( $stack ) > 1 ) {
                $html .= '</ol>';
            }
            
            array_pop( $stack );
        }
        
        $html .= '</ol>';
        
        return $html;
    }
}

// Initialize the TOC class
add_action( 'after_setup_theme', array( 'Custom_Theme_TOC', 'init' ) );

/**
 * Sanitize checkbox settings
 */
function custom_theme_sanitize_checkbox( $checked ) {
    return ( isset( $checked ) && true === $checked ) ? true : false;
}

/**
 * Sanitize select settings
 */
function custom_theme_sanitize_select( $input, $setting ) {
    // Get the list of choices from the control
    $choices = $setting->manager->get_control( $setting->id )->choices;
    
    // Return input if valid or return default option
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}