<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom_Evenement')
            ->add('Description')
            ->add('Date', null, [
                'widget' => 'single_text'
            ])
            ->add('Heure', null, [
                'widget' => 'single_text'
            ])
            ->add('Capacite')
            ->add('Nombre_Participants')
            ->add('Image_Path', FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false, // si tu ne stockes pas directement dans l'entité
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
