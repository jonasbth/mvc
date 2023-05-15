<?php

namespace App\Controller;

use App\Card\CardDeck;
use App\Card\CardHand;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardControllerJson
{
    #[Route("/api/deck", name: "api_deck", methods: ['GET'])]
    public function jsonDeck(SessionInterface $session): JsonResponse
    {
        $deck = new CardDeck();
        $session->set("deck", $deck);

        $deckArr = [];

        foreach ($deck as $card) {
            $deckArr[] = $card->getSuit() . " " . $card->getRank();
        }

        $data = [
            'deck' => $deckArr
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($encOptions);

        return $response;
    }

    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ['GET', 'POST'])]
    public function jsonDeckShuffle(SessionInterface $session): JsonResponse
    {
        $deck = new CardDeck();
        $deck->shuffle(2);
        $session->set("deck", $deck);

        $deckArr = [];

        foreach ($deck as $card) {
            $deckArr[] = $card->getSuit() . " " . $card->getRank();
        }

        $data = [
            'deck' => $deckArr
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($encOptions);

        return $response;
    }

    #[Route("/api/deck/draw/{num<\d{1,2}>?1}", name: "api_deck_draw", methods: ['GET', 'POST'])]
    public function apiDeckDraw(int $num, SessionInterface $session): JsonResponse
    {
        if ($session->has("deck")) {
            /**
             *  @var CardDeck $deck
             */
            $deck = $session->get("deck");
        } else {
            $deck = new CardDeck();
            $deck->shuffle();
        }

        $cardHand = new CardHand("Player", $deck->draw($num));

        $session->set("deck", $deck);

        $cardArr = [];

        foreach ($cardHand as $card) {
            $cardArr[] = $card->getSuit() . " " . $card->getRank();
        }

        $data = [
            'count' => $deck->count(),
            'cardHand' => $cardArr
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($encOptions);

        return $response;
    }

    #[Route(
        "/api/deck/deal/{players<\d{1,2}>}/{num<\d{1,2}>}",
        name: "api_deck_deal",
        methods: ['GET', 'POST']
    )]
    public function apiDeckDeal(int $players, int $num, SessionInterface $session): JsonResponse
    {
        if ($session->has("deck")) {
            /**
             *  @var CardDeck $deck
             */
            $deck = $session->get("deck");
        } else {
            $deck = new CardDeck();
            $deck->shuffle();
        }

        $playerArr = [];

        for ($i = 1; $i <= $players; $i++) {
            $cardHand = new CardHand("Player " . $i, $deck->draw($num));
            $cardArr = [];

            foreach ($cardHand as $card) {
                $cardArr[] = $card->getSuit() . " " . $card->getRank();
            }

            $playerArr[] = [
                'name' => $cardHand->getName(),
                'hand' => $cardArr
            ];
        }

        $session->set("deck", $deck);

        $data = [
            'count' => $deck->count(),
            'players' => $playerArr
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($encOptions);

        return $response;
    }

    #[Route("/api/game", name: "api_game", methods: ['GET'])]
    public function jsonGame(SessionInterface $session): JsonResponse
    {
        if ($session->has("game")) {
            /**
             *  @var \App\Game\Game21 $game
             */
            $game = $session->get("game");
        } else {
            $data = [
                'error_msg' => "No active game session"
            ];

            $response = new JsonResponse($data);

            $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
            $response->setEncodingOptions($encOptions);

            return $response;
        }

        // Player info
        $hand = $game->playerHand();
        $cardArr = [];

        foreach ($hand as $card) {
            $cardArr[] = $card->getSuit() . " " . $card->getRank();
        }

        $player = [
            'player' => $hand->getName(),
            'hand' => $cardArr
        ];

        // Bank info
        $hand = $game->bankHand();
        $cardArr = [];

        if (!$game->playersTurn()) {
            foreach ($hand as $card) {
                $cardArr[] = $card->getSuit() . " " . $card->getRank();
            }
        }

        $bank = [
            'player' => $hand->getName(),
            'hand' => $cardArr
        ];

        // Assemble all data
        $data = [
            'round' => $game->round(),
            'player_wins' => $game->playerWins(),
            'bank_wins' => $game->bankWins(),
            'cards_left' => $game->cardsLeft(),
            'players_turn' => $game->playersTurn(),
            'players' => [$player, $bank]
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT;
        $response->setEncodingOptions($encOptions);

        return $response;
    }
}
