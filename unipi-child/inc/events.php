<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Upcoming events */
function upcomingeventsms($atts) {
    extract(shortcode_atts(array(
        'limit' => 6,
        'layout' => 'default', // image, thumb
        'cat' => false,
    ), $atts));

    $current_blog_id = get_current_blog_id();
    if($current_blog_id != 1) {
        switch_to_blog(1);
    }

    $args = array(
        'post_type' => 'unipievents',
        'posts_per_page' => $limit,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key'=>'unipievents_startdate',
                'compare' => '>=',
                'value' => time()
            ),
            array(
                'key'=>'unipievents_enddate',
                'compare' => '>=',
                'value' => time()
            ),
        ),
        'meta_key'=>'unipievents_startdate',
        'orderby'=>'meta_value',
        'order'=>'ASC'
    );

    if($cat) {
        $cat = str_replace(' ', '', $cat);
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'unipievents_taxonomy',
                'field'    => 'term_id',
                'terms'    => (array) explode(',', $cat),
                'operator' => 'IN',
            ),
        );
    }
        
    $latest = new WP_Query($args);

    update_post_thumbnail_cache($latest);

    $ret = [];
    while ($latest->have_posts()) {
        $latest->the_post();

        $custom = get_post_custom(get_the_ID());
        $sd = $custom["unipievents_startdate"][0];
        $ed = $custom["unipievents_enddate"][0];

        $date_format = get_option('date_format');

        $stimestd = date_i18n('Y-m-d', $sd);
        $etimestd = date_i18n('Y-m-d', $ed);

        $dt = date_i18n($date_format, $sd);
        if($stimestd != $etimestd) {
            $dt .= ' - ' . date_i18n($date_format, $ed);
        }

        // - local time format -
        $time_format = get_option('time_format');
        $stime = date_i18n($time_format, $sd);
        $etime = date_i18n($time_format, $ed);
        $clock = $stime;
        if($stime != $etime) {
            $clock .= ' - ' . $etime;
        }

        $location = $custom["unipievents_place"][0];
        $ret[] = '<article>';
        if($layout == 'thumb') {
            $ret[] = '<div><div class="media mb-1">';
            if (has_post_thumbnail()) {
                $ret[] = '<img src="'.get_the_post_thumbnail_url(get_the_ID(), 'thumbnail').'" class="mr-3 tbsm" alt="' . esc_html ( get_the_post_thumbnail_caption() ) . '">';
            } else {
                $ret[] = '<img src="'.get_stylesheet_directory_uri().'/images/bgnews.png" class="mr-3 tbsm" alt="article default image">';
            }
            $ret[] = '<div class="media-body">';
        }
        if($layout == 'image') {
            $ret[] = '<div><div class="media">';
            if (has_post_thumbnail()) {
                $ret[] = '<img src="'.get_the_post_thumbnail_url(get_the_ID(), 'thumbnail').'" class="mr-3 mb-3 tblg" alt="' . esc_html ( get_the_post_thumbnail_caption() ) . '">';
            } else {
                $ret[] = '<img src="'.get_stylesheet_directory_uri().'/images/bgnews.png" class="mr-3 tblg" alt="article default image">';
            }
            $ret[] = '<div class="media-body">';
        }
        $ret[] = '<div class="entry-meta small">';

        if($ed < strtotime('today')) {
            $ret[] = '<span class="badge badge-primary archivio mr-2">Archivio</span>';
        }
        $ret[] ='<span class="publish-date mr-2"><span class="far fa-calendar"></span> ' . $dt . '</span>';
        if($clock != '00:00') {
            $ret[] = '<span class="hours mr-2"><span class="far fa-clock"></span> '. $clock . '</span>';
        }
        if($location) {
            $ret[] = '<span class="location mr-2"><span class="fas fa-map-marker-alt"></span> ' . $location . '</span>';
        }
        if(has_term('', 'unipievents_taxonomy')) {
            $term_list = get_the_term_list( get_the_ID(), 'unipievents_taxonomy', '', ', ', '' );
            if ( !is_wp_error( $term_list ) ) {
                $ret[] = '<span class="tag mr-2"><i class="fa fa-tags"></i> ' . apply_filters( 'the_terms', $term_list, 'unipievents_taxonomy', '', ', ', '' ) . '</span>';
            }
        }

        $ret[] = '</div>';

        $ret[] = '<h3 class="title entry-title"><a href="'.esc_url( get_permalink() ) .'" rel="bookmark">'.get_the_title().'</a></h3>';
        if($layout == 'thumb') {
            $ret[] = '</div></div>';
        }
        $ret[] = get_the_excerpt();
        if($layout == 'image') {
            $ret[] = '</div></div>';
        }
        $ret[] = '</article>';
    }
    wp_reset_postdata();

    restore_current_blog();

    return implode("\n", $ret);
}

add_shortcode('eventsmulti', 'upcomingeventsms');