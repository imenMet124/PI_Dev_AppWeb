<?php

namespace App\Form;

use App\Entity\JobOffer;
use App\Enum\ContractType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class JobOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'offre',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 5, max: 100),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du poste',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 20),
                ],
            ])
            ->add('department', TextType::class, [
                'label' => 'Département',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            
            ->add('location', TextType::class, [
                'label' => 'Lieu',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 100),
                ],
            ])
            ->add('contractType', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => ContractType::cases(),
                'choice_label' => fn ($choice) => $choice->value,
                'constraints' => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('salaryMin', MoneyType::class, [
                'label' => 'Salaire minimum (€)',
                'currency' => 'EUR',
                'required' => false,
                'constraints' => [
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('salaryMax', MoneyType::class, [
                'label' => 'Salaire maximum (€)',
                'currency' => 'EUR',
                'required' => false,
                'constraints' => [
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('isActive', null, [
                'label' => 'Activer cette offre ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOffer::class,
        ]);
    }
}
