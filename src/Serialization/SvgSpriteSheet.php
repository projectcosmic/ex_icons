<?php

namespace Drupal\ex_icons\Serialization;

use Drupal\Component\Serialization\SerializationInterface;

/**
 * Provides a SVG sprite sheet serialization implementation.
 *
 * Example file format:
 * @code
 * <svg>
 *   <defs>
 *     <linearGradient id="gradient" x1="0" y1="0" x2="10" y2="10" gradientUnits="userSpaceOnUse">
 *       <stop offset="0" stop-color="#fff"/>
 *       <stop offset="1" stop-color="#000"/>
 *     </linearGradient>
 *   </defs>
 *   <symbol id="icon-1" viewBox="0 0 20 20">
 *     <title>Label of the icon (optional)</title>
 *     <rect width="20" height="20" />
 *   </symbol>
 *   <symbol id="icon-2" viewBox="0 0 20 20">
 *     <title>Label of the icon (optional)</title>
 *     <rect width="20" height="20" fill="url(#gradient)" />
 *   </symbol>
 * </svg>
 * @endcode
 *
 * @todo Implement encoding.
 */
class SvgSpriteSheet implements SerializationInterface {

  /**
   * {@inheritdoc}
   */
  public static function encode($data) {
    // Noop.
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public static function decode($raw) {
    $data = [
      'icons' => [],
      'inline_defs' => [],
      'hash' => substr(hash('sha512', $raw), 0, 16),
    ];

    $dom = new \DOMDocument();
    $dom->loadXML($raw);

    foreach ($dom->getElementsByTagName('symbol') as $symbol) {
      // Skip symbols without ID attribute.
      if (!$symbol->hasAttribute('id')) {
        continue;
      }

      $id = $symbol->getAttribute('id');
      $viewbox = explode(' ', $symbol->getAttribute('viewBox'));

      // Catch invalid viewBox attribute value.
      if (count($viewbox) != 4) {
        continue;
      }

      // Attempt to extract a text representation from a title element.
      $title = $symbol
        ->getElementsByTagName('title')
        ->item(0);

      $data['icons'][$id] = [
        'width'  => $viewbox[2],
        'height' => $viewbox[3],
        'label'  => $title ? $title->nodeValue : '',
      ];
    }

    foreach ($dom->getElementsByTagName('defs') as $def) {
      $data['inline_defs'][] = $dom->saveXML($def);
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getFileExtension() {
    return 'svg';
  }

}
