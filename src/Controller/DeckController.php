<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Form\DeckType;
use App\Repository\DeckRepository;
use App\Repository\CardRepository;
use App\Repository\UserCardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/deck')]
#[IsGranted('ROLE_USER')]
final class DeckController extends AbstractController
{
    #[Route(name: 'app_deck_index', methods: ['GET'])]
    public function index(DeckRepository $deckRepository): Response
    {
        $user = $this->getUser();
        return $this->render('deck/index.html.twig', [
            'decks' => $deckRepository->findBy(['owner' => $user]),
        ]);
    }

    #[Route('/new', name: 'app_deck_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CardRepository $cardRepository,
        UserCardRepository $userCardRepository
    ): Response {
        $deck = new Deck();
        $deck->setMaxCards(30);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        // Cartes possédées par l'utilisateur (pour affichage ET validation)
        $userCards      = $userCardRepository->findBy(['user' => $user]);
        $ownedCardIds   = array_map(fn($uc) => $uc->getCard()->getId(), $userCards);
        $availableCards = array_map(fn($uc) => $uc->getCard(), $userCards);

        if ($form->isSubmitted() && $form->isValid()) {

            $deck->setOwner($user);

            $selectedCardIds = $request->request->get('selected_cards');

            if ($selectedCardIds) {
                $cardIds = array_filter(
                    array_map('intval', explode(',', $selectedCardIds))
                );

                if (count($cardIds) > $deck->getMaxCards()) {
                    $this->addFlash('error', 'Vous ne pouvez pas ajouter plus de ' . $deck->getMaxCards() . ' cartes.');
                    return $this->render('deck/new.html.twig', [
                        'deck'            => $deck,
                        'form'            => $form,
                        'available_cards' => $availableCards,
                    ]);
                }

                foreach ($cardIds as $cardId) {
                    // Vérification : la carte doit être dans l'inventaire du joueur
                    if (!in_array($cardId, $ownedCardIds, true)) {
                        $this->addFlash('error', 'Une ou plusieurs cartes sélectionnées ne sont pas dans votre inventaire.');
                        return $this->render('deck/new.html.twig', [
                            'deck'            => $deck,
                            'form'            => $form,
                            'available_cards' => $availableCards,
                        ]);
                    }

                    $card = $cardRepository->find($cardId);
                    if ($card) $deck->addCard($card);
                }
            }

            $entityManager->persist($deck);
            $entityManager->flush();

            $this->addFlash('success', 'Deck créé avec succès !');
            return $this->redirectToRoute('app_deck_show', ['id' => $deck->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deck/new.html.twig', [
            'deck'            => $deck,
            'form'            => $form,
            'available_cards' => $availableCards,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_show', methods: ['GET'])]
    public function show(Deck $deck): Response
    {
        if ($deck->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $currentCards = $deck->getCards()->count();
        $percentage   = $deck->getMaxCards() > 0
            ? round(($currentCards / $deck->getMaxCards() * 100), 1)
            : 0;

        return $this->render('deck/show.html.twig', [
            'deck'          => $deck,
            'current_cards' => $currentCards,
            'percentage'    => $percentage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deck_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Deck $deck,
        EntityManagerInterface $entityManager,
        CardRepository $cardRepository,
        UserCardRepository $userCardRepository
    ): Response {
        if ($deck->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        // Cartes possédées par l'utilisateur (pour affichage ET validation)
        $userCards      = $userCardRepository->findBy(['user' => $user]);
        $ownedCardIds   = array_map(fn($uc) => $uc->getCard()->getId(), $userCards);
        $availableCards = array_map(fn($uc) => $uc->getCard(), $userCards);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCardIds = $request->request->get('selected_cards');

            // Vider les cartes existantes
            foreach ($deck->getCards() as $card) {
                $deck->removeCard($card);
            }

            if ($selectedCardIds) {
                $cardIds = array_filter(
                    array_map('intval', explode(',', $selectedCardIds))
                );

                if (count($cardIds) > $deck->getMaxCards()) {
                    $this->addFlash('error', 'Vous ne pouvez pas ajouter plus de ' . $deck->getMaxCards() . ' cartes.');
                    return $this->render('deck/edit.html.twig', [
                        'deck'            => $deck,
                        'form'            => $form,
                        'available_cards' => $availableCards,
                    ]);
                }

                foreach ($cardIds as $cardId) {
                    // Vérification : la carte doit être dans l'inventaire du joueur
                    if (!in_array($cardId, $ownedCardIds, true)) {
                        $this->addFlash('error', 'Une ou plusieurs cartes sélectionnées ne sont pas dans votre inventaire.');
                        return $this->render('deck/edit.html.twig', [
                            'deck'            => $deck,
                            'form'            => $form,
                            'available_cards' => $availableCards,
                        ]);
                    }

                    $card = $cardRepository->find($cardId);
                    if ($card) $deck->addCard($card);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Deck mis à jour !');
            return $this->redirectToRoute('app_deck_show', ['id' => $deck->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deck/edit.html.twig', [
            'deck'            => $deck,
            'form'            => $form,
            'available_cards' => $availableCards,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_delete', methods: ['POST'])]
    public function delete(Request $request, Deck $deck, EntityManagerInterface $entityManager): Response
    {
        if ($deck->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $deck->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($deck);
            $entityManager->flush();
            $this->addFlash('success', 'Deck supprimé.');
        }

        return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
    }
}