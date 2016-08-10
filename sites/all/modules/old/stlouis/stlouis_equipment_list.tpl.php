<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $ai_output = 'equipment-prices';

  if (strpos($_SERVER['REQUEST_URI'], 'equipment') !== FALSE) {
    $type = '`type` = "m"';
    $link = 'equipment';
  } else {
    $type = '`type` <> "m"';
    $link = 'weapons';
  }

  _recalc_income($game_user);
  $fetch_header($game_user);
  _show_aides_menu($game_user);
    
  $sql = 'select party_title from `values` where id = %d;';
  $result = db_query($sql, $game_user->fkey_values_id);
  $data = db_fetch_object($result);
  $party_title = preg_replace('/^The /', '', $data->party_title);
  
  if ($game_user->level < 15) {
    
    echo <<< EOF
<ul>
  <li>Purchase $equipment_lower to help you and your aides</li>
</ul>
EOF;

  }

  $land_active = ' AND active = 1 ';
  
// for testing - exclude all exclusions (!) if I am abc123
  if ($game_user->phone_id == 'abc123') {
    $land_active = ' AND (active = 1 OR active = 0) ';
  }
    
  $data = array();
  $sql = 'SELECT equipment.*, equipment_ownership.quantity
    FROM equipment
    
    LEFT OUTER JOIN equipment_ownership
      ON equipment_ownership.fkey_equipment_id = equipment.id
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
  
    AND required_level <= %d' . $land_active . '
    AND (is_loot = 0 OR equipment_ownership.quantity > 0)
    AND ' . $type . '
    ORDER BY required_level ASC';
firep($sql);
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  while ($item = db_fetch_object($result)) $data[] = $item;
  
  foreach ($data as $item) {
firep($item);

    show_equipment($game_user, $item);
    
  }

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";
  
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
  
    AND required_level > %d' . $land_active . '
    AND is_loot = 0
    AND ' . $type . '
    ORDER BY required_level ASC LIMIT 1';
  $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
    $game_user->fkey_values_id, $game_user->level);

  $item = db_fetch_object($result);
firep($item);
    
  if (!empty($item)) {

    show_equipment($game_user, $item, array('soon' => TRUE));

  }
  

// clan stuff

  $data = array();

  $sql = 'SELECT equipment.*, clan_equipment_ownership.quantity
    FROM equipment
    
    LEFT OUTER JOIN clan_equipment_ownership
      ON clan_equipment_ownership.fkey_equipment_id = equipment.id
    AND clan_equipment_ownership.fkey_clans_id = %d

    WHERE clan_equipment_ownership.quantity > 0
    AND ' . $type . '
    ORDER BY required_level ASC';
firep($sql);
  $result = db_query($sql, $game_user->fkey_clans_id);

  while ($item = db_fetch_object($result)) $data[] = $item;

  if (count($data) > 0) echo <<< EOF
<div class="title">
  Clan $link
</div>
EOF;
  
  foreach ($data as $item) {
firep($item);

    show_equipment($game_user, $item, array('clan' => TRUE));
    
  }

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";
  
  db_set_active('default');
