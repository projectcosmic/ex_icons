<?php

namespace Drupal\Tests\ex_icons\Kernel;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * @coversDefaultClass Drupal\ex_icons\Plugin\Field\FieldType\ExIconItem
 * @group ex_icons
 */
class ExIconItemTest extends FieldKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['ex_icons', 'ex_icons_module_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create an icon field storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => 'field_icon',
      'entity_type' => 'entity_test',
      'type' => 'ex_icon',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_icon',
      'bundle' => 'entity_test',
    ])->save();

    // Create an icon field (with title) storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => 'field_icon_with_title',
      'entity_type' => 'entity_test',
      'type' => 'ex_icon',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_icon_with_title',
      'bundle' => 'entity_test',
      'settings' => ['add_title' => TRUE],
    ])->save();
  }

  /**
   * Tests using entity fields of the ex_icon field type.
   */
  public function testExIconItem() {
    $entity = EntityTest::create();
    $value = 'module-icon';
    $title = $this->randomString();

    // Verify entity creation.
    $entity->name->value = $this->randomMachineName();
    $entity->field_icon = $value;
    $entity->field_icon_with_title = $value;
    $entity->field_icon_with_title->title = $title;
    $entity->save();

    // Verify entity has been created properly.
    $id = $entity->id();
    $entity = EntityTest::load($id);

    $this->assertTrue($entity->field_icon instanceof FieldItemListInterface, 'Field implements interface.');
    $this->assertTrue($entity->field_icon[0] instanceof FieldItemInterface, 'Field item implements interface.');
    $this->assertEqual($entity->field_icon->value, $value);
    $this->assertEqual($entity->field_icon[0]->value, $value);

    $this->assertTrue($entity->field_icon_with_title instanceof FieldItemListInterface, 'Field implements interface.');
    $this->assertTrue($entity->field_icon_with_title[0] instanceof FieldItemInterface, 'Field item implements interface.');
    $this->assertEqual($entity->field_icon_with_title->value, $value);
    $this->assertEqual($entity->field_icon_with_title[0]->value, $value);
    $this->assertEqual($entity->field_icon_with_title->title, $title);
    $this->assertEqual($entity->field_icon_with_title[0]->title, $title);

    // Verify changing the icon value.
    $new_value = 'module-icon-no-title';
    $entity->field_icon->value = $new_value;
    $this->assertEqual($entity->field_icon->value, $new_value);

    // Verify changing the title value.
    $new_title = $this->randomString();
    $entity->field_icon_with_title->title = $new_title;
    $this->assertEqual($entity->field_icon_with_title->title, $new_title);

    // Read changed entity and assert changed values.
    $entity->save();
    $entity = EntityTest::load($id);
    $this->assertEqual($entity->field_icon->value, $new_value);
    $this->assertEqual($entity->field_icon_with_title->title, $new_title);

    // Test sample item generation.
    $entity = EntityTest::create();
    $entity->field_icon->generateSampleItems();
    $entity->field_icon_with_title->generateSampleItems();
    $this->entityValidateAndSave($entity);
  }

}
