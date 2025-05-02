<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Question;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'Texte de l\'option',
                'attr' => [
                    'placeholder' => 'Saisissez le texte de l\'option',
                    'class' => 'form-control'
                ]
            ])
            ->add('is_correct', CheckboxType::class, [
                'label' => 'Réponse correcte',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
        ;

        // Add the question field only when creating a standalone option (not in a collection)
        // and when the standalone_option option is set to true
        if (isset($options['standalone_option']) && $options['standalone_option'] === true) {
            $builder->add('question', EntityType::class, [
                'class' => Question::class,
                'choice_label' => fn(Question $question) => $question->getText()
                    ? (strlen($question->getText()) > 50 ? substr($question->getText(), 0, 50) . '...' : $question->getText())
                    : 'Question sans texte',
                'placeholder' => 'Sélectionnez une question',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => fn(EntityRepository $er) => $er->createQueryBuilder('q')
                    ->where('q.deletedAt IS NULL')
                    ->orderBy('q.id', 'DESC')
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Option::class,
            'standalone_option' => false,
        ]);

        $resolver->setAllowedTypes('standalone_option', 'bool');
    }
}
