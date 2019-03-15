<?php

namespace Drupal\ex_icons;

use Drupal\Core\Plugin\PluginBase;

/**
 * Default object used for external-use icon plugins.
 *
 * @see \Drupal\ex_icons\ExIconManager
 * @see plugin_api
 */
class ExIcon extends PluginBase implements ExIconInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->t($this->pluginDefinition['label'], [], ['context' => 'ex_icons']);
  }

  /**
   * {@inheritdoc}
   */
  public function getProvider() {
    return $this->pluginDefinition['provider'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWidth() {
    return (float) $this->pluginDefinition['width'];
  }

  /**
   * {@inheritdoc}
   */
  public function getHeight() {
    return (float) $this->pluginDefinition['height'];
  }

}
