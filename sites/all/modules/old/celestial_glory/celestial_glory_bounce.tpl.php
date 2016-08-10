<?php

  $game = check_plain(arg(0));
  $arg2 = check_plain(arg(2));
  $get_id = '_' . $game . '_get_fbid';

  if ($arg2 == 'facebook') {

    $phone_id = $get_id();
// echo $phone_id;
    echo <<< EOF
<style>
body {
  text-align: center;
}
img {
  width: 320px;
}
input {
  position: relative;
  top: -256px;
  font-size: 200%;
}
</style>
<img src="/sites/default/files/images/cg_splash_new.jpg"/>
<form method=post action="/$game/home/$arg2">
<input type="Submit" value="Continue to the game"/>
</form>
EOF;

    db_set_active('default');
    return;

  }

  db_set_active('default');
  drupal_goto($game . '/home/' . $phone_id);
