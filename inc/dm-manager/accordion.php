<?php

function create_accordion($title, $content, $markdown = true) {
    // We need to generate a random ID
    $id = uniqid();
    $ac_id = "ac-" . $id;
    $at_id = "at-" . $id;

    $markdown_class = $markdown ? "markdown-compile" : "";

    return <<<END
<div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item" data-initially-open="false" data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0">
  <h4 id="{$at_id}" class="c-accordion__title js-accordion-controller" role="button" tabindex="0" aria-controls="{$ac_id}" aria-expanded="true">
    {$title}
  </h4>
  <div id="{$ac_id}" class="{$markdown_class} c-accordion__content" style="display: block;">{$content}</div>
</div>
END;
}

?>
