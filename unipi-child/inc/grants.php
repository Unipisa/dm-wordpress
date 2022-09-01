<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Latest post */
function grantsshortcode($atts) {
    extract(shortcode_atts(array(
        'type' => 'grant',
        'limit' => 100,
        'tags' => false,
        'table' => false,
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
        'meta_query' => array(
	        array(
	            'tipologia_progetto_clause' => array(
	                'key'       => 'tipologia_progetto',
	                'compare'   => 'EXISTS',
	            ),
	        )
	    ),
	    'orderby' => array(
	        'tipologia_progetto_clause' => 'ASC',
	        'post_title' => 'ASC',
	    )
    );

    if($tags) {

    	$tagslist = (array) explode(',', $tags);
	    foreach ($tagslist as $k => $f) {
	    	$tagslist[$k] = trim($f);
	    }
	    
	    $tofilter = [];
	    foreach ($tagslist as $t) {
	    	$tofilter[] = [
	    		'taxonomy' => 'granttag',
	            'field' => 'slug',
	            'terms' => $t,
	    	];
	    }

    	$args['tax_query'] = $tofilter;

    	if(count($tofilter) > 1) {
    		$args['tax_query']['relation'] = 'AND';
    	}
    }

    /*if($filter && $filtervalue) {
    	$args['meta_query']	= array(
			array(
				'key'	  	=> $filter,
				'value'	  	=> $filtervalue,
				'compare' 	=> '=',
			),
		);
    }*/

    $grants = new WP_Query($args);

    update_post_thumbnail_cache($grants);

    $ret = [];
    
    while ($grants->have_posts()) {
        $grants->the_post();
        
        if (class_exists('ACF')) {
        	$aux = [];
        	$acf = get_fields(get_the_ID());
        	//var_dump($acf);
        	/*
prima riga = titolo (che è un link se è presente un website) e tra parentesi la categoria =tipologia di progetto; 
seconda riga Coordinatore Scientifico (se esiste), Responsabile di Unità (se coincidono, basta una volta); 
terza riga = componenti dell'unità
        	*/
			$pcat = '';
			if(isset($acf['tipologia_progetto']) && $acf['tipologia_progetto'] != '') {
				$pcat = ' <small class="text-muted">(' . $acf['tipologia_progetto'] . ')</small>';
			}
			if(isset($acf['website']) && $acf['website'] != '') {
				$aux[] = '<h5 class="mb-0 font-weight-bold"><a href="' . $acf['website'] . '" target="_blank">' . get_the_title() . $pcat . '</a></h5>';
			} else {
				$aux[] = '<h5 class="mb-0 font-weight-bold">' . get_the_title() . $pcat . '</h5>';
			}
			$p1 = null;
			if(isset($acf['coordinatore_scientifico']) && $acf['coordinatore_scientifico'] != '') {
				$p1 = $acf['coordinatore_scientifico'];
			}
			$p2 = null;
			if(isset($acf['responsabile_unita']) && $acf['responsabile_unita'] != '') {
				$p2 = $acf['responsabile_unita'];
			}
			$str = [];
			if($p1) {
				$str[] = 'Coordinatore scientifico: <em>' . $p1 . '</em>';
			}
			if($p2 && $p1 != $p2) {
				$str[] = 'Responsabile unità: <em>' . $p2 . '</em>';
			}
			if(count($str) > 0) {
				$aux[] = '<p class="mb-0">' . implode(' | ', $str) . '</p>';
			}
			if(isset($acf['componenti_unita']) && $acf['componenti_unita'] != '') {
				$aux[] = '<p class="mb-0">Componenti dell\'unità: <em>' . $acf['componenti_unita'] . '</em></p>';
			}

		    $tmp = '';
		    if($table) {
		    	$tmp = '<td>'.implode('</td><td>', $aux).'</td>';
		    } else {
		    	$tmp = implode(' ', $aux);
		    }
		    $ret[] = $tmp;
		}
		
    }
    wp_reset_postdata();

    //restore_current_blog();

	if($table) {
		$thead = (array) explode(',', $table);
		$thead = '<thead><tr><th>'.implode('</th><th>', $thead).'</th></tr></thead>';
    	$ret = '<table class="grantstable table table-sm">'.$thead.'<tbody><tr>' . implode("</tr><tr>", $ret) . '</tr></tbody></table>';
    } else {
    	$ret = '<ul class="grantslist"><li class="mb-2">' . implode("</li><li class=\"mb-2\">\n", $ret) . '</li></ul>';
    }
    
    return $ret;
}

add_shortcode('grants', 'grantsshortcode');