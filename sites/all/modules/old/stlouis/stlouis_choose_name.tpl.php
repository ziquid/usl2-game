<?php

  global $game, $phone_id;
  
  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $arg2 = check_plain(arg(2));

  $username = trim(check_plain($_GET['username']));
  
  if (strlen($username) > 0 and strlen($username) < 3) {
    $error_msg .= '<div class="username-error">Your name must be at least 3
      characters long.</div>';
    $username = '';
  }

  $isdupusername = FALSE;

  if ($username != "") { // check for duplicate usernames
    $sql = 'SELECT * FROM users WHERE username = "%s"';
    $result = db_query($sql, $username);
    $isdupusername = ($result->num_rows > 0);
firep('$isdupusername = ' . $isdupusername);
  }

// if they have chosen a username and it's not a dupe
  if ($username != '' && !$isdupusername) {    
    $sql = 'update users set username = "%s" where id = %d;';
    $result = db_query($sql, $username, $game_user->id);
      
    if (empty($game_user->username)) { // first timer

      drupal_goto($game . '/land_buy/' . $arg2 . '/1/1');

    } else { // changing existing name

      if ($game_user->username != $username) {
// only do this if they chose something new

        $message = "I've changed my name from <em>$game_user->username</em> to
          <em>$username</em>.&nbsp; Please call me <em>$username</em> from now
          on.";
        $sql = 'insert into user_messages (fkey_users_from_id,
          fkey_users_to_id, message) values (%d, %d, "%s");';
        $result = db_query($sql, $game_user->id, $game_user->id, $message);

        $sql = 'update users set luck = luck - 10 where id = %d;';
        $result = db_query($sql, $game_user->id);

      } // did they choose a new name?

      drupal_goto($game . '/user/' . $arg2);

    } // first timer?
    
  } else { // haven't chosen a username on this screen, or chose a duplicate

    if ($isdupusername) { // set an error message if a dup

      $msgUserDuplicate =<<< EOF
<div class="message-error big">Sorry!</div>
  <p>The username <em>$username</em> already exists.</p>
  <p class="second">Please choose a different name and try again.</p>
EOF;

    } else {
      $msgUserDuplicate = '<p>&nbsp;</p>';
    } // set an error message is a dup

  if (empty($game_user->username)) {

    $person_class = 'restaurant_owner';
    $intro = t('The store owner asks you:');
    $quote = t("By the way, what's your name?");

  } else {

    $fetch_header($game_user); // allow them to navigate out of this
    $person_class = 'holodad';
    $intro = t('Holo-dad asks you:');
    $quote = t('Hello, %username!&nbsp; What would you like your new name to be?',
      array('%username' => $game_user->username));

    if ($game_user->luck < 10) {

      echo <<< EOF
<div class="land-failed">Not enough $luck!</div>
<div class="subtitle">
  <a href="/$game/elders/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>
EOF;

      db_set_active('default');

    } // out of luck

  }

    echo <<< EOF
$msgUserDuplicate
<div class="welcome">
  <div class="$person_class"></div>
  $error_msg
  <p>$intro</p>
  <p class="quote">$quote</p>
  <div class="ask-name">
    <form method=get action="/$game/choose_name/$arg2">
      <input type="text" name="username" width="20" maxlength="20"/>
      <input type="submit" value="Submit"/>
    </form>
  </div>
</div>
EOF;

  }

  db_set_active('default');
