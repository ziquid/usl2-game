<?php

//  set_time_limit(10); // this page must not bog down server

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $arg2 = check_plain(arg(2));
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');

  $message = check_plain($_GET['message']);

  if ($game_user->level < 6) {

    echo <<< EOF
<p>&nbsp;</p>
<div class="title">
  <img src="/sites/default/files/images/${game}_title.png?1" width=300/>
</div>
<div class="welcome">
  <div class="holodad"></div>
  <p class="quote">You're not yet experienced enough for the home page.&nbsp;
  Come back at level 6.</p>
</div>
EOF;

  _button('quests');

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"home not-yet\"/>\n-->";

  db_set_active('default');
  return;

  }

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"home\"/>\n-->";

  $today = date('Y-m-d');

  if ($game_user->last_bonus_date != $today) {

    $sql = 'select residents from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result);

    $money = ($game_user->level * $item->residents) + $game_user->income -
      $game_user->expenses;
    $extra_bonus = '';
/*
    if ($game == 'stlouis') {

      $sql = 'select quantity from staff_ownership
        where fkey_staff_id = 18 and fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);
      $item = db_fetch_object($result);

      if ($item->quantity >= 1) {
        $money *= 3;
        $extra_text .= '<div class="level-up-text">
          ~ Your private banker tripled your bonus ~
        </div>';
      }

    }
*/
firep("adding $money money because last_bonus_date = $last_bonus_date");

    $sql = 'update users set money = money + %d, last_bonus_date = "%s"
      where id = %d;';
    $result = db_query($sql, $money, $today, $game_user->id);
    $game_user = $fetch_user();

    $extra_bonus = '<div class="level-up">
        <div class="title">// Daily Bonus \\\\</div>
        <div class="level-up-text">Bonus amount: <span>' .
          $money . ' ' . $game_user->values . '</span></div>' .
          $extra_text .
        '<div class="level-up-text">Come back tomorrow for another bonus</div>
      </div>';

  }

  $fetch_header($game_user);
  _show_goal($game_user);

  if (empty($game_user->referral_code)) {

    $good_code = FALSE;
    $count = 0;

    while (!$good_code && $count++ < 10) {

      $referral_code = '0000' .
        base_convert(mt_rand(0, pow(36, 5) - 1) . '', 10, 36);
      $referral_code = strtoupper(substr($referral_code,
        strlen($referral_code) - 5, 5));
firep($referral_code);

      $sql = 'select referral_code from users where referral_code = "%s";';
      $result = db_query($sql, $referral_code);
      $item = db_fetch_object($result);

      if (empty($item->referral_code)) { // code not already in use - use it!

        $good_code = TRUE;
        $sql = 'update users set referral_code = "%s" where id = %d;';
        $result = db_query($sql, $referral_code, $game_user->id);
        $game_user->referral_code = $referral_code;

      }

    }

  }

  if (substr(arg(2), 0, 4) == 'nkc ') {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 6') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 5') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.4') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.3') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.2') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.1') !== FALSE) {

    $coefficient = 1;

  } else if ((stripos($_SERVER['HTTP_USER_AGENT'], 'BNTV') !== FALSE) &&
    (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4') !== FALSE)) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=800') !== FALSE) {

    $coefficient = 2.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=600') !== FALSE) {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=533') !== FALSE) {

    $coefficient = 1.66;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=480') !== FALSE) {

    $coefficient = 1.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=400') !== FALSE) {

    $coefficient = 1.25;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=384') !== FALSE) {

    $coefficient = 1.2;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=360') !== FALSE) {

    $coefficient = 1.125;

  } else {

    $coefficient = 1;

  }

/*
if (($today == '2012-12-26') || $game_user->username == 'abc123')
  $extra_menu = '-boxing';
*/

  $event_text = '';

  switch($event_type) {

    case EVENT_DONE:

      $event_text = '<div class="event">
        The event is over!&nbsp; We hope you had fun.
        </div><div class="event-tagline small">
          <a href="/' . $game . '/top_event_points/' . $arg2 .
            '">Leaderboard</a>
        </div>';

      break;

    case EVENT_DEBATE:

      $event_text = '<div class="event">
          While we are waiting on ToxiCorp to be ready,
          let\'s have a debate mini event.&nbsp; Debate for prizes today!
        </div><div class="event-tagline small">
          <a href="/' . $game . '/top_event_points/' . $arg2 .
            '">Leaderboard</a>
        </div>';

      break;

  }

// dead presidents event
if (FALSE)
  if ($game == 'stlouis') $event_text = '<!--<a href="/' . $game .
  '/top_event_points/' . $arg2 . '">-->
  <div class="event">
    <img src="/sites/default/files/images/toxicorp_takeover.png" border=0
    width="160">
  </div>
  <div class="event-text">
      New&nbsp;Event <!--Starts&nbsp;Feb&nbsp;28th-->DELAYED
  </div>
  <div class="event-tagline small">
    Turning St. Louis into an industrial wasteland
  </div>
  <div class="event-tagline">
    &mdash; one &mdash; hood &mdash; at &mdash; a &mdash; time &mdash;
  </div>
  </div>
  <!--</a>-->';

  echo <<< EOF
$extra_bonus
<div class="title">
<img src="/sites/default/files/images/${game}_title.png" width=300/>
</div>
<div class="new-main-menu">
  <img src="/sites/default/files/images/${game}_home_menu{$extra_menu}.jpg?1"
  usemap="#new_main_menu" width=312/>
  <map name="new_main_menu">
EOF;

   $coords = _stlouis_scale_coords($coefficient, 107, 34, 210, 63);

   echo <<< EOF
    <area shape="rect" coords="$coords" alt="Objectives" href="/$game/quests/$arg2" />
EOF;

   $coords = _stlouis_scale_coords($coefficient, 42, 72, 122, 92);

  echo <<< EOF
    <area shape="rect" coords="$coords" alt="Favors" href="/$game/favors/$arg2" />
EOF;

    $coords = _stlouis_scale_coords($coefficient, 32, 93, 127, 115);

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Elections" href="/$game/hierarchies/$arg2" />
EOF;

  $coords = _stlouis_scale_coords($coefficient, 197, 72, 257, 92);
  $coords2 = _stlouis_scale_coords($coefficient, 187, 93, 267, 115);

  echo <<< EOF
    <area shape="rect" coords="$coords" alt="Items" href="/$game/land/$arg2" />
    <area shape="rect" coords="$coords2" alt="Actions" href="/$game/actions/$arg2" />
EOF;


// Move
    $coords = _stlouis_scale_coords($coefficient, 131, 127, 183, 147);

  echo <<< EOF
    <area shape="rect" coords="$coords" alt="Move" href="/$game/move/$arg2/0" />
EOF;


// Elders, Profile
  $coords = _stlouis_scale_coords($coefficient, 126, 155, 192, 180);
  $coords2 = _stlouis_scale_coords($coefficient, 45, 192, 100, 210);

  echo <<< EOF
    <area shape="rect" coords="$coords" alt="Elders" href="/$game/elders/$arg2" />
    <area shape="rect" coords="$coords2" alt="Profile" href="/$game/user/$arg2" />
EOF;

  $coords = _stlouis_scale_coords($coefficient, 113, 192, 151, 210);

  if ($game_user->fkey_clans_id > 0) {

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Clan"
      href="/$game/clan_list/$arg2/{$game_user->fkey_clans_id}" />
EOF;

  } else {

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Clan"
      href="/$game/clan_list_available/$arg2" />
EOF;

  } // in a clan?

    $coords = _stlouis_scale_coords($coefficient, 162, 192, 200, 210);

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Help" href="/$game/help/$arg2" />
EOF;

    $coords = _stlouis_scale_coords($coefficient, 214, 192, 265, 210);

    if ($game == 'stlouis') {

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Forum"
      href="http://forum.cheek.com/forum/2" />
EOF;

    } else {

    echo <<< EOF
    <area shape="rect" coords="$coords" alt="Forum"
      href="http://forum.cheek.com/forum/12" />
EOF;

    }

    echo <<< EOF
  </map>
</div>
$event_text
<div class="news">
  <div class="title">
    News
  </div>
  <a class="button active" href="#" id="all-button">All</a>
  <a class="button" href="#" id="personal-button">Personal</a>
  <a class="button" href="#" id="election-button">{$election_tab}</a>
  <a class="button" href="#" id="clan-button">$party_small</a>
  <a class="button" href="#" id="system-button">$system</a>
  <div id="all-text">
EOF;

  if (substr($phone_id, 0, 3) == 'ai-') { // no reason to spend cycles on msgs
    db_set_active('default');
    return;
  }


// are we a type 2 elected official?
  $sql = 'SELECT type FROM elected_officials
    left join elected_positions on elected_positions.id = fkey_elected_positions_id
    WHERE fkey_users_id = %d;';
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result);

  $elected_official_type = $item->type;

  if ($elected_official_type == 2) { // if a party official

    $data = array();
    $sql = 'SELECT fkey_clans_id FROM clan_members
      left join users on fkey_users_id = users.id
      WHERE fkey_values_id = %d
      and is_clan_leader = 1;';
    $result = db_query($sql, $game_user->fkey_values_id);
    while ($item = db_fetch_object($result)) $data[] = $item->fkey_clans_id;
    // we need to do this separately to keep the db from locking
    // wish mysql had a select with nolock feature - jwc

    $clan_sql = 'where clan_messages.fkey_neighborhoods_id in (%s)';
    $clan_id_to_use = implode(',', $data);
//firep($clan_id_to_use);
    $limit = 50;

  } else {

    $clan_sql = 'where clan_messages.fkey_neighborhoods_id = %d';
    $clan_id_to_use = $game_user->fkey_clans_id;
    $limit = 20;

  }

  $sql = '
    (
    select user_messages.timestamp, user_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    user_messages.private,
    "user" as type
    from user_messages
    left join users on user_messages.fkey_users_from_id = users.id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
    user_messages.fkey_users_from_id
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    where fkey_users_to_id = %d
    order by timestamp DESC limit %d
    )

    union

    (
    select challenge_messages.timestamp, challenge_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    0 AS private,
    "challenge" as type
    from challenge_messages
    left join users on challenge_messages.fkey_users_from_id = users.id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
    challenge_messages.fkey_users_from_id
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    where fkey_users_to_id = %d
    order by timestamp DESC limit %d
    )

    union

    (
    select party_messages.timestamp, party_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    0 AS private,
    "party" as type
    from party_messages
    left join users on party_messages.fkey_neighborhoods_id =
      users.fkey_neighborhoods_id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
    party_messages.fkey_users_from_id
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    where elected_officials.fkey_users_id = fkey_users_from_id
    and party_messages.fkey_neighborhoods_id = %d
    order by timestamp DESC limit %d
    )

    union

    (
    select clan_messages.timestamp, clan_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    0 AS private,
    "clan" as type
    from clan_messages
    left join users on clan_messages.fkey_users_from_id = users.id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      clan_messages.fkey_users_from_id
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    ' . $clan_sql . '
    order by timestamp DESC limit %d
    )

    union

    (
    select values_messages.timestamp, values_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    0 AS private,
    "values" as type
    from values_messages
    left join users on values_messages.fkey_users_from_id = users.id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      values_messages.fkey_users_from_id
    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    where values_messages.fkey_values_id = %d
--    AND values_messages.fkey_neighborhoods_id = %d
    order by timestamp DESC limit %d
    )

    union

    (
    select system_messages.timestamp, system_messages.message,
    NULL AS username, NULL as phone_id,
    NULL AS ep_name,
    0 AS is_clan_leader,
    NULL AS clan_acronym,
    0 AS private,
    "system" as type
    from system_messages
    left join users on system_messages.fkey_users_from_id = users.id
    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id
    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id
--    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
--    system_messages.fkey_users_from_id
--    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id
    order by timestamp DESC limit %d
    )

    order by timestamp DESC limit %d;';
//firep($sql);

// don't show if load avg too high

//  $load_avg = sys_getloadavg(); // FIXME: get load avg of db server
  $data = array();

  if (TRUE/*$load_avg[0] <= 2.0*/) {
// expensive query - goes to slave
//   db_set_active('game_' . $game . '_slave1');
    $result = db_query($sql, $game_user->id, $limit,
      $game_user->id, 3, // challenge limit of 3
      $game_user->fkey_neighborhoods_id, $limit,
      $clan_id_to_use, $limit,
      $game_user->fkey_values_id, $game_user->fkey_neighborhoods_id, $limit,
      $limit,
      $limit);
    while ($item = db_fetch_object($result)) $data[] = $item;
    db_set_active('game_' . $game); // reset to master
  }

  $msg_shown = FALSE;

  foreach ($data as $item) {
// firep($item);

    $display_time = _stlouis_format_date(strtotime($item->timestamp));
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    if ($item->private) {
      $private_css = 'private';
    } else {
      $private_css = '';
    }

    $private_css .= ' ' . $item->type;

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    if (empty($item->username)) {

      $username = '';
      $reply = '';

    } else {

      $username = 'from ' . $item->ep_name . ' ' . $item->username . ' ' .
        $clan_acronym;
      $reply = '<div class="message-reply-wrapper"><div class="message-reply">
        <a href="/' . $game . '/user/' . $arg2 . '/' . $item->phone_id .
        '">View / Respond</a></div></div>';
    }

    echo <<< EOF
<div class="dateline">
  $display_time $username
</div>
<div class="message-body $private_css">
  <p>$item->message</p>$reply
</div>
EOF;
    $msg_shown = TRUE;

  }

  echo <<< EOF
  </div>
  <div id="personal-text">
EOF;

// PERSONAL messages

  $sql = 'select user_messages.*, users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym

    from user_messages

    left join users on user_messages.fkey_users_from_id = users.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      user_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    where fkey_users_to_id = %d
    order by timestamp DESC limit 20;';
  $result = db_query($sql, $game_user->id);
  $msg_shown = FALSE;

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    $display_time = _stlouis_format_date(strtotime($item->timestamp));
    $clan_acronym = '';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    if ($item->private) {
      $private_css = 'private';
    } else {
      $private_css = '';
    }

    echo <<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body user $private_css">
  <p>$item->message</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$arg2/$item->phone_id">View / Respond</a>
  </div></div>
</div>
EOF;
    $msg_shown = TRUE;

  }

  if (!$msg_shown) echo '<div class="dateline">Now</div>' .
    '<p>No Personal messages yet.</p>';

// ELECTION messages

  echo <<< EOF
  </div>
  <div id="election-text">
EOF;

  $sql = 'select challenge_messages.*, users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym

    from challenge_messages

    left join users on challenge_messages.fkey_users_from_id = users.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    where fkey_users_to_id = %d
    order by timestamp DESC limit 20;';
  $result = db_query($sql, $game_user->id);
  $msg_shown = FALSE;

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    $display_time = _stlouis_format_date(strtotime($item->timestamp));
    $clan_acronym = '';

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    echo <<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body challenge">
  <p>$item->message</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$arg2/$item->phone_id">View / Respond</a>
  </div></div>
</div>
EOF;
    $msg_shown = TRUE;

  }

  if (!$msg_shown) echo '<div class="dateline">Now</div>' .
    '<p>No ' . $election . ' messages yet.</p>';

// CLAN and PARTY messages

  echo <<< EOF
  </div>
  <div id="clan-text">
EOF;

  if ($game_user->can_broadcast_to_party || $game_user->fkey_clans_id ||
    $game == 'celestial_glory') {

    echo <<< EOF
<div class="message-title">Send a message to your $party_lower or clan</div>
EOF;

    echo <<< EOF
<div class="send-message">
  <form method=get action="/$game/party_msg/$arg2">
    <textarea class="message-textarea" name="message" rows="2">$message</textarea>
    <br/>
    <div class="send-message-target">
      <select name="target">
EOF;

    if (($game_user->can_broadcast_to_party) ||
      ($game_user->fkey_neighborhoods_id == 75))
      echo ('<option value="neighborhood">' . $hood . '</option>');

    if ($game_user->fkey_clans_id)
      echo ('<option value="clan">Clan</option>');

// TESTING -- users can party chat but it costs 1 Action -- jwc 11Jan2014
//    if ($elected_official_type == 2 || $game == 'celestial_glory')
// if a party official
      echo '<option value="values">' . $party . ' (1 Action)</option>';

      echo <<< EOF
      </select>
    </div>
    <div class="send-message-send-wrapper">
      <input class="send-message-send" type="submit" value="Send"/></div>
  </form>
</div>
EOF;

  } // send a msg to your party

  $sql = '
    (
    select party_messages.timestamp, party_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    "party" as type

    from party_messages

    left join users on party_messages.fkey_neighborhoods_id =
      users.fkey_neighborhoods_id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      party_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    where elected_officials.fkey_users_id = fkey_users_from_id
      and party_messages.fkey_neighborhoods_id = %d
    order by timestamp DESC limit %d
    )

    union

    (
    select clan_messages.timestamp, clan_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    "clan" as type

    from clan_messages

    left join users on clan_messages.fkey_users_from_id = users.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      clan_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    ' . $clan_sql . '
    order by timestamp DESC limit %d
    )

    union

    (
    select values_messages.timestamp, values_messages.message,
    users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym,
    "values" as type

    from values_messages

    left join users on values_messages.fkey_users_from_id = users.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      values_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    where values_messages.fkey_values_id = %d
--    AND values_messages.fkey_neighborhoods_id = %d
    order by timestamp DESC limit %d
    )

    order by timestamp DESC limit %d;';

  if (TRUE) {
    $result = db_query($sql, $game_user->fkey_neighborhoods_id, $limit,
      $clan_id_to_use, $limit,
      $game_user->fkey_values_id, $game_user->fkey_neighborhoods_id, $limit,
      $limit);
  }
// clan messages use fkey_neigh_id as clans_id
  $msg_shown = FALSE;

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    $display_time = _stlouis_format_date(strtotime($item->timestamp));
    $clan_acronym = '';

    if (empty($item->ep_name)) $item->ep_name = 'Subjugate';

    if (!empty($item->clan_acronym))
      $clan_acronym = "($item->clan_acronym)";

    if ($item->is_clan_leader)
      $clan_acronym .= '*';

    $private_css = ' ' . $item->type;

    echo <<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body $private_css">
  <p>$item->message</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$arg2/$item->phone_id">View / Respond</a>
  </div></div>
</div>
EOF;
    $msg_shown = TRUE;

  }

  if (!$msg_shown) echo '<div class="dateline">Now</div>' .
    '<p>No ' . $party . ' messages yet.</p>';

// SYSTEM messages

  echo <<< EOF
  </div>
  <div id="system-text">
EOF;

    $sql = 'select system_messages.*, users.username, users.phone_id
    from system_messages
    left join users on system_messages.fkey_users_from_id = users.id

    order by timestamp DESC limit 20;';
  $result = db_query($sql, $game_user->id);
  $msg_shown = FALSE;

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {

    $display_time = _stlouis_format_date(strtotime($item->timestamp));

    echo <<< EOF
<div class="dateline">
  $display_time
</div>
<div class="message-body system">
  <p>$item->message</p>
</div>
EOF;
    $msg_shown = TRUE;

  }

  echo <<< EOF
  </div>
</div>

<script type="text/javascript">

window.onload = function() {

  document.getElementById('personal-text').style.display = 'none';
  document.getElementById('election-text').style.display = 'none';
  document.getElementById('clan-text').style.display = 'none';
  document.getElementById('system-text').style.display = 'none';

  document.getElementById('all-button').onclick = function() {
    document.getElementById('all-text').style.display = 'block';
    document.getElementById('personal-text').style.display = 'none';
    document.getElementById('election-text').style.display = 'none';
    document.getElementById('clan-text').style.display = 'none';
    document.getElementById('system-text').style.display = 'none';
    document.getElementById('all-button').className = 'button active';
    document.getElementById('personal-button').className = 'button';
    document.getElementById('election-button').className = 'button';
    document.getElementById('clan-button').className = 'button';
    document.getElementById('system-button').className = 'button';
    return false;
  };

  document.getElementById('personal-button').onclick = function() {
    document.getElementById('all-text').style.display = 'none';
    document.getElementById('personal-text').style.display = 'block';
    document.getElementById('election-text').style.display = 'none';
    document.getElementById('clan-text').style.display = 'none';
    document.getElementById('system-text').style.display = 'none';
    document.getElementById('all-button').className = 'button';
    document.getElementById('personal-button').className = 'button active';
    document.getElementById('election-button').className = 'button';
    document.getElementById('clan-button').className = 'button';
    document.getElementById('system-button').className = 'button';
    return false;
  };

  document.getElementById('election-button').onclick = function() {
    document.getElementById('all-text').style.display = 'none';
    document.getElementById('personal-text').style.display = 'none';
    document.getElementById('election-text').style.display = 'block';
    document.getElementById('clan-text').style.display = 'none';
    document.getElementById('system-text').style.display = 'none';
    document.getElementById('all-button').className = 'button';
    document.getElementById('personal-button').className = 'button';
    document.getElementById('election-button').className = 'button active';
    document.getElementById('clan-button').className = 'button';
    document.getElementById('system-button').className = 'button';
    return false;
  };

  document.getElementById('clan-button').onclick = function() {
    document.getElementById('all-text').style.display = 'none';
    document.getElementById('personal-text').style.display = 'none';
    document.getElementById('election-text').style.display = 'none';
    document.getElementById('clan-text').style.display = 'block';
    document.getElementById('system-text').style.display = 'none';
    document.getElementById('all-button').className = 'button';
    document.getElementById('personal-button').className = 'button';
    document.getElementById('election-button').className = 'button';
    document.getElementById('clan-button').className = 'button active';
    document.getElementById('system-button').className = 'button';
    return false;
  };

  document.getElementById('system-button').onclick = function() {
    document.getElementById('all-text').style.display = 'none';
    document.getElementById('personal-text').style.display = 'none';
    document.getElementById('election-text').style.display = 'none';
    document.getElementById('clan-text').style.display = 'none';
    document.getElementById('system-text').style.display = 'block';
    document.getElementById('all-button').className = 'button';
    document.getElementById('personal-button').className = 'button';
    document.getElementById('election-button').className = 'button';
    document.getElementById('clan-button').className = 'button';
    document.getElementById('system-button').className = 'button active';
    return false;
  };

EOF;

  if (!empty($message)) { // message?  show the clan tab already

  echo <<< EOF
    document.getElementById('all-text').style.display = 'none';
    document.getElementById('personal-text').style.display = 'none';
    document.getElementById('election-text').style.display = 'none';
    document.getElementById('clan-text').style.display = 'block';
    document.getElementById('system-text').style.display = 'none';
    document.getElementById('all-button').className = 'button';
    document.getElementById('personal-button').className = 'button';
    document.getElementById('election-button').className = 'button';
    document.getElementById('clan-button').className = 'button active';
    document.getElementById('system-button').className = 'button';

EOF;

  }

  echo <<< EOF
  }

</script>
EOF;

  db_set_active('default');
