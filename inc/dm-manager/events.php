<?php

include_once('people.php');
include_once('labels.php');
include_once('accordion.php');

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

    // Sort the lessons by date
    $sorted_lessons = $data['lessons'];
    usort($sorted_lessons, function ($a, $b) {
        return $a['date'] > $b['date'];
    });

    $speaker = format_person_name($data['lecturer']);
    $lessons = "<ul>";
    foreach ($sorted_lessons as $l) {
        $d = new DateTime($l['date'], new DateTimeZone('UTC'));
        $d->setTimeZone(new DateTimeZone('Europe/Rome'));
        $formatted_date = $d->format('Y-m-d H:i');
        $room_name = $l['conferenceRoom']['name'];
        $lessons .= <<<END
           <li>{$formatted_date} ({$l['duration']} minutes), {$room_name}.</li>
        END;
    }
    $lessons .= "</ul>";

    $person_card = "";
    foreach ($data['lecturers'] as $lecturer) {
        $person_card .= generate_external_person_card($lecturer);
    }

    $description = create_accordion("Description", $data['description']);
    $lecturer_title = count($data['lecturers']) > 1 ? "Lecturers" : "Lecturer";

    $output = <<<END
    <h3 class="mb-3">{$data['title']}</h3>
    <h4>{$lecturer_title}</h4>
    <div class="row">{$person_card}</div>
    <div class="my-5">{$description}</div>
    <h4>Scheduled lessons</h4>
    <p>
      {$lessons}
    </p>
    END;
    return $output;
}

add_shortcode('phd_course_detail', 'phd_course_detail_shortcode');

// Getting the list of seminars, with optional filters
function dm_manager_seminars_shortcode( $atts ) {
    extract(shortcode_atts(array(
        'filters' => false,
    ), $atts));

    $resp = dm_manager_get('public/seminars', "-endDate", $filters);

    $output = "";
    foreach ($resp as $seminar) {
        $datetime = dm_manager_datetime($seminar['startDatetime']);
        $speaker = format_person_name($seminar['speaker']);

        $output .= <<<END
            <div class="">                
                <h4>Seminar: {$seminar['title']} ({$speaker})</h4>
                <p>
                  {$datetime} &ndash;
                  {$seminar['conferenceRoom']['name']}
                </p>
            </div>
        END;
    }
    
    return $output . var_export($resp, true);
}

add_shortcode('seminars', 'dm_manager_seminars_shortcode');
?>
