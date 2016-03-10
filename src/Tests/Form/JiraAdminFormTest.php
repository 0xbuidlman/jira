<?php
/**
 * @file
 * Contains \Drupal\jira\Tests\Form\JiraAdminFormTest.
 */

namespace Drupal\jira\Tests\Form;

use Drupal\system\Tests\System\SystemConfigFormTestBase;
use Drupal\jira\Form\JiraAdminForm;

/**
 * Tests the JIRA integration settings form.
 *
 * @group jira
 */
class JiraAdminFormTest extends SystemConfigFormTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['jira', 'node'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->form = JiraAdminForm::create($this->container);

    $this->values = [
      'production_domain' => [
        '#value' => 'https://foobar.com',
        '#config_name' => 'jira.settings',
        '#config_key' => 'production_domain',
      ],
    ];
  }

}
