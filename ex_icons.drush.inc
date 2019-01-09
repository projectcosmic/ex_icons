<?php

/**
 * @file
 * Drush integration for the External-use Icons module.
 */

/**
 * Implements hook_drush_cache_clear().
 */
function ex_icons_drush_cache_clear(&$types) {
  $types['ex-icons'] = '_ex_icons_cache_clear';
}

/**
 * Clears the icons cache.
 */
function _ex_icons_cache_clear() {
  cache_clear_all(ExIconsManager::CACHE_ID, 'cache');
}
