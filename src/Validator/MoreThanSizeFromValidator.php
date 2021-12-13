<?php

namespace App\Validator;

use App\Entity\Article;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MoreThanSizeFromValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\MoreThanSizeFrom */
        
        if (null === $value || '' === $value) {
            return;
        }
        
        /**
         * @var Article $article
         */
        $article = $this->context->getObject();
        if ($article->getSizeFrom() > $article->getSizeTo()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
