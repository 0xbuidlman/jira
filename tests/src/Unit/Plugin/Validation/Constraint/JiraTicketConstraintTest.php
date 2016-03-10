<?php
/**
 * @file
 * Contains \Drupal\Tests\jira\Unit\Plugin\Validation\Constraint\JiraTicketConstraintTest.
 */

namespace Drupal\Tests\jira\Unit\Plugin\Validation\Constraint;

use Drupal\Tests\UnitTestCase;
use Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraint;
use Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraintValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Test the JIRA ticket pattern constraint.
 *
 * @coversDefaultClass \Drupal\jira\Plugin\Validation\Constraint\JiraTicketConstraintValidator
 *
 * @group jira
 */
class JiraTicketConstraintTest extends UnitTestCase {

  /**
   * Tests validation.
   *
   * @param string $value
   *   Value to check.
   * @param bool $valid
   *   TRUE if the value should be valid.
   *
   * @covers ::validate
   *
   * @dataProvider providerValidate
   */
  public function testValidate($value, $valid) {
    $context = $this->prophesize(ExecutionContextInterface::class);

    $constraint = new JiraTicketConstraint();

    if ($valid) {
      $context->addViolation()->shouldNotBeCalled();
    }
    else {
      $context->addViolation($constraint->message, ['@ticket' => $value])->shouldBeCalled();
    }

    $validator = new JiraTicketConstraintValidator();
    $validator->initialize($context->reveal());

    $item = new \stdClass();
    $item->value = $value;
    $validator->validate($item, $constraint);
  }

  /**
   * Data provider for ::testValidate.
   */
  public function providerValidate() {
    $data = [];

    $data[] = ['DUG-1234', TRUE];
    $data[] = ['ABCDEF-12345', TRUE];
    $data[] = ['ABC', FALSE];
    $data[] = ['1234', FALSE];
    $data[] = ['-DUG-1234', FALSE];
    $data[] = ['DUG-1234-', FALSE];
    $data[] = ['DUG-12345A', FALSE];
    $data[] = ['dug-123', FALSE];

    return $data;
  }

}
