<?php
/**
 * @file
 * Contains \Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraintValidator.
 */

namespace Drupal\jira\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Validation constraint for JIRA ticket patterns.
 */
class JiraTicketConstraintValidator implements ConstraintValidatorInterface {

  /**
   * Stores the validator's state during validation.
   *
   * @var \Symfony\Component\Validator\ExecutionContextInterface
   */
  protected $context;

  /**
   * JIRA ticket pattern.
   *
   * @var string
   */
  protected $jiraPattern = '/^[A-Z]+-\d+$/';

  /**
   * {@inheritdoc}
   */
  public function initialize(ExecutionContextInterface $context) {
    $this->context = $context;
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (isset($value)) {
      $ticket = $value->value;
      if (!preg_match($this->jiraPattern, $ticket)) {
        $this->context->addViolation($constraint->message, ['@ticket' => $ticket]);
      }
    }
  }

}
