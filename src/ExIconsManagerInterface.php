<?php

namespace Drupal\ex_icons;

/**
 * Defines an interface for SVG external-use icon plugin managers.
 */
interface ExIconsManagerInterface {

  /**
   * Builds the relative URL for use in <use> SVG elements.
   *
   * The URL will have a query cache buster generated from the plugin
   * information discovered.
   *
   * @param string $plugin_id
   *   The plugin_id.
   *
   * @return string
   *   The URL. For example,
   *   '/modules/example/dist/icons.svg?63d2e2bf1452aee5#icon'.
   */
  public function buildUrl($plugin_id);

  /**
   * Returns the inline defs markup.
   *
   * @return string[]
   *   The inline defs markup keyed per provider.
   */
  public function getInlineDefs();

}
