<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {


    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
    wp_enqueue_style( 'unipi-styles', get_template_directory_uri() . '/css/theme.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/css/custom.css?v=1.22',
        array( 'bootstrap', 'unipi-styles' ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'academicons', get_stylesheet_directory_uri() . '/css/academicons.min.css');

    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'unipi-child-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '1.21', true );
    
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

function add_child_theme_textdomain() {
    load_child_theme_textdomain( 'unipi-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

$unipi_includes = array(
    '/setupchild.php',                      // Child theme setup and custom theme supports.
    '/people.php',
    '/grants.php',
    '/events.php',
    //'/common-shortcodes.php',               // Common shortcodes
    //'/ws-esami.php',                        // Retrieve data from esami.di.unipi.it
    //'/ws-unipi.php',                        // Retrieve data from unipi.it
    //'/ws-get.php',                          // Generic remote getter
    //'/ws-ricev.php',                        // WebService Ricevimenti
);

foreach ( $unipi_includes as $file ) {
    $filepath = locate_template( 'inc' . $file );
    if ( ! $filepath ) {
        trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
    }
    require_once $filepath;
}