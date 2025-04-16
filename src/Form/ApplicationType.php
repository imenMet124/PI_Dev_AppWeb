<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Candidat;
use App\Entity\jobOffer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\ApplicationStatus;


class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status', ChoiceType::class, [
            'choices' => [
                'En attente' => ApplicationStatus::EN_ATTENTE,
                'Acceptée' => ApplicationStatus::ACCEPTEE,
                'Refusée' => ApplicationStatus::REFUSEE,
            ],
            'label' => 'Statut de la candidature',
            'expanded' => false,  // si tu veux un <select>
            'multiple' => false,
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
