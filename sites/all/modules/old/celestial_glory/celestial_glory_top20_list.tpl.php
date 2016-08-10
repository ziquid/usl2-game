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

  if ($debate == 'Box') {
    $title = 'Top Boxers';
  } elseif ($event_type == EVENT_GATHER_AMETHYST
  || $event_type == EVENT_AMETHYST_DONE) {
    $title = 'Top 20 Gatherers';
  } else {
    $title = 'Top 20 Players';
  }

  echo <<< EOF
<div class="news">
  <a href="/$game/debates/$arg2" class="button">{$debate_tab}</a>
  <a href="/$game/elections/$arg2" class="button">{$election_tab}</a>
  <a href="/$game/top20/$arg2" class="button active">$top20</a>
  <a href="/$game/top_aldermen/$arg2" class="button">Top $alders_short</a>
</div>
<div class="title">$title</div>
EOF;

  $data = array();

  if ($event_type == EVENT_GATHER_AMETHYST
  || $event_type == EVENT_AMETHYST_DONE) {

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
      users.meta_int,
      "Raw Amethyst" as weight

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

      where users.meta_int > 0

      ORDER by users.meta_int DESC, users.experience ASC
      LIMIT 100;';

    $result = db_query($sql);
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
    }

  } elseif ($debate == 'Box') {

    $already_listed = array();

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
      users.meta_int,
      "Heavyweight" as weight

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

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql);
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Cruiserweight" as weight

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

      WHERE users.level <= 125
      AND users.id not in %s
      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Middleweight" as weight

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

      WHERE users.level <= 110
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Welterweight" as weight

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

      WHERE users.level <= 95
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Lightweight" as weight

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

      WHERE users.level <= 80
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Featherweight" as weight

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

      WHERE users.level <= 65
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Bantamweight" as weight

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

      WHERE users.level <= 50
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Flyweight" as weight

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

      WHERE users.level <= 35
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

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
      users.meta_int,
      "Minimumweight" as weight

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

      WHERE users.level <= 20
      AND users.id not in %s

      ORDER by users.meta_int DESC
      LIMIT 3;';

    $result = db_query($sql, '(' . implode(',', $already_listed) . ')');
    while ($item = db_fetch_object($result)) {
      $data[] = $item;
      $already_listed[] = $item->id;
    }

  } else { // normal

    $sql = 'SELECT username, experience, initiative, endurance,
      elocution, debates_won, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, phone_id,
      `values`.clan_title, `values`.clan_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
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

      LEFT OUTER JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id

      ORDER by users.experience DESC
      LIMIT 20;';

    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

  }

    $count = 0;

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
// firep($item);

    $count++;
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

    if ($debate == 'Box') {
      $exp = $item->meta_int;
      $experience = 'Boxing Points';
    }

    if ($event_type == EVENT_GATHER_AMETHYST
    || $event_type == EVENT_AMETHYST_DONE) {
      $exp = $item->meta_int;
      $experience = 'Raw Amethyst';
    }

//    if ($game == 'celestial_glory') {

//      $ward = "$item->neighborhood / ";

//    } else {

      $ward = '';

//    }

    if (($item->weight != $last_weight) && $last_weight != '')
      echo '</div><div class="elections">';

    if (($event_type == EVENT_GATHER_AMETHYST
    || $event_type == EVENT_AMETHYST_DONE)
    && $count == 20)
      echo '</div><div class="title">The Rest</div><div class="elections">';

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$ward $clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$exp $experience<br/>
    Level $item->level</div>
</div>
EOF;

    $last_weight = $item->weight;

  } // foreach position

  db_set_active('default');
