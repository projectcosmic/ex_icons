<?php

namespace Drupal\ex_icons\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Radios;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ex_icons\ExIconsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class ExIconSelect extends Radios implements ContainerFactoryPluginInterface {

  /**
   * Empty value for internal logic use.
   */
  const EMPTY_VALUE = '__empty__';

  /**
   * The icons manager service.
   *
   * @var \Drupal\ex_icons\ExIconsManagerInterface
   */
  protected $iconsManager;

  /**
   * Constructs an IconSelect object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ex_icons\ExIconsManagerInterface $icons_manager
   *   The icons manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ExIconsManagerInterface $icons_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->iconsManager = $icons_manager;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ex_icons.icons_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $options = [];
    foreach ($this->iconsManager->getIcons() as $id => $icon_data) {
      $options[$id] = $icon_data['title'];
    }

    return [
      '#options' => $options,
    ] + parent::getInfo();
  }

  /**
   * {@inheritdoc}
   */
  public static function processRadios(&$element, FormStateInterface $form_state, &$complete_form) {
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
    // Revert to normal empty value handling.
    if ($input == self::EMPTY_VALUE) {
      // Work-around to get past value/element validation.
      $element['#options'][''] = '';

      return '';
    }

    return parent::valueCallback($element, $input, $form_state);
  }

}
