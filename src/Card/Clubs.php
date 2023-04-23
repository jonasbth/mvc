<?php

namespace App\Card;

class Clubs extends CardBase
{
    public function __construct(int $rank)
    {
        parent::__construct($rank);
    }

    /**
     *  Return the HTML entity representation of a card.
     *
     *  @return string  The entity.
     */
    public function getHTMLEntity(): string
    {
        return "&#x1f0d" . $this->getLastHexCharUnicode() . ";";
    }
}
