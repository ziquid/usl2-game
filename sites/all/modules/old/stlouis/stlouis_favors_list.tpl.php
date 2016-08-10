<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// do AI moves from this page!!!
  include_once(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if ($game_user->level < 20) {

    echo <<< EOF
<p>&nbsp;</p>
<div class="title">
  <img src="/sites/default/files/images/${game}_title.png?1" width=300/>
</div>
<div class="welcome">
  <div class="holodad"></div>
  <p class="quote">You're not yet experienced enough for this page.&nbsp;
  Come back at level 20.</p>
</div>
EOF;

  _button('quests');

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"home not-yet\"/>\n-->";

  db_set_active('default');
  return;

  }

  show_elections_menu($game_user);
  
  $sql = 'select count(*) as count
    from favor_requests where fkey_users_to_id = %d
    and time_completed = 0;';
  $result = db_query($sql, $game_user->id);
  $runner = db_fetch_object($result);

  if ($runner->count > 0) { // tell the user about favors requests of him/her

    echo <<< EOF
<div class="land">
  <div class="title">
    // {$favor}s Requested Of You //
  </div>
  <div class="subtitle">
    You have $runner->count active $favor request(s)
  </div>
  <div class="try-an-election-wrapper">
    <div class="try-an-election">
      <a href="/$game/user_favors/$arg2">
        Show Active Favor Requests
      </a>
    </div>
  </div>
</div>
EOF;

  }

  echo <<< EOF
<div class="title">
  Available {$favor}s
</div>
EOF;


// abc123 -- show all
  $active_favors = ($phone_id == 'abc123') ? '' : 'where favors.active = 1';

// show each favor
  $sql = 'select favors.*, elected_positions.name as ep_name,
    cr.name as cr_name, ce.name as ce_name from favors

    LEFT JOIN elected_positions
    ON favors.fkey_required_elected_positions_id = elected_positions.id

    LEFT JOIN competencies as cr
    ON favors.fkey_required_competencies_id = cr.id

    LEFT JOIN competencies as ce
    ON favors.fkey_enhanced_competencies_id = ce.id

    ' . $active_favors .
    ' order by favors.id ASC;';
  $result = db_query($sql);
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

firep($item);
    _show_favor($game_user, $item);

  }

  db_set_active('default');
