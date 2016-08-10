<?php

define('CONSERVE_ACTIONS', 1);
define('CONSERVE_MONEY', 2);

  function _move_ai() {

    global $game, $ai_output, $bt_skip;

    $game = check_plain(arg(0));
    include(drupal_get_path('module', $game) . '/game_defs.inc');


// no AI moves if load is too high
    $load = sys_getloadavg();

    if ($load[0] > 7) {
//      mail('joseph@cheek.com', 'not performing AI moves for ' .
//        $game_name_full, "since load average is $load[0]");
      return;

    }

    db_set_active('default');  
    lock_init();

    if (!lock_acquire('ai_move', 1.0)) {
//      mail('joseph@cheek.com', 'not performing AI moves for ' .
//        $game_name_full, "since lock could not be acquired");
      db_set_active('game_' . $game);
      return;
    }

    db_set_active('game_' . $game);

    $ai_output = '';
    $bt_skip = count(debug_backtrace());
    _ai_out('start: load avg ' . $load[0]);

    _ai_list_all_ais();

    _ai_do('have_num_clan_leaders', 1) && _ai_do('build');
    _ai_do('have_num_interns', 21) && _ai_do('build');
    _ai_do('have_num_members', 18) && _ai_do('build');
    _ai_do('have_num_treasurers', 2) && _ai_do('build');
    _ai_do('have_num_alders', 1) && _ai_do('build');

if ((mt_rand(0, 99) < -5) || (arg(2) == 'abc123'))
    mail('joseph@cheek.com', 'performing AI moves for ' .
      $game_name_full, $ai_output);

    db_set_active('default');  
    lock_release('ai_move');
    db_set_active('game_' . $game);

  }


  function _ai_out($output) {
// add output to $ai_output

    global $ai_output, $bt_skip;

    $bt = debug_backtrace();
    $args = '';

    $args = '(' . implode(', ', $bt[1]['args']) . ')';
    $spaces = str_repeat(' ', count($bt) - $bt_skip);

    if ((count($bt) - $bt_skip) <= 2) $ai_output .= "\n";

    if ($bt[1]['function'] == '_ai_do') {
      $ai_output .= $spaces . "$output\n";
    } else {
      $ai_output .= $spaces . $bt[1]['function'] . "$args: $output\n";
    }

// failsafe hack

    if (strlen($ai_output) > 25000) { // too long -- kill script
//      mail('joseph@cheek.com', 'ai output too long!', $ai_output);
      db_set_active('default');
      return;
    }

  }


  function _ai_do($goal) {
// work toward a specific goal, if not already accomplished

    $goal_function = '_ai_' . str_replace(' ', '_', $goal);
    $args = func_get_args();
    $args = array_slice($args, 1);

    if (!function_exists($goal_function)) {
      _ai_out("*FIXME: MISSING* $goal_function(" . implode(', ', $args) . ')');
      return FALSE;
    }

    _ai_out('calling ' . $goal_function . '(' . implode(', ', $args) . ')');

    return call_user_func_array($goal_function, $args);

  }


  function _ai_build() {
// generic function for building a random ai player

    global $game;

    _ai_out('figuring out what is the best building course of action');

    if (mt_rand(0,2) > 0) { // find most available player

      $sql = 'select phone_id from users
        where meta like "ai_%"
        order by (energy + (actions * 40)) desc
        limit 1;';

    } else { // random player

      $sql = 'select phone_id from users
        where meta like "ai_%"
        order by RAND()
        limit 1;';

    }

    $result = db_query($sql);
    $item = db_fetch_object($result);
    $ai_id = $item->phone_id;

    _ai_web_request($ai_id, 'home');
    $ai_user = _ai_fetch_user($ai_id);
    _ai_out("Building player #$ai_id");

    if ($ai_user->skill_points > 0) {

      _ai_out('We have skill points to allocate!');
      return _ai_do('allocate skill points', $ai_id);

    }

    return _ai_do('boost experience', $ai_id);

  }


  function _ai_do_quest($ai_id, $quest) {
// perform quest $quest as player $ai_id

    $ai_response = _ai_web_request($ai_id, 'quests_do', $quest);

    if ($ai_response == 'quest-succeeded') {
//      _ai_out('Quest succeeded!');
      return TRUE;
    }

    _ai_out('FIXME: Quest failed!!!  What to do?');
    $response_array = explode(' ', $ai_response);

    foreach($response_array as $response) {

      if ($response == 'quest-failed') {

        continue;

      } else if (strpos($response, 'need-staff-') === 0) {

        $staff_to_get = substr($response, 11);
        _ai_out("Trying to get Staff #$staff_to_get");

// see if there is a quest that will loot that staff
        $sql = 'select id, name from quests
          where fkey_loot_staff_id = %d
          order by rand() limit 1;';
        $result = db_query($sql, $staff_to_get);
        $item = db_fetch_object($result);

        if (!empty($item->id)) {

          _ai_out("Quest #$item->id, $item->name has Staff #$staff_to_get; " .
            'doing it!');
          $worked = _ai_do('do quest', $ai_id, $item->id);
// yes, call recursively

          if ($worked) return FALSE;
// this quest may have succeeded, but goal failed

        } // quest to loot item

// next, check to see if we can purchase item
        
        _ai_out("Ok, seeing if we can purchase Staff #$staff_to_get");
        _ai_do('purchase staff', $ai_id, $staff_to_get);
        return FALSE;

      } else if (strpos($response, 'need-equipment-') === 0) {

        $eq_to_get = substr($response, 15);
        _ai_out("Trying to get Equipment #$eq_to_get");

// see if there is a quest that will loot that eq
        $sql = 'select id, name from quests
          where fkey_loot_equipment_id = %d
          order by rand() limit 1;';
        $result = db_query($sql, $eq_to_get);
        $item = db_fetch_object($result);

        if (!empty($item->id)) {

          _ai_out("Quest #$item->id, $item->name has Equipment #$eq_to_get; " .
            'doing it!');
          $worked = _ai_do('do quest', $ai_id, $item->id);
// yes, call recursively

          if ($worked) return FALSE;
// this quest may have succeeded, but goal failed

        } // quest to loot item

// next, check to see if we can purchase item
        
        _ai_out("Ok, seeing if we can purchase Equipment #$eq_to_get");
        _ai_do('purchase equipment', $ai_id, $eq_to_get);
        return FALSE;

      } // test response

    } // foreach response

    _ai_out('FIXME: Not doing anything -- giving up!!!');
    return FALSE;

  }


  function _ai_web_request($ai_id, $screen, $arg1 = '') {
// call a web request for player $ai_id to screen $screen with optional
// arg $arg1

    global $game;

    $uri = 'http://' . $_SERVER['SERVER_NAME'] . "/$game/$screen/$ai_id";
    if ($arg1 != '') $uri .= "/$arg1";

    if (($screen != 'home') &&
      ($screen != 'debates_challenge') &&
      ($screen != 'land'))
      _ai_out("$ai_id: doing $screen $arg1 @ $uri");

    $response = file_get_contents($uri);

    $ai_response_start = strpos($response, "\n<ai ") + 5;

    if ($ai_response_start > 5) {
      $ai_response_end = strpos($response, "/>\n", $ai_response_start);
      $ai_response = trim(substr($response, $ai_response_start,
        $ai_response_end - $ai_response_start));
    } else {
      $ai_response = $response;
    }

    if (($screen != 'home') &&
      ($screen != 'land'))
      _ai_out("server response: $ai_response");

    return trim(str_replace('"', '', $ai_response));

  }


  function _ai_have_num_interns($num_interns) {
// goal: $num interns across the game

    $sql = 'select count(users.id) as count from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta like "ai_%"
      and fkey_elected_positions_id >= 7
      and fkey_elected_positions_id <= 11;';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("Want $num_interns interns, have $item->count");

    if ($item->count >= $num_interns) {
      _ai_out('woohoo!  we have enough interns!  returning TRUE');
      return TRUE;
    }

    $extra_accts = 1; // have an extra account

// can't have $num interns if we don't have $num breachers.  do that first.
    if (!_ai_do('have num type level min max players',
      $num_interns + $extra_accts,
      'breacher', 6, 30)) return FALSE;

// can't have $num interns if we don't have $num holders.  do that next.
    if (!_ai_do('have num type level min max players',
      $num_interns + $extra_accts,
      'holder', 6, 30)) return FALSE;

// need some scouts to look for free seats, too
    if (!_ai_do('have num type level min max players',
      ceil(($num_interns + $extra_accts) / 4),
      'scout', 12, 130)) return FALSE;

    $ai_user = _ai_find_free_player('breacher', 6, 30, 1);

    if (!empty($ai_user)) {

      _ai_out("Found a free breacher, $ai_user->phone_id " .
        "(level $ai_user->level)");

      _ai_do('hood seat challenge', $ai_user->phone_id, 7, 11);
      return FALSE;

    }

    _ai_out('What?  We couldn\'t find a single free breacher?');

    _ai_out("*FIXME*: we have $num_interns intern-capable players now, " .
      'but none of them have free actions.  What do I do?');

    return FALSE;

  }


  function _ai_have_num_clan_leaders($num_leaders) {
// goal: $num clan leaders across the game

    $sql = 'SELECT COUNT( users.id ) AS count
      FROM users
      LEFT JOIN clan_members ON clan_members.fkey_users_id = users.id
      WHERE users.phone_id LIKE "ai-%"
      AND clan_members.is_clan_leader =1;';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("Want $num_leaders clan leaders, have $item->count");

    if ($item->count >= $num_leaders) {
      _ai_out('woohoo!  we have enough clan leaders!  returning TRUE');
      return TRUE;
    }

// find a free player.  make it a holder, just cause they don't do much.
    $ai_user = _ai_find_free_player('holder', 20, 150, 5);

    if (!empty($ai_user)) {

      _ai_out("Found a free player, $ai_user->phone_id " .
        "(level $ai_user->level)");

      _ai_out('Checking for necessary equipment ' . "(money: $ai_user->money)");

      $stuff_to_get = array( // equip_id => level
         2 => 20,
         7 => 40,
        12 => 60,
        17 => 80,
      );

      $sql = 'select quantity from equipment_ownership
        where fkey_equipment_id = %d and fkey_users_id = %d;';

      foreach ($stuff_to_get as $equip_id => $level) {

        if ($ai_user->level >= $level) {

          _ai_out("At level $level; maybe I can buy equip #$equip_id?");

          $result = db_query($sql, $equip_id, $ai_user->id);
          $item = db_fetch_object($result);

          if ($item->quantity >= 1) {
            _ai_out('Already have one!');
            continue;
          }

          _ai_out('I don\'t have one... trying to get it!');
          _ai_web_request($ai_user->phone_id, 'equipment_buy', "$equip_id/1");

        } // if high enough level

      } // foreach thing

      $digit = mt_rand(0, 9);
      $name = 'ToxiCorp%20' . $digit;
      $acronym = 'T' . $digit . 'X';

      _ai_web_request($ai_user->phone_id, 'actions_do',
        '4?name=' . $name . '&acronym=' . $acronym);

      return FALSE;

    }

    _ai_out('What?  We couldn\'t find a single free holder?');

    _ai_out("*FIXME* " . __LINE__);
    return FALSE;

  }


  function _ai_have_num_members($num_members) {
// goal: $num members across the game

    $sql = 'select count(users.id) as count from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta like "ai_%"
      and fkey_elected_positions_id >= 4
      and fkey_elected_positions_id <= 6;';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("Want $num_members members, have $item->count");

    if ($item->count >= $num_members) {
      _ai_out('woohoo!  we have enough members!  returning TRUE');
      return TRUE;
    }

    $extra_accts = 1; // have an extra account

// can't have $num interns if we don't have $num breachers.  do that first.
    if (!_ai_do('have num type level min max players',
      $num_members + $extra_accts,
      'breacher', 20, 45)) return FALSE;

// can't have $num interns if we don't have $num holders.  do that next.
    if (!_ai_do('have num type level min max players',
      $num_members + $extra_accts,
      'holder', 20, 45)) return FALSE;

// need some scouts to look for free seats, too
    if (!_ai_do('have num type level min max players',
      ceil(($num_members + $extra_accts) / 4),
      'scout', 12, 130)) return FALSE;

    $ai_user = _ai_find_free_player('breacher', 20, 45, 3);

    if (!empty($ai_user)) {

      _ai_out("Found a free breacher, $ai_user->phone_id " .
        "(level $ai_user->level)");

      _ai_do('hood seat challenge', $ai_user->phone_id, 4, 6);
      return FALSE;

    }

    _ai_out('What?  We couldn\'t find a single free breacher?');

    _ai_out("*FIXME*: we have $num_members member-capable players now, " .
      'but none of them have free actions.  What do I do?');

    return FALSE;

  }


  function _ai_have_num_treasurers($num) {
// goal: $num treasurers across the game

    $sql = 'select count(users.id) as count from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta like "ai_%"
      and fkey_elected_positions_id = 3;';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("Want $num treasurers, have $item->count");

    if ($item->count >= $num) {
      _ai_out('woohoo!  we have enough!  returning TRUE');
      return TRUE;
    }

    $extra_accts = 1; // have an extra account

// can't have $num treasurers if we don't have $num breachers.  do that first.
    if (!_ai_do('have num type level min max players',
      $num + $extra_accts,
      'breacher', 35, 55)) return FALSE;

// can't have $num treasurers if we don't have $num holders.  do that next.
    if (!_ai_do('have num type level min max players',
      $num + $extra_accts,
      'holder', 35, 55)) return FALSE;

// need some scouts to look for free seats, too
    if (!_ai_do('have num type level min max players',
      ceil(($num + $extra_accts) / 4),
      'scout', 12, 130)) return FALSE;

    $ai_user = _ai_find_free_player('breacher', 35, 55, 5);

    if (!empty($ai_user)) {

      _ai_out("Found a free breacher, $ai_user->phone_id " .
        "(level $ai_user->level)");

      _ai_do('hood seat challenge', $ai_user->phone_id, 3, 3);
      return FALSE;

    }

    _ai_out('What?  We couldn\'t find a single free breacher?');

    _ai_out("*FIXME*: we have $num_members treasurer-capable players now, " .
      'but none of them have free actions.  What do I do?');

    return FALSE;

  }


  function _ai_have_num_alders($num_alders) {
// goal: $num alders across the game

    $sql = 'select count(users.id) as count from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta like "ai_%"
      and fkey_elected_positions_id = 1;';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("Want $num_alders alders, have $item->count");

    if ($item->count >= $num_alders) {
//      _ai_out('woohoo!  we have enough alders!  returning TRUE');
      return TRUE;
    }

// can't have $num alders if we don't have $num breachers.  do that first.
    if (!_ai_do('have num type level min max players', $num_alders,
      'breacher', 50, 70)) return FALSE;

// can't have $num alders if we don't have $num holders.  do that next.
    if (!_ai_do('have num type level min max players', $num_alders,
      'holder', 50, 70)) return FALSE;

// need some scouts to look for free seats, too
    if (!_ai_do('have num type level min max players', floor($num_alders / 4),
      'scout', 12, 130)) return FALSE;

    $ai_user = _ai_find_free_player('breacher', 50, 70, 10);

    if (!empty($ai_user)) {

      _ai_out("Found a free breacher, $ai_user->phone_id " .
        "(level $ai_user->level)");

    } else {

      _ai_out('*FIXME*  Egads!  No free breachers found!');

    }

    _ai_out("*FIXME*: we have $num_alders alder-capable players now.
      What do I do?");

    return TRUE;    

  }


  function _ai_find_free_player($type, $level_min, $level_max,
    $min_actions = 0) {

    _ai_out("checking for free $type between levels $level_min and " .
      "$level_max, inclusive, with $min_actions free action(s)");

    $sql = 'select phone_id from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta = "%s"
      and level >= %d
      and level <= %d
      and fkey_elected_positions_id is NULL
      and actions >= %d
      order by (energy + (actions * 40)) DESC
      limit 10;';
    $result = db_query($sql, "ai_$type", $level_min, $level_max, $min_actions);
    while ($item = db_fetch_object($result)) $data[] = $item;

    if (!empty($data)) return _ai_fetch_user($data[mt_rand(0, 9)]->phone_id);

    _ai_out("No free $type ... looking for one with extra skill points");

    $sql = 'select phone_id from users
      left join elected_officials
      on elected_officials.fkey_users_id = users.id
      where users.meta = "%s"
      and level >= %d
      and level <= %d
      and fkey_elected_positions_id is NULL
      and skill_points > 1
      order by skill_points DESC
      limit 1;';
    $result = db_query($sql, "ai_$type", $level_min, $level_max);
    $item = db_fetch_object($result);

    if (!empty($item)) {

      _ai_allocate_skill_for_action($item->phone_id);
      $ai_user = _ai_fetch_user($item->phone_id);

      if ($ai_user->actions >= $min_actions) return $ai_user;

    }

// if not: do we have a player of $type that is a lower level?
// if so, we can just boost

    _ai_out("Still no free $type ... boosting one");
    $data = array();

    $sql = 'select phone_id, level, energy, actions from users
      where meta like "%s"
      and level < %d
      order by (energy + (actions * 40)) desc
      limit 10;';
    $result = db_query($sql, "ai_$type", $level_min);
    while ($item = db_fetch_object($result)) $data[] = $item;

    if (!empty($data)) { // found one!

      $item = $data[mt_rand(0, 9)];

// out of actions and energy?  Maybe we need another one...
      if (($item->energy <= 0) && ($item->actions <= 0)) {

        _ai_out("Maybe we need another $type!");
        _ai_out("*FIXME* stub " . __LINE__);

      }

      _ai_out("found level $item->level $type player ($item->phone_id), " .
        'commencing boosting');

      _ai_do('boost experience', $item->phone_id);

    }

    return FALSE;

  }


  function _ai_have_num_players($num_players) {
// goal: $num players across the game


    $sql = 'select count(id) as count from users
      where meta like "ai_%";';
    $result = db_query($sql);
    $item = db_fetch_object($result);

    _ai_out("want $num_players players, have $item->count");

    if ($item->count >= $num_players) {
//      _ai_out('woohoo!  we have enough players!  returning TRUE');
      return TRUE;
    }

    _ai_do('create new player');
    return FALSE;

  }


  function _ai_create_new_player() {
// create a new player

    global $game;

    while (TRUE) {

      $num = mt_rand(0, 99999);
      $id_to_check = 'ai-' . $num;

       _ai_out("checking for existing player $id_to_check");

      $sql = 'select id from users
        where phone_id = "%s";';
      $result = db_query($sql, $id_to_check);
      $item = db_fetch_object($result);

      if (empty($item)) break;

    }

    $uri = 'http://' . $_SERVER['SERVER_NAME'] . "/$game/home/$id_to_check";

    _ai_out("phone_id $id_to_check not in use; URI is $uri");

    $response = file_get_contents($uri);
//    _ai_out($response);

    _ai_out('updating record to make this a ToxiCorp Employee');
    $sql = 'update users set username = "%s", fkey_neighborhoods_id = 75,
      fkey_values_id = 9, `values` = "Goo", meta = "ai_minion"
      where phone_id = "%s";';
    $result = db_query($sql, "TC Emp $num", $id_to_check);

    return FALSE;

  }


  function _ai_have_num_type_level_min_max_players($num_players, $type,
    $level_min, $level_max) {
// goal: $num players across the game

    $sql = 'select count(id) as count from users
      where meta like "%s"
      and level >= %d
      and level <= %d;';
    $result = db_query($sql, "ai_$type", $level_min, $level_max);
    $item = db_fetch_object($result);

    _ai_out("want $num_players {$type}s, have $item->count");

    if ($item->count >= $num_players) {
//      _ai_out('woohoo!  we have enough players!  returning TRUE');
      return TRUE;
    }

// if not: do we have a player of $type that is a lower level?
// if so, we can just boost

    $sql = 'select phone_id, level from users
      where meta like "%s"
      and level < %d
      order by (energy + (actions * 40)) desc
      limit 1;';
    $result = db_query($sql, "ai_$type", $level_min);
    $item = db_fetch_object($result);

    if ($item->phone_id) { // found one!
      _ai_out("found level $item->level $type player ($item->phone_id), " .
        'commencing boosting');

      _ai_do('boost experience', $item->phone_id);
      return FALSE;

    }

// if not: do we have an extra minion that isn't higher than $level_max?
// if so, tell that player to specialize

    if ($type != 'minion') { // wasted effort if we just want a minion

      $sql = 'select phone_id, level from users
        where meta like "ai_minion"
        and level <= %d
        order by experience desc
        limit 1;';
      $result = db_query($sql, $level_max);
      $item = db_fetch_object($result);

      if ($item->phone_id) { // found one!
        _ai_out("found level $item->level minion ($item->phone_id), " .
          "telling to specialize as $type");

        $sql = 'update users set meta = "%s"
          where phone_id = "%s";';
        $result = db_query($sql, "ai_$type", $item->phone_id);

        return FALSE;

      } // found a minion to specialize

    } // if we aren't searching for a minion

// no $type player found of appropriate level, none lower that we can boost,
// no minions that can be told to specialize.  creating new minion

    _ai_out('efforts to find existing player to progress toward ' .
      "level $level_min to $level_max $type player have failed.  " .
      'Creating new minion.');

    _ai_do('create new player');
    return FALSE;

  }


  function _ai_allocate_skill_for_elocution($ai_id) {
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);
    
    _ai_out("bumping Elocution up (now: $ai_user->elocution)");
    $ai_response = _ai_web_request($ai_id, 'increase_skills', 'elocution');
 
    if ($ai_response == 'increase-skill-succeeded') {
      _ai_out('Woohoo!  Elocution increase succeeded!');
      return TRUE;
    }

    _ai_out('Uhoh!  Elocution increase failed');
    return FALSE;

  }


  function _ai_allocate_skill_for_action($ai_id) {
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);
    
    _ai_out("bumping Actions up (now: $ai_user->actions_max)");
    $ai_response = _ai_web_request($ai_id, 'increase_skills', 'actions');

    if ($ai_response == 'increase-skill-succeeded') {
      _ai_out('Woohoo!  Actions increase succeeded!');
      return TRUE;
    }

    _ai_out('Uhoh!  Actions increase failed');
    return FALSE;

  }


  function _ai_allocate_skill_for_energy($ai_id) {
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);
    
    _ai_out("bumping Energy up (now: $ai_user->energy_max)");
    $ai_response = _ai_web_request($ai_id, 'increase_skills', 'energy_max');

    if ($ai_response == 'increase-skill-succeeded') {
      _ai_out('Woohoo!  Energy increase succeeded!');
      return TRUE;
    }

    _ai_out('Uhoh!  Energy increase failed');
    return FALSE;

  }


  function _ai_allocate_skill_for_initiative($ai_id) {
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);
    
    _ai_out("bumping Initiative up (now: $ai_user->initiative)");
    $ai_response = _ai_web_request($ai_id, 'increase_skills', 'initiative');

    if ($ai_response == 'increase-skill-succeeded') {
      _ai_out('Woohoo!  Initiative increase succeeded!');
      return TRUE;
    }

    _ai_out('Uhoh!  Initiative increase failed');
    return FALSE;

  }


  function _ai_allocate_skill_for_endurance($ai_id) {
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);
    
    _ai_out("bumping Endurance up (now: $ai_user->endurance)");
    $ai_response = _ai_web_request($ai_id, 'increase_skills', 'endurance');

    if ($ai_response == 'increase-skill-succeeded') {
      _ai_out('Woohoo!  Endurance increase succeeded!');
      return TRUE;
    }

    _ai_out('Uhoh!  Endurance increase failed');
    return FALSE;

  }


  function _ai_allocate_skill_points($ai_id) {
// allocate skill points
// NB: don't *EVER* use _ai_fetch_user() here --
// this could cause an infinite loop

    $sql = 'select * from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $item = db_fetch_object($result);
    
    _ai_out("$ai_id is an $item->meta; allocating skill points based on that");

    if ($item->elocution < 3) return _ai_allocate_skill_for_elocution($ai_id);

    if (($item->actions_max < 20) && ($item->skill_points >= 2))
      return _ai_allocate_skill_for_action($ai_id);

    if ($item->level <= 25) {
// up to level 25: even split between actions, energy, specialty, and elocution

      switch(mt_rand(0, 3)) {

        case 0: // actions
          return _ai_allocate_skill_for_action($ai_id);

        case 1: // energy
          return _ai_allocate_skill_for_energy($ai_id);

        case 2: // specialty

          switch($item->meta) {

            case 'ai_breacher':
              return _ai_allocate_skill_for_initiative($ai_id);

            case 'ai_holder':
              return _ai_allocate_skill_for_endurance($ai_id);

            case 'ai_scout':
              return _ai_allocate_skill_for_action($ai_id);

          } // switch specialty

        case 3: // elocution
          return _ai_allocate_skill_for_elocution($ai_id);

      } // switch 0 - 3

    } // level 25 or less

// above level 25: pure specialty
    switch($item->meta) {

      case 'ai_breacher':
        return _ai_allocate_skill_for_initiative($ai_id);

      case 'ai_holder':
        return _ai_allocate_skill_for_endurance($ai_id);

      case 'ai_scout':
        return _ai_allocate_skill_for_action($ai_id);

    } // switch specialty

  }


  function _ai_boost_experience($ai_id, $boost_flags = 0) {
// boost player $ai_id's experience

    global $game;

    include(drupal_get_path('module', $game) . '/game_defs.inc');

    $ai_user = _ai_fetch_user($ai_id);
    _ai_out("Trying to boost experience of $ai_id");

    $multiplier = 0;
    ($ai_user->level >= 10) && $multiplier = 100;
    ($ai_user->level >= 20) && $multiplier = 200;
    ($ai_user->level >= 25) && $multiplier = 400;
    ($ai_user->level >= 30) && $multiplier = 700;
    ($ai_user->level >= 35) && $multiplier = 1000;

    $net_income = $ai_user->income - $ai_user->expenses;
    $income_wanted = $ai_user->level * $multiplier;

    if ($net_income < $income_wanted) {

      _ai_out("Sorry, but I need more income first.  Have $net_income iph, " .
        "want $income_wanted.");

      _ai_do('buy land', $ai_id);
      return FALSE;

    }

    $boost_conserve_actions = ($boost_flags & CONSERVE_ACTIONS);
    $boost_conserve_money = ($boost_flags & CONSERVE_MONEY);

    if ($boost_conserve_actions)
      _ai_out('... but will not use any actions');

    if ($boost_conserve_money)
      _ai_out('... but will not use any money');

    $sql = 'select * from elected_officials
      where fkey_users_id = %d;';
    $result = db_query($sql, $ai_id);
    $seat = db_fetch_object($result);

    if (!$boost_conserve_actions) {

      _ai_out("Seeing if I can exploit my seat... " .
        "(actions: $ai_user->actions/$ai_user->actions_max)");
    
      if (!empty($seat)) {

        _ai_out('Woohoo!  I\'m an elected official!');

        if ($ai_user->actions >= 5) { // need to have 5 actions to exploit

          _ai_out('Woohoo!  I\'m an elected official with 5 action -- ' .
            'I\'m going to exploit my seat!');

          _ai_web_request($ai_id, 'actions_do', 58);
          _ai_do('open presents', $ai_id);

        } // more than 5 actions

      } else {

        _ai_out('I\'m not an elected official 8-(');

      } // in a seat

    } // conserve actions?

    if (empty($seat)) {
// only do missions if i'm not in a seat

      _ai_out("Trying boosting by doing a quest (energy: $ai_user->energy)");

// find the highest level quest we can do
      $sql = 'select id, name from quests 
        where active = 1
        and (fkey_neighborhoods_id = 0 or fkey_neighborhoods_id = %d)
        and required_level <= %d
        and required_energy <= %d
        order by required_energy desc
        limit 1;';
      $result = db_query($sql, $ai_user->fkey_neighborhoods_id,
        $ai_user->level, $ai_user->energy);
      $item = db_fetch_object($result);

      if (!empty($item)) {
  
//        _ai_out("Highest quest I can do is #$item->id, $item->name");
        if (_ai_do('do quest', $ai_id, $item->id)) return TRUE;

      } else {

        _ai_out('No quests found!');

      }


      if (($boost_conserve_actions) && ($event_type != EVENT_DEBATE)) {

        _ai_out('Not going to debate (conserving actions)');

      } else if (($ai_user->meta == 'ai_scout') &&
        ($event_type != EVENT_DEBATE)) {

        _ai_out('Not going to debate (I am a scout)');

      } else {

        _ai_out('Trying boosting by debating');
        if (_ai_do('debate players', $ai_id)) return TRUE;
// if I won debates, call it good
      } 


    } else { // in a seat

      _ai_out('Not going to mission or debate (I am in a seat)');

    }


    if (!$boost_conserve_money) {

      _ai_out('Maybe I can buy something?  Checking for necessary equipment ' .
        "(money: $ai_user->money)");

      $stuff_to_get = array( // equip_id => level
         5 => 16,
         8 => 12,
        13 => 23,
      );

      $sql = 'select quantity from equipment_ownership
        where fkey_equipment_id = %d and fkey_users_id = %d;';

      $attempt_to_get_one = FALSE;

      foreach ($stuff_to_get as $equip_id => $level) {

        if ($ai_user->level >= $level) {

          _ai_out("At level $level; maybe I can buy equip #$equip_id?");

          $result = db_query($sql, $equip_id, $ai_user->id);
          $item = db_fetch_object($result);

          if ($item->quantity >= 1) {
            _ai_out('Already have one!');
            continue;
          }

          _ai_out('I don\'t have one... trying to get it!');
          $attempt_to_get_one = TRUE;
          _ai_web_request($ai_id, 'equipment_buy', "$equip_id/1");

        } // if high enough level

      } // foreach thing

      if ($attempt_to_get_one) return FALSE;
// don't buy land if we need(ed) these


      _ai_out('Checking for necessary staff/agents...');

      $staff_to_get = array( // staff_id => level
        9 => 25,
      );

      $sql = 'select quantity from staff_ownership
        where fkey_staff_id = %d and fkey_users_id = %d;';

      $attempt_to_get_one = FALSE;

      foreach ($staff_to_get as $staff_id => $level) {

        if ($ai_user->level >= $level) {

          _ai_out("At level $level; maybe I can buy staff #$staff_id?");

          $result = db_query($sql, $staff_id, $ai_user->id);
          $item = db_fetch_object($result);

          if ($item->quantity >= 1) {
            _ai_out('Already have one!');
            continue;
          }

          _ai_out('I don\'t have one... trying to get it!');
          $attempt_to_get_one = TRUE;
          _ai_web_request($ai_id, 'staff_hire', "$staff_id/1");

        } // if high enough level

      } // foreach thing

      if ($attempt_to_get_one) return FALSE;
// don't buy land if we need(ed) these

      _ai_out("Ok, trying to get a business...");
      _ai_do('buy land', $ai_id);

    } else {

      _ai_out('Not buying any aides (conserving money)');

    } // conserve money?

    return FALSE;

  }


  function _ai_hood_seat_challenge($ai_id, $seat_min, $seat_max) {
// challenge for a hood seat in a specified range

    $ai_user = _ai_fetch_user($ai_id);   

    _ai_out('Trying to get more seats...');
    _ai_out("looking for empty seats in hood #$ai_user->fkey_neighborhoods_id "
      . "(actions: $ai_user->actions/$ai_user->actions_max)");

    if ($ai_user->actions == 0) {

      _ai_out("No actions!!!");
      _ai_do('boost experience', $ai_id);
      return FALSE;

    }

    $sql = 'SELECT id, name
      FROM elected_positions
      WHERE id NOT IN (

        SELECT elected_officials.fkey_elected_positions_id
          FROM users
          RIGHT JOIN elected_officials
          ON users.id = elected_officials.fkey_users_id
          WHERE users.fkey_neighborhoods_id = %d
        )

      AND id >= %d
      AND id <= %d
      AND energy_bonus <= %d
      order by energy_bonus DESC
      limit 1;';
    $result = db_query($sql, $ai_user->fkey_neighborhoods_id,
      $seat_min, $seat_max, $ai_user->actions);
    $item = db_fetch_object($result);   

    if (!empty($item)) {

      _ai_out("Found empty $item->name seat (#$item->id)!");

      $ai_response = _ai_web_request($ai_id, 'elections_challenge', $item->id);

      if ($ai_response == 'election-won') {
        _ai_out('Woohoo!  Player challenged and won!');
        return TRUE;
      }

      if ($ai_response == 'election-lost') {
        _ai_out('Egads!  Player challenged an empty seat and lost?!?!?');
      }

      if ($ai_response == 'election-failed no-action') {
        _ai_out('Egads!  Out of Action!  But I checked before I challenged!');
      }

      if ($ai_response == 'election-failed no-challenge-yourself') {
        _ai_out('*FIXME*  How can I challenge myself?');
      }

      if ($ai_response == 'election-failed none-here') {
        _ai_out("No elections here (hood $ai_user->fkey_neighborhoods_id)" .
          '...  I must move!');
        _ai_do('move somewhere', $ai_id);
        return FALSE;

      }

    } else {

//      _ai_out('Found no empty seats... 8-(((');

    } // challenge for an empty seat

    _ai_out('Looking for occupied seats to challenge... *FIXME* -- stub @ '
      . __LINE__);

//    _ai_out('Checking for empty seats in other hoods...');

    $sql = 'select DISTINCT fkey_neighborhoods_id as hood_id,
      neighborhoods.has_elections from users
      left join neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id
      where meta like "ai_%"
      and has_elections = 1;';
    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $hoods[] = $item;

    $sql = 'SELECT id, name
      FROM elected_positions
      WHERE id NOT IN (

        SELECT elected_officials.fkey_elected_positions_id
          FROM users
          RIGHT JOIN elected_officials
          ON users.id = elected_officials.fkey_users_id
          WHERE users.fkey_neighborhoods_id = %d
        )

      AND id >= %d
      AND id <= %d
      order by id ASC
      limit 1;';

    foreach ($hoods as $hood) {

      _ai_out("do we have any free seats in hood #$hood->hood_id?");

      $result = db_query($sql, $hood->hood_id, $seat_min, $seat_max);
      $item = db_fetch_object($result);

      if (!empty($item)) {

//        _ai_out("Hood #$hood->hood_id has free seats, getting ready to move...");
        _ai_do('move', $ai_id, $hood->hood_id);
       return FALSE;

      }

    }

    _ai_out('Sending a scout somewhere to find free seats...');
    $ai_user_scout = _ai_find_free_player('scout', 12, 130, 6);

    if (!empty($ai_user_scout)) {

      _ai_out("Found a free scout, $ai_user_scout->phone_id " .
        "(actions $ai_user_scout->actions/$ai_user_scout->actions_max)");

      _ai_do('move somewhere', $ai_user_scout->phone_id);
      return FALSE;

    }

    _ai_out('What?  We couldn\'t find a single free scout?  ' .
      'Guess I will venture out myself...');

    _ai_do('move somewhere', $ai_user->phone_id);
    return FALSE;

  }


  function _ai_move_somewhere($ai_id) {
// take me... far away... anywhere...
// as long as it's FUN FUN FUN FUN FUN FUN FUN i wanna go!

    $ai_user = _ai_fetch_user($ai_id);

    if ($ai_user->actions < 6) { // not enough actions to move!

      _ai_out('The controller wants me to move but I don\'t have enough '.
        "Actions ($ai_user->actions/$ai_user->actions_max)!  What shall I do?");

      if (($ai_user->actions_max < 6) && ($ai_user->skill_points > 0)) {

        _ai_out('I\'m going to allocate some of my skill points');
        _ai_do('allocate skill points', $ai_id);

      }

      _ai_out('Perhaps if I get more experience, ' .
        'I will have enough to move soon');
      _ai_do('boost experience', $ai_id);
      return FALSE;

    }


// scout or already home?  send out and about

    if (($ai_user->meta == 'ai_scout') ||
      ($ai_user->fkey_neighborhoods_id == 75)) {


      $sql = 'select * from neighborhoods
        where has_elections = 1
        and id <> 75
        and xcoor > 0
        and ycoor > 0
        order by rand()
        limit 1;';
      $result = db_query($sql);
      $item = db_fetch_object($result);

      _ai_out("Sending scout $ai_id to hood #$item->id, $item->name");
      return _ai_do('move', $ai_id, $item->id);

    }


    if ($ai_user->fkey_neighborhoods_id != 75) {
// not home -- move there!

      _ai_out('Why not go home?');
      return _ai_do('move', $ai_id, 75);

    }

  }


  function _ai_move($ai_id, $hood) {
// move to hood $hood

    $ai_user = _ai_fetch_user($ai_id);

// are we already here?
    if ($ai_user->fkey_neighborhoods_id == $hood) return TRUE;

    if ($ai_user->actions < 6) { // not enough actions to move!

      _ai_out("What?  I don't have enough actions " .
        "($ai_user->actions/$ai_user->actions_max) " .
        "to move to hood $hood...");

      if ($ai_user->actions_max < 6)
        _ai_do('boost experience', $ai_id);
      else
        _ai_do('boost experience', $ai_id, CONSERVE_ACTIONS);

      return FALSE;

    }

    $boost_flags = CONSERVE_ACTIONS;

    $sql = 'select xcoor, ycoor, name
      from neighborhoods
      where id = %d;';
    $result = db_query($sql, $ai_user->fkey_neighborhoods_id);
    $here = db_fetch_object($result);   

    $sql = 'select xcoor, ycoor, name
      from neighborhoods
      where id = %d;';
    $result = db_query($sql, $hood);
    $there = db_fetch_object($result);   

    $distance_now = _ai_distance($here->xcoor, $here->ycoor,
      $there->xcoor, $there->ycoor);

    _ai_out("I am in hood #$ai_user->fkey_neighborhoods_id, $here->name, " .
      "going to hood #$hood, $there->name.");

    $sql = 'select * from neighborhoods
      where xcoor > 0 and ycoor > 0;';
    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    $winning_hood = 0;
    $winning_distance = 9999;

    foreach ($data as $item) {

      $distance_to_dest = _ai_distance($item->xcoor, $item->ycoor,
        $there->xcoor, $there->ycoor);
      $distance_from_me = _ai_distance($item->xcoor, $item->ycoor,
        $here->xcoor, $here->ycoor);

      if ($distance_to_dest < $distance_now) {

//        _ai_out("Hood #$item->id, $item->name, is closer to $there->name " .
//          "than I am now in $here->name " .
//          "($distance_to_dest vs. $distance_now).  Its distance from me is " .
//          "$distance_from_me.");

        if ($distance_from_me < $winning_distance) {
//          _ai_out("New candidate for closest hood!  $distance_from_me < " .
//            $winning_distance);
          $winning_distance = $distance_from_me;
          $winning_hood = $item->id;
          $winning_name = $item->name;
        }

      } // closer to dest?

    } // check each hood

    if ($winning_hood > 0) {

      _ai_out(
        "hood #$winning_hood, $winning_name, is the next step " .
        "to $there->name...");

      $ai_response = _ai_web_request($ai_id, 'move_do', $winning_hood);

      if ($ai_response == 'move-succeeded') { // succeeded?  try again!
        $ai_user = _ai_fetch_user($ai_id);

// are already here?
        if ($ai_user->fkey_neighborhoods_id == $hood) return TRUE;

        _ai_move($ai_id, $hood);
        return TRUE;
      }

      if ($ai_response == 'move-failed no-action') {

        _ai_out('Not enough action!  Let\'s see if I can get another aide...');

        if (!_ai_do('increase speed stats', $ai_id))
          $boost_flags |= CONSERVE_MONEY;
// don't spend money if we are saving up for another speed boost

      }

    } else {

      _ai_out('What?  *No* hoods can get me there?');

    }
  
    _ai_out('Move failed!  Settling for boosting of my experience...');
    _ai_do('boost experience', $ai_id, $boost_flags);
    return FALSE;

  }


  function _ai_distance($xcoor1, $ycoor1, $xcoor2, $ycoor2) {
// find the distance between the two points

    $deltax = $xcoor1 - $xcoor2;
    $deltay = $ycoor1 - $ycoor2;

    return ceil(sqrt(pow($deltax, 2) + pow($deltay, 2)));

  }


  function _ai_increase_elocution_stats($ai_id) {
// get more elocution aides for player

    $ai_user = _ai_fetch_user($ai_id);

    $ai_response = _ai_web_request($ai_id, 'equipment');
    $response = array_slice(explode(' ', $ai_response), 1);
    $can_buy = 0;

    foreach ($response as $res) {

      if (strpos($res, '/+elo:') === FALSE) continue;
// only look at elo aides

      $res = substr($res, 0, strpos($res, '/')); // remove /+elo: fluff

      if ($ai_user->money >= (substr($res, strpos($res, '=') + 1) + 0))
        $can_buy = substr($res, 0, strpos($res, '='));

    }

    if ($can_buy > 0) {

//      _ai_out("I have $ai_user->money money, " .
//        "so I can buy aide #$can_buy...");
      $ai_response = _ai_web_request($ai_id, 'equipment_buy', "$can_buy/1");

    } else {

//      _ai_out("I have $ai_user->money money, " .
//        "so I can't buy any elocution aides!");

    }

    return FALSE;

  }


  function _ai_increase_speed_stats($ai_id) {
// get more speed aides for player

    $ai_user = _ai_fetch_user($ai_id);

    $ai_response = _ai_web_request($ai_id, 'equipment');
    $response = array_slice(explode(' ', $ai_response), 1);
    $can_buy = 0;

    foreach ($response as $res) {

      if (strpos($res, '/+spd:') === FALSE) continue;
// only look at elo aides

      $res = substr($res, 0, strpos($res, '/')); // remove /+elo: fluff

      if ($ai_user->money >= (substr($res, strpos($res, '=') + 1) + 0))
        $can_buy = substr($res, 0, strpos($res, '='));

    }

    if ($can_buy > 0) {

      _ai_out("I have $ai_user->money money, " .
        "so I can buy aide #$can_buy...");
      $ai_response = _ai_web_request($ai_id, 'equipment_buy', "$can_buy/1");

    } else {

//      _ai_out("I have $ai_user->money money, " .
//        "so I can't buy any speed aides!");

    }

    return FALSE;

  }


  function _ai_buy_land($ai_id) {
// get more land for player

    $ai_user = _ai_fetch_user($ai_id);

    $ai_response = _ai_web_request($ai_id, 'land');
    $response = array_slice(explode(' ', $ai_response), 1);
    $can_buy = 0;

    foreach ($response as $res) {

      if ($ai_user->money >= (substr($res, strpos($res, '=') + 1) + 0))
        $can_buy = substr($res, 0, strpos($res, '='));

    }

    if ($can_buy > 0) {

      $did_buy = FALSE;
      _ai_out("I have $ai_user->money money, " .
        "so I can buy business #$can_buy...");

      $ai_response = _ai_web_request($ai_id, 'land_buy', "$can_buy/10");
      ($ai_response == 'land-succeeded') && $did_buy = TRUE;

      $ai_response = _ai_web_request($ai_id, 'land_buy', "$can_buy/5");
      ($ai_response == 'land-succeeded') && $did_buy = TRUE;

      $ai_response = _ai_web_request($ai_id, 'land_buy', "$can_buy/1");
      ($ai_response == 'land-succeeded') && $did_buy = TRUE;

      return $did_buy;

    } else {

//      _ai_out("I have $ai_user->money money, " .
//        "so I can't buy any businesses!");

    }

    return FALSE;

  }


  function _ai_debate_players($ai_id) {
// debate as many players as we can

    global $game;

    include(drupal_get_path('module', $game) . '/game_defs.inc');

    $ai_user = _ai_fetch_user($ai_id);

//    $debate_time = 1200;
//    $zombie_debate_wait = 300;

    $sql = 'SELECT users.id, users.username
      FROM users
      LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
      LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
      WHERE users.id <> %d
      AND (clans.id <> %d OR clans.id IS NULL OR users.meta = "zombie")
      AND username <> ""
      AND (debates_last_time < "%s" OR
       (users.meta = "zombie" AND debates_last_time < "%s"))
      AND users.level > %d
      AND users.level < %d
      ORDER BY abs(users.experience - %d) ASC
      LIMIT %d;';

    $result = db_query($sql, $ai_user->id, $ai_user->fkey_clans_id,
      date('Y-m-d H:i:s', time() - $debate_time),
      date('Y-m-d H:i:s', time() - $zombie_debate_wait),
      $ai_user->level - 15,
      $ai_user->level + 15, $ai_user->experience, $ai_user->actions);

    $data = array();
    while ($item = db_fetch_object($result)) $data[] = $item;
    $successful_debate = FALSE;
    _ai_out("Found " . count($data) .
      " people to debate ($ai_user->actions actions)");

    foreach ($data as $item) {
  
      _ai_out("Can debate $item->username");
      $ai_response = _ai_web_request($ai_id, 'debates_challenge', $item->id);

      if ($ai_response == 'debate-won') {
//        _ai_out('Woohoo!  Player debated and won!');
        $successful_debate = TRUE;
      }

      if ($ai_response == 'debate-lost') {
//        _ai_out('Egads!  Player debated and lost');
        _ai_do('increase elocution stats', $ai_id);
      }

      if ($ai_response == 'debate-no-action') {
//        _ai_out('Egads!  Out of Action!');
        if ($successful_debate) return TRUE;
        break;
      }

      if ($ai_response == 'debate-must-wait') {
//        _ai_out("Must wait to debate $item->username");
        continue;
      }

    } // foreach player to debate

    return ($successful_debate);

  }


  function _ai_fetch_user($ai_id) {
// fetch user object for phone_id $ai_id

    static $count = 4;

    if (mt_rand(0, 9) < 3)
      _ai_do('open presents', $ai_id);

    $sql = 'select users.*, clan_members.fkey_clans_id
      from users
      LEFT OUTER JOIN clan_members
      ON clan_members.fkey_users_id = users.id
      where phone_id = "%s";';

    do {

      $result = db_query($sql, $ai_id);
      $ai_user = db_fetch_object($result);

      if ($ai_user->skill_points > 0)
        _ai_do('allocate skill points', $ai_id);

    } while (($ai_user->skill_points > 0) && $count--);

    return $ai_user;

  }


  function _ai_open_presents($ai_id) {

    global $game;

    $sql = 'select id from users where phone_id = "%s";';
    $result = db_query($sql, $ai_id);
    $ai_user = db_fetch_object($result);

//    _ai_out('Seeing if I have any presents to open...');

    $exploit_actions = array( // present equip_id => open action_id

      'stlouis' => array(

        35 => 61,
        36 => 62,
        37 => 63,
        38 => 64,

      ),

    );

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d
      and fkey_users_id = %d;';

    foreach ($exploit_actions[$game] as $equip => $action) {

      $result = db_query($sql, $equip, $ai_user->id);
      $item = db_fetch_object($result);
      $quantity = max(min($item->quantity + 0, 3), 0);

//      _ai_out("I have " . ($item->quantity + 0) . " of equipment #$equip, " .
//        "but I will only open $quantity");

      while ($quantity--) {
        _ai_web_request($ai_id, 'actions_do', $action);
      }

    } // foreach array

  }


  function _ai_list_all_ais() {
// list ai users.  Freshen stats of those that haven't been accessed in a while.

    $sql = 'select * from users
      where meta like "ai_%"
      and last_access < "%s"
      limit 6;';
    $result = db_query($sql, date('Y-m-d H:i:s', time() - 600));
    while ($item = db_fetch_object($result)) $data[] = $item;

//    _ai_out(count($data) . ' ai players need refreshing.');

    foreach ($data as $item) {

      _ai_web_request($item->phone_id, 'home');

    }


// tell the richest player(s) to buy more land

    $data = array();
    $sql = 'select phone_id from users
      where meta like "ai_%"
      order by money DESC
      limit 1;';
    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      _ai_do('buy land', $item->phone_id);

    }


// tell the player(s) with the least income to buy more land

    $data = array();
    $sql = 'select phone_id from users
      where meta like "ai_%"
      order by income ASC
      limit 3;';
    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      _ai_do('buy land', $item->phone_id);

    }


// now show stats

    return; // or not

    $data = array();
    $sql = 'select users.*, clan_members.fkey_clans_id
      from users
      LEFT OUTER JOIN clan_members
      ON clan_members.fkey_users_id = users.id
      where meta like "ai_%"
      order by experience desc;';
    $result = db_query($sql);
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      _ai_out(sprintf('%-8s (level %03d %-8s)  energy:%4d  actions:%3d/%3d  ' .
        'money:%8d  last_access:%15s', $item->phone_id, $item->level,
        substr($item->meta, 3),
        $item->energy, $item->actions, $item->actions_max, $item->money,
        substr($item->last_access, 5)));

    }

  }
