<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $get_value = '_' . arg(0) . '_get_value';

// can't join party?
  $cant_join_party_time = $get_value($game_user->id, 'cant_join_party');
  $cant_join_party_time_remaining = !empty($cant_join_party_time) ?
    (int)$cant_join_party_time - time() : NULL;

//    if ($phone_id == 'abc123')
//      $cant_join_party_time_remaining = mt_rand(0, 259200);

  if ($cant_join_party_time_remaining > 0) {

    $days_remaining = sprintf('%d',
      floor($cant_join_party_time_remaining / 86400));
    $cant_join_party_time_remaining %= 86400;
    $hours_remaining = sprintf('%02d',
      floor($cant_join_party_time_remaining / 3600));
    $minutes_remaining_in_sec = $cant_join_party_time_remaining % 3600;
    $minutes_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec / 60));
    $seconds_remaining = sprintf('%02d',
      floor($minutes_remaining_in_sec % 60));

    echo <<< EOF
<div class="title">You Can't Join a $party Yet!</div>
<div class="subtitle">
  Come back in $days_remaining day(s)
  $hours_remaining:$minutes_remaining:$seconds_remaining
</div>
<div class="subtitle">
  <a href="/$game/home/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    db_set_active('default');
    return;

  }

// if they have chosen a clan
  if ($clan_id != 0) {

    if ($clan_id == $game_user->fkey_values_id) { // no change?  just show stats
      db_set_active('default');
      drupal_goto($game . '/user/' . $arg2);
    }

// changing clans?  dock experience, bring level down to match

    competency_gain($game_user, 'dissenter', 3);

    $new_experience = floor($game_user->experience * 0.75);
    if ($new_experience < 75) $new_experience = 75;

    $sql = 'SELECT max(level) as new_level from levels where experience <= %d;';
    $result = db_query($sql, $new_experience);
    $item = db_fetch_object($result);
    $new_level = $item->new_level;

    $sql = 'SELECT count(quests.id) as bonus FROM `quest_group_completion`
      left outer join quests
      on quest_group_completion.fkey_quest_groups_id = quests.group
      WHERE fkey_users_id = %d and quests.active = 1;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result); // limited to 1 in db

    $new_skill_points = ($new_level * 4) + $item->bonus - 20;

    $sql = 'select * from `values` where id = %d;';
    $result = db_query($sql, $clan_id);
    $item = db_fetch_object($result);

// update his/her user entry
    $sql = 'update users set fkey_neighborhoods_id = %d, fkey_values_id = %d,
      `values` = "%s", level = %d, experience = %d, energy_max = 200,
      skill_points = %d, initiative = 1, endurance = 1, actions = 3,
      actions_max = 3, elocution = 1
      where id = %d;';
    $result = db_query($sql, $item->fkey_neighborhoods_id, $clan_id,
      $item->name, $new_level, $new_experience, $new_skill_points,
      $game_user->id);

    if ($game_user->fkey_values_id != 0) { // remove Luck if changing clans

      $sql = 'update users set luck = luck - 5 where id = %d;';
      $result = db_query($sql, $game_user->id);

    }

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
    $set_value = '_' . arg(0) . '_set_value';
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

  if ($game_user->level <= 6) { // new clan

  $elder = 'You are met by the city elder again';

  if ($game == 'celestial_glory')
    $elder = 'You see Lehi in a vision again';

    echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_large">
  </div>
  <p>$elder.&nbsp; &quot;Well done,&quot; he
    says.&nbsp; &quot;I am impressed by what you have learned.</p>
  <p class="second">&quot;In order to continue your journey, you will need a
    mentor.&nbsp; Your mentor will provide guidance and answer any questions
    that you may have.&nbsp; He or she should have provided you with a
    referral code.</p>
  <p class="second">&quot;Alternatively, you can continue on your own without a
    code.&nbsp; Which do you prefer?&quot;</p>
</div>
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/enter_referral_code/$arg2">I have a referral code</a>
  </div>
</div>
<div class="choose-clan">
<div class="subtitle">If you don't have a referral code, you may<br/>
  instead choose a $party_small_lower:</div>
<br/>
EOF;

  } else {

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

  $sql = 'SELECT COUNT( users.id ) AS count,  `values` . *
    FROM  `users`
    LEFT JOIN  `values` ON users.fkey_values_id =  `values`.id
    where `values`.user_selectable = 1
    GROUP BY fkey_values_id
    ORDER BY count ASC;';
  $result = db_query($sql);
  $data = array();

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
    $value = strtolower($item->name);
    $icon = $game . '_clan_' . $item->clan_icon . '.png';

    echo <<< EOF
<div>
  <div class="choose-clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <span class="choose-clan-name"><a
  href="/$game/choose_clan/$arg2/$item->id">$item->clan_title</a></span>
  value $value</div>
  <div class="choose-clan-slogan">$item->slogan</div>
EOF;

  }

  echo '</div>';

  db_set_active('default');
