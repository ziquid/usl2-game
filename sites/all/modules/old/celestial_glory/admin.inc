<?php
/** @file */

/**
 * (Re)draw the map.
 *
 * @todo: add a lockfile so that the maps aren't requested as they are being
 *   generated.
 */
function _celestial_glory_draw_maps() {

  // Grab and slice the files.
  $map_large = imagecreatefromjpeg(
    'sites/default/files/images/celestial_glory_map_large.jpg'
  );
  $map_mid = imagecreatetruecolor(530, 600);
  $map_large_colored = imagecreatetruecolor(800, 500);
  $map_bottom = imagecreatetruecolor(530, 300);
  $map_bottom_colored = imagecreatetruecolor(800, 300);

  // Grab the CPs.
  $sql = 'SELECT xcoor, ycoor, clan_icon
    FROM `elected_officials`
    left join users on fkey_users_id = users.id
    left join `values` on fkey_values_id = `values`.id
    left join neighborhoods on users.fkey_neighborhoods_id = neighborhoods.id
    WHERE fkey_elected_positions_id = 1 and (xcoor > 1 or ycoor > 1);';
  $result = db_query($sql);
  while ($item = db_fetch_object($result)) $data[] = $item;

  // Draw the icons.
  foreach ($data as $item) {

    $icon = imagecreatefrompng(
      'sites/default/files/images/celestial_glory_clan_'
      . $item->clan_icon . '.png');
    $icon_scaled = imagescale($icon, 32, 32);
    imagecopy($map_large, $icon_scaled, $item->xcoor - 12, $item->ycoor, 0, 0,
      32, 32);
    imagedestroy($icon);
    imagedestroy($icon_scaled);

  }

  // Save the files.
  imagecopy($map_large_colored, $map_large, 0, 0, 0, 0, 800, 500);
  imagecopy($map_bottom_colored, $map_large, 0, 0, 0, 500, 800, 300);

  imagejpeg($map_large_colored,
    'sites/default/files/images/celestial_glory_map_large_colored.jpg');
  imagejpeg($map_bottom_colored,
    'sites/default/files/images/celestial_glory_map_large_bottom_colored.jpg');

  imagecopy($map_mid, $map_large, 0, 0, 0, 0, 530, 600);
  imagecopy($map_bottom, $map_large, 0, 0, 200, 500, 530, 300);

  imagejpeg($map_mid,
    'sites/default/files/images/celestial_glory_map_mid.jpg');
  imagejpeg($map_bottom,
    'sites/default/files/images/celestial_glory_map_bottom.jpg');

  // Clean up.
  imagedestroy($map_large);
  imagedestroy($map_mid);
  imagedestroy($map_bottom);
  imagedestroy($map_large_colored);
  imagedestroy($map_bottom_colored);

}
