<?php

namespace Drupal\Tests\ex_icons\Unit;

use Drupal\ex_icons\Discovery\SvgSymbolDiscovery;

/**
 * Class for testing SvgSymbolDiscovery.
 */
class TestSvgSymbolDiscovery extends SvgSymbolDiscovery {

  /**
   * {@inheritdoc}
   */
  protected function transformFileUrl($file_path) {
    return "transformed:$file_path";
  }

}
