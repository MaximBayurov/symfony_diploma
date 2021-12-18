<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class KeywordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Keyword */

        if (null === $value || '' === $value) {
            return;
        }

        if (empty($value['nominative'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
