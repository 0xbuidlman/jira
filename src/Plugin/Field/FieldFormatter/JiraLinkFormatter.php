<?php
/**
 * @file
 * Contains \Drupal\jira\Plugin\Field\FieldFormatter\JiraLinkFormatter.
 */

namespace Drupal\jira\Plugin\Field\FieldFormatter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation to format a JIRA link.
 *
 * @FieldFormatter(
 *   id = "jira_link",
 *   label = @Translation("JIRA link"),
 *   field_types = {
 *     "jira_ticket"
 *   }
 * )
 */
class JiraLinkFormatter extends StringFormatter {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a JIRA link formatter.
   *
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityManagerInterface $entity_manager, ConfigFactoryInterface $config_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_manager);
    $this->config = $config_factory->get('jira.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Remove the 'link_to_entity' option.
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    unset($settings['link_to_entity']);
    return $settings;
  }

  /**
   * {@inheritdoc}
   *
   * Remove the 'link_to_entity' option.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    unset($form['link_to_entity']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $view_value = $this->viewValue($item);
      $url = Url::fromUri($this->config->get('production_domain') . '/browse/' . $item->value);
      $elements[$delta] = [
        '#type' => 'link',
        '#title' => $view_value,
        '#url' => $url,
        '#cache' => [
          'tags' => $this->config->getCacheTags(),
        ],
      ];
    }
    return $elements;
  }

}
