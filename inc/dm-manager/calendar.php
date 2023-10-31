<?php

// wp_register_style('dm-calendar', 'https://unipisa.github.io/dm-calendar/lib/dm-calendar.css', false, '1.0', 'all' );

function calendar_shortcode( $atts ) {
    wp_enqueue_style('dm-calendar');
    return <<<EOF
        <div class="dm-calendar"></div>
        <script src="https://unipisa.github.io/dm-calendar/lib/only-phd.iife.js"></script>
    EOF;
}

add_shortcode('event_calendar', 'calendar_shortcode');
