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
    wp_enqueue_style( 'custom-theme-style', get_stylesheet_uri(), array(), CUSTOM_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );
