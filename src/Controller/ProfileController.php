<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AuctionRepository;
use App\Repository\MarketListingRepository;
use App\Repository\UserCardRepository;
use App\Repository\UserBoosterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserCardRepository $userCardRepository,
        private UserBoosterRepository $userBoosterRepository,
        private MarketListingRepository $marketListingRepository,
        private AuctionRepository $auctionRepository,
    ) {
    }

    // ── Mon profil ────────────────────────────────────────────────────────────

    #[Route('', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('profile/index.html.twig', $this->buildProfileData($user, true));
    }

    // ── Profil public ─────────────────────────────────────────────────────────

    #[Route('/view/{id}', name: 'app_profile_view')]
    public function view(User $user): Response
    {
        return $this->render('profile/index.html.twig', $this->buildProfileData($user, false));
    }

    // ── Mise à jour pseudo + avatar ───────────────────────────────────────────

    #[Route('/update', name: 'app_profile_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $username = trim($request->request->get('username', ''));
        $avatar = trim($request->request->get('avatar', ''));

        if ($username !== '' && strlen($username) >= 3 && strlen($username) <= 30) {
            $user->setUsername($username);
        } elseif ($username !== '') {
            return $this->json(['success' => false, 'message' => 'Le pseudo doit faire entre 3 et 30 caractères.'], 400);
        }

        if ($avatar !== '') {
            $user->setAvatar($avatar);
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Profil mis à jour !',
            'username' => $user->getUsername() ?? $user->getEmail(),
            'avatar' => $user->getAvatar() ?? '🏎️',
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function buildProfileData(User $user, bool $isOwner): array
    {
        $userCards = $this->userCardRepository->findBy(['user' => $user]);
        $userBoosters = $this->userBoosterRepository->findBy(['user' => $user]);
        $myListings = $this->marketListingRepository->findBySellerOrderedByDate($user);
        $myAuctions = $this->auctionRepository->findBySellerOrBidder($user);

        // Stats
        $totalCards = array_sum(array_map(fn($uc) => $uc->getQuantity(), $userCards));
        $uniqueCards = count($userCards);
        $soldListings = array_filter($myListings, fn($l) => $l->isSold());
        $totalEarned = array_sum(array_map(fn($l) => $l->getPrice(), $soldListings));
        $wonAuctions = array_filter($myAuctions, fn($a) => $a->getWinner() === $user);

        // Répartition par rareté — compatible enum PHP 8.1 ET string classique
        $rarityCount = ['common' => 0, 'rare' => 0, 'epic' => 0, 'legendary' => 0, 'mythic' => 0];
        foreach ($userCards as $uc) {
            $rarity = $uc->getCard()->getRarity();
            $rarityKey = strtolower($rarity instanceof \BackedEnum ? $rarity->value : (string) $rarity);
            if (isset($rarityCount[$rarityKey])) {
                $rarityCount[$rarityKey] += $uc->getQuantity();
            }
        }

        return [
            'profileUser' => $user,
            'isOwner' => $isOwner,
            'userCards' => $userCards,
            'userBoosters' => $userBoosters,
            'myListings' => $myListings,
            'myAuctions' => $myAuctions,
            'stats' => [
                'credits' => $user->getCredits(),
                'totalCards' => $totalCards,
                'uniqueCards' => $uniqueCards,
                'boosters' => count($userBoosters),
                'totalEarned' => $totalEarned,
                'soldCount' => count($soldListings),
                'wonAuctions' => count($wonAuctions),
            ],
            'rarityCount' => $rarityCount,
        ];
    }
}