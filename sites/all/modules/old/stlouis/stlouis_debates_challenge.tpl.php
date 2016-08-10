<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);
/*  
  $sql = 'SELECT users.*,  elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym
    
    from users 
    
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id
    
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    
    WHERE users.id = %d';
  
  $result = db_query($sql, $position_id);
  $item = db_fetch_object($result);
*/
  $item = fetch_user_by_id((int) $position_id);
firep($item);

  $username = $item->username;
  $title = "$debate with $item->ep_name $username";

  if ($item->id == $game_user->id) {

    $fetch_header($game_user);

    echo "<div class=\"title\">$title</div>";
    echo '<div class="subtitle">' . t('You cannot @debate with yourself.',
      array('@debate' => $debate_lower)) . '</div>';
    echo '<div class="subtitle">
      <a href="/' . $game . '/debates/' . $arg2 . '">
        <img src="/sites/default/files/images/' . $game . '_continue.png"/>
      </a>
    </div>';

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"debate-lost\"/>\n-->";

    db_set_active('default');
    return;

  }
  
  if ($game_user->actions <= 0) {

    $fetch_header($game_user);
    
    echo '<div class="title">' .
        $title . '
      </div>
      <div class="land failed">
        <div class="title">' .  
          t('∆ Action Quantity ∆<br/><span>Insufficient</span>') . '
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/elders_do_fill/' . $arg2 .
              '/action?destination=/' . $game . '/debates/' . $arg2 . '">
              Refill  your Action (2&nbsp;' . $luck . ')
            </a>
          </div>
        </div>
      </div>';

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"debate-no-action\"/>\n-->";

    db_set_active('default');
    return;

  }

//  $debate_time = 1200;
//  if ($debate == 'Box') $debate_time = 900;

  if (($item->meta != 'zombie' &&
    (time() - strtotime($item->debates_last_time)) <= $debate_time) ||
    ($item->meta == 'zombie' && 
    (time() - strtotime($item->debates_last_time)) <= $zombie_debate_wait)) {
// not long enough
    
    $fetch_header($game_user);

    echo '<div class="title">' .
        $title . '
      </div>
      <div class="subtitle">' .
        t('You must wait longer to @debate with this player',
          array('@debate' => $debate_lower)
        ) . '
      </div>';
    _button('debates');

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"debate-must-wait\"/>\n-->";

    db_set_active('default');
    return;
    
  }
  
/*  if ($game_user->experience > ($item->experience * 2)) {
// you cannot challenge someone if you have more than twice their influence

    $fetch_header($game_user);

    echo "<div class=\"title\">$title</div>";
    echo '<div class="election-failed">' . t('Sorry!') . '</div>';
    echo '<div class="subtitle">' .
      t('Your @experience is too high to challenge ',
        array('@experience' => $experience)) . $item->username . 
        '.</div>';
    echo '<div class="election-continue"><a href="/' . $game . '/debates/' .
      $arg2 . '">' . t('Continue') . '</a></div>';

    db_set_active('default');
    return;
    
  }*/

// otherwise, we need to apply a formula to get votes

// experience

  $sql = 'SELECT sum(equipment.elocution_bonus * equipment_ownership.quantity)
    as elocution from equipment 

    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';
  
  $result = db_query($sql, $game_user->id);
  $elocution_bonus = db_fetch_object($result);

  $sql = 'SELECT sum(staff.elocution_bonus * staff_ownership.quantity)
    as elocution from staff

    left join staff_ownership
    on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';
  
  $result = db_query($sql, $game_user->id);
  $elocution_bonus_st = db_fetch_object($result);
firep('staff elocution bonus is ' . $elocution_bonus_st->elocution);

  $my_el_bonus = $elocution_bonus->elocution + $elocution_bonus_st->elocution +
    50;

// opponent elocution

  $sql = 'SELECT sum(equipment.elocution_bonus * equipment_ownership.quantity)
    as elocution from equipment 

    left join equipment_ownership 
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';
  
  $result = db_query($sql, $item->id);
  $elocution_bonus = db_fetch_object($result);

  $sql = 'SELECT sum(staff.elocution_bonus * staff_ownership.quantity)
    as elocution from staff

    left join staff_ownership
    on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';
  
  $result = db_query($sql, $game_user->id);
  $elocution_bonus_st = db_fetch_object($result);

  $opp_el_bonus = $elocution_bonus->elocution + $elocution_bonus_st->elocution +
    50;

  $my_influence = sqrt(max(0, $game_user->experience)) + ($game_user->elocution *
    $my_el_bonus);
firep("your total influence: sqrt($game_user->experience) +
  ($game_user->elocution * $my_el_bonus) = $my_influence");

  $opp_influence = sqrt(max(0, $item->experience)) + ($item->elocution * 
    $opp_el_bonus);
firep("opp total influence: sqrt($item->experience) + ($item->elocution * 
    $opp_el_bonus) = $opp_influence");
    
  $money_change = mt_rand(5 + $game_user->level,
    10 + ($game_user->level * 2)); // values changed

// don't change more than net income / 6 for each user
  $money_change = min($money_change,
    floor(($game_user->income - $game_user->expenses) / 6));
  $money_change = min($money_change,
    floor(($item->income - $item->expenses) / 6));

// don't let money get negative or more than double
  if ($money_change > $game_user->money) $money_change = $game_user->money;
  if ($money_change > $item->money) $money_change = $item->money;
  if ($money_change < 0) $money_change = 0;
  
  if ($my_influence > $opp_influence) { // you won!  woohoo!

    $experience_gained = mt_rand(floor($item->level / 3),
      ceil($item->level * 2 / 3));
// the experience you gain is based on their level    
    
    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('%user has successfully @debated you!  ' .
      'You lost @money @value.',
      array('%user' => $game_user->username, '@money' => $money_change,
        '@value' => $item->values, '@debated' => "{$debated_lower}"));
    $result = db_query($sql, $game_user->id, $item->id, $message);
    
    $sql = 'update users set money = money + %d, experience = experience + %d,
      actions = actions - 1, debates_won = debates_won + 1 where id = %d;';
    $result = db_query($sql, $money_change, $experience_gained, $game_user->id);
    $sql = 'update users set money = money - %d, debates_lost = debates_lost + 1,
      debates_last_time = "%s" where id = %d;';
    $result = db_query($sql, $money_change, date('Y-m-d H:i:s', time()), $item->id);

    if ($debate == 'Box') { // boxing day?  add boxing stats

      $sql = 'update users set meta_int = meta_int + %d where id = %d;';
      $result = db_query($sql, $money_change, $game_user->id);
      $sql = 'update users set meta_int = meta_int - %d where id = %d;';
      $result = db_query($sql, $money_change, $item->id);
      $gain_extra = ' and Boxing Points<br/>';

    } else {

      $gain_extra = '';

    }

// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }
    
    $game_user = $fetch_user();

    if ($event_type == EVENT_DEBATE) {

      $bump = '_' . $game . '_bump_event_tags_con';
      $reset = '_' . $game . '_reset_event_tags_con';
      $row = $bump($game_user->id);
      $reset($item->id);
      firep($row);

    }

    $fetch_header($game_user);
    
    echo '<div class="election-succeeded">' . t('Success!') . '</div>';
    echo "
      <div class=\"subtitle\">You beat 
        <a href=\"/$game/user/$arg2/$item->phone_id\">
          <span class=\"username\">
            $item->username
          </span>
        </a>
      </div>
      <div class=\"action-effect\">
        You gained $money_change $game_user->values $gain_extra
          and $experience_gained $experience!
      </div>";

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"debate-won\"/>\n-->";
      
    if ($event_type == EVENT_DEBATE) {

      echo '<div class="subsubtitle">You have ' . $row->tags_con .
      ' consecutive debate win(s) and ' . $row->points . ' point(s)</div>';

    }


// zombies
    if ($item->meta == 'zombie' /* || $phone_id == 'abc123' */) {

      $points_to_add = 0;

      if ($game_user->debates_won >= ($game_user->level * 100)) {
// beaten by super debater... evolve

        $sql = 'select fkey_clans_id from clan_members 
          where fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);
        $clan_player = db_fetch_object($result);
        $sql = 'select fkey_clans_id from clan_members 
          where fkey_users_id = %d;';
        $result = db_query($sql, $item->id);
        $clan_zombie = db_fetch_object($result);

        if (($item->fkey_values_id != $game_user->fkey_values_id) 
          && ($phone_id != 'abc123')) {
// first -- join party

          $sql = 'update users set fkey_values_id = %d, values = "%s"
            where id = %d;';
          $result = db_query($sql, $game_user->fkey_values_id,
            $game_user->values, $item->id);
          $sql = 'delete from clan_members where fkey_users_id = %d;';
          $result = db_query($sql, $item->id);
          $sql = 'select party_title from `values`
            where id = %d;';
          $result = db_query($sql, $game_user->fkey_values_id);
          $party = db_fetch_object($result);
          echo '<div class="subtitle">Because you are a super debater,<br/>' .
            $item->username . ' now owes his allegiance to ' . 
            $party->party_title . '.</div>';

          $points_to_add = 10;

//          mail('joseph@cheek.com', "Zombie $item->id switched parties",
//            "$item->username was beaten by super debater $game_user->username"
//            . " and has switched to $party->party_title!");

        } else if 
          (($clan_player->fkey_clans_id != $clan_zombie->fkey_clans_id) &&
          ($clan_player->fkey_clans_id > 0) && $phone_id != 'abc123') {
// second -- join clan

          $sql = 'delete from clan_members where fkey_users_id = %d;';
          $result = db_query($sql, $item->id);

          $sql = 'insert into clan_members
            (fkey_clans_id, fkey_users_id) values (%d, %d);';
          $result = db_query($sql, $clan_player->fkey_clans_id, $item->id);

          $sql = 'select name from clans where id = %d;';
          $result = db_query($sql, $clan_player->fkey_clans_id);
          $clan_name = db_fetch_object($result);

          echo '<div class="subtitle">Because you are a super debater,<br/>' .
            $item->username . ' has now joined your clan.</div>';

          $points_to_add = 15;

//          mail('joseph@cheek.com', "Zombie $item->id joined a clan",
//            "$item->username was beaten by super debater $game_user->username"
//            . " and has switched to $clan_name->name!");

// already party and clan -- move them!
        } else if 
          ((($clan_player->fkey_clans_id == $clan_zombie->fkey_clans_id) &&
          ($clan_player->fkey_clans_id > 0) &&
          ($item->fkey_values_id == $game_user->fkey_values_id))
          /* || $phone_id == 'abc123' */
          ) { // move them!

          $hoods = array();
          $sql = 'select id, name from neighborhoods
            where has_elections = 1
            order by name ASC;';
          $result = db_query($sql);
          while ($hood = db_fetch_object($result)) $hoods[] = $hood;

          echo <<< EOF
<div class="subtitle">Because you are a super debater,<br/>
$item->username will move to where you want him or her.</div>
<div class="title">To where should he or she move?</div>
EOF;

          foreach ($hoods as $hood) {

            echo <<< EOF
<div class="subsubtitle">
  <a href="/$game/zombie_move/$phone_id/$item->id/$hood->id">
$hood->name</a></div>
EOF;

          } // foreach hood

          $points_to_add = 20;

        } // in super debater's party / clan?

// not beaten by a super debater
      } else if ($item->debates_lost > 4) { // lost 5 debates

          $sql = 'delete from users where id = %d;';
          $result = db_query($sql, $item->id);
          $sql = 'delete from user_messages where fkey_from_users_id = %d
            or fkey_to_users_id = %d;';
          $result = db_query($sql, $item->id, $item->id);
          echo '<div class="subtitle">' . $item->username .
            ' has retreated in shame.</div>';

          $points_to_add = 10;

//          mail('joseph@cheek.com', "Zombie conquered ($item->id)",
//            "$item->username has five debate losses and has left!");

      } else { // not 5 losses yet

        $points_to_add = ($item->debates_lost + 1) * 2;

//        mail('joseph@cheek.com', "Zombie beaten but not conquered ($item->id)",
//          "$item->username has less than five debate losses");

      } // # of losses

// create entry for debater

      $sql = 'select * from event_points where fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);
      $row = db_fetch_object($result);

      if (empty($row)) {

        $sql = 'insert into event_points set fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);

      }

      $points = $row->points + $points_to_add;

      $sql = 'update event_points set points = %d
        where fkey_users_id = %d;';
      $result = db_query($sql, $points, $game_user->id);

      echo <<< EOF
<div class="subsubtitle">You gained $points_to_add point(s)</div>
EOF;

    } // a zombie?
/*
// flag day -- did they get a flag?
    $sql = 'select * from equipment_ownership where fkey_users_id = %d
      and fkey_equipment_id = 23;';
    $result = db_query($sql, $item->id);
    $flag = db_fetch_object($result);

    if ($flag->quantity > 0) { // they had a flag -- you get it!

      echo '<div class="election-succeeded">You found a flag!</div>';
      echo '<div class="subtitle"><img 
        src="/sites/default/files/images/equipment/stlouis-23.png"></div>';
      echo '<div class="subtitle">It will give you 1 Luck every 5 minutes</div>';
      $sql = 'update equipment_ownership set fkey_users_id = %d
        where id = %d;';
firep("update equipment_ownership set fkey_users_id = $game_user->id
        where id = $flag->id");
      $result = db_query($sql, $game_user->id, $flag->id);

    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('%user took your flag!',
      array('%user' => $game_user->username));
    $result = db_query($sql, $game_user->id, $item->id, $message);
    
      mail('joseph@cheek.com', 'flag transfer',
        "$item->username's flag was captured by $game_user->username!");

    }
*/ // flag day
  } else { // you lost
    
    $experience_gained = mt_rand(floor($game_user->level / 3),
      ceil($game_user->level * 2 / 3));
// the experience they gain is based on your level 
    
    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('You have successfully defended yourself against a @debate ' .
      'from %user.  You gained @money @value and @exp @experience.',
      array('%user' => $game_user->username, '@money' => $money_change,
        '@value' => $item->values, '@experience' => $experience,
        '@exp' => $experience_gained, '@debate' => $debate_lower));
    $result = db_query($sql, $game_user->id, $item->id, $message);
    
    $sql = 'update users set money = money - %d, actions = actions - 1,
      debates_lost = debates_lost + 1 where id = %d;';
    $result = db_query($sql, $money_change, $game_user->id);
    $sql = 'update users set money = money + %d,
      experience = experience + %d, debates_won = debates_won + 1,
      debates_last_time = "%s" where id = %d;';
    $result = db_query($sql, $money_change, $experience_gained,
      date('Y-m-d H:i:s', time()), $item->id);

    if ($debate == 'Box') { // boxing day?  add boxing stats

      $sql = 'update users set meta_int = meta_int - %d where id = %d;';
      $result = db_query($sql, $money_change, $game_user->id);
      $sql = 'update users set meta_int = meta_int + %d where id = %d;';
      $result = db_query($sql, $money_change, $item->id);
      $gain_extra = ' and Boxing Points<br/>';

    } else {

      $gain_extra = '';

    }


// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }

    $game_user = $fetch_user();

    if ($event_type == EVENT_DEBATE) {

      $bump = '_' . $game . '_bump_event_tags_con';
      $reset = '_' . $game . '_reset_event_tags_con';
      $row = $reset($game_user->id);
      $bump($item->id);
      firep($row);

    }

    $fetch_header($game_user);

    if ($item->meta == 'zombie') {

      $sql = 'updates users set experience = experience + 1000
        where id = %d;';
      $result = db_query($sql, $item->id);
      echo '<div class="subtitle">' . $item->username .
        ' has gained 1000 influence.</div>';

//      mail('joseph@cheek.com', "Zombie $item->id won the debate!",
//        "growing stronger...");

    }
    
    echo '<div class="election-failed">' . t('Defeated') . '</div>';
    echo "
      <div class=\"subtitle\">
        You lost to 
        <a href=\"/$game/user/$arg2/$item->phone_id\">
          <span class=\"username\">
            $item->username
          </span>
        </a>
      </div>
      <div class=\"action-effect\">" .
        t('You lost @money @value' . $gain_extra,
          array('@money' => $money_change, '@value' => $game_user->values)) .
      '</div>';

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"debate-lost\"/>\n-->";

    if ($event_type == EVENT_DEBATE) {

      echo '<div class="subsubtitle">You have ' . $row->tags_con .
      ' consecutive debate win(s) and ' . $row->points . ' point(s)</div>';

    }

/*
// flag day -- did they get a flag?
    $sql = 'select * from equipment_ownership where fkey_users_id = %d
      and fkey_equipment_id = 23;';
    $result = db_query($sql, $item->id);
    $flag = db_fetch_object($result);

    if ($flag->quantity > 0) { // they had a flag, but you don't get it

      echo '<div class="subtitle">He or she had a flag,
        but you don\'t get it</div>';

      mail('joseph@cheek.com', 'attempted flag transfer',
        "$item->username's flag was NOT captured by $game_user->username!");

    }
*/
  }

// YOU USED

  echo <<< EOF
<div class="subtitle">
  You used
</div>
<div class="debate-used-wrapper">
EOF;
  
  $data = array();
  $sql = 'SELECT equipment.id, equipment.elocution_bonus,
    "equipment" as type, equipment_ownership.quantity,
    equipment.chance_of_loss, equipment.name
    FROM equipment

    LEFT OUTER JOIN equipment_ownership
    ON equipment_ownership.fkey_equipment_id = equipment.id
    AND equipment_ownership.fkey_users_id = %d

    WHERE equipment.elocution_bonus > 0
    AND equipment_ownership.quantity > 0

    union

    SELECT staff.id, staff.elocution_bonus, "staff" as type, 
    staff_ownership.quantity, staff.chance_of_loss, staff.name
    FROM staff

    LEFT OUTER JOIN staff_ownership
    ON staff_ownership.fkey_staff_id = staff.id
    AND staff_ownership.fkey_users_id = %d

    WHERE staff.elocution_bonus > 0
    AND staff_ownership.quantity > 0

    ORDER BY elocution_bonus DESC;';
  $result = db_query($sql, $game_user->id, $game_user->id);

  while ($eqst = db_fetch_object($result)) $data[] = $eqst;
  
  if (empty($data)) echo '<div class="debate-used">' .
      t('Nothing') . '
    </div>';

  $you_lost = $opp_lost = '';

  foreach ($data as $eqst) {
firep($item);

    echo <<< EOF
<div class="debate-used">
  <img src="/sites/default/files/images/$eqst->type/$game-$eqst->id.png"
    border="0" width="72"/>
  <span class="debate-num">
    x $eqst->quantity
  </span>
</div>
EOF;

// see if any have worn out
    list($lost, $failure_reason, $quantity_left) =
      equipment_check_wear_out($game_user, $eqst->id);
    if ($lost) $you_lost .= '<div class="subtitle">' .
        $failure_reason . '
      </div>';

  }

// OPPONENT USED

  echo <<< EOF
</div>
<div class="subtitle">
  <span class="username">
    $username
  </span>
  used
</div>
<div class="debate-used-wrapper">
EOF;
  
  $data = array();
  $result = db_query($sql, $position_id, $position_id);
  while ($eqst = db_fetch_object($result)) $data[] = $eqst;

  if (empty($data)) echo '<div class="debate-used">' . t('Nothing') . '</div>';
  
  foreach ($data as $eqst) {
// firep($eqst);

    echo <<< EOF
<div class="debate-used">
  <img src="/sites/default/files/images/$eqst->type/$game-$eqst->id.png"
    border="0" width="72">
  <span class="debate-num">
    x $eqst->quantity
  </span>
</div>
EOF;

// see if any have worn out
    list($lost, $failure_reason, $quantity_left) =
      equipment_check_wear_out($item, $eqst->id);

    if ($lost) {
      $opp_lost .= '<div class="subtitle">' .
        str_replace('your', 'his/her', $failure_reason) . '
      </div>';

      $sql = 'insert into challenge_messages
        (fkey_users_from_id, fkey_users_to_id, message)
        values (%d, %d, "%s");';
      $message = t('During the @debate, @loss.',
        array('@debate' => $debate_lower,
          '@loss' => strtolower($failure_reason)));
      $result = db_query($sql, $game_user->id, $item->id, $message);

    }

  }

  echo $you_lost;
  echo $opp_lost;

// super win -- chance of pillaging

  if (
    (($my_influence > ($opp_influence * 2)) && (mt_rand(0, 9) == 0))) {

    unset($eq);
    foreach ($data as $eqst)
      if ($eqst->type == 'equipment') {
        $eq = $eqst;
// firep($eq);
        break;
      }

    if (isset($eq)) {

      equipment_gain($game_user, $eq->id, 1);
      equipment_lose($item, $eq->id, 1);

      echo '<div class="subtitle">Your superior ' . $elocution . '
        causes ' . $username . ' to flee from your presence, dropping
        one ' . $eq->name . ' for you to keep!</div>';

      $sql = 'insert into challenge_messages
        (fkey_users_from_id, fkey_users_to_id, message)
        values (%d, %d, "%s");';
      $message = t('During the @debate, you ran away, dropping one @eq.',
        array('@debate' => $debate_lower, '@eq' => $eq->name));
      $result = db_query($sql, $game_user->id, $item->id, $message);

    }

  }

  _button('debates');
  
  db_set_active('default');
