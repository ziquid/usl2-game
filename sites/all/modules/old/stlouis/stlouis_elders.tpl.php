<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $get_value = '_' . $game . '_get_value';

  if (substr($phone_id, 0, 3) == 'ai-') {
// useful for freshening stats
    echo "<!--\n<ai \"elders\"/>\n-->";
    db_set_active('default');
    return;
  }

  if ($game_user->fkey_values_id > 0) {
    $join_party_luck = 5;
    $join_party_luck_text = '5&nbsp;' . $luck;
    $different = 'different';
  } else {
    $join_party_luck = 0;
    $join_party_luck_text = t('Free');
    $different = '';
  }

  if ($game_user->luck >= $join_party_luck) {

    $join_party_text = '<div class="menu-option">
        <a href="/' . $game . '/choose_clan/' . $arg2 . '/0">
          Join a ' . $different . ' ' . $party_lower . ' (' .
            $join_party_luck_text . ')
        </a>
      </div>';

  } else {

    $join_party_text = '<div class="menu-option not-yet">
        Join a ' . $different . ' ' . $party_lower . ' (' .
          $join_party_luck_text . ')
      </div>';

  }

  $offer = ($game_user->income - $game_user->expenses) * 5;
  $offer = min($offer, $game_user->level * 1000);
  $offer = max($offer, $game_user->level * 100);

  $text = t(empty($game_user->username) ? 'How can I help you?' :
    'How can I help you, @username?',
      array('@username' => $game_user->username));

  echo <<< EOF
<div class="title">Ask $elders</div>
<div class="welcome">
  <div class="holodad">
  </div>
  <p class="quote">$text</p>
  <p>&laquo; You have $game_user->luck&nbsp;$luck &raquo;</p>
</div>
<div class="elders-menu">
EOF;

  if ($game_user->level >= 6) {
// only allow users to change parties if they can join one

    if ($game_user->luck > 9) { // AT LEAST 10 LUCK

      echo <<< EOF
<div class="menu-option"><a href="/$game/choose_name/$arg2">Change your
  character's name (10&nbsp;$luck)</a></div>
$join_party_text
<div class="menu-option"><a href="/$game/elders_ask_reset_skills/$arg2">Reset
  your skill points (3&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/action">Refill
  your Action (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/energy">Refill
  your Energy (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/money">Receive
  $offer $game_user->values (1&nbsp;$luck)</a></div>
EOF;

    } elseif ($game_user->luck > 4) { // AT LEAST 5 LUCK

      echo <<< EOF
<div class="menu-option not-yet">Change your character's name (10&nbsp;$luck)</div>
$join_party_text
<div class="menu-option"><a href="/$game/elders_ask_reset_skills/$arg2">Reset
  your skill points (3&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/action">Refill
  your Action (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/energy">Refill
  your Energy (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/money">Receive
  $offer $game_user->values (1&nbsp;$luck)</a></div>
EOF;

    } elseif ($game_user->luck > 2) {
// AT LEAST THREE LUCK

      echo <<< EOF
<div class="menu-option not-yet">Change your character's name (10&nbsp;$luck)</div>
$join_party_text
<div class="menu-option"><a href="/$game/elders_ask_reset_skills/$arg2">Reset
  your skill points (3&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/action">Refill
  your Action (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/energy">Refill
  your Energy (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/money">Receive
  $offer $game_user->values (1&nbsp;$luck)</a></div>
EOF;
  		
    } elseif ($game_user->luck > 1) { // AT LEAST TWO LUCK

      echo <<< EOF
<div class="menu-option not-yet">Change your character's name (10&nbsp;$luck)</div>
$join_party_text
<div class="menu-option not-yet">Reset your skill points (3&nbsp;$luck)</div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/action">Refill
  your Action (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/energy">Refill
  your Energy (2&nbsp;$luck)</a></div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/money">Receive
  $offer $game_user->values (1&nbsp;$luck)</a></div>
EOF;
  		
   } elseif ($game_user->luck > 0) { // AT LEAST ONE LUCK

      echo <<< EOF
<div class="menu-option not-yet">Change your character's name (10&nbsp;$luck)</div>
$join_party_text
<div class="menu-option not-yet">Reset your skill points (3&nbsp;$luck)</div>
<div class="menu-option not-yet">Refill your Action (2&nbsp;$luck)</div>
<div class="menu-option not-yet">Refill your Energy (1&nbsp;$luck)</div>
<div class="menu-option"><a href="/$game/elders_do_fill/$arg2/money">Receive
  $offer $game_user->values (1&nbsp;$luck)</a></div>
EOF;
  		
    } else { // NO LUCK!

      echo <<< EOF
<div class="menu-option not-yet">Change your character's name (10&nbsp;$luck)</div>
$join_party_text
<div class="menu-option not-yet">Reset your skill points (3&nbsp;$luck)</div>
<div class="menu-option not-yet">Refill your Action (2&nbsp;$luck)</div>
<div class="menu-option not-yet">Refill your Energy (2&nbsp;$luck)</div>
<div class="menu-option not-yet">Receive $offer $game_user->values (1&nbsp;$luck)</div>
EOF;
  		
    } // luck?

  } // level >= 6?

  echo <<< EOF
<div class="menu-option"><a href="/$game/elders_set_password/$arg2">Set a
  password for your account (Free)</a></div>
  <div class="menu-option"><a href="/$game/elders_ask_reset/$arg2">Reset
  your character (Free)</a></div>
  <!--<div class="menu-option"><a href="/$game/elders_preferences/$arg2">Game 
  Preferences</a></div>-->
<div class="menu-option"><a href="/$game/elders_ask_purchase/$arg2">Purchase
  more $luck</a></div>
EOF;

  if ($get_value($game_user->id, 'allow_2114', 0) == 1) {

    if ($_SERVER['HTTP_HOST'] == 'codero1.cheek.com') {
      $host = 'stl2114.game.ziquid.com';
    } else {
      $host = 'codero1.cheek.com';
    }

    echo <<< EOF
<div class="menu-option"><a href="http://$host/$game/home/$arg2">Switch
  games</a></div>
EOF;

  }

  echo '</div>';

  db_set_active('default');
