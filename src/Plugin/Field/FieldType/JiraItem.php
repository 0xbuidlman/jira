<?php
/**
 * @file
 * Contains \Drupal\jira\Plugin\Field\FieldType\JiraItem.
 */

namespace Drupal\jira\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItemBase;

/**
 * Defines the 'jira_ticket' entity field type.
 *
 * @FieldType(
 *   id = "jira_ticket",
 *   label = @Translation("JIRA ticket number"),
 *   description = @Translation("A field containing a JIRA ticket number."),
 *   category = @Translation("Text"),
 *   default_widget = "string_textfield",
 *   default_formatter = "jira_link",
 *   constraints = {"JiraTicket" = {}}
 * )
 */
class JiraItem extends StringItemBase {

  /**
   * {@inheritdoc}
   *
   * Removes the case-sensitive option.
   */
  public static function defaultStorageSettings() {
    $settings = parent::defaultStorageSettings();
    unset($settings['case_sensitive']);
    $settings['max_length'] = 32;
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'varchar_ascii',
          'length' => (int) $field_definition->getSetting('max_length'),
          'binary' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = strtoupper($random->word(3)) . '-' . mt_rand(1000, 10000);
    return $values;
  }

}
