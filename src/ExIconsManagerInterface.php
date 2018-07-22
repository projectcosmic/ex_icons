<?php

namespace Drupal\ex_icons;

use Drupal\Core\Cache\CacheableDependencyInterface;

/**
 * Provides an interface for querying icon data from a sprite sheet.
 */
interface ExIconsManagerInterface extends CacheableDependencyInterface {

  /**
   * Returns the list of icons.
   *
   * @return array
   *   The list of icons keyed by their id, with each element containing:
   *   - width: The effective width of the icon, as defined by its viewBox.
   *   - height: The effective height of the icon, as defined by its viewBox.
   */
  public function getIcons();

  /**
   * Returns the inline defs markup.
   *
   * @return string
   *   The inline defs markup without the surrounding <defs> tags.
   */
  public function getInlineDefs();

  /**
   * Invalidates any caches and rebuilds icon data.
   */
  public function rebuild();

}
