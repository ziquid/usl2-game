<?php


function celestial_glory_menu() {

  $items['celestial_glory/home/%'] = array(
    'title' => 'Celestial Glory Home Page',
    'page callback' => 'celestial_glory_homepage_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/user/%'] = array(
    'title' => 'Celestial Glory User Profile Page',
    'page callback' => 'celestial_glory_userprofile_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/user_comp/%'] = array(
    'title' => 'Celestial Glory User Competencies Page',
    'page callback' => 'celestial_glory_user_competencies_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/clan_list/%/%'] = array(
    'title' => 'Celestial Glory Clan List Page',
    'page callback' => 'celestial_glory_clan_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/clan_list_available/%'] = array(
    'title' => 'Celestial Glory Available Clan List Page',
    'page callback' => 'celestial_glory_clan_list_available_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/clan_msg/%/%'] = array(
    'title' => 'Celestial Glory Clan Messages Page',
    'page callback' => 'celestial_glory_clan_msg_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/clan_announcements/%/%'] = array(
    'title' => 'Celestial Glory Clan Announcements Page',
    'page callback' => 'celestial_glory_clan_announcements_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/clan_msg_delete/%/%'] = array(
    'title' => 'Celestial Glory Clan Message Delete Page',
    'page callback' => 'celestial_glory_clan_msg_delete_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/class_list/%/%'] = array(
    'title' => 'Celestial Glory Class List Page',
    'page callback' => 'celestial_glory_class_list_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/block_user_toggle/%/%'] = array(
    'title' => 'Celestial Glory Block User Toggle',
    'page callback' => 'celestial_glory_block_user_toggle_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/msg_delete/%/%'] = array(
    'title' => 'Celestial Glory Message Delete Page',
    'page callback' => 'celestial_glory_msg_delete_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/party_msg/%'] = array(
    'title' => 'Celestial Glory Party Message Page',
    'page callback' => 'celestial_glory_party_msg_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/increase_skills/%/%'] = array(
    'title' => 'Celestial Glory Increase Skills Page',
    'page callback' => 'celestial_glory_increase_skills_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/quests/%'] = array(
    'title' => 'Celestial Glory Quests List',
    'page callback' => 'celestial_glory_quests_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/quests_do/%/%'] = array(
    'title' => 'Celestial Glory Quests Do',
    'page callback' => 'celestial_glory_quests_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders/%'] = array(
    'title' => 'Celestial Glory Elders',
    'page callback' => 'celestial_glory_elders_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_ask_reset/%'] = array(
    'title' => 'Celestial Glory Elders Ask Reset',
    'page callback' => 'celestial_glory_elders_ask_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_do_reset/%'] = array(
    'title' => 'Celestial Glory Elders Do Reset',
    'page callback' => 'celestial_glory_elders_do_reset_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_ask_reset_skills/%'] = array(
    'title' => 'Celestial Glory Elders Ask Reset Skills',
    'page callback' => 'celestial_glory_elders_ask_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_do_reset_skills/%'] = array(
    'title' => 'Celestial Glory Elders Do Reset Skills',
    'page callback' => 'celestial_glory_elders_do_reset_skills_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_ask_purchase/%'] = array(
    'title' => 'Celestial Glory Elders Ask Purchase',
    'page callback' => 'celestial_glory_elders_ask_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_do_purchase/%'] = array(
    'title' => 'Celestial Glory Elders Do Purchase',
    'page callback' => 'celestial_glory_elders_do_purchase_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_check_receipt/%'] = array(
    'title' => 'Celestial Glory Elders Check Receipt',
    'page callback' => 'celestial_glory_elders_check_receipt_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/google_checkout/%'] = array(
    'title' => 'Celestial Glory Google Checkout',
    'page callback' => 'celestial_glory_google_checkout_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_do_fill/%/%'] = array(
    'title' => 'Celestial Glory Elders Do Fill',
    'page callback' => 'celestial_glory_elders_do_fill_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_set_password/%'] = array(
    'title' => 'Celestial Glory Elders Set Password',
    'page callback' => 'celestial_glory_elders_set_password_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_rename/%'] = array(
    'title' => 'Celestial Glory Elders Rename',
    'page callback' => 'celestial_glory_elders_rename_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/welcome/%'] = array(
    'title' => 'Celestial Glory Welcome',
    'page callback' => 'celestial_glory_welcome_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/choose_clan/%/%'] = array(
    'title' => 'Celestial Glory Choose Clan',
    'page callback' => 'celestial_glory_choose_clan_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/help/%'] = array(
    'title' => 'Celestial Glory Help',
    'page callback' => 'celestial_glory_help_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/changelog/%'] = array(
    'title' => 'Celestial Glory Changelog',
    'page callback' => 'celestial_glory_changelog_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/prize_list/%'] = array(
    'title' => 'Celestial Glory Prize List',
    'page callback' => 'celestial_glory_prize_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/debates/%'] = array(
    'title' => 'Celestial Glory Debates List',
    'page callback' => 'celestial_glory_debates_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/debates_challenge/%/%'] = array(
    'title' => 'Celestial Glory Debates Challenge',
    'page callback' => 'celestial_glory_debates_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elections/%'] = array(
    'title' => 'Celestial Glory Elections List',
    'page callback' => 'celestial_glory_elections_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elections_challenge/%/%'] = array(
    'title' => 'Celestial Glory Elections Challenge',
    'page callback' => 'celestial_glory_elections_challenge_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/top20/%'] = array(
    'title' => 'Celestial Glory Top 20 List',
    'page callback' => 'celestial_glory_top20_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/top_wards/%'] = array(
    'title' => 'Celestial Glory Top Wards List',
    'page callback' => 'celestial_glory_top_wards_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/top_aldermen/%'] = array(
    'title' => 'Celestial Glory Top Aldermen List',
    'page callback' => 'celestial_glory_top_aldermen_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/top_event_points/%'] = array(
    'title' => 'Celestial Glory Top 100 Event Points',
    'page callback' => 'celestial_glory_top_event_points_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/choose_name/%'] = array(
    'title' => 'Celestial Glory Choose Name',
    'page callback' => 'celestial_glory_choose_name_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/enter_referral_code/%'] = array(
    'title' => 'Celestial Glory Enter Referral Code',
    'page callback' => 'celestial_glory_enter_referral_code_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/aides/%'] = array(
    'title' => 'Celestial Glory Aides',
    'page callback' => 'celestial_glory_aides_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/land/%'] = array(
    'title' => 'Celestial Glory Land',
    'page callback' => 'celestial_glory_land_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/land_buy/%/%/%'] = array(
    'title' => 'Celestial Glory Land Buy',
    'page callback' => 'celestial_glory_land_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/land_sell/%/%/%'] = array(
    'title' => 'Celestial Glory Land Sell',
    'page callback' => 'celestial_glory_land_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/staff/%'] = array(
    'title' => 'Celestial Glory Staff',
    'page callback' => 'celestial_glory_staff_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/staff_hire/%/%/%'] = array(
    'title' => 'Celestial Glory Staff Hire',
    'page callback' => 'celestial_glory_staff_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/staff_fire/%/%/%'] = array(
    'title' => 'Celestial Glory Staff Fire',
    'page callback' => 'celestial_glory_staff_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/agents/%'] = array(
    'title' => 'Celestial Glory Agents',
    'page callback' => 'celestial_glory_agents_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/agents_hire/%/%/%'] = array(
    'title' => 'Celestial Glory Agents Hire',
    'page callback' => 'celestial_glory_agents_hire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/agents_fire/%/%/%'] = array(
    'title' => 'Celestial Glory Agents Fire',
    'page callback' => 'celestial_glory_agents_fire_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/actions/%'] = array(
    'title' => 'Celestial Glory Actions',
    'page callback' => 'celestial_glory_actions_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/actions_do/%/%'] = array(
    'title' => 'Celestial Glory Actions Do',
    'page callback' => 'celestial_glory_actions_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/equipment/%'] = array(
    'title' => 'Celestial Glory Equipment',
    'page callback' => 'celestial_glory_equipment_list_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/equipment_buy/%/%/%'] = array(
    'title' => 'Celestial Glory Equipment Buy',
    'page callback' => 'celestial_glory_equipment_buy_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/equipment_sell/%/%/%'] = array(
    'title' => 'Celestial Glory Equipment Sell',
    'page callback' => 'celestial_glory_equipment_sell_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/move/%/%'] = array(
    'title' => 'Celestial Glory Move',
    'page callback' => 'celestial_glory_move_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/move_do/%/%'] = array(
    'title' => 'Celestial Glory Move Do',
    'page callback' => 'celestial_glory_move_do_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/authenticate/%'] = array(
    'title' => 'Celestial Glory Authenticate',
    'page callback' => 'celestial_glory_authenticate_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/playbook_null'] = array(
    'title' => 'Celestial Glory device unauthorized',
    'page callback' => 'celestial_glory_playbook_null_callback',
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/bounce/%'] = array(
    'title' => 'Celestial Glory bounce',
    'page callback' => 'celestial_glory_bounce_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/elders_preferences/%'] = array(
    'title' => 'Celestial Glory Preferences Page',
    'page callback' => 'celestial_glory_elders_preference_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/zombie_move/%/%/%'] = array(
    'title' => 'Celestial Glory Zombie Move Page',
    'page callback' => 'celestial_glory_zombie_move_callback',
    'page arguments' => array(2, 3, 4),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/event_info/%'] = array(
    'title' => 'Celestial Glory Event Info Page',
    'page callback' => 'celestial_glory_event_info_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/event_prizes/%'] = array(
    'title' => 'Celestial Glory Event Prizes Page',
    'page callback' => 'celestial_glory_event_prizes_callback',
    'page arguments' => array(2),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  $items['celestial_glory/error/%/%'] = array(
    'title' => 'Celestial Glory Error Page',
    'page callback' => 'celestial_glory_error_callback',
    'page arguments' => array(2, 3),
    'access arguments' => array('access celestial_glory content'),
    'type' => MENU_CALLBACK,
  );

  return $items;

}


function celestial_glory_homepage_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_homepage', $phone_id);

}


function celestial_glory_userprofile_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_userprofile', $phone_id);

}


function celestial_glory_user_competencies_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_user_competencies', $phone_id);

}


function celestial_glory_clan_list_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_clan_list', $phone_id, $clan_id);

}


function celestial_glory_clan_list_available_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_clan_list_available', $phone_id);

}


function celestial_glory_clan_msg_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_clan_msg', $phone_id, $clan_id);

}


function celestial_glory_clan_announcements_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_clan_announcements', $phone_id, $clan_id);

}


function celestial_glory_class_list_callback($phone_id, $class_id) {

  if (empty($phone_id) || !is_numeric($class_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_class_list', $phone_id, $class_id);

}


function celestial_glory_block_user_toggle_callback($phone_id, $phone_id_to_block) {

  if (empty($phone_id) || empty($phone_id_to_block)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_block_user_toggle', $phone_id,
    check_plain(filter_xss($phone_id_to_block, array())));

}


function celestial_glory_msg_delete_callback($phone_id, $msg_id) {

  if (empty($phone_id) || !is_numeric($msg_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_msg_delete', $phone_id, $msg_id);

}


function celestial_glory_clan_msg_delete_callback($phone_id, $msg_id) {

  if (empty($phone_id) || !is_numeric($msg_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_clan_msg_delete', $phone_id, $msg_id);

}


function celestial_glory_party_msg_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_party_msg', $phone_id);

}


function celestial_glory_increase_skills_callback($phone_id, $skill) {

  if (empty($phone_id) || empty($skill)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_increase_skills', $phone_id, $skill);

}


function celestial_glory_quests_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_quests_list', $phone_id);

}


function celestial_glory_quests_do_callback($phone_id, $quest_id) {

  if (empty($phone_id) || !is_numeric($quest_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_quests_do', $phone_id, $quest_id);

}


function celestial_glory_elders_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders', $phone_id);

}


function celestial_glory_elders_ask_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_ask_reset', $phone_id);

}


function celestial_glory_elders_do_reset_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_do_reset', $phone_id);

}


function celestial_glory_elders_ask_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_ask_reset_skills', $phone_id);

}


function celestial_glory_elders_do_reset_skills_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_do_reset_skills', $phone_id);

}


function celestial_glory_elders_do_fill_callback($phone_id, $fill_type) {

  if (empty($phone_id) || empty($fill_type)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_do_fill', $phone_id, $fill_type);

}


function celestial_glory_elders_rename_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_rename', $phone_id);

}


function celestial_glory_elders_set_password_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_set_password', $phone_id);

}


function celestial_glory_elders_ask_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_ask_purchase', $phone_id);

}


function celestial_glory_elders_do_purchase_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_do_purchase', $phone_id);

}


function celestial_glory_elders_check_receipt_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_check_receipt', $phone_id);

}


function celestial_glory_google_checkout_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_google_checkout', $phone_id);

}


function celestial_glory_welcome_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_welcome', $phone_id);

}


function celestial_glory_choose_clan_callback($phone_id, $clan_id) {

  if (empty($phone_id) || !is_numeric($clan_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_choose_clan', $phone_id, $clan_id);

}


function celestial_glory_help_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_help', $phone_id);

}


function celestial_glory_changelog_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_changelog', $phone_id);

}


function celestial_glory_prize_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_prize_list', $phone_id);

}


function celestial_glory_debates_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_debates_list', $phone_id);

}


function celestial_glory_debates_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_debates_challenge', $phone_id, $position_id);

}


function celestial_glory_elections_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elections_list', $phone_id);

}


function celestial_glory_elections_challenge_callback($phone_id, $position_id) {

  if (empty($phone_id) || !is_numeric($position_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elections_challenge', $phone_id, $position_id);

}


function celestial_glory_top20_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_top20_list', $phone_id);

}


function celestial_glory_top_wards_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_top_wards_list', $phone_id);

}


function celestial_glory_top_aldermen_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_top_aldermen_list', $phone_id);

}


function celestial_glory_top_event_points_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_top_event_points', $phone_id);

}


function celestial_glory_choose_name_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_choose_name', $phone_id);

}


function celestial_glory_enter_referral_code_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_enter_referral_code', $phone_id);

}


function celestial_glory_aides_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_aides', $phone_id);

}


function celestial_glory_land_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_land_list', $phone_id);

}


function celestial_glory_land_buy_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_land_buy', $phone_id, $land_id, $quantity);

}


function celestial_glory_land_sell_callback($phone_id, $land_id, $quantity) {

  if (empty($phone_id) || !is_numeric($land_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_land_sell', $phone_id, $land_id, $quantity);

}


function celestial_glory_staff_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_staff_list', $phone_id);

}


function celestial_glory_staff_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_staff_hire', $phone_id, $staff_id, $quantity);

}


function celestial_glory_staff_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_staff_fire', $phone_id, $staff_id, $quantity);

}


function celestial_glory_agents_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_agents_list', $phone_id);

}


function celestial_glory_agents_hire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_agents_hire', $phone_id, $staff_id, $quantity);

}


function celestial_glory_agents_fire_callback($phone_id, $staff_id, $quantity) {

  if (empty($phone_id) || !is_numeric($staff_id) || !(is_numeric($quantity) ||
    ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_agents_fire', $phone_id, $staff_id, $quantity);

}


function celestial_glory_actions_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_actions_list', $phone_id);

}


function celestial_glory_actions_do_callback($phone_id, $action_id) {

  if (empty($phone_id) || !is_numeric($action_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_actions_do', $phone_id, $action_id);

}


function celestial_glory_equipment_list_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_equipment_list', $phone_id);

}


function celestial_glory_equipment_buy_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_equipment_buy', $phone_id, $equipment_id, $quantity);

}


function celestial_glory_equipment_sell_callback($phone_id, $equipment_id, $quantity) {

  if (empty($phone_id) || !is_numeric($equipment_id) ||
    !(is_numeric($quantity) || ($quantity === 'use-quantity'))) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_equipment_sell', $phone_id, $equipment_id, $quantity);

}


function celestial_glory_move_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_move', $phone_id, $neighborhood_id);

}


function celestial_glory_move_do_callback($phone_id, $neighborhood_id) {

  if (empty($phone_id) || !is_numeric($neighborhood_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_move_do', $phone_id, $neighborhood_id);

}


function celestial_glory_authenticate_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_authenticate', $phone_id);

}

/**
 * Used for playbooks that didn't authorize the app
 */

function celestial_glory_playbook_null_callback() {

  return theme('celestial_glory_playbook_null');

}

function celestial_glory_bounce_callback($phone_id) {

  return theme('celestial_glory_bounce', $phone_id);

}

function celestial_glory_elders_preference_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_elders_preferences', $phone_id);

}


function celestial_glory_zombie_move_callback($phone_id, $zombie_id, $hood_id) {

  if (empty($phone_id) || is_nan($zombie_id) || is_nan($hood_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_zombie_move', $phone_id, $zombie_id, $hood_id);

}


function celestial_glory_event_info_callback($phone_id) {

  if (empty($phone_id)) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_event_info', $phone_id);

}


function celestial_glory_event_prizes_callback($phone_id) {

  global $user;

  if (($user->roles[4] != 'web game access') ||
    ($phone_id != 'abc123')) {
    return drupal_access_denied();
  }

  return theme('celestial_glory_event_prizes', $phone_id);

}


function celestial_glory_error_callback($phone_id, $error_code) {

  return theme('celestial_glory_error', $phone_id, $error_code);

}


function celestial_glory_theme() {
  // theme "A" uses template "B" with args "C"

  return array(

    'celestial_glory_homepage' => array(
      'template' => 'celestial_glory_homepage',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_userprofile' => array(
      'template' => 'celestial_glory_userprofile',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_user_competencies' => array(
      'template' => 'celestial_glory_user_competencies',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_clan_list' => array(
      'template' => 'celestial_glory_clan_list',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'celestial_glory_clan_list_available' => array(
      'template' => 'celestial_glory_clan_list_available',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_clan_msg' => array(
      'template' => 'celestial_glory_clan_msg',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'celestial_glory_clan_announcements' => array(
      'template' => 'celestial_glory_clan_announcements',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'celestial_glory_class_list' => array(
      'template' => 'celestial_glory_class_list',
      'arguments' => array(
        'phone_id' => null,
        'class_id' => null,
      )
    ),

    'celestial_glory_block_user_toggle' => array(
      'template' => 'celestial_glory_block_user_toggle',
      'arguments' => array(
        'phone_id' => null,
        'phone_id_to_block' => null,
      )
    ),

    'celestial_glory_msg_delete' => array(
      'template' => 'celestial_glory_msg_delete',
      'arguments' => array(
        'phone_id' => null,
        'msg_id' => null,
      )
    ),

    'celestial_glory_clan_msg_delete' => array(
      'template' => 'celestial_glory_clan_msg_delete',
      'arguments' => array(
        'phone_id' => null,
        'msg_id' => null,
      )
    ),

    'celestial_glory_party_msg' => array(
      'template' => 'celestial_glory_party_msg',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_increase_skills' => array(
      'template' => 'celestial_glory_increase_skills',
      'arguments' => array(
        'phone_id' => null,
        'skill' => null,
      )
    ),

    'celestial_glory_quests_list' => array(
      'template' => 'celestial_glory_quests_list',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_quests_do' => array(
      'template' => 'celestial_glory_quests_do',
      'arguments' => array(
        'phone_id' => null,
        'quest_id' => null,
      )
    ),

    'celestial_glory_elders' => array(
      'template' => 'celestial_glory_elders',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_elders_ask_reset' => array(
      'template' => 'celestial_glory_elders_ask_reset',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_elders_do_reset' => array(
      'template' => 'celestial_glory_elders_do_reset',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_elders_ask_reset_skills' => array(
      'template' => 'celestial_glory_elders_ask_reset_skills',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_elders_do_reset_skills' => array(
      'template' => 'celestial_glory_elders_do_reset_skills',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_elders_do_fill' => array(
      'template' => 'celestial_glory_elders_do_fill',
      'arguments' => array(
        'phone_id' => null,
        'fill_type' => null,
      )
    ),

    'celestial_glory_elders_rename' => array(
      'template' => 'celestial_glory_elders_rename',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elders_set_password' => array(
      'template' => 'celestial_glory_elders_set_password',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elders_ask_purchase' => array(
      'template' => 'celestial_glory_elders_ask_purchase',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elders_do_purchase' => array(
      'template' => 'celestial_glory_elders_do_purchase',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_check_receipt' => array(
      'template' => 'celestial_glory_elders_check_receipt',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_google_checkout' => array(
      'template' => 'celestial_glory_google_checkout',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_welcome' => array(
      'template' => 'celestial_glory_welcome',
      'arguments' => array(
        'phone_id' => null
      )
    ),

    'celestial_glory_choose_clan' => array(
      'template' => 'celestial_glory_choose_clan',
      'arguments' => array(
        'phone_id' => null,
        'clan_id' => null,
      )
    ),

    'celestial_glory_help' => array(
      'template' => 'celestial_glory_help',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_changelog' => array(
      'template' => 'celestial_glory_changelog',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_prize_list' => array(
      'template' => 'celestial_glory_prize_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elections_list' => array(
      'template' => 'celestial_glory_elections_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elections_challenge' => array(
      'template' => 'celestial_glory_elections_challenge',
      'arguments' => array(
        'phone_id' => null,
        'position_id' => null,
      )
    ),

    'celestial_glory_debates_list' => array(
      'template' => 'celestial_glory_debates_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_debates_challenge' => array(
      'template' => 'celestial_glory_debates_challenge',
      'arguments' => array(
        'phone_id' => null,
        'position_id' => null,
      )
    ),

    'celestial_glory_top20_list' => array(
      'template' => 'celestial_glory_top20_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_top_wards_list' => array(
      'template' => 'celestial_glory_top_wards_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_top_aldermen_list' => array(
      'template' => 'celestial_glory_top_aldermen_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_top_event_points' => array(
      'template' => 'celestial_glory_top_event_points',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_choose_name' => array(
      'template' => 'celestial_glory_choose_name',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_enter_referral_code' => array(
      'template' => 'celestial_glory_enter_referral_code',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_aides' => array(
      'template' => 'celestial_glory_aides',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_land_list' => array(
      'template' => 'celestial_glory_land_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_land_buy' => array(
      'template' => 'celestial_glory_land_buy',
      'arguments' => array(
        'phone_id' => null,
        'land_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_land_sell' => array(
      'template' => 'celestial_glory_land_sell',
      'arguments' => array(
        'phone_id' => null,
        'land_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_staff_list' => array(
      'template' => 'celestial_glory_staff_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_staff_hire' => array(
      'template' => 'celestial_glory_staff_hire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_staff_fire' => array(
      'template' => 'celestial_glory_staff_fire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_agents_list' => array(
      'template' => 'celestial_glory_agents_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_agents_hire' => array(
      'template' => 'celestial_glory_agents_hire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_agents_fire' => array(
      'template' => 'celestial_glory_agents_fire',
      'arguments' => array(
        'phone_id' => null,
        'staff_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_actions_list' => array(
      'template' => 'celestial_glory_actions_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_actions_do' => array(
      'template' => 'celestial_glory_actions_do',
      'arguments' => array(
        'phone_id' => null,
        'action_id' => null,
      )
    ),

    'celestial_glory_equipment_list' => array(
      'template' => 'celestial_glory_equipment_list',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_equipment_buy' => array(
      'template' => 'celestial_glory_equipment_buy',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_equipment_sell' => array(
      'template' => 'celestial_glory_equipment_sell',
      'arguments' => array(
        'phone_id' => null,
        'equipment_id' => null,
        'quantity' => null,
      )
    ),

    'celestial_glory_move' => array(
      'template' => 'celestial_glory_move',
      'arguments' => array(
        'phone_id' => null,
        'neighborhood_id' => null,
      )
    ),

    'celestial_glory_move_do' => array(
      'template' => 'celestial_glory_move_do',
      'arguments' => array(
        'phone_id' => null,
        'neighborhood_id' => null,
      )
    ),

    'celestial_glory_authenticate' => array(
      'template' => 'celestial_glory_authenticate',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_playbook_null' => array(
      'template' => 'celestial_glory_playbook_null',
    ),

    'celestial_glory_bounce' => array(
      'template' => 'celestial_glory_bounce',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_elders_preferences' => array(
      'template' => 'celestial_glory_elders_preferences',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_zombie_move' => array(
      'template' => 'celestial_glory_zombie_move',
      'arguments' => array(
        'phone_id' => null,
        'zombie_id' => null,
        'hood_id' => null,
      )
    ),

    'celestial_glory_event_info' => array(
      'template' => 'celestial_glory_event_info',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_event_prizes' => array(
      'template' => 'celestial_glory_event_prizes',
      'arguments' => array(
        'phone_id' => null,
      )
    ),

    'celestial_glory_error' => array(
      'template' => 'celestial_glory_error',
      'arguments' => array(
		'phone_id' => null,
		'error_code' => null,
      )
    ),

  );

} // celestial_glory_theme()

