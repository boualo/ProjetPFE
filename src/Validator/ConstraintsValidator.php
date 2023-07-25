<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintsValidator extends ConstraintValidator
{
    // public function validate($value, Constraint $constraint)
    // {
    //     /* @var App\Validator\Constraints $constraint */

    //     if (null === $value || '' === $value) {
    //         return;
    //     }

    //     // TODO: implement the validation here
    //     $this->context->buildViolation($constraint->message)
    //         ->setParameter('{{ value }}', $value)
    //         ->addViolation();
    // }
    public function validate($value, Constraint $constraint)
    {
        if(preg_match('/\d/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
