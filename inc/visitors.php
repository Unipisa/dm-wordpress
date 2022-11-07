<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Latest post */
function visitorsshortcode($atts) {
    extract(shortcode_atts(array(
        'type' => 'visitors',
        'limit' => 100,
        'tags' => false,
        'lang' => 'it',
        'filters' => false,
    ), $atts));

    $current_blog_id = get_current_blog_id();
    if($current_blog_id != 6) {
    	switch_to_blog(6);
    }

    $args = array(
        'post_type' => $type,
        'posts_per_page' => $limit,
        //'orderby' => 'post_title',
      	//'order' => 'ASC'
    );

    $visitors = new WP_Query($args);
    update_post_thumbnail_cache($visitors);

    $ret = [];
    while ($visitors->have_posts()) {
        $visitors->the_post();

        if (class_exists('ACF')) {
        	$aux = [];
        	$acf = get_fields(get_the_ID());

                array_push($ret,
                    "<li>" .
                      '<h5 class="mb-0 font-weight-bold">' . $acf["nome"] . " " . $acf["cognome"] .
                      ' <small class="text-muted">(' . "from " . $acf["inizio_visita"] . " to " . $acf['fine_visita'] . ")</small></h5>" .
                      '<p class="mb-0">' .
                        '<em>' . $acf["affiliazione"] . '.</em>' .
                      '</p>' .
                    "</li>"
                );
        	// var_dump($acf);
        }
    }
    wp_reset_postdata();

    $ret = $pre . '<ul class="visitorslist">' . implode("\n", $ret) . '</ul>';
    return $ret;
}

add_shortcode('visitors', 'visitorsshortcode');
