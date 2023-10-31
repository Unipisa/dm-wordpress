<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


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
        $query[] = 'endDate__lt=today';
      } elseif ($key == 'perspective') {
        $query[] = 'startDate__gt=today';
      } elseif ($key == 'year') {
        $query[] = 'startDate__lte_or_null=' . $val . '-12-31&endDate__gte_or_null=' . $val . '-01-01';
      } elseif ($key == 'dateyear') {
        $query[] = 'date__lte=' . $val . '-12-31&date__gte=' . $val . '-01-01';
      } else {
        $query[] = $key . '=' . urlencode($val);
      }
    }
    
    $base = DM_MANAGER_URL;

    // LR: Sostituito strpos con str_contains, ma commentato perché assumo che non ci serva più. 
    // if (str_contains($model, "public/")) {
    //   $base = 'https://manage.develop.lb.cs.dm.unipi.it/api/v0/';
    // }
  
    $url = $base . $model . '?' . implode('&', $query);
    $ret[] = '<!-- QUERY_STRING [' . $url . '] -->';
    
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

?>