<?php

  global $game, $phone_id, $action;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  include_once(drupal_get_path('module', $game) . '/' . $game .
    '_actions.inc');
  include_once(drupal_get_path('module', $game) . '/' . $game .
    '_actions_do.inc');
  $arg2 = check_plain(arg(2));

  if (($game_user->meta == 'frozen') && ($phone_id != 'abc123')) {

    $fetch_header($game_user);

    echo <<< EOF
<div class="title">Frozen!</div> 
<div class="subtitle">You have been tagged and cannot perform any actions</div>
<div class="subtitle">Call on a teammate to unfreeze you!</div>
EOF;

  db_set_active('default');
  return;

  }

  $sql = 'SELECT actions.*, eq2.name as eq2_name
  from actions

  left join equipment as eq2 on fkey_equipment_2_id = eq2.id

  where actions.id = %d;';

  $result = db_query($sql, $action_id);
  $action = db_fetch_object($result);
  firep($action);

  $data = actionlist();
  
  foreach ($data as $item) {
    $list_of_actions[] = $item->id ;
  }

// check to see if action is allowed for user

  if ((!in_array($action->id, $list_of_actions)) &&
    (substr($arg2, 0, 3) != 'ai-')) { // hacking!

    _karma($game_user, 'actions hacking!', -20);

    db_set_active('default');
    drupal_goto($game . '/home/' . $arg2);

  }

  if (substr($_GET['target'], 0, 7) == 'letter_') { // show list

    echo <<< EOF
<div class="title">
$action->name
</div>
<div class="subtitle">
Please select a target
</div>
EOF;

    db_set_active('default');
    return;  

  }

// check to see if valid target is chosen

  if (($_GET['target'] == 0) && ($action->target != 'none')) {

    $fetch_header($game_user);

    echo '<div class="election-failed">' . t('Failure') . '</div>';
    echo "<div class=\"subtitle\">You must choose a target</div>";
    echo '<div class="election-continue"><a href="/' . $game . '/actions/' .
      $arg2 . '">' . t('Try again') . '</a></div>';

    db_set_active('default');
    return;

  }

  if ($_GET['target'] != '') {

    $sql = 'SELECT username, phone_id, elected_positions.name as ep_name,
      elected_positions.id as ep_id, experience, fkey_neighborhoods_id, luck,
      level from users
    
      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id
      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      where users.id = %d';

    $result = db_query($sql, $_GET['target']);
    $target = db_fetch_object($result);
    firep($target);

  }

  $name = str_replace('%value', $game_user->values, $action->name);

  $title = "$name $action->preposition $target->ep_name $target->username";

  $action_succeeded = $can_do_again = TRUE;
  $outcome_reason = '<div class="land-succeeded">' . t('Success!') .
    '</div>';
  $ai_output = 'action-succeeded';

// check to see if user has enough actions

  if ($game_user->actions < $action->cost) {
   
    $action_succeeded = FALSE;

    if (substr($phone_id, 0, 3) == 'ai-')
      $ai_output = 'action-failed no-action';

      $outcome_reason = '
        <div class="land failed">
          <div class="title">' .
            t('∆ Action Quantity ∆<br/><span>Insufficient</span>') . '
          </div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/elders_do_fill/' . $arg2 .
                '/action?destination=/' . $game . '/actions/' . $arg2 . '">
                Refill your Action (2&nbsp;Luck)
              </a>
            </div>
          </div>
        </div>';
   
  }

// check to see if user has enough money

  if (($game_user->money < $action->values_cost) &&
    ($action->values_cost > 0)) {
   
    $action_succeeded = FALSE;
    
    if (substr($phone_id, 0, 3) == 'ai-')
      $ai_output = 'action-failed no-money';
   
    $offer = ($game_user->income - $game_user->expenses) * 5;
    $offer = min($offer, $game_user->level * 1000);
    $offer = max($offer, $game_user->level * 100);

    $outcome_reason = '
      <div class="land failed">
        <div class="title">' .
          t('∆ @value <span>Insufficient</span> ∆',
            array('@value' => $game_user->values)) . '
        </div>
        <div class="try-an-election-wrapper">
          <div  class="try-an-election">
            <a href="/' . $game . '/elders_do_fill/' . $arg2 .
              '/money?destination=/' . $game . '/actions/' . $arg2 . '">' .
              t('Receive @offer @values (1&nbsp;@luck',
                array(
                  '@offer' => $offer,
                  '@values' => $game_user->values,
                  '@luck' => $luck,
                )) . ')
            </a>
          </div>
        </div>
      </div>';
  
  }
   
// check to see if clan has enough money

  if ($action->clan_values_cost > 0) {

    $sql = 'select money from clans
      where id = %d;';
    $result = db_query($sql, $game_user->fkey_clans_id);
    $clan_money = db_fetch_object($result);

    if (($clan_money->money < $action->clan_values_cost) &&
      ($action->values_cost > 0)) {
   
      $action_succeeded = FALSE;

      if (substr($phone_id, 0, 3) == 'ai-')
        $ai_output = 'action-failed no-clan-money';

      $outcome_reason = '
        <div class="land failed">
          <div class="title">' .
            t('∆ Clan @value ∆<br/><span>Insufficient</span>',
              array('@value' => $game_user->values)) . '
          </div>
        </div>';

    } // not enough clan money

  } // a clan money cost?
   
// check to see if user has equipment_2

  if ($action->fkey_equipment_2_id > 0) {

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $action->fkey_equipment_2_id, $game_user->id);
    $eq2 = db_fetch_object($result);

    if ($eq2->quantity == 0) {

      $action_succeeded = FALSE;
      $outcome_reason = '<div class="land failed">
          <div class="title">' .
            t('∆ Action Failed ∆') .
          '</div>
          <div class="title">' .
            t('@name Quantity: <span>Zero</span>',
              array('@name' => $action->eq2_name)) .
          '</div>
         </div>';
    
      if (substr($phone_id, 0, 3) == 'ai-')
        $ai_output = 'action-failed no-required-equipment';

    }
   
  }
   
  $action_function = '_' . $game . '_action_' .
    strtolower(str_replace(
      array(' ', '%', "'", '.', '(', ')', '<sup>', '</sup>'),
      array('_', '', '', '', '', '', '', ''),
      $action->name)) .
    '_function';
// firep("function is $action_function");

  if ($action_succeeded && function_exists($action_function))
    $action_succeeded = $action_function($outcome_reason, $target,
      $can_do_again);

if ($action_succeeded) {

// special case for investigate someone
  if ($action_function == '_stlouis_action_investigate_a_public_official_function') {
    $show_all = '?show_all=yes';
  } else {
    $show_all = '';
  }

/*    
// special case for meet someone new in Fairground Park
  if (($action_function == '_stlouis_action_meet_someone_new_function') &&
    ($game_user->fkey_neighborhoods_id == 80)) {
    $show_all = '?want_jol=yes';
  }
*/
  
// decrement available actions
  $sql = 'update users set actions = actions - %d where id = %d;';
  $result = db_query($sql, $action->cost,  $game_user->id);

// start the actions clock if needed
  if ($game_user->actions == $game_user->actions_max) {

    $sql = 'update users set actions_next_gain = "%s" where id = %d;';
    $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
      $game_user->id);

  }

  // affect influence

  if ($action->influence_change != 0) {

    $inf_change = $action->influence_change;

    if ($action->target == 'none') { // no target - actions affect player
        
      $target_name = 'Your';
      $target_id = $game_user->id;
        
    } else { // there is a target involved
        
      $target_name = $target->ep_name . ' ' . $target->username . '\'s';
      $target_id = $_GET['target'];
        
    }

    $sql = 'update users set experience = greatest(experience + %d, 0)
      where id = %d;';
    $result = db_query($sql, $inf_change,  $target_id);
    $outcome_reason .= '<div class="action-effect">' . $target_name .
         ' ' . $experience . ' is ' . 
    (($inf_change > 0) ? 'increased' : 'decreased') .
        ' by ' . abs($inf_change) . '</div>';
    
    // now save the record of what happened, if positive and not done to yourself
    
    if (($game_user->id != $target_id) && ($inf_change > 0)) {
      $sql = 'insert into challenge_history 
        (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
        fkey_elected_positions_id, won, desc_short, desc_long) values
        ("gift", %d, %d, %d, %d, %d, "%s", "%s");';
      $result = db_query($sql, $game_user->id, $target_id,
        $game_user->fkey_neighborhoods_id, $target->ep_id, 0,
        "$game_user->username gave a gift of $experience to " . 
        substr($target_name, 0, strlen($target_name) - 2) . '.',
        "$game_user->username gave a gift of $inf_change $experience " .
        'to ' . substr($target_name, 0, strlen($target_name) - 2) .
        ' (currently ' . $target->experience . ').');
    }

  } // change influence

  // affect ratings

  if ($action->rating_change != 0) {

    $rat_change = $action->rating_change;

    if (($action->target == 'none') || ($action->rating_change >= 0.10)) {
      // no target or larger positive ratings - actions affect player

      $target_name = 'Your';
      $target_id = $game_user->id;
        
    } else { // target, negative, or smaller positive ratings
        
      $target_name = $target->ep_name . ' ' . $target->username . '\'s';
      $target_id = $_GET['target'];
        
    }

    // affect rating
    $sql = 'update elected_officials
      set approval_rating = greatest(least(approval_rating + %f, 100), 0)
      where fkey_users_id = %d;';
    $result = db_query($sql, $rat_change,  $target_id);

    // get new rating
    $sql = 'select approval_rating from elected_officials
        where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $rating = db_fetch_object($result);

    $outcome_reason .= '<div class="action-effect">' . $target_name .
        ' approval rating is changed by ' .
    $rat_change . '% (now at ' . $rating->approval_rating . '%)</div>';

  }
  
// change hood rating
  
  if ($action->neighborhood_rating_change != 0) {

    $rat_change = $action->neighborhood_rating_change;

    // affect rating
    $sql = 'update neighborhoods
        set rating = greatest(0, rating + %f) where id = %d;';
    $result = db_query($sql, $rat_change,  $game_user->fkey_neighborhoods_id);

    // get new rating
    $sql = 'select name, rating from neighborhoods
        where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $hood = db_fetch_object($result);

    $outcome_reason .= '<div class="action-effect">' . $hood->name .
        '\'s neighborhood ' . $beauty_lower . ' rating is changed by ' .
    $rat_change . ' (now at ' . $hood->rating . ')</div>';

  } // change hood rating

// values COST (ie, what you pay)

  if ($action->values_cost != 0) {

    $sql = 'update users set money = money - %d where id = %d;';
    $result = db_query($sql, $action->values_cost, $game_user->id);
    $outcome_reason .= '<div class="action-effect">
        // Your ' . $game_user->values . ' are decreased by ' .
          $action->values_cost . ' //
      </div>';

  }

// clan values COST (ie, what your clan pays)

  if ($action->clan_values_cost != 0) {

    $sql = 'update clans set money = money - %d where id = %d;';
    $result = db_query($sql, $action->clan_values_cost,
      $game_user->fkey_clans_id);
    $outcome_reason .= '<div class="action-effect">
        // Your clan\'s ' . $game_user->values . ' are decreased by ' .
          $action->clan_values_cost . ' //
      </div>';

  }

// values CHANGE (ie, what target gets)

  if ($action->values_change != 0) {
     
    $target_name = $target->ep_name . ' ' . $target->username . '\'s';
    $target_id = $_GET['target'];
    
    $sql = 'select money from users where id = %d;';
    $result = db_query($sql, $target_id);
    
    if ($action->values_change > 0) {
      $verb = 'increased';
      $money = $action->values_change;
    } else {
      $verb = 'decreased';
      $item = db_fetch_object($result);
      $money = -min(-$action->values_change, $item->money);
    }

//    $sql = 'update users set money = greatest(money + %d, 0) where id = %d;';
    $sql = 'update users set money = money + %d where id = %d;';
    $result = db_query($sql, $money, $target_id);
    $outcome_reason .= '<div class="action-effect">' . $target_name . ' ' .
    $game_user->values . ' is ' . $verb . ' by ' . abs($money) . '</div>';
    
    if ($action->values_change < 0) {
      $can_do_again = FALSE;
      
      $sql = 'update users set money = money + %d where id = %d;';
      $result = db_query($sql, abs(floor($money / 2)), $game_user->id);
      $outcome_reason .= '<div class="action-effect">You gain half</div>';
    
    }      

  }
  
  if ($action->actions_change != 0) {
     
    $target_name = $target->ep_name . ' ' . $target->username . '\'s';
    $target_id = $_GET['target'];
    
    $sql = 'select actions from users where id = %d;';
    $result = db_query($sql, $target_id);
    
    if ($action->actions_change > 0) {
      $verb = 'increased';
      $act_change = $action->actions_change;
    } else {
      $verb = 'decreased';
      $item = db_fetch_object($result);
      $act_change = -min(-$action->actions_change, $item->actions);
    }

    $sql = 'update users set actions = greatest(actions + %d, 0) where id = %d;';
    $result = db_query($sql, $act_change, $target_id);
    $outcome_reason .= '<div class="action-effect">' . $target_name .
     ' Action is ' . $verb . ' by ' . abs($act_change) . '</div>';
    
    if ($action->actions_change < 0) {
      $can_do_again = FALSE;
      
      $sql = 'update users set actions = actions + %d where id = %d;';
      $result = db_query($sql, abs(floor($act_change / 2)), $game_user->id);
      $outcome_reason .= '<div class="action-effect">You gain half</div>';
    
    }      

  }
  
  // chance of loss - equipment
  if ($action->fkey_equipment_id) { // any equipment for this action?

    list($lost, $failure_reason, $quantity_left) = 
      equipment_check_wear_out($game_user, $action->fkey_equipment_id);
    $outcome_reason .= '<div class="subtitle">' .
        $failure_reason . '
      </div>';
    ($quantity_left == 0) && $can_do_again = FALSE;
      
  } // any equipment?

// chance of loss - aides
  if ($action->fkey_staff_id) { // any staff for this action?

    $sql = 'select * from staff where id = %d;';
    $result = db_query($sql, $action->fkey_staff_id);
    $st = db_fetch_object($result);
firep($st);
  
    if ($st->chance_of_loss >= mt_rand(1,110)) { // did it wear out?
      
firep($st->name . ' has run away!');
      $sql = 'update staff_ownership set quantity = quantity - 1
        where fkey_staff_id = %d and fkey_users_id = %d;';
      $result = db_query($sql, $st->id, $game_user->id);
      
// player expenses need resetting?

      if ($st->upkeep > 0) { // subtract upkeep from your expenses
        $sql = 'update users set expenses = expenses - %d where id = %d;';
        $result = db_query($sql, $st->upkeep, $game_user->id);
      } // FIXME: do this before _stlouis_header so that upkeep is accurate
      
      $outcome_reason .= '<div class="subtitle">' . 
        t('Your @staff has/have run away or been caught',
        array('@staff' => strtolower($st->name))) . '</div>';
        
      $sql = 'select quantity from staff_ownership
        where fkey_staff_id = %d and fkey_users_id = %d;';
      $result = db_query($sql, $st->id, $game_user->id);
      $so = db_fetch_object($result);
      
      if ($so->quantity == 0) $can_do_again = FALSE;
      
    } else {
      
      $outcome_reason .= '<div class="subtitle">&nbsp;</div>';
      
firep($st->name . ' did NOT run away');

    } // did anything wear out?

  } // any equipment?
  
    $whom = $target->ep_name . ' ' . $target->username;
    if ($whom == ' ') $whom = 'your profile';

    $outcome_reason .= '<div class="try-an-election-wrapper">
        <div class="try-an-election">
          <a href="/' . $game . '/user/' . $arg2 . '/' . $target->phone_id . 
            $show_all . '">View ' . $whom . 
          '</a>
        </div>
      </div>';

// check action list again, to see if action is still available
  $data = actionlist();
  $list_of_actions = array();
  
  foreach ($data as $item) {
    $list_of_actions[] = $item->id ;
  }

  if ((!in_array($action->id, $list_of_actions)) &&
    (substr($arg2, 0, 3) != 'ai-')) { // not in list
    $can_do_again = FALSE;
  }
   
    if ($can_do_again) {
      $outcome_reason .= '<div class="try-an-election-wrapper"><div
      class="try-an-election"><a
      href="/' . $game . '/actions_do/' . $arg2 . '/' . $action_id .
      '?target=' . $_GET['target'] . 
      '">Do it again</a></div></div>';
    } else {
      $outcome_reason .= '<div class="try-an-election-wrapper"><div
      class="try-an-election not-yet">Can\'t do it again</div></div>';
    }

    $outcome_reason .= '<div class="try-an-election-wrapper"><div
      class="try-an-election"><a
      href="/' . $game . '/actions/' . $arg2 .
      '">Perform a different action</a></div></div>';
   
    $game_user = $fetch_user(); // reprocess user object

} else { // failed - try a different action
   
  $outcome_reason .= '<div class="try-an-election-wrapper">
      <div class="try-an-election">
        <a href="/' . $game . '/actions/' . $arg2 . '">
          Perform a different action
        </a>
      </div>
    </div>';

  $ai_output = 'action-failed';
   
} // action succeeded

  $fetch_header($game_user);

  _show_goal($game_user);

  echo <<< EOF
<div class="title">
  $title
</div>
$outcome_reason
EOF;

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output " .
    filter_xss($outcome_reason, array()) .
    " \"/>\n-->";

  db_set_active('default');
