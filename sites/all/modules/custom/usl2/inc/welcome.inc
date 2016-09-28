<?php

function game_welcome_callback($game_id) {

  db_set_active('game');

  $default_neighborhood = 81;
  $default_value = 'Greenbacks';
/*
// check to make sure not too many from the same IP address
  $sql = 'select count(`value`) as count from user_attributes
    where `key` = "last_IP" and `value` = "%s";';
  $result = db_query($sql, array(ip_address()));
  $item = db_fetch_object($result);

// allow multiple from my IP
  if (($item->count > 5) && (ip_address() != '14.140.251.170') && // Amazon testing IP
    (ip_address() != '38.164.20.244') && // TI
    (ip_address() != '158.69.123.231') && // OVH2
    (ip_address() != '64.150.187.146')) {
    game_goto('error', 'E-2242');
  }

  $sql = 'insert into users set phone_id = "%s", username = "", experience = 0,
    level = 1, fkey_neighborhoods_id = %d, fkey_values_id = 0,
    `values` = "%s",
    money = 1000, energy = 200, energy_max = 200';
  $result = db_query($sql, array($phone_id, $default_neighborhood,
    $default_value));

  $sql = 'insert into user_creations set datetime = "%s", phone_id = "%s",
    remote_ip = "%s";';
  $result = db_query($sql, array(date('Y-m-d H:i:s'), $phone_id, ip_address()));
*/

  db_set_active('default');

  return array(
    array(
      '#theme' => 'title_image',
    ),
    array(
      '#theme' => 'game_welcome',
      '#attributes' => array(
        'game_id' => $game_id,
      ),
    ),
    array(
      '#theme' => 'game_button',
       '#attributes' => array(
         'link' => 'quests',
       ),
    ),
  );
}
