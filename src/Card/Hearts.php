<?php

namespace App\Card;

class Hearts extends CardBase
{
    public function __construct(int $rank)
    {
        parent::__construct($rank);
    }

    public function getHTMLEntity(): string
    {
        return "&#x1f0b" . $this->getLastHexCharUnicode() . ";";
    }
}
