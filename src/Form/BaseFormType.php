<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

abstract class BaseFormType extends AbstractType
{
    protected function setDefaultOptions(string $label): array
    {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $label,
                'class' => 'form-control'
            ],
            'row_attr' => [
                'class' => 'form-label-group'
            ],
            'required' => false,
        ];
    }
}