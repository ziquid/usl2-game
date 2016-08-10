<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
//  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));

  $reset_me = check_plain(trim($_GET['reset_me']));
  
  if (strtoupper($reset_me) == 'RESET ME') { // to prevent errant resets
    
    $sql = 'delete from elected_officials where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'UPDATE bank_accounts SET active = 0 where fkey_users_id = %d';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from user_competencies where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from equipment_ownership where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from land_ownership where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from staff_ownership where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
       
    $sql = 'delete from quest_completion where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from quest_group_completion where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);

    $sql = 'delete from user_messages where fkey_users_to_id = %d;';
    $result = db_query($sql, $game_user->id);

    $sql = 'delete from challenge_messages where fkey_users_to_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'delete from goals_achieved where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
    
    if ($item->is_clan_leader) { // clan leader? delete entire clan
      
      $sql = 'delete from clan_messages where fkey_neighborhoods_id = %d;';
      $result = db_query($sql, $game_user->fkey_clans_id);
      $sql = 'delete from clan_members where fkey_users_id = %d;';
      $result = db_query($sql, $item->id);
      $sql = 'delete from clans where id = %d;';
      $result = db_query($sql, $item->id);
      
    } else {
      
      $sql = 'delete from clan_members where fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);
      
    }

    $sql = 'delete from quest_completion where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    
    if ($game == 'stlouis') {
      $default_neighborhood = 75;
      $default_value = 'Credits';
    }

    if ($game_user->luck > 10) {

      $luck_in_sql = $game_user->luck;

    } else {

      $luck_in_sql = 10;

    }
    
    $sql = "UPDATE users 
      SET username = '',
      password = '',
      referral_code = '', 
      referred_by = '', 
      experience = 0, 
      level = 1,
      fkey_neighborhoods_id = %d, 
      fkey_values_id = 0, 
      `values` = '%s', 
      money = 1000, 
      energy = 200, 
      energy_max = 200, 
      energy_next_gain = CURRENT_TIMESTAMP, 
      income = 0, 
      expenses = 0, 
      income_next_gain = '0000-00-00 00:00:00', 
      actions = 3,
      actions_max = 3, 
      actions_next_gain = '0000-00-00 00:00:00', 
      initiative = 1, 
      endurance = 1, 
      elocution = 1, 
      debates_won = 0, 
      debates_lost = 0,
      debates_last_time = '0000-00-00 00:00:00', 
      last_bonus_date = '0000-00-00', 
      favors_completed = 0,
      favors_asked_completed = 0,
      favors_asked_noncompleted = 0,
      skill_points = 0,
      luck = %d,
      seen_neighborhood_quests = 0,
      fkey_last_played_quest_groups_id = 0
      WHERE id = %d;";
    $result = db_query($sql, $default_neighborhood, $default_value,
      $luck_in_sql, $game_user->id);
  
    drupal_goto($game . '/welcome/' . $arg2);
  
  } else {
    
    drupal_goto($game . '/elders_ask_reset/' . $arg2, 'msg=error');
    
  }
