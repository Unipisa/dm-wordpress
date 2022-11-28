<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';

function get_dotted_field($obj, $dotted_field) {
	foreach (explode(".", $dotted_field) as $field) {
		$obj = $obj[$field];
	}
	return $obj;
}

function dm_manager_get($fields, $table, $sort_field, $filter) {
        $ch = curl_init();

	$ret[] = '<!-- START dm_manager_get -->';

        $query = [];
        $query[] = '_sort=' . $sort_field;
        if ($filter == 'current') {
                $query[] = 'startDate__lt=today';
                $query[] = 'endDate__gt=today';
        } elseif ($filter == 'past') {
                $query[] = 'endDate__lt=today';
	} elseif ($filter == 'perspective') {
                $query[] = 'startDate__gt=today';
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
		$ret[] = '<table class="peopletable">';
		$ret[] = '<thead><tr>';
		foreach ($table as $header) {
			$ret[]='<th>'.$header.'</th>';
		}
		$ret[] = '</tr></thead><tbody>';

		foreach ($resp['data'] as $row) {
			$ret[]='<tr>';
			foreach ($fields as $field) {
				$ret[]='<td>'.get_dotted_field($row, $field).'</td>';
//				$ret[]='<td>'.$row[$field].'</td>';
			}
			$ret[]='</tr>';
		}
		$ret[] = '</tbody></table>';
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
    ), $atts));

    if (get_locale() !== 'it_IT' && $tableen) {
	$table = $tableen;
    }

    $e_fields = explode(',', $fields);
    $e_fields = array_map(function ($x) { return trim($x); }, $e_fields);

    $ret = dm_manager_get($e_fields, explode(',', $table), $sort_field, $filter);
    return $ret;
}
add_shortcode('dm_manager', 'dm_manager_shortcode');
