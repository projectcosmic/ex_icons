<?php

/**
 * @file
 * Contains the icon manager class definition.
 */

/**
 * Service helping with querying icon data from a sprite sheet.
 */
class ExIconsManager implements ExIconsManagerInterface {

  /**
   * Cache ID used to store icon data.
   */
  const CACHE_ID = 'ex_icons_data';

  /**
   * Variable name of the sprite path setting.
   */
  const PATH_SETTING_NAME = 'ex_icons_path';

  /**
   * The set of icon data.
   *
   * @var array
   */
  protected $data;

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
    return check_url(base_path() . variable_get(self::PATH_SETTING_NAME));
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
    cache_clear_all(self::CACHE_ID, 'cache');
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

    if ($cache = cache_get(self::CACHE_ID)) {
      $data = $cache->data;
    }
    else {
      $sheet = variable_get(self::PATH_SETTING_NAME);
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

        cache_set(self::CACHE_ID, $data, 'cache', CACHE_PERMANENT);
      }
    }

    $this->data = $data;
    return $this->data;
  }

}
