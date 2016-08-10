<?php

  global $game, $phone_id;

// we won't have gone through fetch_user() yet, so set these here
  $game = check_plain(arg(0));
  $get_phoneid = '_' . $game . '_get_phoneid';
  $phone_id = $get_phoneid();
  $arg2 = check_plain(arg(2));

  db_set_active('game_' . $game);

  $default_neighborhood = 75;
  $default_value = 'Credits';
//  $default_value = 'New Won';

// check to make sure not too many from the same IP address
  $sql = 'select count(`value`) as count from user_attributes
    where `key` = "last_IP" and `value` = "%s";';
  $result = db_query($sql, ip_address());
  $item = db_fetch_object($result);

// allow multiple from my IP
  if (($item->count > 5) && (ip_address() != '14.140.251.170') && // Amazon testing IP
    (ip_address() != '69.64.69.86') &&
    (ip_address() != '64.150.187.146')) {
    mail('joseph@cheek.com', 'too many users from IP ' . ip_address(),
      'The system successfully blocked an attempt to register user number ' .
      $item->count . ' (' . $arg2 . ').');
    echo 'Error E-2242: ' . $arg2 .
      '.  Please email <strong>support@cheek.com</strong>.';
    exit();
  }

  $sql = 'insert into users set phone_id = "%s", username = "", experience = 0,
    level = 1, fkey_neighborhoods_id = %d, fkey_values_id = 0,
    `values` = "%s",
    money = 1000, energy = 200, energy_max = 200';
  $result = db_query($sql, $phone_id, $default_neighborhood,
    $default_value);

  $sql = 'insert into user_creations set datetime = "%s", phone_id = "%s",
    remote_ip = "%s";';
  $result = db_query($sql, date('Y-m-d H:i:s'), $phone_id, ip_address());

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $game_user = $fetch_user();

  echo <<< EOF
<p>&nbsp;</p>
<div class="title">
  <img src="/sites/default/files/images/{$game}_title.png" width=300/>
</div>
<div class="welcome">
  <div class="wise_old_man_large point">
  </div>
EOF;

  switch ($_REQUEST['page']) {

    case 3:

      echo <<< EOF

  <p class="quote">Why are you still here?</p>
  <p>The ground stops spinning and you struggle to your feet.&nbsp;
    Not sure which direction to go, you just start running.</p>
</div>
EOF;

      _button('quests');
      _sound('panting');
      break;

    case 2:

      echo <<< EOF

  <p class="quote">I don't know why I care, but unless you want to get grisled
    by the Pounders, I suggest you run.</p>
</div>
EOF;

      _button('welcome', 'continue', '?page=3');
      break;

    default:

      echo <<< EOF

  <p>You are roused from your blackout by a strong kick to the midsection.</p>
  <p class="quote">Get up, <strong><em>Subjugate!</em></strong></p>
  <p>Your head throbs in pain.&nbsp; You're not sure where you are.&nbsp;
    You attempt to get to your feet, but fall back to the ground.</p>
</div>
EOF;

      _button('welcome', 'continue', '?page=2');
      _sound('huh');

  }

  db_set_active('default');
