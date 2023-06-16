<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardDeck.
 */
class CardDeckTest extends TestCase
{
    private CardDeck $deck;

    /**
     * Set up fixture.
     */
    protected function setUp(): void
    {
        $this->deck = new CardDeck();
    }

    /**
     * Tear down fixture.
     */
    protected function tearDown(): void
    {
        unset($this->deck);
    }

    /**
     * Test draw a card.
     */
    public function testDrawCard(): void
    {
        $cardArr = $this->deck->draw();

        $this->assertCount(1, $cardArr);
        $this->assertInstanceOf("\App\Card\CardBase", $cardArr[0]);
    }

    /**
     * Test draw cards of specific rank.
     */
    public function testDrawCardsRank(): void
    {
        $callback = fn(CardBase $card): int => $card->getRank();
        $this->deck->shuffle();

        $cardArr = $this->deck->draw(1, 1);
        $this->assertEquals([1], array_map($callback, $cardArr));

        $cardArr = $this->deck->draw(2, 1, 2);
        $this->assertEquals([1, 2], array_map($callback, $cardArr));

        // There are only two aces left, so only those are returned
        $cardArr = $this->deck->draw(3, 1, 1, 1);
        $this->assertEquals([1, 1], array_map($callback, $cardArr));
    }

    /**
     * Test count() method.
     */
    public function testCount(): void
    {
        $count = $this->deck->count();

        $this->assertEquals(52, $count);
    }

    /**
     * Test that shuffle() method preserves a complete card deck.
     */
    public function testShuffle(): void
    {
        $this->deck->shuffle();

        // Create a two-dimensional array where each card may be registered,
        // which is later checked for completeness.
        $checkDeck = array(
            "spades" => array_fill(1, 13, 0),
            "hearts" => array_fill(1, 13, 0),
            "diamonds" => array_fill(1, 13, 0),
            "clubs" => array_fill(1, 13, 0)
        );

        foreach ($this->deck as $card) {
            $checkDeck[$card->getSuit()][$card->getRank()]++;
        }

        // Verify $checkDeck
        foreach ($checkDeck as $suit) {
            $this->assertEquals(array_fill(1, 13, 1), $suit);
        }
    }

    /**
     * Test methods of the Iterator interface.
     */
    public function testIteratorInterface(): void
    {
        $this->assertEquals(0, $this->deck->key());
        $this->assertInstanceOf("\App\Card\CardBase", $this->deck->current());
        $this->assertTrue($this->deck->valid());

        $this->deck->next();
        $this->assertEquals(1, $this->deck->key());

        $this->deck->rewind();
        $this->assertEquals(0, $this->deck->key());

        for ($i = 0; $i < 52; $i++) {
            $this->deck->next();
        }

        $this->assertFalse($this->deck->valid());
    }
}
