<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\User;
use App\Entity\UserPhoto;
use App\Enum\UserRole;
use App\Enum\UserStatus;
use App\Repository\DepartmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'choices' => array_combine(
                    array_map(fn($role) => $role->name, UserRole::cases()),
                    array_map(fn($role) => $role->value, UserRole::cases())
                ),
                'choice_label' => fn($value, $key, $index) => ucfirst(strtolower(str_replace('_', ' ', $key))),
                'attr' => ['class' => 'form-control']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => !$isEdit, // Required only for new users
                'attr' => ['class' => 'form-control'],
                'constraints' => $isEdit ? [] : [
                    new NotBlank(['message' => 'Please enter a password', 'groups' => ['registration']]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Password must be at least {{ limit }} characters long',
                        'groups' => ['registration']
                    ])
                ],
                'mapped' => $isEdit ? false : true // Don't map to entity when editing
            ])
            ->add('position', TextType::class, [
                'label' => 'Position',
                'attr' => ['class' => 'form-control']
            ])
            ->add('salary', NumberType::class, [
                'label' => 'Salary',
                'attr' => ['class' => 'form-control']
            ])
            ->add('hireDate', DateType::class, [
                'label' => 'Hire Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => UserStatus::cases(),
                'choice_value' => fn (?UserStatus $enum) => $enum?->value,
                'choice_label' => fn (?UserStatus $enum) => $enum?->name ? ucfirst(strtolower(str_replace('_', ' ', $enum->name))) : '',
                'attr' => ['class' => 'form-control']
            ])
            
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choose a department',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (DepartmentRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                }
            ]);
            
        // Only show these fields if needed
        if ($options['include_photo']) {
            $builder->add('photo', /* ... */);
        }
        
        if ($options['include_managed_department']) {
            $builder->add('managedDepartment', /* ... */);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => User::class,
        'is_edit' => false,
        'include_photo' => false,
        'include_managed_department' => false,
        'validation_groups' => function ($form) {
            $groups = ['Default'];
            $data = $form->getData();

            if (!$data || null === $data->getId()) {
                $groups[] = 'registration';
            }

            return $groups;
        },
    ]);

    // Explicitly define custom options to prevent error
    $resolver->setDefined(['is_edit', 'include_photo', 'include_managed_department']);
}

}