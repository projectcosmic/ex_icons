<?php

namespace Drupal\ex_icons\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'ex_icon_default' formatter.
 *
 * @FieldFormatter(
 *   id = "ex_icon_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "ex_icon",
 *     "text",
 *     "list_string",
 *   }
 * )
 */
class ExIconDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'classes' => '',
      'height' => 50,
      'width' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['classes'] = [
      '#title' => $this->t('Classes'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('classes'),
      '#description' => $this->t('HTML classes to add to the svg element.'),
    ];

    $field_name = $this->fieldDefinition->getName();
    $settings_mapping = "fields[$field_name][settings_edit_form][settings]";

    $elements['width'] = [
      '#title' => $this->t('Width'),
      '#type' => 'number',
      '#min' => 1,
      '#default_value' => $this->getSetting('width'),
      '#description' => $this->t('Leave blank to scale proportionally to height.'),
      '#states' => [
        'required' => [
          "[name='${settings_mapping}[height]']" => ['filled' => FALSE],
        ],
      ],
    ];

    $elements['height'] = [
      '#title' => $this->t('Height'),
      '#type' => 'number',
      '#min' => 1,
      '#default_value' => $this->getSetting('height'),
      '#description' => $this->t('Leave blank to scale proportionally to width.'),
      '#states' => [
        'required' => [
          "[name='${settings_mapping}[width]']" => ['filled' => FALSE],
        ],
      ],
    ];

    $elements['#element_validate'][] = [get_class($this), 'validateDimensions'];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($classes = $this->getSetting('classes')) {
      $summary[] = $this->t('Classes: @classes', ['@classes' => $classes]);
    }

    $summary[] = $this->t('Width: @width', [
      '@width' => $this->getSetting('width') ?: $this->t('automatic'),
    ]);

    $summary[] = $this->t('Height: @height', [
      '@height' => $this->getSetting('height') ?: $this->t('automatic'),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $classes = array_map(
        '\Drupal\Component\Utility\HTML::cleanCssIdentifier',
        explode(' ', $this->getSetting('classes'))
      );

      $attributes = [
        'class' => $classes,
        'width' => $this->getSetting('width'),
        'height' => $this->getSetting('height'),
      ];

      if ($this->fieldDefinition->getFieldStorageDefinition()->getSetting('add_title')) {
        $attributes['title'] = $item->title;
      }

      $elements[$delta] = [
        '#theme' => 'ex_icon',
        '#id' => $item->value,
        '#attributes' => array_filter($attributes),
      ];
    }

    return $elements;
  }

}
