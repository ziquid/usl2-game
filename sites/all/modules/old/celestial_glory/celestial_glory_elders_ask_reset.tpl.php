<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));

  if ($game == 'stlouis') {

    $experience = 'Influence';

  }

  if ($game == 'celestial_glory') {

    $experience = 'Spirituality';

  }

  if ($game_user->level >= 50) {

    echo <<< EOF
<div class="title">
  Please e-mail us
</div>
<div class="subtitle">
  E-mail us at <strong>zipport@ziquid.com</strong> to have your character reset
</div>
EOF;

    db_set_active('default');
    return;

  }

  if (check_plain($_GET['msg']) == 'error') {

    echo <<< EOF
<div class="subsubtitle">Please enter &quot;RESET ME&quot;.</div>
EOF;

  }

  echo <<< EOF
<div class="title">
  Do you really want to reset your character?
</div>
<div class="subtitle">
  You will lose all $experience, stats, and $game_user->values your character
  has collected
</div>
<div class="subsubtitle">
  If you really want to reset, enter the words &quot;RESET&nbsp;ME&quot; here:
</div>
<div class="ask-name">
  <form method=get action="/$game/elders_do_reset/$arg2">
    <input type="text" name="reset_me" size="8" maxlength="8"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

  db_set_active('default');
