<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {


    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
    wp_enqueue_style( 'unipi-styles', get_template_directory_uri() . '/css/theme.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/css/custom.css?v=1.30',
        array( 'bootstrap', 'unipi-styles' ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'academicons', get_stylesheet_directory_uri() . '/css/academicons.min.css');

    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'unipi-child-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '1.22', true );
    
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

function add_child_theme_textdomain() {
    load_child_theme_textdomain( 'unipi-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

function get_ssds() {
    return [
        'MAT/01' => ['Logica Matematica', 'Mathematical Logic'],
        'MAT/02' => ['Algebra', 'Algebra'],
        'MAT/03' => ['Geometria', 'Geometry'],
        'MAT/04' => ['Didattica della Matematica e Storia della Matematica', 'Mathematics Education and History of Mathematics'],
        'MAT/05' => ['Analisi Matematica', 'Mathematical Analysis'],
        'MAT/06' => ['Probabilità e Statistica Matematica', 'Probability and Mathematical Statistics'],
        'MAT/07' => ['Fisica Matematica', 'Mathematical Physics'],
        'MAT/08' => ['Analisi Numerica', 'Numerical Analysis'],
    ];
}

function get_ssd($ssd, $lang = 'it') {
    $ret = $ssd;

    $ssds = get_ssds();
    
    if(isset($ssds[$ssd])) {
        $i = 0;

        if($lang != 'it'){
            $i= 1;
        }
        $ret = $ssds[$ssd][$i];
    }

    return $ret;
}

$unipi_includes = array(
    '/setupchild.php',                      // Child theme setup and custom theme supports.
    '/people.php',
    '/grants.php',
    '/visitors.php',
    '/events.php',
    '/page-walker.php',
    '/unimap.php',
);

foreach ( $unipi_includes as $file ) {
    $filepath = locate_template( 'inc' . $file );
    if ( ! $filepath ) {
        trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
    }
    require_once $filepath;
}

add_filter( 'wp_is_application_passwords_available', '__return_true' );


// sottotitolo
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
    'key' => 'group_6260741e5d64b',
    'title' => 'Sottotitolo',
    'fields' => array(
        array(
            'key' => 'field_6260742569420',
            'label' => 'Sottotitolo',
            'name' => 'subtitle',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
));

endif;

// Lista figli DM

function list_child_pages_dm($atts) { 
    global $post;

    extract(shortcode_atts(array(
        'pid' => $post->ID,
        'class' => '',
        'depth' => 0,
        'sort' => 'ASC',
    ), $atts));

    $defaults = array(
        'depth'        => $depth,
        'show_date'    => '',
        'date_format'  => get_option( 'date_format' ),
        'child_of'     => $pid,
        'echo'         => 0,
        'authors'      => '',
        'sort_column'  => 'menu_order, post_title',
        'sort_order' => $sort,
        'walker'       => new Walker_Page_Dm(),
        'title_li'     => '',
    );

    if (get_post_type($pid) === 'page') {
        $childpages = wp_list_pages( $defaults );
    }
    $string = '';
    if ( $childpages ) {
        $string = '<ul class="childlist ' . $class . '">' . $childpages . '</ul>';
    }
    return $string;
}
add_shortcode('listafiglidm', 'list_child_pages_dm');

/* Events fields */
add_action( 'rest_api_init', function () {

    register_rest_field( 'unipievents', 'unipievents_startdate', array(
        'get_callback' => function( $post_arr ) {
            $post_obj = get_post( $post_arr['id'] );
            return (int) $post_obj->unipievents_startdate;
        },
        'update_callback' => function( $unipievents_startdate, $post_obj ) {
            $mt = get_post_meta($post_obj->ID, "unipievents_startdate", true);
            if($mt != $unipievents_startdate) {
                $ret = update_post_meta($post_obj->ID, "unipievents_startdate", $unipievents_startdate );
            } else {
                $ret = true;
            }
            if ( false === $ret ) {
                return new WP_Error(
                  'rest_unipievents_startdate_failed',
                  __( 'Failed to update event start date.' ),
                  array( 'status' => 500 )
                );
            }
            return true;
        },
        'schema' => array(
            'description' => __( 'Event start date.' ),
            'type'        => 'integer'
        ),
    ) );

    register_rest_field( 'unipievents', 'unipievents_enddate', array(
        'get_callback' => function( $post_arr ) {
            $post_obj = get_post( $post_arr['id'] );
            return (int) $post_obj->unipievents_enddate;
        },
        'update_callback' => function( $unipievents_enddate, $post_obj ) {
            $mt = get_post_meta($post_obj->ID, "unipievents_enddate", true);
            if($mt != $unipievents_enddate) {
                $ret = update_post_meta($post_obj->ID, "unipievents_enddate", $unipievents_enddate );
            } else {
                $ret = true;
            }
            if ( false === $ret ) {
                return new WP_Error(
                  'rest_unipievents_enddate_failed',
                  __( 'Failed to update event end date.' ),
                  array( 'status' => 500 )
                );
            }
            return true;
        },
        'schema' => array(
            'description' => __( 'Event end date.' ),
            'type'        => 'integer'
        ),
    ) );

    register_rest_field( 'unipievents', 'unipievents_place', array(
        'get_callback' => function( $post_arr ) {
            $post_obj = get_post( $post_arr['id'] );
            return $post_obj->unipievents_place;
        },
        'update_callback' => function( $unipievents_place, $post_obj ) {
            $mt = get_post_meta($post_obj->ID, "unipievents_place", true);
            if($mt != $unipievents_place) {
                $ret = update_post_meta($post_obj->ID, "unipievents_place", $unipievents_place );
            } else {
                $ret = true;
            }
            if ( false === $ret ) {
                return new WP_Error(
                  'rest_unipievents_place_failed',
                  __( 'Failed to update event place.' ),
                  array( 'status' => 500 )
                );
            }
            return true;
        },
        'schema' => array(
            'description' => __( 'Event place.' ),
            'type'        => 'string'
        ),
    ) );

    register_rest_field( 'unipievents', 'unipievents_externalid', array(
        'get_callback' => function( $post_arr ) {
            $post_obj = get_post( $post_arr['id'] );
            return (int) $post_obj->unipievents_externalid;
        },
        'update_callback' => function( $unipievents_externalid, $post_obj ) {
            $mt = get_post_meta($post_obj->ID, "unipievents_externalid", true);
            if($mt != $unipievents_externalid) {
                $ret = update_post_meta($post_obj->ID, "unipievents_externalid", $unipievents_externalid );
            } else {
                $ret = true;
            }
            if ( false === $ret ) {
                return new WP_Error(
                  'rest_unipievents_externalid_failed',
                  __( 'Failed to update event external id.' ),
                  array( 'status' => 500 )
                );
            }
            return true;
        },
        'schema' => array(
            'description' => __( 'Event external id.' ),
            'type'        => 'integer'
        ),
    ) );
} );

add_filter( 'rest_unipievents_query', 'filter_unipievents_by_externalid_field', 999, 2 );
function filter_unipievents_by_externalid_field( $args, $request ) {
    if ( ! isset( $request['externalid'] )  ) {
        return $args;
    }
    
    $externalid_value = sanitize_text_field( $request['externalid'] );
    $externalid_meta_query = array(
        'key' => 'unipievents_externalid',
        'value' => $externalid_value
    );
    
    if ( isset( $args['meta_query'] ) ) {
        $args['meta_query']['relation'] = 'AND';
        $args['meta_query'][] = $externalid_meta_query;
    } else {
        $args['meta_query'] = array();
        $args['meta_query'][] = $externalid_meta_query;
    }
    
    return $args;
}

// wpml has_category fix
function wp_has_category ($category) {
    $category = get_term_by('term_id', $category, 'category');
    return has_category($category);
}
add_filter('wp_has_category', 'wp_has_category', 10, 1);

// FIX login for private site
add_filter( 'members_is_private_page', function( $is_private ) {

    return is_page( 'login' ) ? false : $is_private;

} );

/* Option page */
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page();
    
}

function phdhistory($atts)
{
    extract(shortcode_atts(array(
        'class' => '',
    ), $atts));
    $f = __DIR__.'/out.txt';
    if(file_exists($f)) {
        $cont = (string) @file_get_contents($f);
        return do_shortcode($cont);
    } else {
        return '';
    }
}

add_shortcode('phdhistory', 'phdhistory');

/* Logo fix */
function unipi_change_logo_class( $html ) {

    $html = str_replace( 'class="custom-logo"', 'class="custom-logo img-fluid"', $html );
    $html = str_replace( 'class="custom-logo-link"', 'class=" custom-logo-link"', $html );
    $html = str_replace( 'alt=""', 'title="Home" alt="logo"', $html );

    return $html;
}

/* remove excerpt empty content */
function unipi_all_excerpts_get_more_link( $post_excerpt ) {
    if ( ! is_admin() ) {
        if( trim($post_excerpt) != '') {
            $post_excerpt = '<p>' . $post_excerpt . '&hellip;</p><p><a class="btn btn-dark btn-sm unipi-read-more-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __( 'Read More...',
        'unipi' ) . '</a></p>';
        } else {
            $post_excerpt = '<p><a class="btn btn-dark btn-sm unipi-read-more-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __( 'Read More...',
        'unipi' ) . '</a></p>';
        }
        
    }
    return $post_excerpt;
}
