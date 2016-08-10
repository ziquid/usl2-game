<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include_once(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  echo <<< EOF
<div class="title">
  <a href="/$game/top_event_points/$arg2">Leaderboard</a>
</div>
<div class="title">
  <img src="/sites/default/files/images/VD_massacre.png" border=0>
</div>
<div class="subtitle">Event Feb 13th - 15th</div>
<div class="event-tagline">
  &mdash; D O N ' T &mdash; G E T &mdash; T A G G E D &mdash;
</div>

<div class="event-info">
<div class="subtitle">
The Event
</div>

<div class="subsubtitle">
Enjoy St. Valentine's Day with your St. Louis peeps, playing a game of Freeze
Tag, USL-style.
</div>

<div class="subtitle">
The Goal
</div>

<div class="subsubtitle">
Gain as may points as possible by tagging players from other parties with
your paintball gun.&nbsp; Move around to keep from being tagged yourself.
<br/><br/>
Each consecutive tag earns you more points.&nbsp; Each time you get tagged,
your win streak resets to 0 and you must wait for a teammate to unfreeze you.
<br/><br/>
You also get points for unfreezing teammates.
</div>

<div class="subtitle">
The Rewards
</div>

<div class="subsubtitle">
Presents, including money, free businesses, and free equipment await the top
100 taggers.&nbsp; There will also be prizes for hitting point milestones,
such as 10 points, 20 points, 50 points, and so on.
</div>

<div class="subtitle">
How to Play
</div>

<div class="subsubtitle">
<strong>Step 1:</strong>
Purchase a paintball gun, some ammunition, and some first aid
kits.&nbsp; The gun and first aid kits can be purchased anywhere, but the
ammo must be purchased in Midtown.
</div>

<div class="subsubtitle">
<strong>Step 2:</strong>
Start tagging!&nbsp; You can tag anyone in your current
neighborhood who isn't in your political party.&nbsp; Midtown and the parks
are neutral zones; no tagging can occur there.
</div>

<div class="subsubtitle">
<strong>Step 3:</strong>
You cannot tag anyone if you run out of ammo or if you get tagged.&nbsp;
If you get tagged, you will need to have someone else in your party find you
are unfreeze you.&nbsp; Each tag uses one ammo; each unfreeze uses one
first aid kit.
</div>

<div class="subsubtitle">
<strong>Step 4:</strong>
Watch your points and ranking.&nbsp; You get presents for hitting certain
point milestones, and the top 100 ranked
players at the end of the event will receive
more presents.
</div>

<div class="subtitle">
Strategy Tips
</div>

<div class="subsubtitle">
Don't stay in one neighborhood for too long.
</div>

<div class="subsubtitle">
Decide who will be tagging and who will be unfreezing.&nbsp;
Both will need to occur in order for your team to be successful.&nbsp;
A single player can do both.
</div>

<div class="subsubtitle">
Decide who will be tagging and who will be unfreezing.&nbsp;
Both will need to occur in order for your team to be successful.
</div>

<div class="subtitle">
Notes
</div>

<div class="subsubtitle">
Both Tag and Unfreeze are Actions.&nbsp; Go to the Actions screen to do them.
</div>

<div class="subsubtitle">
If you are tagged, you become frozen,
and cannot tag nor perform any actions until you are unfrozen by a teammate.
</div>

<div class="subtitle">

</div>

<div class="subsubtitle">

</div>

<div class="subtitle">

</div>

<div class="subsubtitle">

</div>

<div class="subtitle">
&nbsp;
</div>

<div class="subsubtitle">
&nbsp;
</div>

</div>
EOF;

  db_set_active('default');
