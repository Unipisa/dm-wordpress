<?php
/**
 * Declaring widgets
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'widgets_init', 'unipi_widgets_init' );

if ( ! function_exists( 'unipi_widgets_init' ) ) {
	/**
	 * Initializes themes widgets.
	 */
	function unipi_widgets_init() {

		$cols = get_theme_mod( 'unipi_dynamic_widget_columns' );
        if(empty($cols) || trim($cols) == '') {
            $cols = 'col-md';
        }

		register_sidebar(
			array(
				'name'          => __( 'Right Sidebar', 'unipi' ),
				'id'            => 'right-sidebar',
				'description'   => __( 'Right sidebar widget area', 'unipi' ),
				'before_widget' => '<div id="%1$s" class="box widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Hero Canvas', 'unipi' ),
				'id'            => 'herocanvas',
				'description'   => __( 'Full size canvas hero area for Bootstrap and other custom HTML markup', 'unipi' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Top Full', 'unipi' ),
				'id'            => 'statichero',
				'description'   => __( 'Full top widget with dynamic grid', 'unipi' ),
				'before_widget' => '<div id="%1$s" class="top-widget %2$s">',
				'after_widget'  => '</div><!-- .static-hero-widget -->',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Bottom Full', 'unipi' ),
				'id'            => 'bottomfull',
				'description'   => __( 'Full bottom widget with dynamic grid', 'unipi' ),
				'before_widget' => '<div id="%1$s" class="prefooter-widget %2$s '.$cols.'">',
				'after_widget'  => '</div><!-- .static-hero-widget -->',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer', 'unipi' ),
				'id'            => 'footer',
				'description'   => __( 'Full sized footer widget with dynamic grid', 'unipi' ),
				'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
				'after_widget'  => '</div><!-- .footer-widget -->',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);
                
		register_sidebar(
			array(
				'name'          => __( 'Privacy', 'unipi' ),
				'id'            => 'privacy',
				'description'   => __( 'Footer privacy', 'unipi' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
			)
		);

	}
} // endif function_exists( 'unipi_widgets_init' ).
