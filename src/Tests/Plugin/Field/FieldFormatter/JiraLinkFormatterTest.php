<?php
/**
 * @file
 * Contains \Drupal\jira\Tests\Plugin\Field\FieldFormatter\JiraLinkFormatterTest.
 */

namespace Drupal\jira\Tests\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\simpletest\KernelTestBase;

/**
 * Test the JIRA link formatter.
 *
 * @group jira
 *
 * @see \Drupal\field\Tests\String\RawStringFormatterTest
 */
class JiraLinkFormatterTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'jira',
    'field',
    'node',
    'text',
    'entity_test',
    'system',
    'filter',
    'user',
  ];

  /**
   * The entity type name.
   *
   * @var string
   */
  protected $entityType;

  /**
   * The bundle name.
   *
   * @var string
   */
  protected $bundle;

  /**
   * The field name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * Display configuration.
   *
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $display;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Configure the theme system.
    $this->installConfig(['system', 'field', 'jira']);
    $this->installSchema('system', 'router');
    \Drupal::service('router.builder')->rebuild();
    $this->installEntitySchema('entity_test');

    $this->entityType = 'entity_test';
    $this->bundle = $this->entityType;
    $this->fieldName = Unicode::strtolower($this->randomMachineName());

    $field_storage = FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'type' => 'jira_ticket',
    ]);
    $field_storage->save();

    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $this->bundle,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();

    $this->display = entity_get_display($this->entityType, $this->bundle, 'default')
      ->setComponent($this->fieldName, [
        'type' => 'jira_link',
        'settings' => [],
      ]);
    $this->display->save();
  }

  /**
   * Renders fields of a given entity with a given display.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity object with attached fields to render.
   * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   *   The display to render the fields in.
   *
   * @return string
   *   The rendered entity fields.
   */
  protected function renderEntityFields(FieldableEntityInterface $entity, EntityViewDisplayInterface $display) {
    $content = $display->build($entity);
    $content = $this->render($content);
    return $content;
  }

  /**
   * Tests string formatter output.
   */
  public function testStringFormatter() {
    $value = 'DUG-1234';

    $entity = EntityTest::create([]);
    $entity->{$this->fieldName}->value = $value;

    // Verify that all HTML is escaped and newlines are retained.
    $this->renderEntityFields($entity, $this->display);
    $this->assertLink($value);
    $this->assertLinkByHref('https://jira.example.com/browse/' . $value);

    // Verify the cache tags.
    $build = $entity->{$this->fieldName}->view();
    $this->assertEqual('config:jira.settings', $build[0]['#cache']['tags'][0], 'The JIRA link formatter has a config cache tag.');
  }

}
