<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'Nom du modèle',
                'attr'        => ['placeholder' => 'ex: Civic Type R'],
                'constraints' => [new NotBlank()],
            ])
            ->add('brand', TextType::class, [
                'label'       => 'Constructeur',
                'attr'        => ['placeholder' => 'ex: Honda'],
                'constraints' => [new NotBlank()],
            ])
            ->add('rarity', ChoiceType::class, [
                'label'   => 'Rareté',
                'choices' => [
                    'Common'    => 'Common',
                    'Uncommon'  => 'Uncommon',
                    'Rare'      => 'Rare',
                    'Epic'      => 'Epic',
                    'Legendary' => 'Legendary',
                    'Mythic'    => 'Mythic',
                ],
            ])
            ->add('imagePath', TextType::class, [
                'label'    => 'Chemin de l\'image',
                'required' => false,
                'attr'     => ['placeholder' => 'ex: cars/honda_civic.jpg'],
            ])
            // ── Stats de combat ──────────────────────────
            ->add('horsepower', IntegerType::class, [
                'label'       => 'Puissance (CH)',
                'attr'        => ['placeholder' => 'ex: 330', 'min' => 1],
                'constraints' => [new NotBlank(), new Positive()],
            ])
            ->add('speed', IntegerType::class, [
                'label'       => 'Vitesse max (km/h)',
                'required'    => false,
                'attr'        => ['placeholder' => 'ex: 280', 'min' => 1],
                'constraints' => [new Positive()],
            ])
            ->add('acceleration', NumberType::class, [
                'label'       => '0-100 km/h (secondes)',
                'required'    => false,
                'scale'       => 1,
                'attr'        => ['placeholder' => 'ex: 5.7', 'step' => '0.1', 'min' => '0.1'],
                'constraints' => [new Positive()],
            ])
            ->add('handling', IntegerType::class, [
                'label'       => 'Maniabilité (1-100)',
                'required'    => false,
                'attr'        => ['placeholder' => 'ex: 85', 'min' => 1, 'max' => 100],
                'constraints' => [new Range(['min' => 1, 'max' => 100])],
            ])
            ->add('weight', IntegerType::class, [
                'label'       => 'Poids (kg)',
                'required'    => false,
                'attr'        => ['placeholder' => 'ex: 1400', 'min' => 1],
                'constraints' => [new Positive()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Card::class]);
    }
}