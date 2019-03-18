<?php

namespace Drupal\Tests\ex_icons\Unit;

use Drupal\ex_icons\ExIcon;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * @coversDefaultClass \Drupal\ex_icons\ExIcon
 * @group ex_icons
 */
class ExIconTest extends UnitTestCase {

  /**
   * The used plugin ID.
   *
   * @var string
   */
  protected $pluginId = 'icon';

  /**
   * The used plugin definition.
   *
   * @var array
   */
  protected $pluginDefinition = [
    'id' => 'icon',
  ];

  /**
   * The icon under test.
   *
   * @var \Drupal\ex_icons\ExIcon
   */
  protected $icon;

  /**
   * The mocked translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->stringTranslation = $this->getMock('Drupal\Core\StringTranslation\TranslationInterface');
  }

  /**
   * Sets up the icon defaults.
   */
  protected function setupIcon() {
    $this->icon = new ExIcon([], $this->pluginId, $this->pluginDefinition);
    $this->icon->setStringTranslation($this->stringTranslation);
  }

  /**
   * @covers ::getLabel
   */
  public function testGetLabel() {
    $this->pluginDefinition['label'] = 'Test label';
    $this->setupIcon();
    $this->assertEquals(
      new TranslatableMarkup('Test label', [], ['context' => 'Icon Label'], $this->stringTranslation),
      $this->icon->getLabel()
    );
  }

  /**
   * @covers ::getWidth
   */
  public function testGetWidth() {
    $this->pluginDefinition['width'] = '1.23';
    $this->setupIcon();
    // Assert that the type returned is a float.
    $this->assertSame(1.23, $this->icon->getWidth());
  }

  /**
   * @covers ::getHeight
   */
  public function testGetHeight() {
    $this->pluginDefinition['height'] = '1.23';
    $this->setupIcon();
    // Assert that the type returned is a float.
    $this->assertSame(1.23, $this->icon->getHeight());
  }

  /**
   * @covers ::getAspectRatio
   */
  public function testGetAspectRatio() {
    $this->pluginDefinition['width'] = 6;
    $this->pluginDefinition['height'] = 3;
    $this->setupIcon();
    $this->assertSame(2.0, $this->icon->getAspectRatio());
  }

  /**
   * @covers ::getProvider
   */
  public function testGetProvider() {
    $this->pluginDefinition['provider'] = 'icon';
    $this->setupIcon();
    $this->assertEquals('icon', $this->icon->getProvider());
  }

  /**
   * @covers ::getUrl
   */
  public function testGetUrl() {
    $this->pluginDefinition['url'] = 'a/b/c.svg#icon';
    $this->setupIcon();
    $this->assertEquals('a/b/c.svg#icon', $this->icon->getUrl());
  }

}
