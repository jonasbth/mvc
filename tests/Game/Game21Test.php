<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Game21.
 */
class Game21Test extends TestCase
{
    private static Game21 $game;

    /**
     * Set up fixture.
     */
    public static function setUpBeforeClass(): void
    {
        self::$game = new Game21();
    }

    /**
     * Test initiate a new game and get some basic properties.
     */
    public function testNewGame(): void
    {
        // Let the player's name be "Test" and let the player and the bank use chance.
        self::$game->newGame("Test", true, true);
        
        $this->assertEquals("Test", self::$game->playerName());
        $this->assertEquals("Banken", self::$game->bankName());
        $this->assertEquals(1, self::$game->round());
        $this->assertEquals(51, self::$game->cardsLeft());  // One card is drawn
        $this->assertEquals(1, self::$game->playerHand()->count());
        $this->assertEquals(0, self::$game->bankHand()->count());
        $this->assertEquals(0, self::$game->playerWins());
        $this->assertEquals(0, self::$game->bankWins());
        $this->assertEquals(true, self::$game->playersTurn());
        $this->assertEquals(true, self::$game->playerUseChance());
        $this->assertEquals(true, self::$game->bankUseChance());

        // Test the cardCount array [51, 4, ... 3, ..., 4]
        $playerCardRank = self::$game->playerHand()->current()->getRank();
        $this->assertEquals(51, self::$game->cardCount()[0]);
        $this->assertEquals(3, self::$game->cardCount()[$playerCardRank]);
        $this->assertEquals(
            array_fill(0, $playerCardRank - 1, 4),
            array_slice(self::$game->cardCount(), 1, $playerCardRank - 1)
        );
        $this->assertEquals(
            array_fill(0, 13 - $playerCardRank, 4),
            array_slice(self::$game->cardCount(), $playerCardRank + 1)
        );
    }

    /**
     * Test let the player play an initial round.
     */
    public function testPlayerRound(): void
    {
        // The player draws an initial ace
        self::$game->newGame("Test", true, true, 1);

        $this->assertEquals("1 eller 14", self::$game->playerWorth());
        $this->assertEquals([100, 53], self::$game->playerChance());

        // The player draws a five
        self::$game->playerNewCard(5);

        $this->assertEquals("6 eller 19", self::$game->playerWorth());
        $this->assertEquals([100, 14], self::$game->playerChance());

        // The player draws a seven
        self::$game->playerNewCard(7);

        $this->assertEquals("13", self::$game->playerWorth());
        $this->assertEquals([59, -1], self::$game->playerChance());

        // The player draws a king and loses the round
        self::$game->playerNewCard(13);

        $this->assertEquals("26", self::$game->playerWorth());
        $this->assertEquals(1, self::$game->bankWins());
    }

    /**
     * Test let the player lose an initial round, and a new round is initiated.
     */
    public function testInitRound(): void
    {
        // The player draws an initial knight
        self::$game->newGame("Test", true, true, 11);

        // The player draws a queen and loses the round
        self::$game->playerNewCard(12);

        $this->assertEquals("23", self::$game->playerWorth());
        $this->assertEquals(1, self::$game->bankWins());

        // A new round is initiated and the player draws an eight
        self::$game->nextRound(8);

        $this->assertEquals(2, self::$game->round());
        $this->assertEquals("8", self::$game->playerWorth());
    }

    /**
     * Test let the bank win an initial round, not using chance.
     */
    public function testBankWinNoChance(): void
    {
        // The player draws an initial knight
        self::$game->newGame("Test", false, false, 11);

        // The player draws a six and stops
        self::$game->playerNewCard(6);

        // The bank plays and wins
        self::$game->bankTurn([9, 7, 1]);

        $this->assertEquals("17", self::$game->playerWorth());
        $this->assertEquals(17, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->bankWins());
    }

    /**
     * Test let the bank lose an initial round, not using chance.
     */
    public function testBankLoseNoChance(): void
    {
        // The player draws an initial seven
        self::$game->newGame("Test", false, false, 7);

        // The player draws an ace and stops
        self::$game->playerNewCard(1);

        // The bank plays and loses
        self::$game->bankTurn([6, 1]);

        $this->assertEquals("21", self::$game->playerWorth());
        $this->assertEquals(20, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->playerWins());
    }

    /**
     * Test let the bank win an initial round, using chance.
     */
    public function testBankWinChance(): void
    {
        // The player draws an initial king
        self::$game->newGame("Test", false, true, 13);

        // The player draws a five and stops
        self::$game->playerNewCard(5);

        // The bank plays and wins
        self::$game->bankTurn([4, 1]);

        $this->assertEquals("18", self::$game->playerWorth());
        $this->assertEquals(18, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->bankWins());
    }

    /**
     * Test that there is no crash when the card deck becomes empty (the drawn hands become empty).
     * Use a "smarter" bank and a smaller card deck with 13 cards (1 suit) to facilitate testing.
     */
    public function testBankChanceNoCrashEmptyDeck(): void
    {
        // Let the player and the bank use chance, let the player draw an initial king,
        // and use just one suit.
        self::$game->newGame("Test", true, true, 13, 1);

        // The player draws a six and stops
        self::$game->playerNewCard(6);

        // The bank plays and wins (the unsmart bank would have stoped at 17 and lost)
        self::$game->bankTurn([12, 5, 2]);

        $this->assertEquals("19", self::$game->playerWorth());
        $this->assertEquals(19, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->bankWins());
        $this->assertEquals(8, self::$game->cardsLeft());
        $this->assertEquals([8, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0], self::$game->cardCount());

        // The player starts a new round with an initial knight
        self::$game->nextRound(11);

        // The player draws a ten and stops
        self::$game->playerNewCard(10);

        // The bank plays and wins (the unsmart bank would have stoped at 17 and lost)
        self::$game->bankTurn([8, 9, 4]);

        $this->assertEquals("21", self::$game->playerWorth());
        $this->assertEquals(21, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(2, self::$game->bankWins());
        $this->assertEquals(3, self::$game->cardsLeft());
        $this->assertEquals([3, 1, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0], self::$game->cardCount());

        // Skip the player's turn just for test (the player's hand i still worth 21) and let
        // the bank play and win.
        self::$game->bankTurn([1, 7]);

        $this->assertEquals("21", self::$game->playerWorth());
        $this->assertEquals(21, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(3, self::$game->bankWins());
        $this->assertEquals(1, self::$game->cardsLeft());
        $this->assertEquals([1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], self::$game->cardCount());

        // The player starts a new round and draws the last card, which should be a three
        self::$game->nextRound();

        $this->assertEquals("3", self::$game->playerWorth());
        $this->assertEquals(0, self::$game->cardsLeft());

        // The player tries to draw a random card, the hand stays the same
        self::$game->playerNewCard();

        $this->assertEquals("3", self::$game->playerWorth());
        $this->assertEquals(0, self::$game->cardsLeft());

        // The bank tries to play but gets an empty hand and loses
        self::$game->bankTurn();

        $this->assertEquals(0, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->playerWins());
        $this->assertEquals(0, self::$game->cardsLeft());
        $this->assertEquals(array_fill(0, 14, 0), self::$game->cardCount());
   }

    /**
     * A similar test as above with a smaller card deck (1 suit), but don´t let the bank use
     * probabilities, which lets the player win a few more rounds.
     */
    public function testBankNoChanceNoCrashEmptyDeck(): void
    {
        // Let the player use chance, but the bank not (the player chance is not used in the test).
        // Let the player draw an initial king, and use just one suit.
        self::$game->newGame("Test", true, false, 13, 1);

        // The player draws a six and stops
        self::$game->playerNewCard(6);

        // The bank plays and loses (the smarter bank would have drawn more cards)
        self::$game->bankTurn([12, 5]);

        $this->assertEquals("19", self::$game->playerWorth());
        $this->assertEquals(17, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->playerWins());
        $this->assertEquals(9, self::$game->cardsLeft());
        $this->assertEquals([9, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0], self::$game->cardCount());

        // The player starts a new round with an initial knight
        self::$game->nextRound(11);

        // The player draws a ten and stops
        self::$game->playerNewCard(10);

        // The bank plays and loses (the smarter bank would have drawn more cards)
        self::$game->bankTurn([8, 9]);

        $this->assertEquals("21", self::$game->playerWorth());
        $this->assertEquals(17, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(2, self::$game->playerWins());
        $this->assertEquals(5, self::$game->cardsLeft());
        $this->assertEquals([5, 1, 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0], self::$game->cardCount());

        // Skip the player's turn just for test (the player's hand i still worth 21) and let
        // the bank play and win.
        self::$game->bankTurn([1, 7]);

        $this->assertEquals("21", self::$game->playerWorth());
        $this->assertEquals(21, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(1, self::$game->bankWins());
        $this->assertEquals(3, self::$game->cardsLeft());
        $this->assertEquals([3, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0], self::$game->cardCount());

        // The player starts a new round and draws the three final cards
        self::$game->nextRound();
        self::$game->playerNewCard();
        self::$game->playerNewCard();

        $this->assertEquals("9", self::$game->playerWorth());
        $this->assertEquals(0, self::$game->cardsLeft());

        // The player tries to draw another card, but the hand stays the same
        self::$game->playerNewCard();

        $this->assertEquals("9", self::$game->playerWorth());
        $this->assertEquals(0, self::$game->cardsLeft());

        // The bank tries to play but gets an empty hand and loses
        self::$game->bankTurn();

        $this->assertEquals(0, self::$game->bankHand()->getMaxPoints21());
        $this->assertEquals(3, self::$game->playerWins());
        $this->assertEquals(0, self::$game->cardsLeft());
        $this->assertEquals(array_fill(0, 14, 0), self::$game->cardCount());
   }
}
