<?php

  $game = check_plain(arg(0));
  db_set_active('default');
  drupal_goto($game . '/home/' . $phone_id);

//For future use.
  echo <<< EOF
It is a bounce page
EOF;

