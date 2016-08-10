<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));
  $ai_output = 'increase-skill-failed';
  
  switch ($skill) {
    
    case 'initiative':
    case 'endurance':
    case 'elocution':
      
      if ($game_user->skill_points >= 1) {

        $sql = 'update users set %s = %s + 1, skill_points = skill_points - 1
          where id = %d;';
        $result = db_query($sql, $skill, $skill, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;

    case 'elocution_10':
      
      if ($game_user->skill_points >= 10) {

        $sql = 'update users set elocution = elocution + 10,
	  skill_points = skill_points - 10
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;

    case 'endurance_10':
      
      if ($game_user->skill_points >= 10) {

        $sql = 'update users set endurance = endurance + 10,
	  skill_points = skill_points - 10
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;
      
      case 'initiative_10':
      
      if ($game_user->skill_points >= 10) {

        $sql = 'update users set initiative = initiative + 10,
          skill_points = skill_points - 10
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;

    case 'energy_max':
      
      if ($game_user->skill_points >= 1) {

        $sql = 'update users set energy = energy + 10,
          energy_max = energy_max + 10, skill_points = skill_points - 1
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;

    case 'energy_100':
      
      if ($game_user->skill_points >= 10) {

        $sql = 'update users set energy = energy + 100,
          energy_max = energy_max + 100, skill_points = skill_points - 10
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;
      
    case 'actions_5':
      
      if ($game_user->skill_points >= 10) {

        $sql = 'update users set actions = actions + 5,
          actions_max = actions_max + 5, skill_points = skill_points - 10
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        
// start the actions clock if needed
        if ($game_user->actions == $game_user->actions_max) {
    
          $sql = 'update users set actions_next_gain = "%s" where id = %d;';
          $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
            $game_user->id);
             
        }
        
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;
      
    case 'actions':
      
      if ($game_user->skill_points >= 2) {

        $sql = 'update users set actions = actions + 1,
          actions_max = actions_max + 1, skill_points = skill_points - 2
          where id = %d;';
        $result = db_query($sql, $game_user->id);
        
// start the actions clock if needed
        if ($game_user->actions == $game_user->actions_max) {
    
          $sql = 'update users set actions_next_gain = "%s" where id = %d;';
          $result = db_query($sql, date('Y-m-d H:i:s', time() + 180),
            $game_user->id);
             
        }
        
        $game_user = $fetch_user();
        $ai_output = 'increase-skill-succeeded';
      
      }
      
      break;

    case 'none':

      $ai_output = 'increase-skill-shown';
      break;

  }

  $fetch_header($game_user);
  
  echo <<< EOF
<ul>
  <li>Use skill points to increase your character's abilities</li>
  <li>All abilities cost 1 point to increase; Actions cost 2</li>
</ul>
<div class="title">
  Skill Points Remaining: $game_user->skill_points
</div>
<div class="user-profile">
EOF;

  if ($game_user->skill_points == 0) {
    
    echo <<< EOF
  <div class="heading">Energy:</div>
  <div class="value">$game_user->energy_max <div class="action not-yet">Can't
    increase Energy</div></div><br/>
    
  <div class="heading">$initiative:</div>
  <div class="value">$game_user->initiative <div class="action not-yet">Can't
    increase $initiative</div></div><br/>
    
  <div class="heading">$endurance:</div>
  <div class="value">$game_user->endurance <div class="action not-yet">Can't
    increase $endurance</div></div><br/>
    
  <div class="heading">$elocution:</div>
  <div class="value">$game_user->elocution <div class="action not-yet">Can't
    increase $elocution</div></div><br/>
    
    <div class="heading">Actions:</div>
  <div class="value">$game_user->actions_max <div class="action not-yet">Can't
    increase Actions</div></div><br/>
</div>
EOF;

  } elseif ($game_user->skill_points == 1) {

    echo <<< EOF
  <div class="heading">Energy:</div>
  <div class="value">$game_user->energy_max <div class="action"><a
    href="/$game/increase_skills/$arg2/energy_max">Increase</a></div></div><br/>
    
  <div class="heading">$initiative:</div>
  <div class="value">$game_user->initiative <div class="action"><a
    href="/$game/increase_skills/$arg2/initiative">Increase</a></div></div><br/>
    
  <div class="heading">$endurance:</div>
  <div class="value">$game_user->endurance <div class="action"><a
    href="/$game/increase_skills/$arg2/endurance">Increase</a></div></div><br/>
    
  <div class="heading">$elocution:</div>
  <div class="value">$game_user->elocution <div class="action"><a
    href="/$game/increase_skills/$arg2/elocution">Increase</a></div></div><br/>
    
    <div class="heading">Actions:</div>
  <div class="value">$game_user->actions_max <div class="action not-yet">Can't
    increase Actions</div></div><br/>
</div>
EOF;
    
  } elseif ($game_user->skill_points >= 10) {

        echo <<< EOF
  <div class="heading">Energy:</div>
  <div class="value">$game_user->energy_max <div class="action"><a
    href="/$game/increase_skills/$arg2/energy_max">Increase</a></div></div>
  <div class="value"><div class="action"><a
    href="/$game/increase_skills/$arg2/energy_100">Inc +100</a></div></div><br/>
    
  <div class="heading">$initiative:</div>
  <div class="value">$game_user->initiative <div class="action"><a
    href="/$game/increase_skills/$arg2/initiative">Increase</a></div></div>
  <div class="value"><div class="action"><a
    href="/$game/increase_skills/$arg2/initiative_10">Increase +10</a></div></div><br/>

  <div class="heading">$endurance:</div>
  <div class="value">$game_user->endurance <div class="action"><a
    href="/$game/increase_skills/$arg2/endurance">Increase</a></div></div>
  <div class="value"><div class="action"><a
    href="/$game/increase_skills/$arg2/endurance_10">Increase +10</a></div></div><br/>

  <div class="heading">$elocution:</div>
  <div class="value">$game_user->elocution <div class="action"><a
    href="/$game/increase_skills/$arg2/elocution">Increase</a></div></div>
  <div class="value"><div class="action"><a
    href="/$game/increase_skills/$arg2/elocution_10">Increase +10</a></div></div><br/>

  <div class="heading">Actions:</div>
  <div class="value">$game_user->actions_max <div class="action"><a
    href="/$game/increase_skills/$arg2/actions">Increase</a></div></div>
  <div class="value"><div class="action"><a
    href="/$game/increase_skills/$arg2/actions_5">Increase +5</a></div></div><br/>
</div>
EOF;
    
  } elseif ($game_user->skill_points > 1) {

        echo <<< EOF
  <div class="heading">Energy:</div>
  <div class="value">$game_user->energy_max <div class="action"><a
    href="/$game/increase_skills/$arg2/energy_max">Increase</a></div></div><br/>
    
  <div class="heading">$initiative:</div>
  <div class="value">$game_user->initiative <div class="action"><a
    href="/$game/increase_skills/$arg2/initiative">Increase</a></div></div><br/>
    
  <div class="heading">$endurance:</div>
  <div class="value">$game_user->endurance <div class="action"><a
    href="/$game/increase_skills/$arg2/endurance">Increase</a></div></div><br/>

  <div class="heading">$elocution:</div>
  <div class="value">$game_user->elocution <div class="action"><a
    href="/$game/increase_skills/$arg2/elocution">Increase</a></div></div><br/>

  <div class="heading">Actions:</div>
  <div class="value">$game_user->actions_max <div class="action"><a
    href="/$game/increase_skills/$arg2/actions">Increase</a></div></div><br/>
</div>
EOF;
    
  }
  
  if (substr($phone_id, 0, 3) == 'ai-')
    echo "<!--\n<ai \"$ai_output\"/>\n-->";

  db_set_active('default');
