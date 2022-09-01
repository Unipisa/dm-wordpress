<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'init_setup' );
function init_setup() {

	/* Degrees menu */
    /*register_nav_menus(
        array(
            'l-inf' => __( 'Laurea in Informatica' ),
            'lm-wif' => __( 'Laurea Computer Science' ),
            'lm-wds' => __( 'Laurea Data Science and Business Informatics' ),
            'lm-win' => __( 'Laurea in Informatica e Networking' ),
            'navbar-right' => __( 'Menu principale (destra)' ),
        )
    );*/

    /* Degrees translations */
    /*__('Dipartimento di Informatica', 'unipi-child');
    __('Area Didattica', 'unipi-child');
    __('Area Ricerca', 'unipi-child');
    __('Area Dottorato', 'unipi-child');
    __('Area Amministrativa', 'unipi-child');*/

    /* Import settings */
    /*$str = array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true
    );
    $num = array(
        'type' => 'number',
        'single' => true,
        'show_in_rest' => true
    );

    register_meta( 'post', 'ws_oid', $num );
    register_meta( 'page', 'ws_oid', $num );

    register_meta( 'post', 'ws_omid', $num );
    register_meta( 'page', 'ws_omid', $num );

    register_meta( 'post', 'ws_ocatid', $num );
    register_meta( 'page', 'ws_ocatid', $num );

    register_meta( 'post', 'ws_down', $str );
    register_meta( 'page', 'ws_down', $str );

    register_meta( 'post', 'ws_access', $str );
    register_meta( 'page', 'ws_access', $str );

    register_meta( 'post', 'ws_featured', $str );
    register_meta( 'page', 'ws_featured', $str );

    register_meta( 'post', 'ws_lang', $str );
    register_meta( 'page', 'ws_lang', $str );*/
}

/* PAge admin custom columns */
/*function add_acf_columns ( $columns ) {
    return array_merge ( $columns, array (
        'categoria' => __ ( 'Categoria' ),
        'categ' => __ ( 'Categoria' ),
        'lng' => __ ( 'Lingua' ),
        'oid' => __ ( 'ID' ),
        'omid' => __ ( 'Menu ID' ),
    ) );
}
add_filter ( 'manage_page_posts_columns', 'add_acf_columns' );

function di_custom_column ( $column, $post_id ) {
    $cats = [
    '2' => 'Pagina',
    '8' => 'Organizzazione',
    '39' => 'Organizzazione > Avvisi e bandi',
    '16' => 'Organization',
    '80' => 'Organization > Announcements and calls',
    '9' => 'Didattica',
    '26' => 'Didattica > inf',
    '31' => 'Didattica > inf > News inf',
    '23' => 'Didattica > wif',
    '32' => 'Didattica > wif > News wif',
    '27' => 'Didattica > wea',
    '33' => 'Didattica > wea > News wea',
    '96' => 'Didattica > wea > Interviste laureati',
    '28' => 'Didattica > win',
    '34' => 'Didattica > win > News win',
    '30' => 'Didattica > News didattica',
    '45' => 'Didattica > Erasmus',
    '46' => 'Didattica > Erasmus > News Erasmus',
    '66' => 'Didattica > share',
    '84' => 'Didattica > masterapp',
    '85' => 'Didattica > masterapp > News masterapp',
    '87' => 'Didattica > Formazione permanente',
    '90' => 'Didattica > masterapp-2014/15',
    '91' => 'Didattica > masterapp-2014/15 > News masterapp 2014/15',
    '17' => 'Education',
    '10' => 'Ricerca',
    '40' => 'Ricerca > News ricerca',
    '54' => 'Ricerca > Gruppi',
    '53' => 'Ricerca > Progetti di ricerca',
    '99' => 'Ricerca > Partner di ricerca',
    '18' => 'Research',
    '100' => 'Research > Research partner',
    '11' => 'Servizi',
    '19' => 'Services',
    '12' => 'Persone',
    '20' => 'People',
    '13' => 'Eventi',
    '21' => 'Events',
    '22' => 'PhD',
    '60' => 'PhD > Students seminars',
    '29' => 'PhD > PhD news',
    '51' => 'PhD > Courses',
    '65' => 'PhD > Courses > 2014',
    '64' => 'PhD > Courses > 2013',
    '89' => 'PhD > Courses > 2015',
    '95' => 'PhD > Courses > 2016',
    '98' => 'PhD > Courses > 2017',
    '102' => 'PhD > Courses > 2018',
    '52' => 'PhD > Schools',
    '55' => 'PhD > Students seminars',
    '67' => 'PhD > Documents',
    '68' => 'PhD > Documents > Allegati',
    '24' => 'Dipartimento',
    '25' => 'Le sfide dell\'Informatica',
    '70' => 'Computer science challenges blog',
    '35' => 'Verbali',
    '36' => 'Verbali > Delibere del Consiglio',
    '37' => 'Verbali > Verbali della Giunta',
    '38' => 'Verbali > delibere-inf',
    '44' => 'Ricerca- sottocategoria',
    '81' => 'PhD Guidelines',
    '83' => 'Archivio dei bandi scaduti',
    '93' => 'Documenti riservati',
    ];
    switch ( $column ) {
        case 'categ':
            $catid = get_post_meta ( $post_id, 'ws_ocatid', true );
            if(isset($cats[$catid])) {
                echo $cats[$catid];
            } else {
                echo $catid;
            }
            break;
        case 'lng':
            echo get_post_meta ( $post_id, 'ws_lang', true );
            break;
        case 'omid':
            echo get_post_meta ( $post_id, 'ws_omid', true );
            break;
        case 'oid':
            echo get_post_meta ( $post_id, 'ws_oid', true );
            break;
    }
}
add_action ( 'manage_page_posts_custom_column', 'di_custom_column', 10, 2 );

add_filter( 'manage_edit-page_sortable_columns', 'my_sortable_cake_column' );
function my_sortable_cake_column( $columns ) {
    $columns['categ'] = 'categ'; 
    return $columns;
}

add_action( 'pre_get_posts', 'di_columns_orderby' );
function di_columns_orderby( $query ) {
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby');
 
    if( 'categ' == $orderby ) {
        $query->set('meta_key','ws_ocatid');
        $query->set('orderby','meta_value_num');
    }
}
*/

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
