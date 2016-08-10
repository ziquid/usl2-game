<?php

  global $game, $phone_id;

// we won't have gone through fetch_user() yet, so set these here
  $game = check_plain(arg(0));
  $get_phoneid = '_' . $game . '_get_phoneid';
  $check_authkey = '_' . $game . '_check_authKey';
  $phone_id = $get_phoneid();

  $arg2 = check_plain(arg(2));

  db_set_active('game_' . $game);

  $sql = 'select * from users where phone_id = "%s";';
  $result = db_query($sql, $phone_id);
  $game_user = db_fetch_object($result);
  $check_authkey($game_user);

// check for authorized client
  if ((strpos($_SERVER['HTTP_USER_AGENT'], 'com.ziquid.celestialglory') === FALSE) &&
    (strpos($_SERVER['HTTP_USER_AGENT'], 'com.cheek.celestialglory') === FALSE) &&
    ($_SERVER['REMOTE_ADDR'] != '66.211.170.66') && // paypal IPN
    ($_SERVER['REMOTE_ADDR'] != '173.0.81.1') && // paypal IPN
    ($_SERVER['REMOTE_ADDR'] != '173.0.81.33') && // paypal IPN
    ($user->roles[4] != 'web game access') && // web users
    (substr(arg(2), 0, 3) != 'fb=') && // identified facebook user
    (substr(arg(2), 0, 3) != 'ai-') && // AI player
    (arg(2) != 'facebook') && // unidentified facebook user
    (substr(arg(2), 0, 3) != 'ms=') // unidentified MS user
  ) {
/*
      mail('joseph@cheek.com', 'unauthorized client',
       	"Unauthorized user agent of " . $_SERVER['HTTP_USER_AGENT'] .
        " for phone_id " . check_plain(arg(2)));
*/
      echo t('This game must be accessed through an authorized client.  ');
      echo t('Please e-mail zipport@ziquid.com if you have any questions.');
      exit;

  }

  $password = trim(check_plain($_GET['password']));

  if ($password == $game_user->password) {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_addr = ip_address();

    $extra_stuff_pos = stripos($user_agent, '(com.cheek');
    if ($extra_stuff_pos !== FALSE) { // remove our added stuff, if present
      $user_agent = trim(substr($user_agent, 0, $extra_stuff_pos));
    }

    $set_value = '_' . $game . '_set_value';
    $set_value($game_user->id, 'user_agent', $user_agent);
    $set_value($game_user->id, 'last_IP', $ip_addr);

    $save_authKey = '_' . $game . '_save_authKey';
    $get_authKey = '_' . $game . '_get_authKey';
    $save_authKey($game_user, $get_authKey());

    competency_gain($game_user, 'security conscious');

//    mail('joseph@cheek.com', 'successful user authentication',
//      $game_user->username . ' has successfully entered his or her password.');

    db_set_active('default');
    drupal_goto("$game/home/$arg2");

  }

//  mail('joseph@cheek.com', 'password challenge',
//    $game_user->username . ' has been asked for his or her password.');

  echo <<< EOF
<div class="title">
<img src="/sites/default/files/images/{$game}_title.png"/>
</div>
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_large">
  </div>
  <p>Welcome back, $game_user->username.</p>
  <p class="second">
    You are almost ready to continue playing!&nbsp;
    Just to ensure you are the correct player, will you give me your
    password?
  </p>
  <p class="second">
    If you can't remember it, you can e-mail <strong>zipport@ziquid.com</strong>
    and we can reset it for you.
  </p>
  <div class="ask-name">
    <form method=get action="/$game/authenticate/$arg2">
      <input type="password" name="password" width="20" maxlength="20"/>
      <input type="submit" value="Submit"/>
    </form>
  </div>
</div>
EOF;

  db_set_active('default');
