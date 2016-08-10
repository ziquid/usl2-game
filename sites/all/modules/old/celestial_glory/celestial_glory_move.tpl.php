<?php

  global $game, $phone_id;

  $fetch_user = '_' . arg(0) . '_fetch_user';
  $fetch_header = '_' . arg(0) . '_header';

  $game_user = $fetch_user();
  $fetch_header($game_user);
  include(drupal_get_path('module', $game) . '/game_defs.inc');
  $arg2 = check_plain(arg(2));

  $sql = 'select name from neighborhoods where id = %d;';
  $result = db_query($sql, $game_user->fkey_neighborhoods_id);
  $item = db_fetch_object($result);
  $location = $item->name;

  if ($neighborhood_id == $game_user->fkey_neighborhoods_id) {

    echo <<< EOF
<div class="title">You are already in $location</div>
<div class="election-continue"><a href="0">Try again</a></div>
EOF;

    db_set_active('default');
    return;

  }

  if ($neighborhood_id > 0) {

    $sql = 'select * from neighborhoods where id = %d;';
    $result = db_query($sql, $game_user->fkey_neighborhoods_id);
    $cur_hood = db_fetch_object($result);
//firep($cur_hood);

    $sql = 'select * from neighborhoods where id = %d;';
    $result = db_query($sql, $neighborhood_id);
    $new_hood = db_fetch_object($result);
//firep($new_hood);

    $distance = floor(sqrt(pow($cur_hood->xcoor - $new_hood->xcoor, 2) +
      pow($cur_hood->ycoor - $new_hood->ycoor, 2)));

    $actions_to_move = floor($distance / 8);
    $verb = t('Walk');

    $sql = 'SELECT equipment.speed_increase as speed_increase,
      action_verb from equipment

      left join equipment_ownership
        on equipment_ownership.fkey_equipment_id = equipment.id
        and equipment_ownership.fkey_users_id = %d

      where equipment_ownership.quantity > 0

      order by equipment.speed_increase DESC limit 1;';

    $result = db_query($sql, $game_user->id);
    $eq = db_fetch_object($result);

    if ($eq->speed_increase > 0) {

      $actions_to_move -= $eq->speed_increase;
      $verb = t($eq->action_verb);

    }

    $actions_to_move = max($actions_to_move, 6);

    if (($game_user->meta == 'frozen') && ($actions_to_move > 6)) {

      echo <<< EOF
<div class="title">Frozen!</div>
<div class="subtitle">You have been tagged and cannot move more than
  6 actions at a time</div>
<div class="subtitle">Call on a teammate to unfreeze you!</div>
<div class="try-an-election-wrapper"><div
  class="try-an-election"><a href="/$game/home/$arg2">Go to the home page</a></div></div>
<div class="try-an-election-wrapper"><div
  class="try-an-election"><a href="0">Let me choose again</a></div></div>
EOF;

      db_set_active('default');
      return;

    }


    echo <<< EOF
<div class="title">$verb from $cur_hood->name to $new_hood->name</div>
<div class="subtitle">It will cost $actions_to_move Action to move</div>
<div class="try-an-election-wrapper"><div
  class="try-an-election"><a href="/$game/move_do/$arg2/$neighborhood_id">Yes,
  I want to go</a></div></div>
<div class="try-an-election-wrapper"><div
  class="try-an-election"><a href="0">No, let me choose again</a></div></div>
EOF;

    db_set_active('default');
    return;

  }

  $ext = '.jpg';
  $nonce = date('Y-m-d-H-i-s-') . mt_rand();

  echo <<< EOF
  <div id="map_large">
    <div class="title">Move&nbsp;from&nbsp;$location to&nbsp;another&nbsp;$hood_lower</div>
    <div class="subtitle">Touch the map to zoom in</div>
    <a href="#">
      <img src="/sites/default/files/images/{$game}_map_large_colored$ext?a=$nonce" width="320"/>
    </a>
  </div>
  <div id="map_large_bottom">
    <a href="#">
      <img src="/sites/default/files/images/{$game}_map_large_bottom_colored$ext?a=$nonce" width="320"/>
    </a>
  </div>
  <div id="map_mid">
  <div class="title">Move&nbsp;from&nbsp;$location to&nbsp;another&nbsp;$hood_lower</div>
  <div class="subtitle">Touch the map to select a new $hood_lower</div>
  <div class="subtitle">Touch the upper left corner of the map to go back</div>
    <img src="/sites/default/files/images/{$game}_map_mid$ext?a=$nonce" width="320"
      usemap="#map_mid"/>
    <map name="map_mid">
      <area id="map_mid_back_click" shape="rect" coords="0,0,80,80" alt="Back" href="#" />
EOF;

  $sql = 'select * from neighborhoods where xcoor > 0 or ycoor > 0;';
  $result = db_query($sql);

  $data = array();
  while ($item = db_fetch_object($result)) $data[] = $item;

  if (substr(arg(2), 0, 4) == 'nkc ') {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 6') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 5') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.4') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.3') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.2') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.1') !== FALSE) {

    $coefficient = 1;

  } else if ((stripos($_SERVER['HTTP_USER_AGENT'], 'BNTV') !== FALSE) &&
    (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4') !== FALSE)) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=800') !== FALSE) {

    $coefficient = 2.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=600') !== FALSE) {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=533') !== FALSE) {

    $coefficient = 1.66;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=480') !== FALSE) {

    $coefficient = 1.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=400') !== FALSE) {

    $coefficient = 1.25;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=360') !== FALSE) {

    $coefficient = 1.125;

  } else {

    $coefficient = 1;

  }

  if ($game == 'stlouis') {

    $divisor = 2.15625; // 690/320
    $xoff = 54; // offset of x
    $yoff = 488; // offset of y

  } else { // celestial glory

    $divisor = 1.65625; // 530/320
    $xoff = 0; // offset of x
    $yoff = 0; // offset of y

  }

  foreach ($data as $item) {
//firep($item);

    $xcoor = floor(($item->xcoor - $xoff) * $coefficient / $divisor);
    $ycoor = floor(($item->ycoor - $yoff) * $coefficient / $divisor);

    echo "<area shape=\"circle\" coords=\"$xcoor,$ycoor,16\" href=\"$item->id\"
      alt=\"$item->name\" />";

  }

  echo <<< EOF
    </map>
  </div>
  <div id="map_bottom">
    <div class="title">Move&nbsp;from&nbsp;$location to&nbsp;another&nbsp;$hood_lower</div>
    <div class="subtitle">Touch the map to select a new $hood_lower</div>
    <div class="subtitle">Touch the upper left corner of the map to go back</div>
    <img src="/sites/default/files/images/{$game}_map_bottom$ext?a=$nonce" width="320"
      usemap="#map_bottom"/>
    <map name="map_bottom">
      <area id="map_bottom_back_click" shape="rect" coords="0,0,20,80" alt="Back" href="#" />
EOF;


  if (substr(arg(2), 0, 4) == 'nkc ') {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 6') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 5') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.4') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.3') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.2') !== FALSE) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4.1') !== FALSE) {

    $coefficient = 1;

  } else if ((stripos($_SERVER['HTTP_USER_AGENT'], 'BNTV') !== FALSE) &&
    (stripos($_SERVER['HTTP_USER_AGENT'], 'Android 4') !== FALSE)) {

    $coefficient = 1;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=800') !== FALSE) {

    $coefficient = 2.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=600') !== FALSE) {

    $coefficient = 1.875;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=533') !== FALSE) {

    $coefficient = 1.66;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=480') !== FALSE) {

    $coefficient = 1.5;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=400') !== FALSE) {

    $coefficient = 1.25;

  } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'width=360') !== FALSE) {

    $coefficient = 1.125;

  } else {

    $coefficient = 1;

  }

  if ($game == 'stlouis') {

    $divisor = 2.15625; // 690/320
    $xoff = 0; // offset of x
    $yoff = 900; // offset of y

  } else { // celestial glory

    $divisor = 1.65625; // 530/320
    $xoff = 200; // offset of x
    $yoff = 500; // offset of y

  }

  foreach ($data as $item) {
//firep($item);

    $xcoor = floor(($item->xcoor - $xoff) * $coefficient / $divisor);
    $ycoor = floor(($item->ycoor - $yoff) * $coefficient / $divisor);

    if ($xcoor >=16 /* && $xcoor < 320 */ &&
      $ycoor >= 16 /* && $ycoor <= 334 */) {
      echo "<area shape=\"circle\" coords=\"$xcoor,$ycoor,16\" href=\"$item->id\"
        alt=\"$item->name\" />\n";
    }

  }

  echo <<< EOF
    </map>
  </div>

<script type="text/javascript">

window.onload = function() {

  document.getElementById('map_mid').style.display = 'none';
  document.getElementById('map_bottom').style.display = 'none';

  document.getElementById('map_large').onclick = function() {
    document.getElementById('map_large').style.display = 'none';
    document.getElementById('map_large_bottom').style.display = 'none';
    document.getElementById('map_mid').style.display = 'block';
    document.getElementById('map_bottom').style.display = 'none';
    return false;
  };

  document.getElementById('map_large_bottom').onclick = function() {
    document.getElementById('map_large').style.display = 'none';
    document.getElementById('map_large_bottom').style.display = 'none';
    document.getElementById('map_mid').style.display = 'none';
    document.getElementById('map_bottom').style.display = 'block';
    return false;
  };

  document.getElementById('map_mid_back_click').onclick = function() {
    document.getElementById('map_large').style.display = 'block';
    document.getElementById('map_large_bottom').style.display = 'block';
    document.getElementById('map_mid').style.display = 'none';
    document.getElementById('map_bottom').style.display = 'none';
    return false;
  };

  document.getElementById('map_bottom_back_click').onclick = function() {
    document.getElementById('map_large').style.display = 'block';
    document.getElementById('map_large_bottom').style.display = 'block';
    document.getElementById('map_mid').style.display = 'none';
    document.getElementById('map_bottom').style.display = 'none';
    return false;
  };

}
</script>
EOF;

  db_set_active('default');
