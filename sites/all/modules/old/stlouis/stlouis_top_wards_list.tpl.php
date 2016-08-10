<?php

  global $game, $phone_id;
  
  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  echo <<< EOF
<div class="news">
  <a href="/$game/debates/$arg2" class="button">{$debate}s</a>
  <a href="/$game/hierarchies/$arg2" class="button">Elections</a>
  <a href="/$game/top20/$arg2" class="button">Top 20</a>
  <a href="/$game/top_wards/$arg2" class="button active">Top Clans</a>
</div>
<div class="title">Top Clans</div>
EOF;
  
  $data = array();
  $sql = 'SELECT fkey_clans_id, clans.name, clans.acronym,
    COUNT( users.id ) AS size, SUM( experience ) AS experience,
    `values`.party_title, `values`.party_icon
    FROM `clan_members` 
    LEFT JOIN users ON fkey_users_id = users.id
    LEFT JOIN clans ON fkey_clans_id = clans.id
    LEFT JOIN `values` ON users.fkey_values_id = `values`.id
    GROUP BY fkey_clans_id
    ORDER BY experience DESC 
    limit 20;';
  
  $result = db_query($sql);
  while ($item = db_fetch_object($result)) $data[] = $item;

    echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">Clan</div>
  <div class="opponent-name">Size</div>
  <div class="opponent-influence">$experience</div>
</div>
</div>
<div class="elections">
EOF;
  
  $rank = 0;
  foreach ($data as $item) {
firep($item);

    $rank++;
    $icon = $game . '_clan_' . $item->party_icon . '.png';

    echo <<< EOF
<div class="election-details">
  <div class="clan-icon"><img src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title" style="width: 38%;">#$rank: $item->name ($item->acronym)
    <span style="font-size: 9px;">($item->party_title)</span></div>
  <div class="opponent-name" style="margin-left: 20px; margin-right: -30px;">$item->size</div>
  <div class="opponent-influence">$item->experience</div>
</div>
EOF;
  
  } // foreach position
  
  db_set_active('default');
