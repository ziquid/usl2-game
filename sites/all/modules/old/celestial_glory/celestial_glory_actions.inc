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

  if ($game == 'stlouis') {

    if (arg(3) == 'banking') {
      $banking_active = 'active';
      $actions_type = 'Banking';
      $order_by = 'actions.id ASC';
    } else {
      $normal_active = 'active';
      $actions_type = 'Normal';
      $order_by = 'required_level DESC';
    }

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
      and (actions.need_specific_official_group = 0 or
      actions.need_specific_official_group = ' .
      $item->group . ')';

  } else {

    $sql_to_add .= ' and actions.need_elected_official <> 1
      and actions.need_specific_elected_official = 0
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
  ' AND quantity > 0 ' . $sql_to_add .
  ' ORDER BY ' . $order_by;
  firep($sql);
  $result = db_query($sql, $game_user->id, $sql_to_add);

  while ($item = db_fetch_object($result)) $data[] = $item;

// actions that have equipment associated with them
  $sql = 'SELECT equipment_ownership.*, actions.*, equipment.required_level
  from equipment_ownership

  left join actions on
  equipment_ownership.fkey_equipment_id = actions.fkey_equipment_id

  left join equipment on
  equipment_ownership.fkey_equipment_id = equipment.id

  where
  (equipment_ownership.fkey_users_id = %d
  OR equipment_ownership.fkey_users_id = 0)

  ' . $actions_active .
  ' AND quantity > 0 ' . $sql_to_add .
  ' ORDER BY required_level DESC';
// firep($sql);
  $result = db_query($sql, $game_user->id);

  while ($item = db_fetch_object($result)) $data[] = $item;

// actions that are free
  $sql = 'select * from {actions}
  where actions.fkey_staff_id = 0 and actions.fkey_equipment_id = 0
  ' . $actions_active . ' ' . $sql_to_add .
  ' ORDER BY name ASC';
// firep($sql);
  $result = db_query($sql, $game_user->id);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as &$action)
    _filter_actions($action, $game_user);

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

  } // switch

  db_set_active('game_' . $game); // reset to master

  return $data2;

}


function _filter_actions(&$action, $game_user) {
// code for action exceptions -- needs to be run in the list and in the do.tpl


// Roses are cheap in Babylon
  if (($action->name == 'Pick roses') &&
    ($game_user->fkey_neighborhoods_id == 6))
    $action->cost = 1;

// Launder money
  if (substr($action->name, 0, 13) == 'Launder some ') {

    if ($game_user->level < 30) {
      $action->active = 0;
      $action->hidden = TRUE;
    } else if ($game_user->level < 60) {
      $action->cost = 10;
      $action->values_cost = $game_user->level * 10000;
      $action->values_change = $game_user->level * 7000;
    } else if ($game_user->level < 90) {
      $action->cost = 20;
      $action->values_cost = $game_user->level * 50000;
      $action->values_change = $game_user->level * 30000;
    } else if ($game_user->level < 120) {
      $action->cost = 30;
      $action->values_cost = $game_user->level * 250000;
      $action->values_change = $game_user->level * 125000;
    } else {
      $action->cost = 40;
      $action->values_cost = $game_user->level * 1250000;
      $action->values_change = $game_user->level * 500000;
    }

  } // launder money

}
