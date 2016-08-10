<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $arg2 = check_plain(arg(2));

  $sql = 'SELECT count(quests.id) as bonus FROM `quest_group_completion`
    left outer join quests
    on quest_group_completion.fkey_quest_groups_id = quests.group
    WHERE fkey_users_id = %d and quests.active = 1;';
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result); // limited to 1 in db

  $skill_points = ($game_user->level * 4) + $item->bonus - 20;

  if ($game_user->skill_points == $skill_points) {

    db_set_active('default');
    drupal_goto($game . '/user/' . $arg2);

  }

  competency_gain($game_user, 'fickle', 2);

// update his/her user entry
  $sql = 'update users set energy_max = 200,
    skill_points = %d, initiative = 1, endurance = 1, actions = 3,
    actions_max = 3, elocution = 1, luck = luck - 3
    where id = %d;';
  $result = db_query($sql, $skill_points, $game_user->id);

  db_set_active('default');
  drupal_goto($game . '/user/' . $arg2);
