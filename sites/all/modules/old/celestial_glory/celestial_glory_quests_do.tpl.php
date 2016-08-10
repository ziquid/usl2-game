<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include (drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $sql = 'select name from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;

  $sql = 'select clan_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $clan_title = preg_replace('/^The /', '', $data->clan_title);

  $data = array();
  $sql = 'select quests.*, neighborhoods.name as hood from quests
    LEFT OUTER JOIN neighborhoods
    ON quests.fkey_neighborhoods_id = neighborhoods.id
    where quests.id = %d;';
  $result = db_query($sql, $quest_id);
  $game_quest = db_fetch_object($result); // limited to 1 in DB
//firep($game_quest);

  if ($event_type == EVENT_QUESTS_100)
    $game_quest->required_energy = min($game_quest->required_energy, 100);

  $quest_succeeded = TRUE;
  $outcome_reason = '<div class="quest-succeeded">' . t('Success!') .
    '</div>';
  $ai_output = 'quest-succeeded';

// check to see if quest prerequisites are met
  if (($game_user->energy < $game_quest->required_energy) &&
    ($game_user->level >= 6)) { // unlimited quests below level 6

    $quest_succeeded = FALSE;
    $outcome_reason = '<div class="quest-failed">' . t('Not enough Energy!') .
      '</div><div class="try-an-election-wrapper">
      <div class="try-an-election"><a
      href="/' . $game . '/elders_do_fill/' . $arg2 . '/energy?destination=/' .
      $game . '/quests/' . $arg2 . '/' . $game_quest->group . '">Refill
      your Energy (1&nbsp;' . $luck . ')</a></div></div>';
    $extra_html = '<p>&nbsp;</p><p class="second">&nbsp;</p>';
    $ai_output = 'quest-failed not-enough-energy';

    competency_gain($game_user, 'too tired');

  } // not enough energy

  if ($game_quest->equipment_1_required_quantity > 0) {

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $game_quest->fkey_equipment_1_required_id,
      $game_user->id);
    $quantity = db_fetch_object($result);

    if ($quantity->quantity < $game_quest->equipment_1_required_quantity) {

      $quest_succeeded = FALSE;
      $outcome_reason = '<div class="quest-failed">' . t('Failed!') .
        '</div><div class="quest-required_stuff missing centered">Missing
        <div class="quest-required_equipment"><a href="/' . $game .
        '/equipment_buy/' .
        $arg2 . '/' . $game_quest->fkey_equipment_1_required_id . '/' .
        ($game_quest->equipment_1_required_quantity - $quantity->quantity) .
        '"><img src="/sites/default/files/images/equipment/' .
        $game . '-' . $game_quest->fkey_equipment_1_required_id . '.png"
        width="48"></a></div>&nbsp;x' .
        $game_quest->equipment_1_required_quantity .
        '</div>';
      $ai_output = 'quest-failed need-equipment-' .
        $game_quest->fkey_equipment_1_required_id;

      competency_gain($game_user, 'hole in pockets');
    }

  } // no required equipment_1

  if ($game_quest->equipment_2_required_quantity > 0) {

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $game_quest->fkey_equipment_2_required_id,
      $game_user->id);
    $quantity = db_fetch_object($result);

    if ($quantity->quantity < $game_quest->equipment_2_required_quantity) {

      $quest_succeeded = FALSE;
      $outcome_reason = '<div class="quest-failed">' . t('Failed!') .
        '</div><div class="quest-required_stuff missing centered">Missing
        <div class="quest-required_equipment"><a href="/' . $game . '/equipment_buy/' .
        $arg2 . '/' . $game_quest->fkey_equipment_2_required_id . '/' .
        ($game_quest->equipment_2_required_quantity - $quantity->quantity) . '"><img
        src="/sites/default/files/images/equipment/' .
        $game . '-' . $game_quest->fkey_equipment_2_required_id . '.png"
        width="48"></a></div>&nbsp;x' . $game_quest->equipment_2_required_quantity .
        '</div>';
      $ai_output = 'quest-failed need-equipment-' .
        $game_quest->fkey_equipment_2_required_id;

      competency_gain($game_user, 'hole in pockets');

    }

  } // no required equipment_2

    if ($game_quest->equipment_3_required_quantity > 0) {

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $game_quest->fkey_equipment_3_required_id,
      $game_user->id);
    $quantity = db_fetch_object($result);

    if ($quantity->quantity < $game_quest->equipment_3_required_quantity) {

      $quest_succeeded = FALSE;
      $outcome_reason = '<div class="quest-failed">' . t('Failed!') .
        '</div><div class="quest-required_stuff missing centered">Missing
        <div class="quest-required_equipment"><a href="/' . $game . '/equipment_buy/' .
        $arg2 . '/' . $game_quest->fkey_equipment_3_required_id . '/' .
        ($game_quest->equipment_3_required_quantity - $quantity->quantity) . '"><img
        src="/sites/default/files/images/equipment/' .
        $game . '-' . $game_quest->fkey_equipment_3_required_id . '.png"
        width="48"></a></div>&nbsp;x' . $game_quest->equipment_3_required_quantity .
        '</div>';
      $ai_output = 'quest-failed need-equipment-' .
        $game_quest->fkey_equipment_3_required_id;

      competency_gain($game_user, 'hole in pockets');

    }

  } // no required equipment_3

  if ($game_quest->staff_required_quantity > 0) {

    $sql = 'select quantity from staff_ownership
      where fkey_staff_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $game_quest->fkey_staff_required_id,
      $game_user->id);
    $quantity = db_fetch_object($result);

    if ($quantity->quantity < $game_quest->staff_required_quantity) {

      $quest_succeeded = FALSE;
      $outcome_reason = '<div class="quest-failed">' . t('Failed!') .
        '</div><div class="quest-required_stuff missing centered">Missing
        <div class="quest-required_equipment"><img
        src="/sites/default/files/images/staff/' .
        $game . '-' . $game_quest->fkey_staff_required_id . '.png"
        width="48"></div>&nbsp;x' . $game_quest->staff_required_quantity .
        '</div>';
      $ai_output = 'quest-failed need-staff-' .
        $game_quest->fkey_staff_required_id;

      competency_gain($game_user, 'friendless');

    }

  } // no required staff

    if ($game_quest->land_required_quantity > 0) {

    $sql = 'select quantity from land_ownership
      where fkey_land_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $game_quest->fkey_land_required_id,
      $game_user->id);
    $quantity = db_fetch_object($result);

    if ($quantity->quantity < $game_quest->land_required_quantity) {

      $quest_succeeded = FALSE;
      $outcome_reason = '<div class="quest-failed">' . t('Failed!') .
        '</div><div class="quest-required_stuff missing centered">Missing
        <div class="quest-required_equipment"><a href="/' . $game . '/land_buy/' .
        $arg2 . '/' . $game_quest->fkey_land_required_id . '/' .
        ($game_quest->land_required_quantity - $quantity->quantity) . '"><img
        src="/sites/default/files/images/land/' .
        $game . '-' . $game_quest->fkey_land_required_id . '.png"
        width="48"></a></div>&nbsp;x' . $game_quest->land_required_quantity .
        '</div>';
      $ai_output = 'quest-failed need-land-' .
        $game_quest->fkey_land_required_id;

      competency_gain($game_user, 'homeless');

    }

  } // no required land


// wrong hood
  if (($game_quest->group > 0) && ($game_quest->fkey_neighborhoods_id != 0) &&
    ($game_quest->fkey_neighborhoods_id != $game_user->fkey_neighborhoods_id)) {

    $quest_succeeded = FALSE;
    $outcome_reason = '<div class="quest-failed">'
    . t('Wrong @hood!', array('@hood' => $hood_lower))
    . '</div>
        <p>This ' . $quest_lower . ' can only be completed in '
        . $game_quest->hood . '.
        </p>
      <div class="try-an-election-wrapper">
        <div class="try-an-election">
          <a href="/' . $game . '/move/' . $arg2 . '/'
          . $game_quest->fkey_neighborhoods_id . '">
            Go there
          </a>
        </div>
      </div>';
    $extra_html = '<p>&nbsp;</p><p class="second">&nbsp;</p>';
    $ai_output = 'quest-failed wrong-hood';

    competency_gain($game_user, 'lost');

  } // wrong hood


  $sql = 'select percent_complete, bonus_given from quest_completion
    where fkey_users_id = %d and fkey_quests_id = %d;';
  $result = db_query($sql, $game_user->id, $quest_id);
  $pc = db_fetch_object($result);
//firep($pc);

// get quest completion stats
  $sql = 'SELECT times_completed FROM `quest_group_completion`
      where fkey_users_id = %d and fkey_quest_groups_id = %d;';
    $result = db_query($sql, $game_user->id, $game_quest->group);
    $quest_group_completion = db_fetch_object($result);
//firep($quest_group_completion);

    $percentage_target = 100;
    $percentage_divisor = 1;

    if ($quest_group_completion->times_completed > 0) {

      $percentage_target = 200;
      $percentage_divisor = 2;

    }

  if ($quest_succeeded) {

    competency_gain($game_user, 'quester');

    $old_energy = $game_user->energy;
    $game_user->energy -= $game_quest->required_energy;
    $game_user->experience += $game_quest->experience;
    $money_added += mt_rand($game_quest->min_money, $game_quest->max_money);
    $game_user->money += $money_added;

    if ($game_quest->group > 1000) { // don't save quests group

      $sql = 'update users set energy = energy - %d,
        experience = experience + %d, money = money + %d
        where id = %d;';
      $result = db_query($sql, $game_quest->required_energy,
        $game_quest->experience, $money_added, $game_user->id);

    } else { // save all

      $sql = 'update users set energy = energy - %d,
        experience = experience + %d, money = money + %d,
        fkey_last_played_quest_groups_id = %d
        where id = %d;';
      $result = db_query($sql, $game_quest->required_energy,
        $game_quest->experience, $money_added, $game_quest->group,
        $game_user->id);

    }

    if ($old_energy == $game_user->energy_max) { // start the energy clock again

      $sql = 'update users set energy_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + $energy_wait), $game_user->id);

    }

// update percentage completion

    if (empty($pc->percent_complete)) { // no entry yet, add one

      $sql = 'insert into quest_completion (fkey_users_id, fkey_quests_id,
        percent_complete) values (%d, %d, %d);';
      $result = db_query($sql, $game_user->id, $quest_id,
       $game_quest->percent_complete);

    } else {

      $sql = 'update quest_completion set percent_complete = least(
        percent_complete + %d, %d) where fkey_users_id = %d and
        fkey_quests_id = %d;';
      $result = db_query($sql,
        floor($game_quest->percent_complete / $percentage_divisor),
        $percentage_target, $game_user->id, $quest_id);

    }

    $percent_complete = min($pc->percent_complete +
      floor($game_quest->percent_complete / $percentage_divisor),
      $percentage_target);

// if they have completed the quest for the first time in a round, give them a bonus
    if ($percent_complete == $percentage_target) {

      if ($pc->bonus_given < $percentage_divisor) {

        competency_gain($game_user, 'quest finisher');

        $game_user->experience += $game_quest->experience;
        $game_user->money += $money_added;

        $sql = 'update users set experience = experience + %d, money = money + %d
          where id = %d;';
        $result = db_query($sql, $game_quest->experience, $money_added,
          $game_user->id);

        $sql = 'update quest_completion set bonus_given = bonus_given + 1
          where fkey_users_id = %d and fkey_quests_id = %d;';
        $result = db_query($sql, $game_user->id, $quest_id);

        $quest_completion_html =<<< EOF
  <div class="title loot">$quest Completed!</div>
  <p>You have completed this $quest_lower and gained an extra $money_added
    $game_user->values and $game_quest->experience $experience!&nbsp; Complete
    all ${quest_lower}s in this group for an extra reward.</p>
EOF;

      } // did they get the mission completion bonus?

// did they complete all quests in the group?

      $sql = 'select * from quest_group_completion
        where fkey_users_id = %d and fkey_quest_groups_id = %d;';
      $result = db_query($sql, $game_user->id, $game_quest->group);
      $qgc = db_fetch_object($result);
//firep($qgc);

      if (empty($qgc) || $qgc->times_completed == 0) {
// if no quest_group bonus has been given

// get quest group stats
        $sql = 'SELECT sum( bonus_given ) AS completed,
          count( quests.id ) AS total, quest_groups.ready_for_bonus
          FROM `quests`
          LEFT OUTER JOIN quest_completion
          ON quest_completion.fkey_quests_id = quests.id
          AND fkey_users_id = %d
          LEFT JOIN quest_groups
          ON quests.group = quest_groups.id
          WHERE `group` = %d
          AND quests.active =1';
        $result = db_query($sql, $game_user->id, $game_quest->group);
        $quest_group = db_fetch_object($result);
//firep($quest_group);

        if (($quest_group->completed == $quest_group->total) &&
          ($quest_group->ready_for_bonus == 1)) {
// woohoo!  user just completed an entire group!

          $quest_completion_html .=<<< EOF
<div class="title loot">Congratulations!</div>
<p>You have completed all {$quest_lower}s in this group and have gained extra skill
  points!</p>
<p class="second"><a href="/$game/increase_skills/$arg2/none">You
  have <span class="highlighted">$quest_group->completed</span> new skill points
  to spend</a></p>
EOF;
          competency_gain($game_user, 'quest groupie', 3);

// update user stats
          $sql = 'update users set skill_points = skill_points + %d
            where id = %d;';
          $result = db_query($sql, $quest_group->completed, $game_user->id);

// update quest_groups_completion
          if (empty($qgc)) { // no record exists - insert one

            $sql = 'insert into quest_group_completion (fkey_users_id,
              fkey_quest_groups_id, times_completed) values (%d, %d, 1);';
            $result = db_query($sql, $game_user->id, $game_quest->group);

          } else { // existing record - update it

            $sql = 'update quest_group_completion set times_completed = 1
              where fkey_users_id = %d and fkey_quest_groups_id = %d;';
            $result = db_query($sql, $game_user->id, $game_quest->group);

          } // insert or update the qgc record

          $quest_group_completion->times_completed = 1;
          $percentage_target = 200;
          $percentage_divisor = 2;

        } // if quest group completed

      } // if no quest_group bonus has been given

      if ($qgc->times_completed == 1) { // what?  they've completed a 2nd time?

// get quest group stats
        $sql = 'SELECT sum( bonus_given ) AS completed,
          count( quests.id ) AS total, quest_groups.ready_for_bonus
          FROM `quests`
          LEFT OUTER JOIN quest_completion
          ON quest_completion.fkey_quests_id = quests.id
          AND fkey_users_id = %d
          LEFT JOIN quest_groups
          ON quests.group = quest_groups.id
          WHERE `group` = %d
          AND quests.active =1';
        $result = db_query($sql, $game_user->id, $game_quest->group);
        $quest_group = db_fetch_object($result);
//firep($quest_group);

        if ($quest_group->completed == ($quest_group->total * 2)) {
// woohoo!  user just completed an entire group the second time!

          competency_gain($game_user, 'second-mile saint', 3);

          $sql = 'select * from quest_group_bonus
            where fkey_quest_groups_id = %d;';
          $result = db_query($sql, $game_quest->group);
          $item = db_fetch_object($result); // limited to 1 in db
//firep($item);
          $eq_id = $item->fkey_equipment_id;
          $land_id = $item->fkey_land_id;
          $st_id = $item->fkey_staff_id;

          if (($eq_id + $land_id + $st_id) > 0) {
// anything to give him/her?

            if ($eq_id > 0) { // equipment bonus

              $data = array();
              $sql = 'SELECT equipment.*, equipment_ownership.quantity
                FROM equipment

                LEFT OUTER JOIN equipment_ownership
                ON equipment_ownership.fkey_equipment_id = equipment.id
                AND equipment_ownership.fkey_users_id = %d

                WHERE equipment.id = %d;';
              $result = db_query($sql, $game_user->id, $eq_id);
              $game_equipment = db_fetch_object($result); // limited to 1 in DB

// give the stuff
              if ($game_equipment->quantity == '') { // no record exists - insert one

                $sql = 'insert into equipment_ownership
                  (fkey_equipment_id, fkey_users_id, quantity)
                  values (%d, %d, %d);';
                $result = db_query($sql, $eq_id, $game_user->id, 1);

              } else { // existing record - update it

                $sql = 'update equipment_ownership set quantity = quantity + 1
                  where fkey_equipment_id = %d and fkey_users_id = %d;';
                $result = db_query($sql, $eq_id, $game_user->id);

              } // insert or update record

// tell the user about it
              $quest_completion_html .=<<< EOF
<div class="quest-succeeded title loot">Congratulations!</div>
<div class="subsubtitle">You have completed the second round of {$quest_lower}s!</div>
<div class="subsubtitle">Here is your bonus:</div>
<div class="quest-icon"><img
 src="/sites/default/files/images/equipment/$game-{$eq_id}.png" width="96"></div>
<div class="quest-details">
  <div class="quest-name loot">$game_equipment->name</div>
  <div class="quest-description">$game_equipment->description</div>
EOF;

              if ($game_equipment->energy_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Energy: +$game_equipment->energy_bonus immediate energy bonus
      </div>
EOF;

              } // energy bonus?

              if ($game_equipment->energy_increase > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Energy: +$game_equipment->energy_increase every $energy_wait_str
      </div>
EOF;

              } // energy increase?

              if ($game_equipment->initiative_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">$initiative: +$game_equipment->initiative_bonus
      </div>
EOF;

              } // initiative bonus?

              if ($game_equipment->endurance_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Endurance: +$game_equipment->endurance_bonus
      </div>
EOF;

              } // endurance bonus?

              if ($game_equipment->elocution_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">$elocution: +$game_equipment->elocution_bonus
      </div>
EOF;

              } // elocution bonus?

              if ($game_equipment->speed_increase > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Speed Increase: $game_equipment->speed_increase fewer Action
      needed to move to a new $hood_lower
      </div>
EOF;

              } // speed increase?

              if ($game_equipment->upkeep > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout negative">Upkeep: $game_equipment->upkeep every 60 minutes</div>
EOF;

              } // upkeep

              if ($game_equipment->chance_of_loss > 0) {

                $lifetime = floor(100 / $game_equipment->chance_of_loss);
                $use = ($lifetime == 1) ? 'use' : 'uses';
                $quest_completion_html .=<<< EOF
    <div class="quest-payout negative">Expected Lifetime: $lifetime $use</div>
EOF;

              } // expected lifetime

              $quest_completion_html .= '</div>';

            } // equipment bonus

            // FIXME: land bonus here


            if ($st_id > 0) { // staff bonus

              $data = array();
              $sql = 'SELECT staff.*, staff_ownership.quantity
                FROM staff

                LEFT OUTER JOIN staff_ownership
                ON staff_ownership.fkey_staff_id = staff.id
                AND staff_ownership.fkey_users_id = %d

                WHERE staff.id = %d;';
              $result = db_query($sql, $game_user->id, $st_id);
              $game_staff = db_fetch_object($result); // limited to 1 in DB

// give the stuff
              if ($game_staff->quantity == '') { // no record exists - insert one

                $sql = 'insert into staff_ownership
                  (fkey_staff_id, fkey_users_id, quantity)
                  values (%d, %d, %d);';
                $result = db_query($sql, $st_id, $game_user->id, 1);

              } else { // existing record - update it

                $sql = 'update staff_ownership set quantity = 1 where
                  fkey_staff_id = %d and fkey_users_id = %d;';
                $result = db_query($sql, $st_id, $game_user->id);

              } // insert or update record

// tell the user about it
              $quest_completion_html .=<<< EOF
<div class="quest-succeeded title loot">Congratulations!</div>
<div class="subsubtitle">You have completed the second round of {$quest_lower}s!</div>
<div class="subsubtitle">Here is your bonus:</div>
<div class="quest-icon"><img
 src="/sites/default/files/images/staff/$game-{$st_id}.png" width="96"></div>
<div class="quest-details">
  <div class="quest-name loot">$game_staff->name</div>
  <div class="quest-description">$game_staff->description</div>
EOF;

              if ($game_staff->energy_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Energy: +$game_staff->energy_bonus immediate energy bonus
      </div>
EOF;

              } // energy bonus?

              if ($game_staff->energy_increase > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Energy: +$game_staff->energy_increase every $energy_wait_str
      </div>
EOF;

              } // energy increase?

              if ($game_staff->initiative_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">$initiative: +$game_staff->initiative_bonus
      </div>
EOF;

              } // initiative bonus?

              if ($game_staff->endurance_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">$endurance: +$game_staff->endurance_bonus
      </div>
EOF;

              } // endurance bonus?

              if ($game_staff->elocution_bonus > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">$elocution: +$game_staff->elocution_bonus
      </div>
EOF;

              } // elocution bonus?

              if ($game_staff->speed_increase > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Speed Increase: $game_staff->speed_increase fewer Action
      needed to move to a new $hood_lower
      </div>
EOF;

              } // speed increase?

              if ($game_staff->extra_votes > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Extra Votes: $game_staff->extra_votes</div>
EOF;

              } // extra votes

              if ($game_staff->extra_defending_votes > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout">Extra Defending Votes: $game_staff->extra_defending_votes</div>
EOF;

              } // extra defending votes

              if ($game_staff->upkeep > 0) {

                $quest_completion_html .=<<< EOF
    <div class="quest-payout negative">Upkeep: $game_staff->upkeep every 60 minutes</div>
EOF;

              } // upkeep

              if ($game_staff->chance_of_loss > 0) {

                $lifetime = floor(100 / $game_staff->chance_of_loss);
                $use = ($lifetime == 1) ? 'use' : 'uses';
                $quest_completion_html .=<<< EOF
    <div class="quest-payout negative">Expected Lifetime: $lifetime $use</div>
EOF;

              } // expected lifetime

              $quest_completion_html .= '</div>';

            } // staff bonus

// update quest_groups_completion
          $sql = 'update quest_group_completion set times_completed = 2
            where fkey_users_id = %d and fkey_quest_groups_id = %d;';
          $result = db_query($sql, $game_user->id, $game_quest->group);

//          $quest_group_completion->times_completed = 1;
//          $percentage_target = 200;
//          $percentage_divisor = 2;

          } else { // we don't have a bonus yet

            $quest_completion_html .=<<< EOF
<div class="title loot">Congratulations!</div>
<div class="quest-icon"><img
 src="/sites/default/files/images/quests/stlouis-soon.png"></div>
<div class="quest-details">
  <div class="quest-name loot">You have completed all {$quest_lower}s in
    this group a second time!</div>
  <div class="quest-description">Unfortunately, we have nothing to give you
    yet!&nbsp; We're still coding it!</div>
  <p class="second">&nbsp;</p>
</div>
EOF;
          } // if we actually have a bonus to give

        } // if quest group completed

      } // if one quest_group bonus has been given

    } // if quest completed

    if ($percent_complete > floor($percentage_target / 2)) {

      $rgb = dechex(floor(($percentage_target - $percent_complete) /
        (4 * $percentage_divisor))) . 'c0';

    } else {

      $rgb = 'c' . dechex(floor(($percent_complete) /
        (4 * $percentage_divisor))) . '0';

    }

    $width = floor($percent_complete * 94 / $percentage_target) + 2;

//firep($rgb);
//firep($width);

// check for loot - equipment

    $sql = 'SELECT equipment.quantity_limit, equipment_ownership.quantity
      FROM equipment

      LEFT OUTER JOIN equipment_ownership
      ON equipment_ownership.fkey_equipment_id = equipment.id
      AND equipment_ownership.fkey_users_id = %d

      WHERE equipment.id = %d;';
    $result = db_query($sql, $game_user->id,
      $game_quest->fkey_loot_equipment_id);
    $game_equipment = db_fetch_object($result); // limited to 1 in DB

    $limit = $game_equipment->quantity_limit > (int) $game_equipment->quantity;

    if ($game_quest->chance_of_loot >= mt_rand(1,100) &&
    ($limit || $game_equipment->quantity_limit == 0)) {

      $sql = 'select * from equipment where id = %d;';
      $result = db_query($sql, $game_quest->fkey_loot_equipment_id);
      $loot = db_fetch_object($result);

      $cumulative_expenses = $game_user->expenses + $loot->upkeep;
      if((int)$game_user->income >= $cumulative_expenses) {
        $game_user->expenses = $cumulative_expenses;
        $sql = 'UPDATE users SET expenses = %d WHERE id = %d';
        $result = db_query($sql, $game_user->expenses, $game_user->id);

        $loot_html =<<< EOF
  <div class="title loot">You Found</div>
  <div class="quest-icon"><img
   src="/sites/default/files/images/equipment/$game-$loot->id.png" width="96"></div>
  <div class="quest-details">
    <div class="quest-name loot">$loot->name</div>
    <div class="quest-description">$loot->description &nbsp;</div>
EOF;

        if ($loot->initiative_bonus > 0) {

          $loot_html .=<<< EOF
      <div class="quest-payout">$initiative: +$loot->initiative_bonus
        </div>
EOF;

        } // initiative bonus?

        if ($loot->endurance_bonus > 0) {

          $loot_html .=<<< EOF
    <div class="quest-payout">$endurance: +$loot->endurance_bonus
      </div>
EOF;

        } // endurance bonus?

        if ($loot->elocution_bonus > 0) {

          $loot_html .=<<< EOF
      <div class="quest-payout">$elocution: +$loot->elocution_bonus
        </div>
EOF;

        } // elocution bonus?

        $loot_html .=<<< EOF
      <p class="second">&nbsp;</p>
    </div>
EOF;

// add/update db entry

        competency_gain($game_user, 'looter');

        if ($game_quest->fkey_loot_equipment_id == 51 &&
          $event_type == EVENT_GATHER_AMETHYST) {

          $sql = 'update users set meta_int = meta_int + 1
            where id = %d;';
          $result = db_query($sql, $game_user->id);

        }

        $sql = 'SELECT equipment.*, equipment_ownership.quantity
          FROM equipment

          LEFT OUTER JOIN equipment_ownership
          ON equipment_ownership.fkey_equipment_id = equipment.id
          AND equipment_ownership.fkey_users_id = %d

          WHERE equipment.id = %d;';
        $result = db_query($sql, $game_user->id,
          $game_quest->fkey_loot_equipment_id);
        $game_equipment = db_fetch_object($result); // limited to 1 in DB

        if ($game_equipment->quantity == '') { // no record exists - insert one

          $sql = 'insert into equipment_ownership (fkey_equipment_id,
            fkey_users_id, quantity) values (%d, %d, 1);';
          $result = db_query($sql, $game_quest->fkey_loot_equipment_id,
            $game_user->id);

        } else { // existing record - update it

          $sql = 'update equipment_ownership set quantity = quantity + 1 where
            fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $game_quest->fkey_loot_equipment_id,
            $game_user->id);

        } // add/update db entry

      } // check for income < expenses after loot

    } // check for loot - equipment

    // check for loot - staff

    $sql = 'SELECT staff.quantity_limit,staff_ownership.quantity
      FROM staff

      LEFT OUTER JOIN staff_ownership
      ON staff_ownership.fkey_staff_id = staff.id
      AND staff_ownership.fkey_users_id = %d

      WHERE staff.id = %d;';
    $result = db_query($sql, $game_user->id,
      $game_quest->fkey_loot_staff_id);
    $game_staff = db_fetch_object($result); // limited to 1 in DB

    $limit = $game_staff->quantity_limit > (int) $game_staff->quantity;

    if ($game_quest->chance_of_loot_staff >= mt_rand(1,100) &&
    ($limit || $game_staff->quantity_limit == 0)) {

      $sql = 'select * from staff where id = %d;';
      $result = db_query($sql, $game_quest->fkey_loot_staff_id);
      $loot = db_fetch_object($result);

      $loot_html .=<<< EOF
  <div class="title loot">You Found</div>
  <div class="quest-icon"><img
   src="/sites/default/files/images/staff/$game-$loot->id.png" width="96"></div>
  <div class="quest-details">
    <div class="quest-name loot">$loot->name</div>
    <div class="quest-description">$loot->description &nbsp;</div>
    <p class="second">&nbsp;</p>
  </div>
EOF;

// add/update db entry

      $sql = 'SELECT staff.*, staff_ownership.quantity
        FROM staff

        LEFT OUTER JOIN staff_ownership
        ON staff_ownership.fkey_staff_id = staff.id
        AND staff_ownership.fkey_users_id = %d

        WHERE staff.id = %d;';
      $result = db_query($sql, $game_user->id,
        $game_quest->fkey_loot_staff_id);
      $game_staff = db_fetch_object($result); // limited to 1 in DB

      if ($game_staff->quantity == '') { // no record exists - insert one

        $sql = 'insert into staff_ownership (fkey_staff_id,
          fkey_users_id, quantity) values (%d, %d, 1);';
        $result = db_query($sql, $game_quest->fkey_loot_staff_id,
          $game_user->id);

      } else { // existing record - update it

        $sql = 'update staff_ownership set quantity = quantity + 1 where
          fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $game_quest->fkey_loot_staff_id,
          $game_user->id);

      } // add/update db entry

    } // check for loot - staff

    $game_user = $fetch_user();
    $fetch_header($game_user);

    if ($game_user->level >= 6) { // show quests menu after level 6

      if ($game_quest->group >= 1000) {
        $merch_active = '';
        $lehite_active = '';
      } elseif ($game_quest->group >= 100) {
        $merch_active = 'active';
        $lehite_active = '';
      } else {
        $merch_active = '';
        $lehite_active = 'active';
      }

      $sql = 'select quantity from `equipment_ownership` where
        fkey_equipment_id = 36 and fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);
      $data = db_fetch_object($result);

      if ($game_user->fkey_values_id == 5 || $data->quantity > 0) {
        $merch_url = '/' . $game . '/quests/' . $arg2 . '/100';
      } else {
        $merch_url = '#';
      }

      echo <<< EOF
<div class="news">
  <a href="/$game/quests/$arg2/0" class="button $lehite_active">Lehites</a>
  <a href="$merch_url"
    class="button $merch_active">Merchants</a>
</div>
EOF;

    }

    if ($game_user->level < 6 and $game_user->experience > 0) {

    echo <<< EOF
<ul>
  <li>Each $quest_lower gives you more $game_user->values and $experience</li>
  <li>Wait and rest for a few minutes if you run out of Energy</li>
</ul>
EOF;

     }

    $description = str_replace('%clan', "<em>$clan_title</em>",
      $game_quest->description);

    echo <<< EOF
<div class="quests">
$outcome_reason
  <div class="quest-icon"><a
    href="/$game/quests_do/$arg2/$game_quest->id"><img
    src="/sites/default/files/images/quests/$game-$game_quest->id.png" width="96"
    border="0"></a><div class="quest-complete"><div class="quest-complete-percentage"
      style="background-color: #$rgb; width: {$width}px">&nbsp;</div>
      <div class="quest-complete-text">$percent_complete%
        complete</div></div></div>
  <div class="quest-details">
    <div class="quest-name"><a
      href="/$game/quests_do/$arg2/$game_quest->id">$game_quest->name</a></div>
    <div class="quest-description">$description</div>
    <div class="quest-experience">You gained <strong>$money_added
      $game_user->values</strong></div>
    <div class="quest-money">You gained <strong>$game_quest->experience
      $experience</strong></div>
      </div>
    $loot_html
    $quest_completion_html
  <div class="quest-do-again">
    <div class="quest-do-again-inside"><a
      href="/$game/quests_do/$arg2/$game_quest->id">Do
      Again</a></div>
  </div>
</div>
EOF;

  } else { // failed!

        $fetch_header($game_user);

    if ($game_user->level < 6 and $game_user->experience > 0) {

      echo <<< EOF
<ul>
  <li>Each $quest_lower gives you more $game_user->values and $experience</li>
  <li>Wait and rest for a few minutes if you run out of Energy</li>
</ul>
EOF;

    }

    $sql = 'SELECT times_completed FROM `quest_group_completion`
      where fkey_users_id = %d and fkey_quest_groups_id = %d;';
    $result = db_query($sql, $game_user->id, $game_quest->group);
    $quest_group_completion = db_fetch_object($result);

    $percentage_target = 100;
    $percentage_divisor = 1;

    if ($quest_group_completion->times_completed > 0) {

      $percentage_target = 200;
      $percentage_divisor = 2;

    }

    $percent_complete = $pc->percent_complete + 0;

    if ($percent_complete > floor($percentage_target / 2)) {

      $rgb = dechex(floor(($percentage_target - $percent_complete) /
        (4 * $percentage_divisor))) . 'c0';

    } else {

      $rgb = 'c' . dechex(floor(($percent_complete) /
        (4 * $percentage_divisor))) . '0';

    }

    $width = floor($percent_complete * 94 / $percentage_target) + 2;

    echo <<< EOF
<div class="quests">
  $outcome_reason
  <div class="quest-icon"><a
    href="/$game/quests_do/$arg2/$game_quest->id"><img
    src="/sites/default/files/images/quests/$game-$game_quest->id.png"
    border="0" width="96"></a>
    <div class="quest-complete"><div class="quest-complete-percentage"
      style="background-color: #$rgb; width: {$width}px">&nbsp;</div>
      <div class="quest-complete-text">$percent_complete%
        complete</div></div></div>
  <div class="quest-details">
    <div class="quest-name"><a
      href="/$game/quests_do/$arg2/$game_quest->id">$game_quest->name</a></div>
    <div class="quest-experience">+$game_quest->experience $experience,
    +$game_quest->min_money to $game_quest->max_money $game_user->values</div>
    <div class="quest-required_energy">Requires $game_quest->required_energy
      Energy</div>
    $extra_html
    <br/><br/>
  </div>
</div>
EOF;

  } // quest succeeded or failed

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";

  $sql = 'select name from quest_groups where id = %s;';
  $result = db_query($sql, $game_quest->group);
  $qg = db_fetch_object($result);
firep($qg);

  $location = str_replace('%location', $location, $qg->name);

  if ($game_user->level < 6) { // show beginning quests, keep location from user

    $location = $older_missions_html = $newer_missions_html = '';
    $sql_quest_neighborhood = 'where fkey_neighborhoods_id = 0';

  } else { // show location-specific quests

    $sql_quest_neighborhood = 'where ((fkey_neighborhoods_id = 0 and
      required_level >= 6) or fkey_neighborhoods_id = ' .
      $game_user->fkey_neighborhoods_id . ')';

  }

  $sql = 'select name from quest_groups where id = %s;';
  $result = db_query($sql, $game_quest->group - 1);
  $qgo = db_fetch_object($result);

  if (!empty($qgo->name)) {

    $older_group = $game_quest->group - 1;
    $older_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$older_group">&lt;&lt;</a>
EOF;

  }

  $sql = 'select min(required_level) as min from quests
    where `group` = %d;';
  $result = db_query($sql, $game_quest->group + 1);
  $item = db_fetch_object($result);
firep($item);

  if (!empty($item->min) && ($item->min <= $game_user->level + 1) &&
    ($group_to_show <= 1000)) {

    $newer_group = $game_quest->group + 1;
    $newer_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$newer_group">&gt;&gt;</a>
EOF;

  }

  if ($game == 'celestial_glory') {

    $quests = '';

  } else {

    $quests = "{$quest}s";

  }

  echo <<< EOF
<div class="title">
$older_missions_html $location $quests $newer_missions_html
</div>
EOF;

// get quest group stats
  $sql = 'SELECT sum(bonus_given) as completed, count(quests.id) as total
    FROM `quests`
    left outer join quest_completion
    on quest_completion.fkey_quests_id = quests.id
    and fkey_users_id = %d
    where `group` = %d and quests.active = 1;';
  $result = db_query($sql, $game_user->id, $game_quest->group);

  $quest_group = db_fetch_object($result);
firep($quest_group);

  $quest_group->completed += 0; // haha!  typecasting!

  if ($quest_group_completion->times_completed > 0) {

    $next_group_html = t('(2nd round)');
    $quest_group->completed -=
      ($quest_group->total * min($quest_group_completion->times_completed, 1));

  }

  echo <<< EOF
<div class="quest-group-completion">
  <strong>$quest_group->completed</strong> of $quest_group->total {$quest}s
  complete $next_group_html
</div>
EOF;

// show each quest
  $data = array();
  $sql = 'select quests.*, quest_completion.percent_complete,
    neighborhoods.name as hood from quests
    LEFT OUTER JOIN neighborhoods
    ON quests.fkey_neighborhoods_id = neighborhoods.id
    LEFT OUTER JOIN quest_completion
    ON quest_completion.fkey_quests_id = quests.id
    AND quest_completion.fkey_users_id = %d where `group` = %d
    and required_level <= %d
    and active = 1 order by required_level ASC;';
  $result = db_query($sql, $game_user->id, $game_quest->group,
    $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    if ($event_type == EVENT_QUESTS_100)
      $item->required_energy = min($item->required_energy, 100);

    $description = str_replace('%clan', "<em>$clan_title</em>",
      $item->description);

    if (empty($item->percent_complete)) $item->percent_complete = 0;

    if ($item->percent_complete > floor($percentage_target / 2)) {

      $rgb = dechex(floor(($percentage_target - $item->percent_complete) /
        (4 * $percentage_divisor))) . 'c0';

    } else {

      $rgb = 'c' . dechex(floor(($item->percent_complete) /
        (4 * $percentage_divisor))) . '0';

    }

    $width = floor($item->percent_complete * 94 / $percentage_target) + 2;

// firep($rgb);

    if (($game_quest->group > 0) &&
      (($item->fkey_neighborhoods_id != 0) &&
      ($item->fkey_neighborhoods_id != $game_user->fkey_neighborhoods_id))) {
// show quests in other hoods?

      echo <<< EOF
  <div class="quests wrong-hood">
    <div class="quest-icon">
      <img src="/sites/default/files/images/quests/$game-$item->id.png"
        border="0" width="96"/>
      <div class="quest-complete">
        <div class="quest-complete-percentage"
          style="background-color: #$rgb; width: {$width}px">
          &nbsp;
        </div>
        <div class="quest-complete-text">
          $item->percent_complete% complete
        </div>
      </div>
    </div>
    <div class="quest-details">
      <div class="quest-name">
        $item->name $active
      </div>
      <div class="quest-description">
        This $quest_lower can only be completed in $item->hood.
      </div>
    </div>
    <form action="/$game/move/$arg2/$item->fkey_neighborhoods_id">
      <div class="quests-perform-button-wrapper">
        <input class="quests-perform-button" type="submit" value="Go there"/>
      </div>
    </form>
  </div>
EOF;

    } else { // quest in my hood

      echo <<< EOF
  <div class="quests">
    <div class="quest-icon"><a href="/$game/quests_do/$arg2/$item->id"><img
      src="/sites/default/files/images/quests/$game-$item->id.png"
      border="0" width="96"></a>
      <div class="quest-complete"><div class="quest-complete-percentage"
        style="background-color: #$rgb; width: {$width}px">&nbsp;</div>
        <div class="quest-complete-text">$item->percent_complete%
          complete</div></div></div>
    <div class="quest-details">
      <div class="quest-name"><a
        href="/$game/quests_do/$arg2/$item->id">$item->name</a></div>
      <div class="quest-description">$description</div>
      <div class="quest-experience">+$item->experience $experience,
      +$item->min_money to $item->max_money $game_user->values</div>
EOF;

      if ($item->chance_of_loot + $item->chance_of_loot_staff > 0) {

        echo <<< EOF
      <div class="quest-loot">Chance of Loot!</div>
EOF;

      }

      echo <<< EOF
      <div class="quest-required_energy">Requires $item->required_energy Energy</div>
EOF;

      // required land
      if ($item->land_required_quantity > 0) {

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $item->fkey_land_required_id,
          $game_user->id);
        $quantity = db_fetch_object($result);

        if ($quantity->quantity >= $item->land_required_quantity) {
          $not_yet = $a_start = $a_end = '';
        } else {
          $not_yet = 'not-yet';
          $a_start = '<a href="/' . $game . '/land_buy/' .
            $arg2 . '/' . $item->fkey_land_required_id . '/' .
            ($item->land_required_quantity - $quantity->quantity) . '">';
          $a_end = '</a>';
        }

        echo <<< EOF
      <div class="quest-required_stuff">Requires
        <div class="quest-required_equipment">$a_start<img class="$not_yet"
          src="/sites/default/files/images/land/$game-$item->fkey_land_required_id.png"
          width="48">$a_end</div>&nbsp;x$item->land_required_quantity
      </div>
EOF;

      } // required land

  // required equipment
      if ($item->equipment_1_required_quantity > 0) {

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $item->fkey_equipment_1_required_id,
          $game_user->id);
        $quantity = db_fetch_object($result);

        if ($quantity->quantity >= $item->equipment_1_required_quantity) {
          $not_yet = $a_start = $a_end = '';
        } else {
          $not_yet = 'not-yet';
          $a_start = '<a href="/' . $game . '/equipment_buy/' .
            $arg2 . '/' . $item->fkey_equipment_1_required_id . '/' .
            ($item->equipment_1_required_quantity - $quantity->quantity) . '">';
          $a_end = '</a>';
        }

        echo <<< EOF
      <div class="quest-required_stuff">Requires
        <div class="quest-required_equipment">$a_start<img class="$not_yet"
          src="/sites/default/files/images/equipment/$game-$item->fkey_equipment_1_required_id.png"
          width="48">$a_end</div>&nbsp;x$item->equipment_1_required_quantity
      </div>
EOF;

  // more required equipment
        if ($item->equipment_2_required_quantity > 0) {

          $sql = 'select quantity from equipment_ownership
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $item->fkey_equipment_2_required_id,
            $game_user->id);
          $quantity = db_fetch_object($result);

          if ($quantity->quantity >= $item->equipment_2_required_quantity) {
            $not_yet = $a_start = $a_end = '';
          } else {
            $not_yet = 'not-yet';
            $a_start = '<a href="/' . $game . '/equipment_buy/' .
              $arg2 . '/' . $item->fkey_equipment_2_required_id . '/' .
              ($item->equipment_2_required_quantity - $quantity->quantity) . '">';
            $a_end = '</a>';
          }

          echo <<< EOF
        <div class="quest-required_stuff">Requires
          <div class="quest-required_equipment">$a_start<img class="$not_yet"
          src="/sites/default/files/images/equipment/$game-$item->fkey_equipment_2_required_id.png"
          width="48">$a_end</div>&nbsp;x$item->equipment_2_required_quantity
      </div>
EOF;

  // more more required equipment
          if ($item->equipment_3_required_quantity > 0) {

            $sql = 'select quantity from equipment_ownership
              where fkey_equipment_id = %d and fkey_users_id = %d;';
            $result = db_query($sql, $item->fkey_equipment_3_required_id,
              $game_user->id);
            $quantity = db_fetch_object($result);

            if ($quantity->quantity >= $item->equipment_3_required_quantity) {
              $not_yet = $a_start = $a_end = '';
            } else {
              $not_yet = 'not-yet';
              $a_start = '<a href="/' . $game . '/equipment_buy/' .
                $arg2 . '/' . $item->fkey_equipment_3_required_id . '/' .
                ($item->equipment_3_required_quantity - $quantity->quantity) . '">';
              $a_end = '</a>';
            }

            echo <<< EOF
          <div class="quest-required_stuff">Requires
            <div class="quest-required_equipment">$a_start<img class="$not_yet"
            src="/sites/default/files/images/equipment/$game-$item->fkey_equipment_3_required_id.png"
            width="48">$a_end</div>&nbsp;x$item->equipment_3_required_quantity
          </div>
EOF;

          } // more more required equipment

        } // more required equipment

      } // required equipment

      if ($item->staff_required_quantity > 0) {

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $item->fkey_staff_required_id,
          $game_user->id);
        $quantity = db_fetch_object($result);

        if ($quantity->quantity >= $item->staff_required_quantity) {
          $not_yet = '';
        } else {
          $not_yet = 'not-yet';
        }

        echo <<< EOF
      <div class="quest-required_stuff">Requires
        <div class="quest-required_equipment"><img class="$not_yet"
          src="/sites/default/files/images/staff/$game-$item->fkey_staff_required_id.png"
          width="48"></div>&nbsp;x$item->staff_required_quantity
      </div>
EOF;

      } // required staff

      echo <<< EOF
      </div>
    </div>
  </div>
EOF;

    } // show quests in other hoods?

  } // foreach item

//  if ($game_user->level > 1) { // don't show extra quests at first

    $data = array();
    $sql = 'select * from quests where `group` = %d and required_level = %d
      and (fkey_neighborhoods_id = 0 or fkey_neighborhoods_id = %d)
      and active = 1 order by required_level ASC;';
    $result = db_query($sql, $game_quest->group, $game_user->level + 1,
      $game_user->fkey_neighborhoods_id);

    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      if ($event_type == EVENT_QUESTS_100)
        $item->required_energy = min($item->required_energy, 100);

      $description = str_replace('%clan', "<em>$clan_title</em>",
        $item->description);
firep($description);
      echo <<< EOF
<div class="quests-soon">
  <div class="quest-name">$item->name</div>
  <div class="quest-description">$description</div>
  <div class="quest-required_level">Requires level $item->required_level</div>
  <div class="quest-experience">+$item->min_money to $item->max_money
    $game_user->values, +$item->experience $experience</div>
  <div class="quest-required_energy">Requires $item->required_energy
    Energy</div>
</div>
EOF;

    }

//  }

  db_set_active('default');
