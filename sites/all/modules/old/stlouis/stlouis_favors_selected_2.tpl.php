<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  list($player, $hash) = explode(':', check_plain($_GET['player']));
  $time = check_plain($_GET['time']);
  $success = check_plain($_GET['success']);

  $game_user = $fetch_user();

  $extra_actions = 0;
  if ($time == 14400) $extra_actions = 3;
  if ($time == 43200) $extra_actions = 1;

  if ($extra_actions > 0) action_use($game_user, $extra_actions);

  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $favor_succeeded = TRUE;
  $fvr = $favor;
  show_elections_menu($game_user);
  
// get the favor
  $sql = 'select favors.*, elected_positions.name as ep_name,
    cr.name as cr_name, ce.name as ce_name from favors

    LEFT JOIN elected_positions
    ON favors.fkey_required_elected_positions_id = elected_positions.id

    LEFT JOIN competencies as cr
    ON favors.fkey_required_competencies_id = cr.id

    LEFT JOIN competencies as ce
    ON favors.fkey_enhanced_competencies_id = ce.id

    ' . $active_favors .
    ' where favors.id = %d;';
  $result = db_query($sql, $favor_id);
  $favor = db_fetch_object($result);
firep($favor);

  echo <<< EOF
<div class="title">
  {$fvr}: $favor->name
</div>
EOF;

//  if ($phone_id != 'abc123') { // check for initiator prerequisites

    if ($game_user->actions < 0) { // not enough action

      $favor_succeeded = FALSE;

      if (substr($phone_id, 0, 3) == 'ai-')
        $ai_output = 'favor-failed no-action';

      $outcome_reason = '
        <div class="land failed">
          <div class="title">' .
            t('∆ Action Quantity ∆<br/><span>Insufficient</span>') . '
          </div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/elders_do_fill/' . $arg2 .
                '/action?destination=/' . $game . '/favors_selected_2/' .
                $arg2 . '?time=' . $time . '+player=' . $player . '+success=' .
                $success . '">
                Refill your Action (2&nbsp;' . $luck . ')
              </a>
            </div>
          </div>
        </div>';

    } // not enough action

//  } // not abc123


// validate the user selected via hash
  $hash1 = substr(md5(date('Y-M-d-H-i') . '-' . $player), 0, 8);
  $hash2 = substr(md5(date('Y-M-d-H-i', time() - 60) . '-' . $player), 0, 8);
  $hash3 = substr(md5(date('Y-M-d-H-i', time() - 120) . '-' . $player), 0, 8);

  if (($hash != $hash1) && ($hash != $hash2) && ($hash != $hash3)) {

    $favor_succeeded = FALSE;

    $outcome_reason = '
      <div class="land failed">
        <div class="title">' .
          t('∆ Available Players ∆<br/><span>Insufficient</span>') . '
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/favors/' . $arg2 . '">' .
              t('Choose a different @favor', array('@favor' => $fvr)) . '
            </a>
          </div>
        </div>
      </div>';

  }

  if (!$favor_succeeded) {

    echo $outcome_reason;

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"$ai_output " .
      filter_xss($outcome_reason, array()) .
      " \"/>\n-->";

    db_set_active('default');
    return;

  }

  $item = fetch_user_by_id((int) $player);

// add the favor to the list
  $sql = 'insert into favor_requests

    (fkey_favors_id, fkey_users_from_id, fkey_users_to_id, time_due,
      success_expected)

    values

    (%d, %d, %d, "%s", "%s");';

  $result = db_query($sql, $favor_id, $game_user->id, $player,
    date('Y-m-d H:i:s', time() + $time), $success);

// notify the runner

  $sql = 'insert into challenge_messages
    (fkey_users_from_id, fkey_users_to_id, message)
    values (%d, %d, "%s");';
  $message = t('@user has requested a @favor from you: @favor_name.',
    array('@user' => $game_user->username, '@favor' => $fvr,
      '@favor_name' => $favor->name));
  $result = db_query($sql, $game_user->id, $player, $message);

// give success message

  if (!empty($item->clan_acronym)) {
    $clan_acronym = "($item->clan_acronym)";
    $clan_link = $item->clan_name;
  } else {
    $clan_link = t('None');
  }

  if ($item->is_clan_leader) {
    $clan_acronym .= '*';
    $clan_link .= " (leader)";
  }

  echo <<< EOF
<div class="land">
  <div class="title">
    // FAVOR REQUEST ACTIVE //
  </div>
  <div class="subtitle">
    $item->ep_name <span class="username">$item->username</span>
      $clan_acronym
  </div>
  <div class="subtitle">
    has been assigned the $fvr
  </div>
  <div class="subtitle">
    :: $favor->name ::
  </div>
EOF;

  _button('favors');

echo <<< EOF
</div>
EOF;

  db_set_active('default');
