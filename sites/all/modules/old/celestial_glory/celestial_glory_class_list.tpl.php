<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $phone_id);
	
  $data = array();
  $sql = 'SELECT username, experience, initiative, endurance, 
  	elocution, debates_won, debates_lost, skill_points, luck,
  	debates_last_time, users.fkey_values_id, level, phone_id,
  	`values`.clan_title, `values`.clan_icon,
    `values`.name, users.id, users.fkey_neighborhoods_id,
    neighborhoods.name as location
    
		FROM `users`
		
		LEFT JOIN `values` ON users.fkey_values_id = `values`.id
				
		LEFT JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id
		
		WHERE users.fkey_values_id = %d
		AND users.fkey_neighborhoods_id = %d
		AND users.username <> ""
		ORDER by users.experience DESC;';
  
  $result = db_query($sql, $class_id, $game_user->fkey_neighborhoods_id);
  while ($item = db_fetch_object($result)) $data[] = $item;

  $num_members = count($data);
  
   echo <<< EOF
<!--<div class="news">
	<a href="/$game/clan_list/$phone_id/$clan_id" class="button active">Clan List</a>
	<a href="/$game/clan_msg/$phone_id/$clan_id" class="button">Clan Messages</a>
</div>-->
<div class="title">{$data[0]->clan_title} Class List</div>
<div class="subtitle">{$data[0]->location} Ward </div>
<div class="subtitle">($num_members members)</div>
EOF;

    echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">Calling</div>
	<div class="opponent-name">Name</div>
	<div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;
  
  foreach ($data as $item) {
firep($item);

  	$username = $item->username;
  	$action_class = '';
  	$official_link = $item->ep_name;
		$clan_class = 'election-details';
		
  	if ($item->can_broadcast_to_party)
  		$official_link .= '<div class="can-broadcast-to-party">*</div>';
  		
   	$official_link .= '<br/><a href="/' . $game . '/user/' .
 		  $phone_id . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';

  	echo <<< EOF
<div class="$clan_class">
  <div class="clan-title">&nbsp;</div>
  <div class="opponent-name">$official_link</div>
	<div class="opponent-influence">$item->experience $experience<br/>
		Level $item->level</div>
</div>
EOF;
  
  } // foreach position
  
  db_set_active('default');
  
