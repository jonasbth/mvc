<?php

namespace App\Controller;

use App\Card\CardDeck;
use App\Card\CardHand;
use App\Game\Game21;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route("/game", name: "game_index")]
    public function game(): Response
    {
        return $this->render("game/index.html.twig");
    }

    #[Route("/game/doc", name: "game_doc")]
    public function gameDoc(): Response
    {
        return $this->render("game/doc.html.twig");
    }

    #[Route("/game/reset", name: "game_reset")]
    public function gameReset(SessionInterface $session): Response
    {
        // Reset session
        $session->invalidate();

        return $this->redirectToRoute("game_index");
    }

    #[Route("/game/init", name: "game_init", methods: ["GET"])]
    public function gameInit(SessionInterface $session): Response
    {
        if ($session->has("game")) {
            /**
             *  @var Game21 $game
             */
            $game = $session->get("game");
            $playerName = $game->playerName();
            $playerUseChance = $game->playerUseChance();
            $bankUseChance = $game->bankUseChance();
        } else {
            $playerName = "";
            $playerUseChance = false;
            $bankUseChance = false;
        }

        $data = [
            "player_name" => $playerName,
            "player_chance" => $playerUseChance,
            "bank_chance" => $bankUseChance
        ];

        return $this->render("game/init.html.twig", $data);
    }

    #[Route("/game/init", name: "game_init_post", methods: ["POST"])]
    public function gameInitPost(
        Request $request,
        SessionInterface $session
    ): Response {
        // $session->invalidate();
        $playerName = (string)$request->request->get("player_name");
        $playerChance = ($request->request->get("player_chance") ?? "off") === "on";
        $bankChance = ($request->request->get("bank_chance") ?? "off") === "on";
        $game = new Game21();
        $game->newGame($playerName, $playerChance, $bankChance);

        $session->set("game", $game);

        return $this->redirectToRoute("game_play");
    }

    #[Route("/game/play", name: "game_play", methods: ["GET"])]
    public function gamePlay(SessionInterface $session): Response
    {
        $game = $session->get("game");

        $data = [
            "game" => $game
        ];

        return $this->render("game/play.html.twig", $data);
    }

    #[Route("/game/play", name: "game_play_post", methods: ["POST"])]
    public function gamePlayPost(
        Request $request,
        SessionInterface $session
    ): Response {
        if ($session->has("game")) {
            /**
             *  @var Game21 $game
             */
            $game = $session->get("game");
        } else {
            return $this->redirectToRoute("game_init");
        }

        if ($request->request->has("new_card")) {
            $game->playerNewCard();

        } elseif ($request->request->has("next_round")) {
            $game->nextRound();

        } elseif ($request->request->has("stop")) {
            $game->bankTurn();
        } else {    // new_game
            return $this->redirectToRoute("game_init");
        }

        return $this->redirectToRoute("game_play");
    }

}
