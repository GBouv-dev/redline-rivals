<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\DeckRepository;
use App\Repository\BattleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private UserRepository         $userRepository,
        private EntityManagerInterface $em,
    ) {}

    // ── Dashboard ──────────────────────────────────────────────────────────
    #[Route('', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'totalUsers'   => $this->userRepository->count([]),
            'totalAdmins'  => count(array_filter(
                $this->userRepository->findAll(),
                fn($u) => in_array('ROLE_ADMIN', $u->getRoles(), true)
            )),
        ]);
    }

    // ── Liste des utilisateurs ─────────────────────────────────────────────
    #[Route('/users', name: 'app_admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    // ── Toggle ROLE_ADMIN ──────────────────────────────────────────────────
    #[Route('/users/{id}/role', name: 'app_admin_toggle_role', methods: ['POST'])]
    public function toggleRole(User $user): JsonResponse
    {
        // Impossible de retirer son propre rôle admin
        if ($user === $this->getUser()) {
            return $this->json(['success' => false, 'message' => 'Impossible de modifier votre propre rôle.'], 400);
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $user->setRoles(array_values(array_filter($roles, fn($r) => $r !== 'ROLE_ADMIN' && $r !== 'ROLE_USER')));
            $isAdmin = false;
        } else {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique(array_filter($roles, fn($r) => $r !== 'ROLE_USER')));
            $isAdmin = true;
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'isAdmin' => $isAdmin,
            'message' => $isAdmin ? 'Rôle ADMIN accordé.' : 'Rôle ADMIN retiré.',
        ]);
    }

    // ── Modifier les crédits ───────────────────────────────────────────────
    #[Route('/users/{id}/credits', name: 'app_admin_set_credits', methods: ['POST'])]
    public function setCredits(User $user, Request $request): JsonResponse
    {
        $amount = (int) $request->request->get('credits');
        if ($amount < 0) {
            return $this->json(['success' => false, 'message' => 'Montant invalide.'], 400);
        }

        $user->setCredits($amount);
        $this->em->flush();

        return $this->json(['success' => true, 'credits' => $user->getCredits()]);
    }
}