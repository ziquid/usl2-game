<?php

/**
 * Welcome the user to the game.  Also creates a new user object.
 *
 * @todo: get initial values from db, not hardcoded.
 * @todo: get IP address whitelist from db.
 */

function game_welcome_callback($game_id) {

  db_set_active('game');

  // Check to make sure not too many from the same IP address.
  $query = db_select('user_attributes', 'ua')
    ->condition('ua.k', 'last_IP', '=')
    ->condition('ua.v', ip_address(), '=')
    ->fields('ua', array('k'))
    ->execute();
  $count = $query->rowCount();

  // Allow multiple from my IP.
  if (($count > 5) && (ip_address() != '14.140.251.170') && // Amazon testing IP
    (ip_address() != '38.164.20.244') && // TI
    (ip_address() != '158.69.123.231') && // OVH2
    (ip_address() != '64.150.187.146')) {
    game_goto('error', 'E-2242'); // too many from same IP
  }

  $default_neighborhood = 81;

  try {
    db_insert('users')->fields(array(
      'phone_id' => $game_id,
      'username' => '',
      'experience' => 0,
      'level' => 1,
      'fkey_neighborhoods_id' => $default_neighborhood,
      'fkey_values_id' => 0,
      'money' => 1000,
      'energy' => 200,
      'energy_max' => 200,
      'last_bonus_date' => date('Y-m-d'),
    ))->execute();

    db_insert('user_creations')->fields(array(
      'datetime' => date('Y-m-d H:i:s'),
      'phone_id' => $game_id,
      'remote_ip' => ip_address(),
    ))->execute();
  }
  catch (Exception $e) {
    game_goto('error', 'E-5514'); // cannot create new user
  }


  db_set_active('default');

  return array(
    array(
      '#theme' => 'title_image',
    ),
    array(
      '#theme' => 'game_welcome',
      '#game_id' => $game_id,
    ),
    array(
      '#theme' => 'game_button',
      '#link' => 'quests',
    ),
  );
}
