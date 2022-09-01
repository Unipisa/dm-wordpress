<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function cptui_register_my_cpts() {

	/**
	 * Post Type: Persone.
	 */

	$labels = [
		"name" => __( "Persone", "custom-post-type-ui" ),
		"singular_name" => __( "Persona", "custom-post-type-ui" ),
		"menu_name" => __( "Persone", "custom-post-type-ui" ),
		"all_items" => __( "Tutte le Persone", "custom-post-type-ui" ),
		"add_new" => __( "Add new", "custom-post-type-ui" ),
		"add_new_item" => __( "Aggiungi nuovo Persona", "custom-post-type-ui" ),
		"edit_item" => __( "Modifica Persona", "custom-post-type-ui" ),
		"new_item" => __( "Nuovo Persona", "custom-post-type-ui" ),
		"view_item" => __( "Visualizza Persona", "custom-post-type-ui" ),
		"view_items" => __( "Visualizza Persone", "custom-post-type-ui" ),
		"search_items" => __( "Cerca Persone", "custom-post-type-ui" ),
		"not_found" => __( "No Persone found", "custom-post-type-ui" ),
		"not_found_in_trash" => __( "No Persone found in trash", "custom-post-type-ui" ),
		"parent" => __( "Genitore Persona:", "custom-post-type-ui" ),
		"featured_image" => __( "Featured image for this Persona", "custom-post-type-ui" ),
		"set_featured_image" => __( "Set featured image for this Persona", "custom-post-type-ui" ),
		"remove_featured_image" => __( "Remove featured image for this Persona", "custom-post-type-ui" ),
		"use_featured_image" => __( "Use as featured image for this Persona", "custom-post-type-ui" ),
		"archives" => __( "Persona archives", "custom-post-type-ui" ),
		"insert_into_item" => __( "Insert into Persona", "custom-post-type-ui" ),
		"uploaded_to_this_item" => __( "Upload to this Persona", "custom-post-type-ui" ),
		"filter_items_list" => __( "Filter Persone list", "custom-post-type-ui" ),
		"items_list_navigation" => __( "Persone list navigation", "custom-post-type-ui" ),
		"items_list" => __( "Persone list", "custom-post-type-ui" ),
		"attributes" => __( "Persone attributes", "custom-post-type-ui" ),
		"name_admin_bar" => __( "Persona", "custom-post-type-ui" ),
		"item_published" => __( "Persona published", "custom-post-type-ui" ),
		"item_published_privately" => __( "Persona published privately.", "custom-post-type-ui" ),
		"item_reverted_to_draft" => __( "Persona reverted to draft.", "custom-post-type-ui" ),
		"item_scheduled" => __( "Persona scheduled", "custom-post-type-ui" ),
		"item_updated" => __( "Persona updated.", "custom-post-type-ui" ),
		"parent_item_colon" => __( "Genitore Persona:", "custom-post-type-ui" ),
	];

	$args = [
		"label" => __( "Persone", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "people", "with_front" => false ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-users",
		"supports" => [ "title", "thumbnail" ],
		"taxonomies" => [ "typology" ],
		"show_in_graphql" => false,
	];

	register_post_type( "people", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );


function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Tipologie.
	 */

	$labels = [
		"name" => __( "Tipologie", "custom-post-type-ui" ),
		"singular_name" => __( "Tipologia", "custom-post-type-ui" ),
		"menu_name" => __( "Tipologie", "custom-post-type-ui" ),
		"all_items" => __( "Tutte le Tipologie", "custom-post-type-ui" ),
		"edit_item" => __( "Modifica Tipologia", "custom-post-type-ui" ),
		"view_item" => __( "Visualizza Tipologia", "custom-post-type-ui" ),
		"update_item" => __( "Update Tipologia name", "custom-post-type-ui" ),
		"add_new_item" => __( "Aggiungi nuovo Tipologia", "custom-post-type-ui" ),
		"new_item_name" => __( "Nuovo nome Tipologia", "custom-post-type-ui" ),
		"parent_item" => __( "Tipologia genitore", "custom-post-type-ui" ),
		"parent_item_colon" => __( "Genitore Tipologia:", "custom-post-type-ui" ),
		"search_items" => __( "Cerca Tipologie", "custom-post-type-ui" ),
		"popular_items" => __( "Tipologie popolari", "custom-post-type-ui" ),
		"separate_items_with_commas" => __( "Separa Tipologie con le virgole", "custom-post-type-ui" ),
		"add_or_remove_items" => __( "Aggiungi o rimuovi Tipologie", "custom-post-type-ui" ),
		"choose_from_most_used" => __( "Scegli tra i Tipologie più utilizzati", "custom-post-type-ui" ),
		"not_found" => __( "No Tipologie found", "custom-post-type-ui" ),
		"no_terms" => __( "No Tipologie", "custom-post-type-ui" ),
		"items_list_navigation" => __( "Tipologie list navigation", "custom-post-type-ui" ),
		"items_list" => __( "Tipologie list", "custom-post-type-ui" ),
		"back_to_items" => __( "Back to Tipologie", "custom-post-type-ui" ),
		"name_field_description" => __( "The name is how it appears on your site.", "custom-post-type-ui" ),
		"parent_field_description" => __( "Assign a parent term to create a hierarchy. The term Jazz, for example, would be the parent of Bebop and Big Band.", "custom-post-type-ui" ),
		"slug_field_description" => __( "The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.", "custom-post-type-ui" ),
		"desc_field_description" => __( "The description is not prominent by default; however, some themes may show it.", "custom-post-type-ui" ),
	];

	
	$args = [
		"label" => __( "Tipologie", "custom-post-type-ui" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'typology', 'with_front' => true, ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "typology",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => true,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "typology", [ "people" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );


/* Latest post */
function peopleshortcode($atts) {
    extract(shortcode_atts(array(
        'type' => 'people',
        'limit' => 100,
        'tags' => false,
        'fields' => 'cognome',
        'table' => false,
        'toptag' => false,
        'toptaglabel' => '',
        'filter' => false,
        'filtervalue' => false,
    ), $atts));

    $current_blog_id = get_current_blog_id();
    if($current_blog_id != 1) {
    	switch_to_blog(1);
    }

    $args = array(
        'post_type' => $type,
        'posts_per_page' => $limit,
        'meta_query' => array(
	        array(
	            'relation' => 'AND',
	            'cognome_clause' => array(
	                'key'       => 'cognome',
	                'compare'   => 'EXISTS',
	            ),
	            'nome_clause' => array(
	                'key'       => 'nome',
	                'compare'   => 'EXISTS',
	            ),
	        )
	    ),
	    'orderby' => array(
	        'cognome_clause' => 'ASC',
	        'nome_clause' => 'ASC',
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
	    		'taxonomy' => 'typology',
	            'field' => 'slug',
	            'terms' => $t,
	    	];
	    }

    	$args['tax_query'] = $tofilter;

    	if(count($tofilter) > 1) {
    		$args['tax_query']['relation'] = 'AND';
    	}
    }

    if($filter && $filtervalue) {
    	$args['meta_query']	= array(
    		array(
	            'relation' => 'AND',
	            'cognome_clause' => array(
	                'key'       => 'cognome',
	                'compare'   => 'EXISTS',
	            ),
	            'nome_clause' => array(
	                'key'       => 'nome',
	                'compare'   => 'EXISTS',
	            ),
	            array(
					'key'	  	=> $filter,
					'value'	  	=> $filtervalue,
					'compare' 	=> '=',
				),
	        )
		);
    }

    $people = new WP_Query($args);

    update_post_thumbnail_cache($people);

    $flds = (array) explode(',', $fields);
    foreach ($flds as $k => $f) {
    	$flds[$k] = trim($f);
    }

    $ret = [];
    $toptaglist = [];
    
    while ($people->have_posts()) {
        $people->the_post();
        
        // FIX to remove
        //$acf = get_post_meta(get_the_ID());
        //print_r($acf);
        /*$groups = acf_get_field_groups(array('post_id' => get_the_ID()));
		foreach ($groups as $group) {
		  $fields = acf_get_fields($group);
		  foreach ($fields as $field) {
		    $value = get_post_meta(get_the_ID(), $field['name'], true);
		    update_field($field['key'], $value, get_the_ID()); 
		  }
		}*/


        if (class_exists('ACF')) {
        	$istop = $toptag && has_term( $toptag, 'typology', get_the_ID() );
        	$aux = [];
        	$acf = get_fields(get_the_ID());
        	//var_dump($acf);
        	foreach ($flds as $k => $f) {
		    	if(isset($acf[$f])) {
		    		switch ($f) {
		    			case 'email':
		    				$aux[] = '<a href="mailto:'.$acf[$f].'"><i class="far fa-envelope fa-fw"></i></a>';
		    				break;
		    			case 'telefono':
		    				$aux[] = '<a href="tel:'.$acf[$f].'"><i class="fas fa-phone-alt fa-fw"></i></a>';
		    				break;
		    			default:
		    				$aux[] = $acf[$f];
		    				break;
		    		}
		    	} else {
		    		switch ($f) {
		    			case 'link':
		    				$aux[] = '<a href="'.get_the_permalink(get_the_ID()).'"><i class="fas fa-id-card fa-fw"></i></i></a>';
		    				break;
		    			case 'toptaglabel':
		    				$aux[] = $istop ? $toptaglabel : '&nbsp;';
		    				break;
	    				case 'extlink':
	    					$tmp = [];
	    					if(isset($acf['arxiv_orcid']) && $acf['arxiv_orcid'] == 1) {
	    						$tmp[] = '[<a href="https://arxiv.org/a/' . $acf['orcid'] . '.html" target="_blank">arXiv</a>]';
	    					}
	    					if(isset($acf['google_scholar']) && strlen(trim($acf['google_scholar'])) > 0) {
	    						$tmp[] = '[<a href="https://scholar.google.com/citations?user=' . $acf['google_scholar'] . '" target="_blank">Google Scholar</a>]';
	    					}
	    					if(isset($acf['mathscinet']) && strlen(trim($acf['mathscinet'])) > 0) {
	    						$tmp[] = '[<a href="http://www.ams.org/mathscinet/search/author.html?mrauthid=' . $acf['mathscinet'] . '" target="_blank">Mathscinet</a>]';
	    					}
	    					if(isset($acf['orcid']) && strlen(trim($acf['orcid'])) > 0) {
	    						$tmp[] = '[<a href="https://orcid.org/' . $acf['orcid'] . '" target="_blank">Orcid</a>]';
	    					}
	    					$aux[] = implode(' ', $tmp);
	    					break;
		    			default:
		    				break;
		    		}
		    	}
		    }
		    $tmp = '';
		    if($table) {
		    	$tmp = '<td>'.implode('</td><td>', $aux).'</td>';
		    } else {
		    	$tmp = implode(' ', $aux);
		    }
		    if($istop) {
		    	$toptaglist[] = $tmp;
		    } else {
		    	$ret[] = $tmp;
		    }
		}
		
    }
    wp_reset_postdata();

    restore_current_blog();

	if($table) {
		$thead = (array) explode(',', $table);
		$thead = '<thead><tr><th>'.implode('</th><th>', $thead).'</th></tr></thead>';
    	$ret = '<table class="peopletable table table-sm">'.$thead.'<tbody><tr>' . implode("</tr><tr>", array_merge($toptaglist, $ret)) . '</tr></tbody></table>';
    } else {
    	$ret = '<ul class="peoplelist"><li>' . implode("</li><li>\n", $ret) . '</li></ul>';
    }
    
    return $ret;
}

add_shortcode('people', 'peopleshortcode');