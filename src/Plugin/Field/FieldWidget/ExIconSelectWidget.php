<?php

namespace Drupal\ex_icons\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;

/**
 * Plugin implementation of the 'ex_icon_select' widget.
 *
 * @FieldWidget(
 *   id = "ex_icon_select",
 *   label = @Translation("Icon select"),
 *   field_types = {
 *     "ex_icon"
 *   },
 * )
 */
class ExIconSelectWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = [
      '#type' => 'icon_select',
      '#default_value' => $items[$delta]->value,
    ];

    if ($this->fieldDefinition->getFieldStorageDefinition()->getSetting('add_title')) {
      $element['#type'] = 'fieldset';

      // Move description to before the fieldset elements if it exists.
      if ($element['#description']) {
        $element['description'] = ['#markup' => $element['#description']];
        unset($element['#description']);
      }

      $element['value'] = [
        '#title' => $this->t('Icon'),
        '#required' => $element['#required'],
      ] + $value;

      $element['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#description' => $this->t('Enter a textual representation of the icon for accessibility and semantics.'),
        '#max_length' => 255,
        '#size' => 40,
        '#required' => $element['#required'],
        '#default_value' => $items[$delta]->title,
      ];
    }
    else {
      $element['value'] = $element + $value;
    }

    return $element;
  }

}
