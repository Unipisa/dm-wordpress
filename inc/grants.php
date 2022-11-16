<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Latest post */
function grantsshortcode($atts) {
    extract(shortcode_atts(array(
        'type' => 'grant',
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
        'meta_query' => array(
	        array(
	            'tipologia_progetto_clause' => array(
	                'key'       => 'tipologia_progetto',
	                'compare'   => 'EXISTS',
	            ),
		    'termine_clause' => array(
                        'key' => 'termine',
                        'compare' => 'EXISTS'
                    )
	        )
	    ),
	    'orderby' => array(
	        // 'tipologia_progetto_clause' => 'ASC',
                'termine_clause' => 'DESC',
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
Coordinatore Scientifico —> Principal Investigator
Responsabile di Unità —> Coordinator of the Research Unit
Membri dell’Unità —> Members of the Research Unit

prima riga = titolo (che è un link se è presente un website) e tra parentesi la categoria =tipologia di progetto; 
seconda riga Coordinatore Scientifico (se esiste), Responsabile di Unità (se coincidono, basta una volta); 
terza riga = componenti dell'unità
        	*/
			$dataattr = [];

			$terms = get_the_terms(get_the_ID(), 'granttag');
			foreach ($terms as $t) {
				$dataattr[] = $t->slug;
			}

			if(isset($acf['fondi'])) {
				$dataattr[] = sanitize_title($acf['fondi']);
			}

			$pcat = '';
			if(isset($acf['tipologia_progetto']) && $acf['tipologia_progetto'] != '') {
				$pcat = ' <small class="text-muted">(' . $acf['tipologia_progetto'] . ')</small>';
			}

			$aux[] = '<li class="mb-2 ' . implode(' ', $dataattr) . '">';

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
				$lbl = $lang == 'it' ? 'Coordinatore scientifico' : 'Principal Investigator';
				$str[] = $lbl . ': <em>' . $p1 . '</em>';
			}
			if($p2 && $p1 != $p2) {
				$lbl = $lang == 'it' ? 'Responsabile unità' : 'Coordinator of the Research Unit';
				$str[] = $lbl . ': <em>' . $p2 . '</em>';
			}
			if(count($str) > 0) {
				$aux[] = '<p class="mb-0">' . implode(' | ', $str) . '</p>';
			}
			if(isset($acf['componenti_unita']) && $acf['componenti_unita'] != '') {
				$lbl = $lang == 'it' ? 'Membri dell\'unità' : 'Members of the Research Unit';
				$aux[] = '<p class="mb-0">' . $lbl . ': <em>' . $acf['componenti_unita'] . '</em></p>';
			}
			$aux[] = '<p class="mb-0">Project period: ' . $acf['inizio'] . ' - ' . $acf['termine'] . '</p>';

			$aux[] = '</li>';

		    $ret[] = implode(' ', $aux);
		}
		
    }
    wp_reset_postdata();

    //restore_current_blog();

    global $wpdb;

    $typecol = $wpdb->get_col( $wpdb->prepare( "
        SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
        WHERE pm.meta_key = %s
        ORDER BY pm.meta_value ASC
    ", 'fondi' ) );

    $opts = [];
    foreach ($typecol as $key => $value) {
    	$opts[] = '<option value="'.sanitize_title($value).'">'.__($value, 'unipi').'</option>';
    }

    $mats = [];
    foreach (get_ssds() as $key => $value) {
    	$v = isset($value[1]) ? $value[1] : $key;
    	$mats[] = '<option value="'.sanitize_title($key).'">'. $v .'</option>';
    }

    $pre = '';

    if($filters) {
    	$pre = '<h4 class="h5 mt-0">'.__('Filters', 'unipi').'</h4>
	    <form class="form mb-3 grantsform">
	    	<div class="row">
		    	<div class="col-lg-4">
				    <div class="form-group">
						<label class="sr-only" for="tipologia_progetto">'.__('Project type', 'unipi').'</label>
						<select class="custom-select" id="tipologia_progetto">
							<option selected>' . __('Project type', 'unipi') . '</option>
							'.implode("\n", $opts).'
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<label class="sr-only" for="current_past">'.__('Project status', 'unipi').'</label>
						<select class="custom-select" id="current_past">
							<option selected>' . __('All projects', 'unipi') . '</option>
							<option value="current">'.__('Current projects', 'unipi').'</option>
							<option value="past">'.__('Past projects', 'unipi').'</option>
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<label class="sr-only" for="area">'.__('Area', 'unipi').'</label>
						<select class="custom-select" id="area">
							<option selected>' . __('All areas', 'unipi') . '</option>
							'.implode("\n", $mats).'
						</select>
					</div>
				</div>
			</div>

		</form>';
    }

	$ret = $pre . '<ul class="grantslist">' . implode("\n", $ret) . '</ul>';
    
    return $ret;
}

add_shortcode('grants', 'grantsshortcode');
