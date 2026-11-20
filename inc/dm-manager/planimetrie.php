<?php

wp_register_script('dm-planimetrie', 'https://unipisa.github.io/dm-planimetrie/lib/dm-planimetrie-element.iife.js');

function planimetrie_shortcode( $atts ) {
    wp_enqueue_script('dm-planimetrie');

    return <<<EOF
    <dm-planimetrie></dm-planimetrie>
    EOF;
}

add_shortcode('planimetrie', 'planimetrie_shortcode');

