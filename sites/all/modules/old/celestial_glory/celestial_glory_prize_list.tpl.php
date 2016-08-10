<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  echo <<< EOF
<div class="help">
  <div class="title">
    Event Prize List
  </div>
EOF;

// straight from the equipment pages
$data = array();
  $sql = 'SELECT equipment.*, equipment_ownership.quantity
    FROM equipment

    LEFT OUTER JOIN equipment_ownership
      ON equipment_ownership.fkey_equipment_id = equipment.id
      AND equipment_ownership.fkey_users_id = %d

    WHERE

      equipment.id = 53';
  $result = db_query($sql, $game_user->id);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
// firep($item);

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
  <div class="land-icon"><a href="/$game/equipment_buy/$arg2/$item->id/1"><img
    src="/sites/default/files/images/equipment/$game-$item->id.png" border="0"
    width="96"></a></div>
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
    <div class="land-payout">$initiative: +$item->initiative_bonus
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

  echo <<< EOF
  <div class="subtitle">
    1st Place
  </div>
  <ul>
    <li>
      20 Perfect Amethyst, 20 Luck
    </li>
  </ul>

  <div class="subtitle">
    2nd Place
  </div>
  <ul>
    <li>
      19 Perfect Amethyst, 19 Luck
    </li>
  </ul>

  <div class="subtitle">
    3rd Place
  </div>
  <ul>
    <li>
      18 Perfect Amethyst, 18 Luck
    </li>
  </ul>

  <div class="subtitle">
    4th Place
  </div>
  <ul>
    <li>
      17 Perfect Amethyst, 17 Luck
    </li>
  </ul>

  <div class="subtitle">
    5th Place
  </div>
  <ul>
    <li>
      16 Perfect Amethyst, 16 Luck
    </li>
  </ul>

  <div class="subtitle">
    6th Place
  </div>
  <ul>
    <li>
      15 Perfect Amethyst, 15 Luck
    </li>
  </ul>

  <div class="subtitle">
    7th Place
  </div>
  <ul>
    <li>
      14 Perfect Amethyst, 14 Luck
    </li>
  </ul>

  <div class="subtitle">
    8th Place
  </div>
  <ul>
    <li>
      13 Perfect Amethyst, 13 Luck
    </li>
  </ul>

  <div class="subtitle">
    9th Place
  </div>
  <ul>
    <li>
      12 Perfect Amethyst, 12 Luck
    </li>
  </ul>

  <div class="subtitle">
    10th Place
  </div>
  <ul>
    <li>
      11 Perfect Amethyst, 11 Luck
    </li>
  </ul>

  <div class="subtitle">
    11th - 15th Places
  </div>
  <ul>
    <li>
      10 Perfect Amethyst, 10 Luck
    </li>
  </ul>

  <div class="subtitle">
    16th - 20th Places
  </div>
  <ul>
    <li>
      5 Perfect Amethyst, 5 Luck
    </li>
  </ul>

  <div class="subtitle">
    Everyone else who loots at least 100 Raw Amethyst
  </div>
  <ul>
    <li>
      3 Perfect Amethyst
    </li>
  </ul>

  <div class="subtitle">
    Everyone else who loots at least 25 Raw Amethyst
  </div>
  <ul>
    <li>
      2 Perfect Amethyst
    </li>
  </ul>

  <div class="subtitle">
    Everyone else who loots at least 10 Raw Amethyst
  </div>
  <ul>
    <li>
      1 Perfect Amethyst
    </li>
  </ul>

</div>
EOF;

  db_set_active('default');
