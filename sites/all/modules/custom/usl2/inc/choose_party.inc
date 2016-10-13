<?php

/**
 * Allow the user to choose a party.
 *
 * @todo: implement game_header theme.
 * @todo: show message when users chooses same party.
 */
function game_choose_party_callback($game_user, $party_id) {

  if (!is_numeric($party_id) && $party_id != 'test') {
    return MENU_ACCESS_DENIED;
  }

  db_set_active('game');

  // Can't join party?
  $cant_join_party = game_get_value($game_user, 'cant_join_party',
    REQUEST_TIME);

  // Testing can't join party.
  if (arg(2) == 'abc123' && arg(3) == 'test') {
    $cant_join_party = REQUEST_TIME + mt_rand(1, 259200);
  }

  if ($cant_join_party > REQUEST_TIME) {
    db_set_active('default');

    return array(
      array(
        '#theme' => 'title_image',
      ),
      array(
        '#theme' => 'game_cant_yet',
        '#action' => 'join a @party',
        '#time' => $cant_join_party,
      ),
      array(
        '#theme' => 'game_button',
        '#link' => 'home',
      ),
    );
  }

  // No party chosen yet?  Show list of parties.
  if ($party_id == 0) {
    db_set_active('default');

    return array(
      array(
        '#theme' => 'game_header',
        '#game_user' => $game_user,
      ),
      array(
        '#theme' => 'game_choose_party',
        '#game_user' => $game_user,
      ),
    );
  }

  // No change to the party?  Just show stats.
  if ($party_id == $game_user->fkey_values_id) {
    game_goto('user');
  }

  // Changing parties!
  $query = db_select('values_table', 'v')
    ->condition('id', $party_id)
    ->fields('v')
    ->execute();
  $new_values = $query->fetch();

  if (empty($new_values)) {
    game_goto('error', 'E-8213'); // no such party
  }

  db_update('users')->fields(array(
      'fkey_neighborhoods_id' => $new_values->fkey_neighborhoods_id,
      'fkey_values_id' => $party_id,
    ))
    ->condition('id', $game_user->id)
    ->execute();

  if ($game_user->fkey_values_id == 0) {

    // Joining from Anarchists.
    if ($game_user->experience >= 1000) {
      competency_gain($game_user, 'conformist', 3);
    }
  }
  else {
    // Joining from other party.  Dock experience, reset skills and points.
    competency_gain($game_user, 'dissenter', 3);

    // Bring experience, level down
    $new_experience = max(floor($game_user->experience * 0.8), 75);

    $sql = 'SELECT max(level) as new_level from levels where experience <= :exp;';
    $result = db_query($sql, array(':exp' => $new_experience));
    $new_level = $result->fetchField();
  dpm($new_level, 'new level');

    $sql = 'SELECT count(quests.id) as bonus FROM `quest_group_completion`
      left outer join quests
      on quest_group_completion.fkey_quest_groups_id = quests.group
      WHERE fkey_users_id = :id and quests.active = 1;';
    $result = db_query($sql, array(':id' => $game_user->id));
    $bonus = $result->fetchField();
    $new_skill_points = ($new_level * 4) + $bonus - 20;
dpm($bonus, 'bonus skill points from quests');

    // Charge 5 Luck.
    $sql = 'update users set luck = luck - 5 where id = :id;';
    $result = db_query($sql, array(':id' => $game_user->id));

    db_update('users')->fields(array(
        'experience' => $new_experience,
        'level' => $new_level,
        'energy' => 200,
        'energy_max' => 200,
        'initiative' => 1,
        'endurance' => 1,
        'actions' => 3,
        'actions_max' => 3,
        'elocution' => 1,
        'skill_points' => $new_skill_points,
      ))
      ->condition('id', $game_user->id)
      ->execute();
  }


/*

// also delete any offices s/he held
    $sql = 'delete from elected_officials where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);

// and any clan memberships s/he had (disband the clan if s/he was the leader)
    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);

    if ($item->is_clan_leader) { // clan leader? delete entire clan

      $sql = 'delete from clan_messages where fkey_neighborhoods_id = %d;';
      $result = db_query($sql, $game_user->fkey_clans_id);
      $sql = 'delete from clan_members where fkey_clans_id = %d;';
      $result = db_query($sql, $item->fkey_clans_id);
      $sql = 'delete from clans where id = %d;';
      $result = db_query($sql, $item->fkey_clans_id);

    } else {

      $sql = 'delete from clan_members where fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);

    }

// add 24-hour waiting period on major actions
    $set_value($game_user->id, 'next_major_action', time() + 86400);

    if ($game_user->clan == 0) { // first time choosing?  go to debates
      db_set_active('default');
      drupal_goto($game . '/debates/' . $arg2);
    }

// otherwise keep him/her from challenging for a day
// and show his/her character profile
    $set_value($game_user->id, 'cant_challenge', time() + 86400);
    db_set_active('default');
    drupal_goto($game . '/user/' . $arg2);

  }

// otherwise they have not chosen a clan or are rechoosing one

    echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;So you wish to join a different $party_small_lower.&nbsp; You will
    not rank as highly in that $party_small_lower as you do in your current
    one, but that is your choice.</p>
  <p class="second">&quot;Which one do you prefer?&quot;</p>
</div>
<div class="choose-clan">
EOF;

  }

*/
  db_set_active('default');
  return 'hello';
}
