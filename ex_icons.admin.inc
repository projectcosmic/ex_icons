<?php

/**
 * @file
 * Admin page callbacks for the External-use icons module.
 */

/**
 * Form constructor for the settings form.
 *
 * @see ex_icons_settings_form_validate()
 * @see system_settings_form_submit()
 *
 * @ingroup forms
 */
function ex_icons_settings_form($form, &$form_state) {
  $form[ExIconsManager::PATH_SETTING_NAME] = array(
    '#type' => 'textfield',
    '#title' => t('Sprite sheet path'),
    '#default_value' => variable_get(ExIconsManager::PATH_SETTING_NAME),
    '#description' => t('Path to the SVG sprite sheet, relative to the Drupal root.'),
  );

  return system_settings_form($form);
}

/**
 * Form validation handler for ex_icons_settings_form().
 */
function ex_icons_settings_form_validate($form, &$form_state) {
  $path = $form_state['values'][ExIconsManager::PATH_SETTING_NAME];
  // Ensures any beginning slash is removed.
  if ($path && strpos($path, '/') === 0) {
    $form_state['values'][ExIconsManager::PATH_SETTING_NAME] = substr($path, 1);
  }
}
