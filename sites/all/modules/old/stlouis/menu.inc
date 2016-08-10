<?php
// $Id$


function stlouis_menu() {

  $items['stlouis/home/%'] = array(
    'title' => 'St. Louis Home Page',
    'page callback' => 'stlouis_homepage_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/user/%'] = array(
    'title' => 'St. Louis User Profile Page',
    'page callback' => 'stlouis_userprofile_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/user_favors/%'] = array(
    'title' => 'St. Louis User Favors Page',
    'page callback' => 'stlouis_user_favors_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/favor_perform/%/%'] = array(
    'title' => 'St. Louis User Favors Page',
    'page callback' => 'stlouis_favor_perform_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/user_comp/%'] = array(
    'title' => 'St. Louis User Competencies Page',
    'page callback' => 'stlouis_user_competencies_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/clan_list/%/%'] = array(
    'title' => 'St. Louis Clan List Page',
    'page callback' => 'stlouis_clan_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/clan_list_available/%'] = array(
    'title' => 'St. Louis Available Clan List Page',
    'page callback' => 'stlouis_clan_list_available_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/clan_msg/%/%'] = array(
    'title' => 'St. Louis Clan Messages Page',
    'page callback' => 'stlouis_clan_msg_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/clan_announcements/%/%'] = array(
    'title' => 'St. Louis Clan Announcements Page',
    'page callback' => 'stlouis_clan_announcements_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/class_list/%/%'] = array(
    'title' => 'St. Louis Class List Page',
    'page callback' => 'stlouis_class_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/block_user_toggle/%/%'] = array(
    'title' => 'St. Louis Block User Toggle',
    'page callback' => 'stlouis_block_user_toggle_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/msg_delete/%/%'] = array(
    'title' => 'St. Louis Message Delete Page',
    'page callback' => 'stlouis_msg_delete_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/party_msg/%'] = array(
    'title' => 'St. Louis Party Message Page',
    'page callback' => 'stlouis_party_msg_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/increase_skills/%/%'] = array(
    'title' => 'St. Louis Increase Skills Page',
    'page callback' => 'stlouis_increase_skills_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/quests/%'] = array(
    'title' => 'St. Louis Quests List',
    'page callback' => 'stlouis_quests_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/quests_do/%/%'] = array(
    'title' => 'St. Louis Quests Do',
    'page callback' => 'stlouis_quests_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders/%'] = array(
    'title' => 'St. Louis Elders',
    'page callback' => 'stlouis_elders_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_ask_reset/%'] = array(
    'title' => 'St. Louis Elders Ask Reset',
    'page callback' => 'stlouis_elders_ask_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_do_reset/%'] = array(
    'title' => 'St. Louis Elders Do Reset',
    'page callback' => 'stlouis_elders_do_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_ask_reset_skills/%'] = array(
    'title' => 'St. Louis Elders Ask Reset Skills',
    'page callback' => 'stlouis_elders_ask_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_do_reset_skills/%'] = array(
    'title' => 'St. Louis Elders Do Reset Skills',
    'page callback' => 'stlouis_elders_do_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_ask_purchase/%'] = array(
    'title' => 'St. Louis Elders Ask Purchase',
    'page callback' => 'stlouis_elders_ask_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_new_ask_purchase/%'] = array(
    'title' => 'St. Louis Elders New Ask Purchase',
    'page callback' => 'stlouis_elders_new_ask_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_do_purchase/%'] = array(
    'title' => 'St. Louis Elders Do Purchase',
    'page callback' => 'stlouis_elders_do_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_check_receipt/%'] = array(
    'title' => 'St. Louis Elders Check Receipt',
    'page callback' => 'stlouis_elders_check_receipt_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/google_checkout/%'] = array(
    'title' => 'St. Louis Google Checkout',
    'page callback' => 'stlouis_google_checkout_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_do_fill/%/%'] = array(
    'title' => 'St. Louis Elders Do Fill',
    'page callback' => 'stlouis_elders_do_fill_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_set_password/%'] = array(
    'title' => 'St. Louis Elders Set Password',
    'page callback' => 'stlouis_elders_set_password_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_rename/%'] = array(
    'title' => 'St. Louis Elders Rename',
    'page callback' => 'stlouis_elders_rename_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/welcome/%'] = array(
    'title' => 'St. Louis Welcome',
    'page callback' => 'stlouis_welcome_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/choose_clan/%/%'] = array(
    'title' => 'St. Louis Choose Clan',
    'page callback' => 'stlouis_choose_clan_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/help/%'] = array(
    'title' => 'St. Louis Help',
    'page callback' => 'stlouis_help_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/debates/%'] = array(
    'title' => 'St. Louis Debates List',
    'page callback' => 'stlouis_debates_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/debates_challenge/%/%'] = array(
    'title' => 'St. Louis Debates Challenge',
    'page callback' => 'stlouis_debates_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elections/%'] = array(
    'title' => 'St. Louis Elections List',
    'page callback' => 'stlouis_elections_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elections_challenge/%/%'] = array(
    'title' => 'St. Louis Elections Challenge',
    'page callback' => 'stlouis_elections_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/favors/%'] = array(
    'title' => 'St. Louis Favors List',
    'page callback' => 'stlouis_favors_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/favors_selected/%/%'] = array(
    'title' => 'St. Louis Favors Selected List',
    'page callback' => 'stlouis_favors_selected_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/favors_selected_2/%/%'] = array(
    'title' => 'St. Louis Favors Selected List',
    'page callback' => 'stlouis_favors_selected_2_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/hierarchies/%'] = array(
    'title' => 'St. Louis Hierarchies List',
    'page callback' => 'stlouis_hierarchies_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/hierarchy_rise/%/%'] = array(
    'title' => 'St. Louis Hierarchy Rise',
    'page callback' => 'stlouis_hierarchy_rise_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top20/%'] = array(
    'title' => 'St. Louis Top 20 List',
    'page callback' => 'stlouis_top20_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_debaters/%'] = array(
    'title' => 'St. Louis Top Debaters List',
    'page callback' => 'stlouis_top_debaters_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_income/%'] = array(
    'title' => 'St. Louis Top Income List',
    'page callback' => 'stlouis_top_income_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_masters/%'] = array(
    'title' => 'St. Louis Top Masters List',
    'page callback' => 'stlouis_top_masters_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_minions/%'] = array(
    'title' => 'St. Louis Top Minions List',
    'page callback' => 'stlouis_top_minions_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_wards/%'] = array(
    'title' => 'St. Louis Top Wards List',
    'page callback' => 'stlouis_top_wards_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_aldermen/%'] = array(
    'title' => 'St. Louis Top Aldermen List',
    'page callback' => 'stlouis_top_aldermen_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/top_event_points/%'] = array(
    'title' => 'St. Louis Top 100 Event Points',
    'page callback' => 'stlouis_top_event_points_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/choose_name/%'] = array(
    'title' => 'St. Louis Choose Name',
    'page callback' => 'stlouis_choose_name_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/enter_referral_code/%'] = array(
    'title' => 'St. Louis Enter Referral Code',
    'page callback' => 'stlouis_enter_referral_code_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/aides/%'] = array(
    'title' => 'St. Louis Aides',
    'page callback' => 'stlouis_aides_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/land/%'] = array(
    'title' => 'St. Louis Land',
    'page callback' => 'stlouis_land_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/land_buy/%/%/%'] = array(
    'title' => 'St. Louis Land Buy',
    'page callback' => 'stlouis_land_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/land_sell/%/%/%'] = array(
    'title' => 'St. Louis Land Sell',
    'page callback' => 'stlouis_land_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/staff/%'] = array(
    'title' => 'St. Louis Staff',
    'page callback' => 'stlouis_staff_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/staff_hire/%/%/%'] = array(
    'title' => 'St. Louis Staff Hire',
    'page callback' => 'stlouis_staff_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/staff_fire/%/%/%'] = array(
    'title' => 'St. Louis Staff Fire',
    'page callback' => 'stlouis_staff_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/agents/%'] = array(
    'title' => 'St. Louis Agents',
    'page callback' => 'stlouis_agents_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/agents_hire/%/%/%'] = array(
    'title' => 'St. Louis Agents Hire',
    'page callback' => 'stlouis_agents_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/agents_fire/%/%/%'] = array(
    'title' => 'St. Louis Agents Fire',
    'page callback' => 'stlouis_agents_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/actions/%'] = array(
    'title' => 'St. Louis Actions',
    'page callback' => 'stlouis_actions_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/actions_do/%/%'] = array(
    'title' => 'St. Louis Actions Do',
    'page callback' => 'stlouis_actions_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/equipment/%'] = array(
    'title' => 'St. Louis Equipment',
    'page callback' => 'stlouis_equipment_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/equipment_buy/%/%/%'] = array(
    'title' => 'St. Louis Equipment Buy',
    'page callback' => 'stlouis_equipment_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/equipment_sell/%/%/%'] = array(
    'title' => 'St. Louis Equipment Sell',
    'page callback' => 'stlouis_equipment_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/weapons/%'] = array(
    'title' => 'St. Louis Weapons',
    'page callback' => 'stlouis_weapons_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/weapons_buy/%/%/%'] = array(
    'title' => 'St. Louis Weapons Buy',
    'page callback' => 'stlouis_weapons_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/weapons_sell/%/%/%'] = array(
    'title' => 'St. Louis Weapons Sell',
    'page callback' => 'stlouis_weapons_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/move/%/%'] = array(
    'title' => 'St. Louis Move',
    'page callback' => 'stlouis_move_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/move_do/%/%'] = array(
    'title' => 'St. Louis Move Do',
    'page callback' => 'stlouis_move_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/authenticate/%'] = array(
    'title' => 'St. Louis Authenticate',
    'page callback' => 'stlouis_authenticate_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/playbook_null'] = array(
    'title' => 'St. Louis device unauthorized',
    'page callback' => 'stlouis_playbook_null_callback',
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/bounce/%'] = array(
    'title' => 'St. Louis bounce',
    'page callback' => 'stlouis_bounce_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/elders_preferences/%'] = array(
    'title' => 'St. Louis Preferences Page',
    'page callback' => 'stlouis_elders_preference_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/zombie_move/%/%/%'] = array(
    'title' => 'St. Louis Zombie Move Page',
    'page callback' => 'stlouis_zombie_move_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/event_info/%'] = array(
    'title' => 'St. Louis Event Info Page',
    'page callback' => 'stlouis_event_info_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  $items['stlouis/event_prizes/%'] = array(
    'title' => 'St. Louis Event Prizes Page',
    'page callback' => 'stlouis_event_prizes_callback',
    'page arguments' => array(2),
    'access arguments' => array('access stlouis content'),
    'type' => MENU_CALLBACK,
  );

  return $items;

}


function stlouis_homepage_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_homepage', $phone_id);

}


function stlouis_userprofile_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_userprofile', $phone_id);

}


function stlouis_user_favors_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_user_favors', $phone_id);

}


function stlouis_favor_perform_callback($phone_id, $req_id) {

  if (empty($phone_id) || !is_numeric($req_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_favor_perform', $phone_id, $req_id);

}


function stlouis_user_competencies_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_user_competencies', $phone_id);

}


function stlouis_clan_list_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_clan_list', $phone_id, $clan_id);

}


function stlouis_clan_list_available_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_clan_list_available', $phone_id);

}


function stlouis_clan_msg_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_clan_msg', $phone_id, $clan_id);

}


function stlouis_clan_announcements_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_clan_announcements', $phone_id, $clan_id);

}


function stlouis_class_list_callback($phone_id, $class_id) {

  if (empty($phone_id) || !is_numeric($class_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_class_list', $phone_id, $class_id);

}


function stlouis_block_user_toggle_callback($phone_id, $phone_id_to_block) {

  if (empty($phone_id) || empty($phone_id_to_block)) {
    return drupal_access_denied();
  }

  return theme('stlouis_block_user_toggle', $phone_id,
    check_plain(filter_xss($phone_id_to_block, array())));

}


function stlouis_msg_delete_callback($phone_id, $msg_id) {

  if (empty($phone_id) || !is_numeric($msg_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_msg_delete', $phone_id, $msg_id);

}


function stlouis_party_msg_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_party_msg', $phone_id);

}


function stlouis_increase_skills_callback($phone_id, $skill) {

  if (empty($phone_id) || empty($skill)) {
    return drupal_access_denied();
  }

  return theme('stlouis_increase_skills', $phone_id, $skill);

}


function stlouis_quests_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_quests_list', $phone_id);

}


function stlouis_quests_do_callback($phone_id, $quest_id) {

  if (empty($phone_id) || !is_numeric($quest_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_quests_do', $phone_id, $quest_id);

}


function stlouis_elders_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders', $phone_id);

}


function stlouis_elders_ask_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_ask_reset', $phone_id);

}


function stlouis_elders_do_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_do_reset', $phone_id);

}


function stlouis_elders_ask_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_ask_reset_skills', $phone_id);

}


function stlouis_elders_do_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_do_reset_skills', $phone_id);

}


function stlouis_elders_do_fill_callback($phone_id, $fill_type) {

  if (empty($phone_id) || empty($fill_type)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_do_fill', $phone_id, $fill_type);

}


function stlouis_elders_rename_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_rename', $phone_id);

}


function stlouis_elders_set_password_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_set_password', $phone_id);

}


function stlouis_elders_ask_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_ask_purchase', $phone_id);

}


function stlouis_elders_new_ask_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_new_ask_purchase', $phone_id);

}


function stlouis_elders_do_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_do_purchase', $phone_id);

}


function stlouis_elders_check_receipt_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_check_receipt', $phone_id);

}


function stlouis_google_checkout_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_google_checkout', $phone_id);

}


function stlouis_welcome_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_welcome', $phone_id);

}


function stlouis_choose_clan_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_choose_clan', $phone_id, $clan_id);

}


function stlouis_help_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_help', $phone_id);

}


function stlouis_debates_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_debates_list', $phone_id);

}


function stlouis_debates_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_debates_challenge', $phone_id, $position_id);

}


function stlouis_elections_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elections_list', $phone_id);

}


function stlouis_elections_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elections_challenge', $phone_id, $position_id);

}


function stlouis_favors_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_favors_list', $phone_id);

}


function stlouis_favors_selected_callback($phone_id, $favor_id) {

  if (empty($phone_id) || !is_numeric($favor_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_favors_selected', $phone_id, $favor_id);

}


function stlouis_favors_selected_2_callback($phone_id, $favor_id) {

  if (empty($phone_id) || !is_numeric($favor_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_favors_selected_2', $phone_id, $favor_id);

}


function stlouis_hierarchies_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_hierarchies_list', $phone_id);

}


function stlouis_hierarchy_rise_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_hierarchy_rise', $phone_id, $position_id);

}


function stlouis_top20_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top20_list', $phone_id);

}


function stlouis_top_debaters_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_debaters_list', $phone_id);

}


function stlouis_top_income_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_income_list', $phone_id);

}


function stlouis_top_masters_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_masters_list', $phone_id);

}


function stlouis_top_minions_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_minions_list', $phone_id);

}


function stlouis_top_wards_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_wards_list', $phone_id);

}


function stlouis_top_aldermen_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_aldermen_list', $phone_id);

}


function stlouis_top_event_points_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_top_event_points', $phone_id);

}


function stlouis_choose_name_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_choose_name', $phone_id);

}


function stlouis_enter_referral_code_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_enter_referral_code', $phone_id);

}


function stlouis_aides_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_aides', $phone_id);

}


function stlouis_land_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_land_list', $phone_id);

}


function stlouis_land_buy_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_land_buy', $phone_id, $land_id, $quantity);

}


function stlouis_land_sell_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_land_sell', $phone_id, $land_id, $quantity);

}


function stlouis_staff_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_staff_list', $phone_id);

}


function stlouis_staff_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_staff_hire', $phone_id, $staff_id, $quantity);

}


function stlouis_staff_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_staff_fire', $phone_id, $staff_id, $quantity);

}


function stlouis_agents_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_agents_list', $phone_id);

}


function stlouis_agents_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_agents_hire', $phone_id, $staff_id, $quantity);

}


function stlouis_agents_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_agents_fire', $phone_id, $staff_id, $quantity);

}


function stlouis_actions_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_actions_list', $phone_id);

}


function stlouis_actions_do_callback($phone_id, $action_id) {

  if (empty($phone_id) || !is_numeric($action_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_actions_do', $phone_id, $action_id);

}


function stlouis_equipment_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_equipment_list', $phone_id);

}


function stlouis_equipment_buy_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_equipment_buy', $phone_id, $equipment_id, $quantity);

}


function stlouis_equipment_sell_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_equipment_sell', $phone_id, $equipment_id, $quantity);

}


function stlouis_weapons_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_weapons_list', $phone_id);

}


function stlouis_weapons_buy_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_weapons_buy', $phone_id, $equipment_id, $quantity);

}


function stlouis_weapons_sell_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('stlouis_weapons_sell', $phone_id, $equipment_id, $quantity);

}


function stlouis_move_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_move', $phone_id, $neighborhood_id);

}


function stlouis_move_do_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_move_do', $phone_id, $neighborhood_id);

}


function stlouis_authenticate_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_authenticate', $phone_id);

}

/**
 * Used for playbooks that didn't authorize the app
 */

function stlouis_playbook_null_callback() {

  return theme('stlouis_playbook_null');

}

function stlouis_bounce_callback($phone_id) {

  return theme('stlouis_bounce', $phone_id);

}

function stlouis_elders_preference_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_elders_preferences', $phone_id);

}


function stlouis_zombie_move_callback($phone_id, $zombie_id, $hood_id) {

  if (empty($phone_id) || is_nan($zombie_id) || is_nan($hood_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_zombie_move', $phone_id, $zombie_id, $hood_id);

}


function stlouis_event_info_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('stlouis_event_info', $phone_id);

}


function stlouis_event_prizes_callback($phone_id) {

  global $user;

  if (($user->roles[4] != 'web game access') ||
    ($phone_id != 'abc123')) {
    return drupal_access_denied();
  }

  return theme('stlouis_event_prizes', $phone_id);

}


function stlouis_theme() {
  // theme "A" uses template "B" with args "C"

  return array(

    'stlouis_homepage' => array(
      'template' => 'stlouis_homepage',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_userprofile' => array(
      'template' => 'stlouis_userprofile',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_user_favors' => array(
      'template' => 'stlouis_user_favors',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_favor_perform' => array(
      'template' => 'stlouis_favor_perform',
      'arguments' => array(
        'phone_id' => null,
        'req_id' => null,
      )
    ),

    'stlouis_user_competencies' => array(
      'template' => 'stlouis_user_competencies',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_clan_list' => array(
      'template' => 'stlouis_clan_list',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'stlouis_clan_list_available' => array(
      'template' => 'stlouis_clan_list_available',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_clan_msg' => array(
      'template' => 'stlouis_clan_msg',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'stlouis_clan_announcements' => array(
      'template' => 'stlouis_clan_announcements',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'stlouis_class_list' => array(
      'template' => 'stlouis_class_list',
      'arguments' => array(
        'phone_id' => null,
        'class_id' => null,
      )
    ),

    'stlouis_block_user_toggle' => array(
      'template' => 'stlouis_block_user_toggle',
      'arguments' => array(
        'phone_id' => null,
        'phone_id_to_block' => null,
      )
    ),

    'stlouis_msg_delete' => array(
      'template' => 'stlouis_msg_delete',
      'arguments' => array(
        'phone_id' => null,
        'msg_id' => null,
      )
    ),

    'stlouis_party_msg' => array(
      'template' => 'stlouis_party_msg',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_increase_skills' => array(
      'template' => 'stlouis_increase_skills',
      'arguments' => array(
        'phone_id' => null,
        'skill' => null,
      )
    ),

    'stlouis_quests_list' => array(
      'template' => 'stlouis_quests_list',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_quests_do' => array(
      'template' => 'stlouis_quests_do',
      'arguments' => array(
        'phone_id' => null,
        'quest_id' => null,
      )
    ),

    'stlouis_elders' => array(
      'template' => 'stlouis_elders',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_elders_ask_reset' => array(
      'template' => 'stlouis_elders_ask_reset',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_elders_do_reset' => array(
      'template' => 'stlouis_elders_do_reset',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_elders_ask_reset_skills' => array(
      'template' => 'stlouis_elders_ask_reset_skills',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_elders_do_reset_skills' => array(
      'template' => 'stlouis_elders_do_reset_skills',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_elders_do_fill' => array(
      'template' => 'stlouis_elders_do_fill',
      'arguments' => array(
        'phone_id' => null,
        'fill_type' => null,
      )
    ),

    'stlouis_elders_rename' => array(
      'template' => 'stlouis_elders_rename',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elders_set_password' => array(
      'template' => 'stlouis_elders_set_password',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elders_ask_purchase' => array(
      'template' => 'stlouis_elders_ask_purchase',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elders_new_ask_purchase' => array(
      'template' => 'stlouis_elders_new_ask_purchase',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elders_do_purchase' => array(
      'template' => 'stlouis_elders_do_purchase',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_check_receipt' => array(
      'template' => 'stlouis_elders_check_receipt',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_google_checkout' => array(
      'template' => 'stlouis_google_checkout',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_welcome' => array(
      'template' => 'stlouis_welcome',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'stlouis_choose_clan' => array(
      'template' => 'stlouis_choose_clan',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'stlouis_help' => array(
      'template' => 'stlouis_help',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elections_list' => array(
      'template' => 'stlouis_elections_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elections_challenge' => array(
      'template' => 'stlouis_elections_challenge',
      'arguments' => array(
        'phone_id' => null,
        'position_id' => null,
      )
    ),

    'stlouis_hierarchies_list' => array(
      'template' => 'stlouis_hierarchies_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_hierarchy_rise' => array(
      'template' => 'stlouis_hierarchy_rise',
      'arguments' => array(
        'phone_id' => null,
        'position_id' => null,
      )
    ),

    'stlouis_debates_list' => array(
      'template' => 'stlouis_debates_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_debates_challenge' => array(
      'template' => 'stlouis_debates_challenge',
      'arguments' => array(
        'phone_id' => null,
        'position_id' => null,
      )
    ),

    'stlouis_favors_list' => array(
      'template' => 'stlouis_favors_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_favors_selected' => array(
      'template' => 'stlouis_favors_selected',
      'arguments' => array(
        'phone_id' => null,
        'favor_id' => null,
      )
    ),

    'stlouis_favors_selected_2' => array(
      'template' => 'stlouis_favors_selected_2',
      'arguments' => array(
        'phone_id' => null,
        'favor_id' => null,
      )
    ),

    'stlouis_top20_list' => array(
      'template' => 'stlouis_top20_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_debaters_list' => array(
      'template' => 'stlouis_top_debaters_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_income_list' => array(
      'template' => 'stlouis_top_income_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_masters_list' => array(
      'template' => 'stlouis_top_masters_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_minions_list' => array(
      'template' => 'stlouis_top_minions_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_wards_list' => array(
      'template' => 'stlouis_top_wards_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_aldermen_list' => array(
      'template' => 'stlouis_top_aldermen_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_top_event_points' => array(
      'template' => 'stlouis_top_event_points',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_choose_name' => array(
      'template' => 'stlouis_choose_name',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_enter_referral_code' => array(
      'template' => 'stlouis_enter_referral_code',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_aides' => array(
      'template' => 'stlouis_aides',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_land_list' => array(
      'template' => 'stlouis_land_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_land_buy' => array(
      'template' => 'stlouis_land_buy',
      'arguments' => array(
        'phone_id' => null,
        'land_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_land_sell' => array(
      'template' => 'stlouis_land_sell',
      'arguments' => array(
        'phone_id' => null,
        'land_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_staff_list' => array(
      'template' => 'stlouis_staff_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_staff_hire' => array(
      'template' => 'stlouis_staff_hire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_staff_fire' => array(
      'template' => 'stlouis_staff_fire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_agents_list' => array(
      'template' => 'stlouis_agents_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_agents_hire' => array(
      'template' => 'stlouis_agents_hire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_agents_fire' => array(
      'template' => 'stlouis_agents_fire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_actions_list' => array(
      'template' => 'stlouis_actions_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_actions_do' => array(
      'template' => 'stlouis_actions_do',
      'arguments' => array(
        'phone_id' => null,
        'action_id' => null,
      )
    ),

    'stlouis_equipment_list' => array(
      'template' => 'stlouis_equipment_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_equipment_buy' => array(
      'template' => 'stlouis_equipment_buy',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_equipment_sell' => array(
      'template' => 'stlouis_equipment_sell',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_weapons_list' => array(
      'template' => 'stlouis_weapons_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_weapons_buy' => array(
      'template' => 'stlouis_weapons_buy',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_weapons_sell' => array(
      'template' => 'stlouis_weapons_sell',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'stlouis_move' => array(
      'template' => 'stlouis_move',
      'arguments' => array(
        'phone_id' => null,
        'neighborhood_id' => null,
      )
    ),

    'stlouis_move_do' => array(
      'template' => 'stlouis_move_do',
      'arguments' => array(
        'phone_id' => null,
        'neighborhood_id' => null,
      )
    ),

    'stlouis_authenticate' => array(
      'template' => 'stlouis_authenticate',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_playbook_null' => array(
      'template' => 'stlouis_playbook_null',
    ),

    'stlouis_bounce' => array(
      'template' => 'stlouis_bounce',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_elders_preferences' => array(
      'template' => 'stlouis_elders_preferences',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_zombie_move' => array(
      'template' => 'stlouis_zombie_move',
      'arguments' => array(
        'phone_id' => null,
        'zombie_id' => null,
        'hood_id' => null,
      )
    ),

    'stlouis_event_info' => array(
      'template' => 'stlouis_event_info',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'stlouis_event_prizes' => array(
      'template' => 'stlouis_event_prizes',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

  );

} // stlouis_theme()

