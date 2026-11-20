<?php

wp_enqueue_script('dm-manager-widgets', 'https://manage.dm.unipi.it/static/dmwidgets.js', [], 1.0, false);

function dm_manager_widget_shortcode($atts) {
    $id = uniqid('dm-manager-widget-');
    $fid = uniqid();
    $name = $atts['widget'];

    $props = array_merge($atts, $_GET);
    $props = json_encode((object) $props);

    // var_dump($props); exit(0);

    return <<<END
      <div id={$id}></div>
      <script>
        (function () {
          const el = document.getElementById("{$id}");
          const props = {$props}
          dmwidgets.loadComponent(el, "{$name}", props);
        })();
      </script>
    END;
}

add_shortcode('dm_manager_widget', 'dm_manager_widget_shortcode');

?>
