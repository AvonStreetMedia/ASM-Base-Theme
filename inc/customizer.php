<?php
/**
 * Theme Customizer
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function custom_theme_customize_register( $wp_customize ) {
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

    // Add primary color setting
    $wp_customize->add_setting(
        'primary_color',
        array(
            'default'           => '#0073aa',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'primary_color',
            array(
                'label'    => __( 'Primary Color', 'custom-theme' ),
                'section'  => 'colors',
                'settings' => 'primary_color',
            )
        )
    );

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector'        => '.site-title a',
                'render_callback' => 'custom_theme_customize_partial_blogname',
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector'        => '.site-description',
                'render_callback' => 'custom_theme_customize_partial_blogdescription',
            )
        );
    }
}
add_action( 'customize_register', 'custom_theme_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function custom_theme_customize_partial_blogname() {
    bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function custom_theme_customize_partial_blogdescription() {
    bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function custom_theme_customize_preview_js() {
    wp_enqueue_script( 'custom-theme-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), CUSTOM_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'custom_theme_customize_preview_js' );

/**
 * Generate CSS for the primary color.
 */
function custom_theme_customize_css() {
    $primary_color = get_theme_mod( 'primary_color', '#0073aa' );
    ?>
    <style type="text/css">
        .main-navigation a:hover,
        .entry-title a:hover,
        .read-more {
            color: <?php echo esc_attr( $primary_color ); ?>;
        }
        .read-more {
            background-color: <?php echo esc_attr( $primary_color ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'custom_theme_customize_css' );
