<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $ai_output = 'land-prices';

  _recalc_income($game_user);
  $fetch_header($game_user);
  _show_aides_menu($game_user);

  $sql_to_add = 'WHERE (((
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

    OR land_ownership.quantity > 0 ';

  if ($game_user->phone_id == 'abc123')
    $sql_to_add = '';

  $data = array();
  $sql = 'SELECT land.*, land_ownership.quantity,
    competencies.name as competency
    FROM land

    LEFT OUTER JOIN land_ownership ON land_ownership.fkey_land_id = land.id
    AND land_ownership.fkey_users_id = %d

    LEFT OUTER JOIN competencies on land.fkey_required_competencies_id =
      competencies.id

    ' . $sql_to_add . '
    ORDER BY required_level ASC';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
// firep($item);

    _show_land($game_user, $item);

    $land_price = $item->price + ($item->quantity * $item->price_increase);
    $ai_output .= " $item->id=$land_price";

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
// firep($item);

  if (!empty($item)) _show_land($game_user, $item, array('soon' => TRUE));

  db_set_active('default');
