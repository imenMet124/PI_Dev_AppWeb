<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Candidat;
use App\Entity\jobOffer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('message')
            ->add('submittedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('cvSnapshotPath')
            ->add('candidat', EntityType::class, [
                'class' => Candidat::class,
                'choice_label' => 'id',
            ])
            ->add('jobOffer', EntityType::class, [
                'class' => jobOffer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
