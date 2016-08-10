<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if ($quantity === 'use-quantity') {
    $quantity = check_plain($_GET['quantity']);

//    mail('joseph@cheek.com', "equipment_sell with use-quantity of $quantity",
//     "user $game_user->username");
  }

  $sql = 'select clan_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $clan_title = preg_replace('/^The /', '', $data->clan_title);

  $data = array();
  $sql = 'SELECT equipment.*, equipment_ownership.quantity
    FROM equipment

    LEFT OUTER JOIN equipment_ownership
      ON equipment_ownership.fkey_equipment_id = equipment.id
      AND equipment_ownership.fkey_users_id = %d

    WHERE equipment.id = %d;';
  $result = db_query($sql, $game_user->id, $equipment_id);
  $game_equipment = db_fetch_object($result); // limited to 1 in DB
  $orig_quantity = $quantity;

  $equipment_price = ceil($game_equipment->price * $quantity * 0.6);
/* allow for 80% sale price
  $equipment_price = ceil($equipment_land->price +
    ($game_equipment->price_increase * ($game_equipment->quantity - $quantity))
     * $quantity * 0.8);
*/
  $equipment_succeeded = TRUE;
  $outcome_reason = '<div class="land-succeeded">' . t('Success!') .
    '</div>';

// check to see if equipment prerequisites are met

// hit a quantity limit?
  if ($quantity > $game_equipment->quantity) {

    $equipment_succeeded = FALSE;
    $outcome_reason = '<div class="land-failed">' . t('You don\'t have that many!') .
      '</div>';

  }

// can't sell?
  if ($game_equipment->can_sell != 1) {

    $equipment_succeeded = FALSE;
    $outcome_reason = '<div class="land-failed">' . t('Sorry!') .
      '</div><div class="subtitle">' .
      t('This item cannot be sold') .
      '</div><br/>';

  }

  if ($equipment_succeeded) {

    competency_gain($game_user, 'seller');

    if ($quantity >= 100) {
      competency_gain($game_user, 'master seller');
    }

     $sql = 'update equipment_ownership set quantity = quantity - %d where
       fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $quantity, $equipment_id, $game_user->id);

// save money
    $sql = 'update users set money = money + %d where id = %d;';
    $result = db_query($sql, $equipment_price, $game_user->id);

// update upkeep, if needed
    if ($game_equipment->upkeep > 0) { // FIXME: do at the same time as money
      $sql = 'update users set expenses = expenses - %d where id = %d;';
      $result = db_query($sql, $game_equipment->upkeep * $quantity,  $game_user->id);
    }

    $game_user = $fetch_user(); // reprocess user object

  } else { // failed - add option to try an election

    $outcome .= '<div class="try-an-election-wrapper"><div
      class="try-an-election"><a
      href="/' . $game . '/elections/' . $arg2 . '">Run for
      office instead</a></div></div>';

    $quantity = 0;

  } // sell equipment succeeded

  $fetch_header($game_user);

  echo <<< EOF
<div class="news">
  <a href="/$game/land/$arg2" class="button">$land_plural</a>
  <a href="/$game/equipment/$arg2" class="button active">$equipment</a>
EOF;

  if ($game != 'celestial_glory') {

    echo <<< EOF
  <a href="/$game/staff/$arg2" class="button">Staff</a>
  <a href="/$game/agents/$arg2" class="button">Agents</a>
EOF;

  }

  echo <<< EOF
</div>
EOF;

  if ($game_user->level < 15) {

    echo <<< EOF
<ul>
  <li>Purchase $equipment_lower to help you and your aides</li>
</ul>
EOF;

  } // user level < 15
firep("game_equipment->quantity: $game_equipment->quantity");
firep("quantity: $quantity");

  $quantity = (int) $game_equipment->quantity - (int) $quantity;
  $equipment_price = $game_equipment->price
  + ($quantity * $game_equipment->price_increase);

  if ($quantity == 0) $quantity = '<em>None</em>'; // gotta love PHP typecasting

  if (($equipment_price % 1000) == 0 && ($equipment_price > 0))
    $equipment_price = ($equipment_price / 1000) . 'K';
  else
    $equipment_price = number_format($equipment_price);

  if ($game_equipmenmt->quantity_limit > 0) {
    $quantity_limit = '<em>(Limited to ' . $game_equipment->quantity_limit
    . ')</em>';
  } else {
    $quantity_limit = '';
  }

  echo <<< EOF
<div class="land">
  $outcome_reason
  <div class="land-icon"><a
    href="/$game/equipment_buy/$arg2/$game_equipment->id/1"><img
    src="/sites/default/files/images/equipment/$game-$game_equipment->id.png"
    border="0" width="96"></a></div>
  <div class="land-details">
    <div class="land-name"><a
      href="/$game/equipment_buy/$arg2/$game_equipment->id/1">$game_equipment->name</a></div>
    <div class="land-owned">Owned: $quantity $quantity_limit</div>
    <div class="land-cost">Cost: $equipment_price $game_user->values</div>
EOF;

    if ($game_equipment->energy_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$game_equipment->energy_bonus immediate energy bonus
      </div>
EOF;

    } // energy bonus?

    if ($game_equipment->energy_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$game_equipment->energy_increase every $energy_wait_str
      </div>
EOF;

    } // energy increase?

    if ($game_equipment->initiative_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">Initiative: +$game_equipment->initiative_bonus
      </div>
EOF;

    } // initiative bonus?

    if ($game_equipment->endurance_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">$endurance: +$game_equipment->endurance_bonus
      </div>
EOF;

    } // endurance bonus?

    if ($game_equipment->elocution_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">$elocution: +$game_equipment->elocution_bonus
      </div>
EOF;

    } // elocution bonus?

    if ($game_equipment->speed_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Speed Increase: $game_equipment->speed_increase fewer Action
      needed to move to a new $hood_lower
      </div>
EOF;

    } // speed increase?

    if ($game_equipment->upkeep > 0) {

      echo <<< EOF
    <div class="land-payout negative">Upkeep: $game_equipment->upkeep every 60 minutes</div>
EOF;

    } // upkeep

    if ($game_equipment->chance_of_loss > 0) {

      $lifetime = floor(100 / $game_equipment->chance_of_loss);
       $use = ($lifetime == 1) ? 'use' : 'uses';
      echo <<< EOF
    <div class="land-payout negative">Expected Lifetime: $lifetime $use</div>
EOF;

    } // expected lifetime

    echo <<< EOF

  </div>
  <div class="land-button-wrapper">
    <form action="/$game/equipment_sell/$arg2/$game_equipment->id/use-quantity">
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
      <input class="land-buy-button" type="submit" Value="Sell More"/>
    </form>
  </div>
</div>

<div class="title">
  Purchase $equipment
</div>
EOF;

  $data = array();
  $sql = 'SELECT equipment.*, equipment_ownership.quantity
    FROM equipment

    LEFT OUTER JOIN equipment_ownership
      ON equipment_ownership.fkey_equipment_id = equipment.id
      AND equipment_ownership.fkey_users_id = %d

    WHERE

      equipment_ownership.quantity > 0

    OR

      "%s" = "abc123"

    OR

    (

      ((
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

    )

    ORDER BY required_level ASC';
  $result = db_query($sql, $game_user->id, $arg2,
    $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
firep($item);

    $description = str_replace('%clan', "<em>$clan_title</em>",
      $item->description);

    $quantity = $item->quantity;
    if (empty($quantity)) $quantity = '<em>None</em>';

    $equipment_price = $item->price + ($item->quantity *
      $item->price_increase);

    if (($equipment_price % 1000) == 0 && ($equipment_price > 0))
      $equipment_price = ($equipment_price / 1000) . 'K';
    else
      $equipment_price = number_format($equipment_price);

    if ($item->quantity_limit > 0) {
      $quantity_limit = '<em>(Limited to ' . $item->quantity_limit . ')</em>';
    } else {
      $quantity_limit = '';
    }

    echo <<< EOF
<div class="land">
  <div class="land-icon"><a href="/$game/equipment_buy/$arg2/$item->id/1">
    <img src="/sites/default/files/images/equipment/$game-$item->id.png"
    width="96" border="0"></a></div>
  <div class="land-details">
    <div class="land-name"><a
      href="/$game/equipment_buy/$arg2/$item->id/1">$item->name</a></div>
    <div class="land-description">$description</div>
    <div class="land-owned">Owned: $quantity $quantity_limit</div>
    <div class="land-cost">Cost: $equipment_price $game_user->values</div>
EOF;

    if ($item->energy_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$item->energy_bonus immediate energy bonus
      </div>
EOF;

    } // energy bonus?

    if ($item->energy_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$item->energy_increase every $energy_wait_str
      </div>
EOF;

    } // energy increase?

    if ($item->initiative_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">Initiative: +$item->initiative_bonus
      </div>
EOF;

    } // initiative bonus?

    if ($item->endurance_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">$endurance: +$item->endurance_bonus
      </div>
EOF;

    } // endurance bonus?

    if ($item->elocution_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">$elocution: +$item->elocution_bonus
      </div>
EOF;

    } // elocution bonus?

    if ($item->speed_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Speed Increase: $item->speed_increase fewer Action
      needed to move to a new $hood_lower
      </div>
EOF;

    } // speed increase?

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

// grab each action for the equipment
    $data2 = array();
    $sql = 'select * from actions where fkey_equipment_id = %d;';
    $result = db_query($sql, $item->id);

    while ($action = db_fetch_object($result)) $data2[] = $action;

    foreach ($data2 as $action) {
firep($action);

      $cost = "Cost: $action->cost Action";
      if ($action->values_cost > 0)
        $cost .= ", $action->values_cost $game_user->values";

      $name = str_replace('%value', $game_user->values, $action->name);

      echo '<div class="land-action">Action: ' . $name . '</div>';
      echo '<div class="land-description">' . $action->description . '</div>';
      echo '<div class="land-action-cost">' . $cost . '</div>';

      if ($action->influence_change < 0) {

        $inf_change = -$action->influence_change;

        echo <<< EOF
          <div class="land-payout negative">Effect: Target's $experience_lower
            is reduced by $inf_change</div>
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

      } // if rating_change < 0

      if ($action->rating_change >= 0.10) {

        $rat_change = $action->rating_change;

        echo <<< EOF
      <div class="land-payout">Effect: Your approval rating is
        increased by $rat_change%</div>
EOF;

      } // if rating_change > 0

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

    echo '</div>';

    if ((!$item->is_loot) &&
      ($item->fkey_neighborhoods_id == 0 || $item->fkey_neighborhoods_id ==
        $game_user->fkey_neighborhoods_id) &&
      ($item->fkey_values_id == 0 || $item->fkey_values_id ==
        $game_user->fkey_values_id) &&
      ($item->required_level <= $game_user->level)) {

      echo <<< EOF
  <div class="land-button-wrapper"><div class="land-buy-button"><a
    href="/$game/equipment_buy/$arg2/$item->id/1">Buy</a></div>
EOF;

    } else {

      echo <<< EOF
  <div class="land-button-wrapper"><div class="land-buy-button not-yet">Can't
    Buy</div>
EOF;

    }

    if ($item->can_sell && $item->quantity > 0) {

      echo <<< EOF
  <div class="land-sell-button"><a
    href="/$game/equipment_sell/$arg2/$item->id/1">Sell</a></div></div>
EOF;

    } else {

      echo <<< EOF
  <div class="land-sell-button not-yet"><!--<a
    href="/$game/equipment_sell/$arg2/$item->id/1">-->Can't Sell<!--</a>--></div></div>
EOF;

    }

    echo '</div>';

  }

// show next one
  $sql = 'SELECT equipment.*, equipment_ownership.quantity
    FROM equipment

    LEFT OUTER JOIN equipment_ownership ON equipment_ownership.fkey_equipment_id = equipment.id
    AND equipment_ownership.fkey_users_id = %d

    WHERE ((
      fkey_neighborhoods_id = 0
      OR fkey_neighborhoods_id = %d
    )

    AND

    (
      fkey_values_id = 0
      OR fkey_values_id = %d
    ))

    AND required_level > %d' . $land_active .
    'AND is_loot = 0
    ORDER BY required_level ASC LIMIT 1';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  $item = db_fetch_object($result);
firep($item);

  if (!empty($item)) {
    $description = str_replace('%clan', "<em>$clan_title</em>",
      $item->description);

    $quantity = $item->quantity;
    if (empty($quantity)) $quantity = '<em>None</em>';

    $equipment_price = $item->price + ($item->quantity *
      $item->price_increase);

    echo <<< EOF
<div class="land-soon">
  <div class="land-details">
    <div class="land-name">$item->name</div>
    <div class="land-description">$description</div>
    <div class="land-required_level">Requires level $item->required_level</div>
    <div class="land-cost">Cost: $equipment_price $game_user->values</div>
EOF;

    if ($item->energy_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$item->energy_bonus immediate energy bonus
      </div>
EOF;

    } // energy bonus?

    if ($item->energy_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Energy: +$item->energy_increase every $energy_wait_str
      </div>
EOF;

    } // energy increase?

    if ($item->elocution_bonus > 0) {

      echo <<< EOF
    <div class="land-payout">$elocution: +$item->elocution_bonus
      </div>
EOF;

    } // elocution bonus?

    if ($item->speed_increase > 0) {

      echo <<< EOF
    <div class="land-payout">Speed Increase: $item->speed_increase fewer Action
      needed to move to a new $hood_lower
      </div>
EOF;

    } // speed increase?

    echo <<< EOF
  </div>
</div>
EOF;

  }

  db_set_active('default');
