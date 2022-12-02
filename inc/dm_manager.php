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
				$ret[]='<td>'.get_dotted_field($row, $field, $date_format).'</td>';
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
	'filter' => false,
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
	     if ($grant['webSite'] != '') {
		$ret[] = '<a href="' . $grant['webSite'] . '">';
                $close = '</a>';
	     }
             $ret[] = $grant['name'] . '<small class="text-muted">';
             $ret[] = '(' . $grant['projectType'] .')</small>'.$close.'</h5>';
	     if ($grant['pi']) {
		$ret[] = '<p class="mb-0">Principal Investigator: <em>' . $grant['pi']['firstName'] . ' ' .$grant['pi']['lastName'] . '</em></p>';
	     }
             if ($grant['localCoordinator']) {
		$ret[] = '<p class="mb-0">Coordinator of the Research Unit: <em>' . $grant['localCoordinator']['firstName'] . ' ' .$grant['localCoordinator']['lastName'] . '</em></p>';
	     }
	     if (count($grant['members'])>0) {
	        $ret[] = '<p class="mb-0">Members of the Research Unit: <em>';
		$comma = '';
                foreach ($grant['members'] as $member) {
		    $ret[] = $comma . $member;
		    $comma = ', ';
		}
	        $ret[] = '</em></p>';
	     }
	     $ret[] = json_encode($grant);
             $ret[] = '</li>';
        }
        $ret[] = '</ul>';
    } else {
		  $ret[] = '<p>' . $no_data_message . '</p>';
    }
    return implode("\n", $ret);
}

function grant_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
	'model' => 'visit',
        'fields' => 'cognome',
        'table' => false,
        'tableen' => false,
	'sort_field' => false,
	'filter' => false,
	'no_data_message' => 'nessuna informazione',
	'no_data_message_en' => 'there is no data',
	'date_format' => 'd.m.Y'
    ), $atts));

    if ($model == 'visit' && !$sort_field) $sort_field = 'person';

    if ($model == 'grant' && !$sort_field) $sort_field = 'startDate';

    if (get_locale() !== 'it_IT') {
	if ($tableen) {
		$table = $tableen;
	}
	$no_data_message = $no_data_message_en;
	$date_format = 'M d, Y';
    }

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);

    $resp = dm_manager_get('grant', $sort_field, $filter);
    $ret[] = $resp['debug'];
    $ret[] = grant_manager_display($resp['data'], $e_fields, explode(',', $table), $date_format, $no_data_message);
    return implode("\n", $ret);
}

add_shortcode('grant_manager', 'grant_manager_shortcode');

