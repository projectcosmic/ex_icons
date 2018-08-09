<?php

namespace Drupal\ex_icons\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Radios;

/**
 * Provides a form element for selecting an icon.
 *
 * Usage example:
 * @code
 * $form['icon'] = array(
 *   '#type' => 'ex_icon_select',
 *   '#title' => $this->t('Title icon'),
 *   '#default_value' => 'arrow',
 * );
 * @endcode
 *
 * @see \Drupal\Core\Render\Element\Radios
 *
 * @FormElement("ex_icon_select")
 */
class ExIconSelect extends Radios {

  /**
   * Empty value for internal logic use.
   */
  const EMPTY_VALUE = '__empty__';

  /**
   * Icon options.
   *
   * @var string[]
   */
  protected static $options;

  /**
   * {@inheritdoc}
   */
  public static function processRadios(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#options'] = self::getOptions();

    // If not required:
    if (!isset($element['#states']['required']) && !$element['#required']) {
      // Add empty value option.
      $element['#options'] = [
        self::EMPTY_VALUE => t('No icon'),
      ] + $element['#options'];

      // Set to empty value if no default value set or previously nulled value.
      if (!isset($element['#default_value']) || $element['#default_value'] === '') {
        $element['#default_value'] = self::EMPTY_VALUE;
      }
    }

    $element = parent::processRadios($element, $form_state, $complete_form);

    foreach ($element['#options'] as $key => $label) {
      $element[$key]['#attributes']['class'][] = 'visually-hidden';

      if ($key != self::EMPTY_VALUE) {
        $element[$key]['#title'] = [
          '#theme' => 'ex_icon',
          '#id' => $key,
          '#attributes' => [
            'title' => $label,
            'class' => ['icon-selector__icon'],
          ],
        ];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $element['#options'] = self::getOptions();

    // Revert to normal empty value handling.
    if ($input == self::EMPTY_VALUE) {
      // Work-around to get past value/element validation.
      $element['#options'][''] = '';

      return '';
    }

    return parent::valueCallback($element, $input, $form_state);
  }

  /**
   * Gets the icon options.
   *
   * @return string[]
   *   The list of icon titles keyed by their ID.
   *
   * @see \Drupal\ex_icons\ExIconsManagerInterface::getIcons()
   */
  protected static function getOptions() {
    if (!isset(self::$options)) {
      self::$options = [];

      foreach (\Drupal::service('ex_icons.icons_manager')->getIcons() as $id => $icon_data) {
        self::$options[$id] = $icon_data['title'];
      }
    }

    return self::$options;
  }

}
