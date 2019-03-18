<?php

namespace Drupal\ex_icons;

/**
 * Interface for external-use icon plugins.
 */
interface ExIconInterface {

  /**
   * Returns the translated label.
   *
   * @return string
   *   The translated label.
   */
  public function getLabel();

  /**
   * Returns the provider.
   *
   * @return string
   *   The provider.
   */
  public function getProvider();

  /**
   * Returns width of the icon.
   *
   * @return float
   *   The width.
   */
  public function getWidth();

  /**
   * Returns the height of the icon.
   *
   * @return float
   *   The height.
   */
  public function getHeight();

  /**
   * Returns the aspect ratio of the icon glyph.
   *
   * @return float
   *   The aspect ratio as a ratio of width to height.
   */
  public function getAspectRatio();

  /**
   * Returns the full URL for the icon.
   *
   * @return string
   *   The full URL, suitable for use in SVG <use> tags.
   */
  public function getUrl();

}
