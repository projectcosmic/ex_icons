<?php

/**
 * @file
 * Contains the icon manager interface definition.
 */

/**
 * Provides an interface for querying icon data from a sprite sheet.
 */
interface ExIconsManagerInterface {

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
   * Returns the URL to the icon sprite sheet suitable for HTML usage.
   *
   * @return string
   *   The URL relative to the website root.
   */
  public function getSheetUrl();

  /**
   * Returns a cache-busting hash of the icon data.
   *
   * @return string
   *   The hash.
   */
  public function getHash();

  /**
   * Invalidates any caches and rebuilds icon data.
   */
  public function rebuild();

}
