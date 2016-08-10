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

//  $party_title = preg_replace('/^The /', '', $item->party_title);

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

  $sql = 'select count(*) as total from competencies;';
  $result = db_query($sql);
  $count = db_fetch_object($result);
  $total_comps = $count->total - 1;

  $sql = 'select count(*) as total from user_competencies
    where fkey_users_id = %d;';
  $result = db_query($sql, $item->id);
  $count = db_fetch_object($result);
  $user_comps = $count->total;

  if ($phone_id_to_check == $phone_id) {
    $stats = t('You have discovered @user out of @total competencies',
      array('@user' => $user_comps, '@total' => $total_comps));
  }
  else {
    $stats = '';
  }

  echo <<< EOF
<div class="title">
  $item->ep_name <span class="username">$item->username</span> $clan_acronym
</div>
<div class="subsubtitle">$stats</div>
<div class="subsubtitle">Recently-increased competencies are yellow<br>
Wait a few minutes before increasing them</div>
<div class="user-profile">
EOF;

  if ($phone_id_to_check == $phone_id) { // show more stats if it's you

    $sql = 'SELECT * FROM  `user_competencies`
      LEFT JOIN competencies ON fkey_competencies_id = competencies.id
      WHERE fkey_users_id = %d
      ORDER BY level DESC, name ASC;';
    $result = db_query($sql, $item->id);
    while ($item = db_fetch_object($result)) $comps[] = $item;

    $old_level = 0;

    foreach ($comps as $comp) {

      $level = $comp->level;
      $comp = (object) array_merge(
        (array) $comp,
        (array) competency_level($game_user,
          intval($comp->fkey_competencies_id))
      );
// Quick-n-dirty: merge the two arrays
// firep($comp, 'competency');
      $pip = '';

      for ($c = 1; $c <= 5; $c++) {
        $competent = ($c <= $comp->level) ? 'attained' : '';
        $pip .= '<span class="competency-pip ' . $competent . '"></span>';
      }

      if ((time() - strtotime($comp->timestamp)) < 300) {
        $too_soon = 'too-soon';
      }
      else {
        $too_soon = '';
      }

      $need_more = $comp->next - $comp->use_count;

      if ($level != $old_level) {
        echo '<div class="title">~~ Level ' . $level
        . ' ~~</div>';
      }

      echo <<< EOF
  <div class="heading wider initial-caps">$comp->name :</div>
  <div class="value">
    $pip
    <span class="small $too_soon">
      &nbsp;($comp->level_name, next: +$need_more)
    </span>
  </div>
  <br>
EOF;

      $old_level = $level;

    }

  }

  echo <<< EOF
</div>
EOF;

  db_set_active('default');
