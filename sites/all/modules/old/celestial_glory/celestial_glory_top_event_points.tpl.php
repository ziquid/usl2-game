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

  $sql = 'select count(id) as count from users
    where meta = "zombie";';
  $result = db_query($sql);
  $item = db_fetch_object($result);


echo <<< EOF
<div class="title">Top 100 Event Players</div>
<!--<div class="subtitle">$item->count Dead Presidents left</div>-->
EOF;
  
  $data = array();

    $sql = 'SELECT username, experience, initiative, endurance, 
      elocution, debates_won, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, phone_id,
      `values`.clan_title, `values`.clan_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
      elected_officials.approval_rating,
      clan_members.is_clan_leader,
      clans.name as clan_name, clans.acronym as clan_acronym,
      neighborhoods.name as neighborhood,
      event_points.points
    
      FROM `users`
    
      LEFT JOIN `values` ON users.fkey_values_id = `values`.id
    
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
    
      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id
    
      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id
    
      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    
      LEFT OUTER JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id
    
      LEFT OUTER JOIN event_points on event_points.fkey_users_id = users.id

      ORDER by event_points.points DESC
      LIMIT 100;';

    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;
  
  foreach ($data as $item) {
firep($item);

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
    if ($debate == 'Box') $official_link = $item->weight;
    $clan_class = 'election-details';
    
    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';
      
    $official_link .= '<br/><a href="/' . $game . '/user/' .
       $arg2 . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';

    $icon = $game . '_clan_' . $item->clan_icon . '.png';
    $clan_title = $item->clan_title;
    $exp = $item->experience;
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";
      
    if ($item->is_clan_leader)
      $clan_acronym .= '*';
      
    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$item->points<br/>Points</div>
</div>
EOF;

  } // foreach position
  
  db_set_active('default');
