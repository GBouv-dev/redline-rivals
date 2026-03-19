<?php

namespace App\Controller;

use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    // Injection du CardRepository pour accéder aux données des cartes
    public function index(CardRepository $cardRepository): Response
    {
        // 1. Récupère les 4 dernières cartes créées (pour une galerie d'accueil)
        $latestCards = $cardRepository->findBy([], ['id' => 'DESC'], 4);

        return $this->render('home/index.html.twig', [
            'latestCards' => $latestCards, // <-- Les données sont passées à la vue
        ]);
    }
}