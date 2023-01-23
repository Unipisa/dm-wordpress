<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';

function get_dotted_field($obj, $dotted_field, $date_format) {
	foreach (explode(".", $dotted_field) as $field) {
		$obj = $obj[$field];
	}
	if (in_array($dotted_field, ['startDate','endDate'])) {
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
    // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

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
                $query[] = 'startDate__lt=today';
                $query[] = 'endDate__gt=today';
          } elseif ($key == 'past') {
                $query[] = 'endDate__lt=today';
	  } elseif ($key == 'perspective') {
                $query[] = 'startDate__gt=today';
          } elseif ($key == 'year') {
		$query[] = 'startDate__lte=' . $val . '-12-31&endDate__gte=' . $val . '-01-01';
          } else {
		$query[] = $key . '=' . $val;
	  }
	}

	$ret[] = '<!-- QUERY_STRING ' . implode('&', $query);

        curl_setopt($ch, CURLOPT_URL, DM_MANAGER_URL . $model . '?' . implode('&', $query));
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

function visit_manager_display($data, $fields, $table, $date_format, $no_data_message) {
    $ret[] = '<!-- 200 OK -->';
    if (count($data)) {
 		  $ret[] = '<table class="peopletable">';
		  $ret[] = '<thead><tr>';
		  foreach ($table as $header) {
			$ret[]='<th>'.$header.'</th>';
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

    $filter = 'publish=1,' . $filter;

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);

    $resp = dm_manager_get('visit', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = visit_manager_display($resp['data'], $e_fields, explode(',', $table), $date_format, $no_data_message);
    return implode("\n", $ret);
}

add_shortcode('dm_manager', 'visit_manager_shortcode');
add_shortcode('visit_manager', 'visit_manager_shortcode');

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
