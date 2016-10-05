<?php
/**
 * @file
 */

  include drupal_get_path('module', 'usl2') . '/inc/common/game_defs.inc';

  $time -= REQUEST_TIME;
  $days = sprintf('%d', floor($time / 86400));
  $time %= 86400;
  $hours = sprintf('%02d', floor($time / 3600));
  $time %= 3600;
  $mins = sprintf('%02d', floor($time / 60));
  $secs = sprintf('%02d', floor($time % 60));

?>
<div class="welcome">
  <div class="elder-image">
  </div>
  <p>
    A wizened old man comes up to you.&nbsp; You recognize him as one of the
    elders of the city.
  </p>
  <h2>
    &ldquo;<?php echo game_get_elder_message(); ?>&rdquo;
  </h2>
  <h5>
    You cannot yet
  </h5>
  <h4 class="title">
    <?php echo t($action, $txt); ?>
  </h4>
  <h5>
    for another
  </h5>
  <h4>
    <?php echo $days; ?> day(s) and <?php echo $hours; ?>h <?php echo $mins; ?>m <?php echo $secs; ?>s.
  </h4>
</div>
