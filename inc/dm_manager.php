<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';
include_once('unimap.php');

include_once('dm-manager/api.php');
include_once('dm-manager/labels.php');
include_once('dm-manager/rooms.php');
include_once('dm-manager/events.php');
include_once('dm-manager/people.php');



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
        if ($row['affiliation']) $ret[] = '(' . $row['affiliation']['name'] . ')';
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
              $person_id = $advisor['_id'];
              // var_dump($advisor);
              $isInternal = isInternal($advisor);
              if ($isInternal)
                $val[]= "<a href=\"/en/scheda-personale/?person_id=$person_id\">" . $advisor['firstName']."  ".$advisor['lastName'] . '</a>';
              else
                $val[]= $advisor['firstName']."  ".$advisor['lastName'];
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
    'date_format' => false,
    'list_mode' => false
  ), $atts));
  
  if (get_locale() === 'it_IT') {
    if (!$date_format) $date_format = 'd.m.Y';
  } else {
    if ($tableen) {
      $table = $tableen;
    }
    $no_data_message = $no_data_message_en;
    if (!$date_format) $date_format = 'M d, Y';
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
//  $filter = implode(',', array_merge(explode(',', $filter),['current']));
  $resp = dm_manager_get('staff', $sort_field, $filter);
  $ret[] = "";
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
        $link = isInternal($grant['pi']) ? '/en/scheda-personale/?person_id=' . $grant['pi']['_id'] : '';
        if ($link)
          $ret[] = "<p class='mb-0'>Principal Investigator: <a href='$link'>" . $grant['pi']['firstName'] . ' ' .$grant['pi']['lastName'] . '</a></p>';
        else
          $ret[] = "<p class='mb-0'>Principal Investigator: <em>" . $grant['pi']['firstName'] . ' ' .$grant['pi']['lastName'] . '</em></p>';
      }
      if ($grant['localCoordinator'] && $grant['localCoordinator']['_id'] != $grant['pi']['_id']) {
        $link = isInternal($grant['localCoordinator']) ? '/en/scheda-personale/?person_id=' . $grant['localCoordinator']['_id'] : '';
        if ($link)
          $ret[] = "<p class='mb-0'>Coordinator of the Research Unit: <a href='$link'>" . $grant['localCoordinator']['firstName'] . ' ' .$grant['localCoordinator']['lastName'] . '</a></p>';
        else
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
      $link = isInternal($grant['pi']) ? '/en/scheda-personale/?person_id=' . $grant['pi']['_id'] : '';
      $ret[] = "Principal Investigator: <a href='$link'>" . $pi_name . "</a><br>";
    }
    if ($grant['localCoordinator'] && $grant['pi']['_id'] != $grant['localCoordinator']['_id']) {
      $link = isInternal($grant['localCoordinator']) ? '/en/scheda-personale/?person_id=' . $grant['localCoordinator']['_id'] : '';
      $ret[] = "Local coordinator: <a href='$link'>" . format_person_name($grant['localCoordinator']) . "</a><br>";
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
  <ul>
  {$chair_text}
  {$vice_text}
  {$members_list}
  </ul>
  END;
}

add_shortcode('dm_manager_group', 'dm_manager_group_list');


function dm_manager_group_cards($atts) {
  $en = get_locale() !== 'it_IT';
  
  $group_id = $atts['group_id'];
  $isInternal = $atts['isinternal'];
  
  $res = dm_manager_get_by_id('group', $group_id);
  $group = $res['data'];
  
  usort($group['members'], function ($a, $b) {
    return strcmp($a['lastName'], $b['lastName']);
  });
  
  $group_members = $group['members'];
  
  // We setup a filter for the members based on their isInternal status in (any of) their affiliation
  $member_filter = function ($x) { return true; };

  // if ($isInternal !== null) {
  //   // Since the group does not contain the information on the staff, just 
  //   // the person object, we retrieve a list of all current staffs, and then
  //   // use it to filter the members array.
  //   $res = dm_manager_get('staff', '-endDate', 'startDate__lte_or_null=today,endDate__gte_or_null=today');
  //   $current_staff = $res['data'];
    
  //   // We are using an array with keys as IDs to make the following lookup O(1) or O(log n), 
  //   // depending on what the current implementation in PHP does.
  //   $current_staff_ids = array_flip(array_map(function ($s) {
  //     return $s['person']['_id'];
  //   }, $current_staff));
    
  //   // Filter the members of the group excluding the ones external
  //   if ($isInternal == '1') {
  //     $member_filter = function ($x) use ($current_staff_ids) { return array_key_exists($x['_id'], $current_staff_ids); };
  //   }
  //   if ($isInternal == '0') {
  //     $member_filter = function ($x) use ($current_staff_ids) { return ! array_key_exists($x['_id'], $current_staff_ids); };
  //   }
  // }

  // Whenever isInternal={0|1} is specified, we set up a true member filter
  if ($isInternal !== null) {
    if ($isInternal == '1') {
      $member_filter = function ($x) { 
        return count($x['staffs']) > 0 && $x['staffs'][0]['isInternal'];
      };
    }
    if ($isInternal == '0') {
      $member_filter = function ($x) { 
        return count($x['staffs']) == 0 || !$x['staffs'][0]['isInternal'];
     };
    }
  }
  
  $group_members = array_filter($group['members'], $member_filter);
  
  $members_list = implode("\n", array_map(function ($m) {
    return generate_person_card($m);
  }, $group_members));
  
  if ($group['chair'] && $member_filter($group['chair'])) {
    $chair = $group['chair'];
    $chair_text = generate_person_card($chair, $group['chair_title']);    
  }
  else {
    $chair_text = "";
  }
  
  if ($group['vice'] && $member_filter($group['vice'])) {
    $vice = $group['vice'];
    $vice_text = generate_person_card($vice, $group['vice_title']);
  }
  else {
    $vice_text = "";
  }
  
  return <<<END
  <div class="d-flex row justify-content-between px-2 pb-4">
  {$chair_text}
  {$vice_text}
  {$members_list}
  </div>
  END;
}

add_shortcode('dm_manager_group_cards', 'dm_manager_group_cards');

