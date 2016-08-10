<?php

function actionlist() {

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// firep('$game is ' . $game);

  $sql_to_add = '';
  $actions_active = 'AND actions.active = 1';

  if (arg(3) == 'clan') {
    $order_by = 'actions.id ASC';
  } else {
    $order_by = 'required_level DESC';
//    $order_by = 'actions.id ASC';
  }


// part of a clan?

  $sql = 'select * from {clan_members}
    left join clans on clans.id = clan_members.fkey_clans_id
    where fkey_users_id = %d;';
// limited to 1 in db
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result);
  firep($item);

  if (!empty($item)) { // part of a clan - exclude all those that need no clan

    $sql_to_add .= ' and actions.need_no_clan <> 1 ';
    $subclan_name = "$item->name ($item->acronym)";

  } else {

    $sql_to_add .= ' and actions.need_clan <> 1 ';

  }

  if (!empty($item->is_clan_leader)) {
// clan leader - exclude all those that need no clan leader

// we don't have any actions that need no clan leader, so line commented out below
//    $sql_to_add .= ' and actions.need_no_clan_leader <> 1 ';

  } else {

    $sql_to_add .= ' and actions.need_clan_leader <> 1 ';

  }

// elected official?

  $sql = 'select *, elected_positions.type, elected_positions.group
    from elected_officials

    left join elected_positions
    on elected_officials.fkey_elected_positions_id = elected_positions.id

    where fkey_users_id = %d;';
// limited to 1 in db
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result);
  firep($item);

  if (!empty($item)) {
// an elected official - exclude all those that need no elected official

    $sql_to_add .= ' and actions.need_no_elected_official <> 1
      and (actions.need_specific_elected_official = 0 or
      actions.need_specific_elected_official = ' .
      $item->fkey_elected_positions_id . ')
      and actions.need_specific_elected_official_or_higher <= ' .
      $item->fkey_elected_positions_id . '
      and (actions.need_specific_official_group = 0 or
      actions.need_specific_official_group = ' .
      $item->group . ')';

  } else {

    $sql_to_add .= ' and actions.need_elected_official <> 1
      and actions.need_specific_elected_official = 0
      and actions.need_specific_elected_official_or_higher = 0
      and actions.need_specific_official_group = 0';

  }

// for testing - exclude all exclusions (!) if I am abc123

  if ($phone_id == 'abc123') {

    $sql_to_add = '';
    $actions_active = 'and (actions.active = 1 or actions.active = 0)';

  }

// actions that have staff associated with them
  $data = array();
  $sql = 'SELECT staff_ownership.*, actions.*, staff.required_level
  from staff_ownership

  left join actions on staff_ownership.fkey_staff_id = actions.fkey_staff_id
  left join staff on staff_ownership.fkey_staff_id = staff.id

  where
  (staff_ownership.fkey_users_id = %d OR staff_ownership.fkey_users_id = 0)

  ' . $actions_active .
  ' AND quantity > 0
    AND (actions.required_level <= %d OR actions.required_level = 0)
  ' . $sql_to_add . '
  ORDER BY ' . $order_by;
  firep($sql, 'staff actions');
  $result = db_query($sql, $game_user->id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

// actions that have equipment associated with them
  $sql = 'SELECT eo.*, actions.*, equipment.required_level,
  eq2.name as eq2_name, e2o.quantity as e2_quantity
  from equipment_ownership as eo

  inner join actions on
  eo.fkey_equipment_id = actions.fkey_equipment_id

  inner join equipment on
  eo.fkey_equipment_id = equipment.id

  left join equipment_ownership as e2o on
  e2o.fkey_equipment_id = actions.fkey_equipment_2_id

  left join equipment as eq2 on
  actions.fkey_equipment_2_id = eq2.id

  where
  (eo.fkey_users_id = %d)
  AND (e2o.fkey_users_id = %d or e2o.fkey_users_id IS NULL)

  ' . $actions_active .
  ' AND eo.quantity > 0
    AND (actions.required_level <= %d OR actions.required_level = 0)
  ' . $sql_to_add . '
  ORDER BY equipment.required_level DESC';
firep($sql, 'equipment actions');
  $result = db_query($sql, $game_user->id, $game_user->id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

// actions that are free
  $sql = 'select * from {actions}
  where actions.fkey_staff_id = 0 and actions.fkey_equipment_id = 0
  ' . $actions_active . '
    AND (actions.required_level <= %d OR actions.required_level = 0)
  ' . $sql_to_add . '
  ORDER BY name ASC';
firep($sql);
  $result = db_query($sql, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

  return $data;

}


function _target_list($target, $game_user) {
// return a list of valid targets

// expensive query - goes to slave
//   db_set_active('game_' . $game . '_slave1');

  switch ($target) {

    case 'clan':
// users in your clan

      $data2 = array();
      $sql = 'SELECT users.username, users.id,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM clan_members
        LEFT JOIN users ON clan_members.fkey_users_id = users.id
        LEFT JOIN clans ON clan_members.fkey_clans_id = clans.id
        WHERE fkey_clans_id = %d
        AND users.id <> %d
        ORDER BY username ASC;';

      $result = db_query($sql, $game_user->fkey_clans_id, $game_user->id);
      while ($user = db_fetch_object($result)) $data2[] = $user;

      break;


    case 'neighborhood':
// users in your neighborhood

      $data2 = array();
      $sql = 'SELECT users.username, users.id,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        WHERE fkey_neighborhoods_id = %d
        AND users.id <> %d
        AND users.username <> ""
        ORDER BY username ASC;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->id);
      while ($user = db_fetch_object($result)) $data2[] = $user;

      break;


    case 'neighborhood_higher_than_you_but_still_debateable':
// people in your neighborhood who aren't on your wall nor in your clan
// who are a higher level than you but are still debateable

      $data2 = array();
      $sql = 'SELECT users.id, users.username,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members
          ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        LEFT OUTER JOIN elected_officials
          ON elected_officials.fkey_users_id = users.id
        LEFT OUTER JOIN `values` ON users.fkey_values_id = `values`.id
        WHERE users.fkey_neighborhoods_id = %d
          AND users.level >= %d
--          AND debates_last_time < "%s"
          AND users.id <> %d
          AND users.id not in
-- all clan members that are in his/her clan
            (select fkey_users_id as id from clan_members
              where fkey_clans_id = %d
            UNION
-- all users that have posted to his/her wall
            select fkey_users_from_id as id from user_messages
              where fkey_users_to_id = %d
            UNION
-- all users to which user has posted
            select fkey_users_to_id as id from user_messages
              where fkey_users_from_id = %d)
              AND username <> ""
        ORDER BY RAND()
        LIMIT 3;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->level, date('Y-m-d H:i:s', time() - 1200),
        $game_user->id, $game_user->fkey_clans_id, $game_user->id,
        $game_user->id);
      while ($user = db_fetch_object($result)) $data2[] = $user;

      break;


    case 'neighborhood_not_met':
// people in your neighborhood who aren't on your wall nor in your clan

      $data2 = array();
      $sql = 'SELECT users.id, users.username,
         clan_members.is_clan_leader, clans.acronym AS clan_acronym,
         NULL as ep_name
        FROM users

        LEFT OUTER JOIN clan_members
          ON clan_members.fkey_users_id = users.id

        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id

        LEFT OUTER JOIN elected_officials
          ON elected_officials.fkey_users_id = users.id

        LEFT OUTER JOIN `values` ON users.fkey_values_id = `values`.id

        WHERE users.fkey_neighborhoods_id = %d
          AND users.id <> %d
          AND users.id not in

-- all clan members that are in his/her clan
            (select fkey_users_id as id from clan_members
              where fkey_clans_id = %d

            UNION
-- all users that have posted to his/her wall
            select fkey_users_from_id as id from user_messages
              where fkey_users_to_id = %d

            UNION
-- all users to which user has posted
            select fkey_users_to_id as id from user_messages
              where fkey_users_from_id = %d)
              AND username <> ""

        ORDER BY RAND()
        LIMIT 3;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->id, $game_user->fkey_clans_id, $game_user->id,
        $game_user->id);

      $allowed_string = '';
      while ($user = db_fetch_object($result)) {
        $data2[] = $user;
        $allowed_string .= $user->id . ',';
      }

      $allowed_string = substr($allowed_string, 0, strlen($allowed_string) - 1);
firep('allowed not_met users: ' . $allowed_string);
      break;


    case 'neighborhood_no_official_not_home':
// non-party users who aren't officials

      $data2 = array();
      $sql = 'SELECT users.id, users.username,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members
          ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        LEFT OUTER JOIN elected_officials
          ON elected_officials.fkey_users_id = users.id
        LEFT OUTER JOIN elected_positions
          ON elected_officials.fkey_elected_positions_id =
          elected_positions.id
        LEFT OUTER JOIN `values` ON users.fkey_values_id = `values`.id
        WHERE users.fkey_neighborhoods_id = %d
          AND users.fkey_values_id <> %d
          AND username <> ""
          AND (elected_officials.fkey_elected_positions_id IS NULL
            OR elected_positions.type = 2)
          AND `values`.fkey_neighborhoods_id <> %d
        ORDER BY username ASC ;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id, $game_user->fkey_neighborhoods_id);
      while ($user = db_fetch_object($result)) $data2[] = $user;

      break;

    case 'neighborhood_no_official_not_home_not_babylonian':
// non-party users who aren't officials and aren't babylonian 8-)))

      if ($game_user->fkey_neighborhoods_id == 1) { // Judea
        $not_babylonian = 'AND users.fkey_values_id <> 4';
      } else {
        $not_babylonian = '';
      }

      $data2 = array();
      $sql = 'SELECT users.id, users.username,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members
          ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        LEFT OUTER JOIN elected_officials
          ON elected_officials.fkey_users_id = users.id
        LEFT OUTER JOIN elected_positions
          ON elected_officials.fkey_elected_positions_id =
          elected_positions.id
        LEFT OUTER JOIN `values` ON users.fkey_values_id = `values`.id
        WHERE users.fkey_neighborhoods_id = %d
          AND users.fkey_values_id <> %d ' . $not_babylonian . '
          AND username <> ""
          AND (elected_officials.fkey_elected_positions_id IS NULL
            OR elected_positions.type = 2)
          AND `values`.fkey_neighborhoods_id <> %d
        ORDER BY username ASC ;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id, $game_user->fkey_neighborhoods_id);
      while ($user = db_fetch_object($result)) $data2[] = $user;

      break;

    case 'officials':

// elected officials only
      $data2 = array();
      $sql = 'SELECT elected_positions.id AS ep_id,
        elected_positions.group as ep_group,
        elected_positions.name AS ep_name, blah.*,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym
        FROM elected_positions
        RIGHT JOIN (

-- type 1: neighborhood positions

        SELECT elected_officials.fkey_elected_positions_id,
          elected_officials.approval_rating, users.*
        FROM elected_officials
        LEFT JOIN users ON elected_officials.fkey_users_id = users.id
        LEFT JOIN elected_positions
          ON elected_positions.id =
            elected_officials.fkey_elected_positions_id
        WHERE users.fkey_neighborhoods_id = %d
        AND elected_positions.type = 1

        UNION

-- type 2: party positions

        SELECT elected_officials.fkey_elected_positions_id,
          elected_officials.approval_rating, users.*
        FROM elected_officials
        LEFT JOIN users ON elected_officials.fkey_users_id = users.id
        LEFT JOIN elected_positions
          ON elected_positions.id =
            elected_officials.fkey_elected_positions_id
        WHERE users.fkey_values_id = %d
        AND elected_positions.type = 2

        UNION

-- type 3: district positions

        SELECT elected_officials.fkey_elected_positions_id,
          elected_officials.approval_rating, users.*
        FROM elected_officials
        LEFT JOIN users ON elected_officials.fkey_users_id = users.id
        LEFT JOIN elected_positions
          ON elected_positions.id =
            elected_officials.fkey_elected_positions_id
        WHERE users.fkey_neighborhoods_id IN
          (SELECT id from neighborhoods where district = %d)
        AND elected_positions.type = 3

        ) AS blah ON blah.fkey_elected_positions_id = elected_positions.id

        LEFT OUTER JOIN clan_members
          ON clan_members.fkey_users_id = blah.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        ORDER BY elected_positions.energy_bonus DESC, ep_id ASC;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id, $district);
      while ($official = db_fetch_object($result)) $data2[] = $official;

      break;

  } // switch

  db_set_active('game_' . $game); // reset to master

  return $data2;

}


function _show_action($game_user, $item, $party_title, $clan_name) {

  global $game, $phone_id;

  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $description = str_replace(array('%party', '%clan', '%value'),
    array("<em>$party_title</em>", "<em>$clan_name</em>",
      $game_user->values),
    $item->description);

  if ((arg(3) != 'clan') && (stripos($description, 'clan') !== FALSE))
    return FALSE; // move clan to its own screen

  if ((arg(3) == 'clan') && (stripos($description, 'clan') === FALSE))
    return FALSE; // move clan to its own screen

  if ($item->cost > 0) {
    $cost = "$item->cost $actions";
  } else {
    $cost = 'Nothing';
  }

  if ($item->values_cost > 0) {

    if (substr($cost, -7) == $actions) $cost .= ', ';
    $cost .= "$item->values_cost $game_user->values";

  }

  if ($item->clan_values_cost > 0)
    $cost .= ", $item->clan_values_cost Clan $game_user->values";

  if (!empty($item->fkey_equipment_id)) {
    $image = '/sites/default/files/images/equipment/' .
      "$game-$item->fkey_equipment_id.png";
  } else {
    $image = '/sites/default/files/images/staff/' .
      "$game-$item->fkey_staff_id.png";
  }

  if (is_file($_SERVER['DOCUMENT_ROOT'] .
    "/sites/default/files/images/actions/$game-{$item->id}.png"))
    $image = "/sites/default/files/images/actions/$game-$item->id.png";

  if ($item->target == 'none')
    $target = t('Your');
  else
    $target = t('Target\'s');

  $name = str_replace(array('%party', '%clan', '%value'),
    array("<em>$party_title</em>", "<em>$clan_name</em>", $game_user->values),
    $item->name);

  if ($item->active == 0) $name .= ' (inactive)';

  if ($item->fkey_equipment_2_id > 0) {
    $addl_req = '<div class="land-action-cost">' . $item->eq2_name . '</div>';
  }

  echo <<< EOF
<div class="land">
  <div class="land-icon"><img src="$image" border="0" width="96"></div>
  <div class="land-details">
    <div class="land-name">$name</div>
    <div class="land-description">$description</div>
    <div class="land-requisites">// Requisites //</div>
    <div class="land-action-cost">$cost</div>
    $addl_req
    <div class="land-disbursements">// Automatic Effects //</div>
EOF;

  $effect_listed = FALSE;

  if (!empty($item->extra_effects)) {

    if ($item->extra_effects[0] == '-') {

      $negative = 'negative';
      $item->extra_effects = substr($item->extra_effects, 1);

    } else {

      $negative = '';

    }

    echo <<< EOF
    <div class="land-payout $negative">
      $item->extra_effects
    </div>
EOF;

    $effect_listed = TRUE;

  } // extra effects

  if ($item->influence_change < 0) {

    $inf_change = -$item->influence_change;

    echo <<< EOF
    <div class="land-payout negative">
      $target $experience is reduced by $inf_change
    </div>
EOF;

    $effect_listed = TRUE;

  } // if influence_change < 0

  if ($item->influence_change > 0) {

    $inf_change = $item->influence_change;

    echo <<< EOF
    <div class="land-payout">
      $target $experience is increased by $inf_change
    </div>
EOF;

    $effect_listed = TRUE;

  } // if influence_change < 0

  if (($item->rating_change < 0.10) && ($item->rating_change != 0.0)) {

    $rat_change = abs($item->rating_change);

    if ($item->rating_change < 0.0) {

      echo <<< EOF
    <div class="land-payout negative">Effect: $target approval rating is
      reduced by $rat_change%</div>
EOF;

    } else {

      echo <<< EOF
    <div class="land-payout">Effect: $target approval rating is
      increased by $rat_change%</div>
EOF;

    }

    $effect_listed = TRUE;

  } // if rating_change < 0

  if ($item->rating_change >= 0.10) {

    $rat_change = $item->rating_change;

    echo <<< EOF
    <div class="land-payout">Effect: Your approval rating is
      increased by $rat_change%</div>
EOF;

    $effect_listed = TRUE;

  } // if rating_change > 0

  if ($item->values_change > 0) {

    echo <<< EOF
    <div class="land-payout">Effect: $target $game_user->values is
      increased by $item->values_change</div>
EOF;

    $effect_listed = TRUE;

  } // if values_change > 0

  if ($item->values_change < 0) {

    $val_change = -$item->values_change;

    echo <<< EOF
    <div class="land-payout negative">Effect: $target $game_user->values is
      decreased by $val_change</div>
EOF;

    $effect_listed = TRUE;

  } // if values_change < 0

  if ($item->actions_change > 0) {

    echo <<< EOF
    <div class="land-payout">Effect: $target Action is
      increased by $item->actions_change</div>
EOF;

    $effect_listed = TRUE;

  } // if actions_change > 0

  if ($item->actions_change < 0) {

    $val_change = -$item->actions_change;

    echo <<< EOF
    <div class="land-payout negative">Effect: $target Action is
      decreased by $val_change</div>
EOF;

    $effect_listed = TRUE;

  } // if actions_change < 0

  if ($item->neighborhood_rating_change < 0.0) {

    $rat_change = -$item->neighborhood_rating_change;

    echo <<< EOF
    <div class="land-payout negative">Effect: Neighborhood $beauty_lower
      rating is reduced by $rat_change</div>
EOF;

    $effect_listed = TRUE;

  } // if hood rating_change < 0

  if ($item->neighborhood_rating_change > 0.0) {

    $rat_change = $item->neighborhood_rating_change;

    echo <<< EOF
    <div class="land-payout">Effect: Neighborhood $beauty_lower rating is
      increased by $rat_change</div>
EOF;

    $effect_listed = TRUE;

  } // if hood rating_change > 0

  if (!$effect_listed) echo <<< EOF
    <div class="land-payout">&mdash; Nothing &mdash;</div>
EOF;

  echo <<< EOF
    <div class="land-disbursements">// Competencies Enhanced //</div>
EOF;

  if ($item->competency_enhanced_1 > 0) {

    $sql = 'select name from competencies
      where id = %d;';
    $result = db_query($sql, $item->competency_enhanced_1);
    $comp = db_fetch_object($result);

    echo <<< EOF
    <div class="land-payout">&lt; $comp->name &gt;</div>
EOF;

  } else {

    echo <<< EOF
    <div class="land-payout">None</div>
EOF;

  }

  $get_value = '_' . $game . '_get_value';
  $next_major_action_time = $get_value($game_user->id, 'next_major_action');
  $next_major_action_time_remaining = !empty($next_major_action_time) ?
    (int)$next_major_action_time - time() : NULL;

  echo <<< EOF
  <form action="/$game/actions_do/$arg2/$item->id">
EOF;

  if (($next_major_action_time_remaining > 0) && ($item->major_action > 0)) {

    $hours_remaining = sprintf('%02d',
      floor($next_major_action_time_remaining / 3600));
    $minutes_remaining_in_sec = $next_major_action_time_remaining % 3600;
    $minutes_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec / 60));
    $seconds_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec % 60));

    echo <<< EOF
    <div class="land-perform-button-wrapper">
      <input class="land-perform-button not-yet" type="button" value="Not Yet"/>
    </div>
EOF;
//     if ($item->target != 'none') {
       echo <<< EOF
       Available in $hours_remaining:$minutes_remaining:$seconds_remaining
EOF;
//     }

  } else {
    echo <<< EOF
    <div class="land-perform-button-wrapper">
      <input class="land-perform-button" type="submit" value="Do it"/>
    </div>
EOF;

    if ($item->target != 'none') {

      echo <<< EOF
    <div class="target">
      <select name="target">
        <option value="0">Select one</option>
EOF;

// which target?

// expensive query - goes to slave
//      db_set_active('game_' . $game . '_slave1');
      switch ($item->target) {

        case 'clan': // users in your clan

        case 'neighborhood': // users in your neighborhood

        case 'neighborhood_higher_than_you_but_still_debateable':
// people in your neighborhood who aren't on your wall nor in your clan
// who are a higher level than you but are still debateable

        case 'neighborhood_not_met':
// people in your neighborhood who aren't on your wall nor in your clan

        case 'neighborhood_no_official_not_home':
// non-party users who aren't officials

        case 'neighborhood_no_official_not_home_not_babylonian':
// non-party users who aren't officials and aren't babylonian 8-)))

        case 'officials':
// elected officials only
          $data2 = _target_list($item->target, $game_user);
          break;


        case 'officials_type_1':

// type 1 elected officials only
          $data2 = array();
          $sql = 'SELECT elected_positions.id AS ep_id,
            elected_positions.group as ep_group,
            elected_positions.name AS ep_name, blah.*,
            clan_members.is_clan_leader, clans.acronym AS clan_acronym
            FROM elected_positions
            RIGHT JOIN (

-- type 1: neighborhood positions

            SELECT elected_officials.fkey_elected_positions_id,
              elected_officials.approval_rating, users.*
            FROM elected_officials
            LEFT JOIN users ON elected_officials.fkey_users_id = users.id
            LEFT JOIN elected_positions
              ON elected_positions.id =
                elected_officials.fkey_elected_positions_id
            WHERE users.fkey_neighborhoods_id = %d
            AND elected_positions.type = 1
            ) AS blah ON blah.fkey_elected_positions_id = elected_positions.id

            LEFT OUTER JOIN clan_members
              ON clan_members.fkey_users_id = blah.id
            LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
            ORDER BY elected_positions.energy_bonus DESC, ep_id ASC;';

          $result = db_query($sql, $game_user->fkey_neighborhoods_id,
            $game_user->fkey_values_id);
          while ($official = db_fetch_object($result)) $data2[] = $official;

          break;

        case 'officials_type_2':

// type 2 elected officials only
          $data2 = array();
          $sql = 'SELECT elected_positions.id AS ep_id,
            elected_positions.group as ep_group,
            elected_positions.name AS ep_name, blah.*,
            clan_members.is_clan_leader, clans.acronym AS clan_acronym
            FROM elected_positions
            RIGHT JOIN (
            SELECT elected_officials.fkey_elected_positions_id,
              elected_officials.approval_rating, users.*
            FROM elected_officials
            LEFT JOIN users ON elected_officials.fkey_users_id = users.id
            LEFT JOIN elected_positions
              ON elected_positions.id =
                elected_officials.fkey_elected_positions_id
            WHERE users.fkey_values_id = %d
            AND elected_positions.type = 2
            ) AS blah ON blah.fkey_elected_positions_id = elected_positions.id

            LEFT OUTER JOIN clan_members
              ON clan_members.fkey_users_id = blah.id
            LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
            ORDER BY elected_positions.energy_bonus DESC, ep_id ASC;';

          $result = db_query($sql, $game_user->fkey_values_id);
          while ($official = db_fetch_object($result)) $data2[] = $official;

          break;

        case 'party':
// users in your political party

          $data2 = array();
          $sql = 'SELECT users.username, users.id,
            clan_members.is_clan_leader, clans.acronym AS clan_acronym,
            NULL as ep_name
            FROM users
            LEFT OUTER JOIN clan_members
              ON clan_members.fkey_users_id = users.id
            LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
            WHERE fkey_values_id = %d
            AND users.id <> %d
            AND users.username <> ""
            ORDER BY username ASC;';

          $result = db_query($sql, $game_user->fkey_values_id, $game_user->id);
          while ($user = db_fetch_object($result)) $data2[] = $user;

          break;

        case 'wall_no_official':
// users on your wall who aren't officials

          $data2 = array();
          $sql = 'SELECT DISTINCT user_messages.fkey_users_from_id AS id,
            users.username, clan_members.is_clan_leader,
            clans.acronym AS clan_acronym, NULL AS ep_name
            FROM users

            LEFT JOIN user_messages
              ON user_messages.fkey_users_from_id = users.id

            LEFT OUTER JOIN clan_members
              ON clan_members.fkey_users_id = users.id

            LEFT OUTER JOIN clans
              ON clan_members.fkey_clans_id = clans.id

            LEFT OUTER JOIN elected_officials
              ON users.id = elected_officials.fkey_users_id

            WHERE user_messages.fkey_users_to_id = %d
            AND users.id <> %d
            AND elected_officials.id IS NULL

            ORDER BY username ASC ;';

          $result = db_query($sql, $game_user->id, $game_user->id);
          while ($user = db_fetch_object($result)) $data2[] = $user;

          break;

      } // switch
      db_set_active('game_' . $game); // reset to master

// too many to list?  separate by first letter

      if ($phone_id == 'abc123' &&
        count($data2) > 250) {

        $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'. 'I', 'J',
          'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
          'X', 'Y', 'Z', 'Others');

        foreach ($letters as $letter) { // show mini list

          echo '<option value="letter_' . $letter . '">' .
            $letter . '</option>';

        }

      } else { // full list foreach()

        foreach ($data2 as $user) {
// firep($user);

          $clan_acronym = '';

          if (!empty($user->clan_acronym))
            $clan_acronym = "($user->clan_acronym)";

          if ($user->is_clan_leader)
            $clan_acronym .= '*';

          echo '<option value="' . $user->id . '">' .
            substr($user->ep_name . ' ' . $user->username . ' ' . $clan_acronym,
            0, 30) . '</option>';

        } // full list foreach()

      } // too many to list?

      echo <<< EOF
      </select>
    </div>
EOF;

    } // if target != none

  } // if next_action_time_remaining < 0

  echo <<< EOF
  </form>
</div>
</div>
EOF;

  return TRUE;

}
