<?php

/**
 * Shows an error message.
 *
 * @todo: hook up slack.
 */

function game_error_callback($game_id, $error_code) {

  $error_code = check_plain($error_code);

//   slack_send_message('Error ' . $error_code . ' for phone ID '
//   . $game_id, $slack_channel);

  return array(
    array(
      '#theme' => 'title_image',
    ),
    array(
      '#theme' => 'game_error',
      '#game_id' => $game_id,
      '#error_code' => $error_code,
    ),
    array(
      '#theme' => 'game_button',
      '#link' => 'home',
    ),
  );
}
