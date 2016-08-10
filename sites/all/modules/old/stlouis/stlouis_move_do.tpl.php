<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// random hood -- april fools 2013
/*
  if (mt_rand(0, 1) > 0) {

    $sql = 'select id from neighborhoods where xcoor > 0 and ycoor > 0
      order by rand() limit 1;';
    $result = db_query($sql);
    $item = db_fetch_object($result);
    $neighborhood_id = $item->id;

  }
*/
  if ($neighborhood_id == $game_user->fkey_neighborhoods_id) {
    
    $game_user = $fetch_user();
    $fetch_header($game_user);

    echo <<< EOF
<div class="title">
  You are already in $game_user->location
</div>
<div class="election-continue">
  <a href="/$game/move/$arg2/0">
    Try again
  </a>
</div>
EOF;

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"move-failed already-there\"/>\n-->";

    db_set_active('default');
    return;
      
  }
  
  if ($neighborhood_id > 0) {
    
    $sql = 'select * from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $cur_hood = db_fetch_object($result);
firep($cur_hood);

    $sql = 'select * from neighborhoods where id = %d;';
    $result = db_query($sql, $neighborhood_id);
    $new_hood = db_fetch_object($result);
firep($new_hood);
    
    $distance = floor(sqrt(pow($cur_hood->xcoor - $new_hood->xcoor, 2) +
      pow($cur_hood->ycoor - $new_hood->ycoor, 2)));
      
    $actions_to_move = floor($distance / 8);
    
    $sql = 'SELECT equipment.speed_increase as speed_increase, 
      action_verb, chance_of_loss, equipment.id, name, upkeep from equipment 

      left join equipment_ownership
        on equipment_ownership.fkey_equipment_id = equipment.id
        and equipment_ownership.fkey_users_id = %d
        
      where equipment_ownership.quantity > 0
      order by equipment.speed_increase DESC limit 1;';
  
    $result = db_query($sql, $game_user->id);
    $eq = db_fetch_object($result);
firep($eq);

    if ($eq->speed_increase > 0)
      $actions_to_move -= $eq->speed_increase;
      
    $actions_to_move = max($actions_to_move, 6);
firep($actions_to_move);

// april fools 2013
//    $actions_to_move = 1;
      
    if (!$cur_hood->is_habitable) {
      
      $fetch_header($game_user);
      
      echo '<div class="land-failed">' .
          t('No Transports Available') . 
        '</div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/move/' . $arg2 . '/0">' .
              t('Choose a different @neighborhood',
                array('@neighborhood' => $hood_lower)) .
            '</a>
          </div>
        </div>';
      
      if (substr($phone_id, 0, 3) == 'ai-')
        echo "<!--\n<ai \"move-failed not-habitable\"/>\n-->";

      db_set_active('default');
      return;
      
    }

    if ($game_user->actions < $actions_to_move) {
      
      $fetch_header($game_user);
      
      echo '<div class="land-failed">' .
          t('Out of Action!') . 
        '</div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/elders_do_fill/' . $arg2 .
              '/action?destination=' . $game . '/move/' . $arg2 . '/' .
              $neighborhood_id . '">' .
              t('Refill your Action (2&nbsp;Luck)') .
            '</a>
          </div>
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/move/' . $arg2 . '/0">' .
              t('Choose a different @neighborhood',
                array('@neighborhood' => $hood_lower)) .
            '</a>
          </div>
        </div>';
      
      if (substr($phone_id, 0, 3) == 'ai-')
        echo "<!--\n<ai \"move-failed no-action\"/>\n-->";

      db_set_active('default');
      return;
      
    }

    $resigned_text = '';
    
// update neighborhood and actions
    $sql = 'update users set fkey_neighborhoods_id = %d,
      actions = actions - %d where id = %d;';
    $result = db_query($sql, $neighborhood_id, $actions_to_move,
      $game_user->id);
      
// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }

    $unfrozen_msg = '';

// frozen?  10% chance to mark as unfrozen
    if (($game_user->meta == 'frozen') && (mt_rand(1, 10) == 1)) {
      $sql = 'update users set meta = "" where id = %d;';
      $result = db_query($sql, $game_user->id);
      $game_user->meta = '';
      $unfrozen_msg =
        '<div class="subtitle">Your movement has unfrozen you!</div>';
    }
    
    $game_user = $fetch_user();
    $fetch_header($game_user);
    
    echo '<div class="land-succeeded">' . t('Success!') . 
      '</div>';
    
    echo <<< EOF
<div class="subtitle">You have arrived in your new $hood_lower</div>
<div class="subsubtitle">$resigned_text</div>
EOF;

    if (!empty($new_hood->welcome_msg)) {
    	
    	echo <<< EOF
<p class="second">You see a billboard when you enter the $hood_lower.&nbsp; It states:</p>
<p class="second">$new_hood->welcome_msg</p>    	
EOF;

    }

    echo $unfrozen_msg;

    // chance of loss
    if ($eq->chance_of_loss >= mt_rand(1,110)) { // give them a little extra chance
      
firep($eq->name . ' wore out!');
      $sql = 'update equipment_ownership set quantity = quantity - 1
        where fkey_equipment_id = %d and fkey_users_id = %d;';
      $result = db_query($sql, $eq->id, $game_user->id);
      
// player expenses need resetting?

      if ($eq->upkeep > 0) { // subtract upkeep from your expenses
        $sql = 'update users set expenses = expenses - %d where id = %d;';
        $result = db_query($sql, $eq->upkeep, $game_user->id);
      } // FIXME: do this before _stlouis_header so that upkeep is accurate
      
      echo '<div class="subtitle">' . t('Your @stuff has worn out',
        array('@stuff' => strtolower($eq->name))) . '</div>';
    } else {
firep($eq->name . ' did NOT wear out');
    }
    
    echo <<< EOF
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/quests/$arg2">
      Continue to ${quest}s
    </a>
  </div>
</div>
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/actions/$arg2">
      Continue to Actions
    </a>
  </div>
</div>
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/home/$arg2">
      Go to the home page
    </a>
  </div>
</div>
EOF;
  
  }

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"move-succeeded\"/>\n-->";

  db_set_active('default');
