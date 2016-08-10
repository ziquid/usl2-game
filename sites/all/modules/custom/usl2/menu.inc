<?php

/**
 * @file
 * This file is the menu items and related functions for USL2.
 *
 * @todo CG merge
 *
 */


function usl2_menu() {

  $items = array();

  $items['usl2/home/%'] = array(
    'title' => 'USL2 Home Page',
    'page callback' => 'usl2_homepage_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/user/%'] = array(
    'title' => 'USL2 User Profile Page',
    'page callback' => 'usl2_userprofile_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/user_favors/%'] = array(
    'title' => 'USL2 User Favors Page',
    'page callback' => 'usl2_user_favors_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/favor_perform/%/%'] = array(
    'title' => 'USL2 User Favors Page',
    'page callback' => 'usl2_favor_perform_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/user_comp/%'] = array(
    'title' => 'USL2 User Competencies Page',
    'page callback' => 'usl2_user_competencies_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/clan_list/%/%'] = array(
    'title' => 'USL2 Clan List Page',
    'page callback' => 'usl2_clan_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/clan_list_available/%'] = array(
    'title' => 'USL2 Available Clan List Page',
    'page callback' => 'usl2_clan_list_available_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/clan_msg/%/%'] = array(
    'title' => 'USL2 Clan Messages Page',
    'page callback' => 'usl2_clan_msg_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/clan_announcements/%/%'] = array(
    'title' => 'USL2 Clan Announcements Page',
    'page callback' => 'usl2_clan_announcements_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/class_list/%/%'] = array(
    'title' => 'USL2 Class List Page',
    'page callback' => 'usl2_class_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/block_user_toggle/%/%'] = array(
    'title' => 'USL2 Block User Toggle',
    'page callback' => 'usl2_block_user_toggle_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/msg_delete/%/%'] = array(
    'title' => 'USL2 Message Delete Page',
    'page callback' => 'usl2_msg_delete_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/party_msg/%'] = array(
    'title' => 'USL2 Party Message Page',
    'page callback' => 'usl2_party_msg_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/increase_skills/%/%'] = array(
    'title' => 'USL2 Increase Skills Page',
    'page callback' => 'usl2_increase_skills_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/quests/%'] = array(
    'title' => 'USL2 Quests List',
    'page callback' => 'usl2_quests_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/quests_do/%/%'] = array(
    'title' => 'USL2 Quests Do',
    'page callback' => 'usl2_quests_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders/%'] = array(
    'title' => 'USL2 Elders',
    'page callback' => 'usl2_elders_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_ask_reset/%'] = array(
    'title' => 'USL2 Elders Ask Reset',
    'page callback' => 'usl2_elders_ask_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_do_reset/%'] = array(
    'title' => 'USL2 Elders Do Reset',
    'page callback' => 'usl2_elders_do_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_ask_reset_skills/%'] = array(
    'title' => 'USL2 Elders Ask Reset Skills',
    'page callback' => 'usl2_elders_ask_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_do_reset_skills/%'] = array(
    'title' => 'USL2 Elders Do Reset Skills',
    'page callback' => 'usl2_elders_do_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_ask_purchase/%'] = array(
    'title' => 'USL2 Elders Ask Purchase',
    'page callback' => 'usl2_elders_ask_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_new_ask_purchase/%'] = array(
    'title' => 'USL2 Elders New Ask Purchase',
    'page callback' => 'usl2_elders_new_ask_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_do_purchase/%'] = array(
    'title' => 'USL2 Elders Do Purchase',
    'page callback' => 'usl2_elders_do_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_check_receipt/%'] = array(
    'title' => 'USL2 Elders Check Receipt',
    'page callback' => 'usl2_elders_check_receipt_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/google_checkout/%'] = array(
    'title' => 'USL2 Google Checkout',
    'page callback' => 'usl2_google_checkout_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_do_fill/%/%'] = array(
    'title' => 'USL2 Elders Do Fill',
    'page callback' => 'usl2_elders_do_fill_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_set_password/%'] = array(
    'title' => 'USL2 Elders Set Password',
    'page callback' => 'usl2_elders_set_password_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_rename/%'] = array(
    'title' => 'USL2 Elders Rename',
    'page callback' => 'usl2_elders_rename_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/welcome/%'] = array(
    'title' => 'USL2 Welcome',
    'page callback' => 'usl2_welcome_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/choose_clan/%/%'] = array(
    'title' => 'USL2 Choose Clan',
    'page callback' => 'usl2_choose_clan_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/help/%'] = array(
    'title' => 'USL2 Help',
    'page callback' => 'usl2_help_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/debates/%'] = array(
    'title' => 'USL2 Debates List',
    'page callback' => 'usl2_debates_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/debates_challenge/%/%'] = array(
    'title' => 'USL2 Debates Challenge',
    'page callback' => 'usl2_debates_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elections/%'] = array(
    'title' => 'USL2 Elections List',
    'page callback' => 'usl2_elections_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elections_challenge/%/%'] = array(
    'title' => 'USL2 Elections Challenge',
    'page callback' => 'usl2_elections_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/favors/%'] = array(
    'title' => 'USL2 Favors List',
    'page callback' => 'usl2_favors_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/favors_selected/%/%'] = array(
    'title' => 'USL2 Favors Selected List',
    'page callback' => 'usl2_favors_selected_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/favors_selected_2/%/%'] = array(
    'title' => 'USL2 Favors Selected List',
    'page callback' => 'usl2_favors_selected_2_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/hierarchies/%'] = array(
    'title' => 'USL2 Hierarchies List',
    'page callback' => 'usl2_hierarchies_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/hierarchy_rise/%/%'] = array(
    'title' => 'USL2 Hierarchy Rise',
    'page callback' => 'usl2_hierarchy_rise_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top20/%'] = array(
    'title' => 'USL2 Top 20 List',
    'page callback' => 'usl2_top20_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_debaters/%'] = array(
    'title' => 'USL2 Top Debaters List',
    'page callback' => 'usl2_top_debaters_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_income/%'] = array(
    'title' => 'USL2 Top Income List',
    'page callback' => 'usl2_top_income_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_masters/%'] = array(
    'title' => 'USL2 Top Masters List',
    'page callback' => 'usl2_top_masters_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_minions/%'] = array(
    'title' => 'USL2 Top Minions List',
    'page callback' => 'usl2_top_minions_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_wards/%'] = array(
    'title' => 'USL2 Top Wards List',
    'page callback' => 'usl2_top_wards_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_aldermen/%'] = array(
    'title' => 'USL2 Top Aldermen List',
    'page callback' => 'usl2_top_aldermen_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/top_event_points/%'] = array(
    'title' => 'USL2 Top 100 Event Points',
    'page callback' => 'usl2_top_event_points_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/choose_name/%'] = array(
    'title' => 'USL2 Choose Name',
    'page callback' => 'usl2_choose_name_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/enter_referral_code/%'] = array(
    'title' => 'USL2 Enter Referral Code',
    'page callback' => 'usl2_enter_referral_code_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/aides/%'] = array(
    'title' => 'USL2 Aides',
    'page callback' => 'usl2_aides_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/land/%'] = array(
    'title' => 'USL2 Land',
    'page callback' => 'usl2_land_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/land_buy/%/%/%'] = array(
    'title' => 'USL2 Land Buy',
    'page callback' => 'usl2_land_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/land_sell/%/%/%'] = array(
    'title' => 'USL2 Land Sell',
    'page callback' => 'usl2_land_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/staff/%'] = array(
    'title' => 'USL2 Staff',
    'page callback' => 'usl2_staff_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/staff_hire/%/%/%'] = array(
    'title' => 'USL2 Staff Hire',
    'page callback' => 'usl2_staff_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/staff_fire/%/%/%'] = array(
    'title' => 'USL2 Staff Fire',
    'page callback' => 'usl2_staff_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/agents/%'] = array(
    'title' => 'USL2 Agents',
    'page callback' => 'usl2_agents_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/agents_hire/%/%/%'] = array(
    'title' => 'USL2 Agents Hire',
    'page callback' => 'usl2_agents_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/agents_fire/%/%/%'] = array(
    'title' => 'USL2 Agents Fire',
    'page callback' => 'usl2_agents_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/actions/%'] = array(
    'title' => 'USL2 Actions',
    'page callback' => 'usl2_actions_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/actions_do/%/%'] = array(
    'title' => 'USL2 Actions Do',
    'page callback' => 'usl2_actions_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/equipment/%'] = array(
    'title' => 'USL2 Equipment',
    'page callback' => 'usl2_equipment_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/equipment_buy/%/%/%'] = array(
    'title' => 'USL2 Equipment Buy',
    'page callback' => 'usl2_equipment_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/equipment_sell/%/%/%'] = array(
    'title' => 'USL2 Equipment Sell',
    'page callback' => 'usl2_equipment_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/weapons/%'] = array(
    'title' => 'USL2 Weapons',
    'page callback' => 'usl2_weapons_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/weapons_buy/%/%/%'] = array(
    'title' => 'USL2 Weapons Buy',
    'page callback' => 'usl2_weapons_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/weapons_sell/%/%/%'] = array(
    'title' => 'USL2 Weapons Sell',
    'page callback' => 'usl2_weapons_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/move/%/%'] = array(
    'title' => 'USL2 Move',
    'page callback' => 'usl2_move_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/move_do/%/%'] = array(
    'title' => 'USL2 Move Do',
    'page callback' => 'usl2_move_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/authenticate/%'] = array(
    'title' => 'USL2 Authenticate',
    'page callback' => 'usl2_authenticate_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/playbook_NULL'] = array(
    'title' => 'USL2 device unauthorized',
    'page callback' => 'usl2_playbook_NULL_callback',
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/bounce/%'] = array(
    'title' => 'USL2 bounce',
    'page callback' => 'usl2_bounce_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/elders_preferences/%'] = array(
    'title' => 'USL2 Preferences Page',
    'page callback' => 'usl2_elders_preference_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/zombie_move/%/%/%'] = array(
    'title' => 'USL2 Zombie Move Page',
    'page callback' => 'usl2_zombie_move_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/event_info/%'] = array(
    'title' => 'USL2 Event Info Page',
    'page callback' => 'usl2_event_info_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  $items['usl2/event_prizes/%'] = array(
    'title' => 'USL2 Event Prizes Page',
    'page callback' => 'usl2_event_prizes_callback',
    'page arguments' => array(2),
    'access arguments' => array('access usl2 content'),
    'type' => MENU_CALLBACK,
  );

  return $items;

}


function usl2_homepage_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_homepage', $phone_id);

}


function usl2_userprofile_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_userprofile', $phone_id);

}


function usl2_user_favors_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_user_favors', $phone_id);

}


function usl2_favor_perform_callback($phone_id, $req_id) {

  if (empty($phone_id) || !is_numeric($req_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_favor_perform', $phone_id, $req_id);

}


function usl2_user_competencies_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_user_competencies', $phone_id);

}


function usl2_clan_list_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_clan_list', $phone_id, $clan_id);

}


function usl2_clan_list_available_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_clan_list_available', $phone_id);

}


function usl2_clan_msg_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_clan_msg', $phone_id, $clan_id);

}


function usl2_clan_announcements_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_clan_announcements', $phone_id, $clan_id);

}


function usl2_class_list_callback($phone_id, $class_id) {

  if (empty($phone_id) || !is_numeric($class_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_class_list', $phone_id, $class_id);

}


function usl2_block_user_toggle_callback($phone_id, $phone_id_to_block) {

  if (empty($phone_id) || empty($phone_id_to_block)) {
    return drupal_access_denied();
  }

  return theme('usl2_block_user_toggle', $phone_id,
    check_plain(filter_xss($phone_id_to_block, array())));

}


function usl2_msg_delete_callback($phone_id, $msg_id) {

  if (empty($phone_id) || !is_numeric($msg_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_msg_delete', $phone_id, $msg_id);

}


function usl2_party_msg_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_party_msg', $phone_id);

}


function usl2_increase_skills_callback($phone_id, $skill) {

  if (empty($phone_id) || empty($skill)) {
    return drupal_access_denied();
  }

  return theme('usl2_increase_skills', $phone_id, $skill);

}


function usl2_quests_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_quests_list', $phone_id);

}


function usl2_quests_do_callback($phone_id, $quest_id) {

  if (empty($phone_id) || !is_numeric($quest_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_quests_do', $phone_id, $quest_id);

}


function usl2_elders_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders', $phone_id);

}


function usl2_elders_ask_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_ask_reset', $phone_id);

}


function usl2_elders_do_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_do_reset', $phone_id);

}


function usl2_elders_ask_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_ask_reset_skills', $phone_id);

}


function usl2_elders_do_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_do_reset_skills', $phone_id);

}


function usl2_elders_do_fill_callback($phone_id, $fill_type) {

  if (empty($phone_id) || empty($fill_type)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_do_fill', $phone_id, $fill_type);

}


function usl2_elders_rename_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_rename', $phone_id);

}


function usl2_elders_set_password_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_set_password', $phone_id);

}


function usl2_elders_ask_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_ask_purchase', $phone_id);

}


function usl2_elders_new_ask_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_new_ask_purchase', $phone_id);

}


function usl2_elders_do_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_do_purchase', $phone_id);

}


function usl2_elders_check_receipt_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_check_receipt', $phone_id);

}


function usl2_google_checkout_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_google_checkout', $phone_id);

}


function usl2_welcome_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_welcome', $phone_id);

}


function usl2_choose_clan_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_choose_clan', $phone_id, $clan_id);

}


function usl2_help_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_help', $phone_id);

}


function usl2_debates_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_debates_list', $phone_id);

}


function usl2_debates_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_debates_challenge', $phone_id, $position_id);

}


function usl2_elections_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elections_list', $phone_id);

}


function usl2_elections_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elections_challenge', $phone_id, $position_id);

}


function usl2_favors_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_favors_list', $phone_id);

}


function usl2_favors_selected_callback($phone_id, $favor_id) {

  if (empty($phone_id) || !is_numeric($favor_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_favors_selected', $phone_id, $favor_id);

}


function usl2_favors_selected_2_callback($phone_id, $favor_id) {

  if (empty($phone_id) || !is_numeric($favor_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_favors_selected_2', $phone_id, $favor_id);

}


function usl2_hierarchies_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_hierarchies_list', $phone_id);

}


function usl2_hierarchy_rise_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_hierarchy_rise', $phone_id, $position_id);

}


function usl2_top20_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top20_list', $phone_id);

}


function usl2_top_debaters_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_debaters_list', $phone_id);

}


function usl2_top_income_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_income_list', $phone_id);

}


function usl2_top_masters_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_masters_list', $phone_id);

}


function usl2_top_minions_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_minions_list', $phone_id);

}


function usl2_top_wards_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_wards_list', $phone_id);

}


function usl2_top_aldermen_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_aldermen_list', $phone_id);

}


function usl2_top_event_points_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_top_event_points', $phone_id);

}


function usl2_choose_name_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_choose_name', $phone_id);

}


function usl2_enter_referral_code_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_enter_referral_code', $phone_id);

}


function usl2_aides_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_aides', $phone_id);

}


function usl2_land_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_land_list', $phone_id);

}


function usl2_land_buy_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_land_buy', $phone_id, $land_id, $quantity);

}


function usl2_land_sell_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_land_sell', $phone_id, $land_id, $quantity);

}


function usl2_staff_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_staff_list', $phone_id);

}


function usl2_staff_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_staff_hire', $phone_id, $staff_id, $quantity);

}


function usl2_staff_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_staff_fire', $phone_id, $staff_id, $quantity);

}


function usl2_agents_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_agents_list', $phone_id);

}


function usl2_agents_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_agents_hire', $phone_id, $staff_id, $quantity);

}


function usl2_agents_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_agents_fire', $phone_id, $staff_id, $quantity);

}


function usl2_actions_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_actions_list', $phone_id);

}


function usl2_actions_do_callback($phone_id, $action_id) {

  if (empty($phone_id) || !is_numeric($action_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_actions_do', $phone_id, $action_id);

}


function usl2_equipment_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_equipment_list', $phone_id);

}


function usl2_equipment_buy_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_equipment_buy', $phone_id, $equipment_id, $quantity);

}


function usl2_equipment_sell_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_equipment_sell', $phone_id, $equipment_id, $quantity);

}


function usl2_weapons_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_weapons_list', $phone_id);

}


function usl2_weapons_buy_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_weapons_buy', $phone_id, $equipment_id, $quantity);

}


function usl2_weapons_sell_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('usl2_weapons_sell', $phone_id, $equipment_id, $quantity);

}


function usl2_move_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_move', $phone_id, $neighborhood_id);

}


function usl2_move_do_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_move_do', $phone_id, $neighborhood_id);

}


function usl2_authenticate_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_authenticate', $phone_id);

}

/**
 * Used for playbooks that didn't authorize the app
 */
function usl2_playbook_NULL_callback() {

  return theme('usl2_playbook_NULL');

}

function usl2_bounce_callback($phone_id) {

  return theme('usl2_bounce', $phone_id);

}

function usl2_elders_preference_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_elders_preferences', $phone_id);

}


function usl2_zombie_move_callback($phone_id, $zombie_id, $hood_id) {

  if (empty($phone_id) || is_nan($zombie_id) || is_nan($hood_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_zombie_move', $phone_id, $zombie_id, $hood_id);

}


function usl2_event_info_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('usl2_event_info', $phone_id);

}


function usl2_event_prizes_callback($phone_id) {

  global $user;

  if (($user->roles[4] != 'web game access') ||
    ($phone_id != 'abc123')) {
    return drupal_access_denied();
  }

  return theme('usl2_event_prizes', $phone_id);

}


function usl2_theme() {
  // theme "A" uses template "B" with args "C"

  return array(

    'usl2_homepage' => array(
      'template' => 'usl2_homepage',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_userprofile' => array(
      'template' => 'usl2_userprofile',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_user_favors' => array(
      'template' => 'usl2_user_favors',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_favor_perform' => array(
      'template' => 'usl2_favor_perform',
      'variables' => array(
        'phone_id' => NULL,
        'req_id' => NULL,
      )
    ),

    'usl2_user_competencies' => array(
      'template' => 'usl2_user_competencies',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_clan_list' => array(
      'template' => 'usl2_clan_list',
      'variables' => array(
        'phone_id' => NULL,
        'clan_id' => NULL,
      )
    ),

    'usl2_clan_list_available' => array(
      'template' => 'usl2_clan_list_available',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_clan_msg' => array(
      'template' => 'usl2_clan_msg',
      'variables' => array(
        'phone_id' => NULL,
        'clan_id' => NULL,
      )
    ),

    'usl2_clan_announcements' => array(
      'template' => 'usl2_clan_announcements',
      'variables' => array(
        'phone_id' => NULL,
        'clan_id' => NULL,
      )
    ),

    'usl2_class_list' => array(
      'template' => 'usl2_class_list',
      'variables' => array(
        'phone_id' => NULL,
        'class_id' => NULL,
      )
    ),

    'usl2_block_user_toggle' => array(
      'template' => 'usl2_block_user_toggle',
      'variables' => array(
        'phone_id' => NULL,
        'phone_id_to_block' => NULL,
      )
    ),

    'usl2_msg_delete' => array(
      'template' => 'usl2_msg_delete',
      'variables' => array(
        'phone_id' => NULL,
        'msg_id' => NULL,
      )
    ),

    'usl2_party_msg' => array(
      'template' => 'usl2_party_msg',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_increase_skills' => array(
      'template' => 'usl2_increase_skills',
      'variables' => array(
        'phone_id' => NULL,
        'skill' => NULL,
      )
    ),

    'usl2_quests_list' => array(
      'template' => 'usl2_quests_list',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_quests_do' => array(
      'template' => 'usl2_quests_do',
      'variables' => array(
        'phone_id' => NULL,
        'quest_id' => NULL,
      )
    ),

    'usl2_elders' => array(
      'template' => 'usl2_elders',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_elders_ask_reset' => array(
      'template' => 'usl2_elders_ask_reset',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_elders_do_reset' => array(
      'template' => 'usl2_elders_do_reset',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_elders_ask_reset_skills' => array(
      'template' => 'usl2_elders_ask_reset_skills',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_elders_do_reset_skills' => array(
      'template' => 'usl2_elders_do_reset_skills',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_elders_do_fill' => array(
      'template' => 'usl2_elders_do_fill',
      'variables' => array(
        'phone_id' => NULL,
        'fill_type' => NULL,
      )
    ),

    'usl2_elders_rename' => array(
      'template' => 'usl2_elders_rename',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elders_set_password' => array(
      'template' => 'usl2_elders_set_password',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elders_ask_purchase' => array(
      'template' => 'usl2_elders_ask_purchase',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elders_new_ask_purchase' => array(
      'template' => 'usl2_elders_new_ask_purchase',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elders_do_purchase' => array(
      'template' => 'usl2_elders_do_purchase',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_check_receipt' => array(
      'template' => 'usl2_elders_check_receipt',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_google_checkout' => array(
      'template' => 'usl2_google_checkout',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_welcome' => array(
      'template' => 'usl2_welcome',
      'variables' => array(
        'phone_id' => NULL
      )
    ),

    'usl2_choose_clan' => array(
      'template' => 'usl2_choose_clan',
      'variables' => array(
        'phone_id' => NULL,
        'clan_id' => NULL,
      )
    ),

    'usl2_help' => array(
      'template' => 'usl2_help',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elections_list' => array(
      'template' => 'usl2_elections_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elections_challenge' => array(
      'template' => 'usl2_elections_challenge',
      'variables' => array(
        'phone_id' => NULL,
        'position_id' => NULL,
      )
    ),

    'usl2_hierarchies_list' => array(
      'template' => 'usl2_hierarchies_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_hierarchy_rise' => array(
      'template' => 'usl2_hierarchy_rise',
      'variables' => array(
        'phone_id' => NULL,
        'position_id' => NULL,
      )
    ),

    'usl2_debates_list' => array(
      'template' => 'usl2_debates_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_debates_challenge' => array(
      'template' => 'usl2_debates_challenge',
      'variables' => array(
        'phone_id' => NULL,
        'position_id' => NULL,
      )
    ),

    'usl2_favors_list' => array(
      'template' => 'usl2_favors_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_favors_selected' => array(
      'template' => 'usl2_favors_selected',
      'variables' => array(
        'phone_id' => NULL,
        'favor_id' => NULL,
      )
    ),

    'usl2_favors_selected_2' => array(
      'template' => 'usl2_favors_selected_2',
      'variables' => array(
        'phone_id' => NULL,
        'favor_id' => NULL,
      )
    ),

    'usl2_top20_list' => array(
      'template' => 'usl2_top20_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_debaters_list' => array(
      'template' => 'usl2_top_debaters_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_income_list' => array(
      'template' => 'usl2_top_income_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_masters_list' => array(
      'template' => 'usl2_top_masters_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_minions_list' => array(
      'template' => 'usl2_top_minions_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_wards_list' => array(
      'template' => 'usl2_top_wards_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_aldermen_list' => array(
      'template' => 'usl2_top_aldermen_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_top_event_points' => array(
      'template' => 'usl2_top_event_points',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_choose_name' => array(
      'template' => 'usl2_choose_name',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_enter_referral_code' => array(
      'template' => 'usl2_enter_referral_code',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_aides' => array(
      'template' => 'usl2_aides',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_land_list' => array(
      'template' => 'usl2_land_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_land_buy' => array(
      'template' => 'usl2_land_buy',
      'variables' => array(
        'phone_id' => NULL,
        'land_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_land_sell' => array(
      'template' => 'usl2_land_sell',
      'variables' => array(
        'phone_id' => NULL,
        'land_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_staff_list' => array(
      'template' => 'usl2_staff_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_staff_hire' => array(
      'template' => 'usl2_staff_hire',
      'variables' => array(
        'phone_id' => NULL,
        'staff_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_staff_fire' => array(
      'template' => 'usl2_staff_fire',
      'variables' => array(
        'phone_id' => NULL,
        'staff_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_agents_list' => array(
      'template' => 'usl2_agents_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_agents_hire' => array(
      'template' => 'usl2_agents_hire',
      'variables' => array(
        'phone_id' => NULL,
        'staff_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_agents_fire' => array(
      'template' => 'usl2_agents_fire',
      'variables' => array(
        'phone_id' => NULL,
        'staff_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_actions_list' => array(
      'template' => 'usl2_actions_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_actions_do' => array(
      'template' => 'usl2_actions_do',
      'variables' => array(
        'phone_id' => NULL,
        'action_id' => NULL,
      )
    ),

    'usl2_equipment_list' => array(
      'template' => 'usl2_equipment_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_equipment_buy' => array(
      'template' => 'usl2_equipment_buy',
      'variables' => array(
        'phone_id' => NULL,
        'equipment_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_equipment_sell' => array(
      'template' => 'usl2_equipment_sell',
      'variables' => array(
        'phone_id' => NULL,
        'equipment_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_weapons_list' => array(
      'template' => 'usl2_weapons_list',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_weapons_buy' => array(
      'template' => 'usl2_weapons_buy',
      'variables' => array(
        'phone_id' => NULL,
        'equipment_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_weapons_sell' => array(
      'template' => 'usl2_weapons_sell',
      'variables' => array(
        'phone_id' => NULL,
        'equipment_id' => NULL,
        'quantity' => NULL,
      )
    ),

    'usl2_move' => array(
      'template' => 'usl2_move',
      'variables' => array(
        'phone_id' => NULL,
        'neighborhood_id' => NULL,
      )
    ),

    'usl2_move_do' => array(
      'template' => 'usl2_move_do',
      'variables' => array(
        'phone_id' => NULL,
        'neighborhood_id' => NULL,
      )
    ),

    'usl2_authenticate' => array(
      'template' => 'usl2_authenticate',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_playbook_NULL' => array(
      'template' => 'usl2_playbook_NULL',
    ),

    'usl2_bounce' => array(
      'template' => 'usl2_bounce',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_elders_preferences' => array(
      'template' => 'usl2_elders_preferences',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_zombie_move' => array(
      'template' => 'usl2_zombie_move',
      'variables' => array(
        'phone_id' => NULL,
        'zombie_id' => NULL,
        'hood_id' => NULL,
      )
    ),

    'usl2_event_info' => array(
      'template' => 'usl2_event_info',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

    'usl2_event_prizes' => array(
      'template' => 'usl2_event_prizes',
      'variables' => array(
        'phone_id' => NULL,
      )
    ),

  );

} // usl2_theme()
