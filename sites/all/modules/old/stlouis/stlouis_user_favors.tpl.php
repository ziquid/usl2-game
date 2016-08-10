<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $fetch_header($game_user);
  $arg2 = check_plain(arg(2));
  $arg3 = check_plain(arg(3));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  _show_profile_menu($game_user);

  $phone_id_to_check = $phone_id;
  if ($arg3 != '') $phone_id_to_check = $arg3;

  if (substr($arg3, 0, 3) == 'id:') {

    $sql = 'select phone_id from users where id = %d;';
    $result = db_query($sql, (int) substr($arg3, 3));
    $item = db_fetch_object($result);
    $phone_id_to_check = $item->phone_id;

  }

  $show_all = FALSE;
  
  if (($phone_id_to_check == $phone_id) ||
    ($_GET['show_all'] == 'yes'))
    $show_all = TRUE;

  $item = fetch_user_by_id($phone_id_to_check);

  $party_title = preg_replace('/^The /', '', $item->party_title);

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
  
  if (($game_user->fkey_clans_id) &&
    ($game_user->fkey_clans_id == $item->fkey_clans_id)) {
      
      $clan_link = '<a href="/' . $game . '/clan_list/' . $arg2 .
        '/' . $game_user->fkey_clans_id . '">' . $clan_link . '</a>';
      
  }
  
  echo <<< EOF
<div class="title">
  $item->ep_name <span class="username">$item->username</span> $clan_acronym
</div>
EOF;

  $sql = 'select count(*) as count
    from favor_requests where fkey_users_to_id = %d
    and time_completed = 0;';
  $result = db_query($sql, $game_user->id);
  $runner = db_fetch_object($result);

  $sql = 'select count(*) as count
    from favor_requests where fkey_users_from_id = %d
    and time_completed = 0;';
  $result = db_query($sql, $game_user->id);
  $initiator = db_fetch_object($result);

  if ($phone_id_to_check == $phone_id) {
    $you = 'You';
  } else {
    $you = "<span class=\"username\">$item->username</span>";
  }

  if ($phone_id_to_check == $phone_id) { // show more stats if it's you

    echo <<< EOF
  <div class="user-profile">
    <div class="subtitle">
      // Initiated By $you //
    </div>
    <div class="heading">Completed : </div>
    <div class="value">$item->favors_asked_completed</div><br/>
    <div class="heading">Not Completed : </div>
    <div class="value">$item->favors_asked_noncompleted</div><br/>
    <div class="heading">Active : </div>
    <div class="value">$initiator->count</div><br/>
  </div>
EOF;

  }

  echo <<< EOF
  <div class="user-profile">
    <div class="subtitle">
      // Requested Of $you //
    </div>
    <div class="heading">Completed : </div>
    <div class="value">$item->favors_completed</div><br/>
    <div class="heading">Not Completed : </div>
    <div class="value">$item->favors_noncompleted</div><br/>
    <div class="heading">Active : </div>
    <div class="value">$runner->count</div><br/>
  </div>
EOF;

  if ($phone_id_to_check == $phone_id) { // show more stats if it's you

    $sql = 'select favor_requests.*, favors.name, favors.runner_description,
      favors.active, favors.id as favor_id, favors.runner_actions_cost,
      favors.values_cost, favors.fkey_enhanced_competencies_id,
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

      where favor_requests.fkey_users_to_id = %d
      and favor_requests.time_completed = 0

      order by time_due ASC
      limit 10;';

    $result = db_query($sql, $game_user->id);
    while ($favor = db_fetch_object($result)) $favors[] = $favor;

    foreach ($favors as $favor) {
firep($favor);

      _show_favor($game_user, $favor, 'runner');

    }

  }

  db_set_active('default');
