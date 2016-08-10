<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  echo <<< EOF

<div class="news">
  <a href="/$game/help/$arg2" class="button active">Help</a>
  <a href="/$game/changelog/$arg2" class="button">Changelog</a>
</div>

<div class="help">
<div class="title">
  Help / FAQ
</div>
<div class="subtitle">
	Terms of Service
</div>
<p>Since this product includes user-generated content (ie, you can choose
  your own usernames and send each other messages),
  we need to tell you that we moderate these for appropriateness.&nbsp;
  We have a filter in place for profanity on user messages; you can block
  users from sending you messages at all; and you can
  send e-mail to us at zipport@ziquid.com if you have complaints or
  need assistance.&nbsp; We occasionally delete user accounts for abuse.</p>
<div class="subtitle">
	Goal of the Game
</div>
<p>Your goal is to build your character to as high a level as you can.&nbsp;
  Gain experience by completing {$quest_lower}s and participating in
  {$debate_lower}s.</p>
<div class="subtitle">
	{$quest}s
</div>
<p>{$quest}s help you become more experienced, both in $game_user->values
  and in $experience.</p>
<div class="subtitle">
	{$debate}s
</div>
<p>{$debate}s match your wits against other players across the game.&nbsp; Those
  players with the highest $experience and $elocution win the most {$debate_lower}s.</p>
EOF;

  if ($game != 'celestial_glory') {

    echo <<< EOF
<div class="subtitle">
	Elections
</div>
<p>Once you become influential enough you can participate in elections to
  gain even more experience.&nbsp; If you become elected to a position, you
  get in-game bonuses.&nbsp; You also get bonuses if you belong to the
  same political party as the elected officials in your neighborhood.</p>
EOF;

  }

  echo <<< EOF
<div class="subtitle">
	Aides
</div>
<p>Aides are non-playing characters and other items that help you attain your
  goals.&nbsp; They could be {$land_plural_lower} who generate income for you or people
  who allow you to perform more actions.</p>
EOF;

  if ($game != 'celestial_glory') {

    echo <<< EOF
<div class="subtitle">
	Staff
</div>
<p>Staff are aides who help you in public, legitimate ways.&nbsp; They may
	provide $initiative_lower or endurance bonuses or make new actions available.</p>
<div class="subtitle">
	Agents
</div>
<p>Agents are aides who help you in covert, clandestine, or illegal ways.&nbsp;
  They also may provide $initiative_lower or endurance bonuses or make new actions
  available.</p>
EOF;

  }

  echo <<< EOF
<div class="subtitle">
	Values
</div>
<p>Values, such as <em>$game_user->values</em>, are shown in the top-left corner
  of the screen.&nbsp; Use them to win and purchase Aides whom will help you
  throughout the game.</p>
<div class="subtitle">
	$experience
</div>
EOF;

  if ($game != 'celestial_glory') {

  echo <<< EOF
<p>$experience is a measure of how popular you are.&nbsp; The higher your
  $experience_lower, the better chance you have of winning elections, whether you
  are the challenger or the incumbent.</p>
EOF;

  } else {

  echo <<< EOF
<p>$experience is a measure of how far you have progressed in the game.&nbsp;
  The higher your $experience, the more actions will be available to you.</p>
EOF;

  }

  echo <<< EOF
<div class="subtitle">
  $initiative
</div>
EOF;

  if ($game != 'celestial_glory') {

  echo <<< EOF
<p>$initiative is a measure of how well you conquer new projects.&nbsp; The
  higher your $initiative, the better chance you have of winning elections
  when you are the challenger.</p>
EOF;

  } else {

  echo <<< EOF
<p>$initiative is a measure of how well you conquer new projects.&nbsp; The
  higher your $initiative, the better chance you have of finding out about
  your ancestors.</p>
EOF;

  }

  echo <<< EOF
<div class="subtitle">
  $endurance
</div>
<p>$endurance is a measure of how well you complete existing projects.&nbsp; The
  higher your $endurance, the better chance you have of winning elections
  when you are the incumbent.</p>
<div class="subtitle">
	$elocution
</div>
EOF;

  if ($game != 'celestial_glory') {

    echo <<< EOF
<p>$elocution is a measure of your public speaking ability.&nbsp; The higher your
  $elocution, the better chance you have of winning {$debate_lower}s, whether you
  or another player initiates them.</p>
</div>
EOF;

  } else {

    echo <<< EOF
<p>$elocution is a measure of your wits and intellect.&nbsp; The higher your
  $elocution, the better chance you have of winning {$debate_lower}s, whether you
  or another player initiates them.</p>
</div>
EOF;

  }

  db_set_active('default');
