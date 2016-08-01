<?php

$conf['install_profile'] = 'minimal';
$conf['master_version'] = 2;
$conf['master_modules'] = array(
  'base' => array(
    'admin_menu_toolbar',
    'adminrole',
    'backup_migrate',
//     'better_formats',
    'block',
    'bootstrap',
//     'contact',
//     'contextual',
//     'date',
//     'draggableviews',
//     'ds',
//     'ds_extras',
//     'ds_format',
//     'entity_token',
//     'entityreference',
//     'exif_custom',
//     'features',
//     'field_group',
    'globalredirect',
//     'googleanalytics',
//     'imagecache_token',
    'jquery_update',
//     'lightbox2',
//     'link',
//     'list',
//     'logintoboggan',
//     'media_bulk_upload',
//     'media_internet',
//     'media_migrate_file_types',
//     'media_wysiwyg_view_mode',
//     'menu',
//     'multiform',
//     'nice_menus',
//     'noindex_external_links',
//     'number',
//     'pathauto',
//     'php',
//     'plupload',
//     'redirect',
//     'remove_ron',
//     'statistics',
//     'strongarm',
//     'subpathauto',
//     'taxonomy',
//     'taxonomy_menu',
//     'tracker',
    'update',

// GDG modules
    'ziquid_games',
  ),

  'local' => array(
    'dblog',
    'devel',
    'diff',
    'stage_file_proxy',
  ),

  'dev' => array(
    'dblog',
    'devel',
    'diff',
    'stage_file_proxy',
  ),

  'prod' => array(),

);
$conf['master_uninstall_blacklist'] = array(
  'base' => array(
    0 => 'ckeditor',
  ),
);
$conf['master_removable_blacklist'] = array(
  0 => 'modules/*',
);
