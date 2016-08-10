<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

/*  if ($game == 'stlouis') {

    $link = $destination ? $destination : "/$game/user/$arg2";

    $fetch_header($game_user);

    echo <<< EOF
<div class="title">
  Luck-free 4th
</div>
<div class="subtitle">
  Sorry!&nbsp; No $luck today!
</div>
<div class="subtitle">
  <a href="$link">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

    db_set_active('default');

    return;

  }*/

  $quest_lower = strtolower($quest);
  $experience_lower = strtolower($experience);

  switch ($fill_type) {

    case 'action':

/*      if (($game == 'stlouis') &&
        ($game_user->actions < $game_user->actions_max)) {

        $sql = 'update users set actions = actions_max where id = %d;';
        $result = db_query($sql, $game_user->id);

        $game_user = $fetch_user();
         $fetch_header($game_user);

        echo '<div class="subtitle">Amusez-vous bien !</div>';
        echo '<div class="subtitle">
          <a href="/' . $game . '/home/' . $arg2 . '">
            <img src="/sites/default/files/images/' . $game . '_continue.png"/>
          </a>
        </div>';

        db_set_active('default');
        return;

      }
*/
      if ($game_user->luck < 2) {

        $fetch_header($game_user);

        echo '<div class="land-failed">' .
          t('Out of @s!', array('@s' => $luck)) .
          '</div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/elders_ask_purchase/' . $arg2 . '">
                Purchase more ' . $luck . '
              </a>
            </div>
          </div>';

        db_set_active('default');
        return;

      }

      if ($game_user->actions < $game_user->actions_max) {
        $sql = 'update users set actions = actions_max, luck = luck - 2
          where id = %d;';
        $result = db_query($sql, $game_user->id);
      }

      break;

    case 'energy':

      if ($game_user->luck < 2) {

        $fetch_header($game_user);

        echo '<div class="land-failed">' .
          t('Out of @s!', array('@s' => $luck)) .
          '</div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/elders_ask_purchase/' . $arg2 . '">
                Purchase more ' . $luck . '
              </a>
            </div>
          </div>';

        db_set_active('default');
        return;

      }

      if ($game_user->energy < $game_user->energy_max) {

        $sql = 'update users set energy = energy_max, luck = luck - 2
          where id = %d;';
        $result = db_query($sql, $game_user->id);

      }

      break;

    case 'money':

      if ($game_user->luck < 1) {

        $fetch_header($game_user);

        echo '<div class="land-failed">' .
          t('Out of @s!', array('@s' => $luck)) .
          '</div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/elders_ask_purchase/' . $arg2 . '">
                Purchase more ' . $luck . '
              </a>
            </div>
          </div>';

        db_set_active('default');
        return;

      }

      $offer = ($game_user->income - $game_user->expenses) * 5;
      $offer = min($offer, $game_user->level * 1000);
      $offer = max($offer, $game_user->level * 100);

      $sql = 'update users set money = money + %d, luck = luck - 1
        where id = %d;';
      $result = db_query($sql, $offer, $game_user->id);

      break;

  }

  drupal_goto($game . '/user/' . $arg2);
