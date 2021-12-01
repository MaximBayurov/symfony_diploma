<?php

namespace App\Entity;

class FlashMessage
{
    public function __construct(
        protected ?string $text,
        protected string $type = 'success'
    ){
    }
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }
}