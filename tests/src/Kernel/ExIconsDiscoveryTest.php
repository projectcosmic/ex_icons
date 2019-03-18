<?php

namespace Drupal\Tests\ex_icons\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests discovery of icons provided by themes and modules.
 *
 * @group ex_icons
 */
class ExIconsDiscoveryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system', 'ex_icons', 'ex_icons_module_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    \Drupal::service('theme_handler')->install(['ex_icons_theme_test']);
  }

  /**
   * Test the icon plugin definitions discovered.
   */
  public function testDiscoveredIcons() {
    $expected = [
      'icon-no-title' => [
        'width' => 25,
        'height' => 25,
        'provider' => 'ex_icons_theme_test',
        'id' => 'icon-no-title',
        'class' => 'Drupal\\ex_icons\\ExIcon',
      ],
      'icon' => [
        'width' => 20,
        'height' => 20,
        'provider' => 'ex_icons_theme_test',
        'id' => 'icon',
        'class' => 'Drupal\\ex_icons\\ExIcon',
      ],
      'module-icon-no-title' => [
        'width' => 25,
        'height' => 25,
        'provider' => 'ex_icons_module_test',
        'id' => 'module-icon-no-title',
        'class' => 'Drupal\\ex_icons\\ExIcon',
      ],
      'module-icon' => [
        'width' => 20,
        'height' => 20,
        'provider' => 'ex_icons_module_test',
        'id' => 'module-icon',
        'class' => 'Drupal\\ex_icons\\ExIcon',
      ],
    ];

    $definitions = \Drupal::service('ex_icons.manager')->getDefinitions();
    foreach ($expected as $id => $expected_definition) {
      $this->assertArraySubset($expected_definition, $definitions[$id]);
    }
  }

  /**
   * Test the inline defs markup discovery.
   */
  public function testInlineDefs() {
    $expected = [
      'ex_icons_module_test' => '',
      'ex_icons_theme_test' => '<linearGradient id="gradient" x1="0" y1="0" x2="10" y2="10" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#000"/></linearGradient><clipPath id="clip-path"><path d="M0 5l3-6z"/></clipPath>',
    ];

    $inline_defs = \Drupal::service('ex_icons.manager')->getInlineDefs();
    foreach ($expected as $id => $expected_def) {
      $this->assertEquals($expected_def, $inline_defs[$id]);
    }
  }

  /**
   * Tests the getInstance method.
   */
  public function testGetInstance() {
    $manager = \Drupal::service('ex_icons.manager');

    // Test that an existing ID returns the instance.
    $instance = $manager->getInstance(['id' => 'icon']);
    $this->assertEquals('icon', $instance->getPluginId());

    // Test that an non-existing ID returns the fall-back null instance.
    $instance = $manager->getInstance(['id' => 'does_not_exist']);
    $this->assertEquals('ex_icon_null', $instance->getPluginId());
  }

}
