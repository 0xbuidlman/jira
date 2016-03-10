<?php
/**
 * @file
 * Contains \Drupal\jira\Form\JiraAdminForm.
 */

namespace Drupal\jira\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for JIRA integration settings.
 */
class JiraAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['jira.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'jira_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('jira.settings');

    $form['production_domain'] = [
      '#type' => 'url',
      '#required' => TRUE,
      '#default_value' => $config->get('production_domain'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()
      ->getEditable('jira.settings')
      ->set('production_domain', rtrim($form_state->getValue('production_domain'), '/'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
