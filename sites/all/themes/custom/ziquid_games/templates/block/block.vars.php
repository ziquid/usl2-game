<?php
/**
 * @file
 * Stub file for "block" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * See template for list of available variables.
 *
 * @see block.tpl.php
 *
 * @ingroup theme_preprocess
 */
function cpcu_ce_preprocess_block(&$variables) {

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-left-nav') {
    $variables['classes_array'][] = 'col-md-3';
  }

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-alerts') {
    $variables['classes_array'][] = 'col-md-9';
  }

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-credit-hours') {
    $variables['classes_array'][] = 'col-md-6';
  }

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-status') {
    $variables['classes_array'][] = 'col-md-3';
  }

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-state-license') {
    $variables['classes_array'][] = 'col-md-3';
  }

  if ($variables['block_html_id'] == 'block-cpcu-ce-app-editable-widget') {
    $variables['classes_array'][] = 'col-md-9';
  }

}
