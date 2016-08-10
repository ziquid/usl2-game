<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $arg2 = check_plain(arg(2));

  if ($game_user->level < 6) {

    echo <<< EOF
<div class="title">
  <img src="/sites/default/files/images/{$game}_title.png"/>
</div>
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;You're not influential enough yet for this page.&nbsp;
  Come back at level 6.&quot;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
</div>
<div class="subtitle"><a
  href="/$game/quests/$arg2"><img
  src="/sites/default/files/images/{$game}_continue.png"/></a></div>
EOF;

  db_set_active('default');
  return;

  }

  $password = trim(check_plain($_GET['password']));

  if (strlen($password) > 0 and strlen($password) < 6) {
    $error_msg .= '<div class="username-error">Your password must be at least 6
      characters long.</div>';
    $password = '';
  }

// if they have chosen a password
  if ($password != '') {

    if ($password == 'delete') $password = '';

    $sql = 'update users set password = "%s" where id = %d;';
    $result = db_query($sql, $password, $game_user->id);

    echo <<< EOF
<div class="title">
  <img src="/sites/default/files/images/{$game}_title.png"/>
</div>
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>
  <p>&quot;Hi <em>$game_user->username</em>,</p>
  <p class="second">&quot;Your password has been changed.&quot;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
  <p class="second">&nbsp;</p>
</div>
<div class="subtitle"><a
  href="/$game/home/$arg2"><img
  src="/sites/default/files/images/{$game}_continue.png"/></a></div>
EOF;

    db_set_active('default');
    return;

  } else { // haven't chosen a password on this screen yet

  if (empty($game_user->password)) {

    $quote = t("What's your password?&nbsp; It needs to be at least six
      characters long.");

    $quote2 = '&nbsp;';

  } else {

    $fetch_header($game_user); // allow them to navigate out of this
    $quote = "Hello, <em>$game_user->username</em>!&nbsp; What would you like
      your new password to be?";

    $quote2 = t("If you want to delete it, type the word
      'delete', all by itself, without the quotes.&nbsp; It must be all
      lowercase.");

  }

    echo <<< EOF
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_small">
  </div>$error_msg
  <p class="second">&quot;$quote&quot;</p>
  <p class="second">$quote2</p>
  <div class="ask-name">
    <form method=get action="/$game/elders_set_password/$arg2">
      <input type="text" name="password" width="20" maxlength="20"
        value="$game_user->password"/>
      <input type="submit" value="Submit"/>
    </form>
  </div>
</div>
EOF;

  }

  db_set_active('default');
