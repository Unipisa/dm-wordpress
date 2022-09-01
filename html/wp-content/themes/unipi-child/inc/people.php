<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function cptui_register_my_cpts() {

	/**
	 * Post Type: Persone.
	 */

	$labels = [
		"name" => __( "Persone", "unipi" ),
		"singular_name" => __( "Persona", "unipi" ),
		"menu_name" => __( "Persone", "unipi" ),
		"all_items" => __( "Tutte le Persone", "unipi" ),
		"add_new" => __( "Add new", "unipi" ),
		"add_new_item" => __( "Aggiungi nuovo Persona", "unipi" ),
		"edit_item" => __( "Modifica Persona", "unipi" ),
		"new_item" => __( "Nuovo Persona", "unipi" ),
		"view_item" => __( "Visualizza Persona", "unipi" ),
		"view_items" => __( "Visualizza Persone", "unipi" ),
		"search_items" => __( "Cerca Persone", "unipi" ),
		"not_found" => __( "No Persone found", "unipi" ),
		"not_found_in_trash" => __( "No Persone found in trash", "unipi" ),
		"parent" => __( "Genitore Persona:", "unipi" ),
		"featured_image" => __( "Featured image for this Persona", "unipi" ),
		"set_featured_image" => __( "Set featured image for this Persona", "unipi" ),
		"remove_featured_image" => __( "Remove featured image for this Persona", "unipi" ),
		"use_featured_image" => __( "Use as featured image for this Persona", "unipi" ),
		"archives" => __( "Persona archives", "unipi" ),
		"insert_into_item" => __( "Insert into Persona", "unipi" ),
		"uploaded_to_this_item" => __( "Upload to this Persona", "unipi" ),
		"filter_items_list" => __( "Filter Persone list", "unipi" ),
		"items_list_navigation" => __( "Persone list navigation", "unipi" ),
		"items_list" => __( "Persone list", "unipi" ),
		"attributes" => __( "Persone attributes", "unipi" ),
		"name_admin_bar" => __( "Persona", "unipi" ),
		"item_published" => __( "Persona published", "unipi" ),
		"item_published_privately" => __( "Persona published privately.", "unipi" ),
		"item_reverted_to_draft" => __( "Persona reverted to draft.", "unipi" ),
		"item_scheduled" => __( "Persona scheduled", "unipi" ),
		"item_updated" => __( "Persona updated.", "unipi" ),
		"parent_item_colon" => __( "Genitore Persona:", "unipi" ),
	];

	$args = [
		"label" => __( "Persone", "unipi" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "people", "with_front" => false ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-users",
		"supports" => [ "title", "thumbnail", "author" ],
		"taxonomies" => [ "typology" ],
		"show_in_graphql" => false,
		"capability_type" => "people",
		"map_meta_cap" => true,
		"capabilities" => [
			"create_posts" => "create_peoples"
        ],
	];

	register_post_type( "people", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

// https://stackoverflow.com/questions/58218457/wordpress-user-with-custom-role-cannot-view-list-page-for-custom-post-types-with
add_filter(
    'user_has_cap',
    function( $all_caps, $caps ) {
        global $typenow, $menu;

        if ( is_admin() && ! empty( $typenow ) && stripos( $_SERVER['REQUEST_URI'], 'edit.php' ) && stripos( $_SERVER['REQUEST_URI'], 'post_type=' . $typenow ) && in_array( 'edit_posts', $caps, true ) ) {
            // Temporarily assign the user the edit_posts capability
            $all_caps['edit_posts'] = true;
            // Now Remove any menu items with edit_posts besides the custom post type pages.
            if ( ! empty( $menu ) ) {
                foreach ( $menu as $menu_key => $menu_item ) {
                    if ( ! empty( $menu_item[1] ) && ( $menu_item[1] === 'edit_posts' || $menu_item[2] === 'edit.php' ) ) {
                        remove_menu_page( $menu_item[2] );
                    }
                }
            }
        }

        return $all_caps;
    },
    10,
    2
);

/*
add_action(
    'admin_menu',
    function () {
        global $pagenow, $typenow;

        if ( is_admin() && ! empty( $typenow ) && ! empty( $pagenow ) && $pagenow === 'edit.php' && stripos( $_SERVER['REQUEST_URI'], 'edit.php' ) && stripos( $_SERVER['REQUEST_URI'], 'post_type=' . $typenow ) ) {
            $pagenow = 'custom_post_type_edit.php';
        }
    }
);
*/


function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Tipologie.
	 */

	$labels = [
		"name" => __( "Tipologie", "unipi" ),
		"singular_name" => __( "Tipologia", "unipi" ),
		"menu_name" => __( "Tipologie", "unipi" ),
		"all_items" => __( "Tutte le Tipologie", "unipi" ),
		"edit_item" => __( "Modifica Tipologia", "unipi" ),
		"view_item" => __( "Visualizza Tipologia", "unipi" ),
		"update_item" => __( "Update Tipologia name", "unipi" ),
		"add_new_item" => __( "Aggiungi nuovo Tipologia", "unipi" ),
		"new_item_name" => __( "Nuovo nome Tipologia", "unipi" ),
		"parent_item" => __( "Tipologia genitore", "unipi" ),
		"parent_item_colon" => __( "Genitore Tipologia:", "unipi" ),
		"search_items" => __( "Cerca Tipologie", "unipi" ),
		"popular_items" => __( "Tipologie popolari", "unipi" ),
		"separate_items_with_commas" => __( "Separa Tipologie con le virgole", "unipi" ),
		"add_or_remove_items" => __( "Aggiungi o rimuovi Tipologie", "unipi" ),
		"choose_from_most_used" => __( "Scegli tra i Tipologie più utilizzati", "unipi" ),
		"not_found" => __( "No Tipologie found", "unipi" ),
		"no_terms" => __( "No Tipologie", "unipi" ),
		"items_list_navigation" => __( "Tipologie list navigation", "unipi" ),
		"items_list" => __( "Tipologie list", "unipi" ),
		"back_to_items" => __( "Back to Tipologie", "unipi" ),
		"name_field_description" => __( "The name is how it appears on your site.", "unipi" ),
		"parent_field_description" => __( "Assign a parent term to create a hierarchy. The term Jazz, for example, would be the parent of Bebop and Big Band.", "unipi" ),
		"slug_field_description" => __( "The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.", "unipi" ),
		"desc_field_description" => __( "The description is not prominent by default; however, some themes may show it.", "unipi" ),
	];

	
	$args = [
		"label" => __( "Tipologie", "unipi" ),
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
		"capabilities" => array(
			"manage_terms"=> "manage_categories",
			"edit_terms"=> "manage_categories",
			"delete_terms"=> "manage_categories",
			"assign_terms" => "read"
		),
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
        'tableen' => false,
        'toptag' => false,
        'toptaglabel' => '',
        'filter' => false,
        'filtervalue' => false,
    ), $atts));

    $current_blog_id = get_current_blog_id();
    if($current_blog_id != 1) {
    	switch_to_blog(1);
    }

    global $sitepress;

    if($sitepress) {
    	remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10 );
	    remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
	    remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10 );   
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

    if(get_locale() !== 'it_IT' && $tableen) {
    	$table = $tableen;
	}
    
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
		    			case 'email-ruolo-persona':
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

    if($sitepress) {
	    // Add the filter back
	    add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10, 2 );
	    add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
	    add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
	}
	
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

function get_people_role_label($role, $lang = 'it', $genre = 'm') {
	$ret = $role;
	$roles = [
		'PO' => ['Professore Ordinario', 'Professoressa Ordinaria', 'Full Professor', 'Full Professor'],
		'PA' => ['Professore Associato', 'Professoressa Associata', 'Associate Professor', 'Associate Professor'],
		'RTDb' => ['Ricercatore a tempo determinato senior', 'Ricercatrice a tempo determinato senior', 'Tenure-track Assistant Professor', 'Tenure-track Assistant Professor'],
		'RTDa' => ['Ricercatore a tempo determinato junior', 'Ricercatrice a tempo determinato junior', 'Tenure-track Assistant Professor', 'Non-tenured Assistant Professor'],
		'RIC' => ['Ricercatore a tempo indeterminato', 'Ricercatrice a tempo indeterminato', 'Tenured Assistant Professor', 'Tenured Assistant Professor'],
		'Assegnista' => ['Assegnista', 'Assegnista', 'Postdoctoral Fellow', 'Postdoctoral Fellow'],
		'Dottorando' => ['Dottorando', 'Dottoranda', 'Ph.D. Student', 'Ph.D. Student'],
		'PTA' => ['Personale Tecnico Amministrativo', 'Personale Tecnico Amministrativo', 'Administrative Staff', 'Administrative Staff'],
		'Professore Emerito' => ['Professore Emerito', 'Professore Emerito', 'Emeritus Professor', 'Emeritus Professor'],
		'Collaboratore e Docente Esterno' => ['Collaboratore e Docente Esterno', 'Collaboratrice e Docente Esterna', 'External Collaborator', 'External Collaborator'],
		'Studente' => ['Studente', 'Studentessa', 'Student', 'Student'],
	];
	if(isset($roles[$role])) {
		$i = 0;
		if($genre == 'f') {
			$i++;
		}
		if($lang != 'it'){
			$i += 2;
		}
		$ret = $roles[$role][$i];
	}

	return $ret;
}
