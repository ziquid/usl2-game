<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
   
  $message_orig = check_plain($_GET['message']);
  $message = _stlouis_filter_profanity($message_orig);
  $arg2 = check_plain(arg(2));
  
  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);
  
  $target = check_plain($_GET['target']);
//firep($target);

  if (strlen($message) > 0 and strlen($message) < 3) {
    echo '<div class="message-error">Your message must be at least 3
      characters long.</div>';
    $message = '';
  }
  
  if (substr($message, 0, 3) == 'XXX') {
    
    $fetch_header($game_user);
    
    echo '<div class="message-error">Your message contains words that are not
      allowed.&nbsp; Please rephrase.&nbsp; ' . $message . '</div>';
    echo '<div class="election-continue"><a href="/' . $game . '/home/' .
      $arg2 . '?message=' . $message_orig . '">' .
      t('Try again') . '</a></div>';

    db_set_active('default');
    return;
    
  }

  if (!empty($message)) {

    switch ($target) {
      
      case 'neighborhood':
        $sql = 'insert into party_messages (fkey_users_from_id,
          fkey_neighborhoods_id, message) values (%d, %d, "%s");';
        $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
          $message);
        break;

      case 'clan':

        $sql = 'insert into clan_messages (fkey_users_from_id,
          fkey_neighborhoods_id, message) values (%d, %d, "%s");';
        $result = db_query($sql, $game_user->id, $game_user->fkey_clans_id,
          $message);
        break;

      case 'values':

        if (action_use($game_user, 1)) {

          $sql = 'insert into values_messages (fkey_users_from_id,
            fkey_values_id, fkey_neighborhoods_id, message)
            values (%d, %d, %d, "%s");';
          $result = db_query($sql, $game_user->id, $game_user->fkey_values_id,
            $game_user->fkey_neighborhoods_id, $message);

        } else {

          $fetch_header($game_user);

          echo '
<div class="land failed">
  <div class="title">' .
    t('∆ Action Quantity ∆<br/><span>Insufficient</span>') . '
  </div>
  <div class="try-an-election-wrapper">
    <div class="try-an-election">
      <a href="/' . $game . '/elders_do_fill/' . $arg2 .
        '/action?destination=/' . $game . '/home/' . $arg2 .
        '?message=' . $message_orig . '">
        Refill your Action (2&nbsp;Luck)
      </a>
    </div>
  </div>
</div>';

          db_set_active('default');
          return;

        }

        break;
        
    } // switch $target
    
    db_set_active('default');
    drupal_goto($game . '/home/' . $arg2);

  }
  
  db_set_active('default');
