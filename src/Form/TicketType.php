<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('type')
            ->add('nbreTicket')
            ->add('evenement', EntityType::class, [
              'class' => Evenement::class,
              'choice_label' => 'nom',
              'label' => 'Evenement',
              'placeholder' => 'Sélectionnez un événement',
            ]);
           // ->add('utilisateur')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
