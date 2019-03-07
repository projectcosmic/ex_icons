<?php

namespace Drupal\ex_icons\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'ex_icon_link' formatter.
 *
 * @FieldFormatter(
 *   id = "ex_icon_link",
 *   label = @Translation("Link as icon"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class ExIconLinkFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'icon' => '',
      'height' => 50,
      'width' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    // Hide inherited trim length field; it is irrelevant for this formatter.
    $elements['trim_length']['#access'] = FALSE;

    $elements['icon'] = [
      '#title' => $this->t('Icon'),
      '#type' => 'ex_icon_select',
      '#default_value' => $this->getSetting('icon'),
      '#description' => $this->t('The icon to use.'),
      '#required' => TRUE,
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
   * Validates dimension settings for formatter settings form.
   *
   * @see \Drupal\ex_icons\Plugin\Field\FieldFormatter\ExIconLinkFormatter::settingsForm()
   */
  public static function validateDimensions($element, FormStateInterface $form_state) {
    $values = $form_state->getValue($element['#parents']);

    if (!($values['width'] || $values['height'])) {
      $form_state
        ->setError($element['width'], t('Width or height must be set.'))
        ->setError($element['height'], t('Width or height must be set.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    // Remove the first summary row; it is the trim length and is irrelevant
    // for this formatter.
    $summary = array_slice(parent::settingsSummary(), 1, 0);

    $summary[] = $this->t('Icon: @icon', [
      '@icon' => $this->getSetting('icon'),
    ]);

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
    $element = parent::viewElements($items, $langcode);
    $entity = $items->getEntity();

    foreach ($element as $delta => $item) {
      $link_title = $this->fieldDefinition->getLabel();

      // If the title field value is available, use it for the link text.
      if (empty($this->getSetting('url_only')) && !empty($item->title)) {
        $link_title = \Drupal::token()->replace(
          $item->title,
          [$entity->getEntityTypeId() => $entity],
          ['clear' => TRUE]
        );
      }

      $element[$delta]['#title'] = [
        '#theme' => 'ex_icon',
        '#id' => $this->getSetting('icon'),
        // Use array_filter() to remove height or width if they are not set so
        // it can be automatically calculated later.
        '#attributes' => array_filter([
          'width' => $this->getSetting('width'),
          'height' => $this->getSetting('height'),
          'title' => Html::escape($link_title),
        ]),
      ];
    }

    return $element;
  }

}
