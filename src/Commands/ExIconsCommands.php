<?php

namespace Drupal\ex_icons\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * A Drush commandfile.
 */
class ExIconsCommands extends DrushCommands {

  /**
   * The icons manager service.
   *
   * @var \Drupal\Core\Plugin\DefaultPluginManager
   */
  protected $iconsManager;

  /**
   * Constructs a ExIconsCommands object.
   *
   * @param \Drupal\Core\Plugin\DefaultPluginManager $icons_manager
   *   The icons manager service.
   */
  public function __construct(DefaultPluginManager $icons_manager) {
    parent::__construct();
    $this->iconsManager = $icons_manager;
  }

  /**
   * Modifies drush cache list.
   *
   * @param array $types
   *   The cache types.
   *
   * @hook on-event cache-clear
   */
  public function cacheTypes(array &$types) {
    $types['ex-icons'] = [$this, 'clearIconsCache'];
  }

  /**
   * Clears icon plugin definitions cache.
   */
  public function clearIconsCache() {
    $this->iconsManager->clearCachedDefinitions();
  }

}
