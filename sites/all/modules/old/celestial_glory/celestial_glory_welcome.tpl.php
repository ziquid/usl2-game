<?php

  global $game, $phone_id;

// we won't have gone through fetch_user() yet, so set these here
  $game = check_plain(arg(0));
  $get_phoneid = '_' . $game . '_get_phoneid';
  $phone_id = $get_phoneid();
  $arg2 = check_plain(arg(2));

  if ((arg(0) == 'celestial_glory') && ($arg2 == 'facebook')) {
//   echo 'welcome facebook user!'; exit;
  }

  db_set_active('game_' . $game);

  if ($game == 'stlouis') {
    $default_neighborhood = 81;
    $default_value = 'Greenbacks';
  }

  if ($game == 'celestial_glory') {

    $sql = 'SELECT count(users.id) as count,
      fkey_neighborhoods_id from users
      left join neighborhoods on neighborhoods.id = users.fkey_neighborhoods_id
       group by fkey_neighborhoods_id
      order by count asc
      limit 1;';

    $result = db_query($sql);
    $item = db_fetch_object($result);
    $default_neighborhood = $item->fkey_neighborhoods_id;
    $default_value = 'Faith';
  }

  if ($game == 'robber_barons') {
    $default_neighborhood = 1;
    $default_value = 'Faith';
  }

// check to make sure not too many from the same IP address
  $sql = 'select count(`value`) as count from user_attributes
    where `key` = "last_IP" and `value` = "%s";';
  $result = db_query($sql, ip_address());
  $item = db_fetch_object($result);
/* turn off while Amazon is testing -- jwc 10May2014
// allow multiple from my IP*/
  if (($item->count > 5) && (ip_address() != '14.140.251.170') && // Amazon testing IP
    (ip_address() != '38.164.20.244') && // TI
    (ip_address() != '158.69.123.231') && // OVH2
    (ip_address() != '64.150.187.146')) {
    db_set_active('default');
    drupal_goto($game . '/error/' . $arg2 . '/E-2242');
  }

  $sql = 'insert into users set phone_id = "%s", username = "", experience = 0,
    level = 1, fkey_neighborhoods_id = %d, fkey_values_id = 0,
    `values` = "%s",
    money = 500, energy = 200, energy_max = 200';
  $result = db_query($sql, $phone_id, $default_neighborhood,
    $default_value);

  $sql = 'insert into user_creations set datetime = "%s", phone_id = "%s",
    remote_ip = "%s";';
  $result = db_query($sql, date('Y-m-d H:i:s'), $phone_id, ip_address());

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $game_user = $fetch_user();

  echo <<< EOF
<div class="title">
<img src="/sites/default/files/images/{$game}_title.png"/>
</div>
<p>&nbsp;</p>
<div class="welcome">
  <div class="wise_old_man_large">
  </div>
EOF;

  if ($game == 'stlouis') {

    echo <<< EOF

  <p>A wizened old man comes up to you.&nbsp; You recognize him as one of the
    elders of the city.</p>
  <p class="second">&quot;I've been watching you for some time,
    and I like what I see.&nbsp; I think you have the potential for
    greatness.&nbsp; Maybe you could even lead this city.&quot;</p>
  <p class="second">Could you?</p>
  <div class="subtitle">
    How to play
  </div>
  <ul>
    <li>Finish missions to earn skills and influence</li>
    <li>Cooperate and compete with other players to achieve your goals</li>
    <li>Purchase equipment and businesses to win votes</li>
    <li>Become a city elder, political party leader, and then mayor</li>
  </ul>

EOF;

  }

  if ($game == 'celestial_glory') {

    echo <<< EOF

      <p>You are met in a dream by an old man.</p>
      <p class="second">&quot;Hello.&nbsp; My name is Lehi and I have come to
        tell you my story.</p>
      <p class="second">&quot;Six hundred years before the coming of Jesus
        Christ, I was living in Jerusalem with my family when I saw a
        vision.&nbsp; God showed me many things and asked me to follow his
        direction.</p>
      <p class="second">&quot;I have come to tell you this story now.&nbsp; You
        will learn the story by playing the part of my son, Nephi, who became
        the leader of a great number of people.</p>
      <p class="second">&quot;Listen to my story and you, too, can become
        great.&quot;</p>
      <div class="subtitle">
        How to play
      </div>
      <ul>
        <li>Play the part of Nephi, a son of Lehi</li>
        <li>Perform the game's quests to increase your abilities</li>
        <li>Prepare yourself for the challenges and trials that you will face</li>
        <li>Lead your family to the promised land!</li>
      </ul>

EOF;

  }

  if ($game == 'robber_barons') {

    echo <<< EOF

  <p>You approach the old man respectfully, with your cap in hand.&nbsp;
    You figure if you're going to make it big in industry, you better learn
    from the best.
  </p>
  <p class="second">&quot;So you've come for advice from the town's richest man,
    eh?&nbsp; Well, I may be an old man now, but once upon a time I was young
    and full of get up and go just like you.&nbsp; I've been watching you and I
    think you have what it takes to succeed.&nbsp; Maybe you could even become
    one of the richest people in the country.&quot;</p>
  <p class="second">Could you?</p>
  <div class="subtitle">
    How to play
  </div>
  <ul>
    <li>Finish tasks to earn money and influence</li>
    <li>Cooperate and compete with other players to achieve your goals</li>
    <li>Purchase equipment and businesses to win market share</li>
    <li>Become a business leader, a Captain of Industry, and then a Tycoon</li>
  </ul>

EOF;

  }

  echo <<< EOF

</div>
<div class="subtitle">
  <a href="/$game/quests/$arg2">
    <img src="/sites/default/files/images/{$game}_continue.png"/>
  </a>
</div>

EOF;

  db_set_active('default');
