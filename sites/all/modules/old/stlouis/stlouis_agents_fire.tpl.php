<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if ($quantity === 'use-quantity') {
    $quantity = check_plain($_GET['quantity']);

//    mail('joseph@cheek.com', "agents_fire with use-quantity of $quantity",
//     "user $game_user->username");

  }

  $sql = 'select name from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;
  
  $sql = 'select party_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $party_title = preg_replace('/^The /', '', $data->party_title);
  
  $data = array();
  $sql = 'SELECT staff.*, staff_ownership.quantity
    FROM staff
    
    LEFT OUTER JOIN staff_ownership
      ON staff_ownership.fkey_staff_id = staff.id
      AND staff_ownership.fkey_users_id = %d
    
    WHERE staff.id = %d;';
  $result = db_query($sql, $game_user->id, $staff_id);
  $game_staff = db_fetch_object($result); // limited to 1 in DB
  $orig_quantity = $quantity;
  $staff_price = ceil($game_staff->price * $quantity * 0.6);
/* allow for 80% sale price
  $staff_price = ceil($game_staff->price +
    ($game_staff->price_increase * ($game_staff->quantity - $quantity))
     * $quantity * 0.8);
*/
  $staff_succeeded = TRUE;
  $outcome_reason = '<div class="land-succeeded">' . t('Success!') .
    '</div>';
  
// check to see if staff prerequisites are met

// hit a quantity limit?

  if ($quantity > $game_staff->quantity) {
    
    $staff_succeeded = FALSE;
    $outcome_reason = '<div class="land-failed">' . t('You don\'t have that many!') .
      '</div>';
    
  }

// can't sell?
  if ($game_staff->can_sell != 1) {

    $staff_succeeded = FALSE;
    $outcome_reason = '<div class="land-failed">' . t('Sorry!') .
      '</div><div class="subtitle">' .
      t('This item cannot be sold') .
      '</div><br/>';

  }
  
  if ($staff_succeeded) {
    
    $sql = 'update staff_ownership set quantity = quantity - %d where
      fkey_staff_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $quantity, $staff_id, $game_user->id);
    
    $sql = 'update users set money = money + %d, income = income - %d, 
      expenses = expenses - %d
      where id = %d;';
    $result = db_query($sql, $staff_price,
      $game_staff->income * $quantity,
      $game_staff->upkeep * $quantity,
      $game_user->id);

    if (substr($game_user->income_next_gain, 0, 4) == '0000') { // start the income clock if needed

       $sql = 'update users set income_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 3600),
         $game_user->id);
      
    }

    $game_user = $fetch_user(); // reprocess user object
    
  } else { // failed - add option to try an election
    
    $outcome .= '<div class="try-an-election-wrapper"><div
      class="try-an-election"><a
      href="/' . $game . '/hierarchies/' . $arg2 . '">Run for
      office instead</a></div></div>';

    $quantity = 0;
    
  } // fire staff succeeded

  $fetch_header($game_user);
  
  echo <<< EOF
<div class="news">
  <a href="/$game/land/$arg2" class="button">Businesses</a>
  <a href="/$game/equipment/$arg2" class="button">Equipment</a>
  <a href="/$game/staff/$arg2" class="button">Staff</a>
  <a href="/$game/agents/$arg2" class="button active">Agents</a>
</div>  
EOF;
  
  if ($game_user->level < 20) {
  
    echo <<< EOF
<ul>
  <li>Hire agents to help you win elections and stay elected</li>
</ul>
EOF;

  } // user level < 20
//firep("game_staff->quantity: $game_staff->quantity");
//firep("quantity: $quantity");
  
  $quantity = (int) $game_staff->quantity - (int) $quantity;
  $staff_price = $game_staff->price + ($quantity * $game_staff->price_increase);
  
  if ($quantity == 0) $quantity = '<em>None</em>'; // gotta love PHP typecasting
  
  if ($game_staff->quantity_limit > 0) {
    $quantity_limit = '<em>(Limited to ' . $game_staff->quantity_limit . ')</em>';
  } else {
    $quantity_limit = '';
  }
    
  echo <<< EOF
<div class="land">
  $outcome_reason
  <div class="land-icon"><a
    href="/$game/agents_hire/$arg2/$game_staff->id/1"><img
    src="/sites/default/files/images/staff/$game-$game_staff->id.png"
    border="0" width="96"></a></div>
  <div class="land-details">
    <div class="land-name"><a
      href="/$game/agents_hire/$arg2/$game_staff->id/1">$game_staff->name</a></div>
    <div class="land-owned">Hired: $quantity $quantity_limit</div>
    <div class="land-cost">Cost: $staff_price $game_user->values</div>
EOF;

    if ($game_staff->initiative_bonus > 0) {
      
      echo <<< EOF
    <div class="land-payout">$initiative: +$game_staff->initiative_bonus</div>
EOF;

    }
    
    if ($game_staff->endurance_bonus > 0) {
      
      echo <<< EOF
    <div class="land-payout">$endurance: +$game_staff->endurance_bonus</div>
EOF;

    }

    if ($game_staff->experience_bonus > 0) {
      
      echo <<< EOF
    <div class="land-payout">$experience: +$game_staff->experience_bonus</div>
EOF;

    }
    
    echo <<< EOF
  </div>
  <div class="land-button-wrapper">
    <form action="/$game/agents_fire/$arg2/$game_staff->id/use-quantity">
      <div class="quantity">
        <select name="quantity">
EOF;

  foreach (array(1, 5, 10, 25, 50, 100) as $option) {

    if ($option == $orig_quantity) {
      echo '<option selected="selected" value="' . $option . '">' .
        $option . '</option>';
    } else {
      echo '<option value="' . $option . '">' . $option . '</option>';
    }

  }

  echo <<< EOF
        </select>
      </div>
      <input class="land-buy-button" type="submit" Value="Fire"/>
    </form>
  </div>
</div>

<div class="title">
Hire Agents
</div>
EOF;
    
  $data = array();
  $sql = 'SELECT staff.*, staff_ownership.quantity 
    FROM staff
    
    LEFT OUTER JOIN staff_ownership ON staff_ownership.fkey_staff_id = staff.id
    AND staff_ownership.fkey_users_id = %d

    WHERE ((
      fkey_neighborhoods_id = 0
      OR fkey_neighborhoods_id = %d
    ) 
    
    AND
    
    (
      fkey_values_id = 0
      OR fkey_values_id = %d
    ))
  
    AND required_level <= %d
    AND active = 1
    AND is_loot = 0
    AND staff_or_agent = "a"
    ORDER BY required_level ASC';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;
  
  foreach ($data as $item) {
//firep($item);
    
    $description = str_replace('%clan', "<em>$party_title</em>",
      $item->description);
      
    $quantity = $item->quantity;
    if (empty($quantity)) $quantity = '<em>None</em>';

    $agents_price = $item->price + ($item->quantity *
      $item->price_increase);

    if ($item->quantity_limit > 0) {
      $quantity_limit = '<em>(Limited to ' . $item->quantity_limit . ')</em>';
    } else {
      $quantity_limit = '';
    }
    
    echo <<< EOF
<div class="land">
  <div class="land-icon"><a href="/$game/agents_hire/$arg2/$item->id/1"><img
    src="/sites/default/files/images/staff/$game-$item->id.png" border="0"
    width="96"></a></div>
  <div class="land-details">
    <div class="land-name"><a
      href="/$game/agents_hire/$arg2/$item->id/1">$item->name</a></div>
    <div class="land-description">$description</div>
    <div class="land-owned">Hired: $quantity $quantity_limit</div>
    <div class="land-cost">Cost: $agents_price $game_user->values</div>
EOF;

    if ($item->initiative_bonus > 0) {
      
      echo <<< EOF
    <div class="land-payout">Initiative: +$item->initiative_bonus</div>
EOF;

    }

    if ($item->endurance_bonus > 0) {
      
      echo <<< EOF
    <div class="land-payout">$endurance: +$item->endurance_bonus</div>
EOF;

    }

    if ($item->upkeep > 0) {
      
      echo <<< EOF
    <div class="land-payout negative">Upkeep: $item->upkeep every 60 minutes</div>
EOF;

    } // upkeep
    
    if ($item->chance_of_loss > 0) {
      
      $lifetime = floor(100 / $item->chance_of_loss);
       $use = ($lifetime == 1) ? 'use' : 'uses';
      echo <<< EOF
    <div class="land-payout negative">Expected Lifetime: $lifetime $use</div>
EOF;

    } // expected lifetime

// grab each action for an agent

    $data2 = array();
    $sql = 'select * from actions where fkey_staff_id = %d;';
    $result = db_query($sql, $item->id);

    while ($action = db_fetch_object($result)) $data2[] = $action;
  
    foreach ($data2 as $action) {
//firep($action);

      $name = str_replace('%value', $game_user->values, $action->name);
    
      echo '<div class="land-action">Action: ' . $name . '</div>';
      echo '<div class="land-description">' . $action->description . '</div>';
      echo '<div class="land-action-cost">Cost: ' . $action->cost . 
        ' Action</div>';
      
      if ($action->influence_change < 0) {
        
        $inf_change = -$action->influence_change;
        
        echo <<< EOF
          <div class="land-payout negative">Effect: Target's $experience is
            reduced by $inf_change</div>
EOF;

      } // if influence_change < 0
      
      if (($action->rating_change < 0.10) && ($action->rating_change != 0.0)) {
        
        $rat_change = abs($action->rating_change);
        
        if ($action->rating_change < 0.0) {
          
          echo <<< EOF
      <div class="land-payout negative">Effect: $target approval rating is
        reduced by $rat_change%</div>
EOF;
        
        } else {
  
          echo <<< EOF
      <div class="land-payout">Effect: $target approval rating is
        increased by $rat_change%</div>
EOF;
          
        }
  
      } // if rating_change < 0.10
      
      if ($action->rating_change >= 0.10) {
          
        $rat_change = $action->rating_change;
          
        echo <<< EOF
      <div class="land-payout">Effect: Your approval rating is
        increased by $rat_change%</div>
EOF;
  
      } // if rating_change >= 0.10
      
      if ($action->neighborhood_rating_change < 0.0) {
          
        $rat_change = -$action->neighborhood_rating_change;
          
        echo <<< EOF
      <div class="land-payout negative">Effect: Neighborhood $beauty_lower rating is
        reduced by $rat_change</div>
EOF;
  
      } // if hood rating_change < 0
      
      if ($action->neighborhood_rating_change > 0.0) {
          
        $rat_change = $action->neighborhood_rating_change;
          
        echo <<< EOF
      <div class="land-payout">Effect: Neighborhood $beauty_lower rating is
        increased by $rat_change</div>
EOF;
  
      } // if hood rating_change > 0
      
      if ($action->values_change < 0) {
        
        $val_change = -$action->values_change;
        
        echo <<< EOF
          <div class="land-payout negative">Effect: Target's $game_user->values is
            reduced by $val_change</div>
EOF;

      } // if values_change < 0
    
    } // foreach action

    echo <<< EOF
  </div>
  <div class="land-button-wrapper"><div class="land-buy-button"><a
    href="/$game/agents_hire/$arg2/$item->id/1">Hire</a></div>
  <div class="land-sell-button"><a
    href="/$game/agents_fire/$arg2/$item->id/1">Fire</a></div></div>
</div>
EOF;

  }
  
// show next agent

  $sql = 'SELECT staff.*, staff_ownership.quantity 
    FROM staff
    
    LEFT OUTER JOIN staff_ownership ON staff_ownership.fkey_staff_id = staff.id
    AND staff_ownership.fkey_users_id = %d

    WHERE ((
      fkey_neighborhoods_id = 0
      OR fkey_neighborhoods_id = %d
    ) 
    
    AND
    
    (
      fkey_values_id = 0
      OR fkey_values_id = %d
    ))
  
    AND required_level > %d
    AND active = 1
    AND is_loot = 0
    AND staff_or_agent = "a"
    ORDER BY required_level ASC LIMIT 1';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  $item = db_fetch_object($result);
  
  if (!empty($item)) {
//firep($item);
    
    $description = str_replace('%clan', "<em>$party_title</em>",
      $item->description);
      
    $quantity = $item->quantity;
    if (empty($quantity)) $quantity = '<em>None</em>';

    $agents_price = $item->price + ($item->quantity *
      $item->price_increase);

    if ($item->quantity_limit > 0) {
      $quantity_limit = '<em>(Limited to ' . $item->quantity_limit . ')</em>';
    } else {
      $quantity_limit = '';
    }
    
    echo <<< EOF
<div class="land-soon">
  <div class="land-details">
    <div class="land-name">$item->name</div>
    <div class="land-description">$description</div>
    <div class="land-required_level">Requires level $item->required_level</div>
    <div class="land-cost">Cost: $agents_price $game_user->values</div>
EOF;

// grab each action for an agent

    $data2 = array();
    $sql = 'select * from actions where fkey_staff_id = %d;';
    $result = db_query($sql, $item->id);

    while ($action = db_fetch_object($result)) $data2[] = $action;
  
    foreach ($data2 as $action) {
// firep($action);

      $name = str_replace('%value', $game_user->values, $action->name);
    
      echo '<div class="land-action">Action: ' . $name . '</div>';
      echo '<div class="land-description">' . $action->description . '</div>';
      echo '<div class="land-action-cost">Cost: ' . $action->cost . 
        ' Action</div>';
      
      if ($action->influence_change < 0) {
        
        $inf_change = -$action->influence_change;
        
        echo <<< EOF
          <div class="land-payout negative">Effect: Target's $experience is
            reduced by $inf_change</div>
EOF;

      } // if influence_change < 0
      
      if (($action->rating_change < 0.10) && ($action->rating_change != 0.0)) {
        
        $rat_change = abs($action->rating_change);
        
        if ($action->rating_change < 0.0) {
          
          echo <<< EOF
      <div class="land-payout negative">Effect: $target approval rating is
        reduced by $rat_change%</div>
EOF;
        
        } else {
  
          echo <<< EOF
      <div class="land-payout">Effect: $target approval rating is
        increased by $rat_change%</div>
EOF;
          
        }
  
      } // if rating_change < 0.10
      
      if ($action->rating_change >= 0.10) {
          
        $rat_change = $action->rating_change;
          
        echo <<< EOF
      <div class="land-payout">Effect: Your approval rating is
        increased by $rat_change%</div>
EOF;
  
      } // if rating_change >= 0.10
    
    } // foreach action

    echo <<< EOF
  </div>
</div>
EOF;

  } // if !empty($item)
  
  db_set_active('default');
