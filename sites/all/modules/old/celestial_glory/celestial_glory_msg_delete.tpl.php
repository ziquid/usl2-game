<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
   $arg2 = check_plain(arg(2));

// check permissions
  $sql = 'select fkey_users_to_id
    from user_messages
    where id = %d;';
  $result = db_query($sql, $msg_id);
  $msg = db_fetch_object($result);

  if ($msg->fkey_users_to_id != $game_user->id) { // not recipient of msg?
// FIXME jwc 10Apr2014 -- deduct karma
    db_set_active('default');
    drupal_goto($game . '/home/' . $arg2);
  }

  competency_gain($game_user, 'pruner');

  $sql = 'delete from user_messages where id = %d;';
  $result = db_query($sql, $msg_id);

  db_set_active('default');
  drupal_goto($game . '/user/' . $arg2);
