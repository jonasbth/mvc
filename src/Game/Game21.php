<?php

namespace App\Game;

use App\Card\CardDeck;
use App\Card\CardHand;

class Game21
{
    private CardDeck $deck;
    private CardHand $playerHand;
    private CardHand $bankHand;
    private int $round;
    private int $playerWins;
    private int $bankWins;
    private bool $playersTurn;

    public function __construct()
    {
    }

    /**
     *  Return number of cards left.
     *
     *  @return int  Number of cards.
     */
    public function cardsLeft(): int
    {
        return $this->deck->count();
    }

    /**
     *  Return player name.
     *
     *  @return string  The name.
     */
    public function playerName(): string
    {
        return $this->playerHand->getName();
    }

    /**
     *  Return bank name.
     *
     *  @return string  The name.
     */
    public function bankName(): string
    {
        return $this->bankHand->getName();
    }

    /**
     *  Return player hand.
     *
     *  @return CardHand  The hand.
     */
    public function playerHand(): CardHand
    {
        return $this->playerHand;
    }

    /**
     *  Return bank hand.
     *
     *  @return CardHand  The hand.
     */
    public function bankHand(): CardHand
    {
        return $this->bankHand;
    }

    /**
     *  Return the round.
     *
     *  @return int  The round.
     */
    public function round(): int
    {
        return $this->round;
    }

    /**
     *  Return number of player wins.
     *
     *  @return int  The number.
     */
    public function playerWins(): int
    {
        return $this->playerWins;
    }

    /**
     *  Return number of bank wins.
     *
     *  @return int  The number.
     */
    public function bankWins(): int
    {
        return $this->bankWins;
    }

    /**
     *  Return whether it is the player´s turn.
     *
     *  @return bool  true if it is the player's turn.
     */
    public function playersTurn(): bool
    {
        return $this->playersTurn;
    }

    /**
     *  Return a string describing the value of the player's hand.
     *
     *  @return string  The description.
     */
    public function playerWorth(): string
    {
        $points = $this->playerHand->getPoints21();

        if ($this->playersTurn) {
            return "$points[0]" . (count($points) > 1 ? " eller " . $points[1] : "");
        }

        return "{$this->playerHand->getMaxPoints21()}";
    }

    /**
     *  Initiate a new game of "21".
     *
     *  @param string $playerName  The player´s name.
     */
    public function newGame(string $playerName): void
    {
        $this->round = 1;
        $this->playerWins = 0;
        $this->bankWins = 0;
        $this->playersTurn = true;

        $this->deck = new CardDeck();
        $this->deck->shuffle(2);

        $this->playerHand = new CardHand($playerName, $this->deck->draw(1));
        $this->bankHand = new CardHand("Banken");
    }

    /**
     *  Initiate a new round.
     *
     */
    public function nextRound(): void
    {
        $this->round++;
        $this->playerHand->reset();
        $this->playerHand->addCards($this->deck->draw(1));
        $this->playersTurn = true;
    }

    /**
     *  Let the player draw one card.
     *
     */
    public function playerNewCard(): void
    {
        $this->playerHand->addCards($this->deck->draw(1));
        $points = $this->playerHand->getPoints21();

        if ($points[0] > 21) {
            $this->bankWins++;
        }
    }

    /**
     *  It's the banks turn to play a round.
     *
     */
    public function bankTurn(): void
    {
        $this->playersTurn = false;
        $this->bankPlay();
        $bankPoints = $this->bankHand->getMaxPoints21();

        if ($bankPoints <= 21 && $bankPoints >= $this->playerHand->getMaxPoints21()) {
            $this->bankWins++;
        } else {
            $this->playerWins++;
        }
    }

    /**
     *  Let the bank play one round of "21".
     *
     */
    public function bankPlay(): void
    {
        $this->bankHand->reset();
        $this->bankHand->addCards($this->deck->draw(2));
        $maxPoints = $this->bankHand->getMaxPoints21();

        while ($maxPoints < 17 && $this->cardsLeft() > 0) {
            $this->bankHand->addCards($this->deck->draw(1));
            $maxPoints = $this->bankHand->getMaxPoints21();
        }
    }
}
