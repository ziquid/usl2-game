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

  // Include Roboto font from Google.
  $font = array(
    '#type' => 'html_tag',
    '#tag' => 'link',
    '#attributes' => array(
      'href' =>  'https://fonts.googleapis.com/css?family=Roboto:300,300italic,400,400italic,700,700italic&subset=latin,latin-ext',
      'rel'  => 'stylesheet',
      'type' => 'text/css',
    )
  );
  drupal_add_html_head($font, 'font_roboto');

  // Include Roboto Slab font from Google.
  $font = array(
    '#type' => 'html_tag',
    '#tag' => 'link',
    '#attributes' => array(
      'href' =>  'https://fonts.googleapis.com/css?family=Roboto+Slab:300,400&subset=latin,latin-ext&effect=putting-green',
      'rel'  => 'stylesheet',
      'type' => 'text/css',
    )
  );
  drupal_add_html_head($font, 'font_roboto_slab');

} // preprocess_html()

/**
 * Implementation of hook_preprocess_page().
 */
function ziquid_games_preprocess_page(&$vars) {

  $game_user = game_user_load(check_plain(arg(2)));
  $header = array(
    '#theme' => 'game_header',
    '#game_user' => $game_user,
  );
  $vars['page']['header'] = drupal_render($header);
  $vars['page']['footer'] =
    '<img class="center-block" src="http://www.ziquid.com/sites/default/files/ziquid_800_2.png"/>
    <div class="design-studio">Design Studio</div>';

}
