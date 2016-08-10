<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $phone_id);

  $sql = 'SELECT party_title from `values`
    where id = %d;';

  $result = db_query($sql, $game_user->fkey_values_id);
  $values = db_fetch_object($result);
    
  echo <<< EOF
<div class="title">
  Available Clans
</div>
EOF;

  _show_goal($game_user);

  echo <<< EOF
<div class="subtitle">
  To join a clan, go to the <strong>Actions</strong> screen and choose
    <strong>Join a clan</strong>.&nbsp;
    You will need to know the clan's three letter acronym.
</div>
<div class="clan-list">
EOF;
	
  $data = array();
  $sql = 'SELECT count( clan_members.id ) AS members, clans.name, clans.acronym,
    clans.rules, clans.wins, clans.losses, clans.money
    FROM clan_members
    LEFT JOIN clans ON clan_members.fkey_clans_id = clans.id
    LEFT JOIN users ON clan_members.fkey_users_id = users.id
--    WHERE users.fkey_values_id = %d
    GROUP BY clans.id
    HAVING members > 1
--    ORDER BY members DESC
    ORDER BY RAND()
    LIMIT 10;';
  
  $result = db_query($sql, $game_user->fkey_values_id);
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
firep($item);

    if (empty($item->rules))
      $item->rules = 'No rules';

    echo <<< EOF
<h4>{$item->name} ({$item->acronym}): $item->members member(s)</h4>
Wins / Losses: $item->wins / $item->losses<br/>
$game_user->values: $item->money<br/>
Rules: $item->rules
EOF;

  } // foreach clan
  
  echo '</div>';
  
  db_set_active('default');
