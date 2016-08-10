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

  echo <<< EOF
<div class="news">
  <a href="/$game/debates/$arg2" class="button">{$debate_tab}</a>
  <a href="/$game/elections/$arg2" class="button">{$election_tab}</a>
  <a href="/$game/top20/$arg2" class="button">$top20</a>
  <a href="/$game/top_aldermen/$arg2" class="button active">Top $alders_short</a>
</div>
<div class="title">Top 20 $aldermen</div>
EOF;

  $data = array();
  $sql = 'SELECT username, experience, initiative, endurance,
    elocution, debates_won, debates_lost, skill_points, luck,
    debates_last_time, users.fkey_values_id, level, phone_id,
    `values`.clan_title, `values`.clan_icon,
    `values`.name, users.id, users.fkey_neighborhoods_id,
    elected_positions.name as ep_name,
    elected_officials.timestamp,
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
    ON users.fkey_neighborhoods_id = neighborhoods.id

    WHERE elected_officials.fkey_elected_positions_id = 1
    ORDER by elected_officials.timestamp ASC
    LIMIT 20;';

  $result = db_query($sql);
  while ($item = db_fetch_object($result)) $data[] = $item;

    echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">$alder_short Since</div>
</div>
</div>
<div class="elections">
EOF;

  foreach ($data as $item) {
firep($item);

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
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

    $time_len_function = '_' . $game . '_format_date';
    $time_len = $time_len_function(strtotime($item->timestamp));

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$time_len<br/>
    ($item->neighborhood)</div>
</div>
EOF;

  } // foreach position

  db_set_active('default');
