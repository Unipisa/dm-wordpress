<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';

function dm_manager_get() {
        $ch = curl_init();

	$ret[] = '<!-- START dm_manager_get -->';

        curl_setopt($ch, CURLOPT_URL, DM_MANAGER_URL . 'visit?_sort=lastName');
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
	    $fields = ['firstName', 'lastName', 'affiliation', 'roomNumber'];
	    if (isset($resp['data'])) {
		$ret[] = '<table>';

		$ret[] = '<tr>';
		foreach ($fields as $field) {
			$ret[]='<th>'.$field.'</th>';
		}
		$ret[] = '</tr>';

		foreach ($resp['data'] as $row) {
			$ret[]='<tr>';
			foreach ($fields as $field) {
				$ret[]='<td>'.$row[$field].'</td>';
			}
			$ret[]='</tr>';
		}
		$ret[] = '</table>';
	    }
        }
	$ret[] = '<!-- END dm_manager_get -->';
	return implode("\n", $ret);
    }

/* Shortcode */
function dm_manager_shortcode( $atts ) {
    $ret = dm_manager_get();
    return $ret;
}
add_shortcode('dm_manager', 'dm_manager_shortcode');
