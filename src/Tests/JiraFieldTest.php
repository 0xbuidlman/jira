<?php
/**
 * @file
 * Contains \Drupal\jira\Tests\JiraFieldTest.
 */

namespace Drupal\jira\Tests;

use Drupal\Component\Utility\Unicode;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\simpletest\WebTestBase;
use Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraint;

/**
 * Integration test for the JIRA field type and formatter.
 *
 * @group jira
 */
class JiraFieldTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['jira', 'entity_test'];

  /**
   * A user with test entity permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->webUser = $this->drupalCreateUser(['view test entity', 'administer entity_test content']);
    $this->drupalLogin($this->webUser);
  }

  /**
   * Test the JIRA field type and formatter.
   */
  public function testJiraField() {
    $field_name = Unicode::strtolower($this->randomMachineName());
    $field_storage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'entity_test',
      'type' => 'jira_ticket',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => $this->randomMachineName() . '_label',
    ])->save();
    entity_get_form_display('entity_test', 'entity_test', 'default')
      ->setComponent($field_name, [
        'type' => 'string_textfield',
        'settings' => [],
      ])
      ->save();
    entity_get_display('entity_test', 'entity_test', 'full')
      ->setComponent($field_name)
      ->save();

    // Add an entity with a valid value.
    $this->drupalGet('entity_test/add');
    $this->assertFieldByName("{$field_name}[0][value]", '', 'Field is available on the form');
    $value = strtoupper($this->getRandomGenerator()->word(3)) . '-' . mt_rand(1000, 10000);
    $edit = [
      "{$field_name}[0][value]" => $value,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    preg_match('|entity_test/manage/(\d+)|', $this->url, $match);
    $id = $match[1];
    $this->assertText(t('entity_test @id has been created.', ['@id' => $id]));

    // Link should be present in rendered entity.
    $entity = EntityTest::load($id);
    $display = entity_get_display($entity->getEntityTypeId(), $entity->bundle(), 'full');
    $content = $display->build($entity);
    $this->setRawContent(\Drupal::service('renderer')->renderRoot($content));
    $this->assertLink($value);
    $this->assertLinkByHref($this->config('jira.settings')->get('production_domain') . '/browse/' . $value);

    // Try invalid jira ticket values.
    $this->drupalGet('entity_test/add');
    $this->assertFieldByName("{$field_name}[0][value]", '', 'Field is available on the form');
    $value = 'foobar';
    $edit = [
      "{$field_name}[0][value]" => $value,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $contraint = new JiraTicketConstraint();
    $this->assertText(strtr($contraint->message, ['@ticket' => $value]));
    $this->assertUrl('entity_test/add');
  }

}
