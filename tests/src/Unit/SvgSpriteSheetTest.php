<?php

namespace Drupal\Tests\ex_icons\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\ex_icons\Serialization\SvgSpriteSheet;

/**
 * @coversDefaultClass \Drupal\ex_icons\Serialization\SvgSpriteSheet
 * @group ex_icons
 */
class SvgSpriteSheetTest extends UnitTestCase {

  /**
   * @covers ::decode
   */
  public function testDecoding() {
    $content = file_get_contents(__DIR__ . '/../../fixtures/sprites.svg');
    $result = SvgSpriteSheet::decode($content);

    $this->assertArrayEquals(
      [
        'icon-no-title' => [
          'width'  => 25,
          'height' => 25,
          'label'  => '',
        ],
        'icon' => [
          'width'  => 20,
          'height' => 60,
          'label'  => 'Icon',
        ],
      ],
      $result['icons']
    );

    $this->assertEquals(
      '<linearGradient id="gradient" x1="0" y1="0" x2="10" y2="10" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#000"/></linearGradient><clipPath id="clip-path"><path d="M0 5l3-6z"/></clipPath>',
      $result['inline_defs'],
      'Only <def> children with an ID attribute should be included.'
    );
  }

}
