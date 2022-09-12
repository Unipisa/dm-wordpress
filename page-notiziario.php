<?php
/**
 * Template Name: Notiziario
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
// seminari, conferenze
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body>
    <?php
    function print_events($cat, $tags = null) {
        $limit = 6;
        $layout = 'default';

        $current_blog_id = get_current_blog_id();
        if($current_blog_id != 1) {
            switch_to_blog(1);
        }

        $args = array(
            'post_type' => 'unipievents',
            'posts_per_page' => $limit,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'=>'unipievents_startdate',
                    'compare' => '>=',
                    'value' => time()
                ),
                array(
                    'key'=>'unipievents_startdate',
                    'compare' => '<=',
                    'value' => time() + (7 * 24 * 60 * 60 + 3600 * 8)
                ),
            ),
            'meta_key'=>'unipievents_startdate',
            'orderby'=>'meta_value',
            'order'=>'ASC'
        );

        if($cat) {
            $cat = str_replace(' ', '', $cat);

            if($tags) {
                $args['tax_query'] = array(
                    'AND',
                    array(
                        'taxonomy' => 'unipievents_taxonomy',
                        'field'    => 'term_id',
                        'terms'    => (array) explode(',', $cat),
                        'operator' => 'IN',
                    ),
                    array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'term_id',
                        'terms'    => (array) explode(',', $tags), // A Pisa
                        'operator' => 'IN',
                    ),
                );
            } else {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'unipievents_taxonomy',
                        'field'    => 'term_id',
                        'terms'    => (array) explode(',', $cat),
                        'operator' => 'IN',
                    ),
                );
            }
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
            $ret[] = '<div>';
            $ret[] = '<h3 class="title entry-title"><a href="'.esc_url( get_permalink() ) .'" rel="bookmark">'.get_the_title().'</a></h3>';
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
            $ret[] ='<span class="publish-date">Data: ' . $dt . '</span>';
            if($clock != '00:00') {
                $ret[] = '<span class="hours"> - ore: '. $clock . '</span>';
            }
            if($location) {
                $ret[] = '<span class="location"> - luogo: ' . $location . '</span>';
            }
            /*if(has_term('', 'unipievents_taxonomy')) {
                $term_list = get_the_term_list( get_the_ID(), 'unipievents_taxonomy', '', ', ', '' );
                if ( !is_wp_error( $term_list ) ) {
                    $ret[] = '<span class="tag mr-2"><i class="fa fa-tags"></i> ' . apply_filters( 'the_terms', $term_list, 'unipievents_taxonomy', '', ', ', '' ) . '</span>';
                }
            }*/

            $ret[] = '</div>';

            if($layout == 'thumb') {
                $ret[] = '</div></div>';
            }
            if($layout == 'image') {
                $ret[] = '</div></div>';
            }
            $ret[] = '</div>';
            $ret[] = '<div style="height: 10px;">&nbsp;</div><div style="background-color: #ccc; height: 1px;"></div><div style="height: 10px;">&nbsp;</div>';
        }

        if($latest->found_posts == 0) {
            $ret[] = '- Nessun evento in programma - <br><br>';
        }

        wp_reset_postdata();

        restore_current_blog();

        echo implode("\n", $ret);
    }
    

    ?>
    <h2>Prossimi seminari</h2>
    <div style="height: 10px;">&nbsp;</div>
    <?php print_events(75) ?>
    <p>&nbsp;</p>

    <h2>Prossime conferenze a Pisa</h2>
    <div style="height: 10px;">&nbsp;</div>
    <?php print_events(90, 196) ?>
    <p>&nbsp;</p>
    
    <h2>Ultime notizie</h2>
    <div style="height: 10px;">&nbsp;</div>
    <?php
    $type = 'post';
    $limit = 20;
    $layout = 'default';
    $meta = 'both';
    $cat = false;
    $showcat = true;

    $args = array(
        'post_type' => $type,
        'posts_per_page' => $limit,
        'date_query' => array(
            'after' => date(DateTime::ISO8601, strtotime('-2 week'))
        )
    );

    if($showcat === 'true') $showcat = true;

    if($cat) {
        $args['cat'] = $cat;
    } else {
        $cat = rand();
    }
    
    $latest = new WP_Query($args);

    update_post_thumbnail_cache($latest);

    $ret = [];
    $count = 0;
    while ($latest->have_posts()) {
        $latest->the_post();

        if($count == 6) {
            break;
        }

        if(apply_filters( 'wp_has_category', 170 )) {
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
            $date1 = get_field('date1');
            $date2 = get_field('date2');
            $dtime = strtotime($date1);
            $dt = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
            $dt = $dt->format('Y-m-d H:i:s');
            /*$lbl = __('Open', 'unipi');
            if($date1 < $dt && $dt < $date2) {
                $lbl = __('In progress', 'unipi');
            }*/
            if($date1 < $dt) {
                continue;
            }
        }

        $ret[] = '<div>';
        $ret[] = '<h3 class="title entry-title"><a href="'.esc_url( get_permalink() ) .'" rel="bookmark">'.get_the_title().'</a></h3>';

        if($meta != 'none') {
            $by = ($meta == 'date') ? false : true;
            $cat = '';
            if($type === 'post' && $showcat) {
                $categories_list = get_the_category_list(esc_html__(', ', 'unipi'));
                if ($categories_list) {
                    $cat = ' - ' . sprintf('<span class="cat-links"> in %s</span>', $categories_list); // WPCS: XSS OK.
                }
            }
            $dt = unipi_posted_on(true, $by);
            //$dt = str_replace('<span class="far fa-calendar"></span>', '', $dt);
            //$dt = str_replace('sr-only', '', $dt);
            $dt = strip_tags($dt);
            $ret[] = '<div class="entry-meta small">' . $dt . $cat . '</div>';
        }
        $ret[] = '</div>';
        $ret[] = '<div style="height: 10px;">&nbsp;</div><div style="background-color: #ccc; height: 1px;"></div><div style="height: 10px;">&nbsp;</div>';
        $count++;
    }
    wp_reset_postdata();

    if($count == 0) {
        $ret[] = '- Nessuna news presente -';
    }

    echo implode("\n", $ret);
    ?>
    <?php while ( have_posts() ) : the_post(); ?>

        <?php //get_template_part( 'loop-templates/content', 'blank' ); ?>

    <?php endwhile; // end of the loop. ?>
    <?php wp_footer(); ?>
</body>
</html>
