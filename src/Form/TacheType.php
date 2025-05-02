<?php

namespace App\Form;

use App\Entity\Tache;
use App\Entity\User;
use App\Entity\Projet;
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
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('descTache', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Deadline',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('statutTache', ChoiceType::class, [
                'label' => 'Status',
                'required' => true,
                'choices' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priority',
                'required' => true,
                'choices' => [
                    'Low' => 'Low',
                    'Medium' => 'Medium',
                    'High' => 'High'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('progression', IntegerType::class, [
                'label' => 'Progress (%)',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 100
                ]
            ])
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'nomProjet',
                'label' => 'Project',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ]);

        if (!$options['is_employee']) {
            $builder->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // Use the getter method for email
                'label' => 'Assign To',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
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