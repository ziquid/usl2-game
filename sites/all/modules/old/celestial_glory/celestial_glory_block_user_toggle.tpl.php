<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));

  $sql = 'select id, username from users where phone_id = "%s";';
  $result = db_query($sql, $phone_id_to_block);
  $item = db_fetch_object($result);
  $target_id = $item->id;  
  
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
<div class="subtitle"><a href="/$game/user/$arg2/$phone_id_to_block">$item->username</a>
  can no longer send you messages</div>
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/user/$arg2">Continue</a>
  </div>
</div>
EOF;

  } else { // delete block
    
    $sql = 'delete from message_blocks
      where fkey_blocked_users_id = %d
      and fkey_blocking_users_id = %d;';
    $result = db_query($sql, $target_id, $game_user->id);
    
    echo <<< EOF
<div class="title">Unblock player $item->username</div>
<div class="subtitle"><a href="/$game/user/$arg2/$phone_id_to_block">$item->username</a>
  can again send you messages</div>
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/user/$arg2">Continue</a>
  </div>
</div>
EOF;
    
  }
  
db_set_active('default');
