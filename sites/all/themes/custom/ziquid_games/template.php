<?php

/**/
function ziquid_games_preprocess_html(&$variables) {

// viewport
  $meta_viewport = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' =>  'width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0',
    )
  );

  drupal_add_html_head($meta_viewport, 'meta_viewport');

} // preprocess_html()
