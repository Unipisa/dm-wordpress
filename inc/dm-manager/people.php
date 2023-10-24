<?php

function isInternal($p) {
    return count($p['staffs']) > 0 && $p['staffs'][0]['isInternal'];
}

function people_manager_display($data, $fields, $table, $date_format, $no_data_message) {
  $ret[] = '<!-- 200 OK -->';
  $en = get_locale() !== 'it_IT';
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
        if ($field == 'building' || $field == 'roomAssignment.room.building') {
          $val = dm_manager_building_name($val, $en);
        }
        else if ($field == 'roomNumber') {
          $val = str_replace("Piano", "Floor", $val);
        }
        else if ($field == 'roomAssignment.room.floor') {
          if ($val == "0") {
            $val = $en ? "Ground floor" : "Piano terra";
          }
          else if ($val == "1") {
            $val = $en ? "First floor" : "Primo piano";
          }
          else if ($val == "2") {
            $val = $en ? "Second floor" : "Secondo piano";
          }
        }
        else if ($field == 'person.email' && $val!='') {
          $val = '<a href="mailto:' . $val . '">'
          . '<i class="far fa-envelope fa-fw"></i><span class="d-none d-lg-inline">'
          . $val . '</span></a>';
        } else if ($field == 'person.phone' && $val!='') {
          $val = '<a href="tel:' . $val . '">'
          . '<i class="fas fa-phone-alt fa-fw"></i> <span class="d-none d-lg-inline">'
          . $val.'</span></a>';
        } else if ($field == 'person._id') {
          $val = '<a href="' . ($en ? '/en' : '') . '/scheda-personale/?person_id=' . $val . '">'
          . '<i class="fas fa-id-card fa-fw"></i></a>';
        } else if ($field == 'affiliations' || $field == 'person.affiliations') {
          $val = implode(', ', array_map(function ($a) { return $a['name']; }, $val));
	} else if ($field == 'SSD_name') {
	  $val = get_dotted_field($row, 'SSD', $date_format);
	  $val = dm_manager_get_research_group_label($val, $en);
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


function dm_manager_person_details_shortcode( $atts ) {
    $en = get_locale() !== 'it_IT';
    $debug = [];
    $debug[] = "<!-- dm_manager_person_details_shortcode -->";
    
    
    $dateFormat = 'M d, Y';
    $person_id = $_GET['person_id'];
    
    if (! $person_id) {
      return "Persona non trovata";
    }
    
    $res = dm_manager_get_by_id('person', $person_id);
    $p = $res['data'];
    $res = dm_manager_get('staff', '-endDate', 'person=' . $person_id . ',endDate__gte_or_null=today,startDate__lte_or_null=today');
    $s = $res['data'];
    $debug[] = $res['debug'];
    
    // Get the groups for which the user is either member, chair, or vice
    $res = dm_manager_get('group', 'name', 'members=' . $person_id . ',endDate__gte_or_null=today,startDate__lte_or_null=today');
    $groups = $res['data'];
    $res = dm_manager_get('group', 'name', 'chair=' . $person_id . ',endDate__gte_or_null=today,startDate__lte_or_null=today');
    $groups = array_merge($groups, $res['data']);
    $res = dm_manager_get('group', 'name', 'vice=' . $person_id . ',endDate__gte_or_null=today,startDate__lte_or_null=today');
    $groups = array_merge($groups, $res['data']);
    
    // Remove duplicates, if any
    $deduplicated_groups = [];
    foreach ($groups as $g) {
      $deduplicated_groups[$g['_id']] = $g;
    }
    $groups = $deduplicated_groups;
    
    // For grants, we need to get the list of the ones where the user is either PI, localCoordinator, or member. 
    // We get them all, merge them, and then sort them by endDate. 
    $grant = [];
    $res = dm_manager_get('grant', '-endDate', 'pi=' . $person_id . ",endDate__gte=today,startDate__lte=today");
    $grant_pi = $res['data'];
    $res = dm_manager_get('grant', '-endDate', 'localCoordinator=' . $person_id . ",endDate__gte=today,startDate__lte=today");
    $grant_lc = $res['data'];
    $res = dm_manager_get('grant', '-endDate', 'members=' . $person_id . ",endDate__gte=today,startDate__lte=today");
    $grant_member = $res['data'];
    foreach ($grant_pi as $g) { $grant[$g['_id']] = $g; }
    foreach ($grant_lc as $g) { $grant[$g['_id']] = $g; }
    foreach ($grant_member as $g) { $grant[$g['_id']] = $g; }
    usort($grant, function ($g1, $g2) { return (($g1['endDate'] < $g2['endDate']) ? 1 : -1); });
    
    if (! $p) {
      return "Persona non trovata";
    }
    
    $imageUrl = $p['photoUrl']; 
    
    if (! $imageUrl || $imageUrl == "") {
      $imageUrl = "https://www.dm.unipi.it/wp-content/uploads/2022/07/No-Image-Placeholder.svg_.png";
    }
    
    // Generate the qualification string
    $gender = ($p['gender'] == 'Uomo') ? 'm' : 'f';
    $qualification = implode(", ", array_map(function ($s) use ($en, $gender, $person_id) {
      return dm_manager_get_role_label($s['qualification'], $en, $gender);
    }, $p['staffs']));
    $debug[] = "<!--" . json_encode($p) . "-->";
    
    // Gruppo di ricerca
    $research_group = dm_manager_get_research_group_label($p['staff']['SSD'], $en);
    
    $email = $p['email'];
    $phone = $p['phone'];
    $web = $p['personalPage'];
    
    // Room
    try {
      $room = $s[0]['roomAssignments'][0]['room'];
      $address  = ($room['building'] == 'X') ? 'Via Buonarroti, 1/c, ' : 'L.go B. Pontecorvo, 5, ';
      $address .= '56127 Pisa (PI), Italy.';
      $floor_desc = dm_manager_floor_label($room['floor'], $en);
      
      $address_desc =  dm_manager_building_name($room['building'], $en) . ', ' . $floor_desc . ', ' 
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
      
      // Everybody has an ARPI publication link
      $unimap = new Unimap();
      $arpilink = $unimap->getArpiLink($unipi_id);
      if ($arpilink) {
        $pub_links[] = [
          "label" => "Arpi", 
          "url" => $arpilink
        ];
      }
      
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
      $grant_text = implode("\n", array_map(function ($g) use ($dateFormat, $en) {
        $sd = get_dotted_field($g, 'startDate', $dateFormat);
        $ed = get_dotted_field($g, 'endDate', $dateFormat);
        $pp_text = $en ? "Project period" : "Periodo";
        return <<<END
        <li>
        <a href="/research/grant-details/?grant_id={$g['_id']}">{$g['name']}</a> 
        <span class="text-muted small">({$g['projectType']})</span><br>
        Principal Investigator: <em>{$g['pi']['firstName']} {$g['pi']['lastName']}</em><br>
        {$pp_text}: {$sd} &ndash; {$ed}
        </li>
        END;
      }, $grant_list));
      
      $finanziamenti_desc = $en ? 'Grants' : 'Finanziamenti';
      
      $grant_block = "";
      if (count($grant_list) > 0) {
        $grant_block = <<<END
        <h5 class="my-2">{$finanziamenti_desc}</h5>
        <ul>
        {$grant_text}
        </ul>
        END;
      }
      
      $recent_publications = "<h5>" . ($en ? "Recent publications" : "Pubblicazioni recenti") . "</h5>";
      
      if ($arpi_data == "") {
        $recent_publications = "";
      }
      
      if ($grant_text == "" && $arpi_data == "" && !$p['orcid'] && !$p['google_scholar']) {
        $research_accordion = "";
      }
      else {
        $research_accordion = <<<END
        <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" 
        data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0">
        <h4 id="at-1001" class="c-accordion__title js-accordion-controller" role="button" tabindex="0" aria-controls="ac-1001" aria-expanded="true">
        {$pub_title}
        </h4>      
        <div id="ac-1001" class="c-accordion__content" style="display: block;">
        {$recent_publications}
        {$arpi_data}
        {$see_all} {$pub_links_html}
        {$grant_block}
        </div>
        </div>  
        END;
      }
      
      
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
    
    $single_group_text = trim(implode("\n", array_map(function ($g) use ($en) {
      if (!str_starts_with($g['name'], 'MAT/')) {
        return <<<END
        <li>{$g['name']}</li>
        END;
      }
      else {
        return "";
      }
    }, $single_groups)));
    
    $other_group_text = trim(implode("\n", array_map(function ($g) use ($person_id, $en) {
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
    }, $other_groups)));
    
    // Translations
    $membro = $en ? "Member of" : "Membro di";
    
    if ($single_group_text != "") {
      $single_group_block = <<<END
      <ul>
      {$single_group_text}
      </ul>
      END;
    }
    else {
      $single_group_block = "";
    }
    
    if ($other_group_text != "") {
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
    
    if ($other_group_text != "" || $single_group_text != "") {
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
    }
    else {
      $duties_accordion = "";
    }
    
    $about = htmlspecialchars($en ? $p['about_en'] : $p['about_it']);
    if ($about !== "") {
      $about = <<<END
      <p class="mb-4">
      {$about}
      </p>
      END;
    }
    $debug_text = implode("\n",$debug);
    return <<<END
     {$debug_text} 
    <div class="entry-content box clearfix mb-0">
    <div class="d-flex flex-wrap align-middle">
    <div class="mr-4 mb-4">
    <img width="280" height="280" src="{$imageUrl}" class="rounded img-fluid" alt="" decoding="async">
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
    {$about}
    {$duties_accordion}
    {$research_accordion}
    {$courses_data}
    END;
  }
  
add_shortcode('dm_manager_person_details', 'dm_manager_person_details_shortcode');


function generate_external_person_card($p, $badge = null) {
    $en = get_locale() !== 'it_IT';
    $prefix = $en ? 'en/' : '';
    
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
    
    $pname = format_person_name($p);
    
    return <<<END
    <div class="col-lg-6 col-12 py-2">
    <div class="card h-100 m-2 shadow-sm">
    <div class="card-body">
    <i class="fas fa-id-card fa-fw"></i>
    <span class="card-title ml-2 h5">
    {$pname}
    {$badge_block}
    </span><br>
    {$email_block}
    </div>
    </div>
    </div>
    END;
  }
  
  function generate_person_card($p, $badge = null) {
    $en = get_locale() !== 'it_IT';
    $prefix = $en ? 'en/' : '';
    
    if (!$p['staffs'] || !$p['staffs'][0]['isInternal']) {
      return generate_external_person_card($p, $badge);
    }
    
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
    <a href="/${prefix}scheda-personale/?person_id={$p['_id']}"><i class="fas fa-id-card fa-fw"></i></a>
    <span class="card-title ml-2 h5">
    {$p['firstName']} {$p['lastName']}
    {$badge_block}
    </span><br>
    {$email_block}
    </div>
    </div>
    </div>
    END;
  }


function dm_manager_person_card($atts) {
    $person_id = $atts['person_id'];
    $badge = $atts['badge'];
    
    $res = dm_manager_get_by_id('person', $person_id);
    $person = $res['data'];
    
    return generate_person_card($person, $badge);
}
  
add_shortcode('dm_manager_person_card', 'dm_manager_person_card');

?>