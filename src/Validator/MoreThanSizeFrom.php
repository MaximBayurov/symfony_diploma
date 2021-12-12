<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MoreThanSizeFrom extends Constraint
{
    public $message = 'Некорректное значение поля - "Размер статьи до"';
}
