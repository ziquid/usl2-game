<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $referral_code = check_plain($_GET['referral_code']);
  
  if ((substr($phone_id, 0, 3) == 'sdk') ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'vm Build/MASTER') !== FALSE) ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'sdk Build') !== FALSE)) {
      
      echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;I'm sorry, but emulator users cannot enter referral codes.</p>
  <p class="second">&quot;Please choose a $party_lower instead.&quot;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
  <div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/choose_clan/$arg2/0">Choose a $party_lower</a>
  </div>
</div>
</div>
EOF;

    db_set_active('default');
      
    return;
      
  }

  if (!empty($referral_code)) {
    
    if ($referral_code == $game_user->referral_code) // no change?  just show stats
      drupal_goto($game . '/user/' . $arg2);
      
// have they already used that code?

    $sql = 'select referred_by from user_creations
      where phone_id = "%s" and referred_by <> "";';
    $result = db_query($sql, $phone_id);
    $item = db_fetch_object($result);
    
    $sql = 'SELECT count( id ) as count
      FROM `user_creations`
      WHERE remote_ip = "%s"
      AND datetime >= "%s"
      AND referred_by <> "";';
    $result = db_query($sql, $_SERVER['REMOTE_ADDR'],
      date('Y-m-d', time() - 86400));
    $item2 = db_fetch_object($result);
    
    if (!empty($item->referred_by) ||
      ($item2->count > 5)) { // houston, we have a problem
// user is trying to enter a code more than once from same phone
// or more than 5 referral codes from same IP in 24 hours

      echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;I'm sorry, but you are only allowed one referral code per device.</p>
  <p class="second">&quot;Please choose a $party_lower instead.&quot;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
  <div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/choose_clan/$arg2/0">Choose a $party_lower</a>
  </div>
</div>
</div>
EOF;

      db_set_active('default');
        
      return;
        
    }
      
    $sql = 'SELECT username, experience, initiative, endurance, 
      elocution, debates_won, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, referral_code,
      `values`.party_title, `values`.party_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
      elected_officials.approval_rating,
      clan_members.is_clan_leader,
      clans.name as clan_name, clans.acronym as clan_acronym,
      clans.id as fkey_clans_id,
      neighborhoods.name as neighborhood
    
      FROM `users`
    
      LEFT JOIN `values` ON users.fkey_values_id = `values`.id
    
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
    
      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id
    
      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id
    
      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
      
      LEFT OUTER JOIN neighborhoods on neighborhoods.id = users.fkey_neighborhoods_id
    
      WHERE users.referral_code = "%s"';
    $result = db_query($sql, $referral_code);
    $mentor = db_fetch_object($result);
firep($mentor);

    if (empty($mentor)) {
      
      echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;I'm sorry, but that referral code is not valid.</p>
  <p class="second">&quot;Please try again.&quot;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
  <div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/choose_clan/$arg2/0">Try again</a>
  </div>
</div>
</div>
EOF;

      db_set_active('default');
      
      return;
      
    }
    
    $icon = $game . '_clan_' . $mentor->party_icon . '.png';
    $party_title = preg_replace('/^The /', '', $mentor->party_title);
    
    if (!empty($mentor->clan_acronym))
      $clan_html = " and the <span class=\"highlighted\">$mentor->clan_name
        ($mentor->clan_acronym)</span> clan";
    
    echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;Wonderful!&nbsp; Your mentor is
    <span class="highlighted">$mentor->username</span>.</p>
  <p class="second">&quot;He or she belongs to the
    <span class="highlighted">$party_title</span> political party.</p>
  <p class="second">&quot;You will start out in the same $hood_lower as your
    mentor &mdash; <span class="highlighted">$mentor->neighborhood</span>.</p>
  <p class="second">&nbsp;</p>
  <div class="try-an-election-wrapper">
    <div class="try-an-election">
      <a href="/$game/debates/$arg2">Continue</a>
    </div>
  </div>
</div>
EOF;

// update his/her user entry
    $sql = 'update users set fkey_neighborhoods_id = %d, referred_by = "%s",
      fkey_values_id = %d, `values` = "%s", money = money + 1000 
      where id = %d;';
    $result = db_query($sql, $mentor->fkey_neighborhoods_id, $referral_code,
      $mentor->fkey_values_id, $mentor->name, $game_user->id);
      
    $sql = 'update user_creations set referred_by = "%s" where phone_id = "%s"
      order by datetime desc limit 1;';
    $result = db_query($sql, $referral_code, $game_user->phone_id);

// give the referrer an experience bonus
    $sql = 'update users set experience = experience + 1000 where id = %d;';
    $result = db_query($sql, $mentor->id);

// tell them about each other
    $sql = 'insert into user_messages (fkey_users_from_id, fkey_users_to_id,
      message) values (%d, %d, "%s");';
    $message = 'Hi!&nbsp; I am a new user who used your referral code to join
      the game!&nbsp; You have gained
      1000 ' . $experience_lower . '.&nbsp; Please welcome me to ' .
      $mentor->party_title . '.';
    $result = db_query($sql, $game_user->id, $mentor->id, $message);
    
    $message = 'Welcome to the game!&nbsp; I am your mentor and will gladly
      answer any questions you have about the game.&nbsp; Touch the
      <strong>View / Respond</strong> button next to this message to send me a
      message back.';
    $result = db_query($sql, $mentor->id, $game_user->id, $message);
    
    db_set_active('default');
    
    return;
    
  }
  
// otherwise they have not chosen a clan or are rechoosing one
    
  echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;What is your referral code?&nbsp; It is a mixture of five letters
    and numbers.&quot;</p>
  <p class="second">Example: <font size=+1><strong>AB123</strong></font></p>
  <p class="second">&nbsp;</p>
  <form method=get action="/$game/enter_referral_code/$arg2">
    <input type="text" name="referral_code" size="5" maxlength="5"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

  db_set_active('default');
