<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $ask_luck_refill = trim(check_plain($_GET['ask_luck_refill']));
  if ($ask_luck_refill <= 0) {
    $ask_luck_refill = 0;
  }
 
  $currentPreferences = _stlouis_get_value($game_user->id, 'ask_before_refilling_luck',0);
  if ($currentPreferences > 0) {
    $checkedYes = 'checked="checked"';
  }
  else {
    $checkedNo = 'checked="checked"';
  }

  echo <<< EOF
<div class="title">Game Preferences</div>
<div class="subtitle">Ask for confirmation when refilling with $luck?</div>  
<div class="menu-option">
  <div class="ask-name">
    <form method=get action="/$game/elders_preferences/$arg2">      
      <input type="radio" name="ask_luck_refill" value="1" $checkedYes />&nbsp;
        Yes
      <input type="radio" name="ask_luck_refill" value="0" $checkedNo />&nbsp;No
      <input type="submit" value="Submit"/>
    </form>
  </div>  
</div>
EOF;
  
  if (($ask_luck_refill) >= 0) {
    _stlouis_set_value($game_user->id, 'ask_before_refilling_luck', $ask_luck_refill);
  }

  db_set_active('default');
