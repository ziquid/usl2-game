<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
 
  $sql = 'delete from user_messages where id = %d;';
  $result = db_query($sql, $msg_id);
  drupal_goto($game . '/user/' . check_plain(arg(2)));
