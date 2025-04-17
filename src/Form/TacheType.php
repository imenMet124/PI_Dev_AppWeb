<?php

namespace App\Form;

use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreTache', TextType::class, [
                'label' => 'Task Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('descTache', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Deadline',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('statutTache', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priority',
                'choices' => [
                    'Low' => 'Low',
                    'Medium' => 'Medium',
                    'High' => 'High'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('progression', IntegerType::class, [
                'label' => 'Progress (%)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 100
                ]
            ]);

        if (!$options['is_employee']) {
            $builder->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'iyedEmailUser',
                'label' => 'Assign To',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
            'is_employee' => false,
        ]);
    }
} 