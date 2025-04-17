<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\User;
use App\Entity\Tache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_affectation', DateType::class, [
                'label' => 'Assignment Date',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('tache', EntityType::class, [
                'class' => Tache::class,
                'choice_label' => 'titre_tache',
                'label' => 'Task',
                'attr' => ['class' => 'form-control']
            ])
            ->add('employe', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'iyedNomUser',
                'label' => 'Employee',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
        ]);
    }
} 