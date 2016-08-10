<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $favor_succeeded = TRUE;
  $fvr = $favor;

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

  if ($phone_id != 'abc123') { // check for initiator prerequisites

    if ($favor->actions_cost > $game_user->actions) { // not enough action

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
                '/action?destination=/' . $game . '/favors/' . $arg2 . '">
                Refill your Action (2&nbsp;' . $luck . ')
              </a>
            </div>
          </div>
        </div>';

    } // not enough action

    if ($favor->values_cost > $game_user->money) { // not enough money

      $favor_succeeded = FALSE;

      if (substr($phone_id, 0, 3) == 'ai-')
        $ai_output = 'favor-failed no-money';

      $offer = ($game_user->income - $game_user->expenses) * 5;
      $offer = min($offer, $game_user->level * 1000);
      $offer = max($offer, $game_user->level * 100);

      $outcome_reason = '
        <div class="land failed">
          <div class="title">' .
            t('∆ @value <span>Insufficient</span> ∆',
              array('@value' => $game_user->values)) . '
          </div>
          <div class="try-an-election-wrapper">
            <div  class="try-an-election">
              <a href="/' . $game . '/elders_do_fill/' . $arg2 .
                '/money?destination=/' . $game . '/favors/' . $arg2 . '">' .
                t('Receive @offer @values (1&nbsp;@luck',
                  array(
                    '@offer' => $offer,
                    '@values' => $game_user->values,
                    '@luck' => $luck,
                  )) . ')
              </a>
            </div>
          </div>
        </div>';

    } // not enough money

  } // not abc123


// find the users

  if ($favor->fkey_required_elected_positions_id > 0) {

    $where_ep = 'and elected_positions.id = ' .
      $favor->fkey_required_elected_positions_id;

  } else {

    $where_ep = 'and (elected_positions.id = ' . $game_user->ep_level . ' OR
      elected_positions.id = ' . ($game_user->ep_level - 1) . ')';

  }

  if ($favor->fkey_required_competencies_id > 0 &&
    $favor->required_competencies_level > 0) {

    $count_needed = competency_min_count($favor->required_competencies_level);
    $join_comp = 'left join user_competencies as uc on fkey_users_id = users.id
      and fkey_competencies_id = ' . $favor->fkey_required_competencies_id;
    $where_comp = 'and uc.use_count > ' . $count_needed;

  } else {

    $join_comp = $where_comp = '';

  }

  $sql = 'select users.*,
    `values`.party_title, `values`.party_icon, `values`.name,
    elected_positions.name as ep_name,
    elected_positions.id as ep_level,
    elected_officials.approval_rating,
    elected_positions.energy_bonus as ep_energy_bonus,

    clan_members.is_clan_leader,
    clans.name as clan_name, clans.acronym as clan_acronym,
    clans.id as fkey_clans_id

    FROM `users`

    LEFT JOIN `values` ON users.fkey_values_id = `values`.id

    LEFT OUTER JOIN elected_officials
    ON elected_officials.fkey_users_id = users.id

    LEFT OUTER JOIN elected_positions
    ON elected_positions.id = elected_officials.fkey_elected_positions_id

    LEFT OUTER JOIN clan_members on clan_members.fkey_users_id = users.id

    LEFT OUTER JOIN clans on clan_members.fkey_clans_id = clans.id

    ' . $join_comp . '

    where users.actions > %d
    and users.id <> %d
    and users.fkey_neighborhoods_id = %d
    
    ' . $where_ep . ' ' . $where_comp . '
    order by RAND() limit 40;';
firep($sql);
  $result = db_query($sql, $favor->runner_actions_cost, $game_user->id,
    $game_user->fkey_neighborhoods_id);
  while ($item = db_fetch_object($result)) $data[] = $item;

  if (count($data) == 0) {

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

    $fetch_header($game_user);
    show_elections_menu($game_user);
  
    echo <<< EOF
<div class="title">
  {$fvr}: $favor->name
</div>
$outcome_reason
EOF;

    if (substr($phone_id, 0, 3) == 'ai-')
      echo "<!--\n<ai \"$ai_output " .
      filter_xss($outcome_reason, array()) .
      " \"/>\n-->";

    db_set_active('default');
    return;

  }


// favor looks good!
// deduct action, money here

    $sql = 'update users set actions = actions - %d,
      money = money - %d
      where id = %d;';
    $result = db_query($sql, $favor->actions_cost, $favor->values_cost,
      $game_user->id);

// start the actions clock, if needed
    if ($game_user->actions == $game_user->actions_max) {

      $sql = 'update users set actions_next_gain = "%s" where id = %d;';
      $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
        $game_user->id);

    }

    $game_user = $fetch_user(); // reprocess user object
    $fetch_header($game_user);
    show_elections_menu($game_user);
  
    echo <<< EOF
<div class="title">
  {$fvr}: $favor->name
</div>
<div class="subtitle">
  &laquo; Debited: $favor->actions_cost $actions, $favor->values_cost
    $game_user->values &raquo;
</div>
<div class="land">
  <form action="/$game/favors_selected_2/$arg2/$favor_id">
    <div class="title">
      ◊ Select Desired Runner ◊
    </div>
EOF;

  foreach ($data as $item) {
firep($item);

    if (!empty($item->clan_acronym)) {
      $clan_acronym = "($item->clan_acronym)";
      $clan_link = $item->clan_name;
    } else {
      $clan_acronym = '';
      $clan_link = t('None');
    }

    if ($item->is_clan_leader) {
      $clan_acronym .= '*';
      $clan_link .= " (leader)";
    }

    $hash = substr(md5(date('Y-M-d-H-i') . '-' . $item->id), 0, 8);

    echo <<< EOF
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="player" type="radio" value="$item->id:$hash"
          id="player-$item->id">
          <label for="player-$item->id">
            $item->ep_name <span class="username">$item->username</span>
              $clan_acronym
          </label>
        </input>
      </div>
    </div>
EOF;

  }

  echo <<< EOF
    <div class="title">
      ◊ Select Desired Time Window ◊
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="time" type="radio" value="14400" id="time-14400">
          <label for="time-14400">
            4 Hours (Action Cost: +3)
          </label>
        </input>
      </div>
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="time" type="radio" value="43200" id="time-43200">
          <label for="time-43200">
            12 Hours (Action Cost: +1)
          </label>
        </input>
      </div>
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="time" type="radio" value="86400" id="time-86400"
          checked="checked">
          <label for="time-86400">
            24 Hours
          </label>
        </input>
      </div>
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="time" type="radio" value="259200" id="time-259200">
          <label for="time-259200">
            72 Hours
          </label>
        </input>
      </div>
    </div>
    <div class="title">
      ◊ Select Desired Outcome ◊
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="success" type="radio" value="y" id="success-y"
          checked="checked">
          <label for="success-y">
            Successful Completion
          </label>
        </input>
      </div>
    </div>
    <div class="try-an-election-wrapper">
      <div class="try-an-election">
        <input name="success" type="radio" value="n" id="success-n">
          <label for="success-n">
            Unsuccessful Completion
          </label>
        </input>
      </div>
    </div>
    <div class="title">
      ◊ Proceed ◊
      <input type="image" src="/sites/default/files/images/{$game}_continue.png"
        width=266/>
    </div>
  </form>
</div>
EOF;

  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output " .
    filter_xss($outcome_reason, array()) .
    " \"/>\n-->";

  db_set_active('default');
