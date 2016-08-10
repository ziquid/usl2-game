<?php

  global $game, $phone_id;
  
  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  $title = 'Top 20 ';

  show_elections_menu($game_user);

  $data3 = $data2 = $data = array();

    $sql = 'SELECT username, experience, initiative, endurance, 
      elocution, favors_asked_completed, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, phone_id,
      `values`.party_title, `values`.party_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
      elected_positions.id as ep_level,
      elected_officials.approval_rating,
      clan_members.is_clan_leader,
      clans.name as clan_name, clans.acronym as clan_acronym,
      neighborhoods.name as neighborhood
    
      FROM `users`
    
      LEFT JOIN `values` ON users.fkey_values_id = `values`.id
    
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
    
      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id
    
      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id
    
      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    
      LEFT OUTER JOIN neighborhoods
      ON users.fkey_neighborhoods_id = neighborhoods.id';

    $sql3 = $sql2 = $sql . '
      WHERE users.fkey_neighborhoods_id = %d
      ';

    $sql .= '    
      ORDER by users.favors_asked_completed DESC
      LIMIT 20;';

    $sql2 .= '    
      ORDER by users.favors_asked_completed DESC
      LIMIT 6;';

    $sql3 .= '
      ORDER by users.favors_asked_completed DESC
      LIMIT %d, 11;';

    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    $result = db_query($sql2, $game_user->fkey_neighborhoods_id);
    while ($item = db_fetch_object($result)) $data2[] = $item;

    $sql = 'select count(*) as count from users
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      WHERE
      (favors_asked_completed > %d);';
    $result = db_query($sql, $game_user->favors_asked_completed);
    $game_rank = db_fetch_object($result);
    $game_rank = $game_rank->count + 1;

   $result = db_query($sql3, $game_user->fkey_neighborhoods_id,
      max($game_rank - 6, 0));
    while ($item = db_fetch_object($result)) $data3[] = $item;

    $sql = 'select count(*) as count from users
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      where fkey_neighborhoods_id = %d AND
      (favors_asked_completed > %d);';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id,
      $game_user->favors_asked_completed);
    $hood_rank = db_fetch_object($result);
    $hood_rank = $hood_rank->count + 1;

  echo <<< EOF
<div class="title">Top 6 in $game_user->location</div>
<div class="subsubtitle">(Your rank: $hood_rank)</div>
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;

  foreach ($data2 as $item) {
firep($item);

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
    if ($debate == 'Box') $official_link = $item->weight;
    $clan_class = 'election-details';

    if ($item->id == $game_user->id) $clan_class .= ' me';
    
    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';
      
    $official_link .= '<br/><a href="/' . $game . '/user/' .
       $arg2 . '/id:' . $item->id . '"><em>' . $username . '</em></a>';

    $icon = $game . '_clan_' . $item->party_icon . '.png';
    $party_title = $item->party_title;
    $exp = $item->favors_asked_completed;
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";
      
    if ($item->is_clan_leader)
      $clan_acronym .= '*';
      
    $sql = 'select count(*) as count from users
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      where fkey_neighborhoods_id = %d AND
      (favors_asked_completed > %d);';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id,
      $item->favors_asked_completed);
    $user_rank = db_fetch_object($result);
    $user_rank = $user_rank->count + 1;

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$party_title</div>
  <div class="rank">$user_rank</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$exp assigned<br/>
    ${favor_lower}s done<br/>
    (Level $item->level)</div>
</div>
EOF;

  } // foreach position

  echo <<< EOF
<div class="election-details">
  <div class="clan-icon">&nbsp;</div>
  <div class="clan-title">&nbsp;</div>
  <div class="opponent-name">. . .</div>
  <div class="opponent-influence">&nbsp;</div>
</div>
EOF;

  foreach ($data3 as $item) {
firep($item);

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
    if ($debate == 'Box') $official_link = $item->weight;
    $clan_class = 'election-details';

    if ($item->id == $game_user->id) $clan_class .= ' me';
    
    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';
      
    $official_link .= '<br/><a href="/' . $game . '/user/' .
       $arg2 . '/id:' . $item->id . '"><em>' . $username . '</em></a>';

    $icon = $game . '_clan_' . $item->party_icon . '.png';
    $party_title = $item->party_title;
    $exp = $item->favors_asked_completed;
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";
      
    if ($item->is_clan_leader)
      $clan_acronym .= '*';
      
    $sql = 'select count(*) as count from users
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      where fkey_neighborhoods_id = %d AND
      (favors_asked_completed > %d);';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id,
      $item->favors_asked_completed);
    $user_rank = db_fetch_object($result);
    $user_rank = $user_rank->count + 1;

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$party_title</div>
  <div class="rank">$user_rank</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$exp assigned<br/>
    ${favor_lower}s done<br/>
    (Level $item->level)</div>
</div>
EOF;

  } // foreach position

// GAME-WIDE TOP 20
  
  echo <<< EOF
</div>
<div class="title">$title Game-Wide</div>
<div class="subsubtitle">(Your rank: $game_rank)</div>
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;
  
  $rank = 1;

  foreach ($data as $item) {
firep($item);

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
    if ($debate == 'Box') $official_link = $item->weight;
    $clan_class = 'election-details';
    
    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';
      
    $official_link .= '<br/><a href="/' . $game . '/user/' .
       $arg2 . '/id:' . $item->id . '"><em>' . $username . '</em></a>';

    $icon = $game . '_clan_' . $item->party_icon . '.png';
    $party_title = $item->party_title;
    $exp = $item->favors_asked_completed;
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";
      
    if ($item->is_clan_leader)
      $clan_acronym .= '*';
      
    $sql = 'select count(*) as count from users
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      WHERE
      (favors_asked_completed > %d);';
    $result = db_query($sql, $item->favors_asked_completed);
    $user_rank = db_fetch_object($result);
    $user_rank = $user_rank->count + 1;

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$party_title</div>
  <div class="rank">$user_rank</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$exp assigned<br/>
    ${favor_lower}s done<br/>
    (Level $item->level)</div>
</div>
EOF;
  
  } // foreach position

  echo '</div>';  

  db_set_active('default');
