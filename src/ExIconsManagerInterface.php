<?php

namespace Drupal\ex_icons;

/**
 * Defines an interface for SVG external-use icon plugin managers.
 */
interface ExIconsManagerInterface {

  /**
   * Returns the inline defs markup.
   *
   * @return string[]
   *   The inline defs markup keyed per provider.
   */
  public function getInlineDefs();

}
