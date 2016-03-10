<?php
/**
 * @file
 * Contains \Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraint.
 */

namespace Drupal\jira\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for links receiving data allowed by its settings.
 *
 * @Constraint(
 *   id = "JiraTicket",
 *   label = @Translation("Valid JIRA ticket pattern", context = "Validation")
 * )
 */
class JiraTicketConstraint extends Constraint {

  /**
   * {@inheritdoc}
   */
  public $message = 'Invalid JIRA key "@ticket". This must be of the form "PROJECT-12345".';

}
