<?php

  define(EVENT_NONE, 0);
  define(EVENT_DONE, 1);
  define(EVENT_DEBATE, 2);
  define(EVENT_CG_VALENTINES, 1001);
  define(EVENT_PRE_APR1, 1002);
  define(EVENT_QUESTS_100, 1003);
  define(EVENT_PRE_MERCH_QUESTS, 1004);
  define(EVENT_MERCH_QUESTS, 1005);
  define(EVENT_GATHER_AMETHYST, 1006);
  define(EVENT_AMETHYST_DONE, 1007);
  define(EVENT_SPEED_ENERGY, 1008);
  define(EVENT_MOTHERS_DAY, 1009);

  $debate_time = 1200;
  $zombie_debate_wait = 180; // 300
  $event_type = EVENT_NONE;
  $energy_wait = 240;
  $energy_wait_str = '4 minutes';

  global $game;
firep('$game in game_defs.inc is :' . $game . ':');
/*
  if ($game == 'stlouis') {

    switch(date('Y-m-d', time() - 3600)) {

      case '2013-03-08':
        $event_type = EVENT_DEBATE;
        $debate_time = 600; // 900; // 1200;
        break;

      case '2013-03-09':
        $event_type = EVENT_DONE;
        break;

    }

    $alderman = 'Alderman';
    $aldermen = 'Aldermen';
    $alder_short = 'Alder';
    $alders_short = 'Alders';
    $beauty = 'Beauty';
    $city = 'City';
    $debate = 'Debate';
    $debate_tab = 'Debates';
    $elders = 'Elders';
    $election = 'Election';
    $election_tab = 'Election';
    $elocution = 'Elocution';
    $endurance = 'Endurance';
    $equipment = 'Equipment';
    $experience = 'Influence';
    $experience_short = 'Inf';
    $game_name_full = 'Uprising: St. Louis';
    $hood = 'Neighborhood';
    $initiative = 'Initiative';
    $land = 'Business';
    $land_plural = 'Businesses';
    $luck = 'Luck';
    $party = 'Political Party';
    $party_small = 'Party';
    $politics = 'Politics';
    $quest = 'Mission';
    $residence = 'Residence';
    $system = 'System';
    $top20 = 'Top 20';

// boxing day
    if ((date('Y-m-d') == '2012-12-26') || arg(2) == 'abc123') {
      $debate = 'Box';
      $debate_tab = 'Boxing';
      $top20 = 'Top Boxers';
    }

  }
*/
  if ($game == 'celestial_glory') {

    $date = date('Y-m-d');

    if (($date >= '2016-03-30') && ($date <= '2016-03-31'))
      $event_type = EVENT_PRE_APR1;

    if ($date == '2016-04-01') $event_type = EVENT_QUESTS_100;

    if (($date >= '2016-04-02') && ($date <= '2016-04-05'))
      $event_type = EVENT_PRE_MERCH_QUESTS;

    if (($date >= '2016-04-06') && ($date <= '2016-04-14'))
      $event_type = EVENT_MERCH_QUESTS;

    if (($date >= '2016-04-15') && ($date <= '2016-04-18'))
      $event_type = EVENT_GATHER_AMETHYST;

    if ($date == '2016-04-19') $event_type = EVENT_AMETHYST_DONE;

    if (($date >= '2016-04-29') && ($date <= '2016-05-02')) {
      $event_type = EVENT_SPEED_ENERGY;
      $energy_wait = 60;
      $energy_wait_str = '1 minute';
    }

    if (($date >= '2016-05-06') && ($date <= '2016-05-09')) {
      $event_type = EVENT_MOTHERS_DAY;
    }

    $alderman = 'Chief Priest';
    $aldermen = 'Chief Priests';
    $alder_short = 'CP';
    $alders_short = 'CPs';
    $beauty = 'Piety';
    $city = 'Land';
    $competencies = 'Competencies';
    $debate = 'Challenge';
    $debate_tab = 'Challenges';
    $elders = 'Elders';
    $election = 'Challenge';
    $election_tab = 'Offices';
    $elocution = 'Cunning';
    $endurance = 'Defense';
    $equipment = 'Supplies for Use';
    $experience = 'Spirituality';
    $experience_short = 'Spir';
    $game_name_full = 'Celestial Glory';
    $hood = 'Region';
    $initiative = 'Attack';
    $land = 'Goods for Trade or Barter';
    $land_plural = 'Goods for Trade or Barter';
    $luck = 'Luck';
    $party = 'Family';
    $party_small = 'Family';
    $politics = 'Heritage';
    $quest = 'Quest';
    $residence = 'Residence';
    $slack_channel = 'cg-data';
    $system = 'System';
    $top20 = 'Top 20';

  }
/*
  if ($game == 'robber_barons') {

    $beauty = 'Piety';
    $debate = 'Debate';
    $elders = 'Elders';
    $election = 'Election';
    $election_tab = 'Election';
    $elocution = 'Elocution';
    $equipment = 'Equipment';
    $experience = 'Influence';
    $experience_short = 'Inf';
    $game_name_full = 'Robber Barons: The Gilded Age';
    $hood = 'Neighborhood';
    $initiative = 'Initiative';
    $land = 'Business';
    $land_plural = 'Businesses';
    $luck = 'Luck';
    $party = 'Political Party';
    $party_small = 'Party';
    $politics = 'Politics';
    $quest = 'Task';
    $residence = 'Residence';
    $system = 'System';

  }
*/
  $beauty_lower = strtolower($beauty);
  $city_lower = strtolower($city);
  $debate_lower = strtolower($debate);
  $election_lower = strtolower($election);
  $election_tab_lower = strtolower($election_tab);
  $elocution_lower = strtolower($elocution);
  $equipment_lower = strtolower($equipment);
  $experience_lower = strtolower($experience);
  $experience_short_lower = strtolower($experience_short);
  $hood_lower = strtolower($hood);
  $initiative_lower = strtolower($initiative);
  $land_lower = strtolower($land);
  $land_plural_lower = strtolower($land_plural);
  $party_lower = strtolower($party);
  $party_small_lower = strtolower($party_small);
  $politics_lower = strtolower($politics);
  $quest_lower = strtolower($quest);
  $residence_lower = strtolower($residence);
  $system_lower = strtolower($system);

