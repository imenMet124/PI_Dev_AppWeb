<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Texte de la question',
                'attr' => [
                    'placeholder' => 'Saisissez votre question',
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ]);

        // Only add the quiz field if it's not already set in the options
        if (!isset($options['quiz'])) {
            $builder->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'title',
                'placeholder' => 'Sélectionnez un quiz',
                'required' => true, // Make quiz field required
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => fn(EntityRepository $er) => $er->createQueryBuilder('q')
                    ->where('q.deletedAt IS NULL')
                    ->orderBy('q.title', 'ASC')
            ]);
        }

        $builder->add('options', CollectionType::class, [
            'entry_type' => OptionType::class,
            'entry_options' => [
                'label' => false,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false, // This is important - it ensures addOption() is called
            'label' => 'Options',
            'attr' => [
                'class' => 'options-collection'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'quiz' => null,
        ]);

        $resolver->setAllowedTypes('quiz', ['null', Quiz::class]);
    }
}