<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Battle;
use App\Entity\BoosterType;
use App\Entity\Card;
use App\Entity\MarketListing;
use App\Entity\UserBooster;
use App\Repository\BattleRepository;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Service\AuctionService;
use App\Service\BoosterService;
use App\Service\MarketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/game')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    public function __construct(
        private BoosterService $boosterService,
        private MarketService $marketService,
        private AuctionService $auctionService,
        private EntityManagerInterface $em,
        private BattleRepository $battleRepository,
        private DeckRepository $deckRepository,
        private CardRepository $cardRepository,
    ) {
    }

    // =========================================================================
    // ARENA
    // =========================================================================

    #[Route('/arena', name: 'app_game_arena')]
    public function arena(): Response
    {
        $user = $this->getUser();
        return $this->render('game/arena.html.twig', [
            'waitingBattles' => $this->battleRepository->findWaitingBattles($user),
            'activeBattles' => $this->battleRepository->findUserActiveBattles($user),
            'userDecks' => $this->deckRepository->findBy(['owner' => $user]),
        ]);
    }

    /** Créer un nouveau combat (lobby). */
    #[Route('/arena/create', name: 'app_game_arena_create', methods: ['POST'])]
    public function createBattle(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $deck = $this->deckRepository->find((int) $request->request->get('deck_id'));

        if (!$deck || $deck->getOwner() !== $user) {
            return $this->json(['success' => false, 'message' => 'Deck invalide.'], 400);
        }

        $cardIds = array_map(fn($card) => $card->getId(), $deck->getCards()->toArray());
        if (count($cardIds) < 3) {
            return $this->json(['success' => false, 'message' => 'Il faut au moins 3 cartes dans le deck.'], 400);
        }
        shuffle($cardIds);

        $battle = (new Battle())
            ->setPlayer1($user)
            ->setDeck1($deck)
            ->setGameState([
                'p1Hand' => $cardIds,
                'p2Hand' => [],
                'p1Played' => null,
                'p2Played' => null,
                'rounds' => [],
            ]);

        $this->em->persist($battle);
        $this->em->flush();

        return $this->json(['success' => true, 'battleId' => $battle->getId()]);
    }

    /** Rejoindre un combat en attente. */
    #[Route('/arena/{id}/join', name: 'app_game_arena_join', methods: ['POST'])]
    public function joinBattle(Battle $battle, Request $request): JsonResponse
    {
        $user = $this->getUser();

        if ($battle->getStatus() !== Battle::STATUS_WAITING) {
            return $this->json(['success' => false, 'message' => 'Combat non disponible.'], 400);
        }
        if ($battle->getPlayer1() === $user) {
            return $this->json(['success' => false, 'message' => 'Vous avez créé ce combat.'], 400);
        }

        $deck = $this->deckRepository->find((int) $request->request->get('deck_id'));
        if (!$deck || $deck->getOwner() !== $user) {
            return $this->json(['success' => false, 'message' => 'Deck invalide.'], 400);
        }

        $cardIds = array_map(fn($card) => $card->getId(), $deck->getCards()->toArray());
        shuffle($cardIds);

        $gs = $battle->getGameState();
        $gs['p2Hand'] = $cardIds;

        $battle->setPlayer2($user)
            ->setDeck2($deck)
            ->setStatus(Battle::STATUS_ACTIVE)
            ->setGameState($gs);

        $this->em->flush();

        return $this->json(['success' => true, 'battleId' => $battle->getId()]);
    }

    /** Page de jeu. */
    #[Route('/arena/{id}', name: 'app_game_arena_battle')]
    public function battleGame(Battle $battle): Response
    {
        $user = $this->getUser();
        if ($battle->getPlayer1() !== $user && $battle->getPlayer2() !== $user) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('game/battle.html.twig', ['battle' => $battle]);
    }

    /** Jouer une carte. */
    #[Route('/arena/{id}/play', name: 'app_game_arena_play', methods: ['POST'])]
    public function playCard(Battle $battle, Request $request): JsonResponse
    {
        $user = $this->getUser();
        $cardId = (int) $request->request->get('card_id');

        if ($battle->getStatus() !== Battle::STATUS_ACTIVE) {
            return $this->json(['success' => false, 'message' => 'Combat non actif.'], 400);
        }

        $isP1 = ($battle->getPlayer1() === $user);
        $isP2 = ($battle->getPlayer2() === $user);
        if (!$isP1 && !$isP2) {
            return $this->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $gs = $battle->getGameState();
        $playedKey = $isP1 ? 'p1Played' : 'p2Played';
        $handKey = $isP1 ? 'p1Hand' : 'p2Hand';

        if ($gs[$playedKey] !== null) {
            return $this->json(['success' => false, 'message' => 'Vous avez déjà joué ce tour.'], 400);
        }
        if (!in_array($cardId, $gs[$handKey], true)) {
            return $this->json(['success' => false, 'message' => 'Carte introuvable dans votre main.'], 400);
        }

        $gs[$handKey] = array_values(array_filter($gs[$handKey], fn($id) => $id !== $cardId));
        $gs[$playedKey] = $cardId;

        // Les deux ont joué → résolution du round
        if ($gs['p1Played'] !== null && $gs['p2Played'] !== null) {
            $card1 = $this->cardRepository->find($gs['p1Played']);
            $card2 = $this->cardRepository->find($gs['p2Played']);

            // Critère du round : rotation parmi 4 thèmes
            $roundIndex = count($gs['rounds']);
            $criteria = ['horsepower', 'speed', 'acceleration', 'handling'];
            $criterion = $criteria[$roundIndex % count($criteria)];

            [$val1, $val2, $roundWinner] = $this->resolveCriterion($card1, $card2, $criterion);

            $gs['rounds'][] = [
                'p1CardId' => $gs['p1Played'],
                'p2CardId' => $gs['p2Played'],
                'p1Name' => $card1->getName(),
                'p2Name' => $card2->getName(),
                'p1Val' => $val1,
                'p2Val' => $val2,
                'criterion' => $criterion,
                'p1ImagePath' => $card1->getImagePath(),
                'p2ImagePath' => $card2->getImagePath(),
                'winner' => $roundWinner,
            ];

            $gs['p1Played'] = null;
            $gs['p2Played'] = null;

            if ($roundWinner === 1)
                $battle->setRoundsP1($battle->getRoundsP1() + 1);
            elseif ($roundWinner === 2)
                $battle->setRoundsP2($battle->getRoundsP2() + 1);

            $gameOver = $battle->getRoundsP1() >= 3
                || $battle->getRoundsP2() >= 3
                || (empty($gs['p1Hand']) && empty($gs['p2Hand']));

            if ($gameOver) {
                $battle->setStatus(Battle::STATUS_FINISHED);
                $winner = match (true) {
                    $battle->getRoundsP1() > $battle->getRoundsP2() => $battle->getPlayer1(),
                    $battle->getRoundsP2() > $battle->getRoundsP1() => $battle->getPlayer2(),
                    default => null,
                };
                $battle->setWinner($winner);
                if ($winner && method_exists($winner, 'setCredits')) {
                    $winner->setCredits($winner->getCredits() + 500);
                }
            }
        }

        $battle->setGameState($gs);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    private function resolveCriterion(Card $c1, Card $c2, string $criterion): array
    {
        // Pour l'accélération : plus bas = mieux, donc on inverse
        $invert = ($criterion === 'acceleration');

        $v1 = match ($criterion) {
            'horsepower' => $c1->getHorsepower() ?? 0,
            'speed' => $c1->getSpeed() ?? 0,
            'acceleration' => $c1->getAcceleration() ?? 99.0,
            'handling' => $c1->getHandling() ?? 0,
            default => $c1->getHorsepower() ?? 0,
        };
        $v2 = match ($criterion) {
            'horsepower' => $c2->getHorsepower() ?? 0,
            'speed' => $c2->getSpeed() ?? 0,
            'acceleration' => $c2->getAcceleration() ?? 99.0,
            'handling' => $c2->getHandling() ?? 0,
            default => $c2->getHorsepower() ?? 0,
        };

        if ($v1 === $v2) {
            $roundWinner = 0;
        } elseif ($invert) {
            $roundWinner = $v1 < $v2 ? 1 : 2;
        } else {
            $roundWinner = $v1 > $v2 ? 1 : 2;
        }

        return [$v1, $v2, $roundWinner];
    }

    /** Polling : état du combat (appelé toutes les 2s par le JS). */
    #[Route('/arena/{id}/state', name: 'app_game_arena_state', methods: ['GET'])]
    public function battleState(Battle $battle): JsonResponse
    {
        $user = $this->getUser();
        $isP1 = ($battle->getPlayer1() === $user);
        $gs = $battle->getGameState() ?? [];

        $playedKey = $isP1 ? 'p1Played' : 'p2Played';
        $opponentPlayedKey = $isP1 ? 'p2Played' : 'p1Played';
        $handKey = $isP1 ? 'p1Hand' : 'p2Hand';

        $hand = array_map(
            fn($id) => $this->cardToArray($this->cardRepository->find($id)),
            $gs[$handKey] ?? []
        );

        // Dernier round pour l'animation
        $rounds = $gs['rounds'] ?? [];
        $lastRound = !empty($rounds) ? end($rounds) : null;

        return $this->json([
            'status' => $battle->getStatus(),
            'player2Joined' => $battle->getPlayer2() !== null,
            'iHavePlayed' => ($gs[$playedKey] ?? null) !== null,
            'opponentPlayed' => ($gs[$opponentPlayedKey] ?? null) !== null,
            'hand' => $hand,
            'roundsMe' => $isP1 ? $battle->getRoundsP1() : $battle->getRoundsP2(),
            'roundsOpponent' => $isP1 ? $battle->getRoundsP2() : $battle->getRoundsP1(),
            'totalRounds' => count($rounds),
            'lastRound' => $lastRound,
            'iAmWinner' => $battle->getWinner() === $user,
            'winnerId' => $battle->getWinner()?->getId(),
            'myPlayerNumber' => $isP1 ? 1 : 2,
        ]);
    }

    private function cardToArray(Card $card): array
    {
        return [
            'id' => $card->getId(),
            'name' => $card->getName(),
            'brand' => $card->getBrand(),
            'horsepower' => $card->getHorsepower(),
            'speed' => $card->getSpeed(),
            'acceleration' => $card->getAcceleration(),
            'handling' => $card->getHandling(),
            'rarity' => $card->getRarity(),
            'imagePath' => $card->getImagePath(),
        ];
    }
    // =========================================================================
    // SHOP
    // =========================================================================

    #[Route('/shop', name: 'app_game_shop')]
    public function shop(): Response
    {
        $user = $this->getUser();
        $boosters = $this->boosterService->getAvailableBoosters();
        $userBoostersCount = $this->boosterService->countUserBoosters($user);

        return $this->render('game/shop.html.twig', [
            'boosters' => $boosters,
            'userBoostersCount' => $userBoostersCount,
        ]);
    }

    #[Route('/shop/buy/{id}', name: 'app_game_shop_buy', methods: ['POST'])]
    public function buyBooster(BoosterType $boosterType): JsonResponse
    {
        try {
            $user = $this->getUser();
            $this->boosterService->purchaseBooster($user, $boosterType);

            return $this->json([
                'success' => true,
                'message' => 'Booster acheté avec succès !',
                'credits' => method_exists($user, 'getCredits') ? $user->getCredits() : 0,
                'boostersCount' => $this->boosterService->countUserBoosters($user),
            ]);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // =========================================================================
    // INVENTORY
    // =========================================================================

    #[Route('/inventory', name: 'app_game_inventory')]
    public function inventory(): Response
    {
        $user = $this->getUser();
        return $this->render('game/inventory.html.twig', [
            'boosters' => $this->boosterService->getUserBoosters($user),
        ]);
    }

    #[Route('/booster/open/{id}', name: 'app_game_booster_open', methods: ['POST'])]
    public function openBooster(UserBooster $userBooster): JsonResponse
    {
        try {
            $user = $this->getUser();
            $cards = $this->boosterService->openBooster($user, $userBooster);

            $cardsData = array_map(function ($userCard) {
                $card = $userCard->getCard();
                return [
                    'id' => $card->getId(),
                    'name' => $card->getName(),
                    'brand' => $card->getBrand(),
                    'horsepower' => $card->getHorsepower(),
                    'rarity' => $card->getRarity(),
                    'imagePath' => $card->getImagePath(),
                    'isNew' => $userCard->getQuantity() === 1,
                ];
            }, $cards);

            return $this->json([
                'success' => true,
                'cards' => $cardsData,
                'boostersCount' => $this->boosterService->countUserBoosters($user),
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // =========================================================================
    // MARKET
    // =========================================================================

    #[Route('/market', name: 'app_game_market')]
    public function market(Request $request): Response
    {
        $user = $this->getUser();
        $rarity = $request->query->get('rarity');
        $brand = $request->query->get('brand');
        $maxPrice = $request->query->getInt('maxPrice') ?: null;
        $search = $request->query->get('search');

        return $this->render('game/market.html.twig', [
            'listings' => $this->marketService->getActiveListings($rarity, $brand, $maxPrice, $search),
            'myListings' => $this->marketService->getUserListings($user),
            'sellableCards' => $this->marketService->getSellableCards($user),
            'filters' => compact('rarity', 'brand', 'maxPrice', 'search'),
        ]);
    }

    #[Route('/market/list', name: 'app_game_market_list', methods: ['POST'])]
    public function createListing(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $card = $this->marketService->getCardById((int) $request->request->get('card_id'));
            if (!$card)
                return $this->json(['success' => false, 'message' => 'Carte introuvable.'], 404);

            $listing = $this->marketService->createListing(
                $user,
                $card,
                (int) $request->request->get('price'),
                (int) ($request->request->get('quantity') ?? 1)
            );

            return $this->json(['success' => true, 'listingId' => $listing->getId(), 'credits' => $user->getCredits()]);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/market/buy/{id}', name: 'app_game_market_buy', methods: ['POST'])]
    public function buyListing(MarketListing $listing): JsonResponse
    {
        try {
            $user = $this->getUser();
            $this->marketService->purchaseListing($user, $listing);
            return $this->json(['success' => true, 'message' => sprintf('✅ Vous avez acheté "%s" !', $listing->getCard()->getName()), 'credits' => $user->getCredits()]);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/market/cancel/{id}', name: 'app_game_market_cancel', methods: ['POST'])]
    public function cancelListing(MarketListing $listing): JsonResponse
    {
        try {
            $this->marketService->cancelListing($this->getUser(), $listing);
            return $this->json(['success' => true, 'message' => 'Annonce annulée.']);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // =========================================================================
    // AUCTIONS
    // =========================================================================

    #[Route('/auctions', name: 'app_game_auctions')]
    public function auctions(Request $request): Response
    {
        $user = $this->getUser();
        $rarity = $request->query->get('rarity');
        $search = $request->query->get('search');

        return $this->render('game/auction.html.twig', [
            'auctions' => $this->auctionService->getActiveAuctions($rarity, $search),
            'myAuctions' => $this->auctionService->getUserAuctions($user),
            'sellableCards' => $this->auctionService->getSellableCards($user),
            'filters' => compact('rarity', 'search'),
        ]);
    }

    #[Route('/auctions/create', name: 'app_game_auction_create', methods: ['POST'])]
    public function createAuction(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $card = $this->auctionService->getCardById((int) $request->request->get('card_id'));
            if (!$card)
                return $this->json(['success' => false, 'message' => 'Carte introuvable.'], 404);

            $auction = $this->auctionService->createAuction(
                $user,
                $card,
                (int) $request->request->get('start_price'),
                (int) ($request->request->get('duration_hours') ?? 24),
                (int) ($request->request->get('quantity') ?? 1)
            );

            return $this->json(['success' => true, 'auctionId' => $auction->getId()]);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/auctions/bid/{id}', name: 'app_game_auction_bid', methods: ['POST'])]
    public function placeBid(Auction $auction, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $amount = (int) $request->request->get('amount');
            $this->auctionService->placeBid($user, $auction, $amount);

            return $this->json([
                'success' => true,
                'message' => sprintf('Enchère de %d crédits placée !', $amount),
                'currentPrice' => $auction->getCurrentPrice(),
                'minimumNext' => $auction->getMinimumNextBid(),
                'bidCount' => $auction->getBidCount(),
                'credits' => $user->getCredits(),
            ]);
        } catch (\RuntimeException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/auctions/{id}/bids', name: 'app_game_auction_bids', methods: ['GET'])]
    public function getAuctionBids(Auction $auction): JsonResponse
    {
        $bids = $auction->getBids()->map(fn($bid) => [
            'bidder' => $bid->getBidder()->getEmail(),
            'amount' => $bid->getAmount(),
            'placedAt' => $bid->getPlacedAt()->format('d/m H:i:s'),
        ])->toArray();

        return $this->json([
            'currentPrice' => $auction->getCurrentPrice(),
            'minimumNext' => $auction->getMinimumNextBid(),
            'bidCount' => $auction->getBidCount(),
            'bids' => $bids,
            'secondsLeft' => $auction->getSecondsRemaining(),
        ]);
    }
}