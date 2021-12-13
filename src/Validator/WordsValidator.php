<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WordsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint Words */

        if (null === $value[0]['promotedWord'] || '' === $value[0]['promotedWord']) {
            return;
        }

        foreach ($value as $word) {
            if (empty($word['promotedWord'])) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
    
            $word['count'] = $word['count'] ?? 0;
            if (!is_numeric($word['count'])) {
                $this->context->buildViolation($constraint->countMessage)
                    ->addViolation();
            }
        }
    }
}
