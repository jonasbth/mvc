<?php

namespace App\Card;

class Diamonds extends CardBase
{
    public function __construct(int $rank)
    {
        parent::__construct($rank);
    }

    public function getHTMLEntity(): string
    {
        return "&#x1f0c" . $this->getLastHexCharUnicode() . ";";
    }
}
