<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include (drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// do AI moves from this page!!!
  include(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if (is_numeric(arg(3))) $group_to_show = arg(3);

  if (is_numeric($group_to_show)) {

    $sql_quest_neighborhood = 'where `group` = ' . $group_to_show;

  } elseif ($game_user->level < 6) { // show beginning quests

    $group_to_show = '0';
    $sql_quest_neighborhood = 'where `group` = 0';

  } else { // show the group for which the player last successfully completed a quest

    $group_to_show = $game_user->fkey_last_played_quest_groups_id;
    $sql_quest_neighborhood = 'where `group` = ' . $group_to_show;

  }

  $sql = 'select name from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;

  $sql = 'select clan_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $clan_title = preg_replace('/^The /', '', $data->clan_title);

  if ($game_user->level >= 6) { // show quests menu after level 6

    if ($group_to_show >= 1000) {
      $merch_active = '';
      $lehite_active = '';
    } elseif ($group_to_show >= 100) {
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

  if ($game_user->experience == 0) { // show more welcome text for new user

    echo <<< EOF
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>Your father, Lehi, continues.</p>
  <p class="second">&quot;Here is the start of my tale.&nbsp; Perform these
    quests to learn my story, and you will make it your story.</p>
  <p class="second">&quot;To perform a quest, touch its picture or title.&quot;</p>
  <ul>
    <li>Each quest completed gives you more $game_user->values and
      $experience</li>
    <li>Wait and rest for a few minutes if you run out of Energy</li>
  </ul>
</div>

EOF;
  } // experience = 0

  if ($game_user->fkey_values_id == 0 && $game_user->level >= 6 &&
    $game_user->level <= 25)
    drupal_goto($game . '/choose_clan/' . $arg2 . '/0');
// don't let them do quests at levels 6-25 without being in a party

  if (!$game_user->seen_neighborhood_quests && $game_user->level >= 6) {
// intro neighborhood quests at level 6

    echo <<< EOF
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <!--<p>&quot;A wise choice &mdash; that party will serve you well.</p>-->
  <p>&quot;Some of your {$quest}s now depend on the part of the $city_lower in
    which you are located.&nbsp; You are now in the <strong>$location</strong>
    $hood_lower.&nbsp;
    You will find more {$quest}s as you move to different parts of the
    $city_lower.&quot;</p>
  <br/>
</div>
EOF;

    $sql = 'update users set seen_neighborhood_quests = 1 where id = %d;';
    $result = db_query($sql, $game_user->id);

  } // haven't seen quests intro

  if ($game_user->level < 6) $location = ''; // keep location from user

  if ($game_user->level < 6 and $game_user->experience > 0) {

    echo <<< EOF
<ul>
  <li>Each $quest gives you more $game_user->values and $experience</li>
  <li>Wait and rest for a few minutes if you run out of Energy</li>
</ul>
EOF;

  }

  $sql = 'select name from quest_groups where id = %s;';
  $result = db_query($sql, $group_to_show);
  $qg = db_fetch_object($result);
firep($qg);

  $location = str_replace('%location', $location, $qg->name);

  if ($game_user->level < 6) $location = '';

  $sql = 'select name from quest_groups where id = %s;';
  $result = db_query($sql, $group_to_show - 1);
  $qgo = db_fetch_object($result);

  if (!empty($qgo->name)) {

    $older_group = $group_to_show - 1;
    $older_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$older_group">&lt;&lt;</a>
EOF;

  }

  $sql = 'select min(required_level) as min from quests
    where `group` = %d;';
  $result = db_query($sql, $group_to_show + 1);
  $item = db_fetch_object($result);
firep($item);

  if (!empty($item->min) && ($item->min <= $game_user->level + 1) &&
    ($group_to_show <= 1000)) {

    $newer_group = $group_to_show + 1;
    $newer_missions_html =<<< EOF
<a href="/$game/quests/$arg2/$newer_group">&gt;&gt;</a>
EOF;

  }

  $quests = '';

  echo <<< EOF
<div class="title">
$older_missions_html $location $quests $newer_missions_html
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
    AND quest_completion.fkey_users_id = %d
    ' . $sql_quest_neighborhood .
    ' and required_level <= %d ' . $active_quests .
    ' order by required_level ASC;';
firep($sql);
  $result = db_query($sql, $game_user->id, $game_user->level);

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

    $active = ($item->active) ? '' : ' (inactive)';
firep($item);

    if (($group_to_show > 0) &&
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
      src="/sites/default/files/images/quests/$game-$item->id.png" border="0"
      width="96"></a>
      <div class="quest-complete"><div class="quest-complete-percentage"
        style="background-color: #$rgb; width: {$width}px">&nbsp;</div>
      <div class="quest-complete-text">$item->percent_complete%
        complete</div></div></div>
    <div class="quest-details">
      <div class="quest-name"><a
        href="/$game/quests_do/$arg2/$item->id">$item->name $active</a></div>
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

  // required staff
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
    $sql = 'select * from quests ' . $sql_quest_neighborhood .
      ' and required_level = %d ' . $active_quests .
      ' order by required_level ASC;';
    $result = db_query($sql, $game_user->level + 1);

    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      if ($event_type == EVENT_QUESTS_100)
        $item->required_energy = min($item->required_energy, 100);

      $description = str_replace('%clan', "<em>$clan_title</em>",
        $item->description);

      $active = ($item->active) ? '' : ' (inactive)';

      echo <<< EOF
<div class="quests-soon">
  <div class="quest-name">$item->name $active</div>
  <div class="quest-description">$description</div>
  <div class="quest-required_level">Requires level $item->required_level</div>
  <div class="quest-experience">+$item->experience $experience</div>
  <div class="quest-money">+$item->min_money to $item->max_money
    $game_user->values</div>
  <div class="quest-required_energy">Requires $item->required_energy energy</div>
</div>
EOF;

    }

//  }

  db_set_active('default');
