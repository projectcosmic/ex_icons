<?php

namespace Drupal\ex_icons;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service helping with querying icon data from a sprite sheet.
 */
class ExIconsManager implements ExIconsManagerInterface {

  /**
   * Cache ID used to store icon data.
   */
  const CACHE_ID = 'ex_icons_data';

  /**
   * Cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * A config object for the icons configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The set of icon data.
   *
   * @var array
   */
  protected $data;

  /**
   * Constructs a ExIconsManager object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache backend.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   */
  public function __construct(CacheBackendInterface $cache, ConfigFactoryInterface $config_factory) {
    $this->cache = $cache;
    $this->config = $config_factory->get('ex_icons.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getIcons() {
    if (!isset($this->data)) {
      $this->discoverIconData();
    }

    return $this->data['icons'];
  }

  /**
   * {@inheritdoc}
   */
  public function getInlineDefs() {
    if (!isset($this->data)) {
      $this->discoverIconData();
    }

    return $this->data['inline_defs'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSheetUrl() {
    return file_url_transform_relative(file_create_url($this->config->get('path')));
  }

  /**
   * {@inheritdoc}
   */
  public function getHash() {
    if (!isset($this->data)) {
      $this->discoverIconData();
    }

    return $this->data['hash'];
  }

  /**
   * {@inheritdoc}
   */
  public function rebuild() {
    $this->cache->delete(self::CACHE_ID);
  }

  /**
   * Discovers the icon data from the sprite sheet file.
   *
   * @return array
   *   The set of icon data.
   */
  protected function discoverIconData() {
    $data = [
      'icons' => [],
      'inline_defs' => '',
      'hash' => '',
    ];

    if ($cache = $this->cache->get(self::CACHE_ID)) {
      $data = $cache->data;
    }
    else {
      $sheet = $this->config->get('path');
      if ($sheet && file_exists($sheet)) {
        $dom = new \DOMDocument();
        $dom->load($sheet);

        foreach ($dom->getElementsByTagName('symbol') as $symbol) {
          // Skip symbols without ID attribute.
          if (!$symbol->hasAttribute('id')) {
            continue;
          }

          $id = $symbol->getAttribute('id');
          $viewbox = explode(' ', $symbol->getAttribute('viewBox'));

          // Attempt to extract a text representation from a title element.
          $title = $symbol
            ->getElementsByTagName('title')
            ->item(0);

          $data['icons'][$id] = [
            'width'  => $viewbox[2],
            'height' => $viewbox[3],
            'title' => $title ? $title->nodeValue : str_replace('-', ' ', ucfirst($id)),
          ];
        }

        foreach ($dom->getElementsByTagName('defs') as $def) {
          $data['inline_defs'] .= $dom->saveXML($def);
        }

        $data['hash'] = substr(hash_file('sha512', $sheet), 0, 16);

        $this->cache->set(self::CACHE_ID, $data, $this->getCacheMaxAge(), $this->getCacheTags());
      }
    }

    $this->data = $data;
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return $this->config->getCacheContexts();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return CacheBackendInterface::CACHE_PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return $this->config->getCacheTags();
  }

}
