<?php

namespace Drupal\ex_icons\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for External-use Icon settings.
 */
class ExIconsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ex_icons_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ex_icons.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ex_icons.settings');

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sprite sheet path'),
      '#default_value' => $config->get('path'),
      '#description' => $this->t('Path to the SVG sprite sheet, relative to the Drupal root (do <em>not</em> start with a forward slash).'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ex_icons.settings')
      ->set('path', $form_state->getValue('path'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
