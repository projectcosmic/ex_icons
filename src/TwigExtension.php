<?php

namespace Drupal\ex_icons;

/**
 * A class providing Drupal Twig extensions.
 */
class TwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('ex_icon', [$this, 'getIcon']),
    ];
  }

  /**
   * Gets an icon.
   *
   * @param string $icon
   *   The id of the icon.
   * @param array|\Drupal\Core\Template\Attribute $attributes
   *   An optional array or Attribute object of SVG attributes.
   *
   * @return array
   *   A render array representing an icon.
   */
  public function getIcon($icon, $attributes = []) {
    return [
      '#theme' => 'ex_icon',
      '#id' => $icon,
      '#attributes' => $attributes,
    ];
  }

}
