<?php

// includes for special functions

  function _stlouis_action_start_your_own_clan_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $can_do_again = FALSE;

    $clan_name = trim(check_plain($_GET['name']));
    $clan_acronym = trim(check_plain($_GET['acronym']));

    $sql = 'select * from clan_members where fkey_users_id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if (!empty($item)) { // already a member of a clan

      $outcome_reason = '<div class="land-failed">' .
        t('Already a member of a clan!') .
        '</div><div class="action-effect">You already belong to a clan.&nbsp;
        You must secede from it first.</div>';

      return false;

    }


// do they have the appropriate aides?

    $sql = 'select sum(clan_size * quantity) as max_clan_size from users
      left join equipment_ownership
      on equipment_ownership.fkey_users_id = users.id
      left join equipment
      on equipment_ownership.fkey_equipment_id = equipment.id
      where users.id = %d;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);
    $max_clan_size = $item->max_clan_size;

    if ($max_clan_size <= 0) { // no aides to create a clan

      $outcome_reason = '<div class="land-failed">' .
        t('No place to hold meetings!') .
        '</div><div class="action-effect">You need a place to hold
        your clan meetings.</div>';

      return FALSE;

    }


    if (empty($clan_name) XOR empty($clan_acronym)) { // didn't fill both out

      $form_reminder = '<div class="land-failed">' .
        t('Both fields are required') .
        '</div><div class="action-effect">Please give both a name and three
          letters for your clan.</div>';

    } // didn't fill out both fields

    if (empty($clan_name) || empty($clan_acronym)) { // ask for name, acronym

      $outcome_reason = $form_reminder;
      $outcome_reason .=<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$phone_id/$action_id">
    <p>What will you name your clan?</p>
    <input type="text" name="name" size="32" maxlength="32"/>
    <p>Which three letters should appear<br/>after members' names?</p>
    <input type="text" name="acronym" size="3" maxlength="3"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return false;

    } // no name nor acronym

// see if the clan already exists

    $sql = 'select * from clans where acronym = "%s" or name = "%s" limit 1;';
    $result = db_query($sql, $clan_acronym, $clan_name);
    $item = db_fetch_object($result);
firep($item);

    if (!empty($item)) { // clan does exist

      $outcome_reason = '<div class="land-failed">' .
        t('You have chosen an existing clan name or set of three letters') .
        '</div><div class="action-effect">Please check that you enter a unique
        clan name and three letters and try again</div>
        <div class="try-an-election-wrapper"><div
        class="try-an-election"><a
        href="/' . $game . '/actions_do/' . $phone_id . '/' . $action_id .
        '">Try again</a></div></div>';

      return false;

    }


// doesn't exist - create it!

    $sql = 'insert into clans (name, acronym) values ("%s", "%s");';
    $result = db_query($sql, $clan_name, $clan_acronym);

    $sql = 'insert into clan_members (fkey_clans_id, fkey_users_id,
      is_clan_leader) values ((select id from clans where name = "%s"),
      %d, 1);';
    $result = db_query($sql, $clan_name, $game_user->id);

    $outcome_reason .= '<div class="action-effect">The clan ' .
      $clan_name . ' (' . $clan_acronym . ') has been created!';

    return true;

  }


  function _celestial_glory_action_start_your_own_clan_function(&$outcome_reason, $target) {

    return _stlouis_action_start_your_own_clan_function($outcome_reason, $target);

  }


  function _stlouis_action_join_a_clan_function(&$outcome_reason, $target,
    &$can_do_again) {


    global $game, $phone_id;

    $get_value = '_' . $game . '_get_value';
    $set_value = '_' . $game . '_set_value';
    $remove_value = '_' . $game . '_remove_value';
    $fetch_user = '_' . $game . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
    $can_do_again = FALSE;

    $clan_acronym = check_plain($_GET['acronym']);

    $sql = 'select * from clan_members where fkey_users_id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if (!empty($item)) { // already a member of a clan

      $outcome_reason = '<div class="land-failed">' .
        t('Already a member of a clan!') .
        '</div><div class="action-effect">You already belong to a clan.&nbsp;
        You must leave it first.</div>';

      return FALSE;

    }

    if (empty($clan_acronym)) { // ask for acronym

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Which clan would you like to join (three letters)?</p>
    <input type="text" name="acronym" size="3" maxlength="3"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // no acronym

// does clan exist?

    $sql = 'select * from clans where acronym = "%s";';
// limited to 1 in db
    $result = db_query($sql, $clan_acronym);
    $item = db_fetch_object($result);
firep($item);

    if (empty($item)) { // clan does not exist

      $outcome_reason = '<div class="land-failed">' .
        t('The %clan clan does not exist', array('%clan' => $clan_acronym)) .
        '</div><div class="action-effect">Please check that you entered the
        clan\'s three letters correctly and try again</div>
        <div class="try-an-election-wrapper"><div
        class="try-an-election"><a
        href="/' . $game . '/actions_do/' . $phone_id . '/' . $action_id .
        '">Try again</a></div></div>';

      return FALSE;

    }

    $clan_name = $item->name;
    $clan_id = $item->id;
    $clan_password = strtolower($item->password);

    $sql = 'select users.fkey_values_id, users.id from clan_members
      left join clans on clan_members.fkey_clans_id = clans.id
      left join users on clan_members.fkey_users_id = users.id
      where clans.id = %d and clan_members.is_clan_leader = 1;';
    $result = db_query($sql, $clan_id);
    $item = db_fetch_object($result);
firep($item);
    $clan_leader_id = $item->id;

    if ($item->fkey_values_id != $game_user->fkey_values_id) {
// different political party

      $outcome_reason = '<div class="land-failed">' .
        t('Different political party') .
        '</div><div class="action-effect">' .
        t('That clan belongs to a different political party') . '</div>';

      return FALSE;

    }

    $sql = 'select sum(clan_size * quantity) as max_clan_size from users
      left join equipment_ownership
      on equipment_ownership.fkey_users_id = users.id
      left join equipment
      on equipment_ownership.fkey_equipment_id = equipment.id
      where users.id = %d;';
    $result = db_query($sql, $clan_leader_id);
    $item = db_fetch_object($result);
firep($item);
    $max_clan_size = $item->max_clan_size;

    $sql = 'select count(id) as current_clan_size from clan_members
      where fkey_clans_id = %d;';
    $result = db_query($sql, $clan_id);
    $item = db_fetch_object($result);
firep($item);
    $current_clan_size = $item->current_clan_size;

    if ($current_clan_size >= $max_clan_size) { // clan full!

      $outcome_reason = '<div class="land-failed">' .
        t('The clan is full!') .
        '</div><div class="action-effect">' .
        t('There is no room for new members in this clan.  ' .
          'This clan can only hold @num members.',
          array('@num' => $max_clan_size)) . '</div>';

      return false;

    }


    $password = strtolower(trim(check_plain($_GET['password'])));

// need a password

    if (!empty($clan_password) && empty($password)) {

      $outcome_reason = '<div class="land-failed">' .
        t('Enter the password') .
        '</div><div class="action-effect">' .
        t('This clan has a password.  What is it?') . '</div>';

      $outcome_reason .=<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <input type="hidden" name="acronym" value="$clan_acronym"/>
    <input type="text" name="password" size="6" maxlength="6"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    }


// wrong password

    if (!empty($clan_password) && ($clan_password != $password)) {

      $outcome_reason = '<div class="land-failed">' .
        t('Wrong password!') .
        '</div><div class="action-effect">' .
        t('The password you entered is incorrect.') . '</div>';

      return FALSE;

    }


// prohibited from joining for a time?

    $join_allowed_time = $get_value($game_user->id, 'cant_join_clan', 0);
firep($join_allowed_time);
firep(time());

    if (time() < $join_allowed_time) { // can't join yet!

      $min = floor(($join_allowed_time - time()) / 60);
      $sec = ($join_allowed_time - time()) % 60;

      $outcome_reason = '<div class="land-failed">' .
        t('Too soon!') .
        '</div><div class="action-effect">' .
        t('You can join a clan in @min minutes and @sec seconds',
          array('@min' => $min, '@sec' => $sec))
        . '</div>';

mail('joseph@cheek.com', $target_name . ' tried to join a clan',
  "but isn't allowed to for $min:$sec longer.");

      return FALSE;

    }

// join succeeded!

    $remove_value($target_id, 'cant_join_clan');
    $sql = 'insert into clan_members (fkey_clans_id, fkey_users_id,
      is_clan_leader) values ((select id from clans where acronym = "%s"),
      %d, 0);';
    $result = db_query($sql, $clan_acronym, $game_user->id);

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_neighborhoods_id, message) values
      (%d, (select id from clans where acronym = "%s"), "%s");';
    $message = $game_user->username . ' has joined your clan.  Please welcome
      him or her.';
    $result = db_query($sql, $game_user->id, $clan_acronym, $message);

    $outcome_reason .= '<div class="action-effect">You have successfully joined
      the ' . $clan_name . ' (' . $clan_acronym . ') clan!';

    $can_do_again = FALSE;
    return TRUE;

  }


  function _celestial_glory_action_join_a_clan_function(&$outcome_reason,
    $target) {

    return _stlouis_action_join_a_clan_function($outcome_reason, $target);

  }


  function _stlouis_action_leave_your_clan_function(&$outcome_reason, $target,
    &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $can_do_again = FALSE;

    if (empty($game_user->fkey_clans_id)) { // not a member of a clan

      $outcome_reason = '<div class="land-failed">' .
        t('Not a member of a clan!') .
        '</div><div class="action-effect">You don\'t belong to a clan.&nbsp;
        You cannot leave it.</div>';

      return false;

    }

    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);

    if ($item->is_clan_leader) { // clan leader? delete entire clan

      $sure = check_plain($_GET['sure']);

      if ($sure != 'yes') {

        $outcome_reason = '<div class="subtitle">Are you sure?</div>
          <div class="subsubtitle">
            This dissolves <strong>the entire clan</strong> and cannot be undone!
          </div>
          <div class="try-an-election-wrapper">
            <div class="try-an-election">
              <a href="/' . $game . '/actions_do/' .
              $phone_id . '/' . $action_id . '?sure=yes">Yes,  I\'m sure</a>
            </div>
            <div class="try-an-election">
              <a href="/' . $game . '/actions/' . $phone_id .
              '">No, I\'m not sure</a>
            </div>
          </div>';

          return false;

      }

      $sql = 'delete from clan_messages where fkey_neighborhoods_id = %d;';
      $result = db_query($sql, $game_user->fkey_clans_id);
      $sql = 'delete from clan_members where fkey_clans_id = %d;';
      $result = db_query($sql, $game_user->fkey_clans_id);
      $sql = 'delete from clans where id = %d;';
      $result = db_query($sql, $game_user->fkey_clans_id);
      $outcome_reason .= '<div class="action-effect">' .
        t('You have dissolved your clan') . '</div>';

    } else {

      $sql = 'delete from clan_members where fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);
      $sql = 'delete from clan_messages where fkey_users_from_id = %d;';
      $result = db_query($sql, $game_user->id);

// tell the clan
      $sql = 'insert into clan_messages (fkey_users_from_id,
        fkey_neighborhoods_id, message) values (%d, %d, "%s");';
      $message = $game_user->username . ' has left your clan.';
      $result = db_query($sql, $game_user->id, $game_user->fkey_clans_id, $message);

      $outcome_reason .= '<div class="action-effect">' .
        t('You have left your clan') . '</div>';

    }

    return TRUE;

  }


  function _celestial_glory_action_leave_your_clan_function(&$outcome_reason,
    $target) {

    return _stlouis_action_leave_your_clan_function($outcome_reason, $target);

  }


  function _stlouis_action_remove_a_clan_member_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $set_value = '_' . $game . '_set_value';
    $fetch_user = '_' . $game . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $can_do_again = FALSE;

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];

    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);

    if ((empty($item)) || ($item->fkey_clans_id != $game_user->fkey_clans_id)) {
// not a member of any or the same clan

      $outcome_reason = '<div class="land-failed">' .
        t('Not a member of your clan!') .
        '</div><div class="action-effect">' . $target_name .
        ' doesn\'t belong to your clan.&nbsp; You cannot remove him or her.</div>';

      return FALSE;

    }

    if ($item->is_clan_leader) { // clan leader? must dissolve clan instead

      $outcome_reason = '<div class="land-failed">' .
        t('Clan leader!') .
        '</div><div class="action-effect">' . $target_name .
        ' is a clan leader and cannot be removed.</div>';

      return FALSE;

    }

    $sql = 'delete from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

    $sql = 'delete from clan_messages where fkey_users_from_id = %d;';
    $result = db_query($sql, $target_id);

    $set_value($target_id, 'cant_join_clan', time() + 7200);

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_neighborhoods_id, message) values (%d, %d, "%s");';
    $message = $target_name . ' has been forcibly removed from your clan.';
    $result = db_query($sql, $game_user->id, $game_user->fkey_clans_id,
      $message);

    $outcome_reason .= '<div class="action-effect">' .
      t('@name has been removed from your clan',
      array('@name' => $target_name)) . '</div>';

mail('joseph@cheek.com', $target_name . ' has been forcibly removed',
  'from the clan in which s/he was a member.');

    return TRUE;

  }


  function _celestial_glory_action_remove_a_clan_member_function(
    &$outcome_reason, $target) {

    return _stlouis_action_remove_a_clan_member_function($outcome_reason,
      $target);

  }


  function _stlouis_action_change_clan_leadership_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];

    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);

    if (empty($item)) { // not a member of a clan

      $outcome_reason = '<div class="land-failed">' .
        t('Not a member of your clan!') .
        '</div><div class="action-effect">' . $target_name .
        ' doesn\'t belong to your clan.&nbsp; You cannot have him or her lead.</div>';

      return false;

    }

    $sql = 'update clan_members set is_clan_leader = 0 where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);

    $sql = 'update clan_members set is_clan_leader = 1 where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_users_to_id, message) values (%d, %d, "%s");';
    $message = $target_name . ' has been named the new leader of the clan.';
    $result = db_query($sql, $game_user->id, $game_user->fkey_clans_id, $message);

    $outcome_reason .= '<div class="action-effect">' .
      t('@name has made the leader of your clan',
      array('@name' => $target_name)) . '</div>';

    return true;

  }


  function _celestial_glory_action_change_clan_leadership_function(&$outcome_reason, $target) {

    return _stlouis_action_change_clan_leadership_function($outcome_reason, $target);

  }


  function _stlouis_action_change_clan_rules_function(&$outcome_reason, $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);

    $clan_rules = check_plain($_GET['rules']);

    $sql = 'select * from clan_members where fkey_users_id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if ($item->is_clan_leader != 1) { // not clan leader

      $outcome_reason = '<div class="land-failed">' .
        t('Not clan leader!') .
        '</div><div class="action-effect">You are not the clan leader.&nbsp;
        You cannot perform this action.</div>';

      return false;

    }

    if (empty($clan_rules)) { // ask for rules

      $sql = 'select rules from clans where id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->fkey_clans_id);
    $item = db_fetch_object($result);

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$phone_id/$action_id">
    <p>What are your clan rules?</p>
    <textarea name="rules" cols="47" rows="12"/>$item->rules</textarea><br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return false;

    } // no new rules

    $sql = 'update clans set rules = "%s"
      where id = %d;';
    $result = db_query($sql, $clan_rules, $game_user->fkey_clans_id);

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_users_to_id, message) values
      (%d, (select id from clans where acronym = "%s"), "%s");';
    $message = 'The clan rules have changed.&nbsp; Please read them at your
      earliest convenience.';
    $result = db_query($sql, $game_user->id, $clan_acronym, $message);

    $outcome_reason .= '<div class="action-effect">The clan rules have
      successfully been changed.';

    return true;

  }


  function _celestial_glory_action_change_clan_rules_function(&$outcome_reason,
    $target) {

      return _stlouis_action_change_clan_rules_function($outcome_reason,
        $target);

  }


  function _stlouis_action_write_on_a_billboard_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);

    $welcome_msg = check_plain($_GET['welcome_msg']);
    $welcome_msg = _stlouis_filter_profanity($welcome_msg);
firep('welcome message = ' . $welcome_msg);

    $sql = 'select * from elected_officials where fkey_users_id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);
/*
    if ($item->fkey_elected_positions_id != 1) { // not Alderman // FIXME hardcoded ep_id

      $outcome_reason = '<div class="land-failed">' .
        t('Not Alderman!') .
        '</div><div class="action-effect">You are not the Alderman.&nbsp;
        You cannot perform this action.</div>';

      return false;

    }
*/
    $error = '';
    if (substr($welcome_msg, 0, 3) == 'XXX') {

       $error = '<div class="message-error">Your message contains words that are not
          allowed.&nbsp; Please rephrase.&nbsp; ' . $welcome_msg . '</div>';
        $welcome_msg = '';

    }

    if ($welcome_msg == '') { // ask for message

      $sql = 'select welcome_msg from neighborhoods where id = %d;';
// limited to 1 in db
      $result = db_query($sql, $game_user->fkey_neighborhoods_id);
      $item = db_fetch_object($result);

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$phone_id/$action_id">
    <p>What should the sign state?</p>
    $error
    <textarea name="welcome_msg" cols="32"
      rows="8"/>$item->welcome_msg</textarea><br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // no new msg

    $sql = 'update neighborhoods set welcome_msg = "%s"
      where id = %d;';
    $result = db_query($sql, $welcome_msg, $game_user->fkey_neighborhoods_id);

    $sql = 'insert into party_messages (fkey_users_from_id,
      fkey_neighborhoods_id, message) values (%d, %d, "%s");';
    $result = db_query($sql, $game_user->id, $game_user->fkey_neighborhoods_id,
      $welcome_msg);

    $outcome_reason .= '<div class="action-effect">The sign has been
      successfully created.';

    return TRUE;

  }


  function _celestial_glory_action_create_a_sign_function(&$outcome_reason,
    $target, &$can_do_again) {

      return _stlouis_action_write_on_a_billboard_function($outcome_reason,
    $target, $can_do_again);

  }


  function _stlouis_action_change_clan_password_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;
    $game_user = _stlouis_fetch_user();
    $action_id = arg(3);

    $clan_password = trim(check_plain($_GET['password']));

    $sql = 'select * from clan_members where fkey_users_id = %d;';
// limited to 1 in db
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if ($item->is_clan_leader != 1) { // not clan leader

      $outcome_reason = '<div class="land-failed">' .
        t('Not clan leader!') .
        '</div><div class="action-effect">You are not the clan leader.&nbsp;
        You cannot perform this action.</div>';

      return false;

    }

    if (empty($clan_password)) { // ask for password

      $sql = 'select password from clans where id = %d;';
// limited to 1 in db
       $result = db_query($sql, $game_user->fkey_clans_id);
      $item = db_fetch_object($result);

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$phone_id/$action_id">
    <p>What is your clan password (6 characters)?</p>
    <p class="second">Enter <strong>delete</strong> to remove the password</p>
    <input type="text" name="password" size="6" maxlength="6"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return false;

    } // no new password

    if (strtolower($clan_password) == 'delete') $clan_password = '';

    $sql = 'update clans set password = "%s"
      where id = %d;';
    $result = db_query($sql, $clan_password, $game_user->fkey_clans_id);

    $sql = 'insert into clan_messages (fkey_users_from_id,
      fkey_users_to_id, message) values
      (%d, (select id from clans where acronym = "%s"), "%s");';
    $message = 'Notice: the clan password has changed.';
    $result = db_query($sql, $game_user->id, $clan_acronym, $message);

    $outcome_reason .= '<div class="action-effect">The clan password has
      successfully been changed.';

    return true;

  }


  function _stlouis_action_declare_someone_persona_non_grata_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $can_do_again = FALSE;

    $target_id = $_GET['target'];

    if (!_stlouis_check_for_friendly_hood()) {

        $outcome_reason = '<div class="land-failed">' .
        t('Not in a friendly @hood!',
          array('@hood' => $hood_lower)) .
        '</div><div class="action-effect">You must be in friendly territory to
          perform this action.</div>';

      return FALSE;

    }

    $sql = 'SELECT elected_positions.name as ep_name,
      elected_positions.type as ep_type
      FROM `users`

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      WHERE users.id = %d';

    $result = db_query($sql, $game_user->id);
    $data = db_fetch_object($result);
    $ep_name = $data->ep_name;
    $ep_type = $data->ep_type;

    $sql = 'select * from users where id = %d;';
    $result = db_query($sql, $target_id);
    $target_user = db_fetch_object($result);
    $actions_required = ceil($target_user->level / 3);
    $money_required = $target_user->level * 1000;

    if ($ep_type == 1) $actions_required = min($actions_required, 25);
// actions cap of 25 for chief priests

    if ($game_user->actions < $actions_required) { // not enough actions

      $outcome_reason = '<div class="land-failed">' .
        t('Not enough Action!') .
        '</div><div class="action-effect">You do not have enough Action to
        declare this Level ' . $target_user->level .
        ' player <em>persona non grata</em>.</div>';

      $outcome_reason .= '<div class="try-an-election-wrapper"><div
        class="try-an-election"><a href="/' . $game . '/elders_do_fill/' .
        $phone_id . '/action?destination=/' .  $game . '/actions/' . $phone_id .
        '">Refill your Action (1&nbsp;Luck)</a></div></div>';

      return FALSE;

    }

    if ($game_user->money < $money_required) { // not enough money

      $outcome_reason = '<div class="land-failed">' .
        t('Not enough @value', array('@value' => $game_user->values)) .
        '</div><div class="action-effect">You do not have enough ' .
        $game_user->values . ' to declare this Level ' . $target_user->level .
        ' player <em>persona non grata</em>.</div>';

      $offer = ($game_user->income - $game_user->expenses) * 5;
      $offer = min($offer, $game_user->level * 10000);
      $offer = max($offer, $game_user->level * 100);

      $outcome_reason .= '<div class="try-an-election-wrapper"><div
        class="try-an-election"><a href="/' . $game . '/elders_do_fill/' .
        $phone_id . '/money?destination=/' . $game . '/actions/' . $phone_id .
        '">Receive ' . $offer . ' ' . $game_user->values .
        ' (1&nbsp;Luck)</a></div></div>';

      return FALSE;

    }

    $sql = "select name from neighborhoods where id = '%d';";
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $data = db_fetch_object($result);
    $location = $data->name;

// can't boot zombies

    if ($target_user->meta == 'zombie') {

      $outcome_reason = '<div class="land-failed">' .
        t('Action Failed!') .
        '</div><div class="action-effect">Zombies do not respect political
        borders.&nbsp; Have you tried debating them?</div>';

      return FALSE;

    }

// add message to user
    $sql = 'insert into user_messages (fkey_users_from_id,
      fkey_users_to_id, message) values (%d, %d, "%s");';
    $message = $ep_name . ' ' . $game_user->username . ' has declared you
      persona non grata.  You have been kicked out of ' . $location . '.';

    if ($game == 'celestial_glory') {

      if ($target_user->fkey_neighborhoods_id == 1 &&
        $target_user->fkey_values_id == 4) {

        $outcome_reason = '<div class="land-failed">' .
          t('Action Failed!') .
          '</div><div class="action-effect">You can not run a Babylonian
          out of the region.</div>';

        return FALSE;

      } else {

        $message = $ep_name . ' ' . $game_user->username . ' has run you
          out of ' . $location . '.';

      }

    } // game is CG

    $result = db_query($sql, $game_user->id, $target_id, $message);

    competency_gain($target_user, 'outcast');

// move user, empty Action
    $sql = 'update users set fkey_neighborhoods_id =
      (select fkey_neighborhoods_id from `values` where id = %d),
      actions = 0
      where id = %d;';
    $result = db_query($sql, $target_user->fkey_values_id, $target_id);

// deduct your Action, money
    $sql = 'update users
      set actions = actions - %d, money = money - %d
      where id = %d;';
    $result = db_query($sql, $actions_required, $money_required,
      $game_user->id);

    if ($ep_type > 1) { // set major_action counter for three minutes
      $set_value = '_' . arg(0) . '_set_value';
      $set_value($game_user->id, 'next_major_action', time() + 180);
    }

    $outcome_reason .= '<div class="action-effect">' . $target_user->username .
      ' has been returned to his or her home neighborhood.';

    slack_send_message('Player ' . $game_user->username . ' has booted '
    . $target_user->username . ' from ' . $location, $slack_channel);

    return TRUE;

  }


  function _celestial_glory_action_run_someone_out_of_the_region_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_declare_someone_persona_non_grata_function($outcome_reason,
      $target, $can_do_again);

  }


  function _stlouis_action_roll_call_function(&$outcome_reason, $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);

    $sql = "select name from neighborhoods where id = '%d';";
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $data = db_fetch_object($result);
    $location = $data->name;

    $data = array();
    $sql = 'SELECT username, experience, initiative, endurance,
      elocution, debates_won, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, phone_id,
      `values`.clan_title, `values`.clan_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
      elected_officials.approval_rating,
      clan_members.is_clan_leader,
      clans.name as clan_name, clans.acronym as clan_acronym,
      clans.rules as clan_rules,
      neighborhoods.name as location

      FROM `users`

      LEFT JOIN `values` ON users.fkey_values_id = `values`.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      LEFT JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id

      where ( users.actions_next_gain > "%s" OR users.energy_next_gain > "%s" )
      and users.fkey_neighborhoods_id = %d
      and username <> ""
      ORDER by users.experience DESC;';

    $result = db_query($sql, date('Y-m-d', time() - 1728000),
      date('Y-m-d', time() - 1728000), $game_user->fkey_neighborhoods_id);
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="action-effect">These players are in $location:
    <div class="elections-header">
<div class="election-details">
  <div class="clan-title">$politics</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;

    foreach ($data as $item) {
firep($item);

      $username = $item->username;
      if (empty($username)) $username = '<em>Anonymous</em>';

      if ($item->id == $game_user->id) {
        $clan_class = 'election-details me';
      } else {
        $clan_class = 'election-details';
      }

      $action_class = '';
      $official_link = $item->ep_name;
      $clan_class = 'election-details';

      if ($item->can_broadcast_to_party)
        $official_link .= '<div class="can-broadcast-to-party">*</div>';

       $official_link .= '<br/><a href="/' . $game . '/user/' .
         $phone_id . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';

       $experience = $item->experience;
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $icon = $game . '_clan_' . $item->clan_icon . '.png';

      $outcome_reason .=<<< EOF
      <div class="$clan_class">
  <div class="clan-icon"><img
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$item->clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$experience Influence<br/>
    Level $item->level</div>
</div>
EOF;

    } // foreach position

    return true;

  }


  function _celestial_glory_action_take_a_census_function(&$outcome_reason, $target) {

    return _stlouis_action_roll_call_function($outcome_reason, $target);

  }


  function _stlouis_action_ping_function(&$outcome_reason, $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);

    $sql = "select name from neighborhoods where id = '%d';";
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $data = db_fetch_object($result);
    $location = $data->name;

    $data = array();
    $sql = 'SELECT username, experience, initiative, endurance,
      elocution, debates_won, debates_lost, skill_points, luck,
      debates_last_time, users.fkey_values_id, level, phone_id,
      `values`.clan_title, `values`.clan_icon,
      `values`.name, users.id, users.fkey_neighborhoods_id,
      elected_positions.name as ep_name,
      elected_officials.approval_rating,
      clan_members.is_clan_leader,
      clans.name as clan_name, clans.acronym as clan_acronym,
      clans.rules as clan_rules,
      neighborhoods.name as location

      FROM `users`

      LEFT JOIN `values` ON users.fkey_values_id = `values`.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      LEFT JOIN neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id

      where ( users.actions_next_gain > "%s" OR users.energy_next_gain > "%s" )
      and users.fkey_values_id = %d
      and username <> ""
      ORDER by users.experience DESC;';

    $result = db_query($sql, date('Y-m-d', time() - 1200),
      date('Y-m-d', time() - 1200), $game_user->fkey_values_id);
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="action-effect">These players are online:
    <div class="elections-header">
<div class="election-details">
  <div class="clan-title">$politics</div>
  <div class="opponent-name">Name</div>
  <div class="opponent-influence">Stats</div>
</div>
</div>
<div class="elections">
EOF;

    foreach ($data as $item) {
firep($item);

      $username = $item->username;
      if (empty($username)) $username = '<em>Anonymous</em>';

      if ($item->id == $game_user->id) {
        $clan_class = 'election-details me';
      } else {
        $clan_class = 'election-details';
      }

      $action_class = '';
      $official_link = $item->ep_name;
      $clan_class = 'election-details';

      if ($item->can_broadcast_to_party)
        $official_link .= '<div class="can-broadcast-to-party">*</div>';

       $official_link .= '<br/><a href="/' . $game . '/user/' .
         $phone_id . '/' . $item->phone_id . '"><em>' . $username . '</em></a>';

       $experience = $item->experience;
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $icon = $game . '_clan_' . $item->clan_icon . '.png';

      $outcome_reason .=<<< EOF
      <div class="$clan_class">
  <div class="clan-icon"><img
    src="/sites/default/files/images/$icon"/></div>
  <div class="clan-title">$item->clan_title</div>
  <div class="opponent-name">$official_link $clan_acronym</div>
  <div class="opponent-influence">$experience Influence<br/>
    Level $item->level</div>
</div>
EOF;

    } // foreach position

    return true;

  }


  function _celestial_glory_action_fill_out_your_family_tree_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
//    $fetch_header = '_' . arg(0) . '_header';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
//    $action_id = arg(3);

    $sql = 'update users set family_members_found = family_members_found + 1,
      experience = experience + %d where id = %d;';
    $result = db_query($sql, floor(sqrt($game_user->initiative)), $game_user->id);

    $outcome_reason .= '<div class="action-effect">You have now found and recorded ' .
      ($game_user->family_members_found + 1) . ' family members!';
    $outcome_reason .= '<div class="action-effect">You gain a bonus of ' .
      floor(sqrt($game_user->initiative)) . ' ' . $experience;

    return true;

  }


function _celestial_glory_action_bear_your_testimony_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
//    $action_id = arg(3);

    $bonus = mt_rand(5,25);
    $sql = 'update users set money = money + %d where id = %d;';
    $result = db_query($sql, $bonus, $game_user->id);

    $outcome_reason .= '<div class="action-effect">Your ' . $game_user->values .
      ' is increased by ' . $bonus . '</div>';

    return true;

  }


function _celestial_glory_action_pick_roses_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');

    $sql = 'select roses from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result);
firep($item, 'roses');

    if ($item->roses < 1) {

      $outcome_reason = '<div class="title">
          No roses!
        </div>
        <div class="subtitle">
          You cannot find any roses here
        </div>';

      return FALSE;

    }

    $sql = 'update neighborhoods set roses = roses - 1 where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = 31 and fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $data = db_fetch_object($result);
firep($data, 'rose quantity');

    if (empty($data)) { // none yet -- give one

      $sql = 'insert into equipment_ownership
        (fkey_users_id, fkey_equipment_id, quantity) values
        (%d, 31, 1);';
      $result = db_query($sql, $game_user->id);

    } else { // need one more

      $sql = 'update equipment_ownership set quantity = quantity + 1
        where fkey_equipment_id = 31 and fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);

    }

    $outcome_reason = '<div class="subtitle">
        You picked a rose!
      </div>';

    competency_gain($game_user, 'gardener');
    return TRUE;

  }


function _celestial_glory_action_dance_in_the_streets_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');

    $sql = 'select quantity from equipment_ownership
        where fkey_equipment_id = 31 and fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);
// firep($item, 'roses');

    if ($item->quantity < 8) {

      $outcome_reason = '<div class="title">
          Not enough roses!
        </div>
        <div class="subtitle">
          You only have ' . $item->quantity . ' roses &mdash; you need 8!
        </div>';

      return FALSE;

    }

    $sql = 'update equipment_ownership set quantity = quantity - 8
      where fkey_equipment_id = 31 and fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);

    $outcome_reason = '<div class="subtitle">
        What a party!
      </div>';

    return TRUE;

  }


  function _celestial_glory_action_launder_some_value_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . $game . '_fetch_user';
    $set_value = '_' . $game . '_set_value';
    $get_value = '_' . $game . '_get_value';

    $game_user = $fetch_user();
    $target_id = $_GET['target'];

    include(drupal_get_path('module', $game) . '/game_defs.inc');

// get family of CP
    $sql = 'select users.id, users.fkey_values_id from elected_officials
      left join users on elected_officials.fkey_users_id = users.id
      where elected_officials.fkey_elected_positions_id = 1
      and users.fkey_neighborhoods_id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $cp = db_fetch_object($result);
firep($cp, 'family of chief priest in region');

// get family of target
    $sql = 'select fkey_values_id, level from users where id = %d;';
    $result = db_query($sql, $target_id);
    $target_values_id = db_fetch_object($result);
firep($target_values_id, 'family of target');

// not a merchant, target not a merchant, and not in a hood controlled by
// merchants?
    if ($cp->fkey_values_id != 5 && $game_user->fkey_values_id != 5
      && $target_values_id->fkey_values_id != 5) {

      $outcome_reason = '<div class="title">
          No Merchants!
        </div>
        <div class="subtitle">
          You must be in a ' . $hood_lower . ' controlled by Merchants
        </div>';

      return FALSE;

    }

// target has a major action timer?
    if ($get_value($target_id, 'next_major_action', 0) > time()) {

      $outcome_reason = '<div class="title">
          Not Available
        </div>
        <div class="subtitle">
          Your recipient is busy right now<br/>
          Please try again in a few minutes
        </div>';

      return FALSE;

    }

// player gets ten minutes of major action timer per level
    $set_value($game_user->id, 'next_major_action',
      time() + ($game_user->level * 600));

// target gets one minute of major action timer per level
    $set_value($target_id, 'next_major_action',
      time() + ($target_values_id->level * 60));

    $outcome_reason = '<div class="subtitle">
        No one noticed, right?
      </div>';

    if ($game_user->fkey_values_id != 5
      && $target_values_id->fkey_values_id != 5) {
// not a Merchant?  1% goes to CP

      $tax = $game_user->level * 12500;
      if ($game_user->level < 120) $tax = $game_user->level * 2500;
      if ($game_user->level < 90) $tax = $game_user->level * 500;
      if ($game_user->level < 60) $tax = $game_user->level * 100;

      $sql = 'update users set money = money + %d where id = %d;';
      $result = db_query($sql, $tax, $cp->id);

    } // CP tax

    return TRUE;

  }



  function _stlouis_action_steal_some_value_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
//    include(drupal_get_path('module', $game) . '/game_defs.inc');
//    $action_id = arg(3);
    $sql = 'update users set karma = karma - 5 where id = %d;';
    $result = db_query($sql, $game_user->id);

    $sql = 'update users set debates_last_time = "%s" where id = %d;';
    $result = db_query($sql, date('Y-m-d H:i:s', time()), $game_user->id);

    return TRUE;

  }


  function _stlouis_action_erase_your_wall_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
//    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
    $sure = check_plain($_GET['sure']);

    if ($sure != 'yes') {

      $outcome_reason = '<div class="subtitle">Are you sure?</div>
        <div class="subsubtitle">
          This deletes <strong>all messages</strong> and cannot be undone!
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/actions_do/' .
            $arg2 . '/' . $action_id . '?sure=yes">Yes,  I\'m sure</a>
          </div>
          <div class="try-an-election">
            <a href="/' . $game . '/actions/' . $phone_id .
            '">No, I\'m not sure</a>
          </div>
        </div>';

        return FALSE;

    }

    $sql = 'delete from user_messages where fkey_users_to_id = %d;';
    $result = db_query($sql, $game_user->id);

    return TRUE;

  }

  function _stlouis_action_give_luck_to_a_clan_member_function(&$outcome_reason, $target) {

    global $game, $phone_id;
    $game_user = _stlouis_fetch_user();
    $action_id = arg(3);
    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];
    $sure = check_plain($_GET['sure']);

    if ((substr($phone_id, 0, 3) == 'sdk') ||
      (strpos($_SERVER['HTTP_USER_AGENT'], 'vm Build/MASTER') !== FALSE) ||
      (strpos($_SERVER['HTTP_USER_AGENT'], 'sdk Build') !== FALSE)) {

      $outcome_reason = '<div class="land-failed">' . t('Sorry!') .
        '</div><div class="action-effect">SDK users cannot give Luck.</div>';

      return FALSE;

    }

    if ($game_user->luck < 20) { // not enough luck

      $outcome_reason = '<div class="land-failed">' .
        t('Not enough Luck!') .
        '</div><div class="action-effect">You do not have 10 extra Luck to give</div>';

      return FALSE;

    }

    if ($sure != 'yes') {

      $outcome_reason = '<div class="subtitle">Are you sure?</div>
        <div class="subsubtitle">
          This gives 10 of your Luck and cannnot be undone!
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/actions_do/' .
            $phone_id . '/' . $action_id . '?target=' . $target_id .
            '&sure=yes">Yes,  I\'m sure</a>
          </div>
          <div class="try-an-election">
            <a href="/' . $game . '/actions/' . $phone_id .
            '">No, I\'m not sure</a>
          </div>
        </div>';

        return FALSE;

    }

    $sql = 'insert into challenge_history
        (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
        fkey_elected_positions_id, won, desc_short, desc_long) values
        ("gift", %d, %d, %d, %d, %d, "%s", "%s");';
     $result = db_query($sql, $game_user->id, $target_id,$game_user->fkey_neighborhoods_id, $target->ep_id, 0,
        "$game_user->username gave a gift of Luck to " .$target_name. '.',"$game_user->username gave a gift of 10 Luck to " . $target_name.
        ' (currently ' . $target->luck . ').');

    $sql = 'update users set luck = luck - 10 where id = %d;';
    $result = db_query($sql, $game_user->id);

    $sql = 'update users set luck = luck + 10 where id = %d;';
    $result = db_query($sql, $target_id);

    $outcome_reason .= '<div class="action-effect">' .
      t('You have transferred 10 of your Luck to @name',
      array('@name' => $target_name)) . '</div>';

    return true;

  }


  function _celestial_glory_action_eat_some_food_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $sql = 'update equipment_ownership set quantity = quantity - 1
      where fkey_users_id = %d and fkey_equipment_id = 1;';
    $result = db_query($sql, $game_user->id);

// calculate energy bonus
    $energy_bonus = 10 + $game_user->ep_energy_bonus +
      $game_user->eq_energy_increase + $game_user->st_energy_increase;
    if (!empty($game_user->fkey_clans_id)) $energy_bonus++; // bonus for joining a clan

    $sql = 'update users set energy = energy + %d where id = %d;';
    $result = db_query($sql, $energy_bonus, $game_user->id);

    $outcome_reason .= '<div class="action-effect">' .
      t('You have eaten and feel more energetic') . '</div><div class="action-effect">' .
      t('(You gained @energy energy)', array('@energy' => $energy_bonus)) . '</div>';

    competency_gain($game_user, 'feasting');
    return TRUE;

  }


  function _celestial_glory_action_fast_and_pray_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("fasting", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $game_user->id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username fasted and prayed.",
      "$game_user->username fasted and prayed.");

    $outcome_reason .= '<div class="action-effect">' .
      t('You feel closer to God.') . '</div>';

    return true;

  }


  function _stlouis_action_throw_eggs_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'] + 0;

// the player egging and the player being egged must be in the same hood
    if ($game_user->fkey_neighborhoods_id != $target->fkey_neighborhoods_id) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You are no longer in this neighborhood') . '</div>';

      return FALSE;

    }

// STL-236 -- no unaffiliated eggings
    if ($game_user->fkey_values_id == 0) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You must join a political party first') . '</div>';

      return FALSE;

    }

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("egging", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $target_id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username egged $target_name.",
      "$game_user->username egged $target_name.");

    $outcome_reason .= '<div class="action-effect">' .
      t('What a smell!') . '</div>';

    return TRUE;

  }


  function _stlouis_action_meet_someone_new_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');

//    $target_name = $target->ep_name . ' ' . $target->username;
//    $target_id = $_GET['target'] + 0;

    // both players must be in the same hood
    if ($game_user->fkey_neighborhoods_id != $target->fkey_neighborhoods_id) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You two are no longer in the same @hood',
        array('@hood' => $hood_lower)) . '</div>';

      return FALSE;

    }

    return TRUE;

  }


  function _celestial_glory_action_street_preaching_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_meet_someone_new_function($outcome_reason,
    $target, $can_do_again);

  }


  function _celestial_glory_action_stone_an_official_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'] + 0;

    // the player stoning and the player being stoned (target) are in the same hood.
    if ($game_user->fkey_neighborhoods_id != $target->fkey_neighborhoods_id) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You are no longer in this @hood',
        array('@hood' => $hood_lower)) . '</div>';

      return FALSE;

    }

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("stoning", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $target_id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username stoned $target_name.",
      "$game_user->username stoned $target_name.");

    $outcome_reason .= '<div class="action-effect">' .
      t('Ouch!') . '</div>';

    return true;

  }


  function _stlouis_action_spread_gossip_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = check_plain($_GET['target']) + 0;

// been booted?  fail the gossip
    if ($game_user->fkey_neighborhoods_id != $target->fkey_neighborhoods_id) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You are no longer in this neighborhood') . '</div>';

      return FALSE;

    }

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("gossip", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $target_id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username gossiped about $target_name.",
      "$game_user->username gossiped about $target_name.");

    $outcome_reason .= '<div class="action-effect">' .
      t('You wouldn\'t believe what I\'ve heard about ') . $target_name . '</div>';

//    $can_do_again = FALSE;

    return TRUE;

  }


  function _celestial_glory_action_spread_gossip_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_spread_gossip_function($outcome_reason,
      $target, $can_do_again);

  }


  function _stlouis_action_plant_flowers_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

// STL-236 -- no unaffiliated plantings
    if ($game_user->fkey_values_id == 0) {

      $outcome_reason .= '<div class="action-effect">' .
        t('Sorry!&nbsp; You must join a political party first') . '</div>';

      return FALSE;

    }

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("planting", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $game_user->id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username planted.",
      "$game_user->username planted.");

    $outcome_reason .= '<div class="action-effect">' .
      t('Isn\'t it pretty?') . '</div>';

    return TRUE;

  }


  function _stlouis_action_give_to_a_clan_member_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id, $action;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $get_value = '_' . arg(0) . '_get_value';
    $game_user = $fetch_user();

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'] + 0;

    if ((substr($phone_id, 0, 3) == 'sdk') ||
      $get_value($game_user->id, 'sdk') == 1) {

      $outcome_reason = '<div class="land-failed">' . t('Sorry!') .
        '</div><div class="action-effect">SDK users cannot give Actions.</div>';

      return FALSE;

    }

// we need to make the target user's actions current

    $sql = 'select actions, actions_max, actions_next_gain from users
      where id = %d;';
// limited to 1 in db
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);

    $actions_next_gain = strtotime($item->actions_next_gain);
    $secs_until = $actions_next_gain - time();

// make actions current -- if this didn't happen, you could give actions to a
// user that would be swallowed up by time if he was low on actions and not
// currently playing.

    $text = 'User ' . $game_user->username . ' is sending actions to ' .
      $target_name . ' (currently ' . $item->actions . ' of ' .
      $item->actions_max . ' with a next_gain time of ' .
      $item->actions_next_gain . ').';

    while (($item->actions < $item->actions_max) &&
      ($secs_until <= 0)) { // do we need actions?

      $item->actions++; // add 1 action
      $actions_next_gain += 180; // next add in 3 mins
      $secs_until += 180; // ditto

    }

    while (($item->actions > $item->actions_max) &&
      ($secs_until <= 0)) { // do we need actions?

      $item->actions--; // delete 1 action
      $actions_next_gain += 180; // next add in 3 mins
      $secs_until += 180; // ditto

    }

// now current with actions?  bring the date current too.
    if ($item->actions == $item->actions_max) $actions_next_gain = time();

    $item->actions_next_gain = date('Y-m-d H:i:s', $actions_next_gain);
    $sql = 'update users set actions = %d, actions_next_gain = "%s"
      where id = %d;';
    $result = db_query($sql, $item->actions, $item->actions_next_gain,
      $target_id);

// too many actions already?  fail.
    if ($item->actions >= $item->actions_max * 50) {

      $outcome_reason = '<div class="land-failed">' . t('Thanks for Sharing!') .
        '</div><div class="action-effect">... but ' . $target_name .
        ' has plenty of actions already.</div>';

      return FALSE;

    }


// now save the record of what happened

// if ($game == 'celestial_glory')
//    mail('joseph@cheek.com', 'CG actions sent!', $text);

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("gift", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $target_id,
      $game_user->fkey_neighborhoods_id, 0, 0,
      "$game_user->username gave a gift of Action points to $target_name.",
      "$game_user->username gave a gift of $action->actions_change Action " .
      "points to $target_name (currently $item->actions).");

    return TRUE;

  }


  function _celestial_glory_action_give_to_a_clan_member_function(
    &$outcome_reason, $target, &$can_do_again) {

    return _stlouis_action_give_to_a_clan_member_function($outcome_reason,
    $target, $can_do_again);

  }


  function _stlouis_action_check_for_vandalism_function(&$outcome_reason, $target) {

    global $game, $phone_id;
    $game_user = _stlouis_fetch_user();
    $action_id = arg(3);
    $msg_shown = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where fkey_to_users_id = %d and challenge_history.type = "egging" and timestamp >= "%s"
      order by timestamp DESC;';

    $result = db_query($sql, $game_user->id, date('Y-m-d H:i:s', time() - 86400));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Eggs that have been thrown at your office
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No eggings found!';

    $outcome_reason .= '</div>';

    return true;

  }


  function _celestial_glory_action_check_for_stoning_function(&$outcome_reason,
    $target) {

    global $game, $phone_id, $action;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $msg_shown = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where challenge_history.type = "stoning"
      and challenge_history.timestamp >= "%s"
      order by challenge_history.timestamp DESC;';

    $result = db_query($sql, date('Y-m-d H:i:s', time() - 28800));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Stones thrown in your $hood_lower in the past 8 hours
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No stonings found!';

    $outcome_reason .= '</div>';

    return true;

  }


  function _celestial_glory_action_check_for_fasting_and_prayer_function(
    &$outcome_reason, $target) {

    global $game, $phone_id, $action;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $msg_shown = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where challenge_history.type = "fasting" and timestamp >= "%s"
      order by timestamp DESC;';

    $result = db_query($sql, date('Y-m-d H:i:s', time() - 28800));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Fasting and prayer in your $hood_lower in the past 8 hours
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No fasting found!';

    $outcome_reason .= '</div>';

    return true;

  }


  function _stlouis_action_listen_for_rumors_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    $action_id = arg(3);
    $msg_shown = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where fkey_to_users_id = %d and challenge_history.type = "gossip"
      and challenge_history.timestamp >= "%s"
      order by challenge_history.timestamp DESC;';

    $result = db_query($sql, $game_user->id, date('Y-m-d H:i:s',
      time() - 86400));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Rumors around town
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No rumors found!';

    $outcome_reason .= '</div>';

    return true;

  }


  function _celestial_glory_action_eavesdrop_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_listen_for_rumors_function($outcome_reason,
      $target, $can_do_again);

  }


  function _stlouis_action_count_your_benefactors_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'] + 0;

    $action_id = arg(3);
    $msg_shown = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where fkey_to_users_id = %d and challenge_history.type = "gift" and
      challenge_history.timestamp >= "%s"
      order by timestamp DESC;';

    $result = db_query($sql, $game_user->id,
      date('Y-m-d H:i:s', time() - 86400));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    List of Benefactors
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No benefactors found!';

    $outcome_reason .= '</div>';

    return TRUE;

  }


  function _celestial_glory_action_check_for_gifts_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_count_your_benefactors_function($outcome_reason,
    $target, $can_do_again);

  }


  function _stlouis_action_check_security_cameras_function(&$outcome_reason, $target) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $msg_shown = FALSE;

    if (!_stlouis_check_for_friendly_hood()) {

    	$outcome_reason = '<div class="land-failed">' .
        t('Not in a friendly @hood!',
          array('@hood' => $hood_lower)) .
        '</div><div class="action-effect">You must be in friendly territory to
          perform this action.</div>';

      return FALSE;

    }

    $hour_to_show = check_plain($_GET['hour']) + 0;

    $sql = 'select quantity from equipment_ownership
      where fkey_users_id = %d and fkey_equipment_id = 21;';
    $result = db_query($sql, $game_user->id);
    $num_cameras = db_fetch_object($result);

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where challenge_history.fkey_neighborhoods_id = %d
      and challenge_history.timestamp >= "%s"
      and challenge_history.timestamp <= "%s"
      order by challenge_history.timestamp DESC;';

    $result = db_query($sql, $game_user->fkey_neighborhoods_id,
      date('Y-m-d H:i:s', time() - ($hour_to_show * 3600) - 3600),
      date('Y-m-d H:i:s', time() - ($hour_to_show * 3600)));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $item->desc_short = 'end';
    $data[] = $item; // hack to force last item to show

    if ($hour_to_show == 1) {
    	$hours = 'hour';
    } else {
    	$hours = 'hours';
    }

// previous hour
    if ($hour_to_show > 0) {

    	$previous_hour = $hour_to_show - 1;

    	$outcome_reason .=<<< EOF
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/actions_do/$phone_id/$action_id?hour=$previous_hour">&lt;&lt;&lt;
      See $previous_hour hours ago</a>
  </div>
</div>
EOF;

    } else {

    	$outcome_reason .=<<< EOF
<div class="try-an-election-wrapper">
  <div class="try-an-election not-yet">
    &lt;&lt;&lt; No more hours to view
  </div>
</div>
EOF;

    }

// next hour
    if ($hour_to_show < ($num_cameras->quantity - 1)) {

      $next_hour = $hour_to_show + 1;

      $outcome_reason .=<<< EOF
<div class="try-an-election-wrapper">
  <div class="try-an-election">
    <a href="/$game/actions_do/$phone_id/$action_id?hour=$next_hour">See
      $next_hour hours ago &gt;&gt;&gt;</a>
  </div>
</div>
EOF;

    } else {

      $outcome_reason .=<<< EOF
<div class="try-an-election-wrapper">
  <div class="try-an-election not-yet">
    No more security cameras &gt;&gt;&gt;
  </div>
</div>
EOF;

    }

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Actions $hour_to_show $hours ago in your {$hood_lower}
  </div>
EOF;

    unset($old_item);
    $instance_count = 1;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      if ($item->desc_short !== $old_item->desc_short) {
// new item to display... show it and reset counters

      	if ($old_item) { // anything to display?

      		if ($instance_count > 1) {
      			$instance_text = "($instance_count times) ";
      			$time_prefix = 'Starting ';
      		} else {
      			$instance_text = $time_prefix = '';
      		}

          $outcome_reason .=<<< EOF
<div class="dateline">
  $time_prefix $old_display_time from $old_item->ep_name $old_item->username $old_clan_acronym
</div>
<div class="message-body">
  <p>$instance_text $old_item->desc_short</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$old_item->phone_id">View</a>
  </div></div>
</div>
EOF;

      	} // old item exists?

// now display current item
//        $outcome_reason .=<<< EOF
//<div class="dateline">
//  $display_time from $item->ep_name $item->username $clan_acronym
//</div>
//<div class="message-body">
//  <p>($item->id) $item->desc_short</p>
//  <div class="message-reply-wrapper"><div class="message-reply">
//    <a href="/$game/user/$phone_id/$item->phone_id">View</a>
//  </div></div>
//</div>
//EOF;

        $instance_count = 1;
        $old_item = $item;
        $old_clan_acronym = $clan_acronym;
        $old_display_time = $display_time;

      } else { // dup of existing item... abbreviate

      	$instance_count++;

      } // new or existing item?

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'Nothing found!';

    $outcome_reason .= '</div>';

    return TRUE;

  }


  function _stlouis_action_expel_a_party_member_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $arg2 = check_plain(arg(2));
    $action_id = arg(3);
    $sure = check_plain($_GET['sure']);
    $fetch_user = '_' . arg(0) . '_fetch_user';

    $target_id = $_GET['target'];

    if ($sure != 'yes') {

      $outcome_reason = '<div class="subtitle">Are you sure?</div>
        <div class="subsubtitle">
          This expels level <strong>' . $target->level .
          '</strong> <strong>' . $target->username .
          '</strong> and cannot be undone!
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/actions_do/' .
            $arg2 . '/' . $action_id . '?target=' . $target_id .
            '&sure=yes">Yes, I\'m sure</a>
          </div>
          <div class="try-an-election">
            <a href="/' . $game . '/actions/' . $phone_id .
            '">No, I\'m not sure</a>
          </div>
        </div>';

      return FALSE;

    }

    $game_user = $fetch_user();

    $sql = 'select * from users where id = %d;';
    $result = db_query($sql, $target_id);
    $target_user = db_fetch_object($result);
    $actions_required = $target_user->level;

    if ($target_user->fkey_values_id == 0) { // user is already expelled

      $outcome_reason = '<div class="land-failed">' .
        t('Already expelled!') .
        '</div><div class="action-effect">You can\'t expel someone who has
          already been expelled.</div>';

      return FALSE;

    }

    if ($game_user->actions < $actions_required) { // not enough actions

      $outcome_reason = '<div class="land-failed">' .
        t('Not enough Action!') .
        '</div><div class="action-effect">You do not have enough Action to
          expel this Level ' . $target_user->level . ' player.</div>';

      $outcome_reason .= '<div class="try-an-election-wrapper"><div
        class="try-an-election"><a href="/' . $game . '/elders_do_fill/' .
        $phone_id . '/action?destination=/' .  $game . '/actions/' . $phone_id .
        '">Refill your Action (1&nbsp;Luck)</a></div></div>';

      return FALSE;

    }

// expel succeeds!  save as major action

    $sql = 'SELECT elected_positions.name as ep_name,
      elected_positions.type as ep_type
      FROM `users`

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = users.id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      WHERE users.id = %d';

    $result = db_query($sql, $game_user->id);
    $data = db_fetch_object($result);
    $ep_name = $data->ep_name;
    $ep_type = $data->ep_type;

    $set_value = '_' . arg(0) . '_set_value';
    $set_value($game_user->id, 'next_major_action', time() + 7200);
    $set_value($target_id, 'cant_challenge', time() + 86400);
    $set_value($target_id, 'cant_join_party', time() + 259200);

    $sql = "select clan_title from `values` where id = '%d';";
    $result = db_query($sql, $game_user->fkey_values_id);
    $data = db_fetch_object($result);

    $party = ($game == 'stlouis') ? 'party' : 'family';

// add message to user
    $sql = 'insert into user_messages (fkey_users_from_id,
      fkey_users_to_id, message) values (%d, %d, "%s");';
    $message = $ep_name . ' ' . $game_user->username .
    ' has expelled you from the ' .
    $data->clan_title . ' ' . $party . '. &nbsp; You have been stripped
      of 10% of your influence and of your aides.';
    $result = db_query($sql, $game_user->id, $target_id, $message);

// update aides
    $sql = 'update equipment_ownership
      set quantity = ceil(quantity * 0.9) where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

    $sql = 'update land_ownership
      set quantity = ceil(quantity * 0.9) where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

    $sql = 'update staff_ownership
      set quantity = ceil(quantity * 0.9) where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

// update levels, skill points
    $sql = 'select experience from users where id = %d;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);
    $new_experience = ceil($item->experience * 0.9);

    $sql = 'SELECT max(level) as new_level from levels where experience <= %d;';
    $result = db_query($sql, $new_experience);
    $item = db_fetch_object($result);
    $new_level = $item->new_level;

    $sql = 'SELECT count(quests.id) as bonus FROM `quest_group_completion`
      left outer join quests
      on quest_group_completion.fkey_quest_groups_id = quests.group
      WHERE fkey_users_id = %d and quests.active = 1;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result); // limited to 1 in db
    $new_skill_points = ($new_level * 4) + $item->bonus - 20;

    $values = ($game == 'stlouis') ? 'Greenbacks' : 'No Values';

// update his/her user entry
    $sql = 'update users set fkey_values_id = 0,
      `values` = "%s", level = %d, experience = %d, energy_max = 200,
      skill_points = %d, initiative = 1, endurance = 1, actions = 3,
      actions_max = 3, elocution = 1
      where id = %d;';
    $result = db_query($sql, $values, $new_level, $new_experience,
      $new_skill_points, $target_id);

// also delete any offices s/he held
    $sql = 'delete from elected_officials where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

// and any clan memberships s/he had (disband the clan if s/he was the leader)
    $sql = 'select * from clan_members where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);

    if ($item->is_clan_leader) { // clan leader? delete entire clan

      $sql = 'delete from clan_messages where fkey_neighborhoods_id = %d;';
      $result = db_query($sql, $item->fkey_clans_id);
      $sql = 'delete from clan_members where fkey_clans_id = %d;';
      $result = db_query($sql, $item->id);
      $sql = 'delete from clans where id = %d;';
      $result = db_query($sql, $item->id);

    } else {

      $sql = 'delete from clan_members where fkey_users_id = %d;';
      $result = db_query($sql, $target_id);

    }

// update income

  $sql = 'update users set income =
    (SELECT sum(land.payout * land_ownership.quantity)
    as income from land
    left join land_ownership
    on land_ownership.fkey_land_id = land.id and
    land_ownership.fkey_users_id = %d)
    where id = %d;';
  $result = db_query($sql, $target_id, $target_id);

// update expenses

  $sql = 'update users set expenses =

    (SELECT sum(equipment.upkeep * equipment_ownership.quantity)
    as expenses from equipment
    left join equipment_ownership
    on equipment_ownership.fkey_equipment_id = equipment.id and
    equipment_ownership.fkey_users_id = %d)

    where id = %d;';
  $result = db_query($sql, $target_id, $target_id, $target_id);

// deduct your Action
    $sql = 'update users set actions = actions - %d where id = %d;';
    $result = db_query($sql, $actions_required, $game_user->id);

    $outcome_reason .= '<div class="action-effect">' . $target_user->username .
      ' has been expelled from your party.</div>';

    $can_do_again = FALSE;

    return TRUE;

  }


  function _celestial_glory_action_disinherit_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_expel_a_party_member_function($outcome_reason,
      $target, $can_do_again);

  }


  function _stlouis_action_count_your_votes_function(&$outcome_reason, $target,
    &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $msg_shown = FALSE;
    $can_do_again = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_to_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_to_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_to_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where fkey_from_users_id = %d and challenge_history.type = "election"
      order by timestamp DESC limit 1;';

    $result = db_query($sql, $game_user->id, date('Y-m-d H:i:s', time() - 86400));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Detailed Election Results
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $desc_short = nl2br($item->desc_short);
      $desc_long = nl2br($item->desc_long);

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time to $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <p>$desc_short</p>
  <p>$desc_long</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View $item->ep_name
      $item->username $clan_acronym</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No elections found!';

    $outcome_reason .= '</div>';

    return TRUE;

  }


  function _celestial_glory_action_count_your_votes_function(&$outcome_reason,
    $target, &$can_do_again) {

    return _stlouis_action_count_your_votes_function($outcome_reason, $target,
      $can_do_again);

  }


  function _stlouis_action_get_challenge_results_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $msg_shown = FALSE;
    $can_do_again = FALSE;

    $data = array();
    $sql = 'SELECT challenge_history.*, users.phone_id, users.username,
      elected_positions.name as ep_name,
      clan_members.is_clan_leader,
      clans.acronym as clan_acronym

      from challenge_history

      left join users on challenge_history.fkey_from_users_id = users.id

      LEFT OUTER JOIN elected_officials
      ON elected_officials.fkey_users_id = challenge_history.fkey_from_users_id

      LEFT OUTER JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id

      LEFT OUTER JOIN clan_members on clan_members.fkey_users_id =
      challenge_history.fkey_from_users_id

      LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

      where fkey_to_users_id = %d and challenge_history.type = "election"
      order by timestamp DESC limit 5;';

    $result = db_query($sql, $game_user->id, date('Y-m-d H:i:s', time() - 86400));
    while ($item = db_fetch_object($result)) $data[] = $item;

    $outcome_reason .=<<< EOF
<div class="news">
  <div class="messages-title">
    Detailed Election Results
  </div>
EOF;

    foreach ($data as $item) {

      $display_time = _stlouis_format_date(strtotime($item->timestamp));
      $clan_acronym = '';

      if (!empty($item->clan_acronym))
        $clan_acronym = "($item->clan_acronym)";

      if ($item->is_clan_leader)
        $clan_acronym .= '*';

      $desc_short = nl2br($item->desc_short);
      $desc_long = nl2br($item->desc_long);

      $outcome_reason .=<<< EOF
<div class="dateline">
  $display_time from $item->ep_name $item->username $clan_acronym
</div>
<div class="message-body">
  <!--<p>$desc_short</p>-->
  <p>$desc_long</p>
  <div class="message-reply-wrapper"><div class="message-reply">
    <a href="/$game/user/$phone_id/$item->phone_id">View $item->ep_name
      $item->username $clan_acronym</a>
  </div></div>
</div>
EOF;

      $msg_shown = TRUE;

    }

    if (!$msg_shown) $outcome_reason .= 'No elections found!';

    $outcome_reason .= '</div>';

    return true;

  }


  function _celestial_glory_action_get_challenge_results_function(
    &$outcome_reason, $target, &$can_do_again) {

    return _stlouis_action_get_challenge_results_function($outcome_reason,
      $target, $can_do_again);

  }


  function _stlouis_check_for_friendly_hood() {

    global $game, $phone_id;

    if ($phone_id == 'abc123') return TRUE;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $sql = 'SELECT fkey_values_id from elected_officials
      left join users on elected_officials.fkey_users_id = users.id
      where users.fkey_neighborhoods_id = %d
      and elected_officials.fkey_elected_positions_id = 1;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result);

    return ($item->fkey_values_id == $game_user->fkey_values_id);

  }

  function _stlouis_action_clear_clan_announcements_function(&$outcome_reason, $target,
    &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
    $sure = check_plain($_GET['sure']);

    $sql = 'SELECT id FROM {clan_members} c WHERE c.fkey_users_id = %d AND c.fkey_clans_id = %d AND c.is_clan_leader = 1';
    $is_clan_leader = db_result(db_query($sql, (int)$game_user->id, (int)$game_user->fkey_clans_id));

    if (empty($is_clan_leader)) {

      $outcome_reason = '<div class="land-failed">' .
        t('Not clan leader!') .
        '</div><div class="action-effect">You are not the clan leader.&nbsp;
        You cannot perform this action.</div>';

      return FALSE;
    }

    if ($sure != 'yes') {

      $outcome_reason = '<div class="subtitle">Are you sure?</div>
        <div class="subsubtitle">
          This deletes <strong>all announcements</strong> and cannot be undone!
        </div>
        <div class="try-an-election-wrapper">
          <div class="try-an-election">
            <a href="/' . $game . '/actions_do/' .
            $arg2 . '/' . $action_id . '?sure=yes">Yes,  I\'m sure</a>
          </div>
          <div class="try-an-election">
            <a href="/' . $game . '/actions/' . $phone_id .
            '">No, I\'m not sure</a>
          </div>
        </div>';

        return false;

    }

    $sql = 'DELETE FROM clan_messages WHERE fkey_neighborhoods_id = %d AND is_announcement = 1';
    $result = db_query($sql, (int)$game_user->fkey_clans_id);

    return !empty($result) ? TRUE : FALSE;
  }


  function _stlouis_action_open_a_bank_account_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
    $can_do_again = FALSE;

    $password = trim(check_plain($_GET['password']));

    if (empty($password)) { // need a password

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Please enter a password for your new account</p>
    <p class="second">Anyone with your account number and password can
      deposit or withdraw</p>
    <input type="text" name="password" size="20" maxlength="20"/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // need password

// generate an account number

    $account_num = 0;

    while ($account_num == 0) {

      $account_num = mt_rand(100000000, 999999999); // 9 digits
      $sql = 'select account_number from bank_accounts
        where account_number = %d;';
      $result = db_query($sql, $account_num);
      $item = db_fetch_object($result);
firep($item);

      if (!empty($item)) { // account number already exists
        $account_num = 0;
      }

    } // generate an account number

// doesn't exist - create it!

    $sql = 'insert into bank_accounts (fkey_users_id, account_number, password)
      values (%d, %d, "%s");';
    $result = db_query($sql, $game_user->id, $account_num, $password);

//    mail('joseph@cheek.com', 'bank account ' . $account_num . ' created',
//      $game_user->username . ' opened it.');

    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your account number is <span class="account-num">$account_num</span>.
</div>
<div class="action-effect">
  Your password is <span class="account-num">$password</span>.
</div>
<div class="action-effect">
  Please write these down!
</div>
EOF;

    return TRUE;

  }


  function _stlouis_action_close_a_bank_account_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
    $can_do_again = FALSE;

    $account_num = trim(check_plain($_GET['account_num']));
    $password = trim(check_plain($_GET['password']));

    if (empty($password) || empty($account_num)) {
// need an account number and password

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Please enter your account number and password</p>
    Account #: <input type="text" name="account_num" size="9" maxlength="9"/>
    <br/>
    Password: <input type="text" name="password" size="20" maxlength="20"/>
    <br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // need account number and password

// validate the info

    $sql = 'select * from bank_accounts
      where account_number = %d
      and password = "%s"
      and fkey_users_id = %d
      and active = 1;';
    $result = db_query($sql, $account_num, $password, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if (empty($item)) { // account info not correct

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We cannot find an open account in
        your name with that account number and password.</div>';

      return FALSE;

    } // account info not correct

// close the account

    $sql = 'update bank_accounts
      set timestamp_closed = CURRENT_TIMESTAMP, active = 0, balance = 0
      where id = %d;';
    $result = db_query($sql, $item->id);
    $sql = 'update users
      set money = money + %d
      where id = %d;';
    $result = db_query($sql, $item->balance, $game_user->id);
    $game_user->money += $item->balance;
// no need to reprocess; automatically
    $balance = number_format($item->balance);

    mail('joseph@cheek.com', 'bank account ' . $account_num . ' closed',
      'by ' . $game_user->username . ', who pocketed ' . $balance .
      ' ' . $game_user->values . '.');

    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your account is now closed and the remaining balance of $balance
  $game_user->values has been given to you.
</div>
EOF;

    return TRUE;

  }


  function _stlouis_action_list_your_bank_accounts_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
//    $can_do_again = FALSE;

    $sql = 'select * from bank_accounts
      where fkey_users_id = %d
      and active = 1;';
    $result = db_query($sql, $game_user->id);
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      $balance = number_format($item->balance);
      $outcome_reason .=<<< EOF
<div class="action-effect">
  Account <span class="account-num medium">$item->account_number</span> has
  <span class="account-num medium">$balance</span>
  $game_user->values
</div>
EOF;

    }

    if (sizeof($data) == 0)
      $outcome_reason .=<<< EOF
<div class="action-effect">No accounts found!</div>
EOF;

//    mail('joseph@cheek.com', 'bank account check', 'by ' .
//      $game_user->username . ', who has ' . sizeof($data) . ' account(s)');

    return TRUE;

  }


  function _stlouis_action_change_a_bank_account_password_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
//    $can_do_again = FALSE;

    $account_num = trim(check_plain($_GET['account_num']));
    $password = trim(check_plain($_GET['password']));

    if (empty($password) || empty($account_num)) {
// need an account number and password

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Please enter the account number and the new password</p>
    Account #: <input type="text" name="account_num" size="9" maxlength="9"/>
    <br/>
    New Password: <input type="text" name="password" size="20" maxlength="20"/>
    <br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // need account number and password

// validate the info

    $sql = 'select * from bank_accounts
      where account_number = %d
      and fkey_users_id = %d
      and active = 1;';
    $result = db_query($sql, $account_num, $game_user->id);
    $item = db_fetch_object($result);
firep($item);

    if (empty($item)) { // account info not correct

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We cannot find an open account in
        your name with that account number.</div>';

      return FALSE;

    } // account info not correct

// update the password

    $sql = 'update bank_accounts
      set password = "%s"
      where id = %d;';
    $result = db_query($sql, $password, $item->id);

//    mail('joseph@cheek.com', 'bank account ' . $account_num .
//      ' password changed', 'by ' . $game_user->username . '.');

    $outcome_reason .=<<< EOF
<div class="action-effect">
  The password for account <strong>$account_num</strong>
  has successfully been changed to <strong>$password</strong>.
</div>
<div class="action-effect">
  The old password will no longer work for deposits or withdrawals.
</div>
EOF;

    return TRUE;

  }


  function _stlouis_action_deposit_funds_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
//    $can_do_again = FALSE;

    $account_num = trim(check_plain($_GET['account_num']));
//    $password = trim(check_plain($_GET['password']));
    $amount = trim(check_plain($_GET['amount']));

//    if (empty($password) || empty($account_num) || empty($amount)) {
    if (empty($account_num) || empty($amount)) {
// need an account number and password and amount

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Please enter the account number, password, and amount you wish to
      deposit</p>
    Account #: <input type="text" name="account_num" size="9" maxlength="9"/>
    <br/>
<!--    Password: <input type="text" name="password" size="20" maxlength="20"/>
    <br/>-->
    Amount: <input type="text" name="amount" size="10" maxlength="10"/>
    <br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // need account number and password

// validate the info -- first, check that the account exists

    $sql = 'select * from bank_accounts
      where account_number = %d
--      and password = "%s"
      and active = 1;';
    $result = db_query($sql, $account_num/* , $password */ );
    $item = db_fetch_object($result);
firep($item);

    if (empty($item)) { // account info not correct

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We cannot find an open account
        with that account number and password.</div>';

      return FALSE;

    } // account info not correct

// set transaction_lock and then reprocess user object
// spin 10 times for 1 second waiting for a lock, if necessary

    $lock_acquired = FALSE;
    $lock_try = 0;

    while ((!$lock_acquired) && ($lock_try < 10)) {

      $sql = 'select * from bank_accounts
        where account_number = %d
--        and password = "%s"
        and active = 1;';
      $result = db_query($sql, $account_num /*, $password */);
      $item = db_fetch_object($result);

      if ($item->transaction_lock == 0) {
        db_query('update bank_accounts set transaction_lock = 1
          where id = %d;', $item->id);
        $lock_acquired = TRUE;
        $lock_try = 0;
      } else {
        $lock_try++;
        sleep(1);
      }

    } // try to get a lock

    if ($lock_try > 0) { // unable to get a lock

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We temporarily cannot access your
        account.&nbsp; Please try again later.</div>';

      return FALSE;

    } // cannot get lock after 10 tries

    $game_user = $fetch_user();

// check moolah

    if (($amount > $game_user->money) || ($amount < 0)) { // not enough moolah

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">You do not have that much to
        deposit.</div>';

      db_query('update bank_accounts set transaction_lock = 0
        where id = %d;', $item->id);
      return FALSE;

    } // not enough moolah

// next, check account

// make the deposit and subtract from the user

    $sql = 'update bank_accounts
      set balance = balance + %d
      where id = %d;';
    $result = db_query($sql, $amount * 0.8, $item->id);

    $sql = 'update users
      set money = money - %d
      where id = %d;';
    $result = db_query($sql, $amount, $game_user->id);
    $game_user->money -= $amount;
    $new_balance = number_format($item->balance + $amount * 0.8);

    $sql = 'insert into bank_account_transactions
      (fkey_users_id, fkey_bank_accounts_id, transaction_type, orig_balance,
      fkey_account_values_id, transaction_amount, fkey_transaction_values_id,
      account_currency_strength, transaction_currency_strength,
      account_currency_amount, fee_percentage, fee_amount,
      net_transaction_amount, ending_balance)

      values ($game_user->id, %d, "deposit", %d,
        (SELECT users.fkey_values_id
        FROM  `bank_accounts`
        LEFT JOIN users ON users.id = bank_accounts.fkey_users_id
        WHERE bank_accounts.account_number = %d),
      %d, %d, 1.00, 1.00, %d, 20, %d, %d, %d);';
    $result = db_query($sql, $item->id, $item->balance, $account_num,
      $amount, $game_user->fkey_values_id, $amount, $amount * 0.2,
      $amount * 0.8, $item->balance + $amount * 0.8);

/*
    mail('joseph@cheek.com', 'bank account deposit to ' . $account_num . '.',
      'by ' . $game_user->username . ', who deposited ' .
      number_format($amount) . ' less ' . number_format($amount * 0.2) .
      ' in fees.  The ' .
      'current balance in the account is now ' . $new_balance . '.');
*/

    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your deposit was successful!
</div>
EOF;

    if ($game_user->id == $item->fkey_users_id) { // show balance if you own it

    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your account balance is now $new_balance $game_user->values.
</div>
EOF;

    }

// reprocess user object and then clear transaction_lock
      db_query('update bank_accounts set transaction_lock = 0
        where id = %d;', $item->id);

    $game_user = $fetch_user();
    return TRUE;

  }


  function _stlouis_action_withdraw_funds_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
    $arg2 = check_plain(arg(2));
//    $can_do_again = FALSE;

    $account_num = trim(check_plain($_GET['account_num']));
    $password = trim(check_plain($_GET['password']));
    $amount = trim(check_plain($_GET['amount']));

    if (empty($password) || empty($account_num) || empty($amount)) {
// need an account number and password and amount

      $outcome_reason =<<< EOF
<div class="ask-clan-name">
  <form method=get action="/$game/actions_do/$arg2/$action_id">
    <p>Please enter the account number, password, and amount you wish to
      withdraw</p>
    Account #: <input type="text" name="account_num" size="9" maxlength="9"/>
    <br/>
    Password: <input type="text" name="password" size="20" maxlength="20"/>
    <br/>
    Amount: <input type="text" name="amount" size="10" maxlength="10"/>
    <br/>
    <input type="submit" value="Submit"/>
  </form>
</div>
EOF;

      return FALSE;

    } // need account number and password

// validate the info -- first, check that the account exists

    $sql = 'select * from bank_accounts
      where account_number = %d
      and password = "%s"
      and active = 1;';
    $result = db_query($sql, $account_num, $password);
    $item = db_fetch_object($result);
firep($item);

    if (empty($item)) { // account info not correct

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We cannot find an open account
        with that account number and password.</div>';

      return FALSE;

    } // account info not correct

// set transaction_lock and then reprocess user object
// spin 10 times for 1 second waiting for a lock, if necessary

    $lock_acquired = FALSE;
    $lock_try = 0;

    while ((!$lock_acquired) && ($lock_try < 10)) {

      $sql = 'select * from bank_accounts
        where account_number = %d
        and password = "%s"
        and active = 1;';
      $result = db_query($sql, $account_num, $password);
      $item = db_fetch_object($result);

      if ($item->transaction_lock == 0) {
        db_query('update bank_accounts set transaction_lock = 1
          where id = %d;', $item->id);
        $lock_acquired = TRUE;
        $lock_try = 0;
      } else {
        $lock_try++;
        sleep(1);
      }

    } // try to get a lock

    if ($lock_try > 0) { // unable to get a lock

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">We temporarily cannot access your
        account.&nbsp; Please try again later.</div>';

      return FALSE;

    } // cannot get lock after 10 tries

    $game_user = $fetch_user();

// check moolah

    if (($amount > $item->balance) || ($amount < 0)) { // not enough moolah

      $outcome_reason = '<div class="land-failed">' .
        t('Sorry!') .
        '</div><div class="action-effect">The account does not have that much to
        withdraw.</div>';

      db_query('update bank_accounts set transaction_lock = 0
        where id = %d;', $item->id);
      return FALSE;

    } // not enough moolah

// make the withdrawal and add to the user

    $sql = 'update bank_accounts
      set balance = balance - %d
      where id = %d;';
    $result = db_query($sql, $amount, $item->id);

    $sql = 'update users
      set money = money + %d
      where id = %d;';
    $result = db_query($sql, $amount, $game_user->id);
    $game_user->money += $amount;
    $new_balance = number_format($item->balance - $amount);

    $sql = 'insert into bank_account_transactions
      (fkey_users_id, fkey_bank_accounts_id, transaction_type, orig_balance,
      fkey_account_values_id, transaction_amount, fkey_transaction_values_id,
      account_currency_strength, transaction_currency_strength,
      account_currency_amount, fee_percentage, fee_amount,
      net_transaction_amount, ending_balance)

      values ($game_user->id, %d, "withdrawal", %d,
        (SELECT users.fkey_values_id
        FROM  `bank_accounts`
        LEFT JOIN users ON users.id = bank_accounts.fkey_users_id
        WHERE bank_accounts.account_number = %d),
      %d, %d, 1.00, 1.00, %d, 0, 0, %d, %d);';
    $result = db_query($sql, $item->id, $item->balance, $account_num,
      $amount, $game_user->fkey_values_id, $amount,
      $amount, $item->balance - $amount);

    if ($game_user->id != $item->fkey_users_id) { // not owned by you?
      $not_owner = 'The withdrawer is not the account owner!';
    }

/*
    mail('joseph@cheek.com', 'bank account withdrawal from ' . $account_num .
      '.', 'by ' . $game_user->username . ', who withdrew ' .
      number_format($amount) . '.  The ' .
      'current balance in the account is now ' . $new_balance . '.  ' .
      $not_owner);
*/
    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your withdrawal was successful!
</div>
EOF;

    if ($game_user->id == $item->fkey_users_id) { // show balance if you own it

    $outcome_reason .=<<< EOF
<div class="action-effect">
  Your account balance is now $new_balance $game_user->values.
</div>
EOF;

    }

// reprocess user object and then clear transaction_lock
      db_query('update bank_accounts set transaction_lock = 0
        where id = %d;', $item->id);

    $game_user = $fetch_user();
    return TRUE;

  }


  function _stlouis_action_support_your_party_official_function(
    &$outcome_reason, $target, &$can_do_again){

    global $game, $phone_id;
    $game_user = _stlouis_fetch_user();
    $target_id = $_GET['target'];

    $sql = "SELECT elected_officials.id
      FROM elected_officials
      LEFT JOIN users ON elected_officials.fkey_users_id = users.id
      LEFT JOIN elected_positions
      ON elected_positions.id = elected_officials.fkey_elected_positions_id
      WHERE users.fkey_values_id = %d
      AND elected_positions.type = 2
      AND users.id = %d";

    $result = db_query($sql, $game_user->fkey_values_id, (int)$target_id);
    $item = db_fetch_object($result);

    if ($item) {

      return TRUE;

    } else {

      $outcome_reason = '<div class="land-failed">' .
        t('Target is not a party official!') .
        '</div>';
      return FALSE;

    }

  }


  function _celestial_glory_action_support_your_party_official_function(
    &$outcome_reason, $target, &$can_do_again) {

    return _stlouis_action_support_your_party_official_function(
      $outcome_reason, $target, $can_do_again);

  }


  function _stlouis_action_check_st_louis_time_function(&$outcome_reason,
    $target, &$can_do_again) {

    $st_time = date('g:i:s A', time() - 3600); // one hour ago, since we are ET
    $outcome_reason .= '<div class="title">Current Time in St. Louis</div>
      <div class="subtitle">' . $st_time . '</div>';

    return TRUE;

  }


  function _stlouis_action_don_a_different_costume_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
/*
    $sql = 'update users set username =
      concat(elt(floor (rand() * 13) + 1,
      "Happy", "Scary", "Funny", "Undead", "Rainbow", "Hero", "Gothic",
      "Frightening", "Sexy", "Fierce", "Skinny", "Elderly", "Plump"),
      elt(floor (rand() * 28) + 1,
      "Elf", "Ghost", "Witch", "Pirate", "Zombie", "Princess", "Monster",
      "Fairy", "Werewolf", "Pumpkin", "Spider", "Wrestler",  "Dictator",
      "Vampire", "Cowboy", "Cowgirl", "Gypsy", "King", "Queen", "Alien",
      "Bee", "Devil", "Elvis", "Baby", "Man", "Woman", "Lizard", "Dinosaur"),
      floor(rand() * 9999))
      where id = %d';
    $result = db_query($sql, $game_user->id);

    $game_user = $fetch_user();
*/
    $outcome_reason = '<div class="title">Halloween is over!</div>';

    return FALSE;

  }


  function _stlouis_action_remove_your_costume_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    $old_username = $game_user->username;
    $can_do_again = FALSE;

    $sql = 'update users set username = real_username
      where id = %d';
    $result = db_query($sql, $game_user->id);

    $game_user = $fetch_user();
    $new_username = $game_user->username;

    if ($old_username != $new_username) {

      $outcome_reason .= '<div class="subtitle">You are now known as</div>
        <div class="title">' . $game_user->username . '</div>';

      return TRUE;

    } else {

      switch(mt_rand(0, 1)) {

        case 0:
          $outcome_reason = '<div class="title">Sadly</div>
            <div class="subtitle">You are not wearing a costume :-(((</div>';
        break;

        case 1:
          $outcome_reason = '<div class="title">What?</div>
            <div class="subtitle">You want to go around naked???</div>';
        break;

      }

      return FALSE;

    }

  }


  function _stlouis_action_trick_or_treat_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
    $old_username = $game_user->username;
//    $can_do_again = FALSE;

    if ($game_user->username == $game_user->real_username) { // no costume!

      $outcome_reason = '<div class="title">Huh?</div>
        <div class="subtitle">You can\'t go out without a costume!</div>';
      return FALSE;

    }

    $sql = 'select name, special_int from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result); // limited to 1 in db;

    if ($item->special_int < 1) { // out of candy

      $outcome_reason = '<div class="title">' . $item->name . '</div>
        <div class="subtitle">is out of treats -- try another neighborhood</div>';

      $sql = 'select count(name) as count
        from neighborhoods where special_int < 1;';
      $result = db_query($sql);
      $item2 = db_fetch_object($result); // limited to 1 in db;

//      mail('joseph@cheek.com', $item2->count . ' neighborhoods out of treats',
//        'hopefully ' . $item->name . ' will get more soon!');

      return FALSE;

    }

    $candy_used = FALSE;

    switch(mt_rand(0, 90)) {

      case 0:
      case 1:
      case 2:
      case 3:
      case 4:
        $outcome_reason = '<div class="title"><img
          src="/sites/default/files/images/stlouis-angry-dog.png"></div>
          <div class="title">BARK BARK BARK!</div>
          <div class="subtitle">A scary dog chases you away</div>';

        return TRUE;
        break; // just for form

      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
        $outcome_reason = '<div class="title">Sadly</div>
          <div class="subtitle">No one is home 8-(</div>';

        return TRUE;
        break; // just for form

      case 10:
      case 11:
      case 12:
      case 13:
      case 14:
      case 15:
      case 16:
      case 17:
      case 18:
      case 19:
        $outcome_reason .=
          '<div class="title">You get your actions refilled!</div>';

        $sql = 'update users set actions = actions_max + 1
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 20:
      case 21:
      case 22:
      case 23:
      case 24:
      case 25:
      case 26:
      case 27:
      case 28:
      case 29:
        $outcome_reason .=
          '<div class="title">You get your energy refilled!</div>';

        $sql = 'update users set energy = energy_max
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 30:
      case 31:
      case 32:
      case 33:
      case 34:
      case 35:
      case 36:
      case 37:
      case 38:
      case 39:

        $money = floor(($game_user->income - $game_user->expenses) / 10);
        $outcome_reason .=
          '<div class="title">You get ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
      case 45:
      case 46:
      case 47:
      case 48:
      case 49:
        $outcome_reason .=
          '<div class="title">You get 5 actions!</div>';

        $sql = 'update users set actions = actions + 6
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 50:
      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You get 100 influence!</div>';

        $sql = 'update users set experience = experience + 100
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
        $outcome_reason .=
          '<div class="title">You get 1000 Influence!</div>';

        $sql = 'update users set experience = experience + 1000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form

      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">Ono!</div>
          <div class="subtitle">Your costume ripped and fell off</div>';

        $sql = 'update users set username = real_username
          where id = %d';
        $result = db_query($sql, $game_user->id);
        $can_do_again = FALSE;

        return TRUE;
        break; // just for form

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
      case 75:
      case 76:
      case 77:
      case 78:
      case 79:
      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $outcome_reason = '<div class="title">Unfortunately</div>
          <div class="subtitle">This home is all out of treats!</div>
          <div class="subtitle">Try another</div>';

        return TRUE;
        break; // just for form

/*
      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $outcome_reason .= '<div class="title">A guy comes out</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-26.png"/></div>
          <div class="subtitle">and hands you a ticket</div>
          <div class="subtitle">&quot;There\'s a party tonight
          in Fountain Park,&quot; he says</div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = 26 and fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already a ticket

          $sql = 'update equipment_ownership set quantity = 1
            where fkey_equipment_id = 26 and fkey_users_id = %d;';
          $result = db_query($sql, $game_user->id);

          $outcome_reason .= '<div class="title">You already have one, but you
            thank him anyway</div>';

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_users_id, fkey_equipment_id, quantity) values
            (%d, 26, 1);';
          $result = db_query($sql, $game_user->id);

          $outcome_reason .= '<div class="title">You take it.&nbsp; This
            might be fun!</div>';

        }

        return TRUE;
        break; // just for form
*/
      default:
        $outcome_reason .=
          '<div class="title">You get 10 luck!</div>';

        $sql = 'update users set luck = luck + 10
          where id = %d';
        $result = db_query($sql, $game_user->id);

        $candy_used = TRUE;
        break; // just for form


    }

    if ($candy_used) {

      $sql = 'update neighborhoods set special_int = special_int - 1
        where id = %d;';
      $result = db_query($sql, $game_user->fkey_neighborhoods_id);

    }

    return TRUE;

  }


  function _stlouis_action_check_out_all_the_people_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $outcome_reason = '<div class="title">Sorry</div>
      <div class="subtitle">They\'ve all gone home</div>';
    return FALSE;

// real function below
    if ($game_user->username == $game_user->real_username) { // no costume!

      $outcome_reason = '<div class="title">Huh?</div>
        <div class="subtitle">You can\'t party without a costume!</div>';
      return FALSE;

    }

    $outcome_reason = '<div class="title">with the Jack-O\'-Lanterns</div>';

    $outcome_reason .= '<div class="title"><img
      src="/sites/default/files/images/actions/stlouis-53.png"/></div>';

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = 27 and fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $data = db_fetch_object($result);

    if (empty($data)) { // none yet -- give one

      $sql = 'insert into equipment_ownership
        (fkey_users_id, fkey_equipment_id, quantity) values
        (%d, 27, 1);';
      $result = db_query($sql, $game_user->id);

      $outcome_reason .= '<div class="subtitle">The ladies tell you that you
        get the Jack-O\'-Lanterns by talking to people at the party</div>
        <div class="subtitle"><img
          src="/sites/default/files/images/equipment/stlouis-27.png"/></div>
        <div class="subtitle">You can only get up to 10
          by talking with them</div>
        <div class="title">So go meet someone new!</div>';

    } else if ($data->quantity >= 10) { // already have ten

      $outcome_reason .= '<div class="subtitle">The ladies tell you that you
        get the Jack-O\'-Lanterns by talking to people at the party</div>
        <div class="subtitle"><img
          src="/sites/default/files/images/equipment/stlouis-27.png"/></div>
        <div class="subtitle">But you already have ' . $data->quantity .
        '!</div><div class="title">So go meet someone new!</div>';

      $can_do_again = FALSE;

    } else { // need one more

      $outcome_reason .= '<div class="subtitle">The ladies tell you that you
        get the Jack-O\'-Lanterns by talking to people at the party</div>
        <div class="subtitle"><img
          src="/sites/default/files/images/equipment/stlouis-27.png"/></div>
        <div class="subtitle">They give you one more</div>
        <div class="title">Then go meet someone new!</div>';

      $sql = 'update equipment_ownership set quantity = quantity + 1
        where fkey_equipment_id = 27 and fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);

    }


    return TRUE;

  }

/*
  function _stlouis_action_meet_someone_new_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

// not in Fountain Park?  act as normal
    if ($game_user->fkey_neighborhoods_id != 80) return TRUE;

// otherwise, we are in Fountain Park
    if ($game_user->username == $game_user->real_username) { // no costume!

      $outcome_reason = '<div class="title">Huh?</div>
        <div class="subtitle">You\'re not getting a Jack-O\'-Lantern
          without a costume!</div>';
      return FALSE;

    }

    $outcome_reason .= '<div class="title">But...</div>
      <div class="subtitle">To get the Jack-O\'-Lantern,<br/>you must post
        on his or her wall!</div>';
    return TRUE;

  }
*/

  function _stlouis_action_hunt_for_zombies_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $sql = 'select * from users where meta = "zombie"
      order by abs(level - %d) ASC
      limit 10;';
    $result = db_query($sql, $game_user->level);
    $data = array();
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      $sql = 'insert into user_messages (fkey_users_from_id,
        fkey_users_to_id, message) values (%d, %d, "%s");';
      $result = db_query($sql, $item->id, $game_user->id,
        $item->username . ' ' . $item->id . ' loves politicsss...');

    }

    $num = count($data);

    $outcome_reason .= '<div class="subtitle">' . $num .
      ' Dead Presidents have posted on your wall</div>';
    return TRUE;

  }


  function _stlouis_action_hunt_for_zombies_in_your_neighborhood_function
    (&$outcome_reason, $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $sql = 'select * from users
      where meta = "zombie" and fkey_neighborhoods_id = %d
      order by abs(level - %d) ASC
      limit 5;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id, $game_user->level);
    $data = array();
    while ($item = db_fetch_object($result)) $data[] = $item;

    foreach ($data as $item) {

      $sql = 'insert into user_messages (fkey_users_from_id,
        fkey_users_to_id, message) values (%d, %d, "%s");';
      $result = db_query($sql, $item->id, $game_user->id,
        $item->username . ' ' . $item->id . ' loves politicsss...');

    }

    $num = count($data);

    $outcome_reason .= '<div class="subtitle">' . $num .
      ' Dead Presidents have posted on your wall</div>';
    return TRUE;

  }


  function _stlouis_action_sing_christmas_carols_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
//    $can_do_again = FALSE;

    $sql = 'select name, special_int from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result); // limited to 1 in db;

    if ($item->special_int < 0) { // Grinch!

      if ($item->special_int == -1) {

        $outcome_reason = '<div class="title">Oh no!</div>
          <div class="subtitle">The Grinch has visited ' .
          $item->name . '<br/>and taken all the presents!</div>
          <div class="subsubtitle">Carol ' . abs($item->special_int) .
          ' more time to bring back the Christmas spirit';

        $sql = 'update neighborhoods
          set special_int = 50
          where id = %d;';
        $result = db_query($sql, $game_user->fkey_neighborhoods_id);

      } else {

        $outcome_reason = '<div class="title">Oh no!</div>
          <div class="subtitle">The Grinch has visited ' .
          $item->name . '<br/>and taken all the presents!</div>
          <div class="subsubtitle">Carol ' . abs($item->special_int) .
          ' more times to bring back the Christmas spirit';

        $sql = 'update neighborhoods
          set special_int = special_int + 1
          where id = %d;';
        $result = db_query($sql, $game_user->fkey_neighborhoods_id);

      }

//      mail('joseph@cheek.com', 'The grinch has visited ' . $item->name,
//        abs($item->special_int) . ' more carols until it has been restored!');

      return TRUE;

    }

    if ($item->special_int == 0) { // tired of carols

      $outcome_reason = '<div class="subtitle">The people of ' .
        $item->name . '</div>
        <div class="subtitle">are tired of carolers
        &mdash; try another neighborhood</div>';
/*
      $sql = 'select count(name) as count
        from neighborhoods where special_int < 1;';
      $result = db_query($sql);
      $item2 = db_fetch_object($result); // limited to 1 in db;

      mail('joseph@cheek.com', $item2->count .
        ' neighborhoods tired of caroling',
        'hopefully ' . $item->name . ' will get more soon!');
*/
      return FALSE;

    }

    switch(mt_rand(0, 140)) {

/*
      case 0:
        $outcome_reason .=
          '<div class="title">You got 1 luck!</div>';

        $sql = 'update users set luck = luck + 1
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 1:
        $outcome_reason .=
          '<div class="title">You got 3 luck!</div>';

        $sql = 'update users set luck = luck + 3
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 2:
        $outcome_reason .=
          '<div class="title">You got 5 luck!</div>';

        $sql = 'update users set luck = luck + 5
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;
*/
      case 3:
        $outcome_reason .=
          '<div class="title">You got 10 luck!</div>';

        $sql = 'update users set luck = luck + 10
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 4:
      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
        $outcome_reason = '<div class="title"><img
          src="/sites/default/files/images/stlouis-angry-dog-snow.png"></div>
          <div class="title">BARK BARK BARK!</div>
          <div class="subtitle">A scary dog chases you away</div>';

        return TRUE;

      case 10:
      case 11:
      case 12:
      case 13:
      case 14:
      case 15:
      case 16:
      case 17:
      case 18:
      case 19:
        $outcome_reason = '<div class="title">Sadly</div>
          <div class="subtitle">No one is home 8-(</div>';

        return TRUE;

      case 20:
      case 21:
      case 22:
      case 23:
      case 24:
      case 25:
      case 26:
      case 27:
      case 28:
      case 29:
        $outcome_reason .=
          '<div class="title">You got your actions refilled!</div>';

        $sql = 'update users set actions = actions_max + 1
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 30:
      case 31:
      case 32:
      case 33:
      case 34:
      case 35:
      case 36:
      case 37:
      case 38:
      case 39:
        $outcome_reason .=
          '<div class="title">You got your energy refilled!</div>';

        $sql = 'update users set energy = energy_max
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
        $money = floor(($game_user->income - $game_user->expenses) / 8);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 45:
      case 46:
      case 47:
        $money = floor(($game_user->income - $game_user->expenses) / 4);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 48:
      case 49:
        $money = floor(($game_user->income - $game_user->expenses) / 2);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 50:
        $money = floor(($game_user->income - $game_user->expenses));
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You got 5 actions!</div>';

        $sql = 'update users set actions = actions + 6
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">You got 100 influence!</div>';

        $sql = 'update users set experience = experience + 100
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
        $outcome_reason .=
          '<div class="title">You got 500 Influence!</div>';

        $sql = 'update users set experience = experience + 500
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 75:
      case 76:
      case 77:
      case 78:
      case 79:
        $outcome_reason .=
          '<div class="title">You got 1000 Influence!</div>';

        $sql = 'update users set experience = experience + 1000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $outcome_reason .= '<div class="title">A guy comes out</div>
          <div class="subtitle">and hands you a gingerbread cookie (+10 End)</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-29.png"/></div>
          <div class="subtitle">&quot;You must be hungry!&quot; he says</div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = 29 and fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already a cookie

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = 29 and fkey_users_id = %d;';
          $result = db_query($sql, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_users_id, fkey_equipment_id, quantity) values
            (%d, 29, 1);';
          $result = db_query($sql, $game_user->id);

        }

        break;

      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $outcome_reason .= '<div class="title">A lady comes out</div>
          <div class="subtitle">and hands you a mug of hot apple cider
            (+10 Elo)</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-30.png"/></div>
          <div class="subtitle">&quot;You must be cold!&quot; she says</div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = 30 and fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already a mug

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = 30 and fkey_users_id = %d;';
          $result = db_query($sql, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_users_id, fkey_equipment_id, quantity) values
            (%d, 30, 1);';
          $result = db_query($sql, $game_user->id);

        }

        break;

      case 100:
      case 101:
      case 102:
      case 103:
      case 104:
      case 105:
      case 106:
      case 107:
      case 108:
      case 109:
        $outcome_reason .= '<div class="title">A kid comes out</div>
          <div class="subtitle">and hands you a candy cane
            (+10 Init)</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-31.png"/></div>
          <div class="subtitle">&quot;You must be tired!&quot; he says</div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = 31 and fkey_users_id = %d;';
        $result = db_query($sql, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already a candy cane

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = 31 and fkey_users_id = %d;';
          $result = db_query($sql, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_users_id, fkey_equipment_id, quantity) values
            (%d, 31, 1);';
          $result = db_query($sql, $game_user->id);

        }

        break;

      default:
        $outcome_reason = '<div class="title">Beaming with happiness</div>
          <div class="subtitle">the family seems to enjoy your vocals</div>';

    }

    $sql = 'update neighborhoods set special_int = special_int - 1
      where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);

    return TRUE;

  }


  function _stlouis_action_throw_a_snowball_function(&$outcome_reason,
    $target) {

    global $game, $phone_id;

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $outcome_reason .= '<div class="action-effect">' .
      t('Poof!') . '</div>';

    $sql = 'update neighborhoods set special_int = special_int - 3
      where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);

    return TRUE;

  }


  function _celestial_glory_action_give_a_gift_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $arg2 = check_plain(arg(2));
    $action_id = arg(3);

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();

    $outcome_reason .= '<div class="action-effect">' .
      t('Your gift is given back to you, and more') . '<br/>' .
      t('(You get 5 extra Action)') . '</div>';

    $sql = 'update users set actions = actions + 6
      where id = %d';
    $result = db_query($sql, $game_user->id);

// save as major action

    $set_value = '_' . arg(0) . '_set_value';
    $set_value($game_user->id, 'next_major_action', time() + 600);

    $can_do_again = FALSE;

    return TRUE;

  }


  function _stlouis_action_spend_some_funds_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
firep($target);
    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $action_id = arg(3);
//    $can_do_again = FALSE;

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];

    $sql = 'SELECT count(*) as count FROM `challenge_history`
      WHERE fkey_from_users_id = %d
      and fkey_to_users_id = %d
      and type = "gift"
      and timestamp like "%s";';
    $result = db_query($sql, $game_user->id, $target_id, date('Y-m-d%'));
    $item = db_fetch_object($result);

    if ($item->count >= 6) { // can only do it six times per day

      $outcome_reason = '<div class="action-effect">' . $target_name .
        ' has received too many funds today.&nbsp; Please try again another
        day.';

      return FALSE;

    }

    $sql = 'select count(id) as count from users
      where fkey_neighborhoods_id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $item = db_fetch_object($result);

    $money = floor(10000 / $item->count);

    $sql = 'update users set money = money + %d where id = %d;';
    $result = db_query($sql, $money, $target_id);
    $outcome_reason .= '<div class="action-effect">' . $target_name . '\'s ' .
      $game_user->values . ' is increased by ' . $money . ' (' .
      $item->count . ' players here)</div>';

    $sql = 'insert into challenge_history
      (type, fkey_from_users_id, fkey_to_users_id, fkey_neighborhoods_id,
      fkey_elected_positions_id, won, desc_short, desc_long) values
      ("gift", %d, %d, %d, %d, %d, "%s", "%s");';
    $result = db_query($sql, $game_user->id, $target_id,
      $game_user->fkey_neighborhoods_id, $target->ep_id, 0,
      "$game_user->username spent some funds for $target_name.",
      "$game_user->username spent $money $game_user->values for " .
      $target_name . '.');

    return TRUE;

  }


  function _stlouis_action_tag_a_player_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $arg2 = check_plain(arg(2));
    $action_id = arg(3);
    $can_do_again = FALSE;

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];

// in a taggable hood?
/* nowhere is safe
    if ($game_user->fkey_neighborhoods_id == 37 ||
      $game_user->fkey_neighborhoods_id > 79) {

      $outcome_reason = '<div class="title">No tagging here</div>
        <div class="subtitle">Try another neighborhood</div>';

      return FALSE;

    } // in a taggable hood
*/
// out of ammo?

    $sql = 'select quantity from equipment_ownership
      where fkey_users_id = %d and fkey_equipment_id = 40;';
    $result = db_query($sql, $game_user->id);
    $item = db_fetch_object($result);

    if ($item->quantity <= 0) {

      $outcome_reason = '<div class="title">No Ammunition!</div>
        <div class="subtitle">Go to Midtown to get some more</div>';

      return FALSE;

    } // no ammo!

    if ($target_id == '') { // no target specified

      $data = array();
      $sql = 'SELECT users.username, users.id,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        WHERE fkey_neighborhoods_id = %d
        AND users.fkey_values_id <> %d
        AND users.username <> ""
        AND users.meta <> "frozen"
        AND users.last_access > "2013-02-01"
        ORDER BY RAND() LIMIT 20;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id);
      while ($user = db_fetch_object($result)) $data[] = $user;

      $outcome_reason = '<div class="title">Whom Would You Like to Tag?</div>';

      foreach ($data as $user) {

        $outcome_reason .= <<< EOF
<div class="subtitle"><a href=
"/$game/actions_do/$arg2/$action_id?target=$user->id">$user->username</a></div>
EOF;

      }

      return FALSE;

    } // show list of players to tag


// camouflage?

    $sql = 'select quantity from equipment_ownership
      where fkey_users_id = %d and fkey_equipment_id = 42;';
    $result = db_query($sql, $target_id);
    $item = db_fetch_object($result);

    if ($item->quantity > 0) {

      $sql = 'update equipment_ownership set quantity = quantity - 1
        where fkey_users_id = %d and fkey_equipment_id = 42;';
      $result = db_query($sql, $target_id);

      if (mt_rand(1, 3) < 3) { // miss

        $outcome_reason = '<div class="subsubtitle">You had ' . $target_name .
          ' in your sights, but lost him or her in the camouflage.</div>';

        return FALSE;

      } // camouflage caused miss

    } // any camouflage?


// tag the player!

// create entry for tagger, taggee, if needed

    $sql = 'select id from event_points where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $row = db_fetch_object($result);

    if (empty($row)) {

      $sql = 'insert into event_points set fkey_users_id = %d;';
      $result = db_query($sql, $target_id);

    }

    $sql = 'select * from event_points where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $row = db_fetch_object($result);

    if (empty($row)) {

      $sql = 'insert into event_points set fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);

    }

// mark as frozen, reset tags_con and heals_con
    $sql = 'update users set meta = "frozen" where id = %d;';
    $result = db_query($sql, $target_id);

    $sql = 'update event_points set tags_con = 0, heals_con = 0
      where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);

// update points
    $tags_con = $row->tags_con + 1;
    $points = $row->points + min($tags_con, 20);

    $sql = 'update event_points set tags_con = %d, points = %d
      where fkey_users_id = %d;';
    $result = db_query($sql, $tags_con, $points, $game_user->id);

    $outcome_reason .= '<div class="subsubtitle">You have ' . $tags_con .
      ' consecutive tag(s) and ' . $points . ' point(s)</div>';

// use up ammo
    $sql = 'update equipment_ownership set quantity = quantity - 1
      where fkey_users_id = %d and fkey_equipment_id = 40;';
    $result = db_query($sql, $game_user->id);

// add tag message
    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('%user tagged you!',
      array('%user' => $game_user->username));
    $result = db_query($sql, $game_user->id, $target_id, $message);

    return TRUE;

  }


  function _stlouis_action_unfreeze_a_player_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;
    $fetch_user = '_' . arg(0) . '_fetch_user';

    $game_user = $fetch_user();
    include(drupal_get_path('module', $game) . '/game_defs.inc');
    $arg2 = check_plain(arg(2));
    $action_id = arg(3);
    $can_do_again = FALSE;

    $target_name = $target->ep_name . ' ' . $target->username;
    $target_id = $_GET['target'];
/*
// in a taggable hood?

    if ($game_user->fkey_neighborhoods_id == 37 ||
      $game_user->fkey_neighborhoods_id > 79) {

      $outcome_reason = '<div class="title">No unfreezing here</div>
        <div class="subtitle">Try another neighborhood</div>';

      return FALSE;

    } // in a taggable hood
*/

// no target specified
    if ($target_id == '') {

      $data = array();
      $sql = 'SELECT users.username, users.id,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name
        FROM users
        LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        WHERE fkey_neighborhoods_id = %d
        AND users.fkey_values_id = %d
        AND users.username <> ""
        AND users.meta = "frozen"
        ORDER BY RAND() LIMIT 20;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id);
      while ($user = db_fetch_object($result)) $data[] = $user;

      $outcome_reason = '<div class="title">Frozen Players
        in your Neighborhood</div>';

      foreach ($data as $user) {

        $outcome_reason .= <<< EOF
<div class="subtitle"><a href=
"/$game/actions_do/$arg2/$action_id?target=$user->id">$user->username</a></div>
EOF;

      }

      if (empty($data)) $outcome_reason .= '<div class="subtitle">
        None</div>';

      $data = array();
      $sql = 'SELECT users.username, users.id,
        clan_members.is_clan_leader, clans.acronym AS clan_acronym,
        NULL as ep_name,
        neighborhoods.name as hood, neighborhoods.id as hood_id
        FROM users
        LEFT OUTER JOIN clan_members ON clan_members.fkey_users_id = users.id
        LEFT OUTER JOIN clans ON clan_members.fkey_clans_id = clans.id
        LEFT JOIN neighborhoods
          ON neighborhoods.id = users.fkey_neighborhoods_id

        WHERE fkey_neighborhoods_id <> %d
        AND users.fkey_values_id = %d
        AND users.username <> ""
        AND users.meta = "frozen"
        ORDER BY RAND() LIMIT 20;';

      $result = db_query($sql, $game_user->fkey_neighborhoods_id,
        $game_user->fkey_values_id);
      while ($user = db_fetch_object($result)) $data[] = $user;

      $outcome_reason .= '<div class="title">Frozen Players
        in other Neighborhoods</div>';

      foreach ($data as $user) {

        $outcome_reason .= <<< EOF
<div class="subtitle"><a href=
"/$game/actions_do/$arg2/$action_id?target=$user->id">$user->username</a>
<a href="/$game/move/$arg2/$user->hood_id">($user->hood)</a></div>
EOF;

      }

      if (empty($data)) $outcome_reason .= '<div class="subtitle">
        None</div>';

      $outcome_reason .= '<div class="try-an-election-wrapper"><div
        class="try-an-election"><a
        href="/' . $game . '/actions_do/' . $arg2 . '/' . $action_id .
        '">Check the list again</a></div></div>';

      return FALSE;

    } // show list of players to tag


// not in same hood

    if ($game_user->fkey_neighborhoods_id != $target->fkey_neighborhoods_id) {

      $outcome_reason = '<div class="title">Not in same neighborhood!</div>
        <div class="subtitle">You must be in the same hood to unfreeze</div>';

      $outcome_reason .= '<div class="try-an-election-wrapper"><div
        class="try-an-election"><a
        href="/' . $game . '/actions_do/' . $arg2 . '/' . $action_id .
        '">Check the list again</a></div></div>';

      return FALSE;

    }


// unfreeze the player

// create entry for tagger, taggee, if needed

    $sql = 'select id from event_points where fkey_users_id = %d;';
    $result = db_query($sql, $target_id);
    $row = db_fetch_object($result);

    if (empty($row)) {

      $sql = 'insert into event_points set fkey_users_id = %d;';
      $result = db_query($sql, $target_id);

    }

    $sql = 'select * from event_points where fkey_users_id = %d;';
    $result = db_query($sql, $game_user->id);
    $row = db_fetch_object($result);

    if (empty($row)) {

      $sql = 'insert into event_points set fkey_users_id = %d;';
      $result = db_query($sql, $game_user->id);

    }

// mark as unfrozen
    $sql = 'update users set meta = "" where id = %d;';
    $result = db_query($sql, $target_id);

// update points
    $heals_con = $row->heals_con + 1;
    $points = $row->points + min($heals_con, 20) + 1;

    $sql = 'update event_points set heals_con = %d, points = %d
      where fkey_users_id = %d;';
    $result = db_query($sql, $heals_con, $points, $game_user->id);

    $outcome_reason .= '<div class="subsubtitle">You have ' . $heals_con .
      ' consecutive heal(s) and ' . $points . ' point(s)</div>';

// add unfreeze message
    $sql = 'insert into challenge_messages
      (fkey_users_from_id, fkey_users_to_id, message)
      values (%d, %d, "%s");';
    $message = t('%user unfroze you!',
      array('%user' => $game_user->username));
    $result = db_query($sql, $game_user->id, $target_id, $message);

   return TRUE;

  }


  function _stlouis_action_open_a_small_present_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
//    $can_do_again = FALSE;


    switch(mt_rand(0, 142)) {

      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
        $money = floor(($game_user->income - $game_user->expenses) / 8);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You got 5 actions!</div>';

        $sql = 'update users set actions = actions + 6
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">You got 100 influence!</div>';

        $sql = 'update users set experience = experience + 100
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
        $outcome_reason .=
          '<div class="title">You got 500 Influence!</div>';

        $sql = 'update users set experience = experience + 500
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $land = 1;

        $outcome_reason .= '<div class="title">You got a deed to
          a Lemonade Stand!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $land = 2;

        $outcome_reason .= '<div class="title">You got a deed to
          a Paper Route!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 100:
      case 101:
      case 102:
      case 103:
      case 104:
      case 105:
      case 106:
      case 107:
      case 108:
      case 109:
        $land = 3;

        $outcome_reason .= '<div class="title">You got a deed to an
          Online Auction Business!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 110:
      case 111:
      case 112:
      case 113:
      case 114:
      case 115:
      case 116:
      case 117:
      case 118:
      case 119:
        $equipment = 3;

        $outcome_reason .= '<div class="title">You got a Dictionary!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_equipment_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 120:
      case 121:
      case 122:
      case 123:
      case 124:
      case 125:
      case 126:
      case 127:
      case 128:
      case 129:
        $equipment = 10;

        $outcome_reason .= '<div class="title">You got a Political Flyer!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_equipment_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 130:
      case 131:
      case 132:
      case 133:
      case 134:
      case 135:
      case 136:
      case 137:
      case 138:
      case 139:
        $equipment = 11;

        $outcome_reason .= '<div class="title">You got a Political Button!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 1
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_eqiupment_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 140:
        $land = 10;

        $outcome_reason .= '<div class="title">You got a deed to a
          Bank!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 141:
        $land = 11;

        $outcome_reason .= '<div class="title">You got a deed to a
          Luxury Car Importer!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 142:
        $land = 12;

        $outcome_reason .= '<div class="title">You got a deed to a
          Manufacturing Plant!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      default:
        $money = $game_user->level * 100;
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

    }

    return TRUE;

  }


  function _stlouis_action_open_a_medium_present_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
//    $can_do_again = FALSE;


    switch(mt_rand(30, 145)) {

/*
      case 30:
      case 31:
      case 32:
      case 33:
      case 34:
      case 35:
      case 36:
      case 37:
      case 38:
      case 39:
        $outcome_reason .=
          '<div class="title">You got your energy refilled!</div>';

        $sql = 'update users set energy = energy_max
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;
*/
      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
      case 45:
      case 46:
      case 47:
      case 48:
      case 49:
        $money = floor(($game_user->income - $game_user->expenses) / 2);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 50:
      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You got 20 actions!</div>';

        $sql = 'update users set actions = actions + 21
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">You got 1000 influence!</div>';

        $sql = 'update users set experience = experience + 1000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
      case 75:
      case 76:
      case 77:
      case 78:
      case 79:
        $outcome_reason .=
          '<div class="title">You got 2500 Influence!</div>';

        $sql = 'update users set experience = experience + 2500
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $land = 4;

        $outcome_reason .= '<div class="title">You got a deed to
          a Pool Hall!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $land = 5;

        $outcome_reason .= '<div class="title">You got a deed to
          a Woodworking Shop!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 100:
      case 101:
      case 102:
      case 103:
      case 104:
      case 105:
      case 106:
      case 107:
      case 108:
      case 109:
        $land = 6;

        $outcome_reason .= '<div class="title">You got a deed to a
          Restaurant!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 110:
      case 111:
      case 112:
      case 113:
      case 114:
      case 115:
      case 116:
      case 117:
      case 118:
      case 119:
        $equipment = 4;

        $outcome_reason .= '<div class="title">You got 10 Thesauruses!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 10
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_equipment_id, fkey_users_id, quantity) values
            (%d, %d, 10);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 120:
      case 121:
      case 122:
      case 123:
      case 124:
      case 125:
      case 126:
      case 127:
      case 128:
      case 129:
        $staff = 1;

        $outcome_reason .= '<div class="title">You found 10
          Supportive Family Members!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 10
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 10);';
          $result = db_query($sql, $staff, $game_user->id);

        }

        break;

      case 130:
      case 131:
      case 132:
      case 133:
      case 134:
      case 135:
      case 136:
      case 137:
      case 138:
      case 139:
        $staff = 6;

        $outcome_reason .= '<div class="title">You found 10
          Supportive Friends!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 10
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 10);';
          $result = db_query($sql, $staff, $game_user->id);

        }

        break;

      case 140:
      case 141:
        $land = 10;

        $outcome_reason .= '<div class="title">You got a deed to a
          Bank!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 142:
      case 143:
        $land = 11;

        $outcome_reason .= '<div class="title">You got a deed to a
          Luxury Car Importer!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 144:
      case 145:
        $land = 12;

        $outcome_reason .= '<div class="title">You got a deed to a
          Manufacturing Plant!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      default:
        $money = $game_user->level * 1000;
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

    }

    return TRUE;

  }


  function _stlouis_action_open_a_large_present_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
//    $can_do_again = FALSE;


    switch(mt_rand(30, 151)) {

      case 30:
      case 31:
      case 32:
      case 33:
      case 34:
      case 35:
      case 36:
      case 37:
      case 38:
      case 39:
        $outcome_reason .=
          '<div class="title">You got your energy refilled!</div>';

        $sql = 'update users set energy = energy_max
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
      case 45:
      case 46:
      case 47:
      case 48:
      case 49:
        $money = floor(($game_user->income - $game_user->expenses) * 4);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 50:
      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You got 50 actions!</div>';

        $sql = 'update users set actions = actions + 50
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">You got 5000 Influence!</div>';

        $sql = 'update users set experience = experience + 5000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
      case 75:
      case 76:
      case 77:
      case 78:
      case 79:
        $outcome_reason .=
          '<div class="title">You got 10000 Influence!</div>';

        $sql = 'update users set experience = experience + 10000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $land = 7;

        $outcome_reason .= '<div class="title">You got a deed to
          a Used Car Dealership!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $land = 8;

        $outcome_reason .= '<div class="title">You got a deed to
          a Department Store!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 100:
      case 101:
      case 102:
      case 103:
      case 104:
      case 105:
      case 106:
      case 107:
      case 108:
      case 109:
        $land = 9;

        $outcome_reason .= '<div class="title">You got a deed to a
          Luxury Home Builder!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 110:
      case 111:
      case 112:
      case 113:
      case 114:
      case 115:
      case 116:
      case 117:
      case 118:
      case 119:
        $equipment = 14;

        $outcome_reason .= '<div class="title">You got 100
          Toastmasters Memberships!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 100
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_equipment_id, fkey_users_id, quantity) values
            (%d, %d, 100);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 120:
      case 121:
      case 122:
      case 123:
      case 124:
      case 125:
      case 126:
      case 127:
      case 128:
      case 129:
        $staff = 2;

        $outcome_reason .= '<div class="title">You found a class of 100
          Political Science Interns!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 100
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 100);';
          $result = db_query($sql, $game_user->id, $staff);

        }

        break;

      case 130:
      case 131:
      case 132:
      case 133:
      case 134:
      case 135:
      case 136:
      case 137:
      case 138:
      case 139:
        $staff = 7;

        $outcome_reason .= '<div class="title">You found 100
          Foreign War Veterans!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 100
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 100);';
          $result = db_query($sql, $staff, $game_user->id);

        }

        break;

      case 140:
      case 141:
      case 142:
      case 143:
        $land = 10;

        $outcome_reason .= '<div class="title">You got a deed to a
          Bank!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 144:
      case 145:
      case 146:
      case 147:
        $land = 11;

        $outcome_reason .= '<div class="title">You got a deed to a
          Luxury Car Importer!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 148:
      case 149:
      case 150:
      case 151:
        $land = 12;

        $outcome_reason .= '<div class="title">You got a deed to a
          Manufacturing Plant!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already own that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      default:
        $money = $game_user->level * 100000;
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

    }

    return TRUE;

  }


  function _stlouis_action_open_a_giant_present_function(&$outcome_reason,
    $target, &$can_do_again) {

    $fetch_user = '_' . arg(0) . '_fetch_user';
    $game_user = $fetch_user();
//    $can_do_again = FALSE;


    switch(mt_rand(30, 139)) {

      case 30:
      case 31:
      case 32:
      case 33:
      case 34:
      case 35:
      case 36:
      case 37:
      case 38:
      case 39:
        $outcome_reason .=
          '<div class="title">You got your energy refilled!</div>';

        $sql = 'update users set energy = energy_max
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 40:
      case 41:
      case 42:
      case 43:
      case 44:
      case 45:
      case 46:
      case 47:
      case 48:
      case 49:
        $money = floor(($game_user->income - $game_user->expenses) * 24);
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

      case 50:
      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
      case 56:
      case 57:
      case 58:
      case 59:
        $outcome_reason .=
          '<div class="title">You got 100 actions!</div>';

        $sql = 'update users set actions = actions + 100
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 60:
      case 61:
      case 62:
      case 63:
      case 64:
      case 65:
      case 66:
      case 67:
      case 68:
      case 69:
        $outcome_reason .=
          '<div class="title">You got 20000 Influence!</div>';

        $sql = 'update users set experience = experience + 20000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 70:
      case 71:
      case 72:
      case 73:
      case 74:
      case 75:
      case 76:
      case 77:
      case 78:
      case 79:
        $outcome_reason .=
          '<div class="title">You got 50000 Influence!</div>';

        $sql = 'update users set experience = experience + 50000
          where id = %d';
        $result = db_query($sql, $game_user->id);

        break;

      case 80:
      case 81:
      case 82:
      case 83:
      case 84:
      case 85:
      case 86:
      case 87:
      case 88:
      case 89:
        $land = 10;

        $outcome_reason .= '<div class="title">You got a deed to
          a Bank!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 90:
      case 91:
      case 92:
      case 93:
      case 94:
      case 95:
      case 96:
      case 97:
      case 98:
      case 99:
        $land = 11;

        $outcome_reason .= '<div class="title">You got a deed to
          a Luxury Car Importer!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 100:
      case 101:
      case 102:
      case 103:
      case 104:
      case 105:
      case 106:
      case 107:
      case 108:
      case 109:
        $land = 12;

        $outcome_reason .= '<div class="title">You got a deed to a
          Manufacturing Plant!</div>
          <div class="title"><img
            src="/sites/default/files/images/land/stlouis-' . $land .
            '.png"/></div>';

        $sql = 'select quantity from land_ownership
          where fkey_land_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $land, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that business

          $sql = 'update land_ownership set quantity = quantity + 1
            where fkey_land_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $land, $game_user->id);

        } else {

          $sql = 'insert into land_ownership
            (fkey_land_id, fkey_users_id, quantity) values
            (%d, %d, 1);';
          $result = db_query($sql, $land, $game_user->id);

        }

        break;

      case 110:
      case 111:
      case 112:
      case 113:
      case 114:
      case 115:
      case 116:
      case 117:
      case 118:
      case 119:
        $equipment = 20;

        $outcome_reason .= '<div class="title">You got 250
          iPads!</div>
          <div class="title"><img
            src="/sites/default/files/images/equipment/stlouis-' . $equipment .
            '.png"/></div>';

        $sql = 'select quantity from equipment_ownership
          where fkey_equipment_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $equipment, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that equipment

          $sql = 'update equipment_ownership set quantity = quantity + 250
            where fkey_equipment_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $equipment, $game_user->id);

        } else {

          $sql = 'insert into equipment_ownership
            (fkey_equipment_id, fkey_users_id, quantity) values
            (%d, %d, 250);';
          $result = db_query($sql, $equipment, $game_user->id);

        }

        break;

      case 120:
      case 121:
      case 122:
      case 123:
      case 124:
      case 125:
      case 126:
      case 127:
      case 128:
      case 129:
        $staff = 22;

        $outcome_reason .= '<div class="title">You found a forest full of
          250 Environmental Activists!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 250
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 250);';
          $result = db_query($sql, $staff, $game_user->id);

        }

        break;

      case 130:
      case 131:
      case 132:
      case 133:
      case 134:
      case 135:
      case 136:
      case 137:
      case 138:
      case 139:
        $staff = 23;

        $outcome_reason .= '<div class="title">You found a classful of 250
          Press Attachés!</div>
          <div class="title"><img
            src="/sites/default/files/images/staff/stlouis-' . $staff .
            '.png"/></div>';

        $sql = 'select quantity from staff_ownership
          where fkey_staff_id = %d and fkey_users_id = %d;';
        $result = db_query($sql, $staff, $game_user->id);
        $data = db_fetch_object($result);

        if (!empty($data)) { // already have that staff member

          $sql = 'update staff_ownership set quantity = quantity + 250
            where fkey_staff_id = %d and fkey_users_id = %d;';
          $result = db_query($sql, $staff, $game_user->id);

        } else {

          $sql = 'insert into staff_ownership
            (fkey_staff_id, fkey_users_id, quantity) values
            (%d, %d, 250);';
          $result = db_query($sql, $staff, $game_user->id);

        }

        break;

      default:
        $money = $game_user->level * 1000000;
        $outcome_reason .=
          '<div class="title">You got ' . $money . ' ' .
          $game_user->values .'!</div>';

        $sql = 'update users set money = money + %d
          where id = %d';
        $result = db_query($sql, $money, $game_user->id);

        break;

    }

    return TRUE;

  }


  function _stlouis_action_exploit_your_seat_function(&$outcome_reason,
    $target, &$can_do_again) {

    global $game, $phone_id;

    $fetch_user = '_' . $game . '_fetch_user';
    $game_user = $fetch_user();
    $can_do_again = FALSE;
    $get_value = '_' . $game . '_get_value';
    $set_value = '_' . $game . '_set_value';

    $next_major_action_time =
      $get_value($game_user->id, 'next_major_action', 0);

    if ($next_major_action_time > time()) { // egads!  hacking!

      $sql = 'update users set karma = karma - 50 where id = %d;';
      $result = db_query($sql, $game_user->id);

      $outcome_reason = '<div class="title error">--&gt; h4x0r &lt;--</div>';

      return FALSE;

    }

    if (mt_rand(0, 9) == 0) { // time waster

      $outcome_reason .= '<div class="title">But... your time
        was completely wasted</div>
        <div class="title"><img
          src="/sites/default/files/images/stlouis-frustrated-official.png"/>
        </div>
        <div class="subtitle">That\'s 45 minutes that you will never
        get back</div>';

      $set_value($game_user->id, 'next_major_action', time() + 2700);

      return TRUE;

    }


    if ($game_user->level > 100) {
      $equipment = 37;
      $major_action_timer = 900; // secs for major action
    } else if ($game_user->level > 50) {
      $equipment = 36;
      $major_action_timer = 600; // secs for major action
    } else {
      $equipment = 35;
      $major_action_timer = 360; // secs for major action
    }


    $outcome_reason .= '<div class="title">You got a present!</div>
      <div class="title"><img
        src="/sites/default/files/images/equipment/stlouis-' . $equipment .
        '.png"/></div>';

    $sql = 'select quantity from equipment_ownership
      where fkey_equipment_id = %d and fkey_users_id = %d;';
    $result = db_query($sql, $equipment, $game_user->id);
    $data = db_fetch_object($result);

    if (!empty($data)) { // already have that equipment

      $sql = 'update equipment_ownership set quantity = quantity + 1
        where fkey_equipment_id = %d and fkey_users_id = %d;';
      $result = db_query($sql, $equipment, $game_user->id);

    } else {

      $sql = 'insert into equipment_ownership
        (fkey_equipment_id, fkey_users_id, quantity) values
        (%d, %d, 1);';
      $result = db_query($sql, $equipment, $game_user->id);

    }

    $set_value($game_user->id, 'next_major_action',
      time() + $major_action_timer);

    return TRUE;

  }
