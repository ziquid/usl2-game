<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $phone_id);

  $sql = 'SELECT clan_title from `values`
    where id = %d;';
  
  $result = db_query($sql, $game_user->fkey_values_id);
  $values = db_fetch_object($result);
    
  echo <<< EOF
<div class="title">Available $values->clan_title Clans</div>
<div class="subtitle">To join a clan, go to the <em>Actions</em> screen and choose
  <em>Join a clan</em>.&nbsp; You will need to know the clan's three letter
  acronym.</div>
<div class="clan-list">
EOF;
	
  $data = array();
  $sql = 'SELECT count( clan_members.id ) AS members, clans.name, clans.acronym,
    clans.rules
    FROM clan_members
    LEFT JOIN clans ON clan_members.fkey_clans_id = clans.id
    LEFT JOIN users ON clan_members.fkey_users_id = users.id
    WHERE users.fkey_values_id = %d
    GROUP BY clans.id
    ORDER BY members DESC';
  
  $result = db_query($sql, $game_user->fkey_values_id);
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
firep($item);

    if (empty($item->rules))
      $item->rules = 'No rules';
      
		echo <<< EOF
<h4>{$item->name} ({$item->acronym}): $item->members members</h4>
Rules: $item->rules
EOF;

  } // foreach clan
  
  echo '</div>';
  
  db_set_active('default');
