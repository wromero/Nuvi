<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 18/05/16
 * Time: 3:43 PM
 */

namespace NS\SentinelBundle\Validators;

use NS\SentinelBundle\Entity\BaseCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BirthdayOrAgeValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     * @param BaseCase $value
     * @param BirthdayOrAge $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException(sprintf('Expected object got %s instead', gettype($value)));
        } elseif (!$value instanceof BaseCase) {
            throw new \InvalidArgumentException(sprintf('Expected object of type NS\SentinelBundle\Entity\BaseCase got %s instead', get_class($value)));
        }

        if (!$value->getBirthdate() && !$value->getDobMonths() && !$value->getDobYears()) {
            $this->context->buildViolation($constraint->message)->atPath('dobKnown')->addViolation();
        }
    }
}
