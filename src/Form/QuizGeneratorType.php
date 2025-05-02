<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class QuizGeneratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numQuestions', IntegerType::class, [
                'label' => 'Nombre de questions',
                'attr' => [
                    'min' => 1,
                    'max' => 20,
                    'class' => 'form-control',
                    'data-slider' => 'true',
                ],
                'data' => 5,
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 20,
                        'minMessage' => 'Le nombre de questions doit être au moins {{ limit }}',
                        'maxMessage' => 'Le nombre de questions ne peut pas dépasser {{ limit }}',
                    ]),
                ],
            ])
            ->add('numOptions', IntegerType::class, [
                'label' => 'Nombre d\'options par question',
                'attr' => [
                    'min' => 2,
                    'max' => 6,
                    'class' => 'form-control',
                    'data-slider' => 'true',
                ],
                'data' => 4,
                'constraints' => [
                    new Range([
                        'min' => 2,
                        'max' => 6,
                        'minMessage' => 'Le nombre d\'options doit être au moins {{ limit }}',
                        'maxMessage' => 'Le nombre d\'options ne peut pas dépasser {{ limit }}',
                    ]),
                ],
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Niveau de difficulté',
                'choices' => [
                    'Facile' => 'easy',
                    'Moyen' => 'medium',
                    'Difficile' => 'hard',
                ],
                'data' => 'medium',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Français' => 'french',
                    'Anglais' => 'english',
                ],
                'data' => 'french',
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'attr' => [
                'class' => 'quiz-generator-form',
            ],
        ]);
    }
}
