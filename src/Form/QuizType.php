<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre du quiz',
                    'class' => 'form-control'
                ]
            ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'titre',
                'required' => false,
                'placeholder' => 'Sélectionnez une formation (optionnel)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
