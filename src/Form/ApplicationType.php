<?php

namespace App\Form;

use App\Entity\Application;
use App\Form\CandidatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\ApplicationStatus;


class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 🔥 CECI EST LA LIGNE ESSENTIELLE !
            ->add('candidat', CandidatType::class)

            ->add('resumeFile', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('coverLetterFile', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => ApplicationStatus::EN_ATTENTE,
                    'Acceptée' => ApplicationStatus::ACCEPTEE,
                    'Refusée' => ApplicationStatus::REFUSEE,
                ],
                'label' => 'Statut de la candidature',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
