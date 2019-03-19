<?php

namespace Drupal\Tests\ex_icons\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the form API icon selection element.
 *
 * @coversDefaultClass \Drupal\ex_icons\Element\ExIconSelect
 * @group ex_icons
 */
class ExIconsSelectionTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['ex_icons', 'ex_icons_module_test'];

  /**
   * Tests that #type 'ex_icon_select' fields carry values properly.
   *
   * Covers:
   * - ex_icons_preprocess_radios()
   * - ex_icons_preprocess_form_element_label()
   * - \Drupal\ex_icons\Element\ExIconSelect
   */
  public function testExIconSelection() {
    $this->drupalGet('ex-icons-module-test/icon-selection');
    $this->assertSession()->fieldNotExists('ex_icon_null');
    $this->assertSession()->fieldExists('selection')->selectOption('module-icon');
    $this->getSession()->getPage()->pressButton('Submit');
    $values = Json::decode($this->getSession()->getPage()->getContent());
    $this->assertEqual($values['selection'], 'module-icon');

    $this->drupalGet('ex-icons-module-test/icon-selection');
    $this->getSession()->getPage()->pressButton('Submit');
    $values = Json::decode($this->getSession()->getPage()->getContent());
    $this->assertEmpty($values['selection']);
  }

}
