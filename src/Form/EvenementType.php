<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


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
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'image est obligatoire.',
                    ]),
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image au format JPEG ou PNG.',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ])
                ],
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
