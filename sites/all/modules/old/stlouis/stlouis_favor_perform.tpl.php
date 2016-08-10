<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $fvr = $favor;
  $favor_succeeded = TRUE;

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  $sql = 'select favor_requests.*, favors.name, favors.runner_description,
    favors.active, favors.id as favor_id, favors.runner_actions_cost,
    favors.values_cost, favors.fkey_enhanced_competencies_id,
    favors.fkey_required_elected_positions_id,
    favors.fkey_required_competencies_id,
    favors.required_competencies_level,
    elected_positions.name as ep_name,
    cr.name as cr_name, ce.name as ce_name

    from favor_requests

    left join favors on favor_requests.fkey_favors_id = favors.id

    LEFT JOIN elected_positions
    ON favors.fkey_required_elected_positions_id = elected_positions.id

    LEFT JOIN competencies as cr
    ON favors.fkey_required_competencies_id = cr.id

    LEFT JOIN competencies as ce
    ON favors.fkey_enhanced_competencies_id = ce.id

    where favor_requests.id = %d
    and favor_requests.fkey_users_to_id = %d
    and favor_requests.fkey_users_from_id <> %d
    and favor_requests.time_completed = 0;';

  $result = db_query($sql, $req_id, $game_user->id, $game_user->id);
  $favor = db_fetch_object($result);
firep($favor);

// valid favor?

  if (empty($favor)) {

    $favor_succeeded = FALSE;

    if (substr($phone_id, 0, 3) == 'ai-')
      $ai_output = 'favor-failed no-favor';

    $outcome_reason = '
      <div class="land failed">
        <div class="title">' .
          t('∆ Favor Request ∆<br/><span>NonExistant</span>') . '
        </div>
      </div>';

    _karma($game_user, 'attempting to perform non-existant favor', -50);

  } // favor doesn't exist

  if ($favor->runner_actions_cost > $game_user->actions) { // not enough action

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
              '/action?destination=/' . $game . '/user_favors/' . $arg2 . '">
              Refill your Action (2&nbsp;' . $luck . ')
            </a>
          </div>
        </div>
      </div>';

  } // not enough action

  if (($favor->fkey_required_elected_positions_id > 0) &&
    ($favor->fkey_required_elected_positions_id != $game_user->ep_level)) {
// wrong caste level

    $favor_succeeded = FALSE;

    if (substr($phone_id, 0, 3) == 'ai-')
      $ai_output = 'favor-failed wrong-caste-level';

    $outcome_reason = '
      <div class="land failed">
        <div class="title">' .
          t('∆ Must Be <span>@ep_name</span> ∆',
            array('@ep_name' => $favor->ep_name)) . '
        </div>
      </div>';

  } // wrong caste level

  if (($favor->fkey_required_competencies_id > 0) &&
    ($favor->required_competencies_level > 0)) {
// under-developed competency?

    $count = competency_level($game_user,
      (int) $favor->fkey_required_competencies_id);

    if (($count->level < $favor->required_competencies_level)) { // yes

      $favor_succeeded = FALSE;

      if (substr($phone_id, 0, 3) == 'ai-')
        $ai_output = 'favor-failed wrong-caste-level';

      $outcome_reason = '
        <div class="land failed">
          <div class="title">' .
            t('∆ Required Competency ∆<br/>' .
              '<span>@competency</span><br/><br/>' .
              'Level<br/>' .
              '<span>@comp_level</span>',
              array('@competency' => $favor->cr_name,
                '@comp_level' =>
                  _competency_level_name($favor->required_competencies_level)
              )
            ) . '
          </div>
        </div>';

    } // under-developed competency

  } // under-developed competency?

// did not succeed

  if (!$favor_succeeded) {

    $fetch_header($game_user);

    _show_profile_menu($game_user);

    echo <<< EOF
<div class="title">
  Perform $fvr
</div>
EOF;

    echo $outcome_reason;

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"$ai_output " .
      filter_xss($outcome_reason, array()) .
      " \"/>\n-->";

    _button();

    db_set_active('default');
    return;

  }

  $cost = (int) $favor->values_cost;
  $bounty = mt_rand($cost / 2, $cost * 5);

  if ($favor->success_expected == 'y') // initiator expected success; split bounty
    $bounty = floor($bounty / 2);

  $message = ($favor->success_expected == 'y') ?
    "I completed your favor on time.&nbsp; We split the " . ($bounty * 2) .
      " credit bounty." :
    "I completed your favor on time.&nbsp; I receive the full
      $bounty credit bounty.";

  $sql = 'insert into challenge_messages
    (fkey_users_from_id, fkey_users_to_id, message)
    values (%d, %d, "%s");';
  $result = db_query($sql, $favor->fkey_users_to_id, $favor->fkey_users_from_id,
    $message);

// delete favor
  $sql = 'delete from favor_requests
    where id = %d;';
  $result = db_query($sql, $favor->id);

// increment user stats
  $sql = 'update users
    set favors_asked_completed = favors_asked_completed + 1,
    money = money + %d
    where id = %d;';
  $result = db_query($sql,
    ($favor->success_expected == 'y') ? $bounty : 0,
    $favor->fkey_users_from_id);

  $sql = 'update users
    set favors_completed = favors_completed + 1,
    actions = actions - %d,
    money = money + %d
    where id = %d;';
  $result = db_query($sql,
    $favor->runner_actions_cost, $bounty,
    $favor->fkey_users_to_id);

  if ($favor->fkey_enhanced_competencies_id > 0)
    competency_gain($game_user, (int) $favor->fkey_enhanced_competencies_id);

// start the actions clock if needed
  if ($game_user->actions == $game_user->actions_max) {

    $sql = 'update users set actions_next_gain = "%s" where id = %d;';
    $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
      $game_user->id);

  }

  $game_user = $fetch_user(); // reprocess user object
  $fetch_header($game_user);

  _show_profile_menu($game_user);

  echo <<< EOF
<div class="title">
  Perform $fvr
</div>
<div class="quests succeeded">
  <div class="title">
    // Attempt Successful //
  </div>
</div>
EOF;

  _show_favor($game_user, $favor, 'perform', $bounty);
  _button('user_favors');

  db_set_active('default');
