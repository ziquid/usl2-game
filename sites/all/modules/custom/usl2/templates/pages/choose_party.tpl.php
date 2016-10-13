<?php

/**
 * @file
 * This file shows the choose party screen.
 *
 * @todo: Implement alternate text when the user is changing parties.
 * @todo: Highlight the user's current party.
 * @todo: Order parties by membership.
 */

  $arg0 = check_plain(arg(0));
  $arg2 = check_plain(arg(2));
  include drupal_get_path('module', 'usl2') . '/inc/common/game_defs.inc';

  db_set_active('game');
/*
  $sql = 'SELECT COUNT(users.id) AS count,  `values_table`.*
    FROM  `users`
    LEFT JOIN  `values_table` ON users.fkey_values_id = `values_table`.id
    where `values_table`.user_selectable = 1
    GROUP BY fkey_values_id
    ORDER BY count ASC;';
  $result = db_query($sql, array());
  $data = $result->fetchAll();
*/
  $query = db_select('values_table', 'v')
    ->condition('v.user_selectable', 1)
    ->fields('v')
    ->execute();
  $data = $query->fetchAll();
  db_set_active('default');

?>
<div class="welcome">
  <div class="elder-image">
  </div>
  <p>
    You are met by the city elder again.
  </p>
  <h2>
    &ldquo;Well done.
  </h2>
  <p>
    &ldquo;I am impressed by what you have learned.
  </p>
  <p>&ldquo;In order to continue your journey, you will need a
    mentor.&nbsp; Your mentor will provide guidance and answer any questions
    that you may have.&nbsp; He or she should have provided you with a
    referral code.
  </p>
  <p>
    &ldquo;Alternatively, you can continue on your own without a
    code.&nbsp; Which do you prefer?&rdquo;
  </p>
</div>

<?php echo theme('game_button', array('link' => 'enter_referral_code', 'type' => 'I Have A Referral Code')); ?>

<div class="welcome">
  <h3>
    <?php echo t("If you don't have a referral code, choose a @party:", $txt); ?>
  </h3>

  <?php foreach($data as $item): ?>
    <div>
      <span class="choose-party-name">
        <a href="/<?php echo $arg0; ?>/choose_party/<?php echo $arg2; ?>/<?php echo $item->id; ?>" style="color:#<?php echo $item->color; ?>; font-size: 160%; font-family: 'roboto slab'; font-weight: bold;">
          <img src="/sites/default/files/images/<?php echo $arg0; ?>_party_<?php echo $item->party_icon; ?>.png"/>
          <?php echo $item->party_title; ?>
        </a>
      </span>
      <span class="nowrap">value <strong><?php echo $item->name; ?></strong></span>
    </div>
    <div class="choose-party-slogan">
       <?php echo $item->slogan; ?>
    </div>
  <?php endforeach; ?>
</div>
