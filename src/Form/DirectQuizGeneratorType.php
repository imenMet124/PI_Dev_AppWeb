<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;

class DirectQuizGeneratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la formation',
                'attr' => [
                    'placeholder' => 'Entrez le titre de la formation',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre est obligatoire'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optionnelle)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez une description pour aider l\'IA à générer un quiz plus pertinent',
                    'class' => 'form-control',
                    'rows' => 4
                ]
            ])
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
                        'notInRangeMessage' => 'Le nombre de questions doit être entre {{ min }} et {{ max }}',
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
                        'notInRangeMessage' => 'Le nombre d\'options doit être entre {{ min }} et {{ max }}',
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
                'class' => 'direct-quiz-generator-form',
            ],
        ]);
    }
}
