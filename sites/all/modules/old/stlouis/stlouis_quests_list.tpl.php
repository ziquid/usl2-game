<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// do AI moves from this page!!!
  include_once(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if (is_numeric(arg(3))) $group_to_show = arg(3);

  $sql = 'select name from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;

  $sql = 'select party_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $party_title = preg_replace('/^The /', '', $data->party_title);

  if ($game_user->experience == 0) { // show more welcome text for new user

    echo <<< EOF
<div class="welcome">
  <div class="wise_old_man_large point">
  </div>
  <p class="quote">Find some place to hide!</p>
  <p>You hear him yell this at you as you run away.&nbsp; Where can you go?</p>
</div>

EOF;
  } // experience = 0

  _show_goal($game_user);

  if (is_numeric($group_to_show)) {

    $sql_quest_neighborhood = 'where `group` = ' . $group_to_show .
      ' and (fkey_neighborhoods_id = 0 or fkey_neighborhoods_id = ' .
      $game_user->fkey_neighborhoods_id . ')';

  } elseif ($game_user->level < 6) { // show beginning quests

    $group_to_show = '0';
    $sql_quest_neighborhood = 'where `group` = 0';

  } else { // show the group for which the player last successfully completed a quest

    $group_to_show = $game_user->fkey_last_played_quest_groups_id;
    $sql_quest_neighborhood = 'where `group` = ' . $group_to_show .
      ' and (fkey_neighborhoods_id = 0 or fkey_neighborhoods_id = ' .
      $game_user->fkey_neighborhoods_id . ')';

  }

  $sql = 'select name from quest_groups where id = %s;';
  $result = db_query($sql, $group_to_show);
  $qg = db_fetch_object($result);
firep($qg);

  $location = str_replace('%location', $location, $qg->name);

//  if ($game_user->level < 6) $location = '';

  if ($group_to_show > 0) { // have i said i love php's automatic typecasting?

    $older_group = $group_to_show - 1;
    $older_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$older_group">
  <span class="arrows big">&lsaquo;&lsaquo;&lsaquo;</span>
</a>
EOF;

  } else {

    $older_missions_html = '<span class="arrows big">&lsaquo;&lsaquo;</span>';

  }

  $sql = 'select min(required_level) as min from quests
    where `group` = %d;';
  $result = db_query($sql, $group_to_show + 1);
  $item = db_fetch_object($result);
firep($item);

  if (!empty($item->min) && ($item->min <= $game_user->level + 1)) {

    $newer_group = $group_to_show + 1;
    $newer_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$newer_group">
  <span class="arrows big">&rsaquo;&rsaquo;&rsaquo;</span>
</a>
EOF;

  } else {

    $newer_missions_html = '<span class="arrows big">&rsaquo;&rsaquo;</span>';

  }

  echo <<< EOF
<div class="title">
  <span class="left">$older_missions_html</span>
  <span class="middle">Chapter $group_to_show:<br/>$location</span>
  <span class="right">$newer_missions_html</span>
</div>
EOF;

// abc123 -- show all quests
  $active_quests = ($phone_id == 'abc123') ? '' : 'and quests.active = 1';

// get quest group stats
  $sql = 'SELECT sum(bonus_given) as completed, count(quests.id) as total
    FROM `quests`
    left outer join quest_completion
    on quest_completion.fkey_quests_id = quests.id
    and fkey_users_id = %d
    where `group` = %d ' . $active_quests . ';';
  $result = db_query($sql, $game_user->id, $group_to_show);

  $quest_group = db_fetch_object($result);
firep($quest_group);

  $quest_group->completed += 0; // haha!  typecasting!

  $sql = 'SELECT times_completed FROM `quest_group_completion`
    where fkey_users_id = %d and fkey_quest_groups_id = %d;';
  $result = db_query($sql, $game_user->id, $group_to_show);
  $quest_group_completion = db_fetch_object($result);

  $percentage_target = 100;
  $percentage_divisor = 1;

  if ($quest_group_completion->times_completed > 0) {

    $next_group_html = t('(2nd round)');
    $percentage_target = 200;
    $percentage_divisor = 2;
    $quest_group->completed -=
      ($quest_group->total * min($quest_group_completion->times_completed, 1));

  }

  echo <<< EOF
<div class="quest-group-completion">
  <strong>$quest_group->completed</strong> of $quest_group->total $quests
  complete $next_group_html
</div>
EOF;

// show each quest
  $data = array();

  $sql = 'select fkey_quests_id from quest_completion
    where fkey_users_id = %d
    and percent_complete >= 100
    order by fkey_quests_id DESC
    limit 1;';
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result);
  $next_quest = $item->fkey_quests_id + 1;

  $sql = 'select quests.*, quest_completion.percent_complete from quests
    LEFT OUTER JOIN quest_completion
    ON quest_completion.fkey_quests_id = quests.id
    AND quest_completion.fkey_users_id = %d
    ' . $sql_quest_neighborhood .
    ' and quests.id <= %d ' . $active_quests .
    ' order by quests.id DESC;';
firep($sql);
  $result = db_query($sql, $game_user->id, $next_quest);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    _show_quest($game_user, $item, $percentage_target,
      $percentage_divisor, $quest_group, $party_title);

  }

  if (FALSE /*$game_user->level > 1*/) { // don't show extra quests at first

    $data = array();
    $sql = 'select * from quests ' . $sql_quest_neighborhood .
      ' and required_level = %d ' . $active_quests .
      ' order by required_level ASC;';
    $result = db_query($sql, $game_user->level + 1);

    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {


    }

  }

  db_set_active('default');
