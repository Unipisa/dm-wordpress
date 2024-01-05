<?php


wp_register_script('dm-calendar', 'https://unipisa.github.io/dm-calendar/lib/dm-calendar-element.iife.js');

function calendar_shortcode( $atts ) {
    wp_enqueue_script('dm-calendar');

    $includes = !empty($atts['includes']) ? $atts['includes'] : 'phd-courses seminar-category=pysanum seminar-category=baby-geometri-seminar seminar-category=seminari-map';

    return <<<EOF
    <dm-calendar
        endpoint="https://manage.dm.unipi.it"
        includes="{$includes}"></dm-calendar>
    EOF;
}

add_shortcode('event_calendar', 'calendar_shortcode');


/*
wp_register_script('dm-calendar', 'https://unipisa.github.io/dm-calendar/lib/dm-calendar-element.iife.js');

function calendar_shortcode( $atts ) {
    wp_enqueue_script('dm-calendar');
    return <<<EOF
    <dm-calendar
        endpoint="https://manage.dm.unipi.it"
        includes="phd-courses seminar-category=pysanum seminar-category=baby-geometri-seminar seminar-category=seminari-map"></dm-calendar>
    EOF;
}

add_shortcode('event_calendar', 'calendar_shortcode');

*/

