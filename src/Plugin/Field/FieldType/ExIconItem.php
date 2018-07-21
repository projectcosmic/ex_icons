<?php

namespace Drupal\ex_icons\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItemBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'ex_icon' field type.
 *
 * @FieldType(
 *   id = "ex_icon",
 *   label = @Translation("Icon"),
 *   description = @Translation("This field stores an icon choice."),
 *   category = @Translation("General"),
 *   default_widget = "ex_icon_select",
 *   default_formatter = "ex_icon_default",
 *   cardinality = 1,
 * )
 */
class ExIconItem extends StringItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'add_title' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Icon ID'))
      ->addConstraint('Length', ['max' => 127])
      ->setRequired(TRUE);

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Text value'))
      ->addConstraint('Length', ['max' => 255]);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'description' => 'Icon ID.',
          'type' => 'varchar',
          'length' => 127,
        ],
        'title' => [
          'description' => 'Semantic meaning behind the icon.',
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'value' => [['value', 20]],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'value';
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = 'facebook';
    $values['title'] = (new Random())->word(mt_rand(1, 255));

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];

    $element['add_title'] = [
      '#type' => 'checkbox',
      '#title' => t('Text input'),
      '#default_value' => $this->getSetting('add_title'),
      '#description' => t('Show a text input to enhance the icon value meaning.'),
    ];

    return $element;
  }

}
