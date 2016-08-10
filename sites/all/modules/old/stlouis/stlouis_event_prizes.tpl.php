<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');

  if ($phone_id != 'abc123') {
    db_set_active('default');
    return;
  }

  echo <<< EOF
<div class="title">
  Event Prizes
</div>
EOF;

  $prizes = array(
    array(100, 1, 37),
    array(80, 1, 37),
    array(60, 3, 37),
    array(50, 5, 37),
    array(40, 10, 37),
    array(30, 30, 37),
    array(25, 1, 38),
    array(20, 1, 38),
    array(15, 3, 38),
    array(10, 5, 38),
    array(5, 10, 38),
    array(4, 10, 38),
    array(3, 10, 38),
    array(2, 10, 38),
    array(1, 50, 38),
  );

  foreach ($prizes as $prize) {
// top $top get $quantity gifts #$prize_id

    $top = $prize[0];
    $quantity = $prize[1];
    $prize_id = $prize[2];

    echo '<div class="title">Top ' . $top . ' players get ' . $quantity .
      ' of equipment #' . $prize_id . '</div>';

    mail('joseph@cheek.com', 'event prizes',
      'Top ' . $top . ' players get ' . $quantity .
      ' of equipment #' . $prize_id . '.');

    $sql = 'select fkey_users_id as id, users.username from event_points
      left join users on fkey_users_id = users.id
      order by points DESC
      limit %d;'; // top %d players
    $result = db_query($sql, $top);
    $data = array();
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $user) {

// does user have any of this present?
      $sql = 'select quantity from equipment_ownership
        where fkey_users_id = %d
        and fkey_equipment_id = %d;';
      $result = db_query($sql, $user->id, $prize_id);
      $equip_quantity = db_fetch_object($result);

      if (empty($equip_quantity)) { // create record

        $sql = 'insert into equipment_ownership
          (fkey_users_id, fkey_equipment_id, quantity)
          values
          (%d, %d, %d);';

        $result = db_query($sql, $user->id, $prize_id, $quantity);

echo '<div class="subsubtitle">creating record for ' . $user->username .
  '</div>';

      } else { // update record

        $sql = 'update equipment_ownership
          set quantity = quantity + %d
          where fkey_users_id = %d
          and fkey_equipment_id = %d;';

        $result = db_query($sql, $quantity, $user->id, $prize_id);

echo '<div class="subsubtitle">updating record for ' . $user->username .
  '</div>';


      } // create or update record

    } // foreach user

  } // foreach prize

  db_set_active('default');
