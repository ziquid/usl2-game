<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  if (empty($game_user->username))
    drupal_goto($game . '/choose_name/' . $arg2);

// save the message, if any

    $message = check_plain($_GET['message']);
firep($message);

  if (strlen($message) > 0 and strlen($message) < 3) {
    echo '<div class="message-error">Your message must be at least 3
      characters long.</div>';
    $message = '';
  }

  $sql = 'select fkey_clans_id, is_clan_leader from clan_members
    where fkey_users_id = %d;';
  $result = db_query($sql, $game_user->id);
  $item = db_fetch_object($result);

  if ($item->fkey_clans_id != $clan_id)
    drupal_goto($game . '/home/' . $arg2);

  $is_clan_leader = (($item->is_clan_leader == 1) || ($phone_id == 'abc123'));

  if (!empty($message)) {

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_neighborhoods_id, message, is_announcement)
      values (%d, %d, "%s", 1);';
    $result = db_query($sql, $game_user->id, $clan_id, $message);

  }

  echo <<< EOF
<div class="news">
  <a href="/$game/clan_list/$arg2/$clan_id" class="button">Clan List</a>
  <a href="/$game/clan_msg/$arg2/$clan_id" class="button">Clan Messages</a>
  <a href="/$game/clan_announcements/$arg2/$clan_id"
    class="button active">Announcements</a>
</div>
<div class="title">Clan Announcements</div>
<div class="message-title">Send a clan announcement</div>
<div class="send-message">
  <form method=get action="/$game/clan_announcements/$arg2/$clan_id">
    <textarea class="message-textarea" name="message" rows="2"></textarea>
    <br/>
    <div class="send-message-send-wrapper">
      <input class="send-message-send" type="submit" value="Send"/>
    </div>
  </form>
</div>
EOF;

  echo <<< EOF
<div class="news">
  <div class="messages-title">
    Messages
  </div>
EOF;

  $sql = 'select clan_messages.*, users.username, users.phone_id,
    elected_positions.name as ep_name,
    clan_members.is_clan_leader,
    clans.acronym as clan_acronym, clans.name as clan_name,
    clans.rules as clan_rules

    from clan_messages

    left join users on clan_messages.fkey_users_from_id = users.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      clan_messages.fkey_users_from_id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    where clan_messages.fkey_neighborhoods_id = %d
      AND clan_messages.is_announcement = 1
    order by id DESC
    LIMIT 50;';

  $result = db_query($sql, $clan_id);
  $msg_shown = FALSE;

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  foreach ($data as $item) {
firep($item->id);
    $display_time = _stlouis_format_date(strtotime($item->timestamp));
    $clan_acronym = '';

  if (!empty($item->clan_acronym))
    $clan_acronym = "($item->clan_acronym)";

  if ($item->is_clan_leader)
    $clan_acronym .= '*';

    echo <<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
EOF;

    if ($is_clan_leader) { // allow clan leader to delete announcements

      echo <<< EOF
<div class="message-delete">
  <a href="/$game/clan_msg_delete/$arg2/$item->id">
    <img src="/sites/default/files/images/delete.png" width="16" height="16"/>
  </a>
</div>
EOF;

    }

    echo <<< EOF
  <p>$item->message</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$arg2/$item->phone_id">View / Respond</a>
  </div></div>
</div>
EOF;
    $msg_shown = TRUE;

  }

  db_set_active('default');
