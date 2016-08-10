<?php

  global $game, $phone_id;
  
  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  echo <<< EOF
<div class="title">
Do you really want to reset your skill points?
</div>
<div class="subtitle">
All the $initiative, Endurance, $elocution, and Action your character has
  collected will be converted back into skill points
</div>
<div class="elders-menu big">
<div class="menu-option"><a href="/$game/elders_do_reset_skills/$arg2">Yes,
  I want to reset my skill points</a></div>
</div>
EOF;
    
  db_set_active('default');  
