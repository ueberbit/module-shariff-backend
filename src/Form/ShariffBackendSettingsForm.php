<?php

/**
 * @file
 * Contains \Drupal\shariff_backend\Form\ShariffBackendSettingsForm.
 */

namespace Drupal\shariff_backend\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Shariff backend settings form.
 */
class ShariffBackendSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shariff_backend_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['shariff_backend.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('shariff_backend.settings');

    // Facebook application ID.
    $form['facebook_app_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Facebook application ID'),
      '#default_value' => $config->get('facebook_app_id'),
      '#description' => t('An optional Facebook application ID.'),
    );

    // Facebook application client secret.
    $form['facebook_app_secret'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook application client secret'),
      '#default_value' => $config->get('facebook_app_secret'),
      '#description' => $this->t('An optional client secret needed to access the Facebook application(required if Facebook application ID is set).'),
    );

    // Share count cache TTL
    $options = [3600, 10800, 21600, 43200, 86400, 604800];
    $form['cache_ttl'] = [
      '#type' => 'select',
      '#title' => t('Share count cache TTL'),
      '#description' => t('The time a single share count should be cached.'),
      '#default_value' => $config->get('cache_ttl'),
      '#options' => [0 => '<' . t('no caching') . '>'] + array_map([\Drupal::service('date.formatter'), 'formatInterval'], array_combine($options, $options)),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save new values.
    $this->config('shariff_backend.settings')
      ->set('cache_ttl', $form_state->getValue('cache_ttl'))
      ->set('facebook_app_id', $form_state->getValue('facebook_app_id'))
      ->set('facebook_app_secret', $form_state->getValue('facebook_app_secret'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
