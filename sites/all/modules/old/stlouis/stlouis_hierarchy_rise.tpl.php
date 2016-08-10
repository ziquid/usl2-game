<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  $sql = 'SELECT * from elected_positions
    WHERE id = %d;';
  $result = db_query($sql, $position_id);
  $pos = db_fetch_object($result);

// do checks

// first check -- is current level just below new level?
  if (($game_user->ep_level + 1) != $position_id) {

    $fetch_header($game_user);
    show_elections_menu($game_user);    

    echo <<< EOF
<div class="title">Become $pos->name</div>
EOF;

    $sql = 'SELECT * from elected_positions
      WHERE id = %d;';
    $result = db_query($sql, $position_id - 1);
    $opos = db_fetch_object($result);

    if (empty($opos->name)) $opos->name = 'non-hacker';

    echo '
<div class="quests failed">
  <div class="title">' .
    t('∆ Current Level Attainment <span>Incorrect</span> ∆') . '
  </div>
  <div class="subsubtitle">You must currently be a ' . $opos->name . '</div>
</div>
';

    _button();

    _karma($game_user,
      "trying to rise to $pos->name without being $opos->name", -250);
    db_set_active('default');
    return;

  } // not at appropriate level
  
// second check -- all requisites met?
  $progress = hierarchy_status($game_user, $position_id);

  if (!$progress->qualified) {

    $fetch_header($game_user);
    show_elections_menu($game_user);    

    echo <<< EOF
<div class="title">Become $pos->name</div>
EOF;

    echo '
<div class="quests failed">
  <div class="title">' .
    t('∆ Hierarchical Requisites <span>Unmet</span> ∆') . '
  </div>
</div>
';

    _button();

    _karma($game_user,
      "trying to rise to $pos->name without meeting requirements", -250);
    db_set_active('default');
    return;

  } // requirements not met
  
// things look good!  update the user!

  election_won($game_user, $position_id);

  $game_user = $fetch_user();
  $fetch_header($game_user);
  show_elections_menu($game_user);    

    echo <<< EOF
<div class="title">Become $pos->name</div>

<div class="quests succeeded">
  <div class="title">
    // Attempt Successful //
  </div>
  <div class="subsubtitle">
    You have obtained the rank of $pos->name
  </div>
</div>
EOF;

  _button();
  db_set_active('default');
