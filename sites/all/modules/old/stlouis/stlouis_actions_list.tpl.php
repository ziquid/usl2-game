<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  include_once(drupal_get_path('module', $game) . '/' . $game . 
    '_actions.inc');

  $arg2 = check_plain(arg(2));

  if ($game_user->level < 10) {

    echo <<< EOF
<p>&nbsp;</p>
<div class="title">
  <img src="/sites/default/files/images/${game}_title.png?1" width=300/>
</div>
<div class="welcome">
  <div class="holodad"></div>
  <p class="quote">You're not yet experienced enough for this page.&nbsp;
  Come back at level 10.</p>
</div>
EOF;

  _button('quests');

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"home not-yet\"/>\n-->";

  db_set_active('default');
  return;

  }
    
  $fetch_header($game_user);
  
  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  $sql = 'select name, district from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;
  $district = $data->district;
  
  $sql = 'select party_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $party_title = preg_replace('/^The /', '', $data->party_title);
  
  $sql_to_add = '';
  $actions_active = 'AND actions.active = 1';

  if (($game_user->meta == 'frozen') && ($phone_id != 'abc123')) {
    
    echo <<< EOF
<div class="title">Frozen!</div>
<div class="subtitle">You have been tagged and cannot perform any actions</div>
<div class="subtitle">Call on a teammate to unfreeze you!</div>
EOF;

  db_set_active('default');
  return;

  }

  if (arg(3) == 'clan') {
    $actions_type = 'Clan';
  } else {
    $actions_type = 'Normal';
  }

  _show_actions_menu($game_user);

  if ($game_user->level < 20) {
    
    echo <<< EOF
<ul>
  <li>Use actions to affect your friends and opponents</li>
</ul>
EOF;

  }

  echo <<< EOF
<div class="title">
$actions_type Actions
</div>
EOF;

  $data = actionlist();
  $num_shown = 0;

  foreach ($data as $item) {
firep($item);

    if (_show_action($game_user, $item, $party_title, $game_user->clan_name))
      $num_shown++;

  } // foreach action

  if ($num_shown == 0) {

    echo <<< EOF
<div class="subtitle">
  You cannot yet perform any actions.&nbsp; Try gaining a higher hierarchy
    level.
</div>
EOF;

    _button();
    
  } // no actions available
  
  db_set_active('default');
