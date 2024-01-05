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
        $limit = 100;
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
                    'value' => time() + (7 * 24 * 60 * 60)
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

    <?php
    function print_seminars() {
        $tomorrow = date('Y-m-d', time() + 86400);
        $nextweek = date('Y-m-d', time() + 86400 * 8);

        $h = curl_init();

        curl_setopt($h, CURLOPT_URL, "https://manage.dm.unipi.it/api/v0/public/seminars?from={$tomorrow}&to={$nextweek}");
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

        $seminars = curl_exec($h);
        $seminars = json_decode($seminars);
        $seminars = $seminars->data;
        curl_close($h);

        $seminar_block = "";

        // Sort seminars by increasing datetime
        usort($seminars, function ($a, $b) {
            $dateA = new DateTimeImmutable($a->startDatetime);
            $dateB = new DateTimeImmutable($b->startDatetime);
            return $dateA->getTimestamp() - $dateB->getTimestamp();
        });

        foreach ($seminars as $s) {
            $speaker = $s->speaker->firstName . " " . $s->speaker->lastName;
            $d = (new DateTimeImmutable($s->startDatetime))->setTimeZone(new DateTimeZone("Europe/Rome"));
            $day = $d->format('Y-m-d');
            $starttime = $d->format('H:i');
            $endtime = $d->add(new DateInterval("PT" . $s->duration . "M"))->format('H:i');
            $location = $s->conferenceRoom->name;
            $url = "https://www.dm.unipi.it/seminario/?id={$s->_id}";
            $seminar_block .= <<<END
            <div>
                <h3 class="title entry-title">
                    <a href="$url" rel="bookmark">
                        $s->title - $speaker
                    </a></h3>
                <div class="entry-meta small">
                    <span class="publish-date">Data: $day</span>
                    <span class="hours"> - ore: $starttime - $endtime</span>
                    <span class="location"> - luogo: $location</span>
                </div>
            </div>
            END;
        }

        if (count($seminars) == 0) { 
            $seminar_block = "<p>- Nessun evento in programma -</p>";
        }

        return $seminar_block;
    }

    function print_conferences() {
        $tomorrow = date('Y-m-d', time() + 86400);
        $nextweek = date('Y-m-d', time() + 86400 * 8);

        $h = curl_init();

        curl_setopt($h, CURLOPT_URL, "https://manage.dm.unipi.it/api/v0/public/conferences?from={$tomorrow}&to={$nextweek}");
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

        $conferences = curl_exec($h);
        $conferences = json_decode($conferences);
        $conferences = $conferences->data;
        curl_close($h);

        $conference_block = "";

        // Sort seminars by increasing datetime
        usort($conferences, function ($a, $b) {
            $dateA = new DateTimeImmutable($a->startDate);
            $dateB = new DateTimeImmutable($b->startDate);
            return $dateA->getTimestamp() - $dateB->getTimestamp();
        });

        foreach ($conferences as $c) {
            $starttime = (new DateTimeImmutable($c->startDate))->setTimeZone(new DateTimeZone("Europe/Rome"));
            $starttime = $starttime->format('Y-m-d');
            $endtime   = (new DateTimeImmutable($c->endDate))->setTimeZone(new DateTimeZone("Europe/Rome"));
            $endtime   = $endtime->format('Y-m-d');
            $location = $c->conferenceRoom->name;
            $url = "https://www.dm.unipi.it/conferenza/?id={$c->_id}";
            $conference_block .= <<<END
            <div>
                <h3 class="title entry-title">
                    <a href="$url" rel="bookmark">
                        $c->title
                    </a></h3>
                <div class="entry-meta small">
                    <span class="publish-date">Date: $starttime &mdash; $endtime</span>
                    <span class="location"> - luogo: $location</span>
                </div>
            </div>
            END;
        }

        if (count($conferences) == 0) { 
            $conference_block = "<p>- Nessun evento in programma -</p>";
        }

        return $conference_block;
    }

    ?>
    <h2>Prossimi seminari</h2>
    <div style="height: 10px; margin-top: -10px;">&nbsp;</div>
    <?php // print_events(75) ?>
    <?php echo print_seminars() ?>
    <p>&nbsp;</p>

    <h2>Prossime conferenze a Pisa</h2>
    <div style="height: 10px; margin-top: -10px;">&nbsp;</div>
    <?php // print_events(90, 196) ?>
    <?php echo print_conferences() ?>
    <p>&nbsp;</p>
    
    <h2>Ultime notizie</h2>
    <div style="height: 10px; margin-top: -10px;">&nbsp;</div>
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
