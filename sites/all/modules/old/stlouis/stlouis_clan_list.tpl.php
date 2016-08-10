<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  show_clan_menu($game_user);
  
  $data = array();
  $sql = 'SELECT username, experience, initiative, endurance, 
    elocution, debates_won, debates_lost, skill_points, luck,
    debates_last_time, users.fkey_values_id, level, phone_id,
    `values`.party_title, `values`.party_icon,
    `values`.name, users.id, users.fkey_neighborhoods_id,
    elected_positions.name as ep_name,
    elected_officials.approval_rating,
    clan_members.is_clan_leader,
    clans.name as clan_name, clans.acronym as clan_acronym,
    clans.rules as clan_rules,
    clans.money as clan_money,
    clans.wins as clan_wins,
    clans.losses as clan_losses,
    clans.attack as clan_attack,
    clans.defense as clan_defense,
    clans.prestige,
    neighborhoods.name as location

    FROM `users`

    LEFT JOIN `values` ON users.fkey_values_id = `values`.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    LEFT JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id

    WHERE clan_members.fkey_clans_id = %d
    ORDER by users.experience DESC;';
  
  $result = db_query($sql, $clan_id);
  while ($item = db_fetch_object($result)) $data[] = $item;

  $num_members = count($data);
  
    echo <<< EOF
<div class="title">{$data[0]->clan_name} Clan Members</div>
<div class="subsubtitle">
  ({$data[0]->clan_acronym}) == $num_members members ==
  {$data[0]->prestige} $prestige
</div>
<div class="subsubtitle">
  $game_user->values: {$data[0]->clan_money};
  Wins / Losses: {$data[0]->clan_wins} / {$data[0]->clan_losses};
  Attack / Defense: {$data[0]->clan_attack} / {$data[0]->clan_defense}
</div>
<div class="subsubtitle">
  &laquo;&laquo; {$data[0]->clan_rules} &raquo;&raquo;
</div>
<div class="elections-header">
  <div class="election-details">
    <div class="clan-title">$party_small+$hood_small</div>
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
    $clan_class = 'election-details';
    
    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';
      
    $official_link .= '<br/><a href="/' . $game . '/user/' .
      $arg2 . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';

    $clan_acronym = '';
    if ($item->is_clan_leader)
      $clan_acronym = '*';

    $icon = $game . '_clan_' . $item->party_icon . '.png';

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon">
    <img width="24" src="/sites/default/files/images/$icon"/>
  </div>
  <div class="clan-title">
    $item->party_title
    <br/>
    ($item->location)
  </div>
  <div class="opponent-name">$official_link $clan_acronym<br/>
  </div>
  <div class="opponent-influence">
    $item->experience $experience<br/>
    (Level $item->level)
  </div>
</div>
EOF;
  
  } // foreach position

  echo '</div>';

// clan stuff
   
  $data = array();

  $sql = 'SELECT equipment.*, clan_equipment_ownership.quantity
    FROM equipment

    LEFT OUTER JOIN clan_equipment_ownership
      ON clan_equipment_ownership.fkey_equipment_id = equipment.id
    AND clan_equipment_ownership.fkey_clans_id = %d

    WHERE clan_equipment_ownership.quantity > 0
    ORDER BY required_level ASC';
firep($sql);
  $result = db_query($sql, $game_user->fkey_clans_id);

  while ($item = db_fetch_object($result)) $data[] = $item;

  if (count($data) > 0) echo <<< EOF
<div class="title">
  Clan $equipment
</div>
EOF;

  foreach ($data as $item) {   
firep($item);

    show_equipment($game_user, $item, array('clan' => TRUE));

  }

  db_set_active('default');
