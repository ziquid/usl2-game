<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));
  $arg3 = check_plain(arg(3));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  if (substr($arg3, 0, 3) == 'id:') { // user id

    $sql = 'select username from users where id = %d;';
    $result = db_query($sql, (int) substr($arg3, 3));
    $item = db_fetch_object($result);
    $target_id = (int) substr($arg3, 3);

  } else { // phone_id

    $sql = 'select id, username from users where phone_id = "%s";';
    $result = db_query($sql, $phone_id_to_block);
    $item = db_fetch_object($result);
    $target_id = $item->id;  

  }

  $sql = 'select * from message_blocks where fkey_blocked_users_id = %d
    and fkey_blocking_users_id = %d;';
  $result = db_query($sql, $target_id, $game_user->id);
  $block = db_fetch_object($result);
  
  if (empty($block)) { // block doesn't exist - create one
    
    $sql = 'delete from user_messages where fkey_users_from_id = %d
      and fkey_users_to_id = %d;';
    $result = db_query($sql, $target_id, $game_user->id);
    
    $sql = 'insert into message_blocks
      (fkey_blocked_users_id, fkey_blocking_users_id) values (%d, %d);';
    $result = db_query($sql, $target_id, $game_user->id);
    
    echo <<< EOF
<div class="title">Block player $item->username</div>
<div class="subtitle"><a href="/$game/user/$arg2/$arg3">$item->username</a>
  can no longer send you messages</div>
EOF;

  _button();

  } else { // delete block
    
    $sql = 'delete from message_blocks
      where fkey_blocked_users_id = %d
      and fkey_blocking_users_id = %d;';
    $result = db_query($sql, $target_id, $game_user->id);
    
    echo <<< EOF
<div class="title">Unblock player $item->username</div>
<div class="subtitle"><a href="/$game/user/$arg2/$arg3">$item->username</a>
  can again send you messages</div>
EOF;

  _button();
    
  }
  
  db_set_active('default');
