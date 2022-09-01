<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Shortcode */
function create_row( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'class' => 'row',
    ), $atts ) );

    return '<div class="'.$class.'">' . do_shortcode($content) . '</div>';

}
add_shortcode('row', 'create_row');

function create_col( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'class' => 'col-md',
    ), $atts ) );

    return '<div class="'.$class.'">' . do_shortcode($content) . '</div>';

}
add_shortcode('col', 'create_col');