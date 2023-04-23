<?php

namespace App\Card;

use Countable;
use Iterator;

class CardHand implements Countable, Iterator
{
    private array $hand = [];
    private int $position = 0;
    private string $name = "";

    public function __construct(string $name = "", array $cards = [])
    {
        $this->name = $name;
        $this->hand = $cards;
    }

    /**
     *  Return the name of the hand.
     *
     *  @return string  The name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *  Add card(s) to hand.
     *
     *  @param array $cards  The array of cards to add.
     */
    public function addCards(array $cards): void
    {
        $this->hand = array_merge($this->hand, $cards);
    }

    /**
     *  Return number of cards in the hand.
     *
     *  Required method of the Countable interface.
     *
     *  @return int  Number of cards.
     */
    public function count(): int
    {
        return count($this->hand);
    }

    /**
     *  Required methods below of the Iterator interface.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->hand[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->hand[$this->position]);
    }
}
