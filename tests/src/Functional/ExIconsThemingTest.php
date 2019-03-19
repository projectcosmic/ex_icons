<?php

namespace Drupal\Tests\ex_icons\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests theming output.
 *
 * @group ex_icons
 */
class ExIconsThemingTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['ex_icons'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    \Drupal::service('theme_handler')->install(['ex_icons_theme_test']);
  }

  /**
   * Tests that inline defs are propagated to the page.
   *
   * Covers:
   * - ex_icons_page_bottom()
   */
  public function testExIconInlineDefs() {
    $this->drupalGet('');
    $this->assertSession()->elementExists('css', 'svg.visually-hidden');
    $this->assertSession()->responseContains('<defs><linearGradient id="gradient" x1="0" y1="0" x2="10" y2="10" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#000"/></linearGradient><clipPath id="clip-path"><path d="M0 5l3-6z"/></clipPath></defs>');
  }

}
