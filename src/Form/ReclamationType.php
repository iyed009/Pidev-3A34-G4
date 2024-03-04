<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $reclamation = $builder->getData();

        $builder
            ->add('nom', null, [
                'data' => $reclamation ? $reclamation->getNom() : '',
                'disabled' => true,
// Définir la valeur par défaut
            ])
            ->add('prenom', null, [
                'data' => $reclamation ? $reclamation->getPrenom() : '',
                'disabled' => true,
// Définir la valeur par défaut
            ])
            // Ajouter les autres champs comme avant


            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet',
                'choices' => [
                    'Salle' => 'Salle',
                    'Service' => 'Service',
                    'Autre' => 'Autre',
                    // Ajoutez autant d'options que nécessaire
                ],])

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
            ->add('description', TextareaType::class, [
                'label' => 'input comment',
                'attr' => ['style' => 'height: 150px'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }

}

