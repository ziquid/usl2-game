<?php

/**
 * @file
 */

/**
 * Show the user object.
 */
function game_user_callback($game_user) {

  $arg3 = check_plain(arg(3));
  if (is_numeric($arg3)) {
    $arg3 = intval($arg3);
  }

  $user_to_show = !empty($arg3) ? game_fetch_user_by_id($arg3) : $game_user;

  db_set_active('default');

  return array(
    array(
      '#theme' => 'game_header',
      '#game_user' => $game_user,
    ),
    array(
      '#theme' => 'game_user',
      '#game_user' => $game_user,
      '#user_to_show' => $user_to_show,
    ),
    array(
      '#theme' => 'game_button',
      '#link' => 'quests',
    ),
  );
}
