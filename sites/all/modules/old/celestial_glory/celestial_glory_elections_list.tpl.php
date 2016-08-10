<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $get_value = '_' . $game . '_get_value';

// do AI moves from this page!!!
  include(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  echo <<< EOF
<div class="news">
  <a href="/$game/debates/$arg2" class="button">{$debate_tab}</a>
  <a href="/$game/elections/$arg2" class="button active">{$election_tab}</a>
  <a href="/$game/top20/$arg2" class="button">$top20</a>
  <a href="/$game/top_aldermen/$arg2" class="button">Top $alders_short</a>
</div>
EOF;

  $sql = 'select name, has_elections, rating, residents, district
    from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;
  $rating = $data->rating;
  $residents = $data->residents;
  $district = $data->district;

  if (($rating * 100) == (ceil($rating) * 100)) $rating = ceil($rating);

// no elections?
  if ($data->has_elections == 0) {

    echo <<< EOF
<div class="title">No Elections here!</div>
<div class="subtitle">You're on vacation!&nbsp;
  Why worry about elections here?</div>
<div class="subtitle">
  <a href="/$game/home/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    db_set_active('default');

    return;

  }


// can't challenge?
  $cant_challenge_time = $get_value($game_user->id, 'cant_challenge');
  $cant_challenge_time_remaining = !empty($cant_challenge_time) ?
    (int)$cant_challenge_time - time() : NULL;

//   if ($phone_id == 'abc123')
//     $cant_challenge_time_remaining = mt_rand(0,86400);

  if ($cant_challenge_time_remaining > 0) {

    $hours_remaining = sprintf('%02d',
      floor($cant_challenge_time_remaining / 3600));
    $minutes_remaining_in_sec = $cant_challenge_time_remaining % 3600;
    $minutes_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec / 60));
    $seconds_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec % 60));

    echo <<< EOF
<div class="title">You Can't Challenge!</div>
<div class="subtitle">You are too new to your $party_lower</div>
<div class="subtitle">Come back in
  $hours_remaining:$minutes_remaining:$seconds_remaining</div>
<div class="subtitle">
  <a href="/$game/home/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    db_set_active('default');

    return;

  }


  $sql = 'select clan_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $clan_title = preg_replace('/^The /', '', $data->clan_title);

  if ($game_user->level < 15) {

    echo <<< EOF
<ul>
  <li>Win elections to give you more $game_user->values and Influence</li>
  <li>Positions with <div class="can-broadcast-to-party">*</div>
    can send broadcast messages to everyone in the neighborhood</li>
  <li>Wait and rest for a few minutes if you run out of actions</li>
</ul>
EOF;

  }

  $sql = 'select min(min_level) as next_level from elected_positions
    where min_level > %d;';
  $result = db_query($sql, $game_user->level);
  $item = db_fetch_object($result);
  $see_more_offices_at = $item->next_level;
firep($see_more_offices_at);

  echo <<< EOF
<div class="title">$location Elected Officials</div>
EOF;

  $sql = 'SELECT users.username, users.phone_id FROM elected_officials
    left join users on elected_officials.fkey_users_id = users.id
    WHERE fkey_elected_positions_id = 1
    and users.fkey_neighborhoods_id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $item = db_fetch_object($result);

  echo <<< EOF
<div class="subtitle">Your neighborhood $alderman is
  <a href="/$game/user/$arg2/$item->phone_id">$item->username</a>.</div>
<div class="subtitle">Your $neighborhood_lower $beauty_lower rating is
  $rating%<br/>($residents extra resident voters).</div>
EOF;

  if ($see_more_offices_at) {

    echo <<< EOF
<div class="subtitle">See more offices at level $see_more_offices_at</div>
EOF;

  }

  $data = array();
  $sql = 'SELECT elected_positions.id AS ep_id,
    elected_positions.group as ep_group,
    elected_positions.name AS ep_name, elected_positions.energy_bonus,
    elected_positions.can_broadcast_to_party,
    elected_positions.min_level, elected_positions.max_level,
    blah.*, `values`.clan_icon,
    `values`.clan_title, clan_members.is_clan_leader,
    clans.acronym AS clan_acronym
    FROM elected_positions
    LEFT OUTER JOIN (

-- type 1: neighborhood positions

    SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_neighborhoods_id = %d
      AND elected_positions.type = 1

      UNION

-- type 2: party positions

      SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_values_id = %d
      AND elected_positions.type = 2

      UNION

-- type 3: house positions

      SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_neighborhoods_id IN
        (SELECT id from neighborhoods where district = %d)
      AND elected_positions.type = 3
    ) AS blah ON blah.fkey_elected_positions_id = elected_positions.id

    LEFT JOIN `values` ON blah.fkey_values_id = `values`.id
    LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = blah.id
    LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id

    WHERE elected_positions.min_level <= %d
    ORDER BY elected_positions.energy_bonus DESC,
      elected_positions.type DESC, elected_positions.group ASC,
      elected_positions.id ASC;';

  $result = db_query($sql, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $district, $game_user->level);
  while ($item = db_fetch_object($result)) $data[] = $item;

    echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Energy Bonus</div>
</div>
</div>
<div class="elections">
EOF;

  $last_group = $data[0]->ep_group;

  foreach ($data as $item) {
firep($item);

    $username = $item->username;
    $action_class = '';
    $official_link = $item->ep_name;
    $clan_class = 'election-details';

    if ($item->can_broadcast_to_party)
      $official_link .= '<div class="can-broadcast-to-party">*</div>';

    if (empty($item->id)) { // no existing officer

      $official_link .= ' <em>Available</em></a>';
      $action = '<a href="/' . $game . '/elections_challenge/' . $arg2 .
        '/' . $item->ep_id . '">' . t('Run for office') . '</a>';
      $action_class = '';

    } else { // existing officer

      $official_link .= '<br/><a href="/' . $game . '/user/' .
        $arg2 . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';
      $action = '<a href="/' . $game . '/elections_challenge/' . $arg2 .
        '/' . $item->ep_id . '">' . t('Challenge (%energy Action)',
        array('%energy' => $item->energy_bonus)) . '</a>';
      $action_class = '';

    }

    if ($game_user->level > $item->max_level) { // too high to challenge

      $action = t('Too powerful to challenge');
      $action_class = 'not-yet';

    }

    if ($game_user->actions < $item->energy_bonus) { // not enough action left

//      $action = t('Not enough Action left');
//      $action_class = 'not-yet';

    }

    if ($item->id == $game_user->id) { // can't challenge yourself

      $clan_class .= ' me';
      $action = t('This is you');
      $action_class = 'not-yet';

    }

    if (empty($item->clan_icon)) {
      $icon = $game . '_clan_none.png';
    } else {
      $icon = $game . '_clan_' . $item->clan_icon . '.png';
    }

    if (empty($item->clan_title)) {
      $clan_title = t('Position Open');
    } else {
      $clan_title = $item->clan_title;
    }

    if (empty($item->experience)) {
      $experience = 0;
    } else {
      $experience = $item->experience;
    }

    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    if ($last_group != $item->ep_group)
      echo '</div><div class="elections">';

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">+$item->energy_bonus every $energy_wait_str</div>
  <div class="approval-rating">Approval rating:
    <strong>$item->approval_rating%</strong></div>
  <div class="action-wrapper"><div class="action $action_class">$action</div></div>
</div>
EOF;

  $last_group = $item->ep_group;

  } // foreach position

  db_set_active('default');
