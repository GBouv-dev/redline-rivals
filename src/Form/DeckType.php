<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('maxCards')
            // Le champ 'owner' est commenté pour l'instant car il sera géré par l'utilisateur connecté
            /*
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            */
            /*->add('cards', EntityType::class, [
                'class' => Card::class,
                'choice_label' => 'name', // Afficher le nom de la carte au lieu de l'ID
                'multiple' => true,
                'expanded' => true, // Affiche des cases à cocher au lieu d'une liste déroulante
                'required' => false, // AJOUT CLÉ : permet de créer un Deck vide
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deck::class,
        ]);
    }
}