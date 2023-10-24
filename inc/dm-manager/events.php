<?php

function event_manager_shortcode( $atts ) {
    $resp = dm_manager_get('public/seminars', "sort", "filter");
    $ret[] = "<ul>";
    foreach($resp as $row) { 
      $ret[] = "<li><a href='/seminario/?id=".$row['_id']."'>".$row['title']."</a></li>";
    }
    $ret[] = "</ul>";
    return implode("\n", $ret);
}
  
add_shortcode('event_manager', 'event_manager_shortcode');

// Shortcode to show an event page
function phd_course_detail_shortcode( $atts ) {
    $phd_course_id = $_GET['phd_course_id'];
    if ($phd_course_id) {
        $data = dm_manager_get_by_id('event-phd-course', $phd_course_id);
        $data = $data['data'];
    }

    $speaker = format_person_name($data['lecturer']);
    $lessons = "<ul>";
    foreach ($data['lessons'] as $l) {
        $lessons .= <<<END
           <li>{$l['date']} ({$l['duration']} minutes).</li>
        END;
    }
    $lessons .= "</ul>";

    $output = <<<END
    <h2 class="mb-3">{$data['title']}</h2>
    <p>
      <strong>Lecturer</strong>: {$speaker}.<br>
    </p>
    <h3>Scheduled lessons</h3>
    <p>
      {$lessons}
    </p>
    END;
    return $output;
}

add_shortcode('phd_course_detail', 'phd_course_detail_shortcode');

?>