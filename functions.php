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
}
add_action( 'after_setup_theme', 'custom_theme_setup' );

// Enqueue scripts and styles
function custom_theme_scripts() {
    // Main stylesheet
    wp_enqueue_style( 'custom-theme-style', get_stylesheet_uri(), array(), CUSTOM_THEME_VERSION );
    
    // Main JavaScript
    wp_enqueue_script( 'custom-theme-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), CUSTOM_THEME_VERSION, true );
    
    // Only load TOC assets on single posts and pages
    if ( is_singular() && ! is_admin() ) {
        // Check if TOC is enabled in customizer
        if ( get_theme_mod( 'custom_theme_toc_enable', true ) ) {
            // Check if TOC is not disabled for this post
            if ( ! get_post_meta( get_the_ID(), '_custom_theme_disable_toc', true ) ) {
                wp_enqueue_style( 'custom-theme-toc', get_template_directory_uri() . '/assets/css/toc.css', array(), CUSTOM_THEME_VERSION );
                wp_enqueue_script( 'custom-theme-toc', get_template_directory_uri() . '/assets/js/toc.js', array('jquery'), CUSTOM_THEME_VERSION, true );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );

// Enqueue admin-specific scripts
function custom_theme_admin_scripts( $hook ) {
    // Only load on post edit screens
    if ( $hook == 'post.php' || $hook == 'post-new.php' ) {
        wp_enqueue_script( 
            'custom-theme-toc-admin', 
            get_template_directory_uri() . '/assets/js/toc-admin.js', 
            array( 'jquery' ), 
            CUSTOM_THEME_VERSION, 
            true 
        );
    }
}
add_action( 'admin_enqueue_scripts', 'custom_theme_admin_scripts' );

// Include additional functionality
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/toc-functions.php';
