<?php

namespace App\Form;

use App\Entity\Reponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Date et heure...',
                    'class' => 'datepicker' // Ajouter une classe pour sélectionner cet élément avec JavaScript
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual('today'),
                ],
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'reponset',
                'attr' => ['style' => 'height: 150px'],
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
