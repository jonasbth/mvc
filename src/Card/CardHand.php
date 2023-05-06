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
     *  Remove all cards from hand.
     */
    public function reset(): void
    {
        $this->hand = [];
        $this->position = 0;
    }

    /**
     *  Return the total points of the hand according to the rules of "21".
     *
     *  For totals less than 21, up to two values can be returned as an ace can be worth 1 or 14.
     *
     *  @return int[]  The points array.
     */
    public function getPoints21(): array
    {
        $points = [0, 0];
        $foundAce = false;

        foreach ($this->hand as $card) {
            $rank = $card->getRank();
            $points[0] += $rank;

            if ($rank === 1 && !$foundAce) {
                $points[1] += 14;
                $foundAce = true;
            } else {
                $points[1] += $rank;
            }
        }

        if ($points[0] === $points[1] || $points[1] > 21) {
            array_pop($points);

        } elseif ($points[1] === 21) {
            array_shift($points);
        }

        return $points;
    }

    /**
     *  Return the total maximum points of the hand according to the rules of "21".
     *
     *  For totals up to 21, there can be up to two valid values of the hand.
     *  Return the highest of these.
     *
     *  @return int   The max points.
     */
    public function getMaxPoints21(): int
    {
        $points = $this->getPoints21();

        return array_pop($points);
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
