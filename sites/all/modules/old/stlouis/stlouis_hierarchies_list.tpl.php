<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

// do AI moves from this page!!!
  include_once(drupal_get_path('module', $game) . '/' . $game . '_ai.inc');
  ($game == 'stlouis') && ((mt_rand(0, 5) == 1) || ($arg2 == 'abc123')) &&
    _move_ai();

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

  show_elections_menu($game_user);    

  $sql = 'select name, has_elections, rating, residents, district
    from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $data = db_fetch_object($result);
  $location = $data->name;
  
  echo <<< EOF
<div class="title">$location Caste Hierarchy</div>
EOF;

  $sql = 'SELECT * from elected_positions
    WHERE type = 1
    ORDER BY elected_positions.id DESC;';
  
  $result = db_query($sql);
  while ($item = db_fetch_object($result)) $epos[] = $item;

  $last_group = $epos[0]->group;
  
  foreach ($epos as $pos) {
firep($pos);

  if ((($pos->id - $game_user->ep_level) > 1) && ($phone_id != 'abc123'))
    continue;

  $you = '';

  if ($game_user->ep_name == $pos->name)
    $you = ':&lt; Current Hierarchical Status';

  switch($pos->id) {

    case 0:

      $sql = 'select count(users.id) as count from users

        LEFT OUTER JOIN elected_officials
        ON elected_officials.fkey_users_id = users.id

        LEFT OUTER JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id

        WHERE elected_positions.id IS NULL
        AND users.fkey_neighborhoods_id = %d;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id);
      $count = db_fetch_object($result);
      $count_text = '&raquo;&raquo; ' . $count->count . ' players in ' . $location;

      $sql = 'select users.* from users

        LEFT OUTER JOIN elected_officials
        ON elected_officials.fkey_users_id = users.id

        LEFT OUTER JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id

        WHERE elected_positions.id IS NULL
        AND users.fkey_neighborhoods_id = %d
        ORDER BY RAND() LIMIT 3;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id);
      unset($ex_users);
      while ($ex_user = db_fetch_object($result)) $ex_users[] = $ex_user;

      break;

    default:

      $sql = 'select count(users.id) as count from users

        LEFT OUTER JOIN elected_officials
        ON elected_officials.fkey_users_id = users.id

        LEFT OUTER JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id

        WHERE elected_positions.id = %d
        AND users.fkey_neighborhoods_id = %d;';

      $result = db_query($sql, $pos->id, $game_user->fkey_neighborhoods_id);
      $count = db_fetch_object($result);
      $count_text = '++ ' . $count->count . ' players in ' . $location;

      $sql = 'select users.* from users

        LEFT OUTER JOIN elected_officials
        ON elected_officials.fkey_users_id = users.id

        LEFT OUTER JOIN elected_positions
        ON elected_positions.id = elected_officials.fkey_elected_positions_id

        WHERE elected_positions.id = %d
        AND users.fkey_neighborhoods_id = %d
        ORDER BY RAND() LIMIT 3;';

      $result = db_query($sql, $pos->id, $game_user->fkey_neighborhoods_id);
      unset($ex_users);
      while ($ex_user = db_fetch_object($result)) $ex_users[] = $ex_user;

      break;

  }

firep($count);

  if ($count->count > 0) {

    $c = 0;
    $count_text .= ', including ';

    foreach ($ex_users as $ex_user) {

      $c++;

      if (($c == count($ex_users)) && ($c > 1)) $count_text .= 'and ';
firep($ex_user);
      $count_text .= '<a href="/' . $game . '/user/' . $arg2 . '/id:' .
        $ex_user->id . '">' . $ex_user->username . '</a>';

      if ($c < count($ex_users)) $count_text .= ', ';

    }

  }

/*
    if ($last_group != $pos->group)
      echo '</div><div class="elections">';
*/

  if ((($pos->id - $game_user->ep_level) == 1) || ($phone_id == 'abc123')) {
    $progress = hierarchy_status($game_user, $pos->id);
  } else {
    unset($progress);
  }
  
  echo <<< EOF
<div class="hierarchy">
  <div class="title">
    $pos->name $you
  </div>
  <div class="level-count">
    Level $pos->id / 20 $count_text
  </div>
  <div class="hierarchy-requisites">
    // DISBURSEMENTS //
  </div>
  <div class="hierarchy-attained">
    Energy Gain: +$pos->energy_bonus / 60m
  </div>
  <div class="description">
    &laquo; $pos->description &raquo;
  </div>
EOF;

  for ($c = 0; $c < count($progress->target) ; $c++) {

    $needed = $progress->target[$c];
    $has = $progress->progress[$c];
    $sat_class = $progress->passed[$c] ? '' : 'FALSE';
    $sat = $progress->passed[$c] ? t('(COMPLETE)') : t('(INCOMPLETE)');

    echo <<< EOF
  <div class="hierarchy-requisites $sat_class">
    // REQUISITE #$c DETAIL //
  </div>
  <div class="hierarchy-needed $sat_class">
    $needed
  </div>
  <div class="hierarchy-progress $sat_class">
    &raquo;&raquo; PROGRESS $sat &raquo;&raquo;
  </div>
  <div class="hierarchy-needed $sat_class">
    $has
  </div>
EOF;

  }

  if (isset($progress->qualified)) {

    $sat = $progress->qualified ? t('TRUE') : t('FALSE');

    echo <<< EOF
  <div class="hierarchy-satisfied">
    // ALL REQUISITES SATISFIED //
  </div>
  <div class="hierarchy-attained $sat">
    <span>$sat</span>
  </div>
EOF;

    if ($progress->qualified) {

      echo <<< EOF
  <div class="try-an-election-wrapper">
    <div class="try-an-election">
      <a href="/$game/hierarchy_rise/$arg2/$pos->id">
        &lt;&lt; Become $pos->name &gt;&gt;
      </a>
    </div>
  </div>
EOF;

    } else {

      echo <<< EOF
  <div class="try-an-election-wrapper">
    <div class="try-an-election not-yet">
      Cannot Become $pos->name
    </div>
  </div>
EOF;

    }

  }

  echo '</div>';

  $last_group = $pos->group;
  
  } // foreach position

//  echo '</div>';
  db_set_active('default');
