<?php

  @define('EVENT_NONE', 0);
  @define('EVENT_DONE', 1);
  @define('EVENT_DEBATE', 2);


// what to do if we don't have enough of something
  @define('LACKING_SOMETHING_FAIL', 0); // fail if we don't have enough
  @define('LACKING_SOMETHING_USE_ALL', 1); // use all we have


  $debate_time = 900;
  $zombie_debate_wait = 180; // 300
  $event_type = EVENT_NONE;

  switch(date('Y-m-d', time() - 3600)) {

    case '2013-03-08':
      $event_type = EVENT_DEBATE;
      $debate_time = 600; // 900; // 1200;
      break;

    case '2013-03-09':
      $event_type = EVENT_DONE;
      break;

  }

  $txt = array(
    '@actions' => 'actions',
    '@energy' => 'energy',
    '@party' => 'political party',
    '@party_small' => 'party',
  );
/*
  $alderman = 'Alderman';
  $aldermen = 'Aldermen';
  $alder_short = 'Officiate';
  $alders_short = 'Officiates';
  $beauty = 'Beauty';
  $competencies = 'Competencies';
  $debate = 'Debate';
  $debate_tab = 'Debates';
  $debated_lower = 'clashed with';
  $debaters = 'Debaters';
  $debates = 'Debates';
  $elders = 'Elders';
  $election = 'Hierarchy';
  $election_tab = 'Hierarchies';
  $elocution = 'Courage'; // 'Favor Slots';
  $endurance = 'Defense';
  $ep_full = 'Social Ranking';
  $ep_short = 'Social Rank';
  $equipment = 'Materials';
  $experience = 'Experience';
  $experience_short = 'Exp';
  $favor = 'Favor';
  $favors_tab = 'Favors';
  $game_name_full = 'Uprising: St. Louis 2';
  $hood = 'Neighborhood';
  $hood_small = 'Hood';
  $initiative = 'Initiative';
  $land = 'Income';
  $land_plural = 'Income';
  $luck = 'Luck';
  $party = 'Party';
  $parties = 'Parties';
  $party_small = 'Party';
  $prestige = 'Prestige';
  $politics = 'Politics';
  $quest = 'Objective';
  $quests = 'Objectives';
  $residence = 'Residence';
  $system = 'System';
  $top20 = 'Top 20';
  $weapons = 'Weapons';
*/
/*
// boxing day
    if ((date('Y-m-d') == '2012-12-26') || arg(2) == 'abc123') {
      $debate = 'Box';
      $debate_tab = 'Boxing';
      $top20 = 'Top Boxers';
    }
*/
/*
  $beauty_lower = strtolower($beauty);
  $debate_lower = strtolower($debate);
  $debates_lower = strtolower($debates);
  $election_lower = strtolower($election);
  $election_tab_lower = strtolower($election_tab);
  $elocution_lower = strtolower($elocution);
  $equipment_lower = strtolower($equipment);
  $experience_lower = strtolower($experience);
  $experience_short_lower = strtolower($experience_short);
  $favor_lower = strtolower($favor);
  $hood_lower = strtolower($hood);
  $initiative_lower = strtolower($initiative);
  $land_lower = strtolower($land);
  $land_plural_lower = strtolower($land_plural);
  $party_lower = strtolower($party);
  $parties_lower = strtolower($parties);
  $party_small_lower = strtolower($party_small);
  $politics_lower = strtolower($politics);
  $prestige_lower = strtolower($prestige);
  $quest_lower = strtolower($quest);
  $quests_lower = strtolower($quests);
  $residence_lower = strtolower($residence);
  $system_lower = strtolower($system);
*/
