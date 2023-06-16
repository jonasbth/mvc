<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardHand.
 */
class CardHandTest extends TestCase
{
    private CardHand $hand;

    /**
     * Set up fixture.
     */
    protected function setUp(): void
    {
        $testHand = [new Spades(2), new Hearts(3)];
        $this->hand = new CardHand("Test", $testHand);
    }

    /**
     * Tear down fixture.
     */
    protected function tearDown(): void
    {
        unset($this->hand);
    }

    /**
     * Test getName() method.
     */
    public function testGetName(): void
    {
        $this->assertEquals("Test", $this->hand->getName());
    }

    /**
     * Test addCards() method.
     */
    public function testAddCards(): void
    {
        $this->hand->addCards([new Clubs(4)]);
        $newHand = [];

        // Put each card suit and rank in a new array
        foreach ($this->hand as $card) {
            $newHand[] = $card->getSuit();
            $newHand[] = $card->getRank();
        }

        $this->assertEquals(["spades", 2, "hearts", 3, "clubs", 4], $newHand);
    }

    /**
     * Test reset() method.
     */
    public function testReset(): void
    {
        $this->hand->reset();
        $this->assertEquals(0, $this->hand->count());
    }

    /**
     * Test count() method.
     */
    public function testCount(): void
    {
        $count = $this->hand->count();

        $this->assertEquals(2, $count);
    }

    /**
     * Test getPoints21() method, used in the gard game "21".
     */
    public function testGetPoints21(): void
    {
        // Test two cards, no ace, points < 21.
        $points = $this->hand->getPoints21();
        $this->assertEquals([5], $points);

        // Test three cards, one ace, points < 21.
        $this->hand->addCards([new Hearts(1)]);
        $points = $this->hand->getPoints21();
        $this->assertEquals([6, 19], $points);

        // Test five cards, three aces, points = 21.
        $this->hand->addCards([new Spades(1), new Clubs(1)]);
        $points = $this->hand->getPoints21();
        $this->assertEquals([21], $points);

        // Test six cards, three aces, points = 21.
        $this->hand->addCards([new Spades(13)]);
        $points = $this->hand->getPoints21();
        $this->assertEquals([21], $points);

        // Test seven cards, three aces, points > 21.
        $this->hand->addCards([new Diamonds(2)]);
        $points = $this->hand->getPoints21();
        $this->assertEquals([23], $points);

        // Test an empty hand.
        $empty = new CardHand("Test2");
        $points = $empty->getPoints21();
        $this->assertEquals([0], $points);
    }

    /**
     * Test getMaxPoints21() method, used in the gard game "21".
     */
    public function testGetMaxPoints21(): void
    {
        // Test two cards, no ace, points < 21.
        $points = $this->hand->getMaxPoints21();
        $this->assertEquals(5, $points);

        // Test three cards, one ace, points < 21.
        $this->hand->addCards([new Hearts(1)]);
        $points = $this->hand->getMaxPoints21();
        $this->assertEquals(19, $points);
    }

    /**
     * Test methods of the Iterator interface.
     */
    public function testIteratorInterface(): void
    {
        $this->assertEquals(0, $this->hand->key());
        $this->assertInstanceOf("\App\Card\CardBase", $this->hand->current());
        $this->assertTrue($this->hand->valid());

        $this->hand->next();
        $this->assertEquals(1, $this->hand->key());

        $this->hand->rewind();
        $this->assertEquals(0, $this->hand->key());

        for ($i = 0; $i < 2; $i++) {
            $this->hand->next();
        }

        $this->assertFalse($this->hand->valid());
    }
}
