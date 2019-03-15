<?php

namespace Drupal\ex_icons\Plugin\Discovery;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;

/**
 * An interface defining an SVG symbol plugin discovery component.
 *
 * @ingroup plugin_api
 */
interface SvgSymbolDiscoveryInterface extends DiscoveryInterface {

  /**
   * Gets the <def> element children content of all sprite sheets.
   *
   * @return string[]
   *   An array of SVG markup strings. Keys are provider IDs.
   */
  public function getInlineDefs();

}
