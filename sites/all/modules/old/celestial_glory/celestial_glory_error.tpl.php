<?php

$game = check_plain(arg(0));
$get_phoneid = '_' . $game . '_get_phoneid';
$phone_id = $get_phoneid();
$arg2 = check_plain(arg(2));

include(drupal_get_path('module', $game) . '/game_defs.inc');

echo <<< EOF
  <div class="title">
    <img src="/sites/default/files/images/{$game}_title.png"/>
  </div>
  <p>&nbsp;</p>
  <div class="welcome">
   <div class="wise_old_man_large">

  </div>
  <p>And it came to pass that</p>
  <p class="second">Error <strong>{$error_code}</strong></p>
  <p class="second">happened for user</p>
  <p class="second"><strong>{$phone_id}</strong>.</p>
  <p class="second">Please report this to
    <strong>zipport@ziquid.com</strong>.
  </p>
  </div>
EOF;

slack_send_message('Error ' . $error_code . ' for phone ID '
. $phone_id, $slack_channel);
