/**
 * File customizer.js - OPTIMIZED.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
    // Cache jQuery selectors for better performance
    var $siteTitle = $( '.site-title a' );
    var $siteDescription = $( '.site-description' );
    
    // Site title
    wp.customize( 'blogname', function( value ) {
        value.bind( function( to ) {
            $siteTitle.text( to );
        } );
    });
    
    // Site description
    wp.customize( 'blogdescription', function( value ) {
        value.bind( function( to ) {
            $siteDescription.text( to );
        } );
    });
    
    // Header text color
    wp.customize( 'header_textcolor', function( value ) {
        value.bind( function( to ) {
            if ( 'blank' === to ) {
                $siteTitle.add($siteDescription).css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $siteTitle.add($siteDescription).css({
                    'clip': 'auto',
                    'position': 'relative',
                    'color': to
                });
            }
        });
    });
} )( jQuery );
