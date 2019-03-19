<?php

namespace Drupal\Tests\ex_icons\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests icon theming.
 *
 * @group ex_icons
 */
class ExIconsThemingTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system', 'ex_icons', 'ex_icons_module_test'];

  /**
   * Tests ex-icon.html.twig theme.
   */
  public function testExIconTheme() {
    $renderer = $this->container->get('renderer');

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'does_not_exist',
    ];

    $dom = new \DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $use = $dom->getElementsByTagName('use')->item(0);
    $this->assertNotNull($svg, 'Icon skeleton should be printed for non-existent icon.');
    $this->assertNotNull($use, 'Icon skeleton should be printed for non-existent icon.');
    $this->assertEmpty($use->getAttribute('xlink:href'), 'URL should be empty.');
    $this->assertEquals('img', $svg->getAttribute('role'), 'Should have default role attribute.');

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'does_not_exist',
      '#attributes' => [
        'width' => 20,
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals(20, $svg->getAttribute('height'), 'Height should be calculated as 1:1 aspect ratio for non-existent icon.');
    $this->assertEquals(20, $svg->getAttribute('width'));

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'does_not_exist',
      '#attributes' => [
        'height' => 20,
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals(20, $svg->getAttribute('width'), 'Width should be calculated as 1:1 aspect ratio for non-existent icon.');
    $this->assertEquals(20, $svg->getAttribute('height'));

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'module-icon',
      '#attributes' => [
        'width' => 22,
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals(44, $svg->getAttribute('height'), 'Height should be calculated correctly from aspect ratio.');
    $this->assertEquals(22, $svg->getAttribute('width'));
    $this->assertEquals('img', $svg->getAttribute('role'), 'Should have default role attribute.');

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'module-icon',
      '#attributes' => [
        'height' => 22,
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals(11, $svg->getAttribute('width'), 'Width should be calculated correctly from aspect ratio.');
    $this->assertEquals(22, $svg->getAttribute('height'));

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'module-icon',
      '#attributes' => [
        'width' => 100,
        'height' => 33,
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));
    $this->setRawContent($renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals(100, $svg->getAttribute('width'));
    $this->assertEquals(33, $svg->getAttribute('height'));

    $render_array = [
      '#theme' => 'ex_icon',
      '#id' => 'module-icon',
      '#attributes' => [
        'role' => 'presentation',
        'a' => 'b',
      ],
    ];
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $renderer->renderRoot($render_array));

    $svg = $dom->getElementsByTagName('svg')->item(0);
    $this->assertEquals('presentation', $svg->getAttribute('role'), 'Role attribute should be able to be overridden.');
    $this->assertEquals('b', $svg->getAttribute('a'), 'Arbitrary attributes should be able to be added.');
  }

}
