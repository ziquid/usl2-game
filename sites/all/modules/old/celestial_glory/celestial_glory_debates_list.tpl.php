<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));

// do AI moves from this page!!!
  include(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  echo <<< EOF
<div class="news">
  <a href="/$game/debates/$arg2" class="button active">{$debate_tab}</a>
  <a href="/$game/elections/$arg2" class="button">{$election_tab}</a>
  <a href="/$game/top20/$arg2" class="button">$top20</a>
  <a href="/$game/top_aldermen/$arg2" class="button">Top $alders_short</a>
</div>
EOF;

  if (!$game_user->seen_neighborhood_quests) {
// intro neighborhood quests == debates, if they haven't been shown

    if ($game == 'celestial_glory') {

      echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;As you journey, you will meet others who like to challenge.&nbsp;
  Touch any player's name to challenge them.</p>
  <p class="second">&quot;The more $elocution you have, the better you
  will do in these challenges.</p>
  <p></p>
</div>
EOF;

      $sql = 'update users set seen_neighborhood_quests = 1 where id = %d;';
      $result = db_query($sql, $game_user->id);

    }

  }

  if ($game_user->level < 15) {

    echo <<< EOF
<ul>
  <li>Win {$debate_lower}s to give you more $game_user->values and $experience</li>
  <li>Each $debate_lower costs one Action</li>
  <li>Wait and rest for a few minutes if you run out of Actions</li>
</ul>
EOF;

  }

  if ($debate == 'Box') { // boxing day?  check for gloves

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, 32, $game_user->id);
    $item = db_fetch_object($result);

    if ($item->quantity < 1) { // no boxing gloves!

      echo <<< EOF
<div class="title">
No boxing gloves?
</div>
<div class="subtitle">
How do you expect to be able to box?
</div>
<div class="subtitle">
  <a href="/$game/equipment/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

      db_set_active('default');
      return;

    }

  }

  echo <<< EOF
<div class="title">
Whom would you like to $debate_lower?
</div>
EOF;

  $data = array();
  $sql = 'SELECT username, experience, `values`.clan_title, `values`.clan_icon,
    users.id, users.phone_id, clan_members.is_clan_leader, users.meta,
    clans.acronym AS clan_acronym, neighborhoods.name as neighborhood
    FROM users
    LEFT JOIN `values` ON users.fkey_values_id = `values`.id
    LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
    LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
    LEFT OUTER JOIN neighborhoods
      ON users.fkey_neighborhoods_id = neighborhoods.id
    WHERE users.id <> %d
    AND (clans.id <> %d OR clans.id IS NULL OR users.meta = "zombie")
    AND username <> ""
    AND (debates_last_time < "%s" OR
     (users.meta = "zombie" AND debates_last_time < "%s"))
    AND users.level > %d
    AND users.level < %d
    ORDER BY abs(users.experience - %d) ASC
    LIMIT 12;'; // and users.fkey_neighborhoods_id = %d
  $result = db_query($sql, $game_user->id, $game_user->fkey_clans_id,
    date('Y-m-d H:i:s', time() - $debate_time),
    date('Y-m-d H:i:s', time() - $zombie_debate_wait),
    $game_user->level - 15,
    $game_user->level + 15, $game_user->experience);
// jwc flag day - make debates much more active
  $count = 12;
  while ($count-- && $item = db_fetch_object($result)) $data[] = $item;
firep(db_affected_rows());

  echo <<< EOF
<div class="elections-header">
<div class="election-details">
  <div class="clan-title">$party</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Action</div>
</div>
</div>
<div class="elections">
EOF;

  foreach ($data as $item) {
firep($item);

    $username = $item->username;
    if (empty($username)) $username = '<em>Anonymous</em>';

    if ($item->id == $game_user->id) {
      $clan_class = 'election-details me';
    } else {
      $clan_class = 'election-details';
    }

    $icon = $game . '_clan_' . $item->clan_icon . '.png';
    $clan_acronym = '';

    if (!empty($item->clan_acronym)) {
      $clan_acronym = "($item->clan_acronym)";

      $icon_path = file_directory_path() . '/images/' . $game . '_clan_' .
        strtolower($item->clan_acronym) . '.png';
firep($icon_path);

      if (file_exists($_SERVER['DOCUMENT_ROOT'] . base_path() . $icon_path))
        $icon = $game . '_clan_' . strtolower($item->clan_acronym) . '.png';

    }

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    $action_class = '';
    $action = $debate;

//    if ($game == 'celestial_glory') {

//      $ward = "$item->neighborhood / ";

//    } else {

      $ward = '';

//    }

    echo <<< EOF
<div class="$clan_class">
  <div class="clan-icon"><img width="24"
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$ward $item->clan_title</div>
  <div class="opponent-name"><a
    href="/$game/user/$arg2/$item->phone_id">$username $clan_acronym</a></div>
  <div class="action-wrapper"><div class="action $action_class"><a
    href="/$game/debates_challenge/$arg2/$item->id">$action</a></div></div>
</div>
EOF;

  }

  echo '</div>';

  db_set_active('default');
