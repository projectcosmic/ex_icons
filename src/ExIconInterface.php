<?php

namespace Drupal\ex_icons;

/**
 * Interface for Breakpoint plugins.
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

}
