<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Words extends Constraint
{
    public $countMessage = 'Неправильно указано количество для продвигаемого слова!';
    public $message = 'Неправильно указано продвигаемое слово!';
}
