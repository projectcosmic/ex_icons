<?php

namespace Drupal\ex_icons\Commands;

use Drush\Commands\DrushCommands;
use Drupal\ex_icons\ExIconsManagerInterface;

/**
 * A Drush commandfile.
 */
class ExIconsCommands extends DrushCommands {

  /**
   * The icons manager service.
   *
   * @var \Drupal\ex_icons\ExIconsManagerInterface
   */
  protected $iconsManager;

  /**
   * Constructs a ExIconsCommands object.
   *
   * @param \Drupal\ex_icons\ExIconsManagerInterface $icons_manager
   *   The icons manager service.
   */
  public function __construct(ExIconsManagerInterface $icons_manager) {
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
   * Clears the icons cache.
   */
  public function clearIconsCache() {
    $this->iconsManager->rebuild();
  }

}
