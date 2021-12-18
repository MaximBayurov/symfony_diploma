<?php

namespace App\Services;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;

class FormHelper
{
    public function getFormErrors(FormInterface $form): array
    {
        $errors = $this->errorIteratorToArray($form->getErrors());
        
        $iterator = $form->getIterator()->getIterator();
        $iterator->rewind();
        while ($current = $iterator->current()) {
            $currentErrors = $this->errorIteratorToArray($current->getErrors());
            if ($currentErrors) {
                $errors = array_merge(
                    $errors,
                    $currentErrors
                );
            }
            $iterator->next();
        }
        
        return $errors;
    }
    
    private function errorIteratorToArray(FormErrorIterator $errorIterator): array
    {
        if ($errorIterator->count() < 1) {
            return [];
        }
        
        $result = [];
        $errorIterator->rewind();
        while ($current = $errorIterator->current()) {
            $result[] = $current->getMessage();
            $errorIterator->next();
        }
        
        return $result;
    }
}