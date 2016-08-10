<?php

  global $game, $phone_id;
  
  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);
    
  $sql = 'select name, has_elections, residents, district, is_limited
    from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $item = db_fetch_object($result);
  $location = $item->name;
  $residents = $item->residents;
  $district = $item->district;
  $is_limited = $item->is_limited;
  
  if ($item->has_elections == 0) {

    echo <<< EOF
<div class="title">No Elections here!</div>
<div class="subtitle">You're on vacation!&nbsp;
  Why worry about elections here?</div>
<div class="subtitle">
  <a href="/$game/home/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-failed none-here\"/>\n-->";

    $sql = 'update users set karma = karma - 25 where id = %d;';
    $result = db_query($sql, $game_user->id);

    db_set_active('default');

    return;

  }

  $sql = 'SELECT elected_positions.id AS ep_id, elected_positions.energy_bonus,
    elected_positions.name AS ep_name, elected_positions.type,  
    blah.*, `values`.party_icon,
    `values`.party_title, clan_members.fkey_clans_id
    
    FROM elected_positions
    
    LEFT OUTER JOIN (

-- type 1: neighborhood positions
      
    SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating,
        elected_officials.approval_15,
        elected_officials.approval_30,
        elected_officials.approval_45, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions 
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_neighborhoods_id = %d
      AND elected_positions.type = 1

      UNION
      
-- type 2: party positions
      
      SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating,
        elected_officials.approval_15,
        elected_officials.approval_30,
        elected_officials.approval_45, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions 
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_values_id = %d
      AND elected_positions.type = 2

      UNION
      
-- type 3: house positions
      
      SELECT elected_officials.fkey_elected_positions_id,
        elected_officials.approval_rating,
        elected_officials.approval_15,
        elected_officials.approval_30,
        elected_officials.approval_45, users.*
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions 
        ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_neighborhoods_id IN
        (SELECT id from neighborhoods where district = %d)
      AND elected_positions.type = 3
    ) AS blah ON blah.fkey_elected_positions_id = elected_positions.id
    
    LEFT JOIN `values` ON blah.fkey_values_id = `values`.id
    
    LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = blah.id
    
    WHERE elected_positions.id = %d;';
  
  $result = db_query($sql, $game_user->fkey_neighborhoods_id, 
    $game_user->fkey_values_id, $district, $position_id);
  $item = db_fetch_object($result); 
  firep($item);
  
// labor day -- all are UWP - jwc
//  $game_user->fkey_values_id = 7;

  $username = $item->username;

  if (empty($item->id)) {

    $title = "Run for the office of $item->ep_name";
      
  } else {
      
    $title = "Challenge $item->ep_name $username";
            
  }

  if ($item->id == $game_user->id) {

    echo <<< EOF
<div class="title">$title</div>
EOF;

    echo '<div class="subtitle">' . t('You cannot challenge yourself.') .
      '</div>';
    echo '<div class="subtitle">
  <a href="/' . $game . '/hierarchies/' . $arg2 . '">
    <img src="/sites/default/files/images/' . $game . '_continue.png"/>
  </a>
</div>';

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-failed no-challenge-yourself\"/>\n-->";

    db_set_active('default');
    return;

  }
  
  if ($game_user->actions < $item->energy_bonus) { // not enough action left          
      
    $fetch_header($game_user);
    
    echo <<< EOF
<div class="title">$title</div>
EOF;

    echo '<div class="subtitle">' . t('Not enough Action!') .
      '</div>';
    echo '<div class="election-continue">
        <a href="/' . $game . '/elders_do_fill/' . $arg2 .
          '/action?destination=/' . $game . '/hierarchies/' . $arg2 . '">' .
          t('Refill your Action (2&nbsp;@luck)', array('@luck' => $luck)) .
        '</a>
      </div>';

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-failed no-action\"/>\n-->";

    db_set_active('default');
    return;
      
  }

  if (empty($item->id)) {
// if you are running without an opponent, you automatically win

    if ($item->type == 2) { // party office
// make it so s/he can't perform a major action for a day
      $set_value = '_' . arg(0) . '_set_value';
      $set_value($game_user->id, 'next_major_action', time() + 86400);
    }

    $sql = 'delete from elected_officials where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id); // you can only hold one position
      
    $sql = 'insert into elected_officials set fkey_users_id = %d,
      fkey_elected_positions_id = %d;';
    $result = db_query($sql, $game_user->id, $position_id);

    $sql = 'insert into challenge_history 
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("election", %d, 0, %d, %d, 1, "' . $game_user->username . 
      ' ran unopposed and automatically won.", "' . $game_user->username . 
      ' ran unopposed and automatically won.")';
    $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
      $position_id);
    
    $sql = 'update users set actions = actions - %d  where id = %d;';
    $result = db_query($sql, $item->energy_bonus, $game_user->id);
    
// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }
    
    $game_user = $fetch_user();
    $fetch_header($game_user);
    
    echo <<< EOF
<div class="title">$title</div>
EOF;

    echo '<div class="election-succeeded">' . t('Success!') . '</div>';
    echo '<div class="subtitle">' .
      t('You ran unopposed and automatically win!') . '</div>';
    echo '<div class="subtitle">
  <a href="/' . $game . '/hierarchies/' . $arg2 . '">
    <img src="/sites/default/files/images/' . $game . '_continue.png"/>
  </a>
</div>';
      
    $message = "$game_user->username ran unopposed for the seat $item->ep_name" .
      " in $location.";

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-won\"/>\n-->";

    db_set_active('default');
    return;
    
  }
  
/*    if ($game_user->experience > ($item->experience * 2)) {
// you cannot challenge someone if you have more than twice their influence
    
    echo '<div class="election-failed">' . t('Sorry!') . '</div>';
    echo '<div class="subtitle">' .
      t('Your influence is too high to challenge ') . $item->username . '.</div>';
    echo '<div class="election-continue"><a href="/' . $game . '/hierarchies/' .
      $arg2 . '">' . t('Continue') . '</a></div>';

    db_set_active('default');
    return;
    
  }
*/  
// otherwise, we need to apply a formula to get votes

// CHALLENGER's initiative

  $sql = 'SELECT sum(staff.initiative_bonus * staff_ownership.quantity)
    as initiative from staff 
    left join staff_ownership on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $game_user->id);
  $st_initiative_bonus = db_fetch_object($result);
  
  $sql = 'SELECT sum(equipment.initiative_bonus * equipment_ownership.quantity)
    as initiative from equipment 
    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $game_user->id);
  $eq_initiative_bonus = db_fetch_object($result);
  
  $in_bonus = $st_initiative_bonus->initiative +
    $eq_initiative_bonus->initiative + 100;
firep("Initiative bonus = " . $in_bonus);

  $sql = 'SELECT sum(staff.extra_votes * staff_ownership.quantity)
    as extra_votes from staff 
    left join staff_ownership on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $game_user->id);
  $st_extra_votes = db_fetch_object($result);
  
  $sql = 'SELECT sum(equipment.extra_votes * equipment_ownership.quantity)
    as extra_votes from equipment 
    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $game_user->id);
  $eq_extra_votes = db_fetch_object($result);
  
  $extra_votes = $st_extra_votes->extra_votes + $eq_extra_votes->extra_votes;

// memorial day promo -- every 250 vets = extra vote
/*
    $sql = 'SELECT quantity
      FROM staff_ownership
      WHERE fkey_users_id = %d AND fkey_staff_id = 7;';
    $result = db_query($sql, $game_user->id);
    $vet_bonus = db_fetch_object($result);

    $extra_vet_votes = (int) ($vet_bonus->quantity / 250);
firep('Extra Votes = ' . $extra_votes . ' + ' . $extra_vet_votes);
    $extra_votes += $extra_vet_votes;
*/
// INCUMBENT's endurance

  $sql = 'SELECT sum(staff.endurance_bonus * staff_ownership.quantity)
    as endurance from staff 
    left join staff_ownership on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';
  $result = db_query($sql, $item->id);
  $st_endurance_bonus = db_fetch_object($result);
  
  $sql = 'SELECT sum(equipment.endurance_bonus * equipment_ownership.quantity)
    as endurance from equipment 
    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';
  $result = db_query($sql, $item->id);
  $eq_endurance_bonus = db_fetch_object($result);
  
  $en_bonus = $st_endurance_bonus->endurance +
    $eq_endurance_bonus->endurance + 100;
firep("Endurance bonus = $en_bonus");

  $sql = 'SELECT sum(staff.extra_defending_votes * staff_ownership.quantity)
    as votes from staff 
    left join staff_ownership on staff_ownership.fkey_staff_id = staff.id and
    staff_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $item->id);
  $st_extra_defending_votes = db_fetch_object($result);
  
  $sql = 'SELECT sum(equipment.extra_defending_votes * equipment_ownership.quantity)
    as votes from equipment 
    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d;';  
  $result = db_query($sql, $item->id);
  $eq_extra_defending_votes = db_fetch_object($result);
  
  $extra_defending_votes = $st_extra_defending_votes->votes +
    $eq_extra_defending_votes->votes;

// memorial day promo -- every 250 vets = extra vote
/*
    $sql = 'SELECT quantity
      FROM staff_ownership
      WHERE fkey_users_id = %d AND fkey_staff_id = 7;';
    $result = db_query($sql, $item->id);
    $vet_bonus = db_fetch_object($result);

    $extra_vet_votes = (int) ($vet_bonus->quantity / 250);
firep('Extra Defending Votes = ' . $extra_defending_votes . ' + ' .
  $extra_vet_votes);
    $extra_defending_votes += $extra_vet_votes;
*/
  $my_influence = ceil($game_user->experience / 5) + ($game_user->initiative *
    $in_bonus);
firep("your total influence: $my_influence");

  $opp_approval = max(($item->approval_rating + $item->approval_15 +
    $item->approval_30 + $item->approval_45) / 4, 10); 
// minimum of 10% average approval rating
  
  $opp_influence = ceil((ceil($item->experience / 5) + ($item->endurance * 
    $en_bonus)) * $opp_approval * 0.017);
// 60% is a "normal" approval rating - multiplying by .017 = 1.02, close enough
firep("opp total influence: $opp_influence");

  $total_influence = $my_influence + $opp_influence + 100; // bias for incumbent
  $votes = $extra_defending_votes - $extra_votes;
firep("Your $extra_votes voters vote for you");
firep("His/her $extra_defending_votes voters vote for him/her");
  
// limited (ie, training) hood?  don't allow challengers with more than 100k
// influence for Alderman seat

// also don't allow defenders with more than 100k endurance

  if (($is_limited) && ($item->ep_id == 1)) {

    if ($opp_influence > 100000) { // opponent has too much influence!
// stack the votes so you automatically win

      $extra_votes += 10000;
firep('10000 extra voters spontaneously arrive to vote for you!');
mail('joseph@cheek.com', 'Alder in training hood has too much endurance!',
  "$item->username [$opp_influence] in $location is voted out of office");

    } else if ($my_influence > 100000) { // challenger has too much influence
// do not allow him/her to challenge

mail('joseph@cheek.com',
  'Challenger for Alder in training hood has too much influence!',
  "$game_user->username [$my_influence] in $location " .
  'is not allowed to challenge.');

      echo '<div class="election-failed">' . t('Sorry!') . '</div>';
      echo '<div class="subtitle">' .
        t('Your influence is too high to challenge ') . $item->username .
        '.</div>';
      echo '<div class="election-continue"><a href="/' . $game . '/hierarchies/' .
        $arg2 . '">' . t('Continue') . '</a></div>';

      db_set_active('default');
      return;

    } // influence over 100000
    
  } // training hood and challenging for alder
    
// get voters

  $data = array();

  if ($item->type == 1) { // neighborhood
    
    $sql = 'SELECT users.*, clan_members.fkey_clans_id,
      ua_ip.`value` AS last_IP, ua_sdk.`value` AS sdk

      FROM users

      LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id

      LEFT OUTER JOIN user_attributes AS ua_ip
      ON users.id = ua_ip.fkey_users_id AND ua_ip.`key` =  "last_IP"

      LEFT OUTER JOIN user_attributes AS ua_sdk
      ON users.id = ua_sdk.fkey_users_id AND ua_sdk.`key` =  "sdk"

      WHERE (
        actions_next_gain >  "%s"
        OR energy_next_gain >  "%s"
      )

      AND fkey_neighborhoods_id = %d
      AND (SUBSTR( phone_id, 0, 4 ) <>  "sdk ")
      AND ua_sdk.`value` IS NULL 
      AND username <>  "";';
    $result = db_query($sql, date('Y-m-d', time() - 1728000),
      date('Y-m-d', time() - 1728000),
      $game_user->fkey_neighborhoods_id);
    
  } else if ($item->type == 2) { // party
    
    $sql = 'SELECT users.*, clan_members.fkey_clans_id,
      ua_ip.`value` AS last_IP, ua_sdk.`value` AS sdk

      FROM users

      LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id

      LEFT OUTER JOIN user_attributes AS ua_ip
      ON users.id = ua_ip.fkey_users_id AND ua_ip.`key` =  "last_IP"

      LEFT OUTER JOIN user_attributes AS ua_sdk
      ON users.id = ua_sdk.fkey_users_id AND ua_sdk.`key` =  "sdk"

      WHERE (
        actions_next_gain >  "%s"
        OR energy_next_gain >  "%s"
      )

      AND fkey_values_id = %d
      AND (SUBSTR( phone_id, 0, 4 ) <>  "sdk ")
      AND ua_sdk.`value` IS NULL 
      AND username <>  "";';
    $result = db_query($sql, date('Y-m-d', time() - 1728000),
      date('Y-m-d', time() - 1728000), $game_user->fkey_values_id);
      
  } else if ($item->type == 3) { // district
    
    $sql = 'SELECT users.*, clan_members.fkey_clans_id,
      ua_ip.`value` AS last_IP, ua_sdk.`value` AS sdk

      FROM users

      LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id

      LEFT OUTER JOIN user_attributes AS ua_ip
      ON users.id = ua_ip.fkey_users_id AND ua_ip.`key` =  "last_IP"

      LEFT OUTER JOIN user_attributes AS ua_sdk
      ON users.id = ua_sdk.fkey_users_id AND ua_sdk.`key` =  "sdk"

      WHERE (
        actions_next_gain >  "%s"
        OR energy_next_gain >  "%s"
      )

      AND fkey_neighborhoods_id IN
        (SELECT id from neighborhoods where district = %d)
      AND (SUBSTR( phone_id, 0, 4 ) <>  "sdk ")
      AND ua_sdk.`value` IS NULL 
      AND username <>  "";';
    $result = db_query($sql, date('Y-m-d', time() - 1728000),
      date('Y-m-d', time() - 1728000), $district);
      
  }
  
  $votes_you_same_clan = $votes_you_same_party = $votes_you_influence =
    $votes_opp_same_clan = $votes_opp_same_party = $votes_opp_influence = 0;
  
  while ($voter = db_fetch_object($result)) $data[] = $voter;

  foreach ($data as $voter) {
//firep($voter);
firep('voter IP is ' . $voter->last_IP);
$ip_key = $game_user->fkey_neighborhoods_id . '_' . $voter->last_IP;
$ip_array[$ip_key]++;

// only allow first five players from same IP address to vote
   if (($ip_array[$ip_key] > 6) && (substr($voter->meta, 0, 3) != 'ai_')) {

      if ($game == 'stlouis') { // move to FP, zero actions
        $sql = 'update users set fkey_neighborhoods_id = 81, actions = 0,
          actions_next_gain = "%s", karma = karma - 100
          where id = %d;';
      } else { // CG -- move to zagros
        $sql = 'update users set fkey_neighborhoods_id = 11, actions = 0,
          actions_next_gain = "%s", karma = karma - 100
          where id = %d;';
      }

      db_query($sql, date('Y-m-d H:i:s', time() + 180), $voter->id);

      $sql = 'delete from elected_officials where fkey_users_id = %d;';
      db_query($sql, $voter->id);
/*
      mail('joseph@cheek.com', 'moving ' . $voter->username .
        ' to Forest Park', 'as s/he was voter number ' .
        $ip_array[$ip_key] . ' at IP address ' .
        $voter->last_IP . ' in ' . $location . '.');
*/
      continue; // vote doesn't count!

    } // more than 5 IPs

    if ($voter->id == $game_user->id) {

      $votes--; // you vote for yourself
firep('you vote for yourself');
    } elseif ($voter->id == $item->id) {

      $votes++; // s/he votes for her/himself
firep($item->username . ' votes for her/himself');
    } else { // other voters

/* 4th of July - check wall postings
      $sql = 'select * from user_messages
        where fkey_users_from_id = %d and fkey_users_to_id = %d
        and timestamp > "%s"
        order by timestamp DESC
        limit 1;';
      $result = db_query($sql, $voter->id, $game_user->id,
        date('Y-m-d H:i:s', time() - 259200));
      $challenger_wall = db_fetch_object($result);
if (!empty($challenger_wall)) {
firep('voter posted to challenger:');
firep($challenger_wall);
}
      $sql = 'select * from user_messages
        where fkey_users_from_id = %d and fkey_users_to_id = %d
        and timestamp > "%s"
        order by timestamp DESC
        limit 1;';
      $result = db_query($sql, $voter->id, $item->id,
        date('Y-m-d H:i:s', time() - 259200));
      $incumbent_wall = db_fetch_object($result);
if (!empty($incumbent_wall)) {
firep('voter posted to incumbent:');
firep($incumbent_wall);
}
      
// recent wall post to challenger
      if (empty($incumbent_wall) &&
        !empty($challenger_wall) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes--; // vote for you
firep($voter->username .
  ' votes for you because s/he posted to your wall recently');
        $election_polls[] =
          'I voted for you because I posted to your wall recently.';
        $votes_you_same_clan++;
        continue;
        
      }

// recent wall post to incumbent
      if (!empty($incumbent_wall) &&
        empty($challenger_wall) &&
        ((strtotime($incumbent_wall->timestamp) + 259200) > time()) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes++; // vote for opponent
firep($voter->username .
  ' votes for your opponent because s/he posted to his/her wall recently');
        $election_polls[] =
          'I voted for your opponent because I posted to his/her wall recently.';
        $votes_opp_same_clan++;
        continue;
        
      }
*/

// labor day -- all players are UWP
// $voter->fkey_clans_id = 7;

// same clan, used actions in last 3 days (challenger)
      if (($voter->fkey_clans_id > 0) &&
        ($voter->fkey_clans_id == $game_user->fkey_clans_id) &&
        ($voter->fkey_clans_id != $item->fkey_clans_id) &&
        (((strtotime($voter->actions_next_gain) + 259200) > time()) ||
        ((strtotime($voter->energy_next_gain) + 259200) > time())) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes--; // vote for you
firep($voter->username . ' votes for you because you are in the same clan');
        $election_polls[] = 'I voted for you because we are in the same clan.';
        $votes_you_same_clan++;
        continue;
        
      }

// same clan, used actions in last 3 days (incumbent)
      if (($voter->fkey_clans_id > 0) &&
        ($voter->fkey_clans_id == $item->fkey_clans_id) &&
        ($voter->fkey_clans_id != $game_user->fkey_clans_id) &&
        (((strtotime($voter->actions_next_gain) + 259200) > time()) ||
        ((strtotime($voter->energy_next_gain) + 259200) > time())) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes++; // vote for opponent
firep($voter->username . ' (' . $voter->fkey_neighborhoods_id . ') votes for ' . $item->username .
  ' because they are in the same clan');
        $election_polls[] = 'I voted for your opponent because we are in the same clan.';
        $votes_opp_same_clan++;
        continue;
        
      }

/* 4th of July - check wall postings
      $sql = 'select * from user_messages
        where fkey_users_to_id = %d and fkey_users_from_id = %d
        and timestamp > "%s"
        order by timestamp DESC
        limit 1;';
      $result = db_query($sql, $voter->id, $game_user->id,
        date('Y-m-d H:i:s', time() - 604800));
      $challenger_wall = db_fetch_object($result);
if (!empty($challenger_wall)) {
firep('challenger posted to voter:');
firep($challenger_wall);
}

      $sql = 'select * from user_messages
        where fkey_users_to_id = %d and fkey_users_from_id = %d
        and timestamp > "%s"
        order by timestamp DESC
        limit 1;';
      $result = db_query($sql, $voter->id, $item->id,
        date('Y-m-d H:i:s', time() - 604800));
      $incumbent_wall = db_fetch_object($result);
if (!empty($incumbent_wall)) {
firep('incumbent posted to voter:');
firep($incumbent_wall);
}
      

// recent wall post from challenger
      if (empty($incumbent_wall) &&
        !empty($challenger_wall) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes--; // vote for you
firep($voter->username . 
' votes for you because you posted to his/her wall');
        $election_polls[] =
          'I voted for you because you posted to my wall.';
        $votes_you_same_party++;
        continue;
        
      }

// recent wall post from incumbent
      if (!empty($incumbent_wall) &&
        empty($challenger_wall) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes++; // vote for opponent
firep($voter->username . ' votes for ' . $item->username .
  ' because s/he posted to his/her wall');
        $election_polls[] =
          'I voted for your opponent because s/he posted to my wall.';
        $votes_opp_same_party++;
        continue;
        
      }
*/

// same party, used actions in last 7 days (challenger)
      if (($voter->fkey_values_id > 0) &&
        ($voter->fkey_values_id == $game_user->fkey_values_id) &&
        ($voter->fkey_values_id != $item->fkey_values_id) &&
        (((strtotime($voter->actions_next_gain) + 604800) > time()) ||
        ((strtotime($voter->energy_next_gain) + 604800) > time())) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes--; // vote for you
firep($voter->username . ' votes for you because you are in the same party');
        $election_polls[] = 'I voted for you because we are in the same political party.';
        $votes_you_same_party++;
        continue;
        
      }

// same party, used actions in last 7 days (incumbent)
      if (($voter->fkey_values_id > 0) &&
        ($voter->fkey_values_id == $item->fkey_values_id) &&
        ($voter->fkey_values_id != $game_user->fkey_values_id) &&
        (((strtotime($voter->actions_next_gain) + 604800) > time()) ||
        ((strtotime($voter->energy_next_gain) + 604800) > time())) &&
        (mt_rand(-50,50) <= $voter->level)) {

        $votes++; // vote for opponent
firep($voter->username . ' votes for ' . $item->username .
  ' because they are in the same party');
        $election_polls[] = 'I voted for your opponent because we are in the same political party.';
        $votes_opp_same_party++;
        continue;
        
      }
      
      $vote_rand = mt_rand(0, $total_influence);
    
      if ($vote_rand < $my_influence) { // vote for me!
        $votes--; // voter votes for you
firep($voter->username . ' level ' . $voter->level . ' votes for you');
        $election_polls[] = 'I voted for you because of your ' . $experience . '.';
        $votes_you_influence++;
                
      } else {
       
        $votes++; // voter votes for incumbent
firep($voter->username . ' level ' . $voter->level . ' votes for ' . $item->username);
        $election_polls[] = 'I voted for your opponent because of his or her ' . $experience . '.';
        $votes_opp_influence++;
        
      } // undecided voter
      
    } // if voter is not in election
    
  } // foreach voter

// resident voters for type 1 (hood) elections
  $count = ($item->type == 1) ? $residents : 0;
  while ($count--) {

    $vote_rand = mt_rand(0, $total_influence);
    
    if ($vote_rand < $my_influence) { // vote for me!
    
      $votes--; // voter votes for you
firep('resident votes for you');
      $votes_you_influence++;
                
    } else {
       
      $votes++; // votes for incumbent
firep('resident votes for incumbent');
      $votes_opp_influence++;
    
    } // for whom to vote    
    
  } // foreach voter
firep('total votes are ' . $votes);
firep('voter IP array:');
firep($ip_array);

  $experience_change = mt_rand(10 + ($game_user->level * 2),
    15 + ($game_user->level * 3)); // influence changed
    
// if the same challenge has happened more than 5 times in the past hour, they
// are just trying to game the system.  Don't give anyone any experience.

  $sql = 'select count(id) as count from challenge_history
    where fkey_from_users_id = %d and fkey_to_users_id = %d
    and timestamp > "%s";';
  $result = db_query($sql, $game_user->id, $item->id, date('Y-m-d H:i:s', time() - 3600));
  $challenge_history = db_fetch_object($result);
  
  if ($challenge_history->count > 5) $experience_change = 0; // sorry!  no experience!
  

  if ($votes < 0) { // you won!  woohoo!

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-won\"/>\n-->";

    if ($item->type == 2) { // party office
// make it so s/he can't perform a major action for a day
      $set_value = '_' . arg(0) . '_set_value';
      $set_value($game_user->id, 'next_major_action', time() + 86400);
    }

    if ($item->ep_id == 1) { 
// you beat the Alderman - all officials in that neighborhood lose their seats
      
      $data = array();
      $sql = 'SELECT users.id FROM elected_officials
        left join users on elected_officials.fkey_users_id = users.id
        where fkey_neighborhoods_id = %d;';
      $result = db_query($sql, $game_user->fkey_neighborhoods_id);
      while ($official = db_fetch_object($result)) $data[] = $official;
      
      $message = t('%user1 has successfully challenged %user2 for the office ' .
        'of %office.&nbsp; You lose your seat.',
        array('%user1' => $game_user->username, '%user2' => $item->username,
          '%office' => $item->ep_name));
        
      foreach ($data as $official) {
        
        $sql = 'insert into challenge_messages (fkey_users_from_id,
          fkey_users_to_id, message) values (%d, %d, "%s");';
        $result = db_query($sql, $game_user->id, $official->id, $message);
        
      }
      
      $sql = 'delete from elected_officials
        where fkey_users_id in (
          select id from users where users.fkey_neighborhoods_id = %d
        );';
      $result = db_query($sql, $game_user->fkey_neighborhoods_id);
      $all_officials_in = $location; // set a flag
      
    } // you beat the Alderman
    
    $sql = 'delete from elected_officials where fkey_users_id = %d or
      fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id, $item->id); // incumbent lost
// and you lost your old position, if any

    $sql = 'update users set actions = 0 where id = %d;';
    $result = db_query($sql, $item->id); // incumbent loses all actions
    
// start the incumbent actions clock if needed
    if ($item->actions == $item->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $item->id);
         
    }
    
    $sql = 'insert into elected_officials set fkey_users_id = %d,
      fkey_elected_positions_id = %d;';
    $result = db_query($sql, $game_user->id, $position_id);
    
    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('%user has successfully challenged you for your office!  ' .
      'You are no longer %office and lost @exp influence.',
      array('%user' => $game_user->username, '%office' => $item->ep_name,
        '@exp' => $experience_change));
    $result = db_query($sql, $game_user->id, $item->id, $message);
    
    $sql = 'update users set experience = experience + %d,
      actions = actions - %d where id = %d;';
    $result = db_query($sql, $experience_change, $item->energy_bonus, $game_user->id);
    $sql = 'update users set experience = greatest(experience - %d, 0) where id = %d;';
    $result = db_query($sql, $experience_change, $item->id);
    
// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }
    
    $game_user = $fetch_user();
    $fetch_header($game_user);

    echo <<< EOF
<div class="title">$title</div>
EOF;

    echo '<div class="election-succeeded">' . t('Success!') . '</div>';
    echo "<div class=\"subtitle\">You beat $item->username by " .
      (0 - $votes) . " vote(s)!</div>";
      
    if ($all_officials_in)
      echo '<div class="subtitle">' .
        t('All officials in @place lose their seats',
          array('@place' => $all_officials_in)) . '</div>';
        
  } else { // you lost
    
    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"election-lost\"/>\n-->";

    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('You have successfully defended yourself against a challenge ' .
      'from %user.  You remain %office and gain @exp influence.',
      array('%user' => $game_user->username, '%office' => $item->ep_name,
        '@exp' => $experience_change));
    $result = db_query($sql, $game_user->id, $item->id, $message,
      $experience_change);
    
    $sql = 'update users set experience = greatest(experience - %d, 0),
      actions = actions - %d where id = %d;';
    $result = db_query($sql, $experience_change, $item->energy_bonus,
      $game_user->id);
    $sql = 'update users set experience = experience + %d where id = %d;';
    $result = db_query($sql, $experience_change, $item->id);

// start the actions clock if needed
    if ($game_user->actions == $game_user->actions_max) {

       $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
         $game_user->id);
         
    }
    
    $game_user = $fetch_user();
    $fetch_header($game_user);

    echo <<< EOF
<div class="title">$title</div>
EOF;

    $experience_change = min($experience_change, $game_user->experience);
    // don't tell s/he that s/he has lost more experience than s/he has
    
    echo '<div class="election-failed">' . t('Defeated') . '</div>';
    echo "<div class=\"subtitle\">You lost to $item->username by $votes" .
      " vote(s)</div><div class=\"action-effect\">" .
      t('You lost @exp @influence', array('@exp' => $experience_change,
        '@influence' => $experience_lower)) .
      '</div>';
    
  } // did you win or lose?

  echo '<div class="election-continue"><a href="/' . $game . '/hierarchies/' .
    $arg2 . '">' . t('Continue') . '</a></div>';
    
  $message = "$game_user->username [$my_influence] challenged $item->username " .
  "[$opp_influence] for the seat $item->ep_name in $location and " . 
  (($votes < 0) ? 'won' : 'lost') . " by " . abs($votes) . " votes.

{$game_user->username}'s initiative = $st_initiative_bonus->initiative staff initiative + $eq_initiative_bonus->initiative equipment initiative + 100 = $in_bonus
total influence: ceil($game_user->experience influence / 5) [" .
  ceil($game_user->experience / 5) .
  "] + ($game_user->initiative initiative * $in_bonus initiative bonus) [" .
  ceil($game_user->initiative * $in_bonus) . "] = $my_influence

Clan votes: $votes_you_same_clan
Party votes: $votes_you_same_party
Influence votes: $votes_you_influence
Extra votes: $extra_votes


{$item->username}'s endurance = $st_endurance_bonus->endurance staff endurance + $eq_endurance_bonus->endurance equipment endurance + 100 = $en_bonus
total influence: (ceil($item->experience influence / 5) [" .
  ceil($item->experience / 5) .
  "] + ($item->endurance endurance * $en_bonus endurance_bonus) [" .
  ($item->endurance * $en_bonus) .
  "]) * max(($item->approval_rating + $item->approval_15 + " .
  "$item->approval_30 + $item->approval_45) / 4, 10) [" .
  max(($item->approval_rating + $item->approval_15 +
  $item->approval_30 + $item->approval_45) / 4, 10) .
  "] approval rating * 0.017) [" .
  $opp_approval * 0.017 .
  "] = $opp_influence
  
Clan votes: $votes_opp_same_clan
Party votes: $votes_opp_same_party
Influence votes: $votes_opp_influence
Extra defending votes: $extra_defending_votes


$residents residents";

  if (($item->ep_id == 1) && ($votes < 0)) // mail me hood tosses
    mail('joseph@cheek.com', "election results" /* for $game_user->username " .
      "[$my_influence] vs. $item->username [$opp_influence] in $location" */,
      $message);
    
//  if ($item->ep_id >= 28) // and house  challenges
//    mail('joseph@cheek.com', "house seat results (district $district seat " .
//    "$item->ep_id)", $message);
    
  $sql = 'insert into challenge_history
    (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
    fkey_elected_positions_id, won, desc_short, desc_long) values
    ("election", %d, %d, %d, %d, %d, "%s", "%s");';
  $result = db_query($sql, $game_user->id, $item->id,
    $game_user->fkey_neighborhoods_id, $position_id, (($votes < 0) ? 1 : 0),
    "$game_user->username challenged $item->username " .
    "for the seat $item->ep_name in $location and " . 
    (($votes < 0) ? 'won' : 'lost') . " by " . abs($votes) . " votes.",
    $message);

  echo "<div class=\"subtitle\">Election poll results:</div>";
  $total_polls = 2;
  
  for ($c = 0 ; $c < $total_polls ; $c++) {
    
    $c1 = mt_rand(0, count($election_polls));
    
    echo "<div class=\"action-effect\">&quot;{$election_polls[$c1]}&quot;</div>";
    unset($election_polls[$c1]); // so we don't get dups
    
    
  }
  
  db_set_active('default');  
