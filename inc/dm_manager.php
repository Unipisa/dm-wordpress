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

function dm_manager_get($fields, $table, $sort_field, $filter, $no_data_message, $date_format) {
        $ch = curl_init();

	$ret[] = '<!-- START dm_manager_get -->';

        $query = [];
        $query[] = '_sort=' . $sort_field;
	foreach (explode(",", $filter) as $f) {
	  $f=trim($f);
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

        curl_setopt($ch, CURLOPT_URL, DM_MANAGER_URL . 'visit?' . implode('&', $query));
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
	    $ret[] = '<!-- 200 OK -->';
	    if (isset($resp['data'])) {
		if (count($resp['data'])) {
 		  $ret[] = '<table class="peopletable">';
		  $ret[] = '<thead><tr>';
		  foreach ($table as $header) {
			$ret[]='<th>'.$header.'</th>';
		  }
		  $ret[] = '</tr></thead><tbody>';

		  foreach ($resp['data'] as $row) {
			$ret[]='<tr>';
			foreach ($fields as $field) {
				$ret[]='<td>'.get_dotted_field($row, $field, $date_format).'</td>';
//				$ret[]='<td>'.$row[$field].'</td>';
			}
			$ret[]='</tr>';
		  }
		  $ret[] = '</tbody></table>';
		} else {
		  $ret[] = '<p>' . $no_data_message . '</p>';
		}
	    }
        }
	$ret[] = '<!-- END dm_manager_get -->';
	return implode("\n", $ret);
    }

/* Shortcode */
function dm_manager_shortcode( $atts ) {
    extract(shortcode_atts(array(
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

    $ret = dm_manager_get($e_fields, explode(',', $table), $sort_field, $filter, $no_data_message, $date_format);
    return $ret;
}
add_shortcode('dm_manager', 'dm_manager_shortcode');
