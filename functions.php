<?php
/**
 * Theme functions and definitions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define theme version
define( 'CUSTOM_THEME_VERSION', '1.0.0' );

// Set up theme defaults and register support for various WordPress features
function custom_theme_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support( 'post-thumbnails' );

    // Register main menu
    register_nav_menus(
        array(
            'menu-1' => esc_html__( 'Primary', 'custom-theme' ),
        )
    );

    // Switch default core markup to valid HTML5
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );
    
    // Add support for block editor features
    add_theme_support('editor-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
}
add_action( 'after_setup_theme', 'custom_theme_setup' );

// Enqueue scripts and styles
function custom_theme_scripts() {
    // Properly enqueue main stylesheet separately instead of using @import
    wp_enqueue_style( 'custom-theme-main', get_template_directory_uri() . '/assets/css/main.css', array(), CUSTOM_THEME_VERSION );
    wp_enqueue_style( 'custom-theme-style', get_stylesheet_uri(), array('custom-theme-main'), CUSTOM_THEME_VERSION );
    
    // Add jQuery as dependency if it's used in script.js
    wp_enqueue_script( 'custom-theme-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), CUSTOM_THEME_VERSION, true );
    
    // Only load comment-reply script when needed
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );

// Add resource hints for performance
function custom_theme_resource_hints($hints, $relation_type) {
    if ('preconnect' === $relation_type) {
        // Add resource hints for external fonts/resources if used
        $hints[] = array(
            'href' => '//fonts.googleapis.com',
            'crossorigin',
        );
    }
    return $hints;
}
add_filter('wp_resource_hints', 'custom_theme_resource_hints', 10, 2);

// Include additional functionality
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/customizer.php';
