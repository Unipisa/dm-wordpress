<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*add_action( 'init', 'init_setup' );
function init_setup() {

	
}*/


/* Menu page search */
add_action( 'pre_get_posts', 'menu_filter', 10, 2 );
function menu_filter( $q ) {
    if(isset($_POST['action']) && $_POST['action']=="menu-quick-search" && isset($_POST['menu-settings-column-nonce'])){    
        if( is_a($q->query_vars['walker'], 'Walker_Nav_Menu_Checklist') ){
            $q->query_vars['posts_per_page'] =  100;
        }
    }
    return $q;
}

/* Custom breadcrumb */
function unipi_yoast_breadcrumb_bootstrap() {
    if ( function_exists( 'yoast_breadcrumb' ) ) {
        $breadcrumb = yoast_breadcrumb(
            '<ol class="breadcrumb"><li class="breadcrumb-item">',
            '</li></ol>',
            false
        );
        $breadcrumb = str_replace( '<span>', '', $breadcrumb );
        $breadcrumb = str_replace( '</span>', '', $breadcrumb );
        $breadcrumb = str_replace( '»', '</li><li class="breadcrumb-item">', $breadcrumb );
        $breadcrumb = str_replace( '&raquo;', '</li><li class="breadcrumb-item">', $breadcrumb );
        if(is_main_site()) {
            $breadcrumb = str_replace('Home', '<span class="fas fa-home"></span><span class="sr-only">Home</span>', $breadcrumb);
        } else {
            $subsite = (array) explode('-', get_bloginfo('name'));
            $breadcrumb = str_replace('Home', '<span class="fas fa-home"></span><span class="sr-only">Home</span> ' . trim(current($subsite)), $breadcrumb);
        }
        echo $breadcrumb;
    }
}

/* Switch shortcode */

function switch_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'id' => 1,
    ), $atts ) );
    global $blog_id;
    $current_blog_id = $blog_id;
    switch_to_blog($id); 
    $ret = do_shortcode($content);
    switch_to_blog($current_blog_id);
    return $ret;
}
add_shortcode( 'switch', 'switch_shortcode' );

/* Entra shortcode */

function entra_shortcode( $atts, $content = null ) {
    $ret = '<a href="'. wp_login_url( get_permalink() ) . '" title="Login">'. __('Esegui l\'accesso da rete di ateneo o VPN', 'unipi-child') .'</a>';
    return $ret;
}
add_shortcode( 'entra', 'entra_shortcode' );

/**/

/* Parent page select2 */
function enqueue_select2_jquery() {
    wp_register_style( 'select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
    wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'select2css' );
    wp_enqueue_script( 'select2' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_select2_jquery' );

function select2jquery_inline() {
    ?>
<style type="text/css">
.select2-container {margin: 0 2px 0 2px; width: 100%;}
.tablenav.top #doaction, #doaction2, #post-query-submit {margin: 0px 4px 0 4px;}
</style>
<script type='text/javascript'>
jQuery(document).ready(function ($) {
    if( $( '#parent_id' ).length > 0 ) {
        $( '#parent_id' ).select2();
        /*$( document.body ).on( "click", function() {
             $( '#parent_id' ).select2();
          });*/
    }
});
</script>
    <?php
 }
add_action( 'admin_head', 'select2jquery_inline' );
