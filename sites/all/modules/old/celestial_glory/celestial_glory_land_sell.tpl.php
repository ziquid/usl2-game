<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if ($quantity === 'use-quantity') {
    $quantity = check_plain($_GET['quantity']);

//    mail('joseph@cheek.com', "land_sell with use-quantity of $quantity",
//     "user $game_user->username");
  }

  $sql = 'select clan_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $clan_title = preg_replace('/^The /', '', $data->clan_title);

  $data = array();
  $sql = 'SELECT land.*, land_ownership.quantity
    FROM land

    LEFT OUTER JOIN land_ownership ON land_ownership.fkey_land_id = land.id
    AND land_ownership.fkey_users_id = %d

    WHERE land.id = %d;';
  $result = db_query($sql, $game_user->id, $land_id);
  $game_land = db_fetch_object($result); // limited to 1 in DB
  $orig_quantity = $quantity;
  $land_price = ceil($game_land->price * $quantity * 0.6);

/* allow for 80% sale price
  $land_price = ceil($game_land->price +
    ($game_land->price_increase * ($game_land->quantity - $quantity))
     * $quantity * 0.8);
*/
  $land_succeeded = TRUE;
  $outcome_reason = '<div class="land-succeeded">' . t('Success!') .
    '</div>';
  $ai_output = 'land-succeeded';

// check to see if land prerequisites are met

// hit a quantity limit?
  if ($quantity > $game_land->quantity) {

    $land_succeeded = FALSE;
    $outcome_reason = '<div class="land-failed">' . t('You don\'t have that many!') .
      '</div>';

  }

  if ($game_land->can_sell != 1) {

    $land_succeeded = FALSE;
    $ai_output = 'land-failed cant-sell';

    $outcome_reason = '<div class="land-failed">' . t('Nope',
      array('@value' => $game_user->values)) . '</div>';

  }

  if ($land_succeeded) {

//    $game_user->money -= $game_land->price;
//    $game_user->income += $game_land->payout;

    competency_gain($game_user, 'seller');

    if ($quantity >= 100) {
      competency_gain($game_user, 'master seller');
    }

    $sql = 'update land_ownership set quantity = quantity - %d where
      fkey_land_id = %d and fkey_users_id = %d;';
      $result = db_query($sql, $quantity, $land_id, $game_user->id);

    $sql = 'update users set money = money + %d, income = income - %d
      where id = %d;';
    $result = db_query($sql, $land_price, $game_land->payout * $quantity,
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
      href="/' . $game . '/elections/' . $arg2 . '">Run for
      office instead</a></div></div>';

    $quantity = 0;

  } // sell land succeeded

  $fetch_header($game_user);

  echo <<< EOF
<div class="news">
  <a href="/$game/land/$arg2" class="button active">$land_plural</a>
  <a href="/$game/equipment/$arg2" class="button">$equipment</a>
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
  <li>Purchase $land_plural_lower to earn hourly income</li>
</ul>
EOF;

  } // user level < 15
firep("game_land->quantity: $game_land->quantity");
firep("quantity: $quantity");

  $quantity = (int) $game_land->quantity - (int) $quantity;
  $land_price = $game_land->price + ($quantity * $game_land->price_increase);

  if ($quantity == 0) $quantity = '<em>None</em>'; // gotta love PHP typecasting

  if (($land_price % 1000) == 0)
    $land_price = ($land_price / 1000) . 'K';

  $payout = $game_land->payout;

  if (($payout % 1000) == 0)
    $payout = ($payout / 1000) . 'K';

  echo <<< EOF
<div class="land">
  $outcome_reason
  <div class="land-icon"><a
    href="/$game/land_buy/$arg2/$game_land->id/1"><img
    src="/sites/default/files/images/land/$game-$game_land->id.png"
    border="0" width="96"></a></div>
  <div class="land-details">
    <div class="land-name"><a
      href="/$game/land_buy/$arg2/$game_land->id/1">$game_land->name</a></div>
    <div class="land-owned">Owned: $quantity</div>
    <div class="land-cost">Cost: $land_price $game_user->values</div>
    <div class="land-payout">Income: +$payout $game_user->values
      every 60 minutes</div>
  </div>
  <div class="land-button-wrapper">
    <form action="/$game/land_sell/$arg2/$game_land->id/use-quantity">
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
      <input class="land-buy-button" type="submit" Value="Sell"/>
    </form>
  </div>
</div>

<div class="title">
Purchase $land_plural
</div>
EOF;

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";

  $data = array();
  $sql = 'SELECT land.*, land_ownership.quantity
    FROM land

    LEFT OUTER JOIN land_ownership ON land_ownership.fkey_land_id = land.id
    AND land_ownership.fkey_users_id = %d

    WHERE (((
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
    )

    OR land_ownership.quantity > 0

    ORDER BY required_level ASC';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
firep($item);

    $description = str_replace('%clan', "<em>$clan_title</em>",
      $item->description);

    $quantity = $item->quantity;
    if (empty($quantity)) $quantity = '<em>None</em>';

    $land_price = $item->price + ($item->quantity *
      $item->price_increase);

    if (($land_price % 1000) == 0)
      $land_price = ($land_price / 1000) . 'K';

    $payout = $item->payout;

    if (($payout % 1000) == 0)
      $payout = ($payout / 1000) . 'K';

    $can_buy = $can_sell = TRUE;

    if ($item->fkey_neighborhoods_id != 0 &&
      $item->fkey_neighborhoods_id != $game_user->fkey_neighborhoods_id)
      $can_buy = FALSE;

    if ($item->fkey_values_id != 0 &&
      $item->fkey_values_id != $game_user->fkey_values_id)
      $can_buy = FALSE;

    if ($item->required_level > $game_user->level)
      $can_buy = FALSE;

    if ($item->active != 1)
      $can_buy = FALSE;

    if ($item->is_loot != 0)
      $can_buy = FALSE;

    if ($item->can_sell != 1)
      $can_sell = FALSE;

    if ($item->quantity < 1)
      $can_sell = FALSE;

    if ($can_buy) {
      $icon_button = <<< EOF
<a href="/$game/land_buy/$arg2/$item->id/1">
  <img src="/sites/default/files/images/land/$game-$item->id.png" width="96"
    border="0">
</a>
EOF;
      $name_link = <<< EOF
<a href="/$game/land_buy/$arg2/$item->id/1">
  $item->name
</a>
EOF;
      $buy_button = <<< EOF
<div class="land-buy-button">
  <a href="/$game/land_buy/$arg2/$item->id/1">
    Buy
  </a>
</div>
EOF;
    } else {
      $icon_button = <<< EOF
  <img src="/sites/default/files/images/land/$game-$item->id.png" width="96"
    border="0">
EOF;
      $name_link = <<< EOF
$item->name
EOF;
      $buy_button = <<< EOF
<div class="land-buy-button not-yet">
  Can't Buy
</div>
EOF;
    }

    if ($can_sell) {
      $sell_button = <<< EOF
<div class="land-sell-button">
  <a href="/$game/land_sell/$arg2/$item->id/1">
    Sell
  </a>
</div>
EOF;
    } else {
      $sell_button = <<< EOF
<div class="land-sell-button not-yet">
  Can't Sell
</div>
EOF;
    }

    echo <<< EOF
<div class="land">
  <div class="land-icon">
    $icon_button
  </div>
  <div class="land-details">
    <div class="land-name">
      $name_link
    </div>
    <div class="land-description">$description</div>
    <div class="land-owned">Owned: $quantity</div>
    <div class="land-cost">Cost: $land_price $game_user->values</div>
    <div class="land-payout">Income: +$payout $game_user->values
      every 60 minutes</div>
  </div>
  <div class="land-button-wrapper">
    $buy_button
    $sell_button
  </div>
</div>
EOF;

  }

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";

// show next one
  $sql = 'SELECT land.*, land_ownership.quantity
    FROM land

    LEFT OUTER JOIN land_ownership ON land_ownership.fkey_land_id = land.id
    AND land_ownership.fkey_users_id = %d

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

    $land_price = $item->price + ($item->quantity *
      $item->price_increase);

    echo <<< EOF
<div class="land-soon">
  <div class="land-details">
    <div class="land-name">$item->name</div>
    <div class="land-description">$description</div>
    <div class="land-required_level">Requires level $item->required_level</div>
    <div class="land-cost">Cost: $land_price $game_user->values</div>
    <div class="land-payout">Income: +$item->payout $game_user->values
      every 60 minutes</div>
  </div>
</div>
EOF;

  }

  db_set_active('default');
