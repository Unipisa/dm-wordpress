<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';
include_once('translations.php');

function get_dotted_field($obj, $dotted_field, $date_format) {
	foreach (explode(".", $dotted_field) as $field) {
		$obj = $obj[$field];
	}
	if (in_array($dotted_field, ['startDate','endDate','date'])) {
		$date = date_create($obj);
		$obj = date_format($date, $date_format);
	}
	return $obj;
}

function dm_manager_get_by_id($model, $id) {
    $ch = curl_init();

    // FIXME: Sanitize the ID

    curl_setopt($ch, CURLOPT_URL, DM_MANAGER_URL . $model . '/' . $id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Authorization: Bearer ' . DM_MANAGER_TOKEN;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response = array();

    if ($httpcode == 200) {
        $response['data'] = json_decode($result, true);
    }
    else {
        $response['data'] = NULL;
    }

    return $response;
}

function dm_manager_get($model, $sort_field, $filter) {
        $ch = curl_init();

        $query = [];
        $query[] = '_sort=' . $sort_field;
	foreach (explode(",", $filter) as $f) {
	  $f=trim($f);
	  if ($f == '') continue;
          $key_val = explode("=", $f);
	  $key = $key_val[0];
	  $val = $key_val[1];
          if ($key == 'current') {
                $query[] = 'startDate__lt_or_null=today';
                $query[] = 'endDate__gt_or_null=today';
          } elseif ($key == 'past') {
                $query[] = 'endDate__lt_or_null=today';
	  } elseif ($key == 'perspective') {
                $query[] = 'startDate__gt_or_null=today';
          } elseif ($key == 'year') {
		$query[] = 'startDate__lte_or_null=' . $val . '-12-31&endDate__gte_or_null=' . $val . '-01-01';
          } elseif ($key == 'dateyear') {
		$query[] = 'date__lte=' . $val . '-12-31&date__gte=' . $val . '-01-01';
          } else {
		$query[] = $key . '=' . $val;
	  }
	}

	$url = DM_MANAGER_URL . $model . '?' . implode('&', $query);
	$ret[] = '<!-- QUERY_STRING ' . $url . ' -->';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . DM_MANAGER_TOKEN;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
	    // $ret[] = '<p>RESULT: ' . $result . '</p>';
            $resp = json_decode($result, true);
            $resp['debug'] = implode("\n", $ret);
            if (!isset($resp['data'])) {
		$resp['data'] = array();
	    }
        } else {
	    $resp = array('data' => array(), 'debug' => implode("\n", $ret));
	}
        return $resp;
    }

function people_manager_display($data, $fields, $table, $date_format, $no_data_message) {
    $ret[] = '<!-- 200 OK -->';
    if (count($data)) {
 		  $ret[] = '<table class="peopletable">';
		  $ret[] = '<thead><tr>';
		  for ($i = 0 ; $i < count($fields) ; $i++) {
			$class='';
			if ($fields[$i] == 'person.lastName' || $fields[$i] == 'qualification') {
			  $class=' class="enable-sort"';
			}
			$ret[]='<th'.$class.'>'.$table[$i].'</th>';
		  }
		  $ret[] = '</tr></thead><tbody>';

		  foreach ($data as $row) {
			$ret[]='<tr>';
			foreach ($fields as $field) {
				$val = get_dotted_field($row, $field, $date_format);
				if ($field == 'roomAssignment.room.building') {
					if ($val == 'A') $val = 'Edificio A';
					if ($val == 'B') $val = 'Edificio B';
					if ($val == 'X') $val = 'Ex-Albergo';
				} else if ($field == 'person.email' && $val!='') {
					$val = '<a href="mailto:' . $val . '">'
					. '<i class="far fa-envelope fa-fw"></i><span class="d-none d-lg-inline">'
					. $val . '</span></a>';
				} else if ($field == 'person.phone' && $val!='') {
					$val = '<a href="tel:' . $val . '">'
					. '<i class="fas fa-phone-alt fa-fw"></i> <span class="d-none d-lg-inline">'
					. $val.'</span></a>';
				} else if ($field == 'person._id') {
					$val = '<a href="/scheda-personale/?person_id=' . $val . '">'
					. '<i class="fas fa-id-card fa-fw"></i></a>';
				}
				$ret[]='<td>'.$val.'</td>';
			}
			$ret[]='</tr>';
		  }
		  $ret[] = '</tbody></table>';
    } else {
		  $ret[] = '<p>' . $no_data_message . '</p>';
    }
    return implode("\n", $ret);
}

/* Shortcode */
function visit_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
	'model' => 'visit',
        'fields' => 'cognome',
        'table' => false,
        'tableen' => false,
	'sort_field' => 'person',
	'filter' => '',
	'no_data_message' => 'nessuna informazione',
	'no_data_message_en' => 'there is no data',
	'date_format' => 'd.m.Y'
    ), $atts));

    if (get_locale() !== 'it_IT') {
	if ($tableen) {
		$table = $tableen;
	}
	$no_data_message = $no_data_message_en;
	$date_format = 'M d, Y';
    }

    // $filter = 'publish=1,' . $filter;

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);

    $resp = dm_manager_get('visit', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = people_manager_display($resp['data'], $e_fields, explode(',', $table), $date_format, $no_data_message);
    return implode("\n", $ret);
}

add_shortcode('dm_manager', 'visit_manager_shortcode');
add_shortcode('visit_manager', 'visit_manager_shortcode');

function thesis_manager_display($data, $fields, $table, $date_format, $no_data_message, $list_mode) {
    $ret[] = '<!-- 200 OK -->';
    if (count($data)) {
		  if ($list_mode) {
                    $ret[] = '<ul>';
		  } else {
	  	    $ret[] = '<table>';
		    $ret[] = '<thead><tr>';
  		    for ($i = 0 ; $i < count($fields) ; $i++) {
			$class='';
			if ($fields[$i] == 'person.lastName' || $fields[$i] == 'qualification') {
			  $class=' class="enable-sort"';
			}
			$ret[]='<th'.$class.'>'.$table[$i].'</th>';
		    }
		    $ret[] = '</tr></thead><tbody>';
		  }

		  foreach ($data as $row) {
                    if ($list_mode) {
			$ret[] = '<li>';
                        $ret[] = $row['person']['firstName'];
			$ret[] = $row['person']['lastName'];
			if ($row['affiliation']) $ret[] = '(' . $row['affiliation'] . ')';
			if ($row['title']) $ret[] = '"' . $row['title'] . '"';
			if ($row['advisors']) {
			  $ret[] = 'supervised by';
			  foreach($row['advisors'] as $key => $advisor) {
			    if ($key !== array_key_first($row['advisors'])) $ret[] = 'and';
			    $ret[] = $advisor['firstName'] . '  ' . $advisor['lastName'];
			  }
			}
			$ret[] = '</li>';
		    } else {
			$ret[]='<tr>';
			foreach ($fields as $field) {
				$val = get_dotted_field($row, $field, $date_format);
				if ($field == 'person.genealogyId') {
					if (!empty($val)) {
						$val = '<a href="https://www.genealogy.math.ndsu.nodak.edu/id.php?id=' . $val . 
							'"><span class="fas fa-id-card fa-fw" aria-hidden="true"></span></a>';
					}
				} else if ($field == 'person.email' && $val!='') {
					$val = '<a href="mailto:' . $val . '">'
					. '<i class="far fa-envelope fa-fw"></i><span class="d-none d-lg-inline">'
					. $val . '</span></a>';
				} else if ($field == 'person.phone' && $val!='') {
					$val = '<a href="tel:' . $val . '">'
					. '<i class="fas fa-phone-alt fa-fw"></i> <span class="d-none d-lg-inline">'
					. $val.'</span></a>';
				} else if ($field == 'person._id') {
					$val = '<a href="/scheda-personale/?person_id=' . $val . '">'
					. '<i class="fas fa-id-card fa-fw"></i></a>';
				} else if ($field == 'advisors') {
					$advisors = $val;
					$val = [];
					foreach ($advisors as $advisor) {
						$val[]=$advisor['lastName'];
					}
					$val = implode(' and ', $val);
				}
				$ret[]='<td>'.$val.'</td>';
			}
			$ret[]='</tr>';
		    }
		  }
		  if ($list_mode) {
		    $ret[] = '</ul>';
		  } else {
	   	    $ret[] = '</tbody></table>';
		  }
    } else {
		  $ret[] = '<p>' . $no_data_message . '</p>';
    }
    return implode("\n", $ret);
}

/* Shortcode */
function thesis_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
	'model' => 'thesis',
        'fields' => 'person.firstName,person.lastName,person.genealogyId',
        'table' => false,
        'tableen' => false,
	'sort_field' => 'person',
	'filter' => '',
	'no_data_message' => 'nessuna informazione',
	'no_data_message_en' => 'there is no data',
	'date_format' => 'd.m.Y',
	'list_mode' => false
    ), $atts));

    if (get_locale() !== 'it_IT') {
	if ($tableen) {
		$table = $tableen;
	}
	$no_data_message = $no_data_message_en;
	$date_format = 'M d, Y';
    }

    // $filter = 'publish=1,' . $filter;

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);

    $resp = dm_manager_get('thesis', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = thesis_manager_display($resp['data'], $e_fields, explode(',', $table), $date_format, $no_data_message, $list_mode);
    return implode("\n", $ret);
}

add_shortcode('dm_manager_thesis', 'thesis_manager_shortcode');

/* Shortcode */
function staff_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
	'model' => 'visit',
        'fields' => 'cognome',
        'table' => false,
        'tableen' => false,
	'sort_field' => 'person',
	'filter' => '',
	'no_data_message' => 'nessuna informazione',
	'no_data_message_en' => 'there is no data',
	'date_format' => 'd.m.Y'
    ), $atts));

    if (get_locale() !== 'it_IT') {
	if ($tableen) {
		$table = $tableen;
	}
	$no_data_message = $no_data_message_en;
	$date_format = 'M d, Y';
    }

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);
    $filter = implode(',', array_merge(explode(',', $filter),['current']));
    $resp = dm_manager_get('staff', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = people_manager_display($resp['data'], $e_fields, explode(',', $table), $date_format, $no_data_message);
    return implode("\n", $ret);
}

add_shortcode('staff_manager', 'staff_manager_shortcode');

function grant_manager_display($data, $date_format, $no_data_message) {
    $ret[] = '<!-- 200 OK -->';
    if (count($data)) {
        $ret[] = '<ul>';
	foreach ($data as $grant) {
             $ret[] = '<li class="mb-2 current' . strtolower($grant['ssd']) . ' ' .$grant['funds']. '">';
	     $ret[] = '<h5 class="mb-0 font-weight-bold">';
	     $close = '';
             $ret[] = '<a href="/research/grant-details/?grant_id=' . $grant['_id'] . '">';
             $close = '</a>';
             $ret[] = $grant['name'];
	     if ($grant['projectType'] != '') $ret[] = '<small class="text-muted">(' . $grant['projectType'] .')</small>';
	     $ret[] = $close.'</h5>';
	     if ($grant['pi']) {
		$ret[] = '<p class="mb-0">Principal Investigator: <em>' . $grant['pi']['firstName'] . ' ' .$grant['pi']['lastName'] . '</em></p>';
	     }
             if ($grant['localCoordinator'] && $grant['localCoordinator']['_id'] != $grant['pi']['_id']) {
		$ret[] = '<p class="mb-0">Coordinator of the Research Unit: <em>' . $grant['localCoordinator']['firstName'] . ' ' .$grant['localCoordinator']['lastName'] . '</em></p>';
	     }
	     $ret[] = '<p>Project period: <em>' . get_dotted_field($grant, 'startDate', $date_format) . ' &ndash; ' . get_dotted_field($grant, 'endDate', $date_format) . '</em></p>';
//	     $ret[] = json_encode($grant);
             $ret[] = '</li>';
        }
        $ret[] = '</ul>';
    } else {
		  $ret[] = '<p>' . $no_data_message . '</p>';
    }
    return implode("\n", $ret);
}

function format_person_name($ob) {
	return $ob['firstName'] . " " . $ob['lastName'] . " (" . $ob['affiliation'] . ")";
}

function grant_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
	'model' => 'visit',
	'sort_field' => 'startDate',
	'filter' => '',
	'no_data_message' => 'nessuna informazione',
	'no_data_message_en' => 'there is no data',
	'date_format' => 'd.m.Y'
    ), $atts));

    if (get_locale() !== 'it_IT') {
	$no_data_message = $no_data_message_en;
	$date_format = 'M d, Y';
    }

    $resp = dm_manager_get('grant', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = grant_manager_display($resp['data'], $date_format, $no_data_message);

    return implode("\n", $ret);
}

add_shortcode('grant_manager', 'grant_manager_shortcode');

function grant_manager_details_shortcode( $atts ) {
    $grant_id = $_GET['grant_id'];

    $dateFormat = 'M d, Y';

    $ret = array();

    $resp = dm_manager_get_by_id('grant', $grant_id);
    $grant = $resp['data'];
    if (! $grant) {
        $ret[] = "Grant not found";
    }
    else {
        $ret[] = "<h3 style='margin-top: 8px;'>" . $grant['name'] . "</h3>";
        $ret[] = "<p>";
        $ret[] = "Project Type: " . $grant['projectType'] . "<br>";
        $ret[] = "Funded by: " . $grant['fundingEntity'] . "<br>";
        $ret[] = "Period: " . date_format(date_create($grant['startDate']), $dateFormat) . " &ndash; " . date_format(date_create($grant['endDate']), $dateFormat) . "<br>";
        $ret[] = "Budget: " . $grant['budgetAmount'] . "<br>";
        if ($grant['webSite']) {
            $ret[] = "Website: <a href=\"" . $grant['webSite'] . '">' . $grant['webSite'] . "</a>";
        }
        $ret[] = "</p>";
        // $ret[] = var_export($grant, TRUE);
	      $ret[] = "<p class='mb-3'>";

        if ($grant['pi']) {
            $pi_name = format_person_name($grant['pi']);
            $ret[] = "Principal Investigator: <a href=''>" . $pi_name . "</a><br>";
        }
        if ($grant['localCoordinator'] && $grant['pi']['_id'] != $grant['localCoordinator']['_id']) {
            $ret[] = "Local coordinator: <a>" . format_person_name($grant['localCoordinator']) . "</a><br>";
        }
        $ret[] = "</p>";

        if ($grant['members']) {
            $ret[] = '<div class="mb-3 wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" data-click-to-close="true" data-auto-close="false" data-scroll="false">';
            $ret[] = '<h5 id="at-members" aria-controls="ac-members" class="c-accordion__title js-accordion-controller" role="button" tabindex=0 aria-expanded=false>Participants</h5>';
            $ret[] = '<div id="ac-members" class="c-accordion__content" hidden="hidden">' . implode(", ", array_map(format_person_name, $grant['members'])) . "</div>";
            $ret[] = "</div>";
        }

        if ($grant['description']) {
            $ret[] = '<div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" data-click-to-close="true" data-auto-close="false" data-scroll="false">';
            $ret[] = '<h5 id="at-grant-description" aria-controls="ac-grant-description" class="c-accordion__title js-accordion-controller" role="button" tabindex=0 aria-expanded=false>Description</h5>';
            $ret[] = '<div id="ac-grant-description" class="c-accordion__content" hidden="hidden">' . $grant['description'] . '</div>';
            $ret[] = "</div>";
        }
    }

    return implode("\n", $ret);
}

add_shortcode('grant_manager_details', 'grant_manager_details_shortcode');

function dm_manager_person_details_shortcode( $atts ) {
  $en = get_locale() !== 'it_IT';

  $person_id = $_GET['person_id'];

  if (! $person_id) {
    return "Persona non trovata";
  }

  $res = dm_manager_get_by_id('person', $person_id);
  $p = $res['data'];
  $res = dm_manager_get('staff', '-endDate', 'person=' . $person_id);
  $s = $res['data'];

  $res = dm_manager_get('group', 'name', 'members=' . $person_id);
  $groups = $res['data'];

  $res = dm_manager_get('grant', '-endDate', 'pi=' . $person_id);
  $grant = $res['data'];

  if (! $p) {
    return "Persona non trovata";
  }

  $imageurl = $p['photoUrl']; 

  if (! $imageurl) {
    $imageurl = 'https://i0.wp.com/www.dm.unipi.it/wp-content/uploads/2022/07/No-Image-Placeholder.svg_.png?resize=280%2C280&ssl=1';
  }

  // Generate the qualification string
  $qualification = implode(", ", array_map(function ($s) use ($en, $p) { 
    $gender = ($p['gender'] == 'Uomo') ? 'm' : 'f';
    return dm_manager_get_role_label($s['qualification'], $en, $gender); 
  }, $s));

  // Gruppo di ricerca
  $research_group = implode(", ", array_map(function ($ss) use ($en) { 
    return dm_manager_get_research_group_label($ss['SSD'], $en); 
  }, $s));

  $email = $p['email'];
  $phone = $p['phone'];
  $web = $p['personalPage'];

  // Room
  try {
    $room = $s[0]['roomAssignments'][0]['room'];
    $address  = ($room['building'] == 'X') ? 'Via Buonarroti, 1/c, ' : 'L.go B. Pontecorvo, 5, ';
    $address .= '56127 Pisa (PI), Italy.';
    $floor_desc = dm_manager_floor_label($room['floor'], $en);

    $address_desc =  ($en ? 'Building ' : 'Edificio ') . $room['building'] . ', ' . $floor_desc . ', ' 
      . ($en ? 'Room ' : 'Stanza ') .  $room['number'] . ', <br>'
      . $address;
  }
  catch (Exception $e) {
    $room = null;
  }

  if ($room) {
    $room_desc = <<<END
        <div class="d-flex justify-left">
          <div>
            <i class="fas fa-address-card mr-2"></i>
          </div>
          <div>
            {$address_desc}
          </div>
        </div>
    END;
  }

  $unipi_id = ltrim($s[0]['matricola'], 'a');
  if ($unipi_id) {
    $arpi_data = do_shortcode('[arpi id="' . $unipi_id . '"]');
    $courses_data = do_shortcode('[persona id="' . $unipi_id . '"]');

    $pub_links = [];
    if ($p['google_scholar']) { 
      $pub_links[] = [ 
        "label" => "Google Scholar", 
        "url" => "https://scholar.google.com/citations?user=" . $p['google_scholar']
      ];
    }
    if ($p['orcid']) {
      $pub_links[] = [
        "label" => "ORCID", 
        "url" => 'https://orcid.org/' . $p['orcid']
      ];
    }
    if ($p['arxiv_orcid']) {
      $pub_links[] = [
        "label" => "ArXiV", 
        "url" => "https://arxiv.org/a/" . $p['orcid']
      ];
    }
    if ($p['mathscinet']) {
      $pub_links[] = [ 
        "label" => "MathSciNet", 
        "url" => 'https://mathscinet.ams.org/mathscinet/MRAuthorID/' . $p['mathscinet']
      ];
    }

    $pub_links_html = implode(", \n", array_map(function ($x) {
      return <<<END
      <a href={$x['url']} target="_blank">{$x['label']}</a>
      END;
    }, $pub_links));

    $pub_title = $en ? "Research" : "Ricerca";
    $see_all = $en ? "See all the publications at:" : "Vedi tutte le pubblicazioni su:";

    $grant_list = [];
    foreach ($grant as $g) {
      $grant_list[] = $g;

    }
    $grant_text = implode("\n", array_map(function ($g) {
      return <<<END
        <li>{$g['name']}</li>
      END;
    }, $grant_list));

    $grant_block = "";
    if (count($grant_list) > 0) {
      $grant_block = <<<END
      <h5 class="my-2">Finanziamenti</h5>
          <ul>
            {$grant_text}
          </ul>
      END;
    }

    $recent_publications = $en ? "Recent publications" : "Pubblicazioni recenti";

    $research_accordion = <<<END
    <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" 
    data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0">
      <h4 id="at-1001" class="c-accordion__title js-accordion-controller" role="button" tabindex="0" aria-controls="ac-1001" aria-expanded="true">
        {$pub_title}
      </h4>      
      <div id="ac-1001" class="c-accordion__content" style="display: block;">
        <h5>{$recent_publications}</h5>
        {$arpi_data}
        {$see_all} {$pub_links_html}
        {$grant_block}
      </div>
    </div>  
    END;
  }

  if ($en) {
    $research_group_label = "${research_group} Research Group";
  }
  else {
    $research_group_label = "Gruppo di Ricerca in ${research_group}";
  }

  $research_group_text = $research_group ? <<<END
  <p class="my-1">
    <i class="fas fa-users mr-2"></i>{$research_group_label}
  </p>
  END : "";
  
  $email_text = $email ? <<<END
  <p class="my-1">
    <i class="fas fa-at mr-2"></i><a href="mailto:{$email}">{$email}</a>
  </p>
  END : "";

  $phone_text = $phone ? <<<END
  <p class="my-1">
    <i class="fas fa-phone mr-2"></i><a href="tel:{$phone}">{$phone}</a>
  </p>
  END : "";

  $web_text = $web ? <<<END
  <p class="my-1">
    <i class="fas fa-link mr-2"></i><a href="{$web}">{$web}</a>
  </p>
  END : "";

  // FIXME: Sotto qualifica va verificato se uno degli incarichi è Direttore del DM, Presidente CdS, 
  // Vicedirettore e vicepresidente e coordinatore dottorato. Si fanno apparire con un badge accanto al nome.

  $duties_title = $en ? 'Administrative duties' : 'Incarichi';

  $group_list = [];
  foreach ($groups as $g) {
    $group_list[] = $g;
  }

  // Sort the groups according to the following rule: 
  //  1) Groups with one person only
  //  2) Group where the user is either chair or vice
  //  3) Other groups.
  usort($group_list, function ($a, $b) use ($person_id) {
    $classifier = function ($x) use ($person_id) {
      if (count($x['members']) == 1) {
        return 1;
      }
      else if ($x['chair']['_id'] == $person_id) {
        return 2;
      }
      else if ($x['vice']['_id'] == $person_id) {
        return 3;
      }
      else {
        return 4;
      }
    };

    return $classifier($a) - $classifier($b);
  });

  $single_groups = array_filter($group_list, function ($x) { return count($x['members']) == 1; });
  $other_groups  = array_filter($group_list, function ($x) { return count($x['members']) != 1; });

  $single_group_text = implode("\n", array_map(function ($g) use ($en) {
    return <<<END
      <li>{$g['name']}</li>
    END;
  }, $single_groups));

  $other_group_text = implode("\n", array_map(function ($g) use ($person_id, $en) {
    if (! str_starts_with($g['name'], 'MAT/')) {
      $badge = "";
      if ($g['chair']['_id'] == $person_id) {
        $chair_name = $en ? "Chair" : $g['chair_title'];
        $badge .= "<span class=\"badge badge-primary mr-2\">{$chair_name}</span>";
      }
      if ($g['vice']['_id'] == $person_id) {
        $vice_name = $en ? "Deputy Chair" : $g['vice_title'];
        $badge .= "<span class=\"badge badge-primary mr-2\">{$vice_name}</span>";
      }

      return <<<END
        <li>{$g['name']} {$badge}</li>
      END;
    }
    else {
      return "";
    }
  }, $other_groups));

  // Translations
  $referente = $en ? "Representative for" : "Referente per";
  $membro = $en ? "Member of" : "Membro di";

  if (count($single_groups) > 0) {
    $single_group_block = <<<END
    <h5>{$referente}:</h5>
    <ul>
      {$single_group_text}
    </ul>
    END;
  }
  else {
    $single_group_block = "";
  }

  if (count($other_groups) > 0) {
    $other_group_block = <<<END
    <h5>{$membro}:</h5>
        <ul>
          {$other_group_text}
        </ul>
    END;
  }
  else {
    $other_group_block = "";
  }

  $duties_accordion = <<<END
  <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" 
    data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0">
      <h4 id="at-1003" class="c-accordion__title js-accordion-controller" role="button" tabindex="0" aria-controls="ac-1003" aria-expanded="true">
        {$duties_title}
      </h4>
      <div id="ac-1003" class="c-accordion__content" style="display: block;">
        {$single_group_block}
        {$other_group_block}
      </div>
    </div>  
  END;

  return <<<END
  <div class="entry-content box clearfix">
    <div class="d-flex flex-wrap align-middle">
      <div class="mr-4 mb-4">
        <img width="280" height="280" src="{$imageurl}" class="rounded img-fluid" alt="" decoding="async">
      </div>
      <div class="ml-4">
        <div class="h2 mb-3">{$p['firstName']} {$p['lastName']}</div>
        <div class="h5 mb-3">{$qualification}</div>
      {$research_group_text}
      {$room_desc}
      {$email_text}
      {$phone_text}
      {$web_text}
      </div>
    </div>
  </div>
  {$duties_accordion}
  {$research_accordion}
  {$courses_data}
  END;
}

add_shortcode('dm_manager_person_details', 'dm_manager_person_details_shortcode');


function dm_manager_group_list($atts) {
  $en = get_locale() !== 'it_IT';

  $group_id = $atts['group_id'];
  $res = dm_manager_get_by_id('group', $group_id);
  $group = $res['data'];

  $members_list = implode("\n", array_map(function ($m) {
    return <<<END
      <li>{$m['firstName']} {$m['lastName']}</li>
    END;
  }, $group['members']));

  if ($group['chair']) {
    $chair = $group['chair'];
    $chair_text = <<<END
      <li>{$chair['firstName']} {$chair['lastName']} <span class="badge badge-primary">{$group['chair_title']}</span></li>
    END;
  }
  else {
    $chair_text = "";
  }

  if ($group['vice']) {
    $chair = $group['vice'];
    $vice_text = <<<END
      <li>{$chair['firstName']} {$chair['lastName']} <span class="badge badge-primary">{$group['vice_title']}</span></li>
    END;
  }
  else {
    $vice_text = "";
  }

  return <<<END
    <h5>{$group['name']}</h5>
    <ul>
      {$chair_text}
      {$vice_text}
      {$members_list}
    </ul>
  END;
}

add_shortcode('dm_manager_group', 'dm_manager_group_list');

function generate_person_card($p, $badge = null) {
    $en = get_locale() !== 'it_IT';

    if ($p['email']) {
        $email = $p['email'];
        $email_block = <<<END
        <i class="fas fa-at mr-3"></i><a href="mailto:{$email}">{$email}</a>
        END;
    }
    else {
        $email_block = "";
    }

    if ($badge) {
        $badge_block = <<<END
        <span class="badge badge-sm badge-primary ml-2">{$badge}</span>
        END;
    }
    else {
        $badge_block = "";
    }
    
    return <<<END
    <div class="col-lg-6 col-12 py-2">
      <div class="card h-100 m-2 shadow-sm">
      <div class="card-body">
      <a href="/scheda-personale/?person_id={$p['_id']}">
          <i class="fas fa-id-card fa-fw mr-2"></i>
      </a>
      <span class="card-title h5">
        {$p['firstName']} {$p['lastName']}
        {$badge_block}
      </span><br>
        {$email_block}
      </div>
      </div>
    </div>
  END;
}

function dm_manager_group_cards($atts) {
  $en = get_locale() !== 'it_IT';

  $group_id = $atts['group_id'];
  $res = dm_manager_get_by_id('group', $group_id);
  $group = $res['data'];

  usort($group['members'], function ($a, $b) {
    return strcmp($a['lastName'], $b['lastName']);
  });

  $members_list = implode("\n", array_map(function ($m) {
    return generate_person_card($m);
  }, $group['members']));

  if ($group['chair']) {
    $chair = $group['chair'];
    $chair_text = generate_person_card($chair, $group['chair_title']);
  }
  else {
    $chair_text = "";
  }

  if ($group['vice']) {
    $vice = $group['vice'];
    $vice_text = generate_person_card($vice, $group['vice_title']);
  }
  else {
    $vice_text = "";
  }

  return <<<END
    <h5>{$group['name']}</h5>
    <div class="d-flex row justify-content-between px-2 pb-4">
      {$chair_text}
      {$vice_text}
      {$members_list}
    </div>
  END;
}

add_shortcode('dm_manager_group_cards', 'dm_manager_group_cards');

function dm_manager_person_card($atts) {
    $person_id = $atts['person_id'];
    $badge = $atts['badge'];

    $res = dm_manager_get_by_id('person', $person_id);
    $person = $res['data'];

    return generate_person_card($person, $badge);
}

add_shortcode('dm_manager_person_card', 'dm_manager_person_card');
